<div class="glass-effect p-2 mt-2 rounded">
    <div class="d-flex justify-between align-center mb-2">
        <div>
            <h1 class="text-white text-xl font-700 m-0"><?= $title ?></h1>
            <p class="text-dim mt-05">SKU: <?= $product['sku'] ?></p>
        </div>
        <a href="<?= url('/products') ?>" class="btn btn-secondary">
            <i class="fa fa-arrow-left"></i> <?= __('back_to_products') ?>
        </a>
    </div>

    <!-- Resumen de Lotes -->
    <div class="mb-3">
        <h2 class="text-lg text-bright mb-1"><?= __('batches_in_inventory') ?></h2>
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th><?= __('batch_number') ?></th>
                        <th><?= __('received_date') ?></th>
                        <th><?= __('initial') ?></th>
                        <th><?= __('current') ?></th>
                        <th><?= __('unit_cost') ?></th>
                        <th><?= __('expiry') ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($batches as $batch): ?>
                        <tr>
                            <td class="font-mono"><?= $batch['batch_number'] ?: 'N/A' ?></td>
                            <td><?= date('d/m/Y H:i', strtotime($batch['received_at'])) ?></td>
                            <td><?= number_format($batch['initial_quantity'], 2) ?></td>
                            <td class="font-700">
                                <?= number_format($batch['current_quantity'], 2) ?>
                            </td>
                            <td>$<?= number_format($batch['unit_cost'], 2) ?></td>
                            <td>
                                <?= $batch['expiry_date'] ? date('d/m/Y', strtotime($batch['expiry_date'])) : '<span class="text-dim">N/A</span>' ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Historial de Movimientos -->
    <div>
        <h2 class="text-lg text-info mb-1"><?= __('movement_history_traceability') ?></h2>
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th><?= __('date_time') ?></th>
                        <th><?= __('batch') ?></th>
                        <th><?= __('type') ?></th>
                        <th><?= __('quantity') ?></th>
                        <th><?= __('reference') ?></th>
                        <th><?= __('user') ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($movements as $mov): ?>
                        <tr>
                            <td><?= date('d/m/Y H:i', strtotime($mov['created_at'])) ?></td>
                            <td class="font-mono"><?= $mov['batch_number'] ?: 'N/A' ?></td>
                            <td>
                                <span class="badge <?= $mov['type'] == 'exit' ? 'badge-danger' : 'badge-success' ?>">
                                    <?= strtoupper($mov['type']) ?>
                                </span>
                            </td>
                            <td class="font-700 <?= $mov['type'] == 'exit' ? 'text-danger' : 'text-success' ?>">
                                <?= $mov['type'] == 'exit' ? '-' : '+' ?> <?= number_format($mov['quantity'], 2) ?>
                            </td>
                            <td class="text-dim">
                                <?= strtoupper($mov['reference_type']) ?>: <?= substr($mov['reference_id'], 0, 8) ?>
                            </td>
                            <td><?= htmlspecialchars($mov['user_name']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>