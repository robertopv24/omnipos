<div class="glass-effect p-2 mt-2 rounded" style="max-width: 800px; margin: 2rem auto;">
    <h1 class="text-white mb-2"><?= __('edit_supplier') ?></h1>

    <form action="<?= url('/suppliers/update?id=' . $supplier['id']) ?>" method="POST">
        <div class="form-grid">
            <div class="form-group" style="grid-column: span 2;">
                <label><?= __('supplier_name') ?> *</label>
                <input type="text" name="name" value="<?= htmlspecialchars($supplier['name']) ?>" class="form-control" required>
            </div>

            <div class="form-group">
                <label><?= __('contact_person') ?></label>
                <input type="text" name="contact_person" value="<?= htmlspecialchars($supplier['contact_person'] ?? '') ?>" class="form-control">
            </div>

            <div class="form-group">
                <label><?= __('email') ?></label>
                <input type="email" name="email" value="<?= htmlspecialchars($supplier['email'] ?? '') ?>" class="form-control">
            </div>

            <div class="form-group">
                <label><?= __('phone') ?></label>
                <input type="text" name="phone" value="<?= htmlspecialchars($supplier['phone'] ?? '') ?>" class="form-control">
            </div>

            <div class="form-group">
                <label><?= __('status') ?></label>
                <select name="is_active" class="form-control">
                    <option value="1" <?= $supplier['is_active'] ? 'selected' : '' ?>><?= __('active') ?></option>
                    <option value="0" <?= !$supplier['is_active'] ? 'selected' : '' ?>><?= __('inactive') ?></option>
                </select>
            </div>

            <div class="form-group" style="grid-column: span 2;">
                <label><?= __('address') ?></label>
                <textarea name="address" class="form-control" rows="3" style="resize: vertical;"><?= htmlspecialchars($supplier['address'] ?? '') ?></textarea>
            </div>
        </div>

        <div class="d-flex justify-end gap-1 mt-2">
            <a href="<?= url('/suppliers') ?>" class="btn btn-secondary">
                <?= __('cancel') ?>
            </a>
            <button type="submit" class="btn btn-primary">
                <i class="fa fa-save"></i> <?= __('save_changes') ?>
            </button>
        </div>
    </form>
</div>
