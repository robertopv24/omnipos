<?php
// Widget: POS Terminal
// Derived from src/Views/sales/pos.php
?>
<div class="pos-wrapper d-flex gap-1 h-100 overflow-hidden" style="height: 600px; border: 1px solid #444; border-radius: 8px;">
    <!-- Sección de Productos -->
    <div class="pos-products glass-effect d-flex flex-column" style="flex: 1; min-width: 0;">
        <div class="p-2 border-bottom border-bright bg-glass-dark sticky-top" style="z-index: 10;">
            <div class="header-tools d-flex gap-1">
                <div class="search-bar relative" style="flex: 2;">
                    <i class="fa fa-search absolute left-1 top-50 translate-y-n50 text-dim"></i>
                    <input type="text" id="product-search" class="form-control pl-3" oninput="searchProducts(this.value)"
                        placeholder="<?= __('search_product_placeholder') ?>">
                </div>
                <div class="client-bar relative" style="flex: 1;">
                    <i class="fa fa-user absolute left-1 top-50 translate-y-n50 text-dim"></i>
                    <input type="text" id="client-search" class="form-control pl-3" oninput="searchClients(this.value)"
                        placeholder="<?= __('select_client_optional') ?>">
                    <div id="client-results" class="modal-content absolute z-50 w-full mt-1 max-h-250 overflow-auto bg-dark border-glass p-0 shadow-2xl"
                        style="display:none; left:0; right:0;">
                    </div>
                </div>
                <input type="hidden" id="selected-client-id" value="">
            </div>
        </div>

        <div id="products-container" class="products-grid p-2 d-grid gap-1 grid-cols-auto-fill-150 overflow-y-auto custom-scrollbar flex-grow">
            <?php foreach ($products as $p): ?>
                <div class="product-card glass-effect p-1-5 cursor-pointer hover-glow transition-all rounded-lg d-flex flex-column" 
                     onclick="addToCart(<?= htmlspecialchars(json_encode($p)) ?>)">
                    <div class="aspect-square rounded-md mb-1-5 overflow-hidden bg-dim">
                        <img src="<?= $p['image_url'] ? url($p['image_url']) : url('/img/no-image.png') ?>"
                            onerror="this.onerror=null; this.src='<?= url('/img/no-image.png') ?>'" class="w-full h-full object-cover">
                    </div>
                    <div class="name font-700 leading-tight text-sm line-clamp-2 mb-1"><?= $p['name'] ?></div>
                    <div class="price text-primary font-800 mt-auto">$<?= number_format($p['price_usd'], 2) ?></div>
                </div>
            <?php endforeach; ?>
        </div>
        
        <!-- Paginación POS -->
        <div id="pos-pagination" class="p-1 border-top border-bright bg-glass-dark d-flex justify-center">
        </div>
    </div>

    <!-- Sección de Carrito -->
    <div class="pos-cart glass-effect d-flex flex-column border-left border-bright shadow-xl" style="width: 350px; flex-shrink: 0;">
        <div class="p-2 border-bottom border-bright bg-glass-dark">
            <h2 class="d-flex justify-between align-center m-0 text-xl font-800">
                <?= __('cart') ?> 
                <span id="cart-count" class="badge bg-primary rounded-full text-xs px-2 py-05">0</span>
            </h2>
        </div>

        <div id="cart-items" class="cart-items-container flex-grow overflow-y-auto custom-scrollbar p-2">
            <div class="empty-cart-msg text-center py-4 text-dim">
                <i class="fa fa-shopping-basket text-4xl mb-1 opacity-20"></i>
                <p><?= __('cart_is_empty') ?></p>
            </div>
        </div>

        <div class="cart-summary p-2 border-top border-bright bg-glass-dark">
            <div class="summary-details bg-dim rounded-lg p-1-5 mb-2">
                <div class="summary-row d-flex justify-between font-800 text-xl text-pure border-top border-bright pt-1 mt-1">
                    <span>Total USD</span>
                    <span id="total-usd">$0.00</span>
                </div>
            </div>
            
            <button onclick="openPaymentModal()" class="btn btn-primary w-full py-1-5 text-lg font-800 shadow-glow rounded-lg">
                <i class="fa fa-credit-card mr-05"></i> <?= __('finalize_sale') ?>
            </button>
        </div>
    </div>
</div>

<script>
    const EXCHANGE_RATE = <?= $exchangeRate ?>;
</script>
<script src="<?= url('/js/pos.js') ?>"></script>
