<div class="container py-4">
    <div class="grid-2 gap-4 align-center max-w-1000 mx-auto">
        <div>
            <h1 class="text-4xl font-900 text-gradient mb-1"><?= __('get_in_touch') ?></h1>
            <p class="text-lg text-dim mb-3"><?= __('contact_description') ?></p>
            
            <div class="contact-info d-flex flex-column gap-2">
                <div class="d-flex align-center gap-1">
                    <div class="icon-circle bg-primary-10 text-primary">
                        <i class="fa fa-envelope"></i>
                    </div>
                    <div>
                        <h4 class="font-700">Email</h4>
                        <p class="text-dim">soporte@omnipos.test</p>
                    </div>
                </div>
                <div class="d-flex align-center gap-1">
                    <div class="icon-circle bg-success-10 text-success">
                        <i class="fa fa-phone"></i>
                    </div>
                    <div>
                        <h4 class="font-700">Teléfono</h4>
                        <p class="text-dim">+58 212-000-0000</p>
                    </div>
                </div>
                <div class="d-flex align-center gap-1">
                    <div class="icon-circle bg-warning-10 text-warning">
                        <i class="fa fa-map-marker-alt"></i>
                    </div>
                    <div>
                        <h4 class="font-700">Oficina</h4>
                        <p class="text-dim">Caracas, Venezuela</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="glass-widget p-3">
            <h3 class="text-xl font-700 mb-1-5"><?= __('send_us_message') ?></h3>
            <form action="#" class="d-flex flex-column gap-1">
                <div class="form-group">
                    <label class="form-label"><?= __('name') ?></label>
                    <input type="text" class="form-control" placeholder="<?= __('full_name') ?>">
                </div>
                <div class="form-group">
                    <label class="form-label"><?= __('email') ?></label>
                    <input type="email" class="form-control" placeholder="tu@email.com">
                </div>
                <div class="form-group">
                    <label class="form-label"><?= __('message') ?></label>
                    <textarea class="form-control" rows="4" placeholder="<?= __('how_can_we_help') ?>"></textarea>
                </div>
                <button type="button" class="btn btn-primary w-100 mt-1"><?= __('send_message') ?></button>
            </form>
        </div>
    </div>
</div>
