<?php
/**
 * Site footer component.
 * 
 * Usage:
 *   require_once("includes/footer.php");
 * 
 * Variables expected from the calling page:
 *   $theme        - 'light' or 'dark'
 *   $currentLang  - current language code
 * 
 * Optional: $footerMarginLeft controls whether the footer is flush with the viewport.
 */
$footerMarginLeft = $footerMarginLeft ?? '0!important';
$footerFlushClass = $footerMarginLeft !== '' ? ' !ml-0' : '';
?>
<footer class="modern-footer PageContentFooter mt-[100px] shrink-0 border-0 border-t border-solid border-[var(--footer-border-color)] bg-[var(--footer-bg)] pb-5 pt-10 text-[var(--footer-text-color)] shadow-[var(--shadow-footer)] transition-[margin-left] duration-300 lg:ml-[260px]<?= $footerFlushClass ?>">
  <div class="mx-auto flex max-w-[1200px] flex-wrap items-start justify-between px-[15px]">
    <div class="mb-[30px] basis-full md:mr-5 md:basis-[calc(33.333%-20px)]">
      <a href="/"><img src="/assets/images/logo<?= $theme === 'dark' ? 'White' : '' ?>.png" class="mb-3 h-auto max-w-[120px]" alt="Logo" width="451" height="109"></a>
      <p class="mb-2.5 text-[0.9em] leading-relaxed"><?= __("footer_about") ?></p>
      <p class="mb-2.5 text-[0.9em] leading-relaxed">&copy; <?= date("Y") ?> GameMaker Italia. <?= __("footer_copyright") ?></p>
    </div>
    <div class="mb-[30px] basis-full md:mr-5 md:basis-[calc(33.333%-20px)]">
      <h5 class="mb-[15px] mt-0 text-[1.2em] font-semibold text-[var(--footer-heading-color)]"><?= __("footer_links_title") ?></h5>
      <ul class="m-0 list-none p-0">
        <li class="mb-2"><a href="/documentation.php" class="text-[0.9em] text-[var(--footer-link-color)] no-underline transition-colors hover:text-[var(--footer-link-hover-color)] hover:underline"><?= __("footer_documentation") ?></a></li>
        <li class="mb-2"><a href="/terms.php" class="text-[0.9em] text-[var(--footer-link-color)] no-underline transition-colors hover:text-[var(--footer-link-hover-color)] hover:underline"><?= __("footer_terms") ?></a></li>
        <li class="mb-2"><a href="/privacy.php" class="text-[0.9em] text-[var(--footer-link-color)] no-underline transition-colors hover:text-[var(--footer-link-hover-color)] hover:underline"><?= __("footer_privacy") ?></a></li>
        <li class="mb-2"><a href="/cookie.php" class="text-[0.9em] text-[var(--footer-link-color)] no-underline transition-colors hover:text-[var(--footer-link-hover-color)] hover:underline"><?= __("footer_cookie") ?></a></li>
        <li class="mb-2"><a href="https://github.com/manuel-di-iorio/gmicloud/issues" class="text-[0.9em] text-[var(--footer-link-color)] no-underline transition-colors hover:text-[var(--footer-link-hover-color)] hover:underline" target="_blank" rel="noopener noreferrer"><?= __("footer_report_issue") ?></a></li>
      </ul>
    </div>
    <div class="mb-[30px] basis-full md:basis-[calc(33.333%-20px)]">
      <h5 class="mb-[15px] mt-0 text-[1.2em] font-semibold text-[var(--footer-heading-color)]"><?= __("footer_follow_title") ?></h5>
      <a href="https://discord.gg/85RCMD9VQD" class="mr-[15px] text-2xl text-[var(--footer-link-color)] transition-colors hover:text-[var(--footer-link-hover-color)]" target="_blank" rel="noopener noreferrer" aria-label="Discord"><i class="fab fa-discord"></i></a>
      <a href="https://www.facebook.com/gmitalia" class="mr-[15px] text-2xl text-[var(--footer-link-color)] transition-colors hover:text-[var(--footer-link-hover-color)]" target="_blank" rel="noopener noreferrer" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a>
      <a href="https://twitter.com/gamemakerita" class="mr-[15px] text-2xl text-[var(--footer-link-color)] transition-colors hover:text-[var(--footer-link-hover-color)]" target="_blank" rel="noopener noreferrer" aria-label="Twitter"><i class="fab fa-twitter"></i></a>
      <a href="https://github.com/manuel-di-iorio/gmicloud" class="text-2xl text-[var(--footer-link-color)] transition-colors hover:text-[var(--footer-link-hover-color)]" target="_blank" rel="noopener noreferrer" aria-label="GitHub"><i class="fab fa-github"></i></a>
    </div>
  </div>
  <div class="flex flex-wrap items-center justify-center gap-[50px] border-0 border-t border-solid border-[var(--footer-border-color)] pt-5 text-center text-[0.85em]">
    <a href="/switch-theme.php?theme=<?= $theme === 'dark' ? 'light' : 'dark' ?>&go=<?= urlencode($_SERVER["REQUEST_URI"]) ?>" class="inline-flex items-center gap-1.5 text-[0.82rem] text-text-secondary no-underline transition-colors hover:text-text">
      <i class="fas <?= $theme === 'dark' ? 'fa-sun' : 'fa-moon' ?>"></i> <?= __('index_theme_toggle') ?>
    </a>
    <div class="flex items-center gap-2">
      <a href="/switch-lang.php?lang=en&go=<?= urlencode($_SERVER["REQUEST_URI"]) ?>" class="text-[0.82rem] no-underline transition-colors hover:text-text <?= $currentLang === 'en' ? 'font-semibold text-indigo-400' : 'text-text-secondary' ?>"><?= __("lang_en") ?></a>
      <span class="text-[0.82rem] text-text-secondary opacity-40">|</span>
      <a href="/switch-lang.php?lang=it&go=<?= urlencode($_SERVER["REQUEST_URI"]) ?>" class="text-[0.82rem] no-underline transition-colors hover:text-text <?= $currentLang === 'it' ? 'font-semibold text-indigo-400' : 'text-text-secondary' ?>"><?= __("lang_it") ?></a>
      <span class="text-[0.82rem] text-text-secondary opacity-40">|</span>
      <a href="/switch-lang.php?lang=es&go=<?= urlencode($_SERVER["REQUEST_URI"]) ?>" class="text-[0.82rem] no-underline transition-colors hover:text-text <?= $currentLang === 'es' ? 'font-semibold text-indigo-400' : 'text-text-secondary' ?>"><?= __("lang_es") ?></a>
      <span class="text-[0.82rem] text-text-secondary opacity-40">|</span>
      <a href="/switch-lang.php?lang=fr&go=<?= urlencode($_SERVER["REQUEST_URI"]) ?>" class="text-[0.82rem] no-underline transition-colors hover:text-text <?= $currentLang === 'fr' ? 'font-semibold text-indigo-400' : 'text-text-secondary' ?>"><?= __("lang_fr") ?></a>
      <span class="text-[0.82rem] text-text-secondary opacity-40">|</span>
      <a href="/switch-lang.php?lang=de&go=<?= urlencode($_SERVER["REQUEST_URI"]) ?>" class="text-[0.82rem] no-underline transition-colors hover:text-text <?= $currentLang === 'de' ? 'font-semibold text-indigo-400' : 'text-text-secondary' ?>"><?= __("lang_de") ?></a>
    </div>
  </div>
</footer>
