<div class="hero-section text-center py-5">
    <h1 class="text-gradient display-1 mb-2">OmniPOS <span class="text-bright">SaaS</span></h1>
    <p class="text-xl text-dim mb-4">La solución definitiva de punto de venta y gestión empresarial multi-negocio.</p>

    <?php if (\OmniPOS\Core\Session::has('user_id')): ?>
        <div class="glass-effect p-3 d-inline-block border border-bright mt-3">
            <h3 class="mb-0">Bienvenido de nuevo, <?= htmlspecialchars(\OmniPOS\Core\Session::get('user_name')) ?></h3>
        </div>
    <?php endif; ?>

    <div class="features-grid mt-5">
        <div class="feature-card glass-effect">
            <i class="fas fa-chart-pie text-primary mb-2"></i>
            <h4>Análisis de Rentabilidad</h4>
            <p class="text-sm">Reportes detallados de ingresos, COGS y márgenes netos por sucursal.</p>
        </div>
        <div class="feature-card glass-effect">
            <i class="fas fa-microchip text-warning mb-2"></i>
            <h4>Gestión de Manufactura</h4>
            <p class="text-sm">Control total de recetas, producción y trazabilidad de insumos.</p>
        </div>
        <div class="feature-card glass-effect">
            <i class="fas fa-globe text-info mb-2"></i>
            <h4>Multi-Tenant Cloud</h4>
            <p class="text-sm">Administra múltiples empresas y locales desde una sola cuenta.</p>
        </div>
    </div>
</div>

<style>
.display-1 { font-size: 3.5rem; font-weight: 900; }
.text-huge { font-size: 4rem; }
.mr-1 { margin-right: 0.5rem; }
.features-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 1.5rem;
    max-width: 900px;
    margin: 0 auto;
}
.feature-card {
    padding: 1.5rem;
    border-radius: 1rem;
    transition: all 0.3s ease;
}
.feature-card i {
    font-size: 2rem;
}
.feature-card h4 {
    font-size: 1.1rem;
    margin-bottom: 0.5rem;
}
.feature-card p {
    font-size: 0.9rem;
    color: var(--text-dim);
    line-height: 1.4;
}
</style>