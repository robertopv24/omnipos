<div class="d-flex justify-between align-center mb-2">
    <div>
        <h1 class="text-gradient border-none mb-0"><?= __('my_profile') ?></h1>
        <p class="text-dim"><?= __('profile_description') ?></p>
    </div>
</div>

<div class="grid-3 gap-2">
    <div class="glass-widget col-span-3 md:col-span-1 d-flex flex-column align-center p-3">
        <div class="avatar-circle-lg bg-primary-10 text-primary mb-1-5 text-4xl">
            <?= strtoupper(substr($user['name'], 0, 1)) ?>
        </div>
        <h2 class="text-xl font-700"><?= htmlspecialchars($user['name']) ?></h2>
        <p class="text-dim mb-1-5"><?= htmlspecialchars($user['role']) ?></p>
        
        <div class="w-100 border-top border-bright pt-1-5">
            <div class="d-flex justify-between mb-05">
                <span class="text-dim"><?= __('user_id_label') ?>:</span>
                <span class="font-mono text-xs"><?= substr($user['id'], 0, 8) ?></span>
            </div>
            <div class="d-flex justify-between">
                <span class="text-dim"><?= __('member_since') ?>:</span>
                <span><?= date('d/m/Y', strtotime($user['created_at'])) ?></span>
            </div>
        </div>
    </div>

    <div class="glass-widget col-span-3 md:col-span-2">
        <h3 class="text-lg font-600 mb-1-5">Información Personal</h3>
        <form action="<?= url('/profile/update') ?>" method="POST" class="form-grid">
            <div class="form-group">
                <label for="name" class="form-label"><?= __('name') ?></label>
                <input type="text" name="name" id="name" class="form-control" value="<?= htmlspecialchars($user['name']) ?>" required>
            </div>

            <div class="form-group">
                <label for="email" class="form-label"><?= __('email') ?></label>
                <input type="email" name="email" id="email" class="form-control" value="<?= htmlspecialchars($user['email']) ?>" required>
            </div>

            <div class="form-group col-span-2">
                <label for="password" class="form-label"><?= __('new_password') ?></label>
                <input type="password" name="password" id="password" class="form-control" placeholder="<?= __('leave_blank_no_change_password') ?>">
                <p class="text-xs text-dim mt-05"><?= __('password_change_recommendation') ?></p>
            </div>

            <div class="col-span-2 d-flex justify-end mt-1">
                <button type="submit" class="btn btn-primary">
                    <i class="fa fa-save"></i> <?= __('save_changes') ?>
                </button>
            </div>
        </form>
    </div>
</div>
