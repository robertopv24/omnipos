<div class="content-header mb-2">
    <h1><i class="fas fa-wallet text-warning"></i> <?= __('Gestión de Caja Chica') ?></h1>
    <p class="text-muted">Control de egresos menores y gastos operativos diarios.</p>
</div>

<div class="d-grid gap-2" style="grid-template-columns: 1fr 2fr;">
    <!-- Formulario de Registro -->
    <div class="glass-effect p-2 h-fit">
        <h3 class="mb-1">Registrar Gasto</h3>
        <form action="<?= url('/cash/movement') ?>" method="POST">
            <input type="hidden" name="type" value="expense">
            
            <div class="form-group mb-1">
                <label>Monto</label>
                <input type="number" name="amount" step="0.01" class="form-control" placeholder="0.00" required>
            </div>
            
            <div class="form-group mb-1">
                <label>Método de Pago (Origen)</label>
                <select name="payment_method_id" class="form-control" required>
                    <?php foreach ($paymentMethods as $pm): ?>
                        <option value="<?= $pm['id'] ?>"><?= htmlspecialchars($pm['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="form-group mb-1">
                <label>Descripción / Concepto</label>
                <textarea name="description" class="form-control" rows="2" placeholder="Ej: Pago de hielo, Transporte, etc." required></textarea>
            </div>
            
            <button type="submit" class="btn btn-warning w-full mt-1">
                <i class="fas fa-save"></i> Registrar Egreso
            </button>
        </form>
    </div>

    <!-- Historial de Movimientos -->
    <div class="glass-effect p-2">
        <h3 class="mb-1 text-bright">Últimos Movimientos</h3>
        <table class="table w-full">
            <thead>
                <tr class="text-left border-bottom border-bright">
                    <th class="p-1">Fecha</th>
                    <th class="p-1">Concepto</th>
                    <th class="p-1">Método</th>
                    <th class="p-1 text-right">Monto</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($movements)): ?>
                    <tr>
                        <td colspan="4" class="text-center p-2 text-dim">No hay movimientos recientes.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($movements as $mov): ?>
                        <tr class="border-bottom border-glass">
                            <td class="p-1 text-xs"><?= date('d/m H:i', strtotime($mov['created_at'])) ?></td>
                            <td class="p-1"><?= htmlspecialchars($mov['description']) ?></td>
                            <td class="p-1 text-xs text-muted"><?= htmlspecialchars($mov['method_name']) ?></td>
                            <td class="p-1 text-right font-600 text-danger">-<?= \OmniPOS\Services\LocalizationService::formatCurrency($mov['amount']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
