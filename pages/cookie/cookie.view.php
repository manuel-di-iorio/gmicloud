<div class="internal-page">
  <div class="internal-page__header">
    <h2><?= __('cookie_title') ?></h2>
    <p><?= __('cookie_last_update') ?></p>
  </div>

  <h2><?= __('cookie_section1_title') ?></h2>
  <p><?= __('cookie_section1_text') ?></p>

  <h2><?= __('cookie_section2_title') ?></h2>
  <p><?= __('cookie_section2_text1') ?></p>
  <p><?= __('cookie_section2_text2') ?></p>
  <ul>
    <li><strong><?= __('cookie_section2_bullet1_title') ?></strong> <?= __('cookie_section2_bullet1_text') ?></li>
    <li><strong><?= __('cookie_section2_bullet2_title') ?></strong> <?= __('cookie_section2_bullet2_text') ?></li>
  </ul>

  <h2><?= __('cookie_section3_title') ?></h2>
  <?php if (!empty($config["analytics"])) { ?>
  <p><?= __('cookie_section3_text1') ?></p>
  <p><?= __('cookie_section3_text2') ?></p>
  <?php } ?>
  <p><?= __('cookie_section3_text3') ?></p>

  <h2><?= __('cookie_section4_title') ?></h2>
  <p><?= __('cookie_section4_text1') ?></p>
  <p><?= __('cookie_section4_text2') ?></p>
  <ul>
    <li><a class="text-[var(--primary-color)] hover:underline" href="https://support.google.com/chrome/answer/95647?hl=it" target="_blank" rel="noopener noreferrer">Google Chrome</a></li>
    <li><a class="text-[var(--primary-color)] hover:underline" href="https://support.mozilla.org/it/kb/Gestione%20dei%20cookie" target="_blank" rel="noopener noreferrer">Mozilla Firefox</a></li>
    <li><a class="text-[var(--primary-color)] hover:underline" href="https://support.apple.com/it-it/guide/safari/sfri11471/mac" target="_blank" rel="noopener noreferrer">Apple Safari</a></li>
    <li><a class="text-[var(--primary-color)] hover:underline" href="https://support.microsoft.com/it-it/windows/eliminare-e-gestire-i-cookie-168dab11-0753-043d-7c16-ede5947fc64d" target="_blank" rel="noopener noreferrer">Microsoft Edge</a></li>
  </ul>

  <h2><?= __('cookie_section5_title') ?></h2>
  <p><?= !empty($config["analytics"]) ? __('cookie_section5_text') : __('cookie_section5_text_tech') ?></p>
  <?php if (!empty($config["analytics"])) { ?>
  <p>
    <button type="button" onclick="window.openCookiePreferences&&window.openCookiePreferences()" class="cursor-pointer rounded-md border-0 bg-primary-color px-5 py-2.5 font-semibold text-white"><?= __('cookie_manage_button') ?></button>
  </p>
  <?php } ?>
  <h2><?= __('cookie_table_title') ?></h2>
  <p><?= !empty($config["analytics"]) ? __('cookie_table_text') : __('cookie_table_text_tech') ?></p>

  <h2><?= __('cookie_section6_title') ?></h2>
  <p><?= __('cookie_section6_text') ?></p>

  <h2><?= __('cookie_section7_title') ?></h2>
  <p><?= __('cookie_section7_text') ?></p>
</div>
