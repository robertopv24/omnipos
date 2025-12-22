<div class="glass-effect p-2 rounded" style="max-width: 600px; margin: 2rem auto;">
    <div class="d-flex justify-between align-center mb-2">
        <h1 class="text-white m-0"><?= $title ?></h1>
        <span class="badge badge-success"><?= __('active_shift') ?></span>
    </div>

    <div class="glass-effect p-15 border-dim rounded mb-2">
        <h3 class="text-dim text-xs uppercase mb-1">
            <?= __('system_calculated_summary') ?>
        </h3>
        <div class="dashboard-grid gap-15">
            <div>
                <div class="text-dim text-xs"><?= __('expected_usd') ?></div>
                <div class="text-white font-700 text-lg">
                    $<?= number_format($balances['usd'], 2) ?></div>
            </div>
            <div>
                <div class="text-dim text-xs"><?= __('expected_ves') ?></div>
                <div class="text-white font-700 text-lg">Bs
                    <?= number_format($balances['ves'], 2) ?>
                </div>
            </div>
        </div>
    </div>

    <form action="<?= url('/cash/close') ?>" method="POST">
        <h3 class="text-bright text-md mb-15"><?= __('manual_count_cash') ?></h3>

        <div class="dashboard-grid gap-15 mb-2">
            <div class="form-group">
                <label class="text-dim text-xs mb-05 block"><?= __('actual_close_usd') ?></label>
                <input type="number" name="closing_balance_usd" step="0.01" required placeholder="0.00"
                    class="form-control">
            </div>
            <div class="form-group">
                <label class="text-dim text-xs mb-05 block"><?= __('actual_close_ves') ?></label>
                <input type="number" name="closing_balance_ves" step="0.01" required placeholder="0.00"
                    class="form-control">
            </div>
        </div>

        <div class="mb-2">
            <label class="text-dim text-xs mb-05 block"><?= __('observations_differences') ?></label>
            <textarea name="notes" class="form-control" style="min-height: 80px;"></textarea>
        </div>

        <button type="submit" class="btn btn-primary w-100 font-700 p-1 text-lg"
            onclick="return confirm('<?= __('confirm_close_shift') ?>')">
            <?= __('finalize_close_cash') ?>
        </button>
    </form>
</div>