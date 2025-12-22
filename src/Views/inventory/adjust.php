<div class="d-flex justify-between align-center mb-2">
    <div>
        <h1 class="text-gradient border-none mb-0"><?= __('inventory') ?>: <?= __('inventory_adjustment') ?></h1>
        <p class="text-dim"><?= __('inventory_adjustment_description') ?></p>
    </div>
    <div class="d-flex gap-1">
        <a href="<?= url('/products') ?>" class="btn btn-secondary">
            <i class="fa fa-boxes-stacked"></i> <?= __('view_products') ?>
        </a>
    </div>
</div>

<div class="glass-widget">
    <form action="<?= url('/inventory/adjust') ?>" method="POST" class="form-grid">
        <div class="form-group col-span-2 md:col-span-1">
            <label for="item_id" class="form-label"><?= __('product_or_supply') ?> <span class="text-danger">*</span></label>
            <div class="select-wrapper">
                <select name="item_id" id="item_id" class="form-control" required>
                    <option value=""><?= __('select_item_placeholder') ?>...</option>
                    <optgroup label="<?= __('products') ?>">
                        <?php foreach ($products as $p): ?>
                            <option value="product:<?= $p['id'] ?>">
                                <?= htmlspecialchars($p['name']) ?> (Stock: <?= $p['stock'] ?>)
                            </option>
                        <?php endforeach; ?>
                    </optgroup>
                    <optgroup label="<?= __('supplies_raw_materials') ?>">
                        <?php foreach ($rawMaterials as $rm): ?>
                            <option value="raw_material:<?= $rm['id'] ?>">
                                <?= htmlspecialchars($rm['name']) ?> (Stock: <?= $rm['stock_quantity'] ?? $rm['stock'] ?? 0 ?>)
                            </option>
                        <?php endforeach; ?>
                    </optgroup>
                </select>
                <i class="fa fa-chevron-down select-arrow"></i>
            </div>
        </div>

        <div class="form-group col-span-2 md:col-span-1">
            <label for="type" class="form-label">Tipo de Ajuste <span class="text-danger">*</span></label>
            <div class="select-wrapper">
                <select name="type" id="type" class="form-control" required>
                    <option value="entry"><?= __('stock_entry_sum') ?></option>
                    <option value="exit"><?= __('stock_exit_negative') ?></option>
                    <option value="discard"><?= __('stock_discard_loss') ?></option>
                </select>
                <i class="fa fa-chevron-down select-arrow"></i>
            </div>
        </div>

        <div class="form-group col-span-2 md:col-span-1">
            <label for="quantity" class="form-label">Cantidad <span class="text-danger">*</span></label>
            <input type="number" step="0.0001" name="quantity" id="quantity" class="form-control" placeholder="0.00" required>
        </div>

        <div class="form-group col-span-2 md:col-span-1">
            <label for="notes" class="form-label"><?= __('justification_reason') ?></label>
            <input type="text" name="notes" id="notes" class="form-control" placeholder="<?= __('eg_counting_error_expiry') ?>...">
        </div>

        <div class="col-span-2 d-flex justify-end gap-1 mt-2">
            <button type="submit" class="btn btn-primary">
                <i class="fa fa-sync"></i> <?= __('process_adjustment') ?>
            </button>
        </div>
    </form>
</div>

<div class="mt-3">
    <h3 class="mb-1 text-lg font-600"><?= __('important_notices') ?></h3>
    <div class="glass-widget bg-warning-10 border-warning-subtle">
        <ul class="text-sm text-dim d-flex flex-column gap-05">
            <li><i class="fa fa-info-circle text-warning"></i> <?= __('stock_entry_notice') ?></li>
            <li><i class="fa fa-info-circle text-warning"></i> <?= __('stock_exit_notice') ?></li>
            <li><i class="fa fa-info-circle text-warning"></i> <?= __('stock_traceability_notice') ?></li>
        </ul>
    </div>
</div>
