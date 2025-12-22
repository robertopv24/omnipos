<div class="content-header d-flex justify-between align-center mb-2">
    <div>
        <h1 class="text-gradient mb-05 border-none"><i class="fa fa-cogs"></i> <?= __('Configuración Global') ?></h1>
        <p class="text-dim"><?= __('Parámetros fundamentales de la infraestructura SaaS y comportamiento del sistema.') ?></p>
    </div>
    <button class="btn btn-primary btn-sm"><i class="fa fa-save"></i> <?= __('Guardar Cambios') ?></button>
</div>

<div class="glass-widget p-2">
    <div class="d-grid gap-2" style="grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));">
        <div class="form-group">
            <label class="text-xs text-dim uppercase font-700 mb-05 d-block">Nombre del Sistema</label>
            <input type="text" class="form-control" value="<?= htmlspecialchars($settings['system_name']) ?>">
        </div>
        <div class="form-group">
            <label class="text-xs text-dim uppercase font-700 mb-05 d-block">Email de Soporte</label>
            <input type="email" class="form-control" value="<?= htmlspecialchars($settings['support_email'] ?? 'support@omnipos-saas.com') ?>">
        </div>
        <div class="form-group">
            <label class="text-xs text-dim uppercase font-700 mb-05 d-block">Moneda Base</label>
            <select class="form-control">
                <option selected>USD - Dólares Americanos</option>
                <option>VES - Bolívares</option>
            </select>
        </div>
        <div class="form-group">
            <label class="text-xs text-dim uppercase font-700 mb-05 d-block">Estado de la Plataforma</label>
            <select class="form-control">
                <option selected>Producción (Online)</option>
                <option>Mantenimiento (Solo Super Admins)</option>
            </select>
        </div>
    </div>
</div>

<div class="mt-4">
    <h3 class="text-bright font-700 mb-1"><i class="fa fa-terminal text-info"></i> <?= __('Monitor de Infraestructura') ?></h3>
    <div class="d-grid gap-2" style="grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));">
        <div class="glass-widget p-1 text-center border border-success">
            <div class="text-dim text-xs uppercase mb-05">Base de Datos</div>
            <div class="text-success font-800 text-lg"><i class="fa fa-check-circle"></i> CONECTADO</div>
            <div class="text-xs text-muted">Latencia: 12ms</div>
        </div>
        <div class="glass-widget p-1 text-center border border-success">
            <div class="text-dim text-xs uppercase mb-05">Almacenamiento S3</div>
            <div class="text-success font-800 text-lg"><i class="fa fa-cloud"></i> DISPONIBLE</div>
            <div class="text-xs text-muted">98% libre</div>
        </div>
        <div class="glass-widget p-1 text-center border border-warning">
            <div class="text-dim text-xs uppercase mb-05">Servicio de Correo</div>
            <div class="text-warning font-800 text-lg"><i class="fa fa-clock"></i> 2 PENDIENTES</div>
            <div class="text-xs text-muted">Cola activa</div>
        </div>
        <div class="glass-widget p-1 text-center border border-info">
            <div class="text-dim text-xs uppercase mb-05">Redis / Cache</div>
            <div class="text-info font-800 text-lg"><i class="fa fa-bolt"></i> OPTIMIZADO</div>
            <div class="text-xs text-muted">Hit Rate: 94%</div>
        </div>
    </div>
</div>
