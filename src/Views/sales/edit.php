<div class="d-flex justify-between align-center mb-3">
    <div>
        <div class="d-flex align-center gap-1">
            <a href="<?= url('/sales/show?id=' . $sale['id']) ?>" class="btn-icon bg-bright hover-primary transition-all">
                <i class="fa fa-arrow-left"></i>
            </a>
            <div>
                <h1 class="text-3xl font-900 text-gradient m-0"><?= __('edit_sale') ?> #<?= strtoupper(substr($sale['id'], 0, 8)) ?></h1>
                <p class="text-dim mt-025 font-600"><?= __('manage_sales_description') ?></p>
            </div>
        </div>
    </div>
</div>

<div class="row gap-2">
    <div class="col-7">
        <div class="glass-effect p-2-5 rounded-xl border-bright shadow-xl bg-glass-dark">
            <h3 class="m-0 mb-2-5 text-lg font-800 d-flex align-center gap-05 border-bottom border-bright pb-1">
                <i class="fa fa-edit text-primary"></i> <?= __('update_order_details') ?>
            </h3>
            
            <form action="<?= url('/sales/update?id=' . $sale['id']) ?>" method="POST">
                <div class="form-grid d-grid grid-cols-2 gap-2">
                    <!-- Selección de Cliente -->
                    <div class="form-group col-span-2">
                        <label for="client_id" class="form-label font-700 text-xs uppercase tracking-wider text-dim mb-075 d-block"><?= __('client') ?></label>
                        <div class="select-wrapper relative">
                            <select name="client_id" id="client_id" class="form-control bg-bright border-bright pl-1 pr-3 py-1 font-700 rounded-lg w-100 appearance-none">
                                <option value=""><?= __('select_client_optional') ?></option>
                                <?php foreach ($clients as $client): ?>
                                    <option value="<?= $client['id'] ?>" <?= ($sale['client_id'] === $client['id']) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($client['name']) ?> (<?= $client['tax_id'] ?? 'N/A' ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <i class="fa fa-chevron-down absolute right-1 top-50 translate-y-n50 text-dim pointer-events-none"></i>
                        </div>
                    </div>

                    <!-- Selección de Estado -->
                    <div class="form-group">
                        <label for="status" class="form-label font-700 text-xs uppercase tracking-wider text-dim mb-075 d-block"><?= __('status') ?></label>
                        <div class="select-wrapper relative">
                            <select name="status" id="status" class="form-control bg-bright border-bright pl-1 pr-3 py-1 font-700 rounded-lg w-100 appearance-none">
                                <option value="paid" <?= ($sale['status'] === 'paid') ? 'selected' : '' ?>><?= __('paid') ?></option>
                                <option value="pending" <?= ($sale['status'] === 'pending') ? 'selected' : '' ?>><?= __('pending') ?></option>
                                <option value="cancelled" <?= ($sale['status'] === 'cancelled') ? 'selected' : '' ?>><?= __('cancelled') ?></option>
                            </select>
                            <i class="fa fa-chevron-down absolute right-1 top-50 translate-y-n50 text-dim pointer-events-none"></i>
                        </div>
                    </div>

                    <!-- Información de solo lectura -->
                    <div class="form-group">
                        <label class="form-label font-700 text-xs uppercase tracking-wider text-dim mb-075 d-block"><?= __('total') ?></label>
                        <div class="form-control bg-dim border-bright pl-1 py-1 font-800 rounded-lg text-pure opacity-70">
                            <?= \OmniPOS\Services\LocalizationService::formatCurrency($sale['total_price']) ?>
                        </div>
                    </div>

                    <div class="form-group col-span-2">
                        <div class="p-1-5 bg-amber-10 border border-amber-20 rounded-lg d-flex gap-1 align-start mt-1">
                            <i class="fa fa-exclamation-triangle text-amber mt-025"></i>
                            <p class="text-xs text-amber-dark m-0 leading-relaxed font-600">
                                <strong>Nota importante:</strong> Cambiar el estado a "Cancelado" no revierte automáticamente el inventario en esta versión. Debe realizar los ajustes manuales si es necesario.
                            </p>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-end mt-3 pt-2 border-top border-bright gap-1">
                    <a href="<?= url('/sales/show?id=' . $sale['id']) ?>" class="btn btn-secondary px-2">
                        <?= __('cancel') ?>
                    </a>
                    <button type="submit" class="btn btn-primary px-3 shadow-glow font-800">
                        <i class="fa fa-save mr-05"></i> <?= __('update_sale') ?>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="col-5">
        <div class="glass-effect p-2 rounded-xl border-bright">
            <h3 class="m-0 mb-1-5 text-lg font-800 border-bottom border-bright pb-1"><?= __('original_summary') ?></h3>
            <div class="d-flex flex-column gap-1">
                <?php foreach ($sale['items'] as $item): ?>
                    <div class="d-flex justify-between align-center p-075 bg-dim rounded-lg border border-bright">
                        <div class="flex-grow">
                            <div class="font-700 text-sm text-pure"><?= htmlspecialchars($item['product_name']) ?></div>
                            <div class="text-xs text-dim"><?= $item['quantity'] ?> x <?= \OmniPOS\Services\LocalizationService::formatCurrency($item['price']) ?></div>
                        </div>
                        <div class="font-800 text-sm">
                            <?= \OmniPOS\Services\LocalizationService::formatCurrency($item['price'] * $item['quantity']) ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            
            <div class="mt-2 pt-2 border-top border-bright d-flex justify-between align-center">
                <span class="text-sm font-800 text-dim lowercase"><?= __('total') ?></span>
                <span class="text-xl font-900 text-gradient">
                    <?= \OmniPOS\Services\LocalizationService::formatCurrency($sale['total_price']) ?>
                </span>
            </div>
        </div>
    </div>
</div>

<style>
.row { display: flex; flex-wrap: wrap; margin-left: -1rem; margin-right: -1rem; }
.col-7 { flex: 0 0 58.333333%; max-width: 58.333333%; padding: 1rem; }
.col-5 { flex: 0 0 41.666667%; max-width: 41.666667%; padding: 1rem; }

@media (max-width: 992px) {
    .col-7, .col-5 { flex: 0 0 100%; max-width: 100%; }
}
</style>
