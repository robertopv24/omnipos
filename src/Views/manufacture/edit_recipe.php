<div class="glass-effect p-2 mt-2 rounded" style="max-width: 900px; margin: 0 auto;">
    <div class="d-flex justify-between align-center mb-2">
        <h1 class="text-white m-0"><i class="fas fa-edit"></i> <?= __('edit_manufactured_product') ?></h1>
        <a href="<?= url('/manufacture/recipes') ?>" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> <?= __('back') ?>
        </a>
    </div>

    <form action="<?= url('/manufacture/recipes/update') ?>" method="POST">
        <input type="hidden" name="id" value="<?= $product['id'] ?>">
        
        <div class="form-grid">
            <div class="form-group">
                <label><?= __('product_name') ?></label>
                <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($product['name']) ?>" required>
            </div>
            <div class="form-group">
                <label><?= __('unit') ?></label>
                <input type="text" name="unit" class="form-control" value="<?= htmlspecialchars($product['unit']) ?>" required>
            </div>
        </div>

        <h3 class="text-white mt-2 mb-1 border-bottom-dim pb-05">
            <i class="fas fa-list"></i> <?= __('ingredients_recipe') ?>
        </h3>
        
        <div id="ingredients-list">
            <?php if (empty($recipeMaterials)): ?>
                <!-- Fila base si no hay ingredientes -->
                <div class="ingredient-row" style="display: grid; grid-template-columns: 2fr 1fr auto; gap: 1rem; margin-bottom: 1rem; align-items: flex-end;">
                    <div class="form-group mb-0">
                        <label><?= __('raw_material') ?></label>
                        <select name="materials[]" class="form-control" required>
                            <option value=""><?= __('select_option') ?></option>
                            <?php foreach ($materials as $m): ?>
                                <option value="<?= $m['id'] ?>"><?= htmlspecialchars($m['name']) ?> (<?= $m['unit'] ?>)</option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group mb-0">
                        <label><?= __('required_quantity') ?></label>
                        <input type="number" step="0.0001" name="quantities[]" class="form-control" placeholder="0.0000" required>
                    </div>
                    <button type="button" class="btn btn-danger" onclick="removeRow(this)" style="padding: 0.75rem 1rem;">
                        <i class="fa fa-trash"></i>
                    </button>
                </div>
            <?php else: ?>
                <?php foreach ($recipeMaterials as $rm): ?>
                    <div class="ingredient-row" style="display: grid; grid-template-columns: 2fr 1fr auto; gap: 1rem; margin-bottom: 1rem; align-items: flex-end;">
                        <div class="form-group mb-0">
                            <label><?= __('raw_material') ?></label>
                            <select name="materials[]" class="form-control" required>
                                <option value=""><?= __('select_option') ?></option>
                                <?php foreach ($materials as $m): ?>
                                    <option value="<?= $m['id'] ?>" <?= ($m['id'] === $rm['raw_material_id']) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($m['name']) ?> (<?= $m['unit'] ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group mb-0">
                            <label><?= __('required_quantity') ?></label>
                            <input type="number" step="0.0001" name="quantities[]" class="form-control" value="<?= $rm['quantity_required'] ?>" required>
                        </div>
                        <button type="button" class="btn btn-danger" onclick="removeRow(this)" style="padding: 0.75rem 1rem;">
                            <i class="fa fa-trash"></i>
                        </button>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <div class="mt-1">
            <button type="button" class="btn btn-info" onclick="addIngredientRow()">
                <i class="fas fa-plus"></i> <?= __('add_ingredient') ?>
            </button>
        </div>

        <div class="d-flex gap-1 mt-3" style="justify-content: space-between;">
            <a href="<?= url('/manufacture/recipes/delete?id=' . $product['id']) ?>" 
               class="btn btn-danger"
               onclick="return confirm('<?= __('confirm_delete_recipe') ?>')">
                <i class="fas fa-trash"></i> <?= __('delete_product') ?>
            </a>
            <div class="d-flex gap-1">
                <a href="<?= url('/manufacture/recipes') ?>" class="btn btn-secondary"><?= __('cancel') ?></a>
                <button type="submit" class="btn btn-primary btn-lg">
                    <i class="fas fa-save"></i> <?= __('update_recipe') ?>
                </button>
            </div>
        </div>
    </form>
</div>

<!-- Template para nueva fila (oculto) -->
<div id="ingredient-template" style="display: none;">
    <div class="ingredient-row" style="display: grid; grid-template-columns: 2fr 1fr auto; gap: 1rem; margin-bottom: 1rem; align-items: flex-end;">
        <div class="form-group mb-0">
            <label><?= __('raw_material') ?></label>
            <select name="materials[]" class="form-control">
                <option value=""><?= __('select_option') ?></option>
                <?php foreach ($materials as $m): ?>
                    <option value="<?= $m['id'] ?>"><?= htmlspecialchars($m['name']) ?> (<?= $m['unit'] ?>)</option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group mb-0">
            <label><?= __('required_quantity') ?></label>
            <input type="number" step="0.0001" name="quantities[]" class="form-control" placeholder="0.0000">
        </div>
        <button type="button" class="btn btn-danger" onclick="removeRow(this)" style="padding: 0.75rem 1rem;">
            <i class="fa fa-trash"></i>
        </button>
    </div>
</div>

<script>
    function addIngredientRow() {
        const list = document.getElementById('ingredients-list');
        const template = document.getElementById('ingredient-template').firstElementChild;
        const newRow = template.cloneNode(true);
        newRow.querySelector('select').required = true;
        newRow.querySelector('input').required = true;
        list.appendChild(newRow);
    }

    function removeRow(btn) {
        const list = document.getElementById('ingredients-list');
        if (list.querySelectorAll('.ingredient-row').length > 1) {
            btn.closest('.ingredient-row').remove();
        } else {
            alert('<?= __('recipe_min_one_ingredient') ?>');
        }
    }
</script>
