<?php
/**
 * Site footer — white, bordered, uppercase titles, square social icons.
 */
$footerMarginLeft = $footerMarginLeft ?? '0!important';
$footerFlush = $footerMarginLeft !== '' ? ' ds-footer--full' : '';
?>
<footer class="ds-footer<?= $footerFlush ?>">
  <div class="ds-footer__grid">
    <div class="ds-footer__col">
      <a href="/"><img src="/assets/images/logo.png" style="margin-bottom:0.75rem;max-width:120px;height:auto" alt="Logo" width="451" height="109"></a>
      <p style="font-size:0.9em;line-height:1.6"><?= __("footer_about") ?></p>
      <p style="font-size:0.9em;line-height:1.6">&copy; <?= date("Y") ?> GameMaker Italia. <?= __("footer_copyright") ?></p>
    </div>
    <div class="ds-footer__col">
      <h5><?= __("footer_links_title") ?></h5>
      <ul>
        <li><a class="ds-footer__link" href="/documentation.php"><?= __("footer_documentation") ?></a></li>
        <li><a class="ds-footer__link" href="/terms.php"><?= __("footer_terms") ?></a></li>
        <li><a class="ds-footer__link" href="/privacy.php"><?= __("footer_privacy") ?></a></li>
        <li><a class="ds-footer__link" href="/cookie.php"><?= __("footer_cookie") ?></a></li>
        <?php if (!empty($config["analytics"])) { ?><li><a class="ds-footer__link" href="#" onclick="window.openCookiePreferences&&window.openCookiePreferences();return false;"><?= __("footer_manage_consent") ?></a></li><?php } ?>
        <li><a class="ds-footer__link" href="https://github.com/manuel-di-iorio/gmicloud/issues" target="_blank" rel="noopener noreferrer"><?= __("footer_report_issue") ?></a></li>
      </ul>
    </div>
    <div class="ds-footer__col">
      <h5><?= __("footer_follow_title") ?></h5>
      <?= ui_social_icon("https://discord.gg/85RCMD9VQD", "fab fa-discord", ["label" => "Discord"]) ?>
      <?= ui_social_icon("https://www.facebook.com/gmitalia", "fab fa-facebook-f", ["label" => "Facebook"]) ?>
      <?= ui_social_icon("https://twitter.com/gamemakerita", "fab fa-twitter", ["label" => "Twitter"]) ?>
      <?= ui_social_icon("https://github.com/manuel-di-iorio/gmicloud", "fab fa-github", ["label" => "GitHub"]) ?>
    </div>
  </div>
  <div class="ds-footer__bottom">
    <div class="ds-footer__langs" style="display:flex;align-items:center;gap:0.5rem;justify-content:center">
      <a href="/switch-lang.php?lang=en&go=<?= urlencode($_SERVER["REQUEST_URI"]) ?>" <?= $currentLang === 'en' ? 'aria-current="true"' : '' ?>><?= __("lang_en") ?></a>
      <span style="opacity:.4">|</span>
      <a href="/switch-lang.php?lang=it&go=<?= urlencode($_SERVER["REQUEST_URI"]) ?>" <?= $currentLang === 'it' ? 'aria-current="true"' : '' ?>><?= __("lang_it") ?></a>
      <span style="opacity:.4">|</span>
      <a href="/switch-lang.php?lang=es&go=<?= urlencode($_SERVER["REQUEST_URI"]) ?>" <?= $currentLang === 'es' ? 'aria-current="true"' : '' ?>><?= __("lang_es") ?></a>
      <span style="opacity:.4">|</span>
      <a href="/switch-lang.php?lang=fr&go=<?= urlencode($_SERVER["REQUEST_URI"]) ?>" <?= $currentLang === 'fr' ? 'aria-current="true"' : '' ?>><?= __("lang_fr") ?></a>
      <span style="opacity:.4">|</span>
      <a href="/switch-lang.php?lang=de&go=<?= urlencode($_SERVER["REQUEST_URI"]) ?>" <?= $currentLang === 'de' ? 'aria-current="true"' : '' ?>><?= __("lang_de") ?></a>
    </div>
  </div>
</footer>
