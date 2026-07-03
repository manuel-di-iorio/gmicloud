<style>
  .HomeBanner {
    background: #08081a;
    width: 100%;
    min-height: 100vh;
    display: flex;
    align-items: center;
    color: white;
    padding: 100px 40px 80px;
    position: relative;
    overflow: hidden;
    box-sizing: border-box;
  }

  .HomeBanner::before {
    content: '';
    position: absolute;
    top: 0; left: 0; right: 0; bottom: 0;
    background:
      radial-gradient(ellipse 80% 60% at 20% 30%, rgba(99,102,241,0.15) 0%, transparent 60%),
      radial-gradient(ellipse 60% 50% at 80% 70%, rgba(168,85,247,0.1) 0%, transparent 55%),
      radial-gradient(ellipse 50% 40% at 60% 20%, rgba(236,72,153,0.08) 0%, transparent 50%);
    pointer-events: none;
    z-index: 0;
  }

  #hero-particles {
    position: absolute;
    inset: 0;
    pointer-events: none;
    overflow: hidden;
    z-index: 1;
  }

  .hero-particle {
    position: absolute;
    border-radius: 50%;
    pointer-events: none;
    will-change: transform;
  }

  .hero-inner {
    display: flex;
    align-items: center;
    gap: 60px;
    max-width: 1200px;
    margin: 0 auto;
    width: 100%;
    position: relative;
    z-index: 2;
  }

  .hero-content { flex: 1.2; min-width: 0; }

  .hero-visual {
    flex: 1; min-width: 0;
    display: flex; justify-content: center; align-items: center;
  }

  .hero-visual img {
    width: 100%; max-width: 520px; height: auto;
    border-radius: 20px;
    box-shadow: 0 32px 64px rgba(0,0,0,0.5), 0 0 80px rgba(99,102,241,0.1);
  }

  .hero-logo {
    position: absolute; top: 28px; left: 40px;
    height: 36px; width: auto; z-index: 3;
  }

  .HomeBanner h1 {
    font-size: clamp(2.4rem, 5.5vw, 4.2rem);
    font-weight: 800; margin-bottom: 0.5em;
    line-height: 1.08; letter-spacing: -0.02em;
  }

  .HomeBanner h1 .gradient-text-hero {
    background: linear-gradient(135deg, #a78bfa, #f9a8d4);
    -webkit-background-clip: text; background-clip: text;
    -webkit-text-fill-color: transparent;
  }

  .HomeBanner .hero-subtitle {
    font-size: clamp(0.95rem, 1.5vw, 1.12rem);
    margin-bottom: 2em; max-width: 480px;
    line-height: 1.7; opacity: 0.7;
  }

  .hero-actions { display: flex; gap: 14px; align-items: center; flex-wrap: wrap; }

  .CtaButton {
    padding: 16px 36px; border-radius: 12px; font-weight: 600;
    letter-spacing: 0.3px; cursor: pointer; border: none; font-size: 1em;
    box-sizing: border-box;
    transition: transform 0.3s cubic-bezier(0.16,1,0.3,1), box-shadow 0.3s;
  }
  .CtaButton--primary {
    background: linear-gradient(135deg, var(--gradient-start, #6366f1), var(--gradient-end, #ec4899)) !important;
    color: white !important;
    box-shadow: 0 4px 24px rgba(var(--primary-color-rgb, 99,102,241), 0.35);
  }
  .CtaButton--primary:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 32px rgba(var(--primary-color-rgb, 99,102,241), 0.5);
  }
  .CtaButton--secondary {
    background: rgba(255,255,255,0.06) !important;
    color: rgba(255,255,255,0.9) !important;
    backdrop-filter: blur(8px);
    border: 1px solid rgba(255,255,255,0.12) !important;
  }
  .CtaButton--secondary:hover {
    background: rgba(255,255,255,0.12) !important;
    transform: translateY(-2px);
  }

  .hero-float-bar {
    position: fixed; top: 20px; right: 20px;
    display: flex; align-items: center; gap: 4px; padding: 6px 8px;
    border-radius: 14px;
    background: rgba(15, 17, 23, 0.7);
    backdrop-filter: blur(20px); -webkit-backdrop-filter: blur(20px);
    border: 1px solid rgba(255, 255, 255, 0.08);
    z-index: 10000; transition: opacity 0.3s;
  }
  .hero-float-bar.hero-float-hidden { opacity: 0; pointer-events: none; }

  .hero-icon-btn {
    display: flex; align-items: center; justify-content: center; gap: 6px;
    height: 38px; padding: 0 12px; border-radius: 10px;
    background: transparent; color: rgba(255,255,255,0.7);
    cursor: pointer; font-size: 0.88em; font-weight: 500;
    text-decoration: none; white-space: nowrap;
    transition: background 0.2s, color 0.2s;
  }
  .hero-icon-btn i { font-size: 1rem; }
  .hero-icon-btn:hover { background: rgba(255,255,255,0.1); color: white; }

  .hero-user-pill {
    display: flex; align-items: center; gap: 8px;
    padding: 4px 12px 4px 4px; border-radius: 10px;
    text-decoration: none; color: white; transition: background 0.2s;
  }
  .hero-user-pill:hover { background: rgba(255,255,255,0.08); }
  .hero-user-avatar {
    width: 30px; height: 30px; border-radius: 8px;
    background: linear-gradient(135deg, var(--gradient-start, #6366f1), var(--gradient-end, #ec4899));
    display: flex; align-items: center; justify-content: center;
    font-size: 0.8em; font-weight: 700; color: white;
  }
  .hero-user-name { font-size: 0.82em; font-weight: 500; white-space: nowrap; }

  .scroll-indicator {
    position: absolute; bottom: 28px; left: 50%; transform: translateX(-50%);
    display: flex; flex-direction: column; align-items: center; gap: 6px;
    color: white; font-size: 0.7em; opacity: 0.4; letter-spacing: 1.5px;
    text-transform: uppercase; z-index: 5;
    animation: scrollHint 2.5s ease-in-out infinite;
  }
  .scroll-indicator .scroll-line {
    width: 1px; height: 28px;
    background: linear-gradient(to bottom, white, transparent);
  }
  @keyframes scrollHint {
    0%, 100% { transform: translateX(-50%) translateY(0); opacity: 0.4; }
    50% { transform: translateX(-50%) translateY(6px); opacity: 0.7; }
  }

  /* ===== SECTION TITLES ===== */
  .SectionTitle {
    text-align: center; margin-bottom: 12px;
    font-size: clamp(1.4rem, 3vw, 2.2rem); font-weight: 800;
    color: var(--text-color-headings, #333);
  }
  .SectionTitle--left { text-align: left; }
  .SectionSubtitle {
    text-align: center; color: var(--text-color-secondary, #555);
    font-size: 1em; line-height: 1.6; margin-bottom: 48px;
    max-width: 500px; margin-left: auto; margin-right: auto;
  }

  /* ===== HOW IT WORKS ===== */
  .HowItWorksSection { padding: 80px 20px; background: var(--bg-color, white); }
  .ProcessStepsGrid {
    display: grid; grid-template-columns: repeat(4, 1fr);
    gap: 20px; max-width: 1100px; margin: 0 auto;
  }
  .process-step {
    position: relative; padding: 32px 24px 28px;
    background: var(--bg-color-card, #fff);
    border: 1px solid var(--border-color, #e5e7eb);
    border-radius: 16px; text-align: center;
    transition: transform 0.35s cubic-bezier(0.16,1,0.3,1), box-shadow 0.35s, border-color 0.3s;
    overflow: hidden;
  }
  .process-step::before {
    content: ''; position: absolute; top: 0; left: 0; right: 0; height: 3px;
    background: linear-gradient(90deg, var(--gradient-start, #6366f1), var(--gradient-end, #ec4899));
    opacity: 0; transition: opacity 0.3s;
  }
  .process-step:hover {
    border-color: var(--glass-border-hover, rgba(99,102,241,0.3));
    transform: translateY(-6px);
    box-shadow: 0 20px 40px rgba(0,0,0,0.08);
  }
  .process-step:hover::before { opacity: 1; }

  .process-step__icon {
    width: 60px; height: 60px; margin: 0 auto 18px; border-radius: 16px;
    background: linear-gradient(135deg, var(--gradient-start, #6366f1), var(--gradient-end, #ec4899));
    display: flex; align-items: center; justify-content: center;
    font-size: 1.5em; color: white;
    box-shadow: 0 6px 20px rgba(var(--primary-color-rgb, 99,102,241), 0.3);
    transition: transform 0.35s cubic-bezier(0.16,1,0.3,1), box-shadow 0.35s;
  }
  .process-step:hover .process-step__icon {
    transform: scale(1.08);
    box-shadow: 0 8px 28px rgba(var(--primary-color-rgb, 99,102,241), 0.4);
  }

  .process-step .process-step__number {
    position: absolute; top: 14px; left: 16px;
    font-size: 0.7em; font-weight: 800;
    color: var(--text-color-secondary, #aaa); opacity: 0.5;
    background: none !important; border-radius: 0 !important;
    width: auto !important; height: auto !important;
    display: block; box-shadow: none !important;
    padding: 0; line-height: 1;
  }
  .process-step .process-step__number::after { display: none !important; }
  .process-step h5 { font-size: 1em; font-weight: 700; margin-bottom: 8px; color: var(--text-color-headings, #333); }
  .process-step p { font-size: 0.85em; color: var(--text-color-secondary, #666); line-height: 1.6; margin: 0; }

  @media (max-width: 900px) { .ProcessStepsGrid { grid-template-columns: repeat(2, 1fr); } }
  @media (max-width: 500px) { .ProcessStepsGrid { grid-template-columns: 1fr; } }

  /* ===== FEATURES ===== */
  .FeaturesSection { padding: 80px 20px; background: var(--section-alt-bg, #f8fafc); }
  .FeaturesGrid {
    display: grid; grid-template-columns: repeat(4, 1fr);
    gap: 20px; max-width: 1100px; margin: 0 auto;
  }
  .FeatureCard {
    background: var(--glass-bg, rgba(255,255,255,0.08));
    backdrop-filter: blur(16px); -webkit-backdrop-filter: blur(16px);
    border: 1px solid var(--glass-border, rgba(255,255,255,0.12));
    border-radius: 16px; padding: 24px 20px; text-align: left;
    transition: transform 0.3s, box-shadow 0.3s, border-color 0.3s;
    position: relative; overflow: hidden;
  }
  .FeatureCard:hover {
    border-color: var(--glass-border-hover, rgba(99,102,241,0.3));
    box-shadow: 0 16px 40px rgba(0,0,0,0.08);
    transform: translateY(-4px);
  }
  .FeatureCard__Icon {
    width: 44px; height: 44px; border-radius: 12px;
    background: rgba(var(--primary-color-rgb, 99,102,241), 0.1);
    display: flex; align-items: center; justify-content: center;
    font-size: 1.2em; color: var(--primary-color, #6366f1); margin-bottom: 16px;
  }
  .FeatureCard h5 { font-size: 1em; font-weight: 700; color: var(--text-color-headings, #333); margin-bottom: 8px; }
  .FeatureCard p { font-size: 0.85em; color: var(--text-color-secondary, #555); line-height: 1.6; }

  @media (max-width: 900px) { .FeaturesGrid { grid-template-columns: repeat(2, 1fr); } }
  @media (max-width: 500px) { .FeaturesGrid { grid-template-columns: 1fr; } }

  /* ===== SERVICES ===== */
  .ServicesSection { padding: 80px 20px; background: var(--bg-color, white); }
  .ServicesGrid {
    display: grid; grid-template-columns: repeat(4, 1fr);
    gap: 20px; max-width: 1100px; margin: 0 auto;
  }
  .ServiceCard {
    background: var(--card-bg, rgba(255,255,255,0.04));
    border: 1px solid var(--glass-border, rgba(255,255,255,0.1));
    border-radius: 16px; padding: 32px 24px; text-align: center;
    transition: transform 0.3s, border-color 0.3s, box-shadow 0.3s;
  }
  .ServiceCard:hover {
    border-color: var(--glass-border-hover, rgba(99,102,241,0.3));
    box-shadow: 0 12px 32px rgba(0,0,0,0.06); transform: translateY(-4px);
  }
  .ServiceCard__Icon {
    width: 56px; height: 56px; margin: 0 auto 20px; border-radius: 14px;
    background: rgba(var(--primary-color-rgb, 99,102,241), 0.1);
    display: flex; align-items: center; justify-content: center;
    font-size: 1.4em; color: var(--primary-color, #6366f1);
  }
  .ServiceCard h5 { font-size: 1.05em; font-weight: 700; color: var(--text-color-headings, #333); margin-bottom: 10px; }
  .ServiceCard p { font-size: 0.88em; color: var(--text-color-secondary, #555); line-height: 1.6; }

  @media (max-width: 900px) { .ServicesGrid { grid-template-columns: repeat(2, 1fr); } }
  @media (max-width: 500px) { .ServicesGrid { grid-template-columns: 1fr; } }

  /* ===== STATS ===== */
  .StatsSection {
    padding: 80px 20px; position: relative; overflow: hidden;
    background: var(--glass-bg, rgba(255,255,255,0.08));
    backdrop-filter: blur(16px); -webkit-backdrop-filter: blur(16px);
    border-top: 1px solid var(--glass-border, rgba(255,255,255,0.08));
    border-bottom: 1px solid var(--glass-border, rgba(255,255,255,0.08));
  }
  .StatsSection::before {
    content: ''; position: absolute; inset: 0;
    background: radial-gradient(ellipse 60% 50% at 20% 50%, rgba(var(--primary-color-rgb, 99,102,241), 0.05), transparent),
                radial-gradient(ellipse 60% 50% at 80% 50%, rgba(236, 72, 153, 0.05), transparent);
    pointer-events: none;
  }
  .StatCardContainer {
    display: grid; grid-template-columns: repeat(6, 1fr);
    gap: 16px; max-width: 1200px; margin: 0 auto; position: relative;
  }
  .StatCard {
    background: var(--card-bg, rgba(255,255,255,0.04));
    border: 1px solid var(--glass-border, rgba(255,255,255,0.1));
    border-radius: 16px; padding: 24px 12px; text-align: center;
    position: relative; transition: box-shadow 0.3s, border-color 0.3s;
  }
  .StatCard:hover {
    border-color: var(--glass-border-hover, rgba(99,102,241,0.3));
    box-shadow: 0 12px 32px rgba(0,0,0,0.06);
  }
  .StatCard__Icon { font-size: 1.4em; color: var(--primary-color, #6366f1); margin-bottom: 8px; }
  .StatCard__Count {
    font-size: 1.6em; font-weight: 800;
    color: var(--text-color-headings, #333); margin-bottom: 4px;
    font-variant-numeric: tabular-nums;
  }
  .StatCard__Label { font-size: 0.72em; color: var(--text-color-secondary, #555); line-height: 1.3; }

  @media (max-width: 1100px) { .StatCardContainer { grid-template-columns: repeat(3, 1fr); } }
  @media (max-width: 600px) { .StatCardContainer { grid-template-columns: repeat(2, 1fr); } }

  /* ===== FAQ + CTA ===== */
  .FaqCtaSection { padding: 80px 20px; background: var(--bg-color-offset, #f4f7f6); }
  .FaqCtaGrid {
    display: grid; grid-template-columns: 1.2fr 1fr;
    gap: 40px; max-width: 1100px; margin: 0 auto; align-items: start;
  }
  .FAQsSection { padding: 0; }
  .FAQsContainer { max-width: 100%; margin: 0; }
  .CtaCard {
    background: linear-gradient(135deg, var(--gradient-start, #6366f1), var(--gradient-end, #ec4899));
    border-radius: 20px; padding: 40px 32px; color: white;
    position: sticky; top: 200px; margin-top: 50px;
  }
  .CtaCard__Label {
    font-size: 0.72em; letter-spacing: 2px; text-transform: uppercase;
    opacity: 0.7; margin-bottom: 12px; font-weight: 600;
  }
  .CtaCard h4 { font-size: 1.5em; font-weight: 800; margin-bottom: 16px; line-height: 1.2; }
  .CtaCard p { font-size: 0.95em; opacity: 0.85; line-height: 1.6; margin-bottom: 28px; }
  .CtaCard .CtaButton {
    background: white !important; color: #3730a3 !important;
    display: inline-flex; align-items: center; gap: 8px;
    text-decoration: none; padding: 14px 28px; font-weight: 700;
  }
  .CtaCard .CtaButton:hover { transform: translateY(-2px); box-shadow: 0 8px 24px rgba(0,0,0,0.2); }

  @media (max-width: 900px) {
    .FaqCtaGrid { grid-template-columns: 1fr; }
    .CtaCard { position: static; }
  }

  /* ===== RESPONSIVE ===== */
  @media (max-width: 900px) {
    .HomeBanner { padding: 60px 24px 60px; }
    .hero-inner { flex-direction: column; gap: 40px; text-align: center; }
    .hero-subtitle { margin-left: auto; margin-right: auto; }
    .hero-actions { justify-content: center; }
    .hero-visual { max-width: 400px; }
    .scroll-indicator { display: none; }
  }
  @media (max-width: 768px) {
    .HowItWorksSection, .FeaturesSection, .StatsSection, .ServicesSection, .FaqCtaSection { padding: 50px 16px; }
    .hero-actions { flex-direction: column; width: 100%; }
    .hero-actions .CtaButton { width: 100%; text-align: center; justify-content: center; }
    .hero-logo { display: none; }
  }
  @media (max-width: 480px) {
    .HomeBanner { padding: 40px 16px; height: auto; min-height: 100vh; }
    .CtaButton { padding: 14px 20px; font-size: 0.9em; }
  }
</style>

<div id="scroll-progress"></div>

<!-- ===== STICKY HEADER ===== -->
<header class="landing-header" role="banner">
  <a href="./index.php" class="header-logo">
    <img src="/assets/images/logo<?= $theme === 'dark' ? 'White' : '' ?>.svg" alt="Logo" width="451" height="109">
  </a>
  <nav class="header-nav">
    <a href="#come-funziona" class="nav-link-underline"><?= __('index_nav_how') ?></a>
    <a href="#servizi" class="nav-link-underline"><?= __('index_nav_services') ?></a>
    <a href="#caratteristiche" class="nav-link-underline"><?= __('index_nav_features') ?></a>
    <a href="#numeri" class="nav-link-underline"><?= __('index_nav_numbers') ?></a>
    <a href="#faq" class="nav-link-underline"><?= __('index_nav_faq') ?></a>
    <?php if (isset($user)) { ?>
      <a href="./home.php" class="header-user-pill">
        <span class="header-user-avatar"><?= strtoupper(mb_substr($user["username"], 0, 1)) ?></span>
        <span class="header-user-name"><?= htmlspecialchars($user["username"]) ?></span>
      </a>
    <?php } else { ?>
      <a href="login.php" class="header-icon-btn" title="<?= __('nav_login') ?>"><i class="fas fa-sign-in-alt"></i></a>
    <?php } ?>
    <a href="<?= isset($user) ? './home.php' : './add-game.php' ?>" class="header-cta"><?= __('index_nav_start') ?></a>
  </nav>
</header>

<!-- ===== HERO ===== -->
<div class="HomeBanner">
  <img src="/assets/images/logoWhite.svg" class="hero-logo" alt="Logo" width="451" height="109">
  <div id="hero-particles"></div>

  <div class="hero-inner">
    <div class="hero-content">
      <h1 class="anim-fade-up">
        <?= __('index_hero_title1') ?><br>
        <span class="gradient-text-hero"><?= __('index_hero_title2') ?></span>
      </h1>

      <p class="hero-subtitle anim-fade-up anim-delay-200">
        <?= __('index_hero_subtitle') ?>
      </p>

      <div class="hero-actions anim-fade-up anim-delay-300">
        <a href="<?= isset($user) ? './home.php' : './add-game.php' ?>" class="CtaButton CtaButton--primary ripple-btn" style="display:inline-flex;align-items:center;gap:10px;text-decoration:none;">
          <?= __('index_hero_cta') ?> <i class="fas fa-arrow-right"></i>
        </a>
        <a href="./documentation.php" class="CtaButton CtaButton--secondary ripple-btn" style="display:inline-flex;align-items:center;gap:10px;text-decoration:none;">
          <?= __('index_hero_docs') ?>
        </a>
      </div>
    </div>

    <div class="hero-visual anim-scale-up anim-delay-200">
      <img src="/assets/images/hero-image.webp" alt="<?= __('index_hero_title2') ?>" width="1402" height="1122">
    </div>
  </div>

  <div class="scroll-indicator">
    <span><?= __('index_scroll') ?></span>
    <div class="scroll-line"></div>
  </div>
</div>

<div class="hero-float-bar">
  <?php if (isset($user)) { ?>
    <a href="./home.php" class="hero-user-pill">
      <span class="hero-user-avatar"><?= strtoupper(mb_substr($user["username"], 0, 1)) ?></span>
      <span class="hero-user-name"><?= htmlspecialchars($user["username"]) ?></span>
    </a>
  <?php } else { ?>
    <a href="login.php" class="hero-icon-btn" title="<?= __('nav_login') ?>">
      <i class="fas fa-sign-in-alt"></i> <span><?= __('nav_login') ?></span>
    </a>
  <?php } ?>
  <div class="hero-icon-btn" onclick="switchThemeHome()" title="<?= __('index_theme_toggle') ?>">
    <i class="fas <?= $theme === 'dark' ? 'fa-sun' : 'fa-moon' ?>"></i>
  </div>
</div>

<!-- ===== HOW IT WORKS ===== -->
<div id="come-funziona" class="section-container HowItWorksSection">
  <h2 class="SectionTitle fade-in-up-on-scroll"><?= __('index_how_title') ?></h2>
  <p class="SectionSubtitle fade-in-up-on-scroll"><?= __('index_how_subtitle') ?></p>
  <div class="ProcessStepsGrid">
    <div class="process-step fade-in-up-on-scroll">
      <div class="process-step__number">1</div>
      <div class="process-step__icon"><i class="fas fa-gamepad"></i></div>
      <h5><?= __('index_step1_title') ?></h5>
      <p><?= __('index_step1_desc') ?></p>
    </div>
    <div class="process-step fade-in-up-on-scroll anim-delay-100">
      <div class="process-step__number">2</div>
      <div class="process-step__icon"><i class="fas fa-puzzle-piece"></i></div>
      <h5><?= __('index_step2_title') ?></h5>
      <p><?= __('index_step2_desc') ?></p>
    </div>
    <div class="process-step fade-in-up-on-scroll anim-delay-200">
      <div class="process-step__number">3</div>
      <div class="process-step__icon"><i class="fas fa-cloud"></i></div>
      <h5><?= __('index_step3_title') ?></h5>
      <p><?= __('index_step3_desc') ?></p>
    </div>
    <div class="process-step fade-in-up-on-scroll anim-delay-300">
      <div class="process-step__number">4</div>
      <div class="process-step__icon"><i class="fas fa-chart-bar"></i></div>
      <h5><?= __('index_step4_title') ?></h5>
      <p><?= __('index_step4_desc') ?></p>
    </div>
  </div>
</div>

<!-- ===== FEATURES ===== -->
<div id="caratteristiche" class="section-container FeaturesSection">
  <div style="max-width:1100px;margin:0 auto;">
    <h2 class="SectionTitle SectionTitle--left fade-in-up-on-scroll" style="margin-bottom:8px;">
      <?= __('index_features_title1') ?> <span class="gradient-text"><?= __('index_features_title2') ?></span>
    </h2>
    <p class="SectionSubtitle fade-in-up-on-scroll" style="text-align:left;margin-bottom:40px;max-width:none;">
      <?= __('index_features_subtitle') ?>
    </p>
  </div>
  <div class="FeaturesGrid">
    <div class="FeatureCard fade-in-up-on-scroll tilt-card">
      <div class="tilt-card__shine"></div>
      <div class="FeatureCard__Icon"><i class="fas fa-cogs"></i></div>
      <h5><?= __('index_feature1_title') ?></h5>
      <p><?= __('index_feature1_desc') ?></p>
    </div>
    <div class="FeatureCard fade-in-up-on-scroll tilt-card">
      <div class="tilt-card__shine"></div>
      <div class="FeatureCard__Icon"><i class="fas fa-expand-arrows-alt"></i></div>
      <h5><?= __('index_feature2_title') ?></h5>
      <p><?= __('index_feature2_desc') ?></p>
    </div>
    <div class="FeatureCard fade-in-up-on-scroll tilt-card">
      <div class="tilt-card__shine"></div>
      <div class="FeatureCard__Icon"><i class="fas fa-shield-alt"></i></div>
      <h5><?= __('index_feature3_title') ?></h5>
      <p><?= __('index_feature3_desc') ?></p>
    </div>
    <div class="FeatureCard fade-in-up-on-scroll tilt-card">
      <div class="tilt-card__shine"></div>
      <div class="FeatureCard__Icon"><i class="fas fa-server"></i></div>
      <h5><?= __('index_feature4_title') ?></h5>
      <p><?= __('index_feature4_desc') ?></p>
    </div>
    <div class="FeatureCard fade-in-up-on-scroll tilt-card">
      <div class="tilt-card__shine"></div>
      <div class="FeatureCard__Icon"><i class="fas fa-tachometer-alt"></i></div>
      <h5><?= __('index_feature5_title') ?></h5>
      <p><?= __('index_feature5_desc') ?></p>
    </div>
    <div class="FeatureCard fade-in-up-on-scroll tilt-card">
      <div class="tilt-card__shine"></div>
      <div class="FeatureCard__Icon"><i class="fas fa-users"></i></div>
      <h5><?= __('index_feature6_title') ?></h5>
      <p><?= __('index_feature6_desc') ?></p>
    </div>
    <div class="FeatureCard fade-in-up-on-scroll tilt-card">
      <div class="tilt-card__shine"></div>
      <div class="FeatureCard__Icon"><i class="fab fa-github"></i></div>
      <h5><?= __('index_feature7_title') ?></h5>
      <p><?= __('index_feature7_desc1') ?> <a href="https://github.com/manuel-di-iorio/gmicloud" style="color:var(--primary-color);"><?= __('index_feature7_github') ?></a>. <?= __('index_feature7_desc2') ?></p>
    </div>
    <div class="FeatureCard fade-in-up-on-scroll tilt-card">
      <div class="tilt-card__shine"></div>
      <div class="FeatureCard__Icon"><i class="fas fa-rocket"></i></div>
      <h5><?= __('index_feature8_title') ?></h5>
      <p><?= __('index_feature8_desc') ?></p>
    </div>
  </div>
</div>

<!-- ===== SERVICES ===== -->
<div id="servizi" class="section-container ServicesSection">
  <h2 class="SectionTitle fade-in-up-on-scroll"><?= __('index_services_title') ?></h2>
  <p class="SectionSubtitle fade-in-up-on-scroll"><?= __('index_services_subtitle') ?></p>
  <div class="ServicesGrid">
    <div class="ServiceCard fade-in-up-on-scroll">
      <div class="ServiceCard__Icon"><i class="fas fa-trophy"></i></div>
      <h5><?= __('index_service1_title') ?></h5>
      <p><?= __('index_service1_desc') ?></p>
    </div>
    <div class="ServiceCard fade-in-up-on-scroll anim-delay-100">
      <div class="ServiceCard__Icon"><i class="fas fa-chart-pie"></i></div>
      <h5><?= __('index_service2_title') ?></h5>
      <p><?= __('index_service2_desc') ?></p>
    </div>
    <div class="ServiceCard fade-in-up-on-scroll anim-delay-200">
      <div class="ServiceCard__Icon"><i class="fas fa-user-shield"></i></div>
      <h5><?= __('index_service3_title') ?></h5>
      <p><?= __('index_service3_desc') ?></p>
    </div>
    <div class="ServiceCard fade-in-up-on-scroll anim-delay-300">
      <div class="ServiceCard__Icon"><i class="fas fa-cloud-upload-alt"></i></div>
      <h5><?= __('index_service4_title') ?></h5>
      <p><?= __('index_service4_desc') ?></p>
    </div>
  </div>
</div>

<!-- ===== STATS ===== -->
<div id="numeri" class="section-container StatsSection stats-gradient-section">
  <h2 class="SectionTitle fade-in-up-on-scroll"><?= __('index_stats_title1') ?> <span class="gradient-text"><?= __('index_stats_title2') ?></span></h2>
  <p class="SectionSubtitle fade-in-up-on-scroll"><?= __('index_stats_subtitle') ?></p>
  <div class="StatCardContainer stagger-grid">
    <?php
      $statIcons = [
        "scores" => "fas fa-star",
        "players" => "fas fa-users",
        "games" => "fas fa-gamepad",
        "active-games" => "fas fa-bolt",
        "unique-scores-countries" => "fas fa-globe-americas",
        "users" => "fas fa-code"
      ];
    ?>
    <?php foreach ($stats as $key => $stat) { ?>
    <div class="StatCard stagger-item tilt-card">
      <div class="tilt-card__shine"></div>
      <div class="StatCard__Icon"><i class="<?= $statIcons[$key] ?? 'fas fa-chart-bar' ?>"></i></div>
      <div class="StatCard__Count">
        <?php if (is_numeric($stat["count"])) { ?>
          <span class="stat-number" data-target="<?= intval($stat["count"]) ?>">0</span>
        <?php } else { ?>
          <span style="font-size:0.7em;display:block;line-height:1.3;word-break:break-word;"><?= htmlspecialchars($stat["count"]) ?></span>
        <?php } ?>
      </div>
      <div class="StatCard__Label"><?= htmlspecialchars($stat["label"]) ?></div>
    </div>
    <?php } ?>
  </div>
</div>

<!-- ===== FAQ + CTA ===== -->
<div id="faq" class="section-container FaqCtaSection">
  <div class="FaqCtaGrid">
    <div class="FAQsSection">
      <h2 class="SectionTitle SectionTitle--left fade-in-up-on-scroll"><?= __('index_faq_title1') ?> <span class="gradient-text"><?= __('index_faq_title2') ?></span></h2>
      <div class="FAQsContainer">
        <div class="faq-item fade-in-up-on-scroll">
          <button class="faq-question">
            <span><?= __('index_faq1_q') ?></span>
            <span class="faq-icon"><i class="fas fa-plus"></i></span>
          </button>
          <div class="faq-answer"><?= __('index_faq1_a') ?></div>
        </div>
        <div class="faq-item fade-in-up-on-scroll anim-delay-100">
          <button class="faq-question">
            <span><?= __('index_faq2_q') ?></span>
            <span class="faq-icon"><i class="fas fa-plus"></i></span>
          </button>
          <div class="faq-answer"><?= __('index_faq2_a') ?></div>
        </div>
        <div class="faq-item fade-in-up-on-scroll anim-delay-200">
          <button class="faq-question">
            <span><?= __('index_faq3_q') ?></span>
            <span class="faq-icon"><i class="fas fa-plus"></i></span>
          </button>
          <div class="faq-answer"><?= __('index_faq3_a') ?></div>
        </div>
        <div class="faq-item fade-in-up-on-scroll anim-delay-300">
          <button class="faq-question">
            <span><?= __('index_faq4_q') ?></span>
            <span class="faq-icon"><i class="fas fa-plus"></i></span>
          </button>
          <div class="faq-answer"><?= __('index_faq4_a') ?></div>
        </div>
      </div>
    </div>

    <div class="CtaCard">
      <div class="CtaCard__Label"><?= __('index_cta_card_label') ?></div>
      <h4><?= __('index_cta_title1') ?> <span style="opacity:0.9;"><?= __('index_cta_title2') ?></span></h4>
      <p><?= __('index_cta_desc') ?></p>
      <a href="<?= isset($user) ? './home.php' : './add-game.php' ?>" class="CtaButton">
        <?= __('index_cta_button') ?> <i class="fas fa-arrow-right"></i>
      </a>
    </div>
  </div>
</div>

<script>
function switchThemeHome() {
  const theme = "<?= $theme === 'dark' ? 'light' : 'dark' ?>";
  location.href = "switch-theme.php?theme=" + theme + "&go=" + encodeURIComponent("<?= $_SERVER["REQUEST_URI"] ?>");
}

(function() {
  const container = document.getElementById('hero-particles');
  if (!container) return;
  const banner = container.parentElement;
  let mouseX = 0, mouseY = 0;
  let particles = [];
  const PARTICLE_COUNT = 40;

  function createParticle() {
    const el = document.createElement('div');
    el.className = 'hero-particle';
    const size = Math.random() * 3 + 1;
    const x = Math.random() * 100;
    const y = Math.random() * 100;
    const dur = Math.random() * 20 + 15;
    const delay = Math.random() * -30;
    const hue = Math.random() > 0.5 ? '239, 102, 241' : '236, 72, 153';
    const alpha = Math.random() * 0.4 + 0.1;
    el.style.cssText = `
      width:${size}px;height:${size}px;
      left:${x}%;top:${y}%;
      background:rgba(${hue},${alpha});
      box-shadow:0 0 ${size * 3}px rgba(${hue},${alpha * 0.5});
      animation:particleFloat ${dur}s linear ${delay}s infinite;
    `;
    container.appendChild(el);
    return { el, baseX: x, baseY: y, size };
  }

  for (let i = 0; i < PARTICLE_COUNT; i++) {
    particles.push(createParticle());
  }

  const style = document.createElement('style');
  style.textContent = `
    @keyframes particleFloat {
      0% { transform: translateY(0) translateX(0); opacity: 0; }
      10% { opacity: 1; }
      90% { opacity: 1; }
      100% { transform: translateY(-100vh) translateX(30px); opacity: 0; }
    }
  `;
  document.head.appendChild(style);

  banner.addEventListener('mousemove', (e) => {
    const rect = banner.getBoundingClientRect();
    mouseX = ((e.clientX - rect.left) / rect.width - 0.5) * 2;
    mouseY = ((e.clientY - rect.top) / rect.height - 0.5) * 2;
  });

  function updateParallax() {
    particles.forEach((p, i) => {
      const depth = (i % 5 + 1) * 0.8;
      const ox = mouseX * depth * 8;
      const oy = mouseY * depth * 4;
      p.el.style.marginLeft = ox + 'px';
      p.el.style.marginTop = oy + 'px';
    });
    requestAnimationFrame(updateParallax);
  }
  requestAnimationFrame(updateParallax);
})();
</script>
