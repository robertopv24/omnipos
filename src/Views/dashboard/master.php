<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2.5rem;">
    <div style="color: var(--text-muted); font-size: 1.1rem; font-weight: 500;">
        Resumen global de tus operaciones
    </div>
    <div
        style="background: rgba(59, 130, 246, 0.1); padding: 0.6rem 1.25rem; border-radius: 10px; border: 1px solid rgba(59, 130, 246, 0.2); display: flex; align-items: center; gap: 0.75rem;">
        <i class="fa fa-calendar-day" style="color: var(--primary-color); font-size: 0.9rem;"></i>
        <span style="color: white; font-weight: 600; font-size: 0.95rem;"><?= date('d M, Y') ?></span>
    </div>
</div>

<!-- Tarjetas de Métricas Globales -->
<div
    style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 1.5rem; margin-bottom: 3rem;">
    <div class="stat-card glass-effect" style="padding: 1.5rem; border-left: 4px solid #10b981;">
        <div style="color: #64748b; font-size: 0.85rem; text-transform: uppercase; margin-bottom: 0.5rem;">Ventas
            Totales (Hoy)</div>
        <div style="color: #10b981; font-weight: 700; font-size: 2rem;">
            $<?= number_format($metrics['total_sales'], 2) ?></div>
    </div>
    <div class="stat-card glass-effect" style="padding: 1.5rem; border-left: 4px solid #3b82f6;">
        <div style="color: #64748b; font-size: 0.85rem; text-transform: uppercase; margin-bottom: 0.5rem;">Efectivo
            en Caja (USD)</div>
        <div style="color: #3b82f6; font-weight: 700; font-size: 2rem;">
            $<?= number_format($metrics['total_cash'], 2) ?></div>
    </div>
    <div class="stat-card glass-effect" style="padding: 1.5rem; border-left: 4px solid #ef4444;">
        <div style="color: #64748b; font-size: 0.85rem; text-transform: uppercase; margin-bottom: 0.5rem;">CXC
            Pendiente Global</div>
        <div style="color: #ef4444; font-weight: 700; font-size: 2rem;">
            $<?= number_format($metrics['total_cxc'], 2) ?></div>
    </div>
</div>

<!-- Listado por Negocio -->
<h3 style="color: white; margin-bottom: 1.5rem;"><i class="fa fa-layer-group"></i> Rendimiento por Negocio</h3>
<div style="overflow-x: auto;">
    <table style="width: 100%; border-collapse: collapse;">
        <thead>
            <tr style="border-bottom: 1px solid #334155; text-align: left;">
                <th style="padding: 1rem; color: #94a3b8;">Sucursal / Negocio</th>
                <th style="padding: 1rem; color: #94a3b8;">Ventas de Hoy</th>
                <th style="padding: 1rem; color: #94a3b8;">CXC Pendiente</th>
                <th style="padding: 1rem; color: #94a3b8; text-align: right;">Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($metrics['business_stats'] as $b): ?>
                <tr style="border-bottom: 1px solid #1e293b; color: #e2e8f0; transition: background 0.2s;"
                    onmouseover="this.style.background='rgba(255,255,255,0.02)'"
                    onmouseout="this.style.background='transparent'">
                    <td style="padding: 1rem;">
                        <div style="font-weight: 600;"><?= htmlspecialchars($b['name']) ?></div>
                        <div style="font-size: 0.75rem; color: #64748b;">ID: <?= substr($b['id'], 0, 8) ?></div>
                    </td>
                    <td style="padding: 1rem; color: #10b981; font-weight: 600;">
                        $<?= number_format($b['daily_sales'], 2) ?></td>
                    <td style="padding: 1rem; color: #ef4444;">$<?= number_format($b['pending_cxc'], 2) ?></td>
                    <td style="padding: 1rem; text-align: right;">
                        <a href="<?= url('/account/switch?id=' . $b['id']) ?>" class="btn btn-outline"
                            style="padding: 0.4rem 0.8rem; font-size: 0.85rem; text-decoration: none;">
                            IR A ESTE NEGOCIO
                        </a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
</div>