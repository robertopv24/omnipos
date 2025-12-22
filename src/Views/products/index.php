<div class="glass-effect p-2 mt-4 rounded">
    <div class="d-flex justify-between align-center mb-1">
        <h1 class="text-white m-0"><?= __('products') ?></h1>
        <a href="<?= url('/products/create') ?>" class="btn btn-primary">
            <i class="fa fa-plus"></i> <?= __('new_product') ?>
        </a>
    </div>

    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th><?= __('image') ?></th>
                    <th><?= __('name') ?></th>
                    <th><?= __('category') ?></th>
                    <th><?= __('price_usd') ?></th>
                    <th><?= __('price_ves') ?></th>
                    <th><?= __('stock') ?></th>
                    <th><?= __('actions') ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($products as $product): ?>
                    <tr>
                        <td>
                            <img src="<?= $product['image_url'] ? url($product['image_url']) : url('/img/no-image.png') ?>"
                                alt="<?= htmlspecialchars($product['name']) ?>"
                                class="rounded shadow-sm" style="width: 50px; height: 50px; object-fit: cover;">
                        </td>
                        <td><strong><?= htmlspecialchars($product['name']) ?></strong></td>
                        <td><span class="badge badge-info"><?= htmlspecialchars($product['category'] ?? __('general')) ?></span></td>
                        <td class="font-700">$<?= number_format($product['price_usd'], 2) ?></td>
                        <td class="text-dim">Bs <?= number_format($product['price_ves'], 2) ?></td>
                        <td>
                            <span class="<?= (float) $product['stock'] <= (float) $product['min_stock'] ? 'text-danger font-700' : '' ?>">
                                <?= (float) $product['stock'] ?>
                            </span>
                        </td>
                        <td>
                            <div class="d-flex gap-05">
                                <a href="<?= url('/products/edit?id=' . $product['id']) ?>" class="btn-icon" title="<?= __('edit') ?>">
                                    <i class="fa fa-edit"></i>
                                </a>
                                <a href="<?= url('/inventory/traceability?product_id=' . $product['id']) ?>" class="btn-icon"
                                    title="<?= __('traceability') ?>">
                                    <i class="fa fa-history"></i>
                                </a>
                                <a href="<?= url('/products/delete?id=' . $product['id']) ?>" class="btn-icon text-danger" title="<?= __('delete') ?>"
                                    onclick="return confirm('<?= __('confirm_delete_product') ?>')">
                                    <i class="fa fa-trash"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

    </div>
</div>