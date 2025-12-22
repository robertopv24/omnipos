<div class="d-flex justify-between align-center mb-2">
    <div class="d-flex flex-column">
        <h1 class="m-0 mb-1"><?= __('saas_client_management') ?></h1>
        <p class="text-muted m-0"><?= __('view_manage_all_accounts') ?></p>
    </div>
    <div class="header-buttons">
        <a href="/platform/dashboard" class="btn btn-secondary">
            <i class="fas fa-chart-line"></i> <?= __('global_dashboard') ?>
        </a>
    </div>
</div>

<div class="glass-effect p-2">
    <div class="overflow-auto">
        <table class="table w-full">
            <thead>
                <tr class="text-left border-bottom border-bright">
                    <th class="p-1"><?= __('owner_admin') ?></th>
                    <th class="p-1"><?= __('email') ?></th>
                    <th class="p-1"><?= __('businesses') ?></th>
                    <th class="p-1"><?= __('registration') ?></th>
                    <th class="p-1 text-right"><?= __('actions') ?></th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($clients)): ?>
                <tr>
                    <td colspan="5" class="text-center p-5 text-muted">
                        <i class="fas fa-users d-block mb-1 opacity-50" style="font-size: 2rem;"></i>
                        <?= __('no_clients_in_platform') ?>
                    </td>
                </tr>
                <?php else: ?>
                    <?php foreach ($clients as $client): ?>
                    <tr class="border-bottom border-glass">
                        <td class="p-1">
                            <div class="font-600 text-bright"><?= htmlspecialchars($client['owner_name']) ?></div>
                            <div class="text-xs text-muted">ID: <?= substr($client['account_id'], 0, 8) ?>...</div>
                        </td>
                        <td class="p-1"><?= htmlspecialchars($client['email']) ?></td>
                        <td class="p-1">
                            <span class="badge badge-info">
                                <?= $client['business_count'] ?> <?= __('business_count_label') ?>
                            </span>
                        </td>
                        <td class="p-1 text-sm text-muted">
                            <?= date('d/m/Y', strtotime($client['created_at'])) ?>
                        </td>
                        <td class="p-1 text-right">
                            <div class="d-flex gap-1 justify-end">
                                <a href="/account/businesses?account_id=<?= $client['account_id'] ?>" class="btn-icon" title="<?= __('explore_businesses') ?>">
                                    <i class="fas fa-external-link-alt"></i>
                                </a>
                                <a href="/users?account_id=<?= $client['account_id'] ?>" class="btn-icon" title="<?= __('view_users') ?>">
                                    <i class="fas fa-user-shield"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
