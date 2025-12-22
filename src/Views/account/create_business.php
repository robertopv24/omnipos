<div class="business-create-container" style="max-width: 600px; margin: 2rem auto;">
    <h1><?= __('register_new_business') ?></h1>

    <div class="glass-effect p-2 mt-2 rounded">
        <form action="<?= url('/account/business/store') ?>" method="POST">
            <div class="form-group mb-1">
                <label for="name"><?= __('business_name') ?></label>
                <input type="text" name="name" id="name" required placeholder="<?= __('enter_business_name') ?>" class="form-control">
            </div>

            <div class="form-group mb-1">
                <label for="tax_id"><?= __('tax_id') ?></label>
                <input type="text" name="tax_id" id="tax_id" required placeholder="J-12345678-9" class="form-control">
            </div>

            <div class="form-group mb-1">
                <label for="address"><?= __('physical_address') ?></label>
                <textarea name="address" id="address" rows="3" class="form-control" placeholder="<?= __('enter_address') ?>"></textarea>
            </div>

            <div class="d-flex gap-1 mt-2">
                <button type="submit" class="btn btn-primary flex-2"><?= __('save_business') ?></button>
                <a href="<?= url('/account/businesses') ?>" class="btn btn-secondary flex-1"><?= __('cancel') ?></a>
            </div>
        </form>
    </div>
</div>