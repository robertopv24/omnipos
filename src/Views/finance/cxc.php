<div class="glass-effect p-2 mt-4 rounded">
    <div class="d-flex justify-between align-center mb-2">
        <div>
            <h1 class="text-white m-0"><?= $title ?></h1>
            <p class="text-dim mt-05"><?= __('cxc_description') ?></p>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th><?= __('debtor') ?></th>
                    <th><?= __('type') ?></th>
                    <th><?= __('original_amount') ?></th>
                    <th><?= __('paid') ?></th>
                    <th><?= __('pending') ?></th>
                    <th><?= __('status') ?></th>
                    <th><?= __('actions') ?></th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($pendingCxc)): ?>
                    <tr>
                        <td colspan="7" class="text-center p-2 text-dim"><?= __('no_pending_cxc') ?></td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($pendingCxc as $cxc): ?>
                        <tr>
                            <td>
                                <strong><?= htmlspecialchars($cxc['client_name'] ?: $cxc['employee_name']) ?></strong>
                            </td>
                            <td>
                                <span class="badge badge-info">
                                    <?= $cxc['client_name'] ? __('client') : __('employee') ?>
                                </span>
                            </td>
                            <td>$<?= number_format($cxc['amount'], 2) ?></td>
                            <td class="text-success">$<?= number_format($cxc['paid_amount'], 2) ?></td>
                            <td class="text-danger font-700">
                                $<?= number_format($cxc['amount'] - $cxc['paid_amount'], 2) ?>
                            </td>
                            <td>
                                <span class="badge <?= $cxc['status'] == 'partial' ? 'badge-warning' : 'badge-danger' ?>">
                                    <?= strtoupper($cxc['status']) ?>
                                </span>
                            </td>
                            <td>
                                <button
                                    onclick="openFinancePaymentModal('cxc', '<?= $cxc['id'] ?>', <?= $cxc['amount'] - $cxc['paid_amount'] ?>, '<?= url('/finance/cxc/pay') ?>')"
                                    class="btn btn-primary btn-sm">
                                    <i class="fa fa-hand-holding-usd"></i> <?= __('collect') ?>
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal para Abono/Pago -->
<div id="payment-modal" class="modal">
    <div class="modal-content" style="max-width: 400px;">
        <div class="d-flex justify-between align-center mb-4">
            <h2 class="text-white m-0"><?= __('register_payment') ?></h2>
            <button type="button" class="btn-icon" onclick="closeModal('payment-modal')">&times;</button>
        </div>
        <form id="payment-form" action="<?= url('/finance/cxc/pay') ?>" method="POST">
            <input type="hidden" name="id" id="cxc-id">

            <div class="form-group mb-4">
                <label><?= __('amount_to_collect') ?></label>
                <input type="number" name="amount" id="payment-amount" class="form-control" step="0.01" required>
                <small class="text-dim mt-05 block"><?= __('max_pending_amount') ?>: <span id="max-amount" class="text-danger"></span></small>
            </div>

            <div class="form-group mb-4">
                <label><?= __('payment_method') ?></label>
                <select name="payment_method_id" class="form-control" required>
                    <?php foreach ($paymentMethods as $pm): ?>
                        <option value="<?= $pm['id'] ?>"><?= htmlspecialchars($pm['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="d-flex gap-1 mt-2">
                <button type="submit" class="btn btn-primary flex-2"><?= __('process_payment') ?></button>
                <button type="button" onclick="closeModal('payment-modal')" class="btn btn-secondary flex-1"><?= __('cancel') ?></button>
            </div>
        </form>
    </div>
</div>
