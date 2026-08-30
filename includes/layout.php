<?php
require_once(__DIR__ . "/../lib/csrf.php");
$pageURI = parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH);
$isIndexPage = basename($pageURI) === 'index.php' || $pageURI === '/'; // Check for index.php or root
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
    <!-- Google Analytics -->
    <?php if ($config["analytics"]) { ?>
    <script async src="https://www.googletagmanager.com/gtag/js?id=<?= $config["analyticsId"] ?>"></script>
    <script>
      window.dataLayer = window.dataLayer || [];
      function gtag(){dataLayer.push(arguments);}
      gtag('js', new Date());
      <?php if (isset($user)) { ?>gtag('set', {'user_id': <?= $user["id"] ?>});<?php } ?>
      gtag('config', '<?= $config["analyticsId"] ?>');
    </script>
    <?php } ?>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="shortcut icon" href="/assets/images/favicon.ico">

    <!-- Social meta -->
    <title><?= $config["platformTitle"] ?></title>
    <meta name="description" content="<?= $config["platformDescription"] ?>">
    <meta property="og:title" content="<?= $config["platformTitle"]; ?>">
    <meta property="og:description" content="<?= $config["platformDescription"] ?>">
    <meta property="og:image" content="<?= $config["logo"] ?>">
    <meta property="og:image:width" content="<?= $config["logoWidth"] ?>">
    <meta property="og:image:height" content="<?= $config["logoHeight"] ?>">
    <meta property="og:site_name" content="<?= $config["platformTitle"] ?>">

    <!-- Style -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Raleway">
    <link rel="stylesheet" href="/assets/css/variables.css?v=<?= asset_version('assets/css/variables.css') ?>">
    <link rel="stylesheet" href="/assets/css/style.css?v=<?= asset_version('assets/css/style.css') ?>">
    <?php if ($isIndexPage): ?>
    <link rel="stylesheet" href="/assets/css/landing.css?v=<?= asset_version('assets/css/landing.css') ?>">
    <?php else: ?>
    <link rel="stylesheet" href="/assets/css/w3codecolor.css?v=<?= asset_version('assets/css/w3codecolor.css') ?>">
    <link rel="stylesheet" href="/assets/css/documentation.css?v=<?= asset_version('assets/css/documentation.css') ?>">
    <?php endif; ?>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <?php if (!$isIndexPage): ?>
    <link rel="stylesheet" href="/assets/ui-kit/Toast/toast.css?v=<?= asset_version('assets/ui-kit/Toast/toast.css') ?>">
    <link rel="stylesheet" href="/assets/ui-kit/Tutorial/tutorial.css?v=<?= asset_version('assets/ui-kit/Tutorial/tutorial.css') ?>">
    <link rel="stylesheet" href="/assets/css/navbar.css?v=<?= asset_version('assets/css/navbar.css') ?>">
    <link rel="stylesheet" href="/assets/css/internal-pages.css?v=<?= asset_version('assets/css/internal-pages.css') ?>">
    <?php endif; ?>
    <?php if (!$isIndexPage): ?>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.7/dist/chart.umd.min.js"></script>
    <?php endif; ?>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
      tailwind.config = {
        corePlugins: { preflight: false },
        darkMode: 'class',
        theme: {
          extend: {
            colors: {
              primary: {
                50: '#eff6ff', 100: '#dbeafe', 200: '#bfdbfe', 300: '#93c5fd',
                400: '#60a5fa', 500: '#3b82f6', 600: '#2563eb', 700: '#1d4ed8',
                800: '#1e40af', 900: '#1e3a8a',
              },
              surface: {
                DEFAULT: 'var(--bg-color)',
                sidebar: 'var(--bg-color-sidebar)',
                reversed: 'var(--bg-color--reversed)',
                card: 'var(--bg-color-card)',
                offset: 'var(--bg-color-offset)',
                'offset-hover': 'var(--bg-color-offset-hover)',
                code: 'var(--bg-color-code)',
                'sidebar-footer': 'var(--bg-color-sidebar-footer)',
                'section-alt': 'var(--section-alt-bg)',
              },
              text: {
                DEFAULT: 'var(--text-color)',
                reversed: 'var(--text-color--reversed)',
                headings: 'var(--text-color-headings)',
                secondary: 'var(--text-color-secondary)',
                primary: 'var(--text-color-primary)',
                code: 'var(--text-color-code)',
                'sidebar-link': 'var(--text-color-sidebar-link)',
                'sidebar-link-hover': 'var(--text-color-sidebar-link-hover)',
              },
              input: {
                bg: 'var(--input-bg)',
                'bg-disabled': 'var(--input-bg--disabled)',
                text: 'var(--input-text)',
                'text-disabled': 'var(--input-text--disabled)',
              },
              table: {
                border: 'var(--table-border-color)',
                'header-bg': 'var(--table-header-bg)',
                'header-text': 'var(--table-header-text-color)',
                'row-even': 'var(--table-row-even-bg)',
                'row-hover': 'var(--table-row-hover-bg)',
                'cell-text': 'var(--table-cell-text-color)',
                'action-icon': 'var(--table-action-icon-color)',
                'action-icon-hover': 'var(--table-action-icon-hover-color)',
                'action-icon-hover-bg': 'var(--table-action-icon-hover-bg)',
                line: 'var(--table-line-color)',
              },
              button: {
                bg: 'var(--button-bg)',
                'bg-hover': 'var(--button-bg--hover)',
                text: 'var(--button-text-color)',
              },
              pagination: {
                'hover-bg': 'var(--pagination-hover-bg)',
                'hover-text': 'var(--pagination-hover-text)',
                'active-bg': 'var(--pagination-active-bg)',
                'active-text': 'var(--pagination-active-text)',
                'disabled-bg': 'var(--pagination-disabled-bg)',
                'disabled-text': 'var(--pagination-disabled-text)',
              },
              navbar: {
                bg: 'var(--navbar-bg)',
                border: 'var(--navbar-border-color)',
                text: 'var(--navbar-text-color)',
                'logo-border': 'var(--navbar-logo-border-color)',
                link: 'var(--navbar-link-color)',
                'link-hover-bg': 'var(--navbar-link-hover-bg)',
                'link-hover-border': 'var(--navbar-link-hover-border-color)',
                'link-hover': 'var(--navbar-link-hover-color)',
                'link-active-bg': 'var(--navbar-link-active-bg)',
                'link-active': 'var(--navbar-link-active-color)',
                'button-bg': 'var(--navbar-button-bg)',
                'button-text': 'var(--navbar-button-text-color)',
                'button-hover-bg': 'var(--navbar-button-hover-bg)',
                'button-hover-text': 'var(--navbar-button-hover-text-color)',
              },
              'border-color': {
                DEFAULT: 'var(--border-color)',
                sidebar: 'var(--border-color-sidebar)',
                soft: 'var(--border-color)',
              },
              'primary-color': {
                DEFAULT: 'var(--primary-color)',
                light: 'var(--primary-color-light)',
                dark: 'var(--primary-color-dark)',
                darker: 'var(--primary-color-darker)',
              },
              accent: {
                DEFAULT: 'var(--accent-color)',
                hover: 'var(--accent-color-hover)',
              },
              secondary: 'var(--accent-color)',
              info: {
                'panel-bg': 'var(--info-panel-bg)',
                'panel-text': 'var(--info-panel-text)',
                'panel-border': 'var(--info-panel-border)',
              },
              cookie: {
                'banner-bg': 'var(--cookie-banner-bg)',
                'banner-text': 'var(--cookie-banner-text-color)',
                'banner-link': 'var(--cookie-banner-link-color)',
                'banner-link-hover': 'var(--cookie-banner-link-hover-color)',
              },
              toggle: {
                bg: 'var(--toggle-bg)',
                'bg-checked': 'var(--toggle-bg--checked)',
                'knob-bg': 'var(--toggle-knob-bg)',
              },
              'code-syntax': {
                DEFAULT: 'var(--code-syntax-default)',
                keyword: 'var(--code-syntax-keyword)',
                string: 'var(--code-syntax-string)',
                number: 'var(--code-syntax-number)',
                property: 'var(--code-syntax-property)',
                comment: 'var(--code-syntax-comment)',
                regexp: 'var(--code-syntax-regexp)',
                stringtemp: 'var(--code-syntax-stringtemp)',
              },
              cta: {
                'button-bg': 'var(--cta-button-bg)',
                'button-text': 'var(--cta-button-text)',
              },
              footer: {
                bg: 'var(--footer-bg)',
                text: 'var(--footer-text-color)',
                border: 'var(--footer-border-color)',
                heading: 'var(--footer-heading-color)',
                link: 'var(--footer-link-color)',
                'link-hover': 'var(--footer-link-hover-color)',
                'social-icon': 'var(--footer-link-color)',
                'social-icon-hover': 'var(--footer-link-hover-color)',
              },
              gradient: {
                start: 'var(--gradient-start)',
                mid: 'var(--gradient-mid)',
                end: 'var(--gradient-end)',
              },
              glass: {
                bg: 'var(--glass-bg)',
                border: 'var(--glass-border)',
                'border-hover': 'var(--glass-border-hover)',
              },
              glow: 'var(--glow-color)',
              header: {
                bg: 'var(--header-bg)',
                border: 'var(--header-border)',
              },
              'nav-hover': 'var(--nav-hover-bg)',
              overlay: {
                1: 'rgb(var(--overlay-color-1))',
                2: 'rgb(var(--overlay-color-2))',
                3: 'rgb(var(--overlay-color-3))',
              },
              scrollbar: {
                thumb: 'var(--scrollbar-thumb)',
                'thumb-hover': 'var(--scrollbar-thumb-hover)',
              },
              divider: 'var(--divider-fill)',
              'progress-bar': 'var(--progress-bar-bg)',
              'mesh-dot': 'var(--mesh-dot)',
              'card-bg-hover': 'var(--card-bg-hover)',
              hr: 'var(--hr-color)',
            },
            boxShadow: {
              'card-right': 'var(--shadow-1--right)',
              'card-lg': 'var(--shadow-2)',
              card: 'var(--shadow-1)',
              navbar: 'var(--shadow-navbar)',
              'card-prominent': 'var(--shadow-2-prominent)',
              'card-subtle': 'var(--shadow-1-subtle)',
              footer: 'var(--shadow-footer)',
            },
            backgroundImage: {
              cta: 'var(--gradient-cta)',
            },
            keyframes: {
              cookieBannerIn: {
                from: { transform: 'translateY(100%)', opacity: '0' },
                to: { transform: 'translateY(0)', opacity: '1' },
              },
              skeletonShimmer: {
                from: { backgroundPosition: '-200% 0' },
                to: { backgroundPosition: '200% 0' },
              },
              modalIn: {
                from: { opacity: '0', transform: 'translateY(24px) scale(.97)' },
                to: { opacity: '1', transform: 'translateY(0) scale(1)' },
              },
              confirmPulse: {
                '0%, 100%': { transform: 'scale(1)' },
                '50%': { transform: 'scale(1.06)' },
              },
              tabsIn: {
                from: { opacity: '0', transform: 'translateY(6px)' },
                to: { opacity: '1', transform: 'translateY(0)' },
              },
            },
            animation: {
              'cookie-banner-in': 'cookieBannerIn 0.5s ease-out forwards',
              'skeleton-shimmer': 'skeletonShimmer 1.8s ease-in-out infinite',
              'modal-in': 'modalIn .3s cubic-bezier(.16,1,.3,1)',
              'confirm-pulse': 'confirmPulse 1s ease-in-out infinite',
              'tabs-in': 'tabsIn 0.25s ease',
            },
          },
        },
      }
    </script>
  </head>

  <body<?= $theme === 'dark' ? ' class="dark"' : '' ?>>
    <?= ui_toast_container() ?>
    <div id="cookie-banner" class="fixed inset-x-0 bottom-0 z-[90000] hidden translate-y-full items-center justify-between gap-5 border-0 border-t-[3px] border-solid border-primary-color bg-cookie-banner-bg px-5 py-5 text-center text-cookie-banner-text opacity-0 shadow-[0_-2px_15px_rgba(0,0,0,0.2)] animate-cookie-banner-in sm:px-[30px] [&_a]:font-medium [&_a]:text-cookie-banner-link [&_a]:underline hover:[&_a]:text-cookie-banner-link-hover [&_p]:m-0 [&_p]:text-[0.95rem] [&_p]:leading-relaxed">
      <div class="flex grow items-center justify-center gap-[15px]">
          <div>
            <p><?= __("cookie_banner_text") ?> <a href="cookie.php"><?= __("cookie_banner_link") ?></a></p>
          </div>
        </div>
        <button id="accept-cookie-banner" class="ml-5 shrink-0 cursor-pointer whitespace-nowrap rounded-md border-0 bg-primary-color px-6 py-2.5 font-semibold text-white transition duration-200 hover:-translate-y-0.5 hover:bg-primary-color-dark"><?= __("cookie_banner_accept") ?></button>
    </div>
    <?php if ($config["maintenance"]) { ?>
      <div class="m-0 bg-amber-500 px-4 py-2 text-center text-black">
        <i class="fas fa-tools mr-2"></i><?= htmlspecialchars($config["maintenanceMessage"]) ?>
      </div>
    <?php } ?>
    <?php if (!$isIndexPage) { // Conditionally include navbar
      require_once(__DIR__ . "/../includes/navbar.php"); 
    } ?>

    <main class="PageContent mt-0 flex-[1_0_auto] px-4 transition-[margin-left] duration-300 lg:ml-[260px] lg:px-20 [&_h1]:mt-6 [&_.ui-page-header_h1]:!m-0 [&_.ui-table-container]:mb-5 [&_.ui-table-container]:overflow-x-auto [&_.ui-table]:w-full [&_.ui-table]:border-collapse [&_.ui-table]:overflow-hidden [&_.ui-table]:rounded-lg [&_.ui-table]:text-[0.95em] [&_.ui-table]:shadow-[0_2px_15px_rgba(0,0,0,0.1)] [&_.ui-table-header]:bg-table-header-bg [&_.ui-table-header]:font-semibold [&_.ui-table-header]:uppercase [&_.ui-table-header]:tracking-[0.03em] [&_.ui-table-header]:text-table-header-text [&_.ui-table-header_a]:text-inherit [&_.ui-table-header_a]:no-underline hover:[&_.ui-table-header_a]:underline [&_.ui-table-header-cell]:border-0 [&_.ui-table-header-cell]:border-b [&_.ui-table-header-cell]:border-solid [&_.ui-table-header-cell]:border-table-border [&_.ui-table-header-cell]:px-3 [&_.ui-table-header-cell]:py-2.5 [&_.ui-table-header-cell]:text-left [&_.ui-table-header-cell:last-child]:text-right [&_.ui-table-body]:bg-surface-card [&_.ui-table-row:nth-child(even)]:bg-table-row-even [&_.ui-table-row:hover]:bg-table-row-hover [&_.ui-table-cell]:border-0 [&_.ui-table-cell]:border-b [&_.ui-table-cell]:border-solid [&_.ui-table-cell]:border-table-border [&_.ui-table-cell]:px-3 [&_.ui-table-cell]:py-2.5 [&_.ui-table-cell]:text-table-cell-text [&_.ui-table-cell:last-child]:whitespace-nowrap [&_.ui-table-cell:last-child]:text-right [&_.ui-table-empty-row]:text-center [&_.ui-table-empty-row]:italic [&_.ui-table-empty-row]:text-text-secondary<?= $isIndexPage || !empty($hidePageHeader) ? ' !ml-0 !p-0' : '' ?>">
      <!-- Header -->
      <?php if (!$isIndexPage && empty($hidePageHeader)) { ?>
        <header id="portfolio" class="pb-0">
          <!-- Small logo shown on small screens -->
          <a href="./index.php"><img src="/assets/images/logo<?= $theme === 'dark' ? 'White' : '' ?>.png" class="shape-circle LogoSmall float-right m-4 hidden w-[120px]" id="logo-small" width="451" height="109" alt="Logo"></a>

          <!-- Close sidebar button -->
          <span id="btn-sidebar-open" class="hidden text-[32px] cursor-pointer px-4 py-2" onclick="w3_open()"><i class="fas fa-bars"></i></span>

          <!-- Page header -->
          <?php if ($layoutPageTitle !== $config["platformTitle"] && !$isIndexPage) { ?>
            <?= ui_page_header(
              $layoutPageTitle,
              [
                'desc' => $layoutPageDesc,
                'back_url' => $layoutPageBackUrl,
                'back_label' => __("back_tooltip"),
              ]
            ) ?>
          <?php } ?>
      </header>
      <?php } ?>

      <!-- Page content -->
      <?php
        // Make table filters available to all views
        require_once(__DIR__ . "/../includes/table-filters.php");
        require_once(__DIR__ . "/../pages/$view/$view.view.php");
      ?>
    </main>

    <!-- Footer -->
    <?php
      $footerMarginLeft = $isIndexPage ? '0 !important' : '';
      require_once(__DIR__ . "/footer.php");
    ?>
  </body>

  <!-- JS -->
  <?php if (!$isIndexPage): ?>
  <script src="https://unpkg.com/@popperjs/core@2"></script>
  <script src="https://unpkg.com/tippy.js@6"></script>
  <?php endif; ?>

  <script src="/assets/js/main.js?v=<?= asset_version('assets/js/main.js') ?>" async></script>
  <?php if (!$isIndexPage): ?>
  <script src="/assets/ui-kit/Toast/toast.js?v=<?= asset_version('assets/ui-kit/Toast/toast.js') ?>"></script>
  <script>
    // Initialize the tooltips
    tippy('[data-tippy-content]', { delay: [300, 200] });
  </script>

  <!-- Tutorial module -->
  <?= ui_tutorial_render() ?>
  <script src="/assets/ui-kit/Tutorial/tutorial.js?v=<?= asset_version('assets/ui-kit/Tutorial/tutorial.js') ?>"></script>
  <?php endif; ?>
</html>
