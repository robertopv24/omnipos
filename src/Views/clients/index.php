<div class="d-flex justify-between align-center mb-2">
    <div>
        <h1 class="text-gradient border-none mb-0"><?= __('manage_clients') ?></h1>
        <p class="text-dim"><?= __('manage_clients_description') ?></p>
    </div>
    <div class="d-flex gap-1">
        <a href="<?= url('/clients/create') ?>" class="btn btn-primary d-flex align-center gap-05">
            <i class="fa fa-user-plus"></i> <?= __('new_client') ?>
        </a>
    </div>
</div>

<div class="glass-widget p-0 overflow-hidden">
    <div class="table-container">
        <table class="table w-100">
            <thead>
                <tr>
                    <th><?= __('name') ?></th>
                    <th><?= __('tax_id') ?> / RIF</th>
                    <th><?= __('email') ?></th>
                    <th><?= __('phone') ?></th>
                    <th><?= __('status') ?></th>
                    <th class="text-right"><?= __('actions') ?></th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($clients)): ?>
                    <tr>
                        <td colspan="6" class="text-center p-3 text-dim">
                            <i class="fa fa-users text-2xl mb-1 d-block"></i>
                            <?= __('no_clients_found') ?>
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($clients as $client): ?>
                        <tr>
                            <td>
                                <div class="d-flex align-center gap-05">
                                    <div class="avatar-circle-sm bg-primary-10 text-primary">
                                        <?= strtoupper(substr($client['name'], 0, 1)) ?>
                                    </div>
                                    <span class="font-600"><?= htmlspecialchars($client['name']) ?></span>
                                </div>
                            </td>
                            <td><?= htmlspecialchars($client['tax_id'] ?? 'N/A') ?></td>
                            <td><?= htmlspecialchars($client['email'] ?? '---') ?></td>
                            <td><?= htmlspecialchars($client['phone'] ?? '---') ?></td>
                            <td>
                                <span class="badge badge-success">Activo</span>
                            </td>
                            <td class="text-right">
                                <div class="d-flex justify-end gap-05">
                                    <a href="<?= url('/clients/edit?id=' . $client['id']) ?>" class="btn btn-secondary btn-sm" title="<?= __('edit') ?>">
                                        <i class="fa fa-edit"></i>
                                    </a>
                                    <form action="<?= url('/clients/delete?id=' . $client['id']) ?>" method="POST" style="display:inline;" onsubmit="return confirm('<?= __('confirm_delete') ?>')">
                                        <button type="submit" class="btn btn-danger btn-sm" title="<?= __('delete') ?>">
                                            <i class="fa fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
