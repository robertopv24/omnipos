<!DOCTYPE html>
<html lang="<?= \OmniPOS\Services\I18nService::getLocale() ?>">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'OmniPOS' ?></title>
    <link rel="stylesheet" href="<?= url('/css/style.css') ?>">
    <link rel="stylesheet" href="<?= url('/css/components.css') ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">

</head>

<body data-theme="dark" class="bg-main text-pure m-0">
    <?= $viewContent ?>
</body>

</html>