<?php $title = __('tax_management'); ?>

<?php ob_start(); ?>

<div class="glass-effect p-2 mt-2 rounded">
    <h1 class="text-white mb-2"><i class="fas fa-percent"></i> <?= __('tax_configuration') ?></h1>

    <div class="dashboard-grid gap-2">
        <!-- Panel IGTF -->
        <div class="glass-effect p-15 rounded">
            <h3 class="text-white mb-1"><?= __('igtf_tax') ?></h3>
            <p class="text-dim mb-15"><?= __('igtf_description') ?></p>
            <form action="<?= url('/admin/taxes/igtf') ?>" method="POST" class="form-grid" style="grid-template-columns: 1fr auto;">
                <div class="form-group">
                    <label><?= __('igtf_percentage') ?></label>
                    <input type="number" step="0.01" name="igtf_percentage" class="form-control" value="<?= $igtf ?>" required>
                </div>
                <div class="form-group" style="align-self: flex-end;">
                    <button type="submit" class="btn btn-primary"><?= __('update') ?></button>
                </div>
            </form>
        </div>

        <!-- Panel IVA -->
        <div class="glass-effect p-15 rounded">
            <div class="d-flex justify-between align-center mb-15">
                <h3 class="text-white m-0"><?= __('vat_rates') ?></h3>
                <button type="button" class="btn btn-primary btn-sm" onclick="openModal('addTaxModal')">
                    <i class="fas fa-plus"></i> <?= __('new_rate') ?>
                </button>
            </div>
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th><?= __('name') ?></th>
                            <th><?= __('percentage') ?></th>
                            <th><?= __('default') ?></th>
                            <th><?= __('actions') ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($rates)): ?>
                            <tr><td colspan="4" class="text-center text-dim"><?= __('no_rates_configured') ?></td></tr>
                        <?php else: ?>
                            <?php foreach ($rates as $rate): ?>
                                <tr>
                                    <td><?= htmlspecialchars($rate['name']) ?></td>
                                    <td><?= number_format($rate['percentage'], 2) ?>%</td>
                                    <td>
                                        <?php if ($rate['is_default']): ?>
                                            <span class="badge badge-success"><?= __('yes') ?></span>
                                        <?php else: ?>
                                            <span class="badge badge-secondary"><?= __('no') ?></span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <a href="<?= url('/admin/taxes/delete?id=' . $rate['id']) ?>" class="btn-icon text-danger" 
                                           onclick="return confirm('<?= __('confirm_delete_rate') ?>')">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal Nueva Tasa -->
<div id="addTaxModal" class="modal">
    <div class="modal-content">
        <div class="d-flex justify-between align-center mb-2">
            <h2 class="text-white m-0"><?= __('tax_management_vat') ?></h2>
            <button type="button" class="btn-icon" onclick="closeModal('addTaxModal')">&times;</button>
        </div>
        <form action="<?= url('/admin/taxes') ?>" method="POST">
            <div class="form-group mb-1">
                <label><?= __('tax_name_example') ?></label>
                <input type="text" name="name" class="form-control" required>
            </div>
            <div class="form-group mb-1">
                <label><?= __('percentage') ?></label>
                <input type="number" step="0.01" name="percentage" class="form-control" required>
            </div>
            <div class="form-group mb-2">
                <label>
                    <input type="checkbox" name="is_default"> <?= __('set_as_default') ?>
                </label>
            </div>
            <div class="d-flex gap-1">
                <button type="button" class="btn btn-secondary flex-1" onclick="closeModal('addTaxModal')"><?= __('cancel') ?></button>
                <button type="submit" class="btn btn-primary flex-2"><?= __('save_rate') ?></button>
            </div>
        </form>
    </div>
</div>

<?php $content = ob_get_clean(); ?>
<?php include __DIR__ . '/../layouts/admin.php'; ?>
