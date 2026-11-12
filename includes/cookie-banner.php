<?php $cmpEnabled = !empty($config["analytics"]); ?>
<div id="cookie-banner" class="ds-cookie hidden" role="dialog" aria-live="polite" aria-label="<?= htmlspecialchars(__("cookie_banner_title")) ?>">
  <div style="flex:1">
    <p><?= $cmpEnabled ? __("cookie_banner_text") : __("cookie_banner_text_tech") ?> <a class="ds-link" href="/cookie.php"><?= __("cookie_banner_link") ?></a></p>
  </div>
  <div class="ds-cookie__actions">
    <?php if ($cmpEnabled) { ?>
    <button id="reject-cookie-banner" type="button" class="ds-btn ds-btn--secondary ds-btn--md"><?= __("cookie_banner_reject") ?></button>
    <button id="customize-cookie-banner" type="button" class="ds-btn ds-btn--secondary ds-btn--md"><?= __("cookie_banner_customize") ?></button>
    <button id="accept-cookie-banner" type="button" class="ds-btn ds-btn--primary ds-btn--md"><?= __("cookie_banner_accept") ?></button>
    <?php } else { ?>
    <button id="close-cookie-banner" type="button" class="ds-btn ds-btn--primary ds-btn--md"><?= __("cookie_banner_close") ?></button>
    <?php } ?>
  </div>
</div>
<?php if (!empty($config["analytics"])) { ?>
<div id="cookie-preferences-modal" class="ds-cookie-modal hidden" role="dialog" aria-modal="true" aria-labelledby="cookie-pref-title">
  <div class="ds-cookie-dialog">
    <h3 id="cookie-pref-title" style="margin-top:0"><?= __("cookie_pref_title") ?></h3>
    <p class="ds-muted" style="font-size:0.9rem"><?= __("cookie_pref_desc") ?></p>
    <label style="display:flex;align-items:flex-start;justify-content:space-between;gap:1rem;border-bottom:1px solid var(--ds-color-border);padding:0.75rem 0">
      <span><strong><?= __("cookie_pref_necessary_title") ?></strong><br><span class="ds-muted" style="font-size:0.85rem"><?= __("cookie_pref_necessary_desc") ?></span></span>
      <input type="checkbox" checked disabled>
    </label>
    <label style="display:flex;align-items:flex-start;justify-content:space-between;gap:1rem;padding:0.75rem 0">
      <span><strong><?= __("cookie_pref_analytics_title") ?></strong><br><span class="ds-muted" style="font-size:0.85rem"><?= __("cookie_pref_analytics_desc") ?></span></span>
      <input type="checkbox" id="cookie-pref-analytics">
    </label>
    <div style="display:flex;flex-wrap:wrap;justify-content:flex-end;gap:0.5rem;margin-top:1rem">
      <button id="save-cookie-preferences" type="button" class="ds-btn ds-btn--primary ds-btn--md"><?= __("cookie_pref_save") ?></button>
      <button id="close-cookie-preferences" type="button" class="ds-btn ds-btn--secondary ds-btn--md"><?= __("cookie_pref_close") ?></button>
    </div>
  </div>
</div>
<?php } ?>
<script src="/assets/js/consent.js?v=<?= asset_version('assets/js/consent.js') ?>"></script>
