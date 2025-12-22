<div class="glass-effect p-4 mt-4">
    <div class="d-flex justify-between align-center mb-4">
        <div>
            <h1 class="h2 text-white m-0"><?= $title ?></h1>
            <p class="text-muted mt-1">Gestión centralizada de pagos de sueldos y beneficios</p>
        </div>
        <button onclick="openModal('payroll-modal')" class="btn btn-primary">
            <i class="fa fa-plus"></i> NUEVO PAGO
        </button>
    </div>

    <h3 class="h4 text-white mb-4">Historial de Pagos</h3>
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>Empleado</th>
                    <th>Fecha</th>
                    <th>Monto Neto</th>
                    <th>Deducciones (CXC)</th>
                    <th>Registrado por</th>
                    <th>Notas</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($payments)): ?>
                    <tr>
                        <td colspan="6" style="padding: 2rem; text-align: center; color: #64748b;">No hay registros de pagos
                            realizados.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($payments as $p): ?>
                        <tr>
                            <td><strong><?= htmlspecialchars($p['employee_name']) ?></strong></td>
                            <td><?= $p['payment_date'] ?></td>
                            <td class="text-success font-bold">$<?= number_format($p['amount'], 2) ?></td>
                            <td class="text-warning">$<?= number_format($p['deductions_amount'], 2) ?></td>
                            <td class="fs-sm"><?= htmlspecialchars($p['creator_name']) ?></td>
                            <td class="fs-sm text-muted"><?= htmlspecialchars($p['notes']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal para Registrar Pago de Nómina -->
<div id="payroll-modal" class="modal">
    <div class="modal-content" style="max-width: 500px;">
        <h2 class="text-white mb-4">Registrar Pago de Nómina</h2>
        <form action="<?= url('/finance/payroll/pay') ?>" method="POST">
            <div class="form-group mb-3">
                <label>Empleado</label>
                <select name="user_id" class="form-control" required>
                    <option value="">Seleccione Empleado...</option>
                    <?php foreach ($employees as $emp): ?>
                        <option value="<?= $emp['id'] ?>"><?= htmlspecialchars($emp['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-grid mb-3">
                <div class="form-group">
                    <label>Monto Neto (A pagar)</label>
                    <input type="number" name="amount" class="form-control" step="0.01" required placeholder="0.00">
                </div>
                <div class="form-group">
                    <label>Deducciones (Opcional)</label>
                    <input type="number" name="deductions_amount" class="form-control" step="0.01" placeholder="0.00">
                </div>
            </div>

            <div class="form-group mb-3">
                <label>Método de Pago</label>
                <select name="payment_method_id" class="form-control" required>
                    <?php foreach ($paymentMethods as $pm): ?>
                        <option value="<?= $pm['id'] ?>"><?= htmlspecialchars($pm['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group mb-4">
                <label>Notas / Concepto</label>
                <textarea name="notes" class="form-control" placeholder="Ej: Sueldo Primera Quincena Diciembre"
                    style="min-height: 80px;"></textarea>
            </div>

            <div class="d-flex gap-4">
                <button type="submit" class="btn btn-primary flex-2">PROCESAR PAGO</button>
                <button type="button" onclick="closeModal('payroll-modal')"
                    class="btn btn-secondary flex-1">CANCELAR</button>
            </div>
        </form>
    </div>
</div>
```