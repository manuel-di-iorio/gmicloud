<?php
require_once(__DIR__ . "/../lib/csrf.php");
require_once(__DIR__ . "/../lib/consent.php");
$consentAnalytics = consent_has_analytics();
$consentDecided = consent_has_decided();
$pageURI = parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH);
$isIndexPage = basename($pageURI) === 'index.php' || $pageURI === '/';
$gameNameShowBackIcon = strpos($pageURI, "/game-scores.php") === 0 || strpos($pageURI, "/game-scores-export.php") === 0 || strpos($pageURI, "/game-scores-import.php") === 0 || strpos($pageURI, "/game-bans.php") === 0 ||
  strpos($pageURI, "/game.php") === 0 || strpos($pageURI, "/leaderboards.php") === 0 ||
  strpos($pageURI, "/add-team.php") === 0 || strpos($pageURI, "/team-move-game.php") === 0 ||
  strpos($pageURI, "/team.php") === 0;
$backUrl = $backUrl ?? "games.php";
$layoutPageTitle = $pageName ?? $pageTitle ?? $config["platformTitle"];
$layoutPageDesc = $pageDesc ?? '';
$layoutPageBackUrl = $gameNameShowBackIcon ? $backUrl : '';
header("Cache-Control: private, must-revalidate");
require_once __DIR__ . '/../assets/ui-kit/kit.php';
?>
<!DOCTYPE html>
<html lang="<?= __("html_lang") ?>">
  <head>
    <script>
      window.dataLayer = window.dataLayer || [];
      function gtag(){dataLayer.push(arguments);}
      gtag('consent', 'default', { ad_storage: 'denied', analytics_storage: 'denied', ad_user_data: 'denied', ad_personalization: 'denied' });
    </script>
    <?php if ($config["analytics"] && $consentAnalytics) { ?>
    <script async src="https://www.googletagmanager.com/gtag/js?id=<?= htmlspecialchars($config["analyticsId"]) ?>"></script>
    <script>
      gtag('js', new Date());
      gtag('consent', 'update', { ad_storage: 'denied', analytics_storage: 'granted' });
      gtag('config', '<?= htmlspecialchars($config["analyticsId"]) ?>', { anonymize_ip: true });
    </script>
    <?php } ?>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="shortcut icon" href="/assets/images/favicon.ico">

    <title><?= $config["platformTitle"] ?></title>
    <meta name="description" content="<?= $config["platformDescription"] ?>">
    <meta property="og:title" content="<?= $config["platformTitle"]; ?>">
    <meta property="og:description" content="<?= $config["platformDescription"] ?>">
    <meta property="og:image" content="<?= $config["logo"] ?>">
    <meta property="og:image:width" content="<?= $config["logoWidth"] ?>">
    <meta property="og:image:height" content="<?= $config["logoHeight"] ?>">
    <meta property="og:site_name" content="<?= $config["platformTitle"] ?>">

    <link rel="stylesheet" href="/assets/css/app.css?v=<?= asset_version('assets/css/app.css') ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <?php if (!$isIndexPage): ?>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.7/dist/chart.umd.min.js"></script>
    <?php endif; ?>
  </head>

  <body>
    <?= ui_toast_container() ?>
    <?php require_once(__DIR__ . "/cookie-banner.php"); ?>
    <?php if ($config["maintenance"]) { ?>
      <div class="m-0 bg-amber-500 px-4 py-2 text-center text-black">
        <i class="fas fa-tools mr-2"></i><?= htmlspecialchars($config["maintenanceMessage"]) ?>
      </div>
    <?php } ?>
    <?php if (!$isIndexPage) {
      require_once(__DIR__ . "/../includes/navbar.php");
    } ?>

    <main class="ds-content<?= $isIndexPage || !empty($hidePageHeader) ? ' ds-content--full' : '' ?>">
      <?php if (!$isIndexPage && empty($hidePageHeader)) { ?>
        <header id="portfolio" class="pb-0">
          <a href="./index.php"><img src="/assets/images/logo.png" class="shape-circle LogoSmall float-right m-4 hidden w-[120px]" id="logo-small" width="451" height="109" alt="Logo"></a>
          <button type="button" id="btn-sidebar-open" class="ds-btn ds-btn--secondary ds-btn--sm" onclick="w3_open()" aria-label="Menu"><i class="fas fa-bars"></i></button>
          <?php if ($layoutPageTitle !== $config["platformTitle"] && !$isIndexPage) { ?>
            <?= ui_page_header(
              $layoutPageTitle,
              ['desc' => $layoutPageDesc, 'back_url' => $layoutPageBackUrl, 'back_label' => __("back_tooltip")]
            ) ?>
          <?php } ?>
      </header>
      <?php } ?>

      <?php
        require_once(__DIR__ . "/../includes/table-filters.php");
        require_once(__DIR__ . "/../pages/$view/$view.view.php");
      ?>
    </main>

    <?php
      $footerMarginLeft = $isIndexPage ? '0 !important' : '';
      require_once(__DIR__ . "/footer.php");
    ?>
  </body>

  <?php if (!$isIndexPage): ?>
  <script src="https://unpkg.com/@popperjs/core@2"></script>
  <script src="https://unpkg.com/tippy.js@6"></script>
  <?php endif; ?>

  <script src="/assets/js/main.js?v=<?= asset_version('assets/js/main.js') ?>"></script>
  <?php if (!$isIndexPage): ?>
  <script src="/assets/ui-kit/Toast/toast.js?v=<?= asset_version('assets/ui-kit/Toast/toast.js') ?>"></script>
  <script>
    if (window.tippy) tippy('[data-tippy-content]', { delay: [300, 200] });
  </script>
  <?= ui_tutorial_render() ?>
  <script src="/assets/ui-kit/Tutorial/tutorial.js?v=<?= asset_version('assets/ui-kit/Tutorial/tutorial.js') ?>"></script>
  <?php endif; ?>
</html>
