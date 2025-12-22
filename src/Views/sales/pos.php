<div class="pos-wrapper d-flex gap-1 h-100 overflow-hidden">
    <!-- Sección de Productos -->
    <div class="pos-products glass-effect d-flex flex-column" style="flex: 1; min-width: 0;">
        <div class="p-2 border-bottom border-bright bg-glass-dark sticky-top" style="z-index: 10;">
            <div class="header-tools d-flex gap-1">
                <div class="search-bar relative" style="flex: 2;">
                    <i class="fa fa-search absolute left-1 top-50 translate-y-n50 text-dim"></i>
                    <input type="text" id="product-search" class="form-control pl-3" oninput="searchProducts(this.value)"
                        placeholder="<?= __('search_product_placeholder') ?>">
                </div>
                <div class="client-bar relative" style="flex: 1;">
                    <i class="fa fa-user absolute left-1 top-50 translate-y-n50 text-dim"></i>
                    <input type="text" id="client-search" class="form-control pl-3" oninput="searchClients(this.value)"
                        placeholder="<?= __('select_client_optional') ?>">
                    <div id="client-results" class="modal-content absolute z-50 w-full mt-1 max-h-250 overflow-auto bg-dark border-glass p-0 shadow-2xl"
                        style="display:none; left:0; right:0;">
                    </div>
                </div>
                <input type="hidden" id="selected-client-id" value="">
            </div>
        </div>

        <div id="products-container" class="products-grid p-2 d-grid gap-1 grid-cols-auto-fill-150 overflow-y-auto custom-scrollbar flex-grow">
            <?php foreach ($products as $p): ?>
                <div class="product-card glass-effect p-1-5 cursor-pointer hover-glow transition-all rounded-lg d-flex flex-column" 
                     onclick="addToCart(<?= htmlspecialchars(json_encode($p)) ?>)">
                    <div class="aspect-square rounded-md mb-1-5 overflow-hidden bg-dim">
                        <img src="<?= $p['image_url'] ? url($p['image_url']) : url('/img/no-image.png') ?>"
                            onerror="this.onerror=null; this.src='<?= url('/img/no-image.png') ?>'" class="w-full h-full object-cover">
                    </div>
                    <div class="name font-700 leading-tight text-sm line-clamp-2 mb-1"><?= $p['name'] ?></div>
                    <div class="price text-primary font-800 mt-auto">$<?= number_format($p['price_usd'], 2) ?></div>
                </div>
            <?php endforeach; ?>
        </div>
        
        <!-- Paginación POS -->
        <div id="pos-pagination" class="p-1 border-top border-bright bg-glass-dark d-flex justify-center">
            <!-- Se carga vía AJAX en pos.js -->
        </div>
    </div>

    <!-- Sección de Carrito -->
    <div class="pos-cart glass-effect d-flex flex-column border-left border-bright shadow-xl" style="width: 400px; flex-shrink: 0;">
        <div class="p-2 border-bottom border-bright bg-glass-dark">
            <h2 class="d-flex justify-between align-center m-0 text-xl font-800">
                <?= __('cart') ?> 
                <span id="cart-count" class="badge bg-primary rounded-full text-xs px-2 py-05">0</span>
            </h2>
        </div>

        <div id="cart-items" class="cart-items-container flex-grow overflow-y-auto custom-scrollbar p-2">
            <!-- Items se cargan aquí vía pos.js -->
            <div class="empty-cart-msg text-center py-4 text-dim">
                <i class="fa fa-shopping-basket text-4xl mb-1 opacity-20"></i>
                <p><?= __('cart_is_empty') ?></p>
            </div>
        </div>

        <div class="cart-summary p-2 border-top border-bright bg-glass-dark">
            <div class="mb-2">
                <div class="d-grid grid-cols-2 gap-1">
                    <button id="btn-dine-in" onclick="setConsumptionType('dine_in')" class="btn btn-primary d-flex align-center justify-center gap-05 py-1">
                        <i class="fa fa-utensils"></i> <?= __('dine_in') ?>
                    </button>
                    <button id="btn-takeaway" onclick="setConsumptionType('takeaway')" class="btn btn-secondary d-flex align-center justify-center gap-05 py-1">
                        <i class="fa fa-bag-shopping"></i> <?= __('takeaway') ?>
                    </button>
                </div>
            </div>

            <div class="summary-details bg-dim rounded-lg p-1-5 mb-2">
                <div class="summary-row d-flex justify-between text-sm text-dim mb-1">
                    <span>Subtotal</span>
                    <span id="subtotal-usd">$0.00</span>
                </div>
                <div id="packaging-display" class="summary-row d-flex justify-between text-yellow text-sm mb-1" style="display: none;">
                    <span><?= __('wrapping_charge') ?></span>
                    <span id="total-packaging">$0.00</span>
                </div>
                <div id="tax-display" class="summary-row d-flex justify-between text-red text-sm mb-1">
                    <span>IVA (16%)</span>
                    <span id="tax-amount">$0.00</span>
                </div>
                <div class="summary-row d-flex justify-between font-800 text-xl text-pure border-top border-bright pt-1 mt-1">
                    <span>Total USD</span>
                    <span id="total-usd">$0.00</span>
                </div>
            </div>
            
            <div class="d-flex justify-between align-center mb-2 px-1">
                <span class="text-xs text-dim"><?= __('exchange_rate') ?>: Bs <?= number_format($exchangeRate, 2) ?></span>
                <span class="font-700 text-lg text-primary" id="total-ves">Bs 0.00</span>
            </div>

            <button onclick="openPaymentModal()" class="btn btn-primary w-full py-1-5 text-lg font-800 shadow-glow rounded-lg">
                <i class="fa fa-credit-card mr-05"></i> <?= __('finalize_sale') ?>
            </button>
        </div>
    </div>
</div>

<style>
.pos-wrapper {
    height: calc(100vh - 70px); /* Ajuste basado en el header */
    background: var(--bg-main);
}
.hover-glow:hover {
    box-shadow: 0 0 15px var(--primary-glow);
    transform: translateY(-2px);
    background: rgba(255,255,255,0.05);
}
.cart-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 1rem;
    margin-bottom: 0.75rem;
    background: rgba(255,255,255,0.03);
    border-radius: 0.75rem;
    border: 1px solid rgba(255,255,255,0.05);
    transition: all 0.2s;
}
.cart-item:hover {
    background: rgba(255,255,255,0.06);
}
.client-result-item:hover {
    background: var(--primary) !important;
    color: white;
}
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
.aspect-square {
    aspect-ratio: 1 / 1;
}
</style>

<!-- Modal de Pago -->
<!-- Modal de Pago -->
<div id="payment-modal" class="modal-backdrop">
    <div class="modal-content">
        <h2><?= __('pay_order') ?></h2>
        <div class="form-group mt-2">
            <label><?= __('sale_type') ?></label>
            <select id="payment-type" class="form-control" onchange="toggleSupervisor(this.value)">
                <option value="cash"><?= __('cash_payment_mobile') ?></option>
                <option value="credit"><?= __('credit_employee_benefit') ?></option>
            </select>
        </div>

        <div id="supervisor-auth" class="form-group mt-2 p-2 border border-dashed border-primary rounded-md bg-blue-5" style="display:none;">
            <label class="text-primary"><i class="fa fa-shield-alt"></i> <?= __('requires_authorization') ?></label>
            <input type="password" id="supervisor-code" class="form-control" placeholder="<?= __('supervisor_password') ?>">
        </div>

        <div id="payment-methods" class="mt-2">
            <?php foreach ($paymentMethods as $pm): ?>
                <div class="payment-method-row d-flex align-center gap-1 mb-1">
                    <input type="checkbox" name="methods" value="<?= $pm['id'] ?>" data-currency="<?= $pm['currency'] ?>" class="w-20 h-20">
                    <span class="flex-1"><?= $pm['name'] ?> (<?= $pm['currency'] ?>)</span>
                    <input type="number" step="0.01" class="payment-amount form-control w-120" placeholder="0.00" oninput="updateChangeDisplay()">
                </div>
            <?php endforeach; ?>
        </div>

        <div class="mt-2 d-flex gap-1">
            <button onclick="submitCheckout()" class="btn btn-primary flex-2"><?= __('confirm') ?></button>
            <button onclick="document.getElementById('payment-modal').style.display='none'" class="btn btn-secondary flex-1"><?= __('cancel') ?></button>
        </div>
    </div>
</div>

<!-- Modal de Modificaciones -->
<div id="modification-modal" class="modal-backdrop">
    <div class="modal-content" style="max-width: 450px;">
        <h3><?= __('customize') ?>: <span id="mod-product-name" class="text-primary"></span></h3>
        
        <div class="form-group mt-2">
            <label><?= __('extras_example') ?></label>
            <input type="text" id="mod-extras" class="form-control" placeholder="Separar por comas">
        </div>

        <div class="form-group">
            <label><?= __('restrictions_example') ?></label>
            <input type="text" id="mod-removals" class="form-control" placeholder="Separar por comas">
        </div>

        <div class="mt-2 d-flex gap-1">
            <button onclick="saveModifications()" class="btn btn-primary flex-2"><?= __('add_to_cart') ?></button>
            <button onclick="document.getElementById('modification-modal').style.display='none'" class="btn btn-secondary flex-1"><?= __('cancel') ?></button>
        </div>
        <input type="hidden" id="mod-product-data">
    </div>
</div>

<!-- Modal de Ticket para Impresión -->
<div id="ticket-modal" class="modal-backdrop bg-black-95">
    <div class="ticket-view">
        <div id="ticket-content"></div>
        <button onclick="location.reload()" class="btn btn-primary w-full mt-2 rounded-sm"><?= __('new_sale') ?></button>
    </div>
</div>

<script>
    const EXCHANGE_RATE = <?= $exchangeRate ?>;
    let CURRENT_PAGE = <?= $currentPage ?>;
    let TOTAL_PAGES = <?= $totalPages ?>;
</script>
<script src="<?= url('/js/pos.js') ?>"></script>