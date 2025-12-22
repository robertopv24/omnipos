<div class="content-header d-flex justify-between align-center mb-2">
    <h1><i class="fas fa-chart-line text-primary"></i> <?= __('Análisis de Rentabilidad') ?></h1>
    <form class="d-flex gap-1 bg-glass p-1 rounded" method="GET">
        <input type="date" name="start_date" class="form-control form-control-sm" value="<?= $startDate ?>">
        <input type="date" name="end_date" class="form-control form-control-sm" value="<?= $endDate ?>">
        <button type="submit" class="btn btn-primary btn-sm"><i class="fas fa-filter"></i></button>
    </form>
</div>

<div class="d-grid gap-2 mb-2" style="grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));">
    <div class="glass-effect p-2">
        <div class="text-xs text-muted text-uppercase"><?= __('Ventas Netas') ?></div>
        <div class="text-xl font-700 text-bright"><?= \OmniPOS\Services\LocalizationService::formatCurrency($metrics['total_sales']) ?></div>
        <div class="text-xs text-success"><i class="fas fa-arrow-up"></i> Ingresos Totales</div>
    </div>
    <div class="glass-effect p-2">
        <div class="text-xs text-muted text-uppercase"><?= __('Costo de Ventas (COGS)') ?></div>
        <div class="text-xl font-700 text-danger"><?= \OmniPOS\Services\LocalizationService::formatCurrency($metrics['total_cogs']) ?></div>
        <div class="text-xs text-muted">Materia Prima Estimada</div>
    </div>
    <div class="glass-effect p-2">
        <div class="text-xs text-muted text-uppercase"><?= __('Utilidad Bruta') ?></div>
        <div class="text-xl font-700 text-success"><?= \OmniPOS\Services\LocalizationService::formatCurrency($metrics['gross_profit']) ?></div>
        <div class="text-xs text-dim">Margen: <?= $metrics['total_sales'] > 0 ? round(($metrics['gross_profit'] / $metrics['total_sales']) * 100, 1) : 0 ?>%</div>
    </div>
    <div class="glass-effect p-2 border border-primary">
        <div class="text-xs text-primary text-uppercase font-700"><?= __('Utilidad Neta') ?></div>
        <div class="text-xl font-800 text-bright"><?= \OmniPOS\Services\LocalizationService::formatCurrency($metrics['net_profit']) ?></div>
        <div class="text-xs text-dim">Después de todos los gastos</div>
    </div>
</div>

<div class="d-grid gap-2" style="grid-template-columns: 1fr 1fr;">
    <div class="glass-effect p-2">
        <h3 class="mb-1 border-bottom border-glass pb-05">Desglose de Egresos</h3>
        <div class="d-flex justify-between mb-1">
            <span>Gastos Operativos (Caja Chica):</span>
            <span class="font-600 text-danger">-<?= \OmniPOS\Services\LocalizationService::formatCurrency($metrics['total_expenses']) ?></span>
        </div>
        <div class="d-flex justify-between mb-2">
            <span>Nómina y Salarios:</span>
            <span class="font-600 text-danger">-<?= \OmniPOS\Services\LocalizationService::formatCurrency($metrics['total_payroll']) ?></span>
        </div>
        <hr class="border-glass mb-1">
        <div class="d-flex justify-between font-700">
            <span>Total Otros Gastos:</span>
            <span><?= \OmniPOS\Services\LocalizationService::formatCurrency($metrics['total_expenses'] + $metrics['total_payroll']) ?></span>
        </div>
    </div>

    <div class="glass-effect p-2 d-flex flex-column align-center justify-center text-center">
        <i class="fas fa-receipt text-huge opacity-20 mb-1"></i>
        <h4 class="text-dim italic">Análisis Gráfico próximamente</h4>
        <p class="text-sm text-muted">Estamos procesando más datos para mostrarte tendencias por hora y categoría.</p>
    </div>
</div>

<style>
.text-huge { font-size: 4rem; }
.bg-glass { background: rgba(255,255,255,0.05); }
</style>
