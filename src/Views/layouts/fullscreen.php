<!DOCTYPE html>
<html lang="<?= \OmniPOS\Services\I18nService::getLocale() ?>">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'OmniPOS Display' ?></title>
    <link rel="stylesheet" href="<?= url('/css/style.css') ?>">
    <link rel="stylesheet" href="<?= url('/css/components.css') ?>">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;600;800&display=swap" rel="stylesheet">
    <style>
        <?php
        $themeSettings = OmniPOS\Core\Session::get('theme_settings') ?: ['primary' => '#3B82F6', 'secondary' => '#1E293B'];
        ?>
        :root {
            --primary-color: <?= $themeSettings['primary'] ?>;
            --secondary-color: <?= $themeSettings['secondary'] ?>;
        }
    </style>
</head>

<body class="font-outfit overflow-hidden m-0 p-0 bg-slate-900 text-white">
    <?= $viewContent ?>
</body>

</html>