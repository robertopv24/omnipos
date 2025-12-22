<div class="d-flex justify-between align-center mb-3">
    <div>
        <div class="d-flex align-center gap-1">
            <a href="<?= url('/sales') ?>" class="btn-icon bg-bright hover-primary transition-all">
                <i class="fa fa-arrow-left"></i>
            </a>
            <div>
                <h1 class="text-3xl font-900 text-gradient m-0"><?= __('sale_id') ?> #<?= strtoupper(substr($sale['id'], 0, 8)) ?></h1>
                <p class="text-dim mt-025 font-600"><?= date('d/m/Y H:i', strtotime($sale['created_at'])) ?></p>
            </div>
        </div>
    </div>
    <div class="d-flex gap-1">
        <button onclick="window.print()" class="btn btn-secondary d-flex align-center gap-05 py-1 px-2 shadow-sm">
            <i class="fa fa-print"></i> <span><?= __('print_receipt') ?></span>
        </button>
        <a href="<?= url('/sales/edit?id=' . $sale['id']) ?>" class="btn btn-primary d-flex align-center gap-05 py-1 px-2 shadow-glow">
            <i class="fa fa-edit"></i> <span class="font-700"><?= __('edit') ?></span>
        </a>
    </div>
</div>

<div class="row gap-2">
    <!-- Columna Principal: Items de la Orden -->
    <div class="col-8">
        <div class="glass-effect overflow-hidden rounded-xl border-bright">
            <div class="p-2 border-bottom border-bright bg-glass-dark">
                <h3 class="m-0 text-lg font-800 d-flex align-center gap-05">
                    <i class="fa fa-list-ul text-primary"></i> <?= __('order_items') ?>
                </h3>
            </div>
            <div class="table-responsive">
                <table class="table w-100">
                    <thead>
                        <tr class="bg-glass-dark border-bottom border-bright">
                            <th class="py-1 px-2 text-xs font-800 text-dim uppercase"><?= __('product') ?></th>
                            <th class="py-1 px-2 text-xs font-800 text-dim uppercase text-center"><?= __('quantity') ?></th>
                            <th class="py-1 px-2 text-xs font-800 text-dim uppercase text-right"><?= __('unit_cost') ?></th>
                            <th class="py-1 px-2 text-xs font-800 text-dim uppercase text-right"><?= __('total') ?></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-bright">
                        <?php foreach ($sale['items'] as $item): ?>
                            <tr>
                                <td class="py-2 px-2">
                                    <div class="font-700 text-pure"><?= htmlspecialchars($item['product_name']) ?></div>
                                    <div class="text-xs text-dim mt-025">SKU: <?= $item['sku'] ?></div>
                                </td>
                                <td class="py-2 px-2 text-center font-600"><?= $item['quantity'] ?></td>
                                <td class="py-2 px-2 text-right text-dim">
                                    <?= \OmniPOS\Services\LocalizationService::formatCurrency($item['price']) ?>
                                </td>
                                <td class="py-2 px-2 text-right font-800 text-pure">
                                    <?= \OmniPOS\Services\LocalizationService::formatCurrency($item['price'] * $item['quantity']) ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Columna Lateral: Resumen e Información -->
    <div class="col-4 d-flex flex-column gap-2">
        <!-- Tarjeta de Totales -->
        <div class="glass-effect p-2 rounded-xl border-bright bg-glass-dark shadow-xl">
            <h3 class="m-0 mb-1-5 text-lg font-800 border-bottom border-bright pb-1"><?= __('summary') ?></h3>
            <div class="d-flex justify-between mb-075 text-sm">
                <span class="text-dim">Subtotal</span>
                <span class="font-600">--</span>
            </div>
            <div class="d-flex justify-between mb-075 text-sm">
                <span class="text-dim"><?= __('taxes') ?> (16%)</span>
                <span class="font-600">--</span>
            </div>
            <div class="d-flex justify-between mt-1-5 pt-1-5 border-top border-bright">
                <span class="text-lg font-800"><?= __('total') ?></span>
                <span class="text-2xl font-900 text-gradient">
                    <?= \OmniPOS\Services\LocalizationService::formatCurrency($sale['total_price']) ?>
                </span>
            </div>
        </div>

        <!-- Estado de la Orden -->
        <div class="glass-effect p-2 rounded-xl border-bright">
            <h3 class="m-0 mb-1-5 text-lg font-800 d-flex align-center gap-05">
                <i class="fa fa-info-circle text-primary"></i> <?= __('order_status') ?>
            </h3>
            
            <div class="d-flex justify-between align-center">
                <span class="text-xs font-800 text-dim uppercase"><?= __('status') ?></span>
                <?php
                $statusClass = match ($sale['status']) {
                    'paid' => 'bg-emerald text-emerald-dark',
                    'pending' => 'bg-amber text-amber-dark',
                    'cancelled' => 'bg-rose text-rose-dark',
                    default => 'bg-slate text-slate-dark'
                };
                ?>
                <span class="badge-dot <?= $statusClass ?>"><?= strtoupper(__($sale['status'])) ?></span>
            </div>
        </div>

        <!-- Cliente y Staff -->
        <div class="glass-effect p-2 rounded-xl border-bright">
            <h3 class="m-0 mb-1-5 text-lg font-800 d-flex align-center gap-05">
                <i class="fa fa-users text-primary"></i> <?= __('people_involved') ?>
            </h3>
            <div class="mb-1-5">
                <div class="text-xs font-800 text-dim uppercase tracking-wider mb-05"><?= __('customer') ?></div>
                <div class="d-flex align-center gap-1">
                    <div class="avatar-sm bg-dim text-white font-800 rounded-full d-flex align-center justify-center" style="width:32px; height:32px;">
                        <i class="fa fa-user"></i>
                    </div>
                    <div class="font-700 text-pure"><?= htmlspecialchars($sale['client_name'] ?? __('final_consumer')) ?></div>
                </div>
            </div>
            <div>
                <div class="text-xs font-800 text-dim uppercase tracking-wider mb-05"><?= __('staff') ?></div>
                <div class="d-flex align-center gap-1">
                    <div class="avatar-sm bg-dim text-white font-800 rounded-full d-flex align-center justify-center" style="width:32px; height:32px;">
                        <i class="fa fa-id-badge"></i>
                    </div>
                    <div>
                        <div class="font-700 text-pure"><?= htmlspecialchars($sale['user_name'] ?? 'System') ?></div>
                        <div class="text-xs text-dim">Cajero</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
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
.row { display: flex; flex-wrap: wrap; margin-left: -1rem; margin-right: -1rem; }
.col-8 { flex: 0 0 66.666667%; max-width: 66.666667%; padding: 1rem; }
.col-4 { flex: 0 0 33.333333%; max-width: 33.333333%; padding: 1rem; }

@media (max-width: 992px) {
    .col-8, .col-4 { flex: 0 0 100%; max-width: 100%; }
}

.divide-bright > * + * {
    border-top: 1px solid var(--border-bright);
}
</style>
