<!doctype html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($title ?? 'Equipment Rental CRM') ?> — RentalCRM</title>
    <link rel="stylesheet" href="/assets/style.css">
</head>
<body>

<?php partial('nav'); ?>

<main class="container">
    <?php partial('flash'); ?>
    <?= $content ?? '' ?>
</main>

<footer class="footer">
    <p>
        Equipment Rental CRM &nbsp;·&nbsp; PHP Lab06 Final
        &nbsp;·&nbsp; PDO · MVC · Repository · Service
        &nbsp;·&nbsp; <?= date('Y') ?>
    </p>
</footer>

</body>
</html>
