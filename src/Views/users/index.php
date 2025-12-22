<div class="users-container">
    <div class="d-flex justify-between align-center mb-2">
        <h1 class="m-0"><?= __('users') ?></h1>
        <a href="<?= url('/users/create') ?>" class="btn btn-primary">
            <i class="fa fa-user-plus"></i> <?= __('add_user') ?>
        </a>
    </div>

    <div class="glass-effect p-1.5 rounded overflow-hidden">
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th><?= __('name') ?></th>
                        <th><?= __('email') ?></th>
                        <th><?= __('role') ?></th>
                        <th><?= __('actions') ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $user): ?>
                        <tr>
                            <td class="font-700"><?= htmlspecialchars($user['name']) ?></td>
                            <td><?= htmlspecialchars($user['email']) ?></td>
                            <td>
                                <span class="badge badge-info">
                                    <?= formatRole($user['role']) ?>
                                </span>
                            </td>
                            <td>
                                <div class="d-flex gap-05">
                                    <a href="<?= url('/users/edit?id=' . $user['id']) ?>" class="btn-icon" title="<?= __('edit') ?>">
                                        <i class="fa fa-edit"></i>
                                    </a>
                                    <a href="<?= url('/users/delete?id=' . $user['id']) ?>" class="btn-icon" style="color: #ef4444;"
                                        title="<?= __('delete') ?>"
                                        onclick="return confirm('<?= __('confirm_delete') ?>')">
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