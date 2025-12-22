<div class="d-flex justify-between align-center mb-2">
    <div>
        <h1 class="text-gradient border-none mb-0">Editar Cliente</h1>
        <p class="text-dim">Actualiza la información del cliente: <strong><?= htmlspecialchars($client['name']) ?></strong></p>
    </div>
    <div class="d-flex gap-1">
        <a href="<?= url('/clients') ?>" class="btn btn-secondary">
            <i class="fa fa-arrow-left"></i> <?= __('back') ?>
        </a>
    </div>
</div>

<div class="glass-widget">
    <form action="<?= url('/clients/update?id=' . $client['id']) ?>" method="POST" class="form-grid">
        <div class="form-group">
            <label for="name" class="form-label"><?= __('name') ?> <span class="text-danger">*</span></label>
            <input type="text" name="name" id="name" class="form-control" value="<?= htmlspecialchars($client['name']) ?>" required>
        </div>

        <div class="form-group">
            <label for="tax_id" class="form-label"><?= __('tax_id') ?> / RIF</label>
            <input type="text" name="tax_id" id="tax_id" class="form-control" value="<?= htmlspecialchars($client['tax_id'] ?? '') ?>">
        </div>

        <div class="form-group">
            <label for="email" class="form-label"><?= __('email') ?></label>
            <input type="email" name="email" id="email" class="form-control" value="<?= htmlspecialchars($client['email'] ?? '') ?>">
        </div>

        <div class="form-group">
            <label for="phone" class="form-label"><?= __('phone') ?></label>
            <input type="text" name="phone" id="phone" class="form-control" value="<?= htmlspecialchars($client['phone'] ?? '') ?>">
        </div>

        <div class="form-group col-span-2">
            <label for="address" class="form-label"><?= __('address') ?></label>
            <textarea name="address" id="address" class="form-control" rows="2"><?= htmlspecialchars($client['address'] ?? '') ?></textarea>
        </div>

        <div class="form-group col-span-2">
            <label for="notes" class="form-label"><?= __('notes') ?></label>
            <textarea name="notes" id="notes" class="form-control" rows="2"><?= htmlspecialchars($client['notes'] ?? '') ?></textarea>
        </div>

        <div class="col-span-2 d-flex justify-end gap-1 mt-2">
            <button type="submit" class="btn btn-primary">
                <i class="fa fa-save"></i> <?= __('update') ?>
            </button>
        </div>
    </form>
</div>
