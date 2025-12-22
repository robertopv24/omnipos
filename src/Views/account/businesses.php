<div class="glass-effect p-2 rounded" style="max-width: 900px; margin: 2rem auto;">
    <div class="flex-center flex-col mb-2">
        <h1 class="text-white font-800 text-lg mb-1">
            <?= __('welcome') ?>, <span class="text-bright"><?= \OmniPOS\Core\Session::get('user_name') ?></span>
        </h1>
        <p class="text-dim"><?= __('select_manage_today') ?></p>
    </div>

    <div class="dashboard-grid" style="grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));">
        <?php foreach ($businesses as $b): ?>
            <div class="business-card" onclick="location.href='<?= url('/account/switch?id=' . $b['id']) ?>'">
                <div class="business-card-icon">
                    <i class="fa fa-briefcase"></i>
                </div>

                <h3 class="text-white font-700 m-0"><?= htmlspecialchars($b['name']) ?></h3>
                <p class="text-dim text-sm mb-1 opacity-8"><?= htmlspecialchars($b['tax_id']) ?></p>

                <?php if (\OmniPOS\Core\Session::get('business_id') === $b['id']): ?>
                    <div style="position: absolute; top: 1rem; right: 1rem;">
                        <span class="badge badge-info"><?= __('active') ?></span>
                    </div>
                <?php endif; ?>

                <div class="hover-action">
                    <?= __('enter_dashboard') ?> <i class="fa fa-arrow-right text-sm"></i>
                </div>
            </div>
        <?php endforeach; ?>

        <?php if (\OmniPOS\Core\Session::get('role') === 'account_admin'): ?>
            <div class="business-card business-card-dashed" onclick="location.href='<?= url('/account/business/create') ?>'">
                <i class="fa fa-plus-circle text-lg mb-1 opacity-5"></i>
                <span class="font-600 text-sm"><?= __('add_business') ?></span>
            </div>
        <?php endif; ?>
    </div>
</div>