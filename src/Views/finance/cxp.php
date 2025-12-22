<div class="glass-effect p-2 mt-4 rounded">
    <div class="d-flex justify-between align-center mb-2">
        <div>
            <h1 class="text-white m-0"><?= $title ?></h1>
            <p class="text-dim mt-05"><?= __('cxp_description') ?></p>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th><?= __('supplier') ?></th>
                    <th><?= __('original_amount') ?></th>
                    <th><?= __('settled') ?></th>
                    <th><?= __('to_pay') ?></th>
                    <th><?= __('due_date') ?></th>
                    <th><?= __('status') ?></th>
                    <th><?= __('actions') ?></th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($pendingCxp)): ?>
                    <tr>
                        <td colspan="7" class="text-center p-2 text-dim"><?= __('no_pending_cxp') ?></td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($pendingCxp as $cxp): ?>
                        <tr>
                            <td>
                                <strong><?= htmlspecialchars($cxp['supplier_name']) ?></strong>
                            </td>
                            <td>$<?= number_format($cxp['amount'], 2) ?></td>
                            <td class="text-success">$<?= number_format($cxp['paid_amount'], 2) ?></td>
                            <td class="text-danger font-700">
                                $<?= number_format($cxp['amount'] - $cxp['paid_amount'], 2) ?>
                            </td>
                            <td><?= $cxp['due_date'] ?: 'N/A' ?></td>
                            <td>
                                <span class="badge <?= $cxp['status'] == 'partial' ? 'badge-warning' : 'badge-danger' ?>">
                                    <?= strtoupper($cxp['status']) ?>
                                </span>
                            </td>
                            <td>
                                <button
                                    onclick="openFinancePaymentModal('cxp', '<?= $cxp['id'] ?>', <?= $cxp['amount'] - $cxp['paid_amount'] ?>, '<?= url('/finance/cxp/pay') ?>')"
                                    class="btn btn-primary btn-sm">
                                    <i class="fa fa-money-bill-wave"></i> <?= __('settle') ?>
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal para Pago -->
<div id="payment-modal" class="modal">
    <div class="modal-content" style="max-width: 400px;">
        <div class="d-flex justify-between align-center mb-4">
            <h2 class="text-white m-0"><?= __('register_supplier_payment') ?></h2>
            <button type="button" class="btn-icon" onclick="closeModal('payment-modal')">&times;</button>
        </div>
        <form id="payment-form" action="<?= url('/finance/cxp/pay') ?>" method="POST">
            <input type="hidden" name="id" id="cxp-id">

            <div class="form-group mb-4">
                <label><?= __('amount_to_pay') ?></label>
                <input type="number" name="amount" id="payment-amount" class="form-control" step="0.01" required>
                <small class="text-dim mt-05 block"><?= __('pending_balance') ?>: <span id="max-amount" class="text-danger"></span></small>
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
                <button type="submit" class="btn btn-success flex-2"><?= __('confirm_payment') ?></button>
                <button type="button" onclick="closeModal('payment-modal')" class="btn btn-secondary flex-1"><?= __('cancel') ?></button>
            </div>
        </form>
    </div>
</div>
