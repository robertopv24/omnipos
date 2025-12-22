<div class="container py-4">
    <div class="max-w-700 mx-auto">
        <div class="glass-widget p-4 text-center mb-2">
            <div class="logo-circle bg-success-10 text-success mb-1-5 text-4xl">
                <i class="fa fa-check-circle"></i>
            </div>
            <h1 class="text-3xl font-800 text-gradient mb-05">¡Pedido Recibido!</h1>
            <p class="text-dim mb-2">Tu pedido <strong>#<?= htmlspecialchars($orderId) ?></strong> ha sido registrado y está siendo procesado.</p>
            
            <div class="order-stepper mt-3 d-flex justify-between relative">
                <div class="step active d-flex flex-column align-center gap-05 z-10">
                    <div class="step-circle glass-effect d-flex align-center justify-center">1</div>
                    <span class="text-xs font-700">Recibido</span>
                </div>
                <div class="step d-flex flex-column align-center gap-05 z-10">
                    <div class="step-circle glass-effect d-flex align-center justify-center">2</div>
                    <span class="text-xs font-700 text-dim">Preparando</span>
                </div>
                <div class="step d-flex flex-column align-center gap-05 z-10">
                    <div class="step-circle glass-effect d-flex align-center justify-center">3</div>
                    <span class="text-xs font-700 text-dim">En Camino</span>
                </div>
                <div class="step d-flex flex-column align-center gap-05 z-10">
                    <div class="step-circle glass-effect d-flex align-center justify-center">4</div>
                    <span class="text-xs font-700 text-dim">Entregado</span>
                </div>
                <div class="stepper-line absolute top-1-25 left-0 w-100 h-2 bg-dim z-0"></div>
            </div>
        </div>

        <div class="glass-widget p-2">
            <h3 class="text-lg font-700 mb-1-5">¿Qué sigue?</h3>
            <ul class="d-flex flex-column gap-1 text-dim">
                <li class="d-flex gap-1">
                    <i class="fa fa-envelope text-primary"></i>
                    <span>Recibirás un correo de confirmación con los detalles de tu compra.</span>
                </li>
                <li class="d-flex gap-1">
                    <i class="fa fa-phone text-primary"></i>
                    <span>Un agente de ventas podría contactarte para validar el pago.</span>
                </li>
                <li class="d-flex gap-1">
                    <i class="fa fa-truck text-primary"></i>
                    <span>Te avisaremos cuando tu pedido esté en camino a tu dirección.</span>
                </li>
            </ul>
            
            <div class="mt-2 pt-2 border-top border-bright d-flex justify-center">
                <a href="<?= url('/shop') ?>" class="btn btn-secondary">
                    <i class="fa fa-arrow-left mr-05"></i> Volver a la Tienda
                </a>
            </div>
        </div>
    </div>
</div>

<style>
.order-stepper {
    padding: 0 1rem;
}
.step-circle {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    font-weight: 800;
    font-size: 1.25rem;
    border: 2px solid rgba(255,255,255,0.1);
}
.step.active .step-circle {
    background: var(--primary);
    border-color: var(--primary);
    color: white;
    box-shadow: 0 0 20px rgba(59, 130, 246, 0.5);
}
.step.active span {
    color: var(--primary);
}
.stepper-line {
    top: 20px;
    height: 2px;
    background: rgba(255,255,255,0.1);
}
.z-0 { z-index: 0; }
.z-10 { z-index: 10; }
</style>
