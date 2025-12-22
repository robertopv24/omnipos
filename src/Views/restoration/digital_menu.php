<div class="vh-100 d-flex flex-column">
    <div class="p-2 d-flex justify-between align-center bg-glass border-bottom-primary">
        <div>
            <h1 class="font-800 m-0 tracking-tight" style="font-size: 3.5rem;">MENU <span class="text-primary"><?= __('menu_of_the_day') ?></span></h1>
        </div>
        <div id="clock" class="font-600 tabular-nums" style="font-size: 2.5rem;"></div>
    </div>

    <div class="flex-1 relative">
        <?php if (empty($products)): ?>
            <div class="d-flex align-center justify-center h-full">
                <h2 class="text-slate-500" style="font-size: 2rem;"><?= __('no_featured_products') ?></h2>
            </div>
        <?php else: ?>
            <?php foreach ($products as $index => $product): ?>
                <div class="product-slide w-full h-full d-flex align-center px-5 gap-5 absolute transition-opacity" id="slide-<?= $index ?>"
                    style="opacity: <?= $index === 0 ? '1' : '0' ?>; padding: 0 8rem; gap: 6rem;">

                    <div class="flex-1 text-center">
                        <img src="<?= $product['image_url'] ? url($product['image_url']) : url('/img/no-image.png') ?>"
                            style="max-width: 100%; max-height: 65vh; border-radius: 3rem; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.7); border: 8px solid rgba(255,255,255,0.05);">
                    </div>

                    <div class="flex-1">
                        <div class="d-inline-block px-2 py-1 bg-primary rounded-pill font-700 text-xl mb-2 uppercase">
                            <?= __('promotion') ?>
                        </div>
                        <h2 class="font-800 leading-none m-0 mb-2" style="font-size: 6rem;">
                            <?= htmlspecialchars($product['name']) ?>
                        </h2>
                        <p class="text-slate-400 leading-normal mb-3" style="font-size: 2.2rem;">
                            <?= htmlspecialchars($product['description'] ?? __('delicious_prepared_fresh')) ?>
                        </p>

                        <div class="d-flex align-baseline gap-1">
                            <span class="text-slate-500 line-through" style="font-size: 2.5rem;">$<?= number_format($product['price_usd'] * 1.2, 2) ?></span>
                            <span class="font-800 text-primary" style="font-size: 7rem;"><?= \OmniPOS\Services\LocalizationService::formatCurrency($product['price_usd'] ?? 0.00) ?></span>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <div class="p-1 bg-primary text-white text-center font-700 tracking-wide" style="font-size: 1.8rem;">
        <?= __('marketing_footer') ?>
    </div>
</div>

<script>
    function updateClock() {
        const now = new Date();
        document.getElementById('clock').innerText = now.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
    }
    setInterval(updateClock, 1000);
    updateClock();

    let currentSlide = 0;
    const slides = document.querySelectorAll('.product-slide');

    if (slides.length > 1) {
        setInterval(() => {
            slides[currentSlide].style.opacity = '0';
            currentSlide = (currentSlide + 1) % slides.length;
            setTimeout(() => {
                slides[currentSlide].style.opacity = '1';
            }, 500);
        }, 8000);
    }
</script>