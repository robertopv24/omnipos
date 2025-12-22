<div class="product-edit-container" style="max-width: 800px;">
    <h1><?= __('edit_product') ?></h1>

    <div class="glass-effect p-2 mt-1 rounded">
        <form action="<?= url('/products/update?id=' . $product['id']) ?>" method="POST" enctype="multipart/form-data">
            <div class="form-grid">
                <div class="form-group">
                    <label for="name"><?= __('product_name') ?></label>
                    <input type="text" name="name" id="name" value="<?= htmlspecialchars($product['name']) ?>" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="category_type"><?= __('item_type') ?></label>
                    <select name="category_type" id="category_type" class="form-control">
                        <option value="resale" <?= $product['category_type'] == 'resale' ? 'selected' : '' ?>><?= __('resale_product') ?></option>
                        <option value="operational_supply" <?= $product['category_type'] == 'operational_supply' ? 'selected' : '' ?>><?= __('operational_supply') ?></option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="sku"><?= __('sku') ?></label>
                    <input type="text" name="sku" id="sku" value="<?= htmlspecialchars($product['sku'] ?? '') ?>" class="form-control">
                </div>

                <div class="form-group">
                    <label for="category"><?= __('category') ?></label>
                    <input type="text" name="category" id="category" value="<?= htmlspecialchars($product['category'] ?? '') ?>" class="form-control">
                </div>

                <div class="form-group">
                    <label for="price_usd"><?= __('price_usd') ?></label>
                    <input type="number" step="0.01" name="price_usd" id="price_usd" value="<?= $product['price_usd'] ?>" class="form-control" required>
                </div>

                <div class="form-group">
                    <label for="price_ves"><?= __('price_ves') ?></label>
                    <input type="number" step="0.01" name="price_ves" id="price_ves" value="<?= $product['price_ves'] ?>" class="form-control" required>
                </div>

                <div class="form-group">
                    <label for="stock"><?= __('current_stock') ?></label>
                    <input type="number" name="stock" id="stock" value="<?= $product['stock'] ?>" class="form-control">
                </div>

                <div class="form-group">
                    <label for="min_stock"><?= __('min_stock') ?></label>
                    <input type="number" name="min_stock" id="min_stock" value="<?= $product['min_stock'] ?>" class="form-control">
                </div>

                <div class="form-group">
                    <label for="linked_manufactured_id"><?= __('link_manufacture') ?></label>
                    <select name="linked_manufactured_id" id="linked_manufactured_id" class="form-control">
                        <option value=""><?= __('simple_product') ?></option>
                        <?php foreach ($manufacturedProducts as $mp): ?>
                            <option value="<?= $mp['id'] ?>" <?= $product['linked_manufactured_id'] == $mp['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($mp['name']) ?> (<?= htmlspecialchars($mp['unit']) ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label><?= __('kitchen_station') ?></label>
                    <select name="kitchen_station" class="form-control">
                        <option value="kitchen" <?= $product['kitchen_station'] == 'kitchen' ? 'selected' : '' ?>><?= __('general_kitchen') ?></option>
                        <option value="pizza" <?= $product['kitchen_station'] == 'pizza' ? 'selected' : '' ?>><?= __('pizzeria_oven') ?></option>
                        <option value="bar" <?= $product['kitchen_station'] == 'bar' ? 'selected' : '' ?>><?= __('bar_drinks') ?></option>
                    </select>
                </div>

                <div class="form-group">
                    <label><?= __('packaging_cost') ?></label>
                    <input type="number" step="0.1" name="packaging_cost" value="<?= $product['packaging_cost'] ?? '0.00' ?>" class="form-control">
                </div>

                <div class="form-group d-flex align-center gap-05" style="grid-column: span 2;">
                    <input type="checkbox" name="is_featured_menu" value="1" id="is_featured_menu" <?= $product['is_featured_menu'] ? 'checked' : '' ?>>
                    <label for="is_featured_menu" class="m-0" style="cursor: pointer;"><?= __('show_on_digital_menu') ?></label>
                </div>
            </div>

            <div class="form-group mt-1">
                <label for="description"><?= __('description') ?></label>
                <textarea name="description" id="description" rows="3" class="form-control" style="resize: vertical;"><?= htmlspecialchars($product['description'] ?? '') ?></textarea>
            </div>

            <div class="form-group mt-1">
                <label for="image"><?= __('product_image') ?></label>
                <?php if ($product['image_url']): ?>
                    <div class="mb-1">
                        <img src="<?= url($product['image_url']) ?>" class="rounded shadow-sm" style="width: 100px; height: 100px; object-fit: cover;">
                    </div>
                <?php endif; ?>
                <input type="file" name="image" id="image" accept="image/*" class="form-control">
            </div>

            <div class="d-flex gap-1 mt-2">
                <button type="submit" class="btn btn-primary"><?= __('update_product') ?></button>
                <a href="<?= url('/products') ?>" class="btn btn-secondary"><?= __('cancel') ?></a>
            </div>
        </form>
    </div>
</div>