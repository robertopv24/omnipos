<div class="glass-effect flex-center flex-col p-2 rounded" style="max-width: 500px; margin: 4rem auto;">
    <div class="text-info text-4xl mb-1">
        <i class="fa fa-cash-register"></i>
    </div>
    <h1 class="text-white mb-05"><?= $title ?></h1>
    <p class="text-dim mb-2 text-center"><?= __('opening_balance_instruction') ?></p>

    <form action="<?= url('/cash/open') ?>" method="POST" class="w-100">
        <div class="dashboard-grid gap-1 mb-2">
            <div class="form-group text-left">
                <label class="text-dim text-xs mb-05 block"><?= __('initial_balance_usd') ?></label>
                <div class="pos-relative">
                    <span class="pos-absolute left-1 top-05 text-dim">$</span>
                    <input type="number" name="opening_balance_usd" step="0.01" value="0.00" required
                        class="form-control pl-2">
                </div>
            </div>
            <div class="form-group text-left">
                <label class="text-dim text-xs mb-05 block"><?= __('initial_balance_ves') ?></label>
                <div class="pos-relative">
                    <span class="pos-absolute left-1 top-05 text-dim">Bs</span>
                    <input type="number" name="opening_balance_ves" step="0.01" value="0.00" required
                        class="form-control pl-2">
                </div>
            </div>
        </div>

        <button type="submit" class="btn btn-primary btn-lg w-100 font-700">
            <?= __('start_shift') ?>
        </button>
    </form>
</div>