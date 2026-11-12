<?php
require_once(__DIR__ . "/../lib/csrf.php");
$pageURI = $_SERVER["REQUEST_URI"];
$isIndexPage = false;
$layoutPageTitle = $pageName ?? $config["platformTitle"];
header("Cache-Control: private, must-revalidate");
require_once __DIR__ . '/../assets/ui-kit/kit.php';
?>
<!DOCTYPE html>
<html lang="<?= __("html_lang") ?>">
  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="shortcut icon" href="/assets/images/favicon.ico">
    <title><?= $config["platformTitle"] ?></title>
    <link rel="stylesheet" href="/assets/css/app.css?v=<?= asset_version('assets/css/app.css') ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
  </head>
  <body class="minimal-layout">
    <?= ui_toast_container() ?>
    <?php require_once(__DIR__ . "/../pages/$view/$view.view.php"); ?>
    <?php require_once(__DIR__ . "/footer.php"); ?>
  </body>
</html>
