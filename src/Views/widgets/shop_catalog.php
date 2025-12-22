<?php
// Widget: Shop Catalog
?>
<div class="shop-widget p-4">
    <div class="d-flex justify-between align-center mb-3">
        <h2 class="text-2xl font-800 m-0"><i class="fa fa-store"></i> Tienda Integrada</h2>
        <div class="search-box relative">
             <input type="text" class="form-control pl-3 rounded-full" placeholder="Buscar..." value="<?= htmlspecialchars($searchQuery) ?>">
        </div>
    </div>

    <div class="grid-4 gap-1">
        <?php foreach ($products as $product): ?>
            <div class="product-card glass-effect p-1-5 cursor-pointer hover-glow transition-all rounded-xl d-flex flex-column" onclick="addToCart('<?= $product['id'] ?>')">
                 <div class="aspect-square rounded-lg mb-1 bg-dim overflow-hidden">
                    <img src="<?= $product['image_url'] ? url($product['image_url']) : url('/img/no-image.png') ?>" class="w-full h-full object-cover">
                 </div>
                 <h3 class="text-md font-700 mb-05"><?= htmlspecialchars($product['name']) ?></h3>
                 <div class="price text-primary font-800 text-lg">
                    $<?= number_format($product['price_usd'], 2) ?>
                 </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>
