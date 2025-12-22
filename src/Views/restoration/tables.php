<div class="glass-effect p-2 mt-2 rounded">
    <h1 class="text-white mb-2"><i class="fas fa-table"></i> <?= __('table_management') ?></h1>

    <div class="dashboard-grid" style="grid-template-columns: 1fr 2fr; gap: 2rem;">
        <!-- Formulario para agregar mesa -->
        <div class="glass-effect p-15 rounded">
            <h3 class="text-white mb-15"><i class="fas fa-plus"></i> <?= __('new_table') ?></h3>
            <form action="<?= url('/restoration/tables') ?>" method="POST">
                <div class="form-group mb-1">
                    <label><?= __('table_number_name') ?></label>
                    <input type="text" name="table_number" class="form-control" 
                           placeholder="<?= __('table_example') ?>" required>
                </div>
                <div class="form-group mb-1">
                    <label><?= __('capacity_persons') ?></label>
                    <input type="number" name="capacity" class="form-control" value="4" min="1" required>
                </div>
                <div class="form-group mb-1">
                    <label><?= __('zone_location') ?></label>
                    <input type="text" name="zone" class="form-control" 
                           placeholder="<?= __('zone_example') ?>" value="General">
                </div>
                <button type="submit" class="btn btn-primary w-100 mt-1">
                    <i class="fas fa-save"></i> <?= __('save_table') ?>
                </button>
            </form>
        </div>

        <!-- Listado de mesas -->
        <div class="glass-effect p-15 rounded">
            <h3 class="text-white mb-15"><i class="fas fa-list"></i> <?= __('configured_tables') ?></h3>
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th><?= __('table') ?></th>
                            <th><?= __('capacity') ?></th>
                            <th><?= __('zone') ?></th>
                            <th><?= __('status') ?></th>
                            <th class="text-right"><?= __('actions') ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($tables)): ?>
                            <tr>
                                <td colspan="5" class="text-center text-dim p-2">
                                    <?= __('no_tables_configured') ?>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($tables as $t): ?>
                                <tr>
                                    <td class="font-600 text-bright">
                                        <?= htmlspecialchars($t['table_number']) ?>
                                    </td>
                                    <td>
                                        <i class="fas fa-users"></i> <?= $t['capacity'] ?> pers.
                                    </td>
                                    <td>
                                        <span class="badge badge-secondary">
                                            <?= htmlspecialchars($t['zone']) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php if ($t['status'] === 'available'): ?>
                                            <span class="badge badge-success"><?= __('available') ?></span>
                                        <?php elseif ($t['status'] === 'occupied'): ?>
                                            <span class="badge badge-danger"><?= __('occupied') ?></span>
                                        <?php else: ?>
                                            <span class="badge badge-warning"><?= __('reserved') ?></span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-right">
                                        <a href="<?= url('/restoration/tables/delete?id=' . $t['id']) ?>" 
                                           class="btn-icon text-danger"
                                           onclick="return confirm('<?= __('confirm_delete_table') ?>')">
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
