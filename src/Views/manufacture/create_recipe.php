<div class="glass-effect p-2 mt-2 rounded" style="max-width: 900px; margin: 0 auto;">
    <div class="d-flex justify-between align-center mb-2">
        <h1 class="text-white m-0"><i class="fas fa-plus"></i> <?= __('new_manufactured_product') ?></h1>
        <a href="<?= url('/manufacture/recipes') ?>" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> <?= __('back') ?>
        </a>
    </div>

    <form action="<?= url('/manufacture/recipes') ?>" method="POST">
        <div class="form-grid">
            <div class="form-group">
                <label><?= __('product_name_example') ?></label>
                <input type="text" name="name" class="form-control" placeholder="<?= __('product_name') ?>" required>
            </div>
            <div class="form-group">
                <label><?= __('unit_kg_lt_und') ?></label>
                <input type="text" name="unit" class="form-control" value="und" required>
            </div>
        </div>

        <h3 class="text-white mt-2 mb-1 border-bottom-dim pb-05">
            <i class="fas fa-list"></i> <?= __('ingredients_recipe') ?>
        </h3>
        
        <div id="ingredients-list">
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
        </div>

        <div class="mt-1">
            <button type="button" class="btn btn-info" onclick="addIngredientRow()">
                <i class="fas fa-plus"></i> <?= __('add_ingredient') ?>
            </button>
        </div>

        <div class="d-flex gap-1 justify-end mt-3">
            <a href="<?= url('/manufacture/recipes') ?>" class="btn btn-secondary"><?= __('cancel') ?></a>
            <button type="submit" class="btn btn-primary btn-lg">
                <i class="fas fa-save"></i> <?= __('save_product_and_recipe') ?>
            </button>
        </div>
    </form>
</div>

<script>
    function addIngredientRow() {
        const list = document.getElementById('ingredients-list');
        const firstRow = list.querySelector('.ingredient-row');
        const newRow = firstRow.cloneNode(true);
        newRow.querySelectorAll('input').forEach(input => input.value = '');
        newRow.querySelectorAll('select').forEach(select => select.value = '');
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
</script>