<!DOCTYPE html>
<html lang="<?= \OmniPOS\Services\I18nService::getLocale() ?>">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'OmniPOS' ?> - <?= $siteName ?? 'SaaS' ?></title>
    <link rel="stylesheet" href="<?= url('/css/style.css') ?>">
    <link rel="stylesheet" href="<?= url('/css/components.css') ?>">
    <link rel="stylesheet" href="<?= url('/css/pos.css') ?>">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>

<body>
    <div class="app-container">
        <!-- Header / Navbar -->
        <header class="main-header glass-effect">
            <div class="header-content">
                <a href="<?= url('/') ?>" class="brand-link">
                    <div class="logo-icon-small">
                        <i class="fa-solid fa-cash-register"></i>
                    </div>
                    <span><?= $siteName ?? 'OmniPOS' ?></span>
                </a>

                <nav class="main-nav">
                    <ul>
                        <?php if (isset($headerMenus) && !empty($headerMenus)): ?>
                            <?php foreach ($headerMenus as $menu): ?>
                                <li>
                                    <a href="<?= url($menu['url']) ?>" class="<?= ($menu['is_active'] ?? false) ? 'active' : '' ?>">
                                        <?php if ($menu['icon']): ?><i class="<?= $menu['icon'] ?>"></i><?php endif; ?>
                                        <?= $menu['title'] ?>
                                    </a>
                                </li>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <li><a href="<?= url('/') ?>"><?= __('home') ?></a></li>
                            <li><a href="<?= url('/shop') ?>"><i class="fa-solid fa-shopping-bag"></i> <?= __('Ver Tienda') ?></a></li>
                        <?php endif; ?>
                    </ul>
                </nav>

                <div class="user-actions">
                    <?php if (\OmniPOS\Core\Session::has('user_id')): ?>
                        <a href="<?= url('/dashboard') ?>" class="btn btn-primary btn-sm">
                            <i class="fa-solid fa-gauge-high"></i> <?= __('dashboard') ?>
                        </a>
                        <a href="<?= url('/logout') ?>" class="btn btn-outline-danger btn-sm ml-2">
                            <i class="fa-solid fa-right-from-bracket"></i>
                        </a>
                    <?php else: ?>
                        <a href="<?= url('/login') ?>" class="btn btn-outline-bright btn-sm">
                            <?= __('login') ?>
                        </a>
                        <a href="<?= url('/register') ?>" class="btn btn-primary btn-sm ml-2">
                            <i class="fa-solid fa-business-time"></i> <?= __('add_business') ?>
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </header>

        <!-- Main Content -->
        <main class="content-wrapper">
            <?= $viewContent ?>
        </main>

        <!-- Footer -->
        <footer class="main-footer">
            <p>&copy; <?= date('Y') ?> <?= $siteName ?? 'OmniPOS' ?>. <?= __('all_rights_reserved') ?></p>
        </footer>
    </div>
</body>

</html>