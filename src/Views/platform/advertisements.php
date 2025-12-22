<div class="content-header d-flex justify-between align-center mb-2">
    <div>
        <h1 class="text-gradient mb-05 border-none"><i class="fa fa-bullhorn"></i> <?= __('Publicidad y Avisos') ?></h1>
        <p class="text-dim"><?= __('Gestiona los anuncios y notificaciones globales que ven los dueños de negocios en su panel.') ?></p>
    </div>
    <button class="btn btn-primary btn-sm"><i class="fa fa-plus"></i> <?= __('Programar Nuevo Aviso') ?></button>
</div>

<div class="glass-widget p-2">
    <h3 class="mb-1 text-pure font-700"><?= __('Campañas Activas') ?></h3>
    <table class="table w-full">
        <thead>
            <tr class="text-left border-bottom border-glass">
                <th class="p-1 text-dim uppercase text-xs">Aviso / Título</th>
                <th class="p-1 text-dim uppercase text-xs">Alcance</th>
                <th class="p-1 text-dim uppercase text-xs">Estado</th>
                <th class="p-1 text-right text-dim uppercase text-xs">Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($ads as $ad): ?>
            <tr class="border-bottom border-glass hover-bright">
                <td class="p-1">
                    <div class="font-600 text-bright"><?= htmlspecialchars($ad['title']) ?></div>
                    <div class="text-xs text-dim">Visible para: Todos los planes</div>
                </td>
                <td class="p-1">
                    <span class="badge badge-<?= $ad['type'] == 'warning' ? 'warning' : 'info' ?> text-xs uppercase">
                        <?= $ad['type'] == 'warning' ? 'Advertencia' : 'Informativo' ?>
                    </span>
                </td>
                <td class="p-1">
                    <div class="d-flex align-center gap-05">
                        <div class="status-dot bg-success" style="width: 8px; height: 8px; border-radius: 50%;"></div>
                        <span class="text-sm font-500"><?= strtoupper($ad['status']) ?></span>
                    </div>
                </td>
                <td class="p-1 text-right">
                    <div class="d-flex gap-05 justify-end">
                        <button class="btn btn-sm btn-secondary px-05" title="Ver"><i class="fa fa-eye"></i></button>
                        <button class="btn btn-sm btn-secondary px-05" title="Editar"><i class="fa fa-edit"></i></button>
                    </div>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<div class="mt-4 glass-widget p-2 border border-warning" style="background: linear-gradient(145deg, rgba(245, 158, 11, 0.1) 0%, rgba(15, 27, 45, 1) 100%);">
    <div class="d-flex align-center gap-1 mb-1">
        <i class="fa fa-desktop text-warning fs-3"></i>
        <h3 class="m-0 text-bright"><?= __('Vista Previa en el Dashboard del Cliente') ?></h3>
    </div>
    
    <div class="p-2 rounded glass-effect border border-warning d-flex align-start gap-1 relative overflow-hidden">
        <div class="absolute top-0 left-0 w-full h-full bg-warning opacity-05"></div>
        <i class="fa fa-triangle-exclamation text-warning text-2xl mt-05"></i>
        <div class="relative">
            <div class="font-800 text-bright text-lg">Mantenimiento programado 24/12</div>
            <div class="text-dim text-md">La plataforma estará fuera de servicio por 2 horas para actualizaciones críticas de seguridad. Recomendamos cerrar sesiones antes de las 02:00 AM UTC.</div>
        </div>
    </div>
</div>
