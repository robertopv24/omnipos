<div class="glass-effect p-2 mt-2 rounded" style="max-width: 1400px;">
    <div class="d-flex justify-between align-center mb-2">
        <h1 class="text-white m-0"><?= __('purchase_orders') ?></h1>
        <a href="<?= url('/purchases/create') ?>" class="btn btn-primary">
            <i class="fa fa-plus"></i> <?= __('new_purchase_order') ?>
        </a>
    </div>

    <?php if (empty($orders)): ?>
        <div class="flex-center flex-col p-4">
            <i class="fa fa-shopping-cart text-dim text-4xl mb-1"></i>
            <p class="text-dim text-lg"><?= __('no_pending_purchase_orders') ?></p>
            <a href="<?= url('/purchases/create') ?>" class="btn btn-primary mt-1">
                <?= __('create_first_order') ?>
            </a>
        </div>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th><?= __('number') ?></th>
                        <th><?= __('supplier') ?></th>
                        <th class="text-right"><?= __('total') ?></th>
                        <th class="text-center"><?= __('status') ?></th>
                        <th class="text-center"><?= __('date') ?></th>
                        <th class="text-center"><?= __('actions') ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($orders as $order): ?>
                        <tr>
                            <td class="font-600"><?= $order['order_number'] ?></td>
                            <td><?= htmlspecialchars($order['supplier_name']) ?></td>
                            <td class="text-right font-600">
                                $<?= number_format($order['total_cost'], 2) ?>
                            </td>
                            <td class="text-center">
                                <?php
                                $statusClasses = [
                                    'pending' => 'badge-warning',
                                    'partial' => 'badge-info',
                                    'received' => 'badge-success',
                                    'cancelled' => 'badge-danger'
                                ];
                                $statusLabels = [
                                    'pending' => __('pending'),
                                    'partial' => __('partial'),
                                    'received' => __('received'),
                                    'cancelled' => __('cancelled')
                                ];
                                $class = $statusClasses[$order['status']] ?? 'badge-secondary';
                                $label = $statusLabels[$order['status']] ?? $order['status'];
                                ?>
                                <span class="badge <?= $class ?>">
                                    <?= $label ?>
                                </span>
                            </td>
                            <td class="text-center text-dim">
                                <?= date('d/m/Y', strtotime($order['created_at'])) ?>
                            </td>
                            <td class="text-center">
                                <?php if ($order['status'] !== 'received' && $order['status'] !== 'cancelled'): ?>
                                    <a href="<?= url('/purchases/receive?id=' . $order['id']) ?>" 
                                       class="btn btn-primary btn-sm">
                                        <i class="fa fa-truck"></i> <?= __('receive') ?>
                                    </a>
                                <?php else: ?>
                                    <span class="text-dim">-</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>
