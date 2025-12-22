<div class="glass-effect p-2 mt-2 rounded">
    <div class="d-flex justify-between align-center mb-2">
        <h1 class="text-white m-0"><i class="fas fa-book"></i> <?= __('manufactured_products') ?></h1>
        <a href="<?= url('/manufacture/recipes/create') ?>" class="btn btn-primary">
            <i class="fas fa-plus"></i> <?= __('new_manufactured_product') ?>
        </a>
    </div>

    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th><?= __('name') ?></th>
                    <th><?= __('unit') ?></th>
                    <th><?= __('current_stock') ?></th>
                    <th><?= __('average_cost') ?></th>
                    <th><?= __('last_production') ?></th>
                    <th><?= __('actions') ?></th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($products)): ?>
                    <tr><td colspan="6" class="text-center text-dim p-2"><?= __('no_manufactured_products') ?></td></tr>
                <?php else: ?>
                    <?php foreach ($products as $p): ?>
                        <tr>
                            <td class="font-700"><?= htmlspecialchars($p['name']) ?></td>
                            <td><?= htmlspecialchars($p['unit']) ?></td>
                            <td><?= number_format($p['stock'], 2) ?></td>
                            <td class="text-bright">$<?= number_format($p['unit_cost_average'], 4) ?></td>
                            <td class="text-dim"><?= $p['last_production_date'] ?? 'N/A' ?></td>
                            <td>
                                <div class="d-flex gap-05">
                                    <a href="<?= url('/manufacture/recipes/edit?id=' . $p['id']) ?>"
                                       class="btn-icon" title="<?= __('edit_recipe') ?>">
                                        <i class="fa fa-edit"></i>
                                    </a>
                                    <a href="<?= url('/manufacture/orders/create?product_id=' . $p['id']) ?>"
                                       class="btn-icon text-success" title="<?= __('register_production') ?>">
                                        <i class="fa fa-industry"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>