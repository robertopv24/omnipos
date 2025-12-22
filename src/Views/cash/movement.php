<div class="d-flex justify-between align-center mb-2">
    <div>
        <h1 class="text-gradient border-none mb-0"><?= __('petty_cash') ?></h1>
        <p class="text-dim">Registra ingresos o egresos manuales de la sesión actual.</p>
    </div>
    <div>
        <a href="<?= url('/cash') ?>" class="btn btn-secondary">
            <i class="fa fa-arrow-left"></i> <?= __('back') ?>
        </a>
    </div>
</div>

<div class="max-w-600 mx-auto">
    <div class="glass-widget">
        <form action="<?= url('/cash/movement') ?>" method="POST" class="d-flex flex-column gap-1-5">
            <div class="form-group">
                <label for="type" class="form-label">Tipo de Movimiento</label>
                <div class="select-wrapper">
                    <select name="type" id="type" class="form-control" required>
                        <option value="income">Ingreso (Entrada de Dinero)</option>
                        <option value="expense">Egreso / Gasto (Salida de Dinero)</option>
                    </select>
                    <i class="fa fa-chevron-down select-arrow"></i>
                </div>
            </div>

            <div class="grid-2 gap-1">
                <div class="form-group">
                    <label for="amount" class="form-label">Monto</label>
                    <input type="number" step="0.01" name="amount" id="amount" class="form-control" placeholder="0.00" required>
                </div>

                <div class="form-group">
                    <label for="payment_method_id" class="form-label">Método de Pago</label>
                    <div class="select-wrapper">
                        <select name="payment_method_id" id="payment_method_id" class="form-control" required>
                            <?php foreach ($paymentMethods as $pm): ?>
                                <option value="<?= $pm['id'] ?>"><?= htmlspecialchars($pm['name']) ?> (<?= $pm['currency'] ?>)</option>
                            <?php endforeach; ?>
                        </select>
                        <i class="fa fa-chevron-down select-arrow"></i>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label for="notes" class="form-label">Justificación / Motivo</label>
                <textarea name="notes" id="notes" class="form-control" rows="3" placeholder="Ej: Compra de artículos de limpieza, Pago de taxi..." required></textarea>
            </div>

            <div class="d-flex justify-end gap-1 mt-1">
                <button type="submit" class="btn btn-primary">
                    <i class="fa fa-save"></i> Registrar Movimiento
                </button>
            </div>
        </form>
    </div>

    <div class="mt-3">
        <div class="glass-widget bg-info-10 border-info-subtle">
            <p class="text-sm text-dim mb-0">
                <i class="fa fa-info-circle text-info"></i> Estos movimientos afectan el saldo final de la sesión de caja abierta y quedarán reflejados en el arqueo de cierre.
            </p>
        </div>
    </div>
</div>