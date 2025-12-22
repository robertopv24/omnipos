<div class="container py-6">
    <div class="d-flex align-center gap-1 mb-3">
        <a href="<?= url('/shop') ?>" class="btn-icon bg-bright hover-primary transition-all">
            <i class="fa fa-arrow-left text-sm"></i>
        </a>
        <h1 class="text-4xl font-900 text-gradient m-0 tracking-tighter">Mi Carrito</h1>
    </div>

    <?php if (empty($cart)): ?>
        <div class="glass-widget p-4 text-center">
            <i class="fa fa-shopping-basket text-6xl text-dim mb-1-5"></i>
            <h2 class="text-xl font-600 mb-1">Tu carrito está vacío</h2>
            <p class="text-dim mb-2">Parece que aún no has añadido nada. ¡Explora nuestro menú!</p>
            <a href="<?= url('/shop') ?>" class="btn btn-primary">
                Ir a la Tienda <i class="fa fa-arrow-right ml-05"></i>
            </a>
        </div>
    <?php else: ?>
        <div class="grid-3 gap-2">
            <div class="col-span-3 md:col-span-2">
                <div class="glass-effect overflow-hidden rounded-2xl border-bright shadow-xl">
                    <div class="table-responsive">
                        <table class="table w-100">
                            <thead>
                                <tr class="bg-glass-dark border-bottom border-bright">
                                    <th class="py-1-5 px-2 text-xs font-800 text-dim uppercase tracking-wider">Producto</th>
                                    <th class="py-1-5 px-2 text-xs font-800 text-dim uppercase tracking-wider text-right">Precio</th>
                                    <th class="py-1-5 px-2 text-xs font-800 text-dim uppercase tracking-wider text-center">Cantidad</th>
                                    <th class="py-1-5 px-2 text-xs font-800 text-dim uppercase tracking-wider text-right">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-bright">
                                <?php $total = 0; ?>
                                <?php foreach ($cart as $id => $item): ?>
                                    <?php $itemSubtotal = $item['price'] * $item['quantity']; ?>
                                    <?php $total += $itemSubtotal; ?>
                                    <tr class="hover-bg-bright transition-all">
                                        <td class="py-2 px-2">
                                            <div class="d-flex align-center gap-1-5">
                                                <div class="avatar-rect bg-bright rounded-lg d-flex align-center justify-center p-05 border border-bright" style="width:50px; height:50px;">
                                                    <i class="fa fa-box text-dim"></i>
                                                </div>
                                                <div>
                                                    <div class="font-800 text-pure text-lg leading-tight mb-025"><?= htmlspecialchars($item['name']) ?></div>
                                                    <div class="text-xs text-dim">SKU: PROD-<?= substr($id, 0, 6) ?></div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="py-2 px-2 text-right font-600 text-dim">
                                            <?= \OmniPOS\Services\LocalizationService::formatCurrency($item['price']) ?>
                                        </td>
                                        <td class="py-2 px-2 text-center">
                                            <div class="d-inline-flex align-center gap-1 bg-bright rounded-full px-1-5 py-05 font-800 border border-bright">
                                                <span class="text-pure w-20 text-center"><?= $item['quantity'] ?></span>
                                            </div>
                                        </td>
                                        <td class="py-2 px-2 text-right font-900 text-pure text-lg">
                                            <?= \OmniPOS\Services\LocalizationService::formatCurrency($itemSubtotal) ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="col-span-3 md:col-span-1">
                <div class="glass-effect p-2-5 rounded-2xl border-bright bg-glass-dark shadow-2xl sticky-top" style="top: 2rem;">
                    <h3 class="m-0 mb-2 text-xl font-900 border-bottom border-bright pb-1-5">Resumen de Compra</h3>
                    
                    <div class="d-flex flex-column gap-1-5 mb-2-5">
                        <div class="d-flex justify-between text-sm">
                            <span class="text-dim font-600 tracking-wide uppercase text-xs">Subtotal</span>
                            <span class="font-800"><?= \OmniPOS\Services\LocalizationService::formatCurrency($total) ?></span>
                        </div>
                        <div class="d-flex justify-between text-sm">
                            <span class="text-dim font-600 tracking-wide uppercase text-xs">Gastos de Envío</span>
                            <span class="text-emerald font-800 uppercase text-xs tracking-widest bg-emerald-10 px-1 py-025 rounded-full">Gratis</span>
                        </div>
                    </div>

                    <div class="d-flex justify-between mt-3 pt-2 border-top border-bright">
                        <span class="text-lg font-900 uppercase tracking-tighter">Total</span>
                        <div class="text-right">
                            <div class="text-3xl font-900 text-gradient leading-none mb-05">
                                <?= \OmniPOS\Services\LocalizationService::formatCurrency($total) ?>
                            </div>
                        </div>
                    </div>
                    
                    <a href="<?= url('/shop/checkout') ?>" class="btn btn-primary w-100 mt-4 py-1-5 text-xl font-900 rounded-xl shadow-glow d-flex align-center justify-center gap-1">
                        Pedir Ahora <i class="fa fa-arrow-right text-sm"></i>
                    </a>
                    
                    <div class="mt-2 text-center">
                        <a href="<?= url('/shop') ?>" class="text-sm font-700 text-dim hover-pure transition-all">
                            Seguir Comprando
                        </a>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<style>
.hover-bg-bright:hover { background-color: rgba(255, 255, 255, 0.05); }
.divide-bright > * + * { border-top: 1px solid var(--border-bright); }
.bg-emerald-10 { background: rgba(16, 185, 129, 0.1); }
.text-emerald { color: #10b981; }
</style>
