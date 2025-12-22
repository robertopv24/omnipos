<div class="content-header d-flex justify-between align-center mb-2">
    <div>
        <h1 class="text-gradient mb-05 border-none"><i class="fa fa-chart-line"></i> <?= __('welcome_super_admin') ?></h1>
        <p class="text-dim"><?= __('global_ecosystem_vision') ?></p>
    </div>
    <div class="d-flex gap-1 text-sm font-600">
        <span class="text-dim">Uptime:</span> <span class="text-success">99.9%</span>
    </div>
</div>

<!-- Métricas Globales -->
<div class="d-grid gap-2 mt-2" style="grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));">
    <div class="glass-widget p-2 d-flex align-center gap-1 border-left-primary">
        <div class="stat-icon bg-primary-soft text-primary">
            <i class="fa fa-users"></i>
        </div>
        <div class="metric-info">
            <h3 class="m-0 text-xs text-dim uppercase font-700"><?= __('total_clients') ?></h3>
            <p class="m-0 text-2xl font-800 text-bright"><?= number_format($metrics['total_clients']) ?></p>
        </div>
    </div>
    <div class="glass-widget p-2 d-flex align-center gap-1 border-left-success">
        <div class="stat-icon bg-success-soft text-success">
            <i class="fa fa-store"></i>
        </div>
        <div class="metric-info">
            <h3 class="m-0 text-xs text-dim uppercase font-700"><?= __('total_businesses') ?></h3>
            <p class="m-0 text-2xl font-800 text-bright"><?= number_format($metrics['total_businesses']) ?></p>
        </div>
    </div>
    <div class="glass-widget p-2 d-flex align-center gap-1 border-left-info">
        <div class="stat-icon bg-info-soft text-info">
            <i class="fa fa-user-friends"></i>
        </div>
        <div class="metric-info">
            <h3 class="m-0 text-xs text-dim uppercase font-700"><?= __('total_users') ?></h3>
            <p class="m-0 text-2xl font-800 text-bright"><?= number_format($metrics['total_users']) ?></p>
        </div>
    </div>
    <div class="glass-widget p-2 d-flex align-center gap-1 border-left-warning bg-gradient-warning">
        <div class="stat-icon bg-warning-soft text-warning">
            <i class="fa fa-dollar-sign"></i>
        </div>
        <div class="metric-info">
            <h3 class="m-0 text-xs text-dim uppercase font-700"><?= __('estimated_mrr') ?></h3>
            <p class="m-0 text-2xl font-800 text-bright">$<?= number_format($metrics['mrr'], 2) ?></p>
        </div>
    </div>
</div>

<!-- Lista de Clientes Recientes -->
<div class="glass-widget mt-2 p-2 relative overflow-hidden">
    <div class="d-flex justify-between align-center mb-2">
        <h3 class="m-0 text-bright font-700"><?= __('recent_clients') ?></h3>
        <a href="<?= url('/platform/accounts') ?>" class="btn btn-outline-primary btn-sm"><?= __('view_all') ?></a>
    </div>
    
    <table class="table w-full">
        <thead>
            <tr class="text-left border-bottom border-glass">
                <th class="p-1 text-dim uppercase text-xs"><?= __('main_business') ?></th>
                <th class="p-1 text-dim uppercase text-xs"><?= __('owner') ?></th>
                <th class="p-1 text-dim uppercase text-xs"><?= __('email') ?></th>
                <th class="p-1 text-dim uppercase text-xs"><?= __('registration_date') ?></th>
                <th class="p-1 text-right text-dim uppercase text-xs"><?= __('actions') ?></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($recentClients as $client): ?>
            <tr class="border-bottom border-glass hover-bright">
                <td class="p-1 font-600 text-bright"><?= htmlspecialchars($client['business_name']) ?></td>
                <td class="p-1"><?= htmlspecialchars($client['owner_name']) ?></td>
                <td class="p-1"><?= htmlspecialchars($client['email']) ?></td>
                <td class="p-1 italic text-sm"><?= date('d/m/Y', strtotime($client['created_at'])) ?></td>
                <td class="p-1 text-right">
                    <button class="btn btn-sm btn-secondary px-1"><?= __('manage') ?></button>
                </td>
            </tr>
            <?php endforeach; ?>
            <?php if (empty($recentClients)): ?>
            <tr>
                <td colspan="5" class="p-2 text-center text-dim italic">
                    <?= __('no_clients_registered_yet') ?>
                </td>
            </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<style>
.stat-icon { width: 50px; height: 50px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; }
.bg-primary-soft { background: rgba(59, 130, 246, 0.1); }
.bg-success-soft { background: rgba(16, 185, 129, 0.1); }
.bg-info-soft { background: rgba(6, 182, 212, 0.1); }
.bg-warning-soft { background: rgba(245, 158, 11, 0.1); }
.border-left-primary { border-left: 4px solid var(--primary-color); }
.border-left-success { border-left: 4px solid #10b981; }
.border-left-info { border-left: 4px solid #06b6d4; }
.border-left-warning { border-left: 4px solid #f59e0b; }
.bg-gradient-warning { background: linear-gradient(to right, rgba(245, 158, 11, 0.05), transparent); }
</style>


