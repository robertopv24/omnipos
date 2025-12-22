<aside class="sidebar">
    <div class="sidebar-header">
        <div class="logo-icon">O</div>
        <span class="brand-title">OmniPOS</span>
    </div>

    <div class="business-card-wrap">
        <div class="business-card">
            <div class="d-flex align-center gap-1">
                <div class="flex-center rounded overflow-hidden shadow-sm" style="width: 42px; height: 42px; background: rgba(255,255,255,0.03); border: 1px solid var(--border-bright);">
                    <?php if ($logo = \OmniPOS\Core\Session::get('business_logo')): ?>
                        <img src="<?= url('/uploads/' . $logo) ?>" class="w-100 h-100" style="object-fit:contain;">
                    <?php else: ?>
                        <i class="fa fa-briefcase text-dim" style="font-size: 1rem; opacity: 0.4;"></i>
                    <?php endif; ?>
                </div>
                <div class="flex-1 overflow-hidden">
                    <div class="business-name">
                        <?= \OmniPOS\Core\Session::get('business_name') ?? __('main_branch') ?>
                    </div>
                    <a href="<?= url('/account/businesses') ?>" class="text-sm font-700 text-bright" style="text-decoration: none; opacity: 0.8;">
                        <?= __('change_business') ?>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <nav class="nav-section">
        <?php foreach ($menus as $menu): ?>
            <?php $hasChildren = !empty($menu['children']); ?>
            <div class="nav-group">
                <a href="<?= $hasChildren ? 'javascript:void(0)' : url($menu['url']) ?>"
                    class="nav-link <?= ($menu['is_active'] ?? false) ? 'active' : '' ?>"
                    <?= $hasChildren ? 'onclick="toggleSubmenu(this)"' : '' ?>>
                    <?php if ($menu['icon']): ?><i class="<?= $menu['icon'] ?>"></i><?php endif; ?>
                    <span><?= __($menu['title']) ?></span>
                    <?php if ($hasChildren): ?>
                        <i class="fa fa-chevron-right text-sm text-dim"
                            style="margin-left: auto; transition: transform 0.2s; <?= $menu['is_active'] ? 'transform: rotate(90deg);' : '' ?>"></i>
                    <?php endif; ?>
                </a>
                <?php if ($hasChildren): ?>
                    <div class="submenu-wrap" style="<?= $menu['is_active'] ? 'display: block;' : 'display: none;' ?>">
                        <?php foreach ($menu['children'] as $child): ?>
                            <a href="<?= url($child['url']) ?>"
                                class="nav-link <?= ($child['is_active'] ?? false) ? 'active' : '' ?> text-sm"
                                style="padding-left: 0.75rem;">
                                <span><?= __($child['title']) ?></span>
                            </a>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    </nav>

    <div class="p-1.5 mt-auto" style="border-top: 1px solid var(--border-subtle);">
        <div class="d-flex align-center gap-1 mb-1">
            <div class="avatar-main">
                <?= strtoupper(substr(\OmniPOS\Core\Session::get('user_name', 'U'), 0, 1)) ?>
            </div>
            <div class="overflow-hidden">
                <div class="text-bright font-700 text-ellipsis">
                    <?= \OmniPOS\Core\Session::get('user_name') ?>
                </div>
                <div class="text-sm text-dim">
                    <?= formatRole(\OmniPOS\Core\Session::get('role')) ?>
                </div>
            </div>
        </div>
        <a href="<?= url('/logout') ?>" class="d-flex align-center gap-1 p-1 rounded font-700" style="color: #f87171; text-decoration: none; font-size: 0.9rem; background: rgba(239, 68, 68, 0.05); transition: background 0.2s;">
            <i class="fa fa-power-off" style="font-size: 1rem;"></i>
            <span><?= __('logout') ?></span>
        </a>
    </div>
</aside>
