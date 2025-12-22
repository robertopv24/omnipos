<div class="shop-header relative py-6 overflow-hidden">
    <div class="bg-primary absolute inset-0 opacity-05 blur-3xl"></div>
    <div class="container relative z-10 text-center">
        <h1 class="text-5xl font-900 text-gradient mb-1-5 tracking-tighter"><?= __('our_store') ?></h1>
        <p class="text-lg text-dim max-w-600 mx-auto leading-relaxed">
            Descubre nuestra selección exclusiva de productos de alta calidad, seleccionados especialmente para ti.
        </p>
    </div>
</div>

<div class="container pb-6">
    <div class="d-flex justify-between align-center mb-3">
        <h2 class="text-2xl font-800 m-0 d-flex align-center gap-075">
            <span class="bg-primary text-white p-05 rounded-lg d-flex align-center justify-center" style="width:32px; height:32px;">
                <i class="fa fa-th-large text-sm"></i>
            </span>
            <?= __('featured_products') ?>
        </h2>
        
        <div class="d-flex gap-1">
            <div class="search-box relative">
                <i class="fa fa-search absolute left-1 top-50 translate-y-n50 text-dim"></i>
                <input type="text" id="shop-search" class="form-control pl-3 py-075 bg-glass border-bright rounded-full text-sm" 
                    placeholder="Buscar productos..." style="min-width: 250px;" 
                    value="<?= htmlspecialchars($searchQuery ?? '') ?>"
                    onkeypress="if(event.key === 'Enter') window.location.href = '?q=' + this.value">
            </div>
        </div>
    </div>

    <div class="grid-6 gap-1">
        <?php foreach ($products as $product): ?>
            <div class="product-card glass-effect p-1-5 cursor-pointer hover-glow transition-all rounded-xl d-flex flex-column h-100" 
                 onclick="addToCart('<?= $product['id'] ?>')">
                <div class="aspect-square rounded-lg mb-1-5 overflow-hidden bg-dim relative">
                    <?php if (isset($product['image_url']) && $product['image_url']): ?>
                        <img src="<?= url($product['image_url']) ?>" alt="<?= htmlspecialchars($product['name']) ?>" 
                             onerror="this.onerror=null; this.src='<?= url('/img/no-image.png') ?>'"
                             class="w-full h-full object-cover product-img-zoom transition-all duration-500">
                    <?php else: ?>
                        <div class="w-full h-full d-flex align-center justify-center">
                            <i class="fa fa-image text-4xl opacity-10"></i>
                        </div>
                    <?php endif; ?>
                </div>
                
                <div class="d-flex flex-column flex-grow">
                    <h3 class="text-md font-700 mb-05 text-pure leading-tight line-clamp-2"><?= htmlspecialchars($product['name']) ?></h3>
                    <?php if ($product['sku']): ?>
                        <span class="text-xs text-dim mb-1 font-600 uppercase tracking-wider">SKU: <?= htmlspecialchars($product['sku']) ?></span>
                    <?php endif; ?>
                    <p class="text-sm text-dim mb-1-5 flex-grow line-clamp-2 leading-relaxed">
                        <?= htmlspecialchars($product['description'] ?? '') ?>
                    </p>
                    
                    <div class="d-flex align-center justify-between mt-auto">
                        <div class="price text-primary font-800 text-lg">
                            <?= \OmniPOS\Services\LocalizationService::formatCurrency($product['price_usd']) ?>
                        </div>
                        <button onclick="event.stopPropagation(); addToCart('<?= $product['id'] ?>')" 
                                class="btn-add-mini bg-primary text-white rounded-lg shadow-glow transition-all active-scale-95">
                            <i class="fa fa-plus"></i>
                        </button>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <!-- Paginación Premium -->
    <?php if ($totalPages > 1): ?>
        <div class="d-flex justify-center mt-4">
            <nav class="pagination-premium d-flex align-center gap-05 bg-glass-dark p-05 rounded-full border-bright">
                <?php if ($currentPage > 1): ?>
                    <a href="?page=<?= $currentPage - 1 ?><?= $searchQuery ? '&q='.urlencode($searchQuery) : '' ?>" class="btn-pagination" title="Anterior">
                        <i class="fa fa-chevron-left text-xs"></i>
                    </a>
                <?php endif; ?>

                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <a href="?page=<?= $i ?><?= $searchQuery ? '&q='.urlencode($searchQuery) : '' ?>" class="btn-pagination <?= $i === $currentPage ? 'active' : '' ?>">
                        <?= $i ?>
                    </a>
                <?php endfor; ?>

                <?php if ($currentPage < $totalPages): ?>
                    <a href="?page=<?= $currentPage + 1 ?><?= $searchQuery ? '&q='.urlencode($searchQuery) : '' ?>" class="btn-pagination" title="Siguiente">
                        <i class="fa fa-chevron-right text-xs"></i>
                    </a>
                <?php endif; ?>
            </nav>
        </div>
    <?php endif; ?>
</div>

<script>
async function addToCart(productId) {
    // Animación de feedback
    const btn = event.currentTarget.tagName === 'BUTTON' ? event.currentTarget : event.target.closest('button');
    if (btn) {
        const originalHtml = btn.innerHTML;
        btn.innerHTML = '<i class="fa fa-spinner fa-spin"></i>';
        btn.disabled = true;
    }

    try {
        const response = await fetch('<?= url('/shop/cart/add') ?>', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `product_id=${productId}&quantity=1`
        });
        const result = await response.json();
        if (result.success) {
            showToast('¡Producto añadido al carrito!', 'check-circle');
            
            // Actualizar contador global si existe
            const cartCountElement = document.getElementById('cart-global-count');
            if (cartCountElement) {
                cartCountElement.innerText = (result.cart_count || (parseInt(cartCountElement.innerText) + 1));
                cartCountElement.classList.add('pulse-animation');
                setTimeout(() => cartCountElement.classList.remove('pulse-animation'), 500);
            }
        }
    } catch (error) {
        console.error('Error:', error);
    } finally {
        if (btn) {
            btn.innerHTML = originalHtml;
            btn.disabled = false;
        }
    }
}

function showToast(message, icon) {
    const container = document.getElementById('toast-container');
    const toast = document.createElement('div');
    toast.className = 'toast-msg';
    toast.innerHTML = `<i class="fa fa-${icon} text-primary"></i> <span>${message}</span>`;
    container.appendChild(toast);
    setTimeout(() => {
        toast.style.opacity = '0';
        toast.style.transform = 'translateY(10px)';
        toast.style.transition = 'all 0.4s ease-in';
        setTimeout(() => toast.remove(), 400);
    }, 3000);
}
</script>

<style>
.pagination-premium {
    box-shadow: 0 10px 30px rgba(0,0,0,0.3);
}
.btn-pagination {
    width: 36px;
    height: 36px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
    color: var(--text-dim);
    font-weight: 800;
    transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    text-decoration: none;
    font-size: 0.85rem;
}
.btn-pagination:hover {
    background: rgba(255, 255, 255, 0.1);
    color: var(--text-pure);
    transform: scale(1.1);
}
.btn-pagination.active {
    background: var(--primary);
    color: white;
    box-shadow: 0 0 15px var(--primary-glow);
}

.product-card {
    transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    border: 1px solid rgba(255, 255, 255, 0.08);
}
.product-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 20px 40px rgba(0,0,0,0.5);
    border-color: var(--primary);
    background: rgba(255, 255, 255, 0.05);
}
.hover-glow:hover {
    box-shadow: 0 0 20px var(--primary-glow);
}
.aspect-square {
    aspect-ratio: 1 / 1;
}
.product-img-zoom {
    object-fit: cover;
}
.product-card:hover .product-img-zoom {
    transform: scale(1.1);
}
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
.btn-add-mini {
    width: 32px;
    height: 32px;
    display: flex;
    align-items: center;
    justify-content: center;
    border: none;
    cursor: pointer;
}

@keyframes pulse {
    0% { transform: scale(1); }
    50% { transform: scale(1.3); color: var(--primary); }
    100% { transform: scale(1); }
}
.pulse-animation {
    animation: pulse 0.5s ease-in-out;
}

/* Toast Notification Styles */
#toast-container {
    position: fixed;
    bottom: 2rem;
    right: 2rem;
    z-index: 1000;
}
.toast-msg {
    background: var(--bg-glass-dark);
    backdrop-filter: blur(10px);
    border: 1px solid var(--primary);
    color: white;
    padding: 1rem 1.5rem;
    border-radius: 12px;
    margin-top: 0.5rem;
    display: flex;
    align-items: center;
    gap: 0.75rem;
    box-shadow: 0 10px 30px rgba(0,0,0,0.5);
    animation: slideIn 0.3s ease-out forwards;
}
@keyframes slideIn {
    from { transform: translateX(100%); opacity: 0; }
    to { transform: translateX(0); opacity: 1; }
}
</style>

<div id="toast-container"></div>
