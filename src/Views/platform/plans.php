<div class="content-header d-flex justify-between align-center mb-2">
    <div>
        <h1 class="text-gradient mb-05 border-none"><i class="fa fa-gem"></i> <?= __('Planes de Suscripción') ?></h1>
        <p class="text-dim"><?= __('Configura los niveles de servicio y límites globales del sistema.') ?></p>
    </div>
    <button class="btn btn-primary btn-sm"><i class="fa fa-plus"></i> <?= __('Crear Nuevo Plan') ?></button>
</div>

<div class="d-grid gap-2" style="grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));">
    <?php foreach ($plans as $plan): ?>
    <div class="glass-widget p-2 relative hover-up transition-300">
        <div class="d-flex justify-between align-center mb-1">
            <span class="badge badge-primary text-xs uppercase px-1"><?= $plan['name'] == 'Pro' ? 'Recomendado' : 'Plan' ?></span>
            <div class="text-2xl font-800 text-bright">
                $<?= number_format($plan['price'], 2) ?>
                <span class="text-xs text-dim font-400">/mes</span>
            </div>
        </div>
        
        <h3 class="text-xl font-700 mb-1 text-pure"><?= htmlspecialchars($plan['name']) ?></h3>
        <p class="text-dim text-sm mb-2" style="min-height: 3rem;"><?= htmlspecialchars($plan['features']) ?></p>
        
        <div class="border-top border-glass pt-1 mt-1 d-flex gap-1">
            <button class="btn btn-secondary btn-sm flex-1"><?= __('Configurar') ?></button>
            <button class="btn btn-outline-danger btn-sm px-1"><i class="fa fa-trash"></i></button>
        </div>
        
        <?php if($plan['name'] == 'Pro'): ?>
        <div class="absolute top-0 right-0 p-1">
            <i class="fa fa-star text-warning animate-pulse"></i>
        </div>
        <?php endif; ?>
    </div>
    <?php endforeach; ?>
</div>

<div class="mt-4 glass-widget p-2">
    <div class="d-flex align-center gap-1 mb-2">
        <i class="fa fa-key text-warning fs-4"></i>
        <h3 class="m-0"><?= __('Configuración de Facturación') ?></h3>
    </div>
    
    <div class="d-grid gap-2" style="grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));">
        <div class="form-group">
            <label class="text-xs text-dim uppercase font-700 mb-05 d-block">Stripe API Key</label>
            <div class="input-group">
                <input type="password" class="form-control" value="sk_test_51Mz..." readonly>
                <button class="btn btn-secondary px-1"><i class="fa fa-eye"></i></button>
            </div>
        </div>
        <div class="form-group">
            <label class="text-xs text-dim uppercase font-700 mb-05 d-block">PayPal Business Email</label>
            <input type="email" class="form-control" value="billing@omnipos-saas.com" readonly>
        </div>
    </div>
</div>

<style>
.hover-up:hover { transform: translateY(-5px); box-shadow: 0 10px 30px -10px rgba(59, 130, 246, 0.5); }
.transition-300 { transition: all 0.3s ease; }
</style>
