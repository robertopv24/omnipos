<div class="glass-effect p-2 mt-2 rounded" style="max-width: 600px; margin: 0 auto;">
    <div class="d-flex justify-between align-center mb-2">
        <h1 class="text-white m-0"><i class="fas fa-industry"></i> <?= __('register_production') ?></h1>
        <a href="<?= url('/manufacture/recipes') ?>" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> <?= __('back') ?>
        </a>
    </div>

    <div class="glass-effect p-15 border-dim rounded mb-2">
        <h2 class="text-bright"><?= htmlspecialchars($product['name']) ?></h2>
        <p class="text-dim">
            <?= __('unit') ?>: <strong><?= htmlspecialchars($product['unit']) ?></strong> | 
            <?= __('current_stock') ?>: <strong><?= number_format($product['stock'], 2) ?></strong>
        </p>
    </div>

    <form action="<?= url('/manufacture/orders') ?>" method="POST">
        <input type="hidden" name="manufactured_product_id" value="<?= $product['id'] ?>">

        <div class="form-group">
            <label><?= __('quantity_to_produce') ?></label>
            <input type="number" step="0.0001" name="quantity" class="form-control" 
                   style="font-size: 1.5rem; height: auto;" placeholder="0.0000" required autofocus>
        </div>

        <div class="d-flex gap-1 mt-2">
            <button type="submit" class="btn btn-primary btn-lg flex-2">
                <i class="fas fa-check-circle"></i> <?= __('register_production') ?>
            </button>
            <a href="<?= url('/manufacture/recipes') ?>" class="btn btn-secondary flex-1"><?= __('cancel') ?></a>
        </div>
    </form>
</div>