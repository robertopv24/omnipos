<div class="user-create-container" style="max-width: 600px;">
    <h1><?= __('add_new_user') ?></h1>

    <div class="glass-effect p-2 mt-1 rounded">
        <form action="<?= url('/users') ?>" method="POST">
            <div class="form-group">
                <label for="name"><?= __('full_name') ?></label>
                <input type="text" name="name" id="name" class="form-control" required placeholder="<?= __('enter_name') ?>">
            </div>

            <div class="form-group">
                <label for="email"><?= __('email_address') ?></label>
                <input type="email" name="email" id="email" class="form-control" required placeholder="<?= __('enter_email') ?>">
            </div>

            <div class="form-group">
                <label for="password"><?= __('password') ?></label>
                <input type="password" name="password" id="password" class="form-control" required placeholder="<?= __('enter_password') ?>">
            </div>

            <div class="form-group">
                <label for="business_id"><?= __('business_assignment') ?></label>
                <select name="business_id" id="business_id" class="form-control" required>
                    <option value=""><?= __('select_business') ?>...</option>
                    <?php foreach ($businesses as $b): ?>
                        <option value="<?= $b['id'] ?>"><?= htmlspecialchars($b['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="role"><?= __('role') ?></label>
                <select name="role" id="role" class="form-control" required>
                    <option value="user"><?= __('standard_user') ?></option>
                    <option value="manager"><?= __('manager') ?></option>
                    <option value="admin"><?= __('business_admin') ?></option>
                    <option value="account_admin"><?= __('account_admin') ?></option>
                </select>
            </div>

            <div class="d-flex gap-1 mt-1">
                <button type="submit" class="btn btn-primary"><?= __('save_user') ?></button>
                <a href="<?= url('/users') ?>" class="btn btn-secondary"><?= __('cancel') ?></a>
            </div>
        </form>
    </div>
</div>