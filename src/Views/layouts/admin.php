<!DOCTYPE html>
<html lang="<?= \OmniPOS\Services\I18nService::getLocale() ?>">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php 
        $currentLocale = \OmniPOS\Services\I18nService::getLocale();
        echo ($title ?? __('dashboard')) . ' - OmniPOS';
    ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="<?= url('/css/style.css') ?>">
    <link rel="stylesheet" href="<?= url('/css/components.css') ?>">
    <?php if (strpos($_SERVER['REQUEST_URI'] ?? '', 'sales') !== false): ?>
        <link rel="stylesheet" href="<?= url('/css/pos.css') ?>">
    <?php endif; ?>
    
    <script>
        const BASE_URL = '<?= url('') ?>';
    </script>
    <style>
        <?php
        $currentTheme = OmniPOS\Core\Session::get('app_theme', 'dark');
        $themeSettings = OmniPOS\Core\Session::get('theme_settings');
        if ($currentTheme === 'custom' && $themeSettings): ?>
            :root {
                --primary: <?= $themeSettings['primary'] ?>;
                --bg-sidebar: <?= $themeSettings['secondary'] ?>;
            }
        <?php endif; ?>
    </style>
</head>

<body data-theme="<?= $currentTheme === 'default' ? 'light' : $currentTheme ?>">
    <div class="admin-layout" id="admin-root">
        <?php 
            $sidebar = new \OmniPOS\Components\Sidebar();
            echo $sidebar->render();
        ?>

        <main class="main-body">
            <header class="topbar">
                <div class="d-flex align-center gap-2">
                    <button class="toggle-btn" id="sidebar-toggle">
                        <i class="fa-solid fa-bars-staggered"></i>
                    </button>
                    <h2 class="page-title"><?= $title ?? __('dashboard') ?></h2>
                </div>

                <div class="user-profile-pill">
                    <div style="width: 8px; height: 8px; background: #10b981; border-radius: 50%; box-shadow: 0 0 10px #10b981;"></div>
                    <span class="font-600"><?= \OmniPOS\Core\Session::get('user_name') ?></span>
                    <span class="text-sm text-dim">(<?= formatRole(\OmniPOS\Core\Session::get('role')) ?>)</span>
                </div>
            </header>

            <?php if (\OmniPOS\Core\Session::hasFlash('success') || \OmniPOS\Core\Session::hasFlash('error') || \OmniPOS\Core\Session::hasFlash('warning')): ?>
                <div style="padding: 1rem 3rem;">
                    <?php if ($successMsg = \OmniPOS\Core\Session::getFlash('success')): ?>
                        <div class="flash-message d-flex align-center gap-1 p-1 rounded" style="background: rgba(16, 185, 129, 0.1); border-left: 4px solid #10b981; color: #10b981; margin-bottom: 1rem;">
                            <i class="fa fa-check-circle text-lg"></i>
                            <span><?= htmlspecialchars($successMsg) ?></span>
                        </div>
                    <?php endif; ?>
                    
                    <?php if ($errorMsg = \OmniPOS\Core\Session::getFlash('error')): ?>
                        <div class="flash-message d-flex align-center gap-1 p-1 rounded" style="background: rgba(239, 68, 68, 0.1); border-left: 4px solid #ef4444; color: #ef4444; margin-bottom: 1rem;">
                            <i class="fa fa-exclamation-circle text-lg"></i>
                            <span><?= htmlspecialchars($errorMsg) ?></span>
                        </div>
                    <?php endif; ?>
                    
                    <?php if ($warningMsg = \OmniPOS\Core\Session::getFlash('warning')): ?>
                        <div class="flash-message d-flex align-center gap-1 p-1 rounded" style="background: rgba(251, 191, 36, 0.1); border-left: 4px solid #fbbf24; color: #fbbf24; margin-bottom: 1rem;">
                            <i class="fa fa-exclamation-triangle text-lg"></i>
                            <span><?= htmlspecialchars($warningMsg) ?></span>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <div class="content-page p-2">
                <?= $viewContent ?>
            </div>
        </main>
    </div>

    <script src="<?= url('/js/admin.js') ?>"></script>
</body>

</html>