<div class="glass-effect p-2 mt-4 rounded">
    <div class="d-flex justify-between align-center mb-2">
        <h1 class="text-white m-0"><?= __('supplier_management') ?></h1>
        <a href="<?= url('/suppliers/create') ?>" class="btn btn-primary">
            <i class="fa fa-plus"></i> <?= __('new_supplier') ?>
        </a>
    </div>

    <?php if (empty($suppliers)): ?>
        <div class="flex-center flex-col p-4">
            <i class="fa fa-truck text-dim text-4xl mb-1"></i>
            <p class="text-dim"><?= __('no_suppliers_registered') ?></p>
            <a href="<?= url('/suppliers/create') ?>" class="btn btn-primary mt-1">
                <?= __('add_first_supplier') ?>
            </a>
        </div>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th><?= __('name') ?></th>
                        <th><?= __('contact') ?></th>
                        <th><?= __('email') ?></th>
                        <th><?= __('phone') ?></th>
                        <th class="text-center"><?= __('status') ?></th>
                        <th class="text-center"><?= __('actions') ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($suppliers as $supplier): ?>
                        <tr>
                            <td>
                                <strong><?= htmlspecialchars($supplier['name']) ?></strong>
                            </td>
                            <td class="text-dim">
                                <?= htmlspecialchars($supplier['contact_person'] ?? '-') ?>
                            </td>
                            <td class="text-dim">
                                <?= htmlspecialchars($supplier['email'] ?? '-') ?>
                            </td>
                            <td class="text-dim">
                                <?= htmlspecialchars($supplier['phone'] ?? '-') ?>
                            </td>
                            <td class="text-center">
                                <?php if (isset($supplier['is_active']) && $supplier['is_active']): ?>
                                    <span class="badge badge-success"><?= __('active') ?></span>
                                <?php else: ?>
                                    <span class="badge badge-secondary"><?= __('inactive') ?></span>
                                <?php endif; ?>
                            </td>
                            <td class="text-center">
                                <a href="<?= url('/suppliers/edit?id=' . $supplier['id']) ?>" class="btn-icon" title="<?= __('edit') ?>"><i class="fa fa-edit"></i></a>
                                <a href="<?= url('/suppliers/delete?id=' . $supplier['id']) ?>" class="btn-icon text-danger" title="<?= __('delete') ?>" onclick="return confirm('<?= __('confirm_delete_supplier') ?>')"><i class="fa fa-trash"></i></a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>
