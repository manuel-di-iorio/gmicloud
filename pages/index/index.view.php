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
  <canvas id="hero-game" class="ds-hero__game" aria-hidden="true"></canvas>
  <div class="ds-hero__scrim" aria-hidden="true"></div>
  <div class="ds-hero__leaderboard" id="hero-leaderboard" aria-hidden="true">
    <div class="ds-hero__leaderboard-title"><?= __('hero_game_top_scores') ?></div>
    <table class="ds-hero__leaderboard-table">
      <thead>
        <tr><th scope="col">#</th><th scope="col"><?= __('hero_game_th_player') ?></th><th scope="col"><?= __('hero_game_th_pts') ?></th><th scope="col"><?= __('hero_game_th_time') ?></th></tr>
      </thead>
      <tbody id="hero-leaderboard-body"></tbody>
    </table>
  </div>
  <div class="ds-hero__hud"><span id="hero-time" aria-hidden="true">0.00s</span><span id="hero-gems" aria-hidden="true">&#9670; 0/13</span><span id="hero-best" aria-hidden="true"></span><button type="button" class="ds-hero__sound" id="hero-sound" aria-label="<?= htmlspecialchars(__('hero_game_music_toggle'), ENT_QUOTES, 'UTF-8') ?>" aria-pressed="false">&#9834;</button></div>
  <div class="ds-hero__progress" aria-hidden="true"><i id="hero-progress-fill"></i></div>
  <div class="ds-hero__hint" id="hero-hint" aria-hidden="true"><?= __('hero_game_hint_play') ?></div>
  <div class="ds-hero__recap" id="hero-recap" aria-hidden="true">
    <div class="ds-hero__recap-card">
      <div class="ds-hero__recap-title"><?= __('hero_game_recap_title') ?></div>
      <div class="ds-hero__recap-row"><span><?= __('hero_game_th_time') ?></span><strong id="recap-time"></strong></div>
      <div class="ds-hero__recap-row"><span><?= __('hero_game_recap_gems') ?></span><strong id="recap-gems"></strong></div>
      <div class="ds-hero__recap-row total"><span><?= __('hero_game_recap_points') ?></span><strong id="recap-points"></strong></div>
      <div class="ds-hero__recap-best" id="recap-best"></div>
      <form class="ds-hero__recap-form" id="hero-score-form" autocomplete="off" inert>
        <label class="ds-hero__recap-label" for="hero-player-name"><?= __('hero_game_player_name') ?></label>
        <div class="ds-hero__recap-controls">
          <input type="text" id="hero-player-name" name="player" maxlength="64" placeholder="<?= htmlspecialchars(__('hero_game_your_name'), ENT_QUOTES, 'UTF-8') ?>" autocomplete="nickname" spellcheck="false">
          <button type="submit" id="hero-score-save"><?= __('hero_game_save_score') ?></button>
        </div>
        <div class="ds-hero__recap-status" id="hero-score-status" role="status"></div>
      </form>
      <div class="ds-hero__recap-again"><?= __('hero_game_continue') ?></div>
    </div>
  </div>
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
      <img src="/assets/images/hero-image.png" alt="<?= __('index_hero_title2') ?>" width="1402" height="1122">
    </div>
  </div>
</div>

<div id="come-funziona" class="ds-section">
  <div class="ds-container">
    <h2 class="ds-section-title ds-section-title--left"><?= __('index_how_title') ?></h2>
    <p class="ds-section-subtitle ds-section-subtitle--left"><?= __('index_how_subtitle') ?></p>
    <div class="ds-grid-4">
      <?php
      $howSteps = [
        ['icon' => 'fas fa-gamepad', 'title' => __('index_step1_title'), 'desc' => __('index_step1_desc')],
        ['icon' => 'fas fa-puzzle-piece', 'title' => __('index_step2_title'), 'desc' => __('index_step2_desc')],
        ['icon' => 'fas fa-cloud', 'title' => __('index_step3_title'), 'desc' => __('index_step3_desc')],
        ['icon' => 'fas fa-chart-bar', 'title' => __('index_step4_title'), 'desc' => __('index_step4_desc')],
      ];
      foreach ($howSteps as $step) {
        echo ui_card(
          '<h3>' . htmlspecialchars($step['title']) . '</h3><p>' . htmlspecialchars($step['desc']) . '</p>',
          ['media' => '<div class="ds-card__media--icon"><i class="' . $step['icon'] . '" aria-hidden="true"></i></div>']
        );
      }
      ?>
    </div>
  </div>
</div>

<div id="caratteristiche" class="ds-section">
  <div class="ds-container">
    <h2 class="ds-section-title ds-section-title--left"><?= __('index_features_title1') ?> <?= __('index_features_title2') ?></h2>
    <p class="ds-section-subtitle ds-section-subtitle--left"><?= __('index_features_subtitle') ?></p>
    <div class="ds-grid-4">
      <?php
      $featureCards = [
        ['icon' => 'fas fa-cogs', 'title' => __('index_feature1_title'), 'desc' => __('index_feature1_desc')],
        ['icon' => 'fas fa-expand-arrows-alt', 'title' => __('index_feature2_title'), 'desc' => __('index_feature2_desc')],
        ['icon' => 'fas fa-shield-alt', 'title' => __('index_feature3_title'), 'desc' => __('index_feature3_desc')],
        ['icon' => 'fas fa-server', 'title' => __('index_feature4_title'), 'desc' => __('index_feature4_desc')],
        ['icon' => 'fas fa-tachometer-alt', 'title' => __('index_feature5_title'), 'desc' => __('index_feature5_desc')],
        ['icon' => 'fas fa-users', 'title' => __('index_feature6_title'), 'desc' => __('index_feature6_desc')],
        ['icon' => 'fab fa-github', 'title' => __('index_feature7_title'), 'desc' => __('index_feature7_desc1') . ' <a class="ds-link" href="https://github.com/manuel-di-iorio/gmicloud">' . __('index_feature7_github') . '</a>. ' . __('index_feature7_desc2')],
        ['icon' => 'fas fa-rocket', 'title' => __('index_feature8_title'), 'desc' => __('index_feature8_desc')],
      ];
      foreach ($featureCards as $feature) {
        echo ui_card(
          '<h3>' . htmlspecialchars($feature['title']) . '</h3><p>' . $feature['desc'] . '</p>',
          ['media' => '<div class="ds-card__media--icon"><i class="' . $feature['icon'] . '" aria-hidden="true"></i></div>']
        );
      }
      ?>
    </div>
  </div>
</div>

<div id="servizi" class="ds-section">
  <div class="ds-container">
    <h2 class="ds-section-title ds-section-title--left"><?= __('index_services_title') ?></h2>
    <p class="ds-section-subtitle ds-section-subtitle--left"><?= __('index_services_subtitle') ?></p>
    <div class="ds-grid-4">
      <?php
      $serviceCards = [
        ['icon' => 'fas fa-trophy', 'title' => __('index_service1_title'), 'desc' => __('index_service1_desc')],
        ['icon' => 'fas fa-chart-pie', 'title' => __('index_service2_title'), 'desc' => __('index_service2_desc')],
        ['icon' => 'fas fa-user-shield', 'title' => __('index_service3_title'), 'desc' => __('index_service3_desc')],
        ['icon' => 'fas fa-cloud-upload-alt', 'title' => __('index_service4_title'), 'desc' => __('index_service4_desc')],
      ];
      foreach ($serviceCards as $service) {
        echo ui_card(
          '<h3>' . htmlspecialchars($service['title']) . '</h3><p>' . htmlspecialchars($service['desc']) . '</p>',
          ['media' => '<div class="ds-card__media--icon"><i class="' . $service['icon'] . '" aria-hidden="true"></i></div>']
        );
      }
      ?>
    </div>
  </div>
</div>

<div id="numeri" class="ds-section">
  <div class="ds-container">
    <h2 class="ds-section-title ds-section-title--left"><?= __('index_stats_title1') ?> <?= __('index_stats_title2') ?></h2>
    <p class="ds-section-subtitle ds-section-subtitle--left"><?= __('index_stats_subtitle') ?></p>
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
        <?= ui_accordion([
          ['title' => __('index_faq1_q'), 'content' => __('index_faq1_a')],
          ['title' => __('index_faq2_q'), 'content' => __('index_faq2_a')],
          ['title' => __('index_faq3_q'), 'content' => __('index_faq3_a')],
          ['title' => __('index_faq4_q'), 'content' => __('index_faq4_a')],
        ]) ?>
      </div>
    </div>
    <div class="ds-cta-card">
      <h3 style="margin:0 0 1rem 0;font-size:1.5rem;font-weight:500"><?= __('index_cta_title1') ?> <?= __('index_cta_title2') ?></h3>
      <p class="ds-muted" style="margin:0 0 1.75rem 0;line-height:1.6"><?= __('index_cta_desc') ?></p>
      <a href="<?= isset($user) ? './home.php' : './add-game.php' ?>" class="ds-btn ds-btn--primary ds-btn--lg"><?= __('index_cta_button') ?> <i class="fas fa-arrow-right"></i></a>
    </div>
  </div>
</div>
<script src="https://cdnjs.cloudflare.com/ajax/libs/three.js/r128/three.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/three@0.128.0/examples/js/loaders/GLTFLoader.js"></script>
<script>
window.HERO_GAME_CONFIG = <?= json_encode([
  'maxGems'   => HERO_GAME_DIAMONDS,
  'minTime'   => round(hero_game_min_time(), 3),
  'maxScore'  => hero_game_max_points(),
  'scoresUrl' => '/hero-scores.php',
  'i18n'      => [
    'hero_game_hint_play'   => __('hero_game_hint_play'),
    'hero_game_hint_jump'   => __('hero_game_hint_jump'),
    'hero_game_hint_retry'  => __('hero_game_hint_retry'),
    'hero_game_hud_best'    => __('hero_game_hud_best'),
    'hero_game_no_scores'   => __('hero_game_no_scores'),
    'hero_game_enter_name'  => __('hero_game_enter_name'),
    'hero_game_invalid_name' => __('hero_game_invalid_name'),
    'hero_game_saving'      => __('hero_game_saving'),
    'hero_game_save_error'  => __('hero_game_save_error'),
    'hero_game_saved'       => __('hero_game_saved'),
    'hero_game_kept'        => __('hero_game_kept'),
    'hero_game_new_best'    => __('hero_game_new_best'),
    'hero_game_best_pts'    => __('hero_game_best_pts'),
    'hero_game_pts'         => __('hero_game_pts'),
    'hero_game_music_on'    => __('hero_game_music_on'),
    'hero_game_music_off'   => __('hero_game_music_off'),
  ],
], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>;
</script>
<script src="/assets/js/hero-game.js?v=<?= asset_version('assets/js/hero-game.js') ?>"></script>
