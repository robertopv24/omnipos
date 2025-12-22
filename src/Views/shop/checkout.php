<div class="container py-6">
    <div class="d-flex align-center justify-center gap-1 mb-4 text-center">
        <div class="d-flex flex-column align-center">
            <h1 class="text-4xl font-900 text-gradient m-0 tracking-tighter">Finalizar Pedido</h1>
            <p class="text-dim mt-05">Completa tus datos para procesar tu orden de inmediato.</p>
        </div>
    </div>

    <div class="row gap-2">
        <!-- Columna de Datos de Envío y Pago -->
        <div class="col-7">
            <div class="glass-effect p-3 rounded-2xl border-bright shadow-2xl bg-glass-dark mb-2">
                <h3 class="m-0 mb-2 text-xl font-900 d-flex align-center gap-075 border-bottom border-bright pb-1-5 uppercase tracking-widest text-xs opacity-70">
                    <span class="bg-primary text-white p-05 rounded-lg text-sm d-flex align-center justify-center" style="width:24px; height:24px;">1</span>
                    Información de Entrega
                </h3>
                
                <form id="checkoutForm">
                    <div class="form-grid d-grid grid-cols-2 gap-2">
                        <div class="form-group">
                            <label for="name" class="form-label font-700 text-xs uppercase tracking-wider text-dim mb-075 d-block">Nombre Completo</label>
                            <input type="text" id="name" class="form-control pl-1 py-1 font-600 rounded-xl bg-bright border-bright w-100" required placeholder="Ej: Roberto Pérez">
                        </div>
                        <div class="form-group">
                            <label for="phone" class="form-label font-700 text-xs uppercase tracking-wider text-dim mb-075 d-block">Teléfono</label>
                            <input type="tel" id="phone" class="form-control pl-1 py-1 font-600 rounded-xl bg-bright border-bright w-100" required placeholder="04XX-XXXXXXX">
                        </div>
                        <div class="form-group col-span-2">
                            <label for="address" class="form-label font-700 text-xs uppercase tracking-wider text-dim mb-075 d-block">Dirección Detallada</label>
                            <textarea id="address" class="form-control pl-1 py-1 font-600 rounded-xl bg-bright border-bright w-100" rows="3" required placeholder="Calle, Av, Apto, Punto de referencia..."></textarea>
                        </div>
                    </div>
                </form>
            </div>

            <div class="glass-effect p-3 rounded-2xl border-bright shadow-2xl">
                <h3 class="m-0 mb-2 text-xl font-900 d-flex align-center gap-075 border-bottom border-bright pb-1-5 uppercase tracking-widest text-xs opacity-70">
                    <span class="bg-primary text-white p-05 rounded-lg text-sm d-flex align-center justify-center" style="width:24px; height:24px;">2</span>
                    Método de Pago
                </h3>
                <div class="grid-2 gap-1-5">
                    <div class="payment-option glass-effect p-2 d-flex flex-column align-center gap-1 cursor-pointer active rounded-2xl border-bright">
                        <i class="fa fa-money-bill-wave text-3xl text-emerald"></i>
                        <span class="font-800 text-pure">Efectivo</span>
                    </div>
                    <div class="payment-option glass-effect p-2 d-flex flex-column align-center gap-1 cursor-pointer rounded-2xl border-bright opacity-40">
                        <i class="fa fa-mobile-alt text-3xl text-sky"></i>
                        <span class="font-800 text-pure">Pago Móvil</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-5">
            <div class="glass-effect p-3 rounded-2xl border-bright bg-glass-dark shadow-2xl sticky-top" style="top: 2rem;">
                <h3 class="m-0 mb-2 text-xl font-900 border-bottom border-bright pb-1-5">Resumen de Orden</h3>
                <div class="d-flex flex-column gap-1 mb-2-5">
                    <?php $total = 0; ?>
                    <?php foreach ($cart as $item): ?>
                        <?php $total += $item['price'] * $item['quantity']; ?>
                        <div class="d-flex justify-between align-center p-075 bg-bright rounded-xl border border-bright">
                            <span class="text-dim text-sm"><?= $item['quantity'] ?>x <?= htmlspecialchars($item['name']) ?></span>
                            <span class="font-800"><?= \OmniPOS\Services\LocalizationService::formatCurrency($item['price'] * $item['quantity']) ?></span>
                        </div>
                    <?php endforeach; ?>
                </div>

                <div class="d-flex justify-between mt-2 pt-2 border-top border-bright">
                    <span class="text-lg font-900 uppercase">Total a Pagar</span>
                    <span class="text-3xl font-900 text-gradient">
                        <?= \OmniPOS\Services\LocalizationService::formatCurrency($total) ?>
                    </span>
                </div>

                <button onclick="confirmOrder()" class="btn btn-primary w-100 mt-4 py-1-5 text-xl font-900 rounded-xl shadow-glow">
                    Confirmar Pedido <i class="fa fa-check-circle ml-05"></i>
                </button>
            </div>
        </div>
    </div>
</div>

<script>
function confirmOrder() {
    alert('¡Pedido realizado con éxito!');
    window.location.href = '<?= url('/shop/order-status?id=TEMP' . time()) ?>';
}
</script>

<style>
.row { display: flex; flex-wrap: wrap; margin-left: -1rem; margin-right: -1rem; }
.col-7 { flex: 0 0 58.333333%; max-width: 58.333333%; padding: 1rem; }
.col-5 { flex: 0 0 41.666667%; max-width: 41.666667%; padding: 1rem; }
@media (max-width: 992px) { .col-7, .col-5 { flex: 0 0 100%; max-width: 100%; } }
.payment-option.active { border-color: var(--primary); background: rgba(59, 130, 246, 0.05); opacity: 1; }
.text-emerald { color: #10b981; }
.text-sky { color: #0ea5e9; }
</style>
