<div class="glass-effect p-2 mt-2">
    <div class="d-flex justify-between align-center mb-2">
        <div>
            <h1 class="text-2xl font-700 text-white m-0"><?= $title ?></h1>
            <p class="text-slate-400 mt-1"><?= __('control_audit_sensitive_ops') ?></p>
        </div>
    </div>

    <div class="overflow-auto">
        <table class="table w-full">
            <thead>
                <tr class="border-bottom border-slate-700 text-left">
                    <th class="p-1 text-slate-400"><?= __('date_time') ?></th>
                    <th class="p-1 text-slate-400"><?= __('operation') ?></th>
                    <th class="p-1 text-slate-400"><?= __('reference') ?></th>
                    <th class="p-1 text-slate-400"><?= __('amount') ?></th>
                    <th class="p-1 text-slate-400"><?= __('cashier') ?></th>
                    <th class="p-1 text-slate-400"><?= __('authorized_by') ?></th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($logs)): ?>
                    <tr>
                        <td colspan="6" class="p-2 text-center text-slate-500">
                            <?= __('no_audit_logs_available') ?>
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($logs as $log): ?>
                        <tr class="border-bottom border-slate-800 text-slate-200">
                            <td class="p-1"><?= date('d/m/Y H:i', strtotime($log['created_at'])) ?></td>
                            <td class="p-1">
                                <span class="badge bg-blue-10 text-blue">
                                    <?= strtoupper(str_replace('_', ' ', $log['operation_type'])) ?>
                                </span>
                            </td>
                            <td class="p-1 font-mono text-sm">
                                <a href="<?= url('/sales/view?id=' . $log['reference_id']) ?>" class="text-primary">
                                    <?= substr($log['reference_id'], 0, 8) ?>
                                </a>
                            </td>
                            <td class="p-1 font-bold">
                                $<?= number_format($log['amount'] ?? 0, 2) ?>
                            </td>
                            <td class="p-1">
                                <i class="fa fa-user-tag text-xs text-slate-400"></i>
                                <?= htmlspecialchars($log['cashier_name']) ?>
                            </td>
                            <td class="p-1">
                                <i class="fa fa-shield-alt text-xs text-secondary"></i>
                                <span class="font-600 text-secondary"><?= htmlspecialchars($log['supervisor_name']) ?></span>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>