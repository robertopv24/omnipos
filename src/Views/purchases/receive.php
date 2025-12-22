<div class="container-fluid">
    <h1><?= $title ?></h1>

    <div class="glass-effect p-2 mt-2">
        <div class="form-grid mb-2">
            <div>
                <label class="text-dim text-sm"><?= __('supplier') ?></label>
                <p class="font-700 text-xl m-0"><?= htmlspecialchars($order['supplier_name']) ?></p>
            </div>
            <div>
                <label class="text-dim text-sm"><?= __('order_number') ?></label>
                <p class="font-700 text-xl text-primary m-0"><?= $order['order_number'] ?></p>
            </div>
            <div>
                <label class="text-dim text-sm"><?= __('estimated_total') ?></label>
                <p class="font-700 text-xl m-0">$<?= number_format($order['total_cost'], 2) ?></p>
            </div>
        </div>

        <?php if ($order['notes']): ?>
            <div class="p-1 px-2 bg-main rounded border-subtle">
                <label class="text-dim text-xs uppercase tracking-wide"><?= __('order_notes') ?></label>
                <p class="mt-1 m-0"><?= htmlspecialchars($order['notes']) ?></p>
            </div>
        <?php endif; ?>
    </div>

    <form action="<?= url('/purchases/receive') ?>" method="POST">
        <input type="hidden" name="order_id" value="<?= $order['id'] ?>">
        
        <div class="glass-effect p-2 mt-2">
            <h3 class="mb-2 text-primary d-flex align-center gap-1">
                <i class="fa fa-boxes"></i> <?= __('items_to_receive') ?>
            </h3>
            
            <table class="table w-full">
                <thead>
                    <tr class="text-left text-dim text-sm uppercase tracking-wide">
                        <th class="p-1 border-bottom border-bright"><?= __('item') ?></th>
                        <th class="p-1 border-bottom border-bright text-center"><?= __('type') ?></th>
                        <th class="p-1 border-bottom border-bright text-right"><?= __('ordered') ?></th>
                        <th class="p-1 border-bottom border-bright text-right"><?= __('received_qty') ?></th>
                        <th class="p-1 border-bottom border-bright text-right"><?= __('receive_now') ?></th>
                        <th class="p-1 border-bottom border-bright text-right"><?= __('est_cost') ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($items as $item): ?>
                        <?php 
                        $pending = $item['quantity'] - $item['received_quantity'];
                        $canReceive = $pending > 0;
                        ?>
                        <tr class="border-bottom border-subtle transition-bg">
                            <td class="p-1">
                                <div class="font-700"><?= htmlspecialchars($item['item_name']) ?></div>
                                <div class="text-xs text-dim mt-1">ID: <?= substr($item['item_id'], 0, 8) ?></div>
                            </td>
                            <td class="p-1 text-center">
                                <span class="badge badge-primary">
                                    <?= $item['item_type'] === 'product' ? __('product') : __('supply') ?>
                                </span>
                            </td>
                            <td class="p-1 text-right font-500"><?= number_format($item['quantity'], 2) ?></td>
                            <td class="p-1 text-right text-dim">
                                <?= number_format($item['received_quantity'], 2) ?>
                            </td>
                            <td class="p-1 text-right">
                                <?php if ($canReceive): ?>
                                    <input type="number" 
                                           name="received[<?= $item['id'] ?>]" 
                                           class="form-control text-right d-inline-block w-120"
                                           step="0.01" 
                                           min="0" 
                                           max="<?= $pending ?>"
                                           value="<?= $pending ?>">
                                <?php else: ?>
                                    <span class="text-success font-700 d-inline-flex align-center gap-1">
                                        <i class="fa fa-check-double"></i> <?= __('complete') ?>
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td class="p-1 text-right text-dim font-mono">
                                $<?= number_format($item['unit_cost'], 2) ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <div class="mt-2 d-flex gap-1 justify-end">
            <a href="<?= url('/purchases') ?>" class="btn btn-secondary px-4 py-2">
                <?= __('cancel') ?>
            </a>
            <button type="submit" class="btn btn-primary px-4 py-2 text-lg">
                <i class="fa fa-check-circle"></i> <?= __('confirm_receipt') ?>
            </button>
        </div>
    </form>
</div>
