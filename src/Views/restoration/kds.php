<div class="content-header">
    <h1><i class="fas fa-desktop"></i> <?= __('kds_screen') ?></h1>
    <div class="d-flex gap-1">
        <a href="<?= url('/restoration/kds') ?>" class="btn <?= !$currentStation ? 'btn-primary' : 'btn-secondary' ?>"><?= __('all_stations') ?></a>
        <a href="<?= url('/restoration/kds?station=pizza') ?>" class="btn <?= $currentStation == 'pizza' ? 'btn-primary' : 'btn-secondary' ?>"><?= __('pizzeria') ?></a>
        <a href="<?= url('/restoration/kds?station=kitchen') ?>" class="btn <?= $currentStation == 'kitchen' ? 'btn-primary' : 'btn-secondary' ?>"><?= __('kitchen') ?></a>
        <a href="<?= url('/restoration/kds?station=bar') ?>" class="btn <?= $currentStation == 'bar' ? 'btn-primary' : 'btn-secondary' ?>"><?= __('bar') ?></a>
    </div>
</div>

<div class="kds-grid d-grid gap-2" style="grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));">
    <?php if (empty($orders)): ?>
        <div class="col-span-full text-center p-5 opacity-50">
            <i class="fas fa-check-circle d-block mb-1" style="font-size: 4rem;"></i>
            <h2><?= __('all_ready') ?></h2>
            <p><?= __('no_pending_orders') ?></p>
        </div>
    <?php else: ?>
        <?php foreach ($orders as $orderId => $order): ?>
            <div class="card kds-order-card d-flex flex-column border-top-primary">
                <div class="card-header d-flex justify-between align-center p-1 bg-glass">
                    <span class="font-800 text-lg">#<?= $order['order_number'] ?></span>
                    <span class="timer badge font-mono text-base" data-start="<?= $order['order_time'] ?>">00:00</span>
                </div>

                <div class="card-body flex-1 p-1">
                    <?php foreach ($order['items'] as $item): ?>
                        <div class="kds-item mb-2 pb-1 border-bottom border-glass">
                            <div class="d-flex justify-between align-start">
                                <span class="font-700 text-lg">
                                    <?= (float)$item['quantity'] ?>x <?= htmlspecialchars($item['product_name']) ?>
                                </span>
                                <span class="badge text-xs opacity-70"><?= strtoupper($item['kitchen_station']) ?></span>
                            </div>

                            <?php if (!empty($item['modifications_data'])): ?>
                                <ul class="my-1 pl-2 text-sm text-yellow list-none">
                                    <?php if (!empty($item['modifications_data']['extras'])): ?>
                                        <?php foreach ($item['modifications_data']['extras'] as $extra): ?>
                                            <li><i class="fas fa-plus-circle text-xs"></i> <?= htmlspecialchars($extra) ?></li>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                    <?php if (!empty($item['modifications_data']['removals'])): ?>
                                        <?php foreach ($item['modifications_data']['removals'] as $rem): ?>
                                            <li class="line-through opacity-60"><i class="fas fa-minus-circle text-xs"></i> <?= htmlspecialchars($rem) ?></li>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </ul>
                            <?php endif; ?>

                            <div class="mt-1">
                                <?php if ($item['status'] == 'pending'): ?>
                                    <button onclick="updateStatus('<?= $item['id'] ?>', 'preparing')" class="btn btn-info w-full"><?= __('prepare') ?></button>
                                <?php else: ?>
                                    <button onclick="updateStatus('<?= $item['id'] ?>', 'ready')" class="btn btn-success w-full"><?= __('ready') ?></button>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <div class="card-footer p-1 bg-dark-10 text-xs text-center font-600">
                    <?php 
                        $consType = $order['items'][0]['consumption_type'] ?? 'dine_in';
                        echo $consType == 'takeaway' ? '🥡 ' . __('takeaway') : '🍽️ ' . __('dine_in');
                    ?>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<script>
    function updateStatus(itemId, status) {
        const btn = event.target;
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';

        fetch('<?= url('/restoration/item/status') ?>', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `item_id=${itemId}&status=${status}`
        }).then(r => r.json()).then(data => {
            if (data.success) {
                // Forzar refresh inmediato de data tras cambio
                refreshKds(true);
            } else {
                alert('<?= __('error') ?>: ' + data.message);
                btn.disabled = false;
                btn.innerText = status === 'preparing' ? '<?= __('prepare') ?>' : '<?= __('ready') ?>';
            }
        });
    }

    function updateTimers() {
        document.querySelectorAll('.timer').forEach(timer => {
            const startStr = timer.dataset.start;
            if (!startStr) return;
            
            const start = new Date(startStr.replace(' ', 'T')); 
            const now = new Date();
            const diff = Math.floor((now - start) / 1000);
            
            if (isNaN(diff)) {
                timer.innerText = '--:--';
                return;
            }

            const mins = Math.floor(diff / 60).toString().padStart(2, '0');
            const secs = (diff % 60).toString().padStart(2, '0');
            timer.innerText = `${mins}:${secs}`;

            if (diff > 600) timer.className = 'timer badge btn-danger';
            else if (diff > 300) timer.className = 'timer badge btn-warning';
            else timer.className = 'timer badge btn-success';
        });
    }

    setInterval(updateTimers, 1000);
    updateTimers();

    let lastDataHash = '';

    function refreshKds(forceUpdate = false) {
        const station = '<?= $currentStation ?>';
        fetch(`<?= url('/restoration/kds/data') ?>?station=${station}`)
            .then(r => r.json())
            .then(data => {
                const currentHash = JSON.stringify(data);
                if (currentHash !== lastDataHash || forceUpdate) {
                    lastDataHash = currentHash;
                    // Recargamos solo si hay cambios reales en los datos
                    location.reload(); 
                }
            });
    }

    // Polling cada 5 segundos
    setInterval(refreshKds, 5000);
</script>