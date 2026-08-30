<div id="scroll-progress"></div>

<!-- ===== STICKY HEADER ===== -->
<header class="landing-header" role="banner">
  <a href="./index.php" class="header-logo">
    <img src="/assets/images/logo<?= $theme === 'dark' ? 'White' : '' ?>.png" alt="Logo" width="451" height="109">
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
<div class="HomeBanner relative box-border flex min-h-screen w-full items-center overflow-hidden bg-[#08081a] px-4 py-10 text-white md:px-6 md:py-[60px] lg:px-10 lg:pb-20 lg:pt-[100px]">
  <div class="pointer-events-none absolute inset-0 bg-[radial-gradient(ellipse_80%_60%_at_20%_30%,rgba(99,102,241,0.15)_0%,transparent_60%),radial-gradient(ellipse_60%_50%_at_80%_70%,rgba(168,85,247,0.1)_0%,transparent_55%),radial-gradient(ellipse_50%_40%_at_60%_20%,rgba(236,72,153,0.08)_0%,transparent_50%)]"></div>
  <img src="/assets/images/logoWhite.png" class="hero-logo absolute left-10 top-7 z-[3] hidden h-9 w-auto lg:block" alt="Logo" width="451" height="109">
  <div id="hero-particles" class="pointer-events-none absolute inset-0 z-[1] overflow-hidden"></div>

  <div class="hero-inner relative z-[2] mx-auto flex w-full max-w-[1200px] flex-col items-center gap-10 text-center lg:flex-row lg:gap-[60px] lg:text-left">
    <div class="hero-content min-w-0 flex-[1.2]">
      <h1 class="anim-fade-up mb-[0.5em] text-4xl font-extrabold leading-[1.08] md:text-5xl lg:text-[4.2rem]">
        <?= __('index_hero_title1') ?><br>
        <span class="gradient-text-hero bg-gradient-to-br from-violet-400 to-pink-300 bg-clip-text text-transparent"><?= __('index_hero_title2') ?></span>
      </h1>

      <p class="hero-subtitle anim-fade-up anim-delay-200 mx-auto mb-8 max-w-[480px] text-[0.95rem] leading-[1.7] opacity-70 lg:mx-0 lg:text-lg">
        <?= __('index_hero_subtitle') ?>
      </p>

      <div class="hero-actions anim-fade-up anim-delay-300 flex w-full flex-col items-center justify-center gap-3.5 md:w-auto md:flex-row md:flex-wrap lg:justify-start">
        <a href="<?= isset($user) ? './home.php' : './add-game.php' ?>" class="CtaButton CtaButton--primary ripple-btn inline-flex w-full cursor-pointer items-center justify-center gap-2.5 rounded-xl border-0 bg-gradient-to-br from-[var(--gradient-start)] to-[var(--gradient-end)] px-5 py-3.5 font-semibold text-white no-underline shadow-[0_4px_24px_rgba(var(--primary-color-rgb),0.35)] transition hover:-translate-y-0.5 hover:shadow-[0_8px_32px_rgba(var(--primary-color-rgb),0.5)] md:w-auto md:px-9 md:py-4">
          <?= __('index_hero_cta') ?> <i class="fas fa-arrow-right"></i>
        </a>
        <a href="./documentation.php" class="CtaButton CtaButton--secondary ripple-btn inline-flex w-full cursor-pointer items-center justify-center gap-2.5 rounded-xl border border-solid border-white/10 bg-white/[0.06] px-5 py-3.5 font-semibold text-white/90 no-underline backdrop-blur-lg transition hover:-translate-y-0.5 hover:bg-white/[0.12] md:w-auto md:px-9 md:py-4">
          <?= __('index_hero_docs') ?>
        </a>
      </div>
    </div>

    <div class="hero-visual anim-scale-up anim-delay-200 flex min-w-0 max-w-[400px] flex-1 items-center justify-center lg:max-w-none">
      <img src="/assets/images/hero-image.webp" alt="<?= __('index_hero_title2') ?>" class="h-auto w-full max-w-[520px] rounded-[20px] shadow-[0_32px_64px_rgba(0,0,0,0.5),0_0_80px_rgba(99,102,241,0.1)]" width="1402" height="1122">
    </div>
  </div>

  <div class="scroll-indicator absolute bottom-7 left-1/2 z-[5] hidden -translate-x-1/2 flex-col items-center gap-1.5 text-[0.7rem] uppercase tracking-[1.5px] text-white opacity-40 lg:flex">
    <span><?= __('index_scroll') ?></span>
    <div class="scroll-line h-7 w-px bg-gradient-to-b from-white to-transparent"></div>
  </div>
</div>

<div class="hero-float-bar fixed right-3 top-3 z-[10000] flex items-center gap-1 rounded-[14px] border border-solid border-white/[0.08] bg-[#0f1117]/70 px-2 py-1.5 backdrop-blur-xl transition-opacity md:right-5 md:top-5">
  <?php if (isset($user)) { ?>
    <a href="./home.php" class="hero-user-pill flex items-center gap-2 rounded-[10px] py-1 pl-1 pr-3 text-white no-underline transition-colors hover:bg-white/[0.08]">
      <span class="hero-user-avatar flex h-[30px] w-[30px] items-center justify-center rounded-lg bg-gradient-to-br from-[var(--gradient-start)] to-[var(--gradient-end)] text-[0.8rem] font-bold text-white"><?= strtoupper(mb_substr($user["username"], 0, 1)) ?></span>
      <span class="hero-user-name whitespace-nowrap text-[0.82rem] font-medium"><?= htmlspecialchars($user["username"]) ?></span>
    </a>
  <?php } else { ?>
    <a href="login.php" class="hero-icon-btn flex h-[38px] items-center justify-center gap-1.5 whitespace-nowrap rounded-[10px] px-3 text-[0.88rem] font-medium text-white/70 no-underline transition-colors hover:bg-white/10 hover:text-white" title="<?= __('nav_login') ?>">
      <i class="fas fa-sign-in-alt"></i> <span><?= __('nav_login') ?></span>
    </a>
  <?php } ?>
  <div class="hero-icon-btn flex h-[38px] cursor-pointer items-center justify-center rounded-[10px] px-3 text-white/70 transition-colors hover:bg-white/10 hover:text-white" onclick="switchThemeHome()" title="<?= __('index_theme_toggle') ?>">
    <i class="fas <?= $theme === 'dark' ? 'fa-sun' : 'fa-moon' ?>"></i>
  </div>
</div>

<!-- ===== HOW IT WORKS ===== -->
<div id="come-funziona" class="section-container HowItWorksSection bg-[var(--bg-color)] px-4 py-[50px] md:px-5 md:py-20">
  <h2 class="SectionTitle fade-in-up-on-scroll mb-3 text-center text-2xl font-extrabold text-[var(--text-color-headings)] md:text-4xl"><?= __('index_how_title') ?></h2>
  <p class="SectionSubtitle fade-in-up-on-scroll mx-auto mb-12 max-w-[500px] text-center leading-relaxed text-[var(--text-color-secondary)]"><?= __('index_how_subtitle') ?></p>
  <div class="ProcessStepsGrid mx-auto grid max-w-[1100px] grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4 [&_.process-step]:relative [&_.process-step]:overflow-hidden [&_.process-step]:rounded-2xl [&_.process-step]:border [&_.process-step]:border-solid [&_.process-step]:border-[var(--border-color)] [&_.process-step]:bg-[var(--bg-color-card)] [&_.process-step]:px-6 [&_.process-step]:pb-7 [&_.process-step]:pt-8 [&_.process-step]:text-center [&_.process-step]:transition [&_.process-step]:duration-300 [&_.process-step:hover]:-translate-y-1.5 [&_.process-step:hover]:shadow-xl [&_.process-step-number]:absolute [&_.process-step-number]:left-4 [&_.process-step-number]:top-3.5 [&_.process-step-number]:text-[0.7rem] [&_.process-step-number]:font-extrabold [&_.process-step-number]:text-[var(--text-color-secondary)] [&_.process-step-icon]:mx-auto [&_.process-step-icon]:mb-[18px] [&_.process-step-icon]:flex [&_.process-step-icon]:h-[60px] [&_.process-step-icon]:w-[60px] [&_.process-step-icon]:items-center [&_.process-step-icon]:justify-center [&_.process-step-icon]:rounded-2xl [&_.process-step-icon]:bg-gradient-to-br [&_.process-step-icon]:from-[var(--gradient-start)] [&_.process-step-icon]:to-[var(--gradient-end)] [&_.process-step-icon]:text-2xl [&_.process-step-icon]:text-white [&_.process-step_h3]:mb-2 [&_.process-step_h3]:font-bold [&_.process-step_h3]:text-[var(--text-color-headings)] [&_.process-step_p]:m-0 [&_.process-step_p]:text-[0.85rem] [&_.process-step_p]:leading-relaxed [&_.process-step_p]:text-[var(--text-color-secondary)]">
    <div class="process-step fade-in-up-on-scroll">
      <div class="process-step-number">1</div>
      <div class="process-step-icon"><i class="fas fa-gamepad"></i></div>
      <h3><?= __('index_step1_title') ?></h3>
      <p><?= __('index_step1_desc') ?></p>
    </div>
    <div class="process-step fade-in-up-on-scroll anim-delay-100">
      <div class="process-step-number">2</div>
      <div class="process-step-icon"><i class="fas fa-puzzle-piece"></i></div>
      <h3><?= __('index_step2_title') ?></h3>
      <p><?= __('index_step2_desc') ?></p>
    </div>
    <div class="process-step fade-in-up-on-scroll anim-delay-200">
      <div class="process-step-number">3</div>
      <div class="process-step-icon"><i class="fas fa-cloud"></i></div>
      <h3><?= __('index_step3_title') ?></h3>
      <p><?= __('index_step3_desc') ?></p>
    </div>
    <div class="process-step fade-in-up-on-scroll anim-delay-300">
      <div class="process-step-number">4</div>
      <div class="process-step-icon"><i class="fas fa-chart-bar"></i></div>
      <h3><?= __('index_step4_title') ?></h3>
      <p><?= __('index_step4_desc') ?></p>
    </div>
  </div>
</div>

<!-- ===== FEATURES ===== -->
<div id="caratteristiche" class="section-container FeaturesSection bg-[var(--section-alt-bg)] px-4 py-[50px] md:px-5 md:py-20">
  <div class="mx-auto max-w-[1100px]">
    <h2 class="SectionTitle SectionTitle--left fade-in-up-on-scroll mb-2 text-left text-2xl font-extrabold text-[var(--text-color-headings)] md:text-4xl">
      <?= __('index_features_title1') ?> <span class="gradient-text"><?= __('index_features_title2') ?></span>
    </h2>
    <p class="SectionSubtitle fade-in-up-on-scroll mb-10 max-w-none text-left leading-relaxed text-[var(--text-color-secondary)]">
      <?= __('index_features_subtitle') ?>
    </p>
  </div>
  <div class="FeaturesGrid mx-auto grid max-w-[1100px] grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4 [&_.FeatureCard]:relative [&_.FeatureCard]:overflow-hidden [&_.FeatureCard]:rounded-2xl [&_.FeatureCard]:border [&_.FeatureCard]:border-solid [&_.FeatureCard]:border-[var(--glass-border)] [&_.FeatureCard]:bg-[var(--glass-bg)] [&_.FeatureCard]:px-5 [&_.FeatureCard]:py-6 [&_.FeatureCard]:text-left [&_.FeatureCard]:backdrop-blur-2xl [&_.FeatureCard]:transition [&_.FeatureCard]:duration-300 [&_.FeatureCard:hover]:border-[rgba(var(--primary-color-rgb),0.3)] [&_.FeatureCard:hover]:shadow-xl [&_.FeatureCardIcon]:mb-4 [&_.FeatureCardIcon]:flex [&_.FeatureCardIcon]:h-11 [&_.FeatureCardIcon]:w-11 [&_.FeatureCardIcon]:items-center [&_.FeatureCardIcon]:justify-center [&_.FeatureCardIcon]:rounded-xl [&_.FeatureCardIcon]:bg-[rgba(var(--primary-color-rgb),0.1)] [&_.FeatureCardIcon]:text-xl [&_.FeatureCardIcon]:text-[var(--primary-color)] [&_.FeatureCard_h3]:mb-2 [&_.FeatureCard_h3]:text-lg [&_.FeatureCard_h3]:font-bold [&_.FeatureCard_h3]:text-[var(--text-color-headings)] [&_.FeatureCard_p]:text-[0.85rem] [&_.FeatureCard_p]:leading-relaxed [&_.FeatureCard_p]:text-[var(--text-color-secondary)]">
    <div class="FeatureCard fade-in-up-on-scroll tilt-card">
      <div class="tilt-card__shine"></div>
      <div class="FeatureCardIcon"><i class="fas fa-cogs"></i></div>
      <h3><?= __('index_feature1_title') ?></h3>
      <p><?= __('index_feature1_desc') ?></p>
    </div>
    <div class="FeatureCard fade-in-up-on-scroll tilt-card">
      <div class="tilt-card__shine"></div>
      <div class="FeatureCardIcon"><i class="fas fa-expand-arrows-alt"></i></div>
      <h3><?= __('index_feature2_title') ?></h3>
      <p><?= __('index_feature2_desc') ?></p>
    </div>
    <div class="FeatureCard fade-in-up-on-scroll tilt-card">
      <div class="tilt-card__shine"></div>
      <div class="FeatureCardIcon"><i class="fas fa-shield-alt"></i></div>
      <h3><?= __('index_feature3_title') ?></h3>
      <p><?= __('index_feature3_desc') ?></p>
    </div>
    <div class="FeatureCard fade-in-up-on-scroll tilt-card">
      <div class="tilt-card__shine"></div>
      <div class="FeatureCardIcon"><i class="fas fa-server"></i></div>
      <h3><?= __('index_feature4_title') ?></h3>
      <p><?= __('index_feature4_desc') ?></p>
    </div>
    <div class="FeatureCard fade-in-up-on-scroll tilt-card">
      <div class="tilt-card__shine"></div>
      <div class="FeatureCardIcon"><i class="fas fa-tachometer-alt"></i></div>
      <h3><?= __('index_feature5_title') ?></h3>
      <p><?= __('index_feature5_desc') ?></p>
    </div>
    <div class="FeatureCard fade-in-up-on-scroll tilt-card">
      <div class="tilt-card__shine"></div>
      <div class="FeatureCardIcon"><i class="fas fa-users"></i></div>
      <h3><?= __('index_feature6_title') ?></h3>
      <p><?= __('index_feature6_desc') ?></p>
    </div>
    <div class="FeatureCard fade-in-up-on-scroll tilt-card">
      <div class="tilt-card__shine"></div>
      <div class="FeatureCardIcon"><i class="fab fa-github"></i></div>
      <h3><?= __('index_feature7_title') ?></h3>
      <p><?= __('index_feature7_desc1') ?> <a href="https://github.com/manuel-di-iorio/gmicloud" class="text-[var(--primary-color)]"><?= __('index_feature7_github') ?></a>. <?= __('index_feature7_desc2') ?></p>
    </div>
    <div class="FeatureCard fade-in-up-on-scroll tilt-card">
      <div class="tilt-card__shine"></div>
      <div class="FeatureCardIcon"><i class="fas fa-rocket"></i></div>
      <h3><?= __('index_feature8_title') ?></h3>
      <p><?= __('index_feature8_desc') ?></p>
    </div>
  </div>
</div>

<!-- ===== SERVICES ===== -->
<div id="servizi" class="section-container ServicesSection bg-[var(--bg-color)] px-4 py-[50px] md:px-5 md:py-20">
  <h2 class="SectionTitle fade-in-up-on-scroll mb-3 text-center text-2xl font-extrabold text-[var(--text-color-headings)] md:text-4xl"><?= __('index_services_title') ?></h2>
  <p class="SectionSubtitle fade-in-up-on-scroll mx-auto mb-12 max-w-[500px] text-center leading-relaxed text-[var(--text-color-secondary)]"><?= __('index_services_subtitle') ?></p>
  <div class="ServicesGrid mx-auto grid max-w-[1100px] grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4 [&_.ServiceCard]:rounded-2xl [&_.ServiceCard]:border [&_.ServiceCard]:border-solid [&_.ServiceCard]:border-[var(--glass-border)] [&_.ServiceCard]:bg-[rgba(255,255,255,0.04)] [&_.ServiceCard]:px-6 [&_.ServiceCard]:py-8 [&_.ServiceCard]:text-center [&_.ServiceCard]:transition [&_.ServiceCard]:duration-300 [&_.ServiceCard:hover]:-translate-y-1 [&_.ServiceCard:hover]:border-[rgba(var(--primary-color-rgb),0.3)] [&_.ServiceCard:hover]:shadow-lg [&_.ServiceCardIcon]:mx-auto [&_.ServiceCardIcon]:mb-5 [&_.ServiceCardIcon]:flex [&_.ServiceCardIcon]:h-14 [&_.ServiceCardIcon]:w-14 [&_.ServiceCardIcon]:items-center [&_.ServiceCardIcon]:justify-center [&_.ServiceCardIcon]:rounded-[14px] [&_.ServiceCardIcon]:bg-[rgba(var(--primary-color-rgb),0.1)] [&_.ServiceCardIcon]:text-2xl [&_.ServiceCardIcon]:text-[var(--primary-color)] [&_.ServiceCard_h3]:mb-2.5 [&_.ServiceCard_h3]:text-lg [&_.ServiceCard_h3]:font-bold [&_.ServiceCard_h3]:text-[var(--text-color-headings)] [&_.ServiceCard_p]:text-[0.88rem] [&_.ServiceCard_p]:leading-relaxed [&_.ServiceCard_p]:text-[var(--text-color-secondary)]">
    <div class="ServiceCard fade-in-up-on-scroll">
      <div class="ServiceCardIcon"><i class="fas fa-trophy"></i></div>
      <h3><?= __('index_service1_title') ?></h3>
      <p><?= __('index_service1_desc') ?></p>
    </div>
    <div class="ServiceCard fade-in-up-on-scroll anim-delay-100">
      <div class="ServiceCardIcon"><i class="fas fa-chart-pie"></i></div>
      <h3><?= __('index_service2_title') ?></h3>
      <p><?= __('index_service2_desc') ?></p>
    </div>
    <div class="ServiceCard fade-in-up-on-scroll anim-delay-200">
      <div class="ServiceCardIcon"><i class="fas fa-user-shield"></i></div>
      <h3><?= __('index_service3_title') ?></h3>
      <p><?= __('index_service3_desc') ?></p>
    </div>
    <div class="ServiceCard fade-in-up-on-scroll anim-delay-300">
      <div class="ServiceCardIcon"><i class="fas fa-cloud-upload-alt"></i></div>
      <h3><?= __('index_service4_title') ?></h3>
      <p><?= __('index_service4_desc') ?></p>
    </div>
  </div>
</div>

<!-- ===== STATS ===== -->
<div id="numeri" class="section-container StatsSection stats-gradient-section relative overflow-hidden border-y border-solid border-[var(--glass-border)] bg-[radial-gradient(ellipse_60%_50%_at_20%_50%,rgba(var(--primary-color-rgb),0.05),transparent),radial-gradient(ellipse_60%_50%_at_80%_50%,rgba(236,72,153,0.05),transparent),var(--glass-bg)] px-4 py-[50px] backdrop-blur-2xl md:px-5 md:py-20">
  <h2 class="SectionTitle fade-in-up-on-scroll mb-3 text-center text-2xl font-extrabold text-[var(--text-color-headings)] md:text-4xl"><?= __('index_stats_title1') ?> <span class="gradient-text"><?= __('index_stats_title2') ?></span></h2>
  <p class="SectionSubtitle fade-in-up-on-scroll mx-auto mb-12 max-w-[500px] text-center leading-relaxed text-[var(--text-color-secondary)]"><?= __('index_stats_subtitle') ?></p>
  <div class="StatCardContainer stagger-grid relative mx-auto grid max-w-[1200px] grid-cols-2 gap-4 md:grid-cols-3 xl:grid-cols-6 [&_.StatCard]:relative [&_.StatCard]:rounded-2xl [&_.StatCard]:border [&_.StatCard]:border-solid [&_.StatCard]:border-[var(--glass-border)] [&_.StatCard]:bg-[rgba(255,255,255,0.04)] [&_.StatCard]:px-3 [&_.StatCard]:py-6 [&_.StatCard]:text-center [&_.StatCard]:transition [&_.StatCard]:duration-300 [&_.StatCard:hover]:shadow-lg [&_.StatCardIcon]:mb-2 [&_.StatCardIcon]:text-2xl [&_.StatCardIcon]:text-[var(--primary-color)] [&_.StatCardCount]:mb-1 [&_.StatCardCount]:text-2xl [&_.StatCardCount]:font-extrabold [&_.StatCardCount]:tabular-nums [&_.StatCardCount]:text-[var(--text-color-headings)] [&_.StatCardLabel]:text-[0.72rem] [&_.StatCardLabel]:leading-tight [&_.StatCardLabel]:text-[var(--text-color-secondary)]">
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
      <div class="StatCardIcon"><i class="<?= $statIcons[$key] ?? 'fas fa-chart-bar' ?>"></i></div>
      <div class="StatCardCount">
        <?php if (is_numeric($stat["count"])) { ?>
          <span class="stat-number" data-target="<?= intval($stat["count"]) ?>">0</span>
        <?php } else { ?>
          <span class="block break-words text-[0.7em] leading-tight"><?= htmlspecialchars($stat["count"]) ?></span>
        <?php } ?>
      </div>
      <div class="StatCardLabel"><?= htmlspecialchars($stat["label"]) ?></div>
    </div>
    <?php } ?>
  </div>
</div>

<!-- ===== FAQ + CTA ===== -->
<div id="faq" class="section-container FaqCtaSection bg-[var(--bg-color-offset)] px-4 py-[50px] md:px-5 md:py-20">
  <div class="FaqCtaGrid mx-auto grid max-w-[1100px] grid-cols-1 items-start gap-10 lg:grid-cols-[1.2fr_1fr]">
    <div class="FAQsSection">
      <h2 class="SectionTitle SectionTitle--left fade-in-up-on-scroll mb-3 text-left text-2xl font-extrabold text-[var(--text-color-headings)] md:text-4xl"><?= __('index_faq_title1') ?> <span class="gradient-text"><?= __('index_faq_title2') ?></span></h2>
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

    <div class="CtaCard mt-[50px] rounded-[20px] bg-gradient-to-br from-[var(--gradient-start)] to-[var(--gradient-end)] px-8 py-10 text-white lg:sticky lg:top-[200px]">
      <div class="CtaCard__Label mb-3 text-[0.72rem] font-semibold uppercase tracking-[2px] opacity-70"><?= __('index_cta_card_label') ?></div>
      <h3 class="mb-4 text-2xl font-extrabold leading-tight"><?= __('index_cta_title1') ?> <span class="opacity-90"><?= __('index_cta_title2') ?></span></h3>
      <p class="mb-7 text-[0.95rem] leading-relaxed opacity-[0.85]"><?= __('index_cta_desc') ?></p>
      <a href="<?= isset($user) ? './home.php' : './add-game.php' ?>" class="CtaButton inline-flex items-center gap-2 rounded-xl bg-white px-7 py-3.5 font-bold text-indigo-800 no-underline transition hover:-translate-y-0.5 hover:shadow-xl">
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
