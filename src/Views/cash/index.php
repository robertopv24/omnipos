<div class="glass-effect p-2 mt-2 rounded">
    <div class="d-flex justify-between align-start mb-2">
        <div>
            <h1 class="text-white m-0"><?= $title ?></h1>
            <p class="text-dim mt-05"><?= __('daily_financial_summary') ?></p>
        </div>
        <div>
            <?php if (!$session): ?>
                <a href="<?= url('/cash/open') ?>" class="btn btn-primary">
                    <i class="fa fa-plus-circle"></i> <?= __('open_new_shift') ?>
                </a>
            <?php else: ?>
                <div class="d-flex gap-1">
                    <a href="<?= url('/cash/movement') ?>" class="btn btn-secondary">
                        <i class="fa fa-exchange-alt"></i> <?= __('petty_cash') ?>
                    </a>
                    <a href="<?= url('/cash/close') ?>" class="btn btn-danger">
                        <i class="fa fa-times-circle"></i> <?= __('close_day') ?>
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <?php if ($session): ?>
        <div class="dashboard-grid mb-3">
            <div class="glass-effect p-15 border-dim rounded">
                <div class="text-dim text-xs uppercase mb-05"><?= __('status') ?></div>
                <div class="text-success font-700 text-lg"><?= __('active') ?></div>
            </div>
            <div class="glass-effect p-15 border-dim rounded">
                <div class="text-dim text-xs uppercase mb-05"><?= __('opening_usd') ?></div>
                <div class="text-white font-700 text-lg">
                    $<?= number_format($session['opening_balance_usd'], 2) ?></div>
            </div>
            <div class="glass-effect p-15 border-dim rounded">
                <div class="text-dim text-xs uppercase mb-05"><?= __('opening_ves') ?></div>
                <div class="text-white font-700 text-lg">Bs
                    <?= number_format($session['opening_balance_ves'], 2) ?>
                </div>
            </div>
            <div class="glass-effect p-15 border-dim rounded">
                <div class="text-dim text-xs uppercase mb-05"><?= __('started_by') ?></div>
                <div class="text-white font-600 text-md">
                    <?= \OmniPOS\Core\Session::get('user_name') ?></div>
            </div>
        </div>

        <div class="flex-center flex-col p-4 border-dashed border-dim rounded-lg">
            <div class="text-pure opacity-3 text-3xl mb-1">
                <i class="fa fa-chart-line"></i>
            </div>
            <h3 class="text-dim m-0"><?= __('real_time_report_system') ?></h3>
            <p class="text-dim opacity-7 text-sm text-center" style="max-width: 400px;">
                <?= __('detailed_count_on_close') ?>
            </p>
        </div>
    <?php else: ?>
        <div class="flex-center flex-col p-4 glass-effect border-dim rounded-lg">
            <div class="text-pure opacity-3 text-4xl mb-2">
                <i class="fa fa-lock"></i>
            </div>
            <h2 class="text-white mb-1"><?= __('no_open_cash') ?></h2>
            <p class="text-dim mb-2 text-center" style="max-width: 400px;">
                <?= __('must_open_shift_before_sales') ?>
            </p>
            <a href="<?= url('/cash/open') ?>" class="btn btn-primary btn-lg">
                <?= __('open_shift_now') ?>
            </a>
        </div>
    <?php endif; ?>
</div>