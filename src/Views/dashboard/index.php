<h1 class="text-gradient mb-05 border-none">
    <?= __('welcome') ?>, <?= htmlspecialchars($user_name) ?>!
</h1>
<p class="text-lg text-dim font-500">
    <?= __('whats_happening_today') ?> <span class="text-bright"><?= \OmniPOS\Core\Session::get('business_name') ?? __('your_business') ?></span>.
</p>
</div>

<div class="dashboard-grid">
    <div class="glass-widget">
        <div class="stat-header">
            <i class="fa fa-chart-line text-bright"></i>
            <?= __('daily_sales') ?>
        </div>
        <div class="stat-main">
            <?= \OmniPOS\Services\LocalizationService::formatCurrency($metrics['total_sales'] ?? 0.00) ?>
        </div>
        <div class="mt-1 text-sm text-success font-600 d-flex align-center gap-05">
            <i class="fa fa-arrow-trend-up"></i> <?= __('real_time_data') ?>
        </div>
    </div>

    <div class="glass-widget">
        <div class="stat-header">
            <i class="fa fa-receipt text-warning"></i>
            <?= __('pending_cxc') ?>
        </div>
        <div class="stat-main text-warning">
            <?= \OmniPOS\Services\LocalizationService::formatCurrency($metrics['total_cxc'] ?? 0.00) ?>
        </div>
        <div class="mt-1 text-sm text-dim font-600">
            <?= __('pending_collections') ?>
        </div>
    </div>

    <div class="glass-widget">
        <div class="stat-header">
            <i class="fa fa-cash-register text-info"></i>
            <?= __('cash_balance') ?>
        </div>
        <div class="stat-main text-pure">
            <?= \OmniPOS\Services\LocalizationService::formatCurrency($metrics['total_cash'] ?? 0.00) ?>
        </div>
        <div class="mt-1 text-sm text-dim font-600 d-flex align-center gap-05">
            <i class="fa fa-circle text-success" style="font-size: 0.5rem;"></i> <?= __('active_sessions') ?>
        </div>
    </div>
</div>

<div class="mt-4">
    <div class="glass-widget p-2"
        style="background: linear-gradient(145deg, rgba(59, 130, 246, 0.05) 0%, rgba(15, 27, 45, 1) 100%);">
        <div class="d-flex justify-between align-center">
            <div>
                <h3 class="text-white text-lg font-700 mb-05"><?= __('next_steps') ?></h3>
                <p class="text-dim text-md"><?= __('no_sales_yet') ?></p>
            </div>
            <a href="<?= url('/pos') ?>" class="btn btn-primary">
                <?= __('go_to_pos') ?>
            </a>
        </div>
    </div>
</div>