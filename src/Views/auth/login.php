<div class="glass-effect p-2 rounded" style="max-width: 400px; margin: 5rem auto;">
    <h2 class="text-center mb-2"><?= __('login_to_omnipos') ?></h2>

    <?php if (isset($error)): ?>
        <div class="alert alert-danger text-center mb-1">
            <?= $error ?>
        </div>
    <?php endif; ?>

    <form action="<?= url('/login') ?>" method="POST">
        <div class="form-group mb-1">
            <label for="email" class="block mb-05"><?= __('email_address') ?></label>
            <input type="email" name="email" id="email" required class="form-control">
        </div>

        <div class="form-group mb-2">
            <label for="password" class="block mb-05"><?= __('password') ?></label>
            <input type="password" name="password" id="password" required class="form-control">
        </div>

        <button type="submit" class="btn btn-primary w-100"><?= __('login') ?></button>
    </form>
</div>