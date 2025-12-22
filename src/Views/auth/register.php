<div class="glass-effect p-2 rounded" style="max-width: 500px; margin: 3rem auto;">
    <h2 class="text-center mb-1"><?= __('register_new_business') ?></h2>
    <p class="text-center text-dim mb-2"><?= __('join_omnipos_description') ?></p>

    <?php if (isset($error)): ?>
        <div class="alert alert-danger text-center mb-1">
            <?= $error ?>
        </div>
    <?php endif; ?>

    <form action="<?= url('/register') ?>" method="POST">
        <div class="form-row" style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
            <div class="form-group mb-1">
                <label for="name" class="block mb-05"><?= __('full_name') ?></label>
                <input type="text" name="name" id="name" required class="form-control" placeholder="<?= __('enter_name') ?>">
            </div>
            <div class="form-group mb-1">
                <label for="email" class="block mb-05"><?= __('email_address') ?></label>
                <input type="email" name="email" id="email" required class="form-control" placeholder="<?= __('enter_email') ?>">
            </div>
            <div class="form-group mb-1">
                <label for="document_id" class="block mb-05"><?= __('tax_id') ?></label>
                <input type="text" name="document_id" id="document_id" required class="form-control" placeholder="V-12345678">
            </div>
        </div>

        <div class="form-row" style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
            <div class="form-group mb-1">
                <label for="business_name" class="block mb-05"><?= __('business_name') ?></label>
                <input type="text" name="business_name" id="business_name" required class="form-control" placeholder="<?= __('enter_business_name') ?>">
            </div>
            <div class="form-group mb-1">
                <label for="phone" class="block mb-05"><?= __('phone') ?></label>
                <input type="text" name="phone" id="phone" required class="form-control" placeholder="+58 412...">
            </div>
        </div>

        <div class="form-group mb-1">
            <label for="address" class="block mb-05"><?= __('physical_address') ?></label>
            <textarea name="address" id="address" required class="form-control" rows="2" placeholder="<?= __('enter_address') ?>"></textarea>
        </div>

        <div class="form-group mb-2">
            <label for="password" class="block mb-05"><?= __('password') ?></label>
            <input type="password" name="password" id="password" required class="form-control" placeholder="<?= __('enter_password') ?>">
        </div>

        <button type="submit" class="btn btn-primary w-100 mb-2"><?= __('register_now') ?></button>
        
        <p class="text-center text-sm text-dim">
            <?= __('already_have_account') ?> <a href="<?= url('/login') ?>" class="text-bright font-bold"><?= __('login_now') ?></a>
        </p>
    </form>
</div>
