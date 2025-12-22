<div class="container-fluid">
    <h1><?= __('new_purchase_order') ?></h1>

    <form action="<?= url('/purchases') ?>" method="POST" id="purchaseForm">
        <div class="glass-effect p-2 mt-2">
            <h3 class="mb-2 text-primary"><?= __('general_information') ?></h3>
            
            <div class="form-grid">
                <div class="form-group">
                    <label><?= __('supplier') ?> *</label>
                    <select name="supplier_id" class="form-control" required>
                        <option value=""><?= __('select_supplier') ?></option>
                        <?php foreach ($suppliers as $supplier): ?>
                            <option value="<?= $supplier['id'] ?>"><?= htmlspecialchars($supplier['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label><?= __('payment_term_days') ?></label>
                    <div class="d-flex gap-1 align-center">
                        <input type="number" name="payment_term_days" id="payment_term_days" class="form-control" value="0" min="0" required style="width: 100px;">
                        <div class="d-flex gap-1">
                            <button type="button" class="btn btn-secondary py-1 px-2 text-sm" onclick="document.getElementById('payment_term_days').value = 0"><?= __('today') ?></button>
                            <button type="button" class="btn btn-secondary" style="padding: 0.4rem 0.6rem; font-size: 0.8rem;" onclick="document.getElementById('payment_term_days').value = 15">15d</button>
                            <button type="button" class="btn btn-secondary" style="padding: 0.4rem 0.6rem; font-size: 0.8rem;" onclick="document.getElementById('payment_term_days').value = 30">30d</button>
                        </div>
                    </div>
                </div>

                <div class="form-group col-span-full">
                    <label><?= __('notes') ?></label>
                    <input type="text" name="notes" class="form-control" placeholder="<?= __('enter_notes') ?>">
                </div>
            </div>
        </div>

        <div class="glass-effect p-2 mt-2">
            <div class="d-flex justify-between align-center mb-2">
                <h3 class="text-primary"><?= __('order_items') ?></h3>
                <button type="button" onclick="addItem()" class="btn btn-primary">
                    <i class="fa fa-plus"></i> <?= __('add_item') ?>
                </button>
            </div>

            <div id="itemsContainer">
                <!-- Items se agregarán dinámicamente aquí -->
            </div>

            <div class="mt-2 pt-2 border-top text-right">
                <h2 class="text-primary"><?= __('total') ?>: $<span id="totalAmount">0.00</span></h2>
            </div>
        </div>

        <div class="mt-2 d-flex gap-1 justify-end">
            <a href="<?= url('/purchases') ?>" class="btn btn-secondary px-4 py-2">
                <?= __('cancel') ?>
            </a>
            <button type="submit" class="btn btn-primary px-4 py-2">
                <i class="fa fa-save"></i> <?= __('create_purchase_order') ?>
            </button>
        </div>
    </form>
</div>

<script>
let itemIndex = 0;
const resaleProducts = <?= json_encode($resaleProducts) ?>;
const operationalSupplies = <?= json_encode($operationalSupplies) ?>;
const rawMaterials = <?= json_encode($rawMaterials) ?>;

function addItem() {
    const container = document.getElementById('itemsContainer');
    const itemDiv = document.createElement('div');
    itemDiv.className = 'item-row';
    itemDiv.style.cssText = 'display: grid; grid-template-columns: 180px 2fr 1fr 1fr 60px; gap: 1rem; margin-bottom: 1rem; padding: 1.25rem; background: var(--bg-main); border-radius: 12px; align-items: end; border: 1px solid var(--border-subtle);';
    itemDiv.innerHTML = `
        <div class="form-group mb-0">
            <label class="text-sm"><?= __('type') ?></label>
            <select name="items[${itemIndex}][item_type]" class="form-control" onchange="updateItemOptions(${itemIndex})">
                <option value="product_resale"><?= __('resale_product') ?></option>
                <option value="product_operational"><?= __('operational_supply') ?></option>
                <option value="raw_material"><?= __('raw_material') ?></option>
            </select>
        </div>
        <div class="form-group mb-0">
            <label class="text-sm"><?= __('item') ?></label>
            <select name="items[${itemIndex}][item_id]" id="item_select_${itemIndex}" class="form-control" required>
                ${resaleProducts.map(p => `<option value="${p.id}">${p.name} (Stock: ${p.stock})</option>`).join('')}
            </select>
        </div>
        <div class="form-group mb-0">
            <label class="text-sm"><?= __('quantity') ?></label>
            <input type="number" name="items[${itemIndex}][quantity]" class="form-control" step="0.01" min="0.01" required onchange="calculateTotal()">
        </div>
        <div class="form-group mb-0">
            <label class="text-sm"><?= __('unit_cost') ?></label>
            <input type="number" name="items[${itemIndex}][unit_cost]" class="form-control" step="0.01" min="0.01" required onchange="calculateTotal()">
        </div>
        <div>
            <button type="button" onclick="removeItem(this)" class="btn btn-danger" style="width: 42px; height: 42px; padding:0;">
                <i class="fa fa-trash"></i>
            </button>
        </div>
    `;
    container.appendChild(itemDiv);
    itemIndex++;
    calculateTotal();
}

function updateItemOptions(index) {
    const typeSelect = document.querySelector(`select[name="items[${index}][item_type]"]`);
    const itemSelect = document.getElementById(`item_select_${index}`);
    const type = typeSelect.value;
    
    itemSelect.innerHTML = '';
    let items = [];
    
    if (type === 'product_resale') {
        items = resaleProducts;
    } else if (type === 'product_operational') {
        items = operationalSupplies;
    } else if (type === 'raw_material') {
        items = rawMaterials;
    }
    
    items.forEach(item => {
        const option = document.createElement('option');
        option.value = item.id;
        option.textContent = `${item.name} (Stock: ${item.stock})`;
        itemSelect.appendChild(option);
    });
}

function removeItem(button) {
    button.closest('.item-row').remove();
    calculateTotal();
}

function calculateTotal() {
    let total = 0;
    document.querySelectorAll('.item-row').forEach(row => {
        const qty = parseFloat(row.querySelector('input[name*="[quantity]"]').value) || 0;
        const cost = parseFloat(row.querySelector('input[name*="[unit_cost]"]').value) || 0;
        total += qty * cost;
    });
    document.getElementById('totalAmount').textContent = total.toFixed(2);
}

// Agregar primer item al cargar
addItem();
</script>
