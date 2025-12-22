<div class="content-header">
    <h1><i class="fas fa-chart-line"></i> <?= __('reports_and_audit') ?></h1>
</div>

<div class="d-grid gap-2" style="grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));">
    <!-- Reporte de Ventas / Libro de IVA -->
    <div class="card">
        <div class="card-body">
            <h3><i class="fas fa-file-invoice-dollar"></i> <?= __('sales_book_vat') ?></h3>
            <p class="text-dim mb-3"><?= __('generate_tax_report_desc') ?></p>
            
            <form action="<?= url('/reports/export-tax') ?>" method="GET">
                <div class="form-group">
                    <label><?= __('from') ?></label>
                    <input type="date" name="start_date" class="form-control" value="<?= date('Y-m-01') ?>">
                </div>
                <div class="form-group">
                    <label><?= __('to') ?></label>
                    <input type="date" name="end_date" class="form-control" value="<?= date('Y-m-d') ?>">
                </div>
                <div class="form-group">
                    <label><?= __('format') ?></label>
                    <select name="format" class="form-control">
                        <option value="excel"><?= __('excel_csv') ?></option>
                        <option value="pdf"><?= __('pdf_preview') ?></option>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary w-full mt-2">
                    <i class="fas fa-download"></i> <?= __('export_report') ?>
                </button>
            </form>
        </div>
    </div>

    <!-- Reporte de Ventas Generales -->
    <!-- Reporte de Ventas Generales -->
    <div class="card opacity-70">
        <div class="card-body">
            <h3><i class="fas fa-shopping-cart"></i> <?= __('detailed_sales') ?></h3>
            <p class="text-dim mb-3"><?= __('detailed_sales_desc') ?></p>
            <div class="badge btn-secondary w-full text-center p-2">
                <i class="fas fa-lock"></i> <?= __('coming_soon_v2_1') ?>
            </div>
        </div>
    </div>
</div>
