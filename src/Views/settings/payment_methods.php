<div class="d-flex justify-between align-center mb-2">
    <div>
        <h1 class="text-gradient border-none mb-0"><?= __('payment_methods') ?></h1>
        <p class="text-dim"><?= __('manage_payment_methods_description') ?></p>
    </div>
    <div class="d-flex gap-1">
        <button onclick="document.getElementById('addMethodModal').style.display = 'flex'" class="btn btn-primary d-flex align-center gap-05">
            <i class="fa fa-plus"></i> <?= __('add_method') ?>
        </button>
    </div>
</div>

<div class="grid-3 gap-2">
    <?php foreach ($methods as $method): ?>
        <div class="glass-widget p-1-5 d-flex flex-column justify-between h-100 <?= !$method['is_active'] ? 'opacity-50' : '' ?>">
            <div class="d-flex justify-between align-start mb-1">
                <div class="avatar-circle <?= $method['currency'] === 'USD' ? 'bg-success-10 text-success' : 'bg-info-10 text-info' ?>">
                    <i class="fa <?= $method['type'] === 'cash' ? 'fa-money-bill-wave' : ($method['type'] === 'bank' ? 'fa-university' : 'fa-mobile-alt') ?>"></i>
                </div>
                <div class="d-flex gap-05">
                    <form action="<?= url('/settings/payment-methods/toggle?id=' . $method['id']) ?>" method="POST">
                        <button type="submit" class="btn-icon" title="<?= $method['is_active'] ? __('deactivate') : __('activate') ?>">
                            <i class="fa <?= $method['is_active'] ? 'fa-toggle-on text-success' : 'fa-toggle-off text-dim' ?> text-lg"></i>
                        </button>
                    </form>
                    <form action="<?= url('/settings/payment-methods/delete?id=' . $method['id']) ?>" method="POST" onsubmit="return confirm('<?= __('confirm_delete_payment_method') ?>')">
                        <button type="submit" class="btn-icon text-danger" title="<?= __('delete') ?>">
                            <i class="fa fa-trash-alt"></i>
                        </button>
                    </form>
                </div>
            </div>

            <div>
                <h3 class="text-lg font-700 mb-025"><?= htmlspecialchars($method['name']) ?></h3>
                <div class="d-flex align-center gap-05 mb-1">
                    <span class="badge badge-secondary"><?= $method['currency'] ?></span>
                    <span class="text-dim text-sm"><?= ucfirst($method['type']) ?></span>
                </div>
            </div>

            <div class="mt-1 pt-1 border-top border-bright d-flex justify-between align-center">
                <span class="text-xs font-600 <?= $method['is_active'] ? 'text-success' : 'text-dim' ?>">
                    <?= $method['is_active'] ? __('active') : __('inactive') ?>
                </span>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<!-- Add Method Modal -->
<div id="addMethodModal" class="modal-backdrop">
    <div class="modal-content glass-effect max-w-500">
        <div class="modal-header d-flex justify-between align-center mb-1-5">
            <h2 class="text-gradient border-none mb-0"><?= __('new_payment_method') ?></h2>
            <button onclick="document.getElementById('addMethodModal').style.display = 'none'" class="btn-icon">
                <i class="fa fa-times"></i>
            </button>
        </div>
        
        <form action="<?= url('/settings/payment-methods') ?>" method="POST" class="d-flex flex-column gap-1-5">
            <div class="form-group">
                <label for="name" class="form-label"><?= __('method_name') ?></label>
                <input type="text" name="name" id="name" class="form-control" placeholder="<?= __('eg_cash_usd_zelle') ?>..." required>
            </div>

            <div class="grid-2 gap-1">
                <div class="form-group">
                    <label for="currency" class="form-label"><?= __('currency') ?></label>
                    <div class="select-wrapper">
                        <select name="currency" id="currency" class="form-control" required>
                            <option value="USD"><?= __('usd_dollars') ?></option>
                            <option value="VES"><?= __('ves_bolivars') ?></option>
                        </select>
                        <i class="fa fa-chevron-down select-arrow"></i>
                    </div>
                </div>

                <div class="form-group">
                    <label for="type" class="form-label"><?= __('type') ?></label>
                    <div class="select-wrapper">
                        <select name="type" id="type" class="form-control" required>
                            <option value="cash"><?= __('cash') ?></option>
                            <option value="bank"><?= __('bank_transfer') ?></option>
                            <option value="digital"><?= __('digital_payment_others') ?></option>
                        </select>
                        <i class="fa fa-chevron-down select-arrow"></i>
                    </div>
                </div>
            </div>

            <div class="d-flex justify-end gap-1 mt-1">
                <button type="button" onclick="document.getElementById('addMethodModal').style.display = 'none'" class="btn btn-secondary"><?= __('cancel') ?></button>
                <button type="submit" class="btn btn-primary"><?= __('save') ?></button>
            </div>
        </form>
    </div>
</div>
