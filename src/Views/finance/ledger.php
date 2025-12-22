<div class="content-header d-flex justify-between align-center mb-2">
    <h1><i class="fas fa-book"></i> <?= __('Libro Contable (Diario)') ?></h1>
    <div class="d-flex gap-1">
        <button class="btn btn-secondary btn-sm"><i class="fas fa-file-export"></i> Exportar</button>
        <button class="btn btn-primary btn-sm"><i class="fas fa-plus"></i> Nuevo Asiento</button>
    </div>
</div>

<div class="glass-effect p-2">
    <table class="table w-full">
        <thead>
            <tr class="text-left border-bottom border-bright">
                <th class="p-1">Fecha</th>
                <th class="p-1">Descripción / Concepto</th>
                <th class="p-1 text-right">Debe</th>
                <th class="p-1 text-right">Haber</th>
                <th class="p-1 text-center">Referencia</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($entries)): ?>
                <tr>
                    <td colspan="5" class="text-center p-4 text-dim italic">No hay registros contables en este periodo.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($entries as $entry): ?>
                    <tr class="border-bottom border-glass hover-bright">
                        <td class="p-1"><?= date('d/m/Y', strtotime($entry['entry_date'])) ?></td>
                        <td class="p-1">
                            <div class="font-600"><?= htmlspecialchars($entry['description']) ?></div>
                            <div class="text-xs text-muted"><?= htmlspecialchars($entry['account_name']) ?></div>
                        </td>
                        <td class="p-1 text-right text-success"><?= $entry['debit'] > 0 ? \OmniPOS\Services\LocalizationService::formatCurrency($entry['debit']) : '-' ?></td>
                        <td class="p-1 text-right text-danger"><?= $entry['credit'] > 0 ? \OmniPOS\Services\LocalizationService::formatCurrency($entry['credit']) : '-' ?></td>
                        <td class="p-1 text-center text-xs">
                            <span class="badge badge-info"><?= strtoupper($entry['reference_type']) ?></span>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>
