<header class="ds-landing-header" role="banner">
  <div class="ds-landing-header__inner">
    <a href="./index.php" title="GMI Cloud - Home" class="ds-landing-header__logo">
      <img src="/assets/images/logo.png" alt="GMI Cloud">
    </a>

    <!-- Desktop navigation -->
    <nav class="ds-landing-header__nav-desktop">
      <ul class="ds-landing-header__nav-list">
        <li><a href="#come-funziona" class="ds-landing-header__link"><?= __('index_nav_how') ?></a></li>
        <li><a href="#servizi" class="ds-landing-header__link"><?= __('index_nav_services') ?></a></li>
        <li><a href="#caratteristiche" class="ds-landing-header__link"><?= __('index_nav_features') ?></a></li>
        <li><a href="#numeri" class="ds-landing-header__link"><?= __('index_nav_numbers') ?></a></li>
        <li><a href="#faq" class="ds-landing-header__link"><?= __('index_nav_faq') ?></a></li>
        <li>
          <?php if (isset($user)) { ?>
            <div class="ds-landing-header__user">
              <a href="./home.php" class="ds-landing-header__user-link">
                <span class="ds-landing-header__avatar"><i class="fab fa-discord"></i></span>
                <span class="ds-landing-header__user-name"><?= htmlspecialchars($user["username"]) ?></span>
              </a>
              <a href="logout.php" class="ds-landing-header__logout"><?= __('nav_logout') ?></a>
            </div>
          <?php } else { ?>
            <a href="login.php" class="ds-btn ds-btn--secondary ds-btn--md"><?= __('nav_login') ?></a>
          <?php } ?>
        </li>
      </ul>
    </nav>

    <!-- Mobile hamburger -->
    <button type="button" class="ds-landing-header__hamburger" id="landing-menu-toggle" aria-label="Apri menu" aria-expanded="false" aria-controls="landing-main-nav">
      <span class="ds-landing-header__bar"></span>
      <span class="ds-landing-header__bar"></span>
      <span class="ds-landing-header__bar"></span>
    </button>

    <!-- Mobile navigation -->
    <nav id="landing-main-nav" class="ds-landing-header__mobile-nav" aria-hidden="true">
      <ul class="ds-landing-header__mobile-list">
        <li><a href="#come-funziona" class="ds-landing-header__link" onclick="closeLandingMenu()"><?= __('index_nav_how') ?></a></li>
        <li><a href="#servizi" class="ds-landing-header__link" onclick="closeLandingMenu()"><?= __('index_nav_services') ?></a></li>
        <li><a href="#caratteristiche" class="ds-landing-header__link" onclick="closeLandingMenu()"><?= __('index_nav_features') ?></a></li>
        <li><a href="#numeri" class="ds-landing-header__link" onclick="closeLandingMenu()"><?= __('index_nav_numbers') ?></a></li>
        <li><a href="#faq" class="ds-landing-header__link" onclick="closeLandingMenu()"><?= __('index_nav_faq') ?></a></li>
        <li class="ds-landing-header__mobile-user">
          <?php if (isset($user)) { ?>
            <div class="ds-landing-header__user">
              <a href="./home.php" class="ds-landing-header__user-link" onclick="closeLandingMenu()">
                <span class="ds-landing-header__avatar"><i class="fab fa-discord"></i></span>
                <span class="ds-landing-header__user-name"><?= htmlspecialchars($user["username"]) ?></span>
              </a>
              <a href="logout.php" class="ds-landing-header__logout" onclick="closeLandingMenu()"><?= __('nav_logout') ?></a>
            </div>
          <?php } else { ?>
            <a href="login.php" class="ds-btn ds-btn--secondary ds-btn--md ds-btn--full" onclick="closeLandingMenu()"><?= __('nav_login') ?></a>
          <?php } ?>
        </li>
      </ul>
    </nav>
  </div>
</header>
<script>
(function () {
  var btn = document.getElementById('landing-menu-toggle');
  var nav = document.getElementById('landing-main-nav');
  if (!btn || !nav) return;
  function setOpen(open) {
    btn.setAttribute('aria-expanded', open ? 'true' : 'false');
    btn.setAttribute('aria-label', open ? 'Chiudi menu' : 'Apri menu');
    nav.classList.toggle('is-open', open);
    document.body.style.overflow = open ? 'hidden' : '';
  }
  btn.addEventListener('click', function () { setOpen(!nav.classList.contains('is-open')); });
  window.closeLandingMenu = function () { setOpen(false); };
  document.addEventListener('keydown', function (e) { if (e.key === 'Escape') setOpen(false); });
})();
</script>

<div class="ds-hero">
  <div class="ds-hero__inner">
    <div style="min-width:0;flex:1.2">
      <!-- <img src="/assets/images/logo.png" alt="" style="height:2.25rem;width:auto;margin-bottom:1rem;filter:brightness(0) invert(1)" width="451" height="109"> -->
      <h1><?= __('index_hero_title1') ?><br><?= __('index_hero_title2') ?></h1>
      <p><?= __('index_hero_subtitle') ?></p>
      <div class="ds-hero__actions">
        <a href="<?= isset($user) ? './home.php' : './add-game.php' ?>" class="ds-btn ds-btn--light ds-btn--lg"><?= __('index_hero_cta') ?></a>
        <a href="./documentation.php" class="ds-btn ds-btn--outline-light ds-btn--lg"><?= __('index_hero_docs') ?></a>
      </div>
    </div>
    <div class="ds-hero__visual" style="flex:1">
      <img src="/assets/images/hero-image.png" alt="<?= __('index_hero_title2') ?>" width="1402" height="1122" style="border-radius: 10px">
    </div>
  </div>
</div>

<div id="come-funziona" class="ds-section">
  <div class="ds-container">
    <h2 class="ds-section-title"><?= __('index_how_title') ?></h2>
    <p class="ds-section-subtitle"><?= __('index_how_subtitle') ?></p>
    <div class="ds-grid-4">
      <div class="ds-editorial-card"><div class="ds-editorial-icon"><i class="fas fa-gamepad"></i></div><h3><?= __('index_step1_title') ?></h3><p><?= __('index_step1_desc') ?></p></div>
      <div class="ds-editorial-card"><div class="ds-editorial-icon"><i class="fas fa-puzzle-piece"></i></div><h3><?= __('index_step2_title') ?></h3><p><?= __('index_step2_desc') ?></p></div>
      <div class="ds-editorial-card"><div class="ds-editorial-icon"><i class="fas fa-cloud"></i></div><h3><?= __('index_step3_title') ?></h3><p><?= __('index_step3_desc') ?></p></div>
      <div class="ds-editorial-card"><div class="ds-editorial-icon"><i class="fas fa-chart-bar"></i></div><h3><?= __('index_step4_title') ?></h3><p><?= __('index_step4_desc') ?></p></div>
    </div>
  </div>
</div>

<div id="caratteristiche" class="ds-section">
  <div class="ds-container">
    <h2 class="ds-section-title"><?= __('index_features_title1') ?> <?= __('index_features_title2') ?></h2>
    <p class="ds-section-subtitle"><?= __('index_features_subtitle') ?></p>
    <div class="ds-grid-4">
      <div class="ds-editorial-card md:text-left"><div class="ds-editorial-icon md:mx-0"><i class="fas fa-cogs"></i></div><h3><?= __('index_feature1_title') ?></h3><p><?= __('index_feature1_desc') ?></p></div>
      <div class="ds-editorial-card md:text-left"><div class="ds-editorial-icon md:mx-0"><i class="fas fa-expand-arrows-alt"></i></div><h3><?= __('index_feature2_title') ?></h3><p><?= __('index_feature2_desc') ?></p></div>
      <div class="ds-editorial-card md:text-left"><div class="ds-editorial-icon md:mx-0"><i class="fas fa-shield-alt"></i></div><h3><?= __('index_feature3_title') ?></h3><p><?= __('index_feature3_desc') ?></p></div>
      <div class="ds-editorial-card md:text-left"><div class="ds-editorial-icon md:mx-0"><i class="fas fa-server"></i></div><h3><?= __('index_feature4_title') ?></h3><p><?= __('index_feature4_desc') ?></p></div>
      <div class="ds-editorial-card md:text-left"><div class="ds-editorial-icon md:mx-0"><i class="fas fa-tachometer-alt"></i></div><h3><?= __('index_feature5_title') ?></h3><p><?= __('index_feature5_desc') ?></p></div>
      <div class="ds-editorial-card md:text-left"><div class="ds-editorial-icon md:mx-0"><i class="fas fa-users"></i></div><h3><?= __('index_feature6_title') ?></h3><p><?= __('index_feature6_desc') ?></p></div>
      <div class="ds-editorial-card md:text-left"><div class="ds-editorial-icon md:mx-0"><i class="fab fa-github"></i></div><h3><?= __('index_feature7_title') ?></h3><p><?= __('index_feature7_desc1') ?> <a class="ds-link" href="https://github.com/manuel-di-iorio/gmicloud"><?= __('index_feature7_github') ?></a>. <?= __('index_feature7_desc2') ?></p></div>
      <div class="ds-editorial-card md:text-left"><div class="ds-editorial-icon md:mx-0"><i class="fas fa-rocket"></i></div><h3><?= __('index_feature8_title') ?></h3><p><?= __('index_feature8_desc') ?></p></div>
    </div>
  </div>
</div>

<div id="servizi" class="ds-section">
  <div class="ds-container">
    <h2 class="ds-section-title"><?= __('index_services_title') ?></h2>
    <p class="ds-section-subtitle"><?= __('index_services_subtitle') ?></p>
    <div class="ds-grid-4">
      <div class="ds-editorial-card"><div class="ds-editorial-icon"><i class="fas fa-trophy"></i></div><h3><?= __('index_service1_title') ?></h3><p><?= __('index_service1_desc') ?></p></div>
      <div class="ds-editorial-card"><div class="ds-editorial-icon"><i class="fas fa-chart-pie"></i></div><h3><?= __('index_service2_title') ?></h3><p><?= __('index_service2_desc') ?></p></div>
      <div class="ds-editorial-card"><div class="ds-editorial-icon"><i class="fas fa-user-shield"></i></div><h3><?= __('index_service3_title') ?></h3><p><?= __('index_service3_desc') ?></p></div>
      <div class="ds-editorial-card"><div class="ds-editorial-icon"><i class="fas fa-cloud-upload-alt"></i></div><h3><?= __('index_service4_title') ?></h3><p><?= __('index_service4_desc') ?></p></div>
    </div>
  </div>
</div>

<div id="numeri" class="ds-section" style="background:var(--ds-color-surface);border-top:1px solid var(--ds-color-border);border-bottom:1px solid var(--ds-color-border)">
  <div class="ds-container">
    <h2 class="ds-section-title"><?= __('index_stats_title1') ?> <?= __('index_stats_title2') ?></h2>
    <p class="ds-section-subtitle"><?= __('index_stats_subtitle') ?></p>
    <div class="ds-grid-4" style="grid-template-columns:repeat(2,minmax(0,1fr))">
      <?php
        $statIcons = [
          "scores" => "fas fa-star", "players" => "fas fa-users", "games" => "fas fa-gamepad",
          "active-games" => "fas fa-bolt", "unique-scores-countries" => "fas fa-globe-americas", "users" => "fas fa-code"
        ];
      ?>
      <?php foreach ($stats as $key => $stat) { ?>
      <div class="ds-stat">
        <div class="ds-stat__icon"><i class="<?= $statIcons[$key] ?? 'fas fa-chart-bar' ?>"></i></div>
        <div><div class="ds-stat__value"><?= htmlspecialchars($stat["count"]) ?></div><div class="ds-stat__label"><?= htmlspecialchars($stat["label"]) ?></div></div>
      </div>
      <?php } ?>
    </div>
  </div>
</div>

<div id="faq" class="ds-section">
  <div class="ds-container" style="display:grid;grid-template-columns:1fr;gap:2.5rem">
    <div>
      <h2 class="ds-section-title ds-section-title--left"><?= __('index_faq_title1') ?> <?= __('index_faq_title2') ?></h2>
      <div>
        <div class="ds-faq__item"><button class="ds-faq__q"><span><?= __('index_faq1_q') ?></span><span class="ds-faq__icon"><i class="fas fa-plus"></i></span></button><div class="ds-faq__a"><div class="ds-faq__a-inner"><?= __('index_faq1_a') ?></div></div></div>
        <div class="ds-faq__item"><button class="ds-faq__q"><span><?= __('index_faq2_q') ?></span><span class="ds-faq__icon"><i class="fas fa-plus"></i></span></button><div class="ds-faq__a"><div class="ds-faq__a-inner"><?= __('index_faq2_a') ?></div></div></div>
        <div class="ds-faq__item"><button class="ds-faq__q"><span><?= __('index_faq3_q') ?></span><span class="ds-faq__icon"><i class="fas fa-plus"></i></span></button><div class="ds-faq__a"><div class="ds-faq__a-inner"><?= __('index_faq3_a') ?></div></div></div>
        <div class="ds-faq__item"><button class="ds-faq__q"><span><?= __('index_faq4_q') ?></span><span class="ds-faq__icon"><i class="fas fa-plus"></i></span></button><div class="ds-faq__a"><div class="ds-faq__a-inner"><?= __('index_faq4_a') ?></div></div></div>
      </div>
    </div>
    <div class="ds-cta-card">
      <h3 style="margin:0 0 1rem 0;font-size:1.5rem;font-weight:500"><?= __('index_cta_title1') ?> <?= __('index_cta_title2') ?></h3>
      <p class="ds-muted" style="margin:0 0 1.75rem 0;line-height:1.6"><?= __('index_cta_desc') ?></p>
      <a href="<?= isset($user) ? './home.php' : './add-game.php' ?>" class="ds-btn ds-btn--primary ds-btn--lg"><?= __('index_cta_button') ?> <i class="fas fa-arrow-right"></i></a>
    </div>
  </div>
</div>
