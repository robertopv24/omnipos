<div class="d-flex justify-between align-center mb-3">
    <div>
        <h1 class="text-3xl font-900 text-gradient m-0"><?= __('sales_history') ?></h1>
        <p class="text-dim mt-05"><?= __('manage_sales_description') ?></p>
    </div>
    <div class="d-flex gap-1">
        <a href="<?= url('/pos') ?>" class="btn btn-primary d-flex align-center gap-05 py-1 px-2 shadow-glow">
            <i class="fa fa-cash-register"></i> <span class="font-700"><?= __('new_sale') ?></span>
        </a>
    </div>
</div>

<div class="glass-effect overflow-hidden rounded-xl border-bright">
    <div class="table-responsive">
        <table class="table w-100">
            <thead>
                <tr class="bg-glass-dark border-bottom border-bright">
                    <th class="py-1-5 px-2 text-xs font-800 text-dim uppercase tracking-wider"><?= __('sale_id') ?></th>
                    <th class="py-1-5 px-2 text-xs font-800 text-dim uppercase tracking-wider"><?= __('date') ?></th>
                    <th class="py-1-5 px-2 text-xs font-800 text-dim uppercase tracking-wider"><?= __('customer') ?></th>
                    <th class="py-1-5 px-2 text-xs font-800 text-dim uppercase tracking-wider"><?= __('status') ?></th>
                    <th class="py-1-5 px-2 text-xs font-800 text-dim uppercase tracking-wider"><?= __('total') ?></th>
                    <th class="py-1-5 px-2 text-xs font-800 text-dim uppercase tracking-wider"><?= __('started_by') ?></th>
                    <th class="py-1-5 px-2 text-xs font-800 text-dim uppercase tracking-wider text-right"><?= __('actions') ?></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-bright">
                <?php if (empty($sales['data'])): ?>
                    <tr>
                        <td colspan="7" class="text-center py-5">
                            <i class="fa fa-inbox text-5xl mb-2 d-block opacity-20"></i>
                            <p class="text-dim text-lg"><?= __('no_sales_found') ?></p>
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($sales['data'] as $sale): ?>
                        <tr class="hover-bg-bright transition-all">
                            <td class="py-2 px-2">
                                <span class="font-mono text-xs bg-dim px-1 py-05 rounded text-pure">#<?= strtoupper(substr($sale['id'], 0, 8)) ?></span>
                            </td>
                            <td class="py-2 px-2 text-sm">
                                <span class="d-block font-600"><?= date('d/m/Y', strtotime($sale['created_at'])) ?></span>
                                <span class="text-xs text-dim"><?= date('H:i', strtotime($sale['created_at'])) ?></span>
                            </td>
                            <td class="py-2 px-2">
                                <?php if ($sale['client_name']): ?>
                                    <div class="d-flex align-center gap-1">
                                        <div class="avatar-sm bg-primary-20 text-primary font-800 rounded-full d-flex align-center justify-center" style="width:32px; height:32px;">
                                            <?= strtoupper(substr($sale['client_name'], 0, 1)) ?>
                                        </div>
                                        <div class="d-flex flex-column">
                                            <span class="font-600 text-sm"><?= htmlspecialchars($sale['client_name']) ?></span>
                                        </div>
                                    </div>
                                <?php else: ?>
                                    <span class="text-dim italic text-sm"><?= __('final_consumer') ?></span>
                                <?php endif; ?>
                            </td>
                            <td class="py-2 px-2">
                                <?php
                                $statusClass = match ($sale['status']) {
                                    'paid' => 'bg-emerald text-emerald-dark',
                                    'pending' => 'bg-amber text-amber-dark',
                                    'cancelled' => 'bg-rose text-rose-dark',
                                    default => 'bg-slate text-slate-dark'
                                };
                                $statusLabel = match ($sale['status']) {
                                    'paid' => __('paid'),
                                    'pending' => __('pending'),
                                    'cancelled' => __('cancelled'),
                                    default => $sale['status']
                                };
                                ?>
                                <span class="badge-dot <?= $statusClass ?>"><?= $statusLabel ?></span>
                            </td>
                            <td class="py-2 px-2 font-800 text-pure">
                                <?= \OmniPOS\Services\LocalizationService::formatCurrency($sale['total_price']) ?>
                            </td>
                            <td class="py-2 px-2">
                                <div class="d-flex align-center gap-05 text-sm text-dim">
                                    <i class="fa fa-user-circle opacity-50"></i>
                                    <span><?= htmlspecialchars($sale['user_name'] ?? 'System') ?></span>
                                </div>
                            </td>
                            <td class="py-2 px-2 text-right">
                                <a href="<?= url('/sales/show?id=' . $sale['id']) ?>" class="btn-icon bg-bright hover-primary transition-all" title="<?= __('view_details') ?>">
                                    <i class="fa fa-eye"></i>
                                </a>
                                <a href="<?= url('/sales/edit?id=' . $sale['id']) ?>" class="btn-icon bg-bright hover-amber transition-all ml-05" title="<?= __('edit') ?>">
                                    <i class="fa fa-edit"></i>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Paginación -->
    <?php if ($sales['last_page'] > 1): ?>
    <div class="px-2 py-1-5 bg-glass-dark border-top border-bright d-flex justify-between align-center">
        <span class="text-dim text-xs font-600 uppercase tracking-tighter">
            <?= __('page') ?> <?= $sales['current_page'] ?> <?= __('of') ?> <?= $sales['last_page'] ?>
        </span>
        <div class="d-flex gap-05">
            <?php if ($sales['current_page'] > 1): ?>
                <a href="<?= url('/sales?page=' . ($sales['current_page'] - 1)) ?>" class="btn btn-secondary btn-sm rounded-lg px-2 shadow-sm">
                    <i class="fa fa-chevron-left mr-05"></i> <?= __('previous') ?>
                </a>
            <?php endif; ?>
            
            <?php if ($sales['current_page'] < $sales['last_page']): ?>
                <a href="<?= url('/sales?page=' . ($sales['current_page'] + 1)) ?>" class="btn btn-secondary btn-sm rounded-lg px-2 shadow-sm">
                    <?= __('next') ?> <i class="fa fa-chevron-right ml-05"></i>
                </a>
            <?php endif; ?>
        </div>
    </div>
    <?php endif; ?>
</div>

<style>
.badge-dot {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.25rem 0.75rem;
    border-radius: 9999px;
    font-size: 0.75rem;
    font-weight: 700;
}
.badge-dot::before {
    content: '';
    width: 6px;
    height: 6px;
    border-radius: 50%;
    background-color: currentColor;
}
.hover-bg-bright:hover {
    background-color: rgba(255, 255, 255, 0.05);
}
.divide-bright > * + * {
    border-top: 1px solid var(--border-bright);
}
</style>
