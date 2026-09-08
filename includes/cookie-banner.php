<?php $cmpEnabled = !empty($config["analytics"]); ?>
<div id="cookie-banner" class="fixed inset-x-0 bottom-0 z-[90000] hidden items-center justify-between gap-5 border-0 border-t-[3px] border-solid border-primary-color bg-cookie-banner-bg px-5 py-5 text-cookie-banner-text shadow-[0_-2px_15px_rgba(0,0,0,0.2)] sm:px-[30px] [&_a]:font-medium [&_a]:text-cookie-banner-link [&_a]:underline hover:[&_a]:text-cookie-banner-link-hover [&_p]:m-0 [&_p]:text-[0.95rem] [&_p]:leading-relaxed" role="dialog" aria-live="polite" aria-label="<?= htmlspecialchars(__("cookie_banner_title")) ?>">
  <div class="flex grow items-center justify-center">
    <p><?= $cmpEnabled ? __("cookie_banner_text") : __("cookie_banner_text_tech") ?> <a href="/cookie.php"><?= __("cookie_banner_link") ?></a></p>
  </div>
  <div class="ml-5 flex shrink-0 flex-wrap gap-2">
    <?php if ($cmpEnabled) { ?>
    <button id="reject-cookie-banner" type="button" class="cursor-pointer whitespace-nowrap rounded-md border border-solid border-primary-color bg-transparent px-5 py-2.5 font-semibold text-cookie-banner-text transition duration-200"><?= __("cookie_banner_reject") ?></button>
    <button id="customize-cookie-banner" type="button" class="cursor-pointer whitespace-nowrap rounded-md border border-solid border-primary-color bg-transparent px-5 py-2.5 font-semibold text-cookie-banner-text transition duration-200"><?= __("cookie_banner_customize") ?></button>
    <button id="accept-cookie-banner" type="button" class="cursor-pointer whitespace-nowrap rounded-md border-0 bg-primary-color px-6 py-2.5 font-semibold text-white transition duration-200 hover:bg-primary-color-dark"><?= __("cookie_banner_accept") ?></button>
    <?php } else { ?>
    <button id="close-cookie-banner" type="button" class="cursor-pointer whitespace-nowrap rounded-md border-0 bg-primary-color px-6 py-2.5 font-semibold text-white transition duration-200 hover:bg-primary-color-dark"><?= __("cookie_banner_close") ?></button>
    <?php } ?>
  </div>
</div>
<?php if (!empty($config["analytics"])) { ?>
<div id="cookie-preferences-modal" class="fixed inset-0 z-[90001] hidden items-center justify-center bg-black/60 p-4" role="dialog" aria-modal="true" aria-labelledby="cookie-pref-title">
  <div class="w-full max-w-[560px] rounded-lg bg-surface-card p-6 text-text shadow-card-lg">
    <h3 id="cookie-pref-title" class="mt-0"><?= __("cookie_pref_title") ?></h3>
    <p class="text-[0.9rem] text-text-secondary"><?= __("cookie_pref_desc") ?></p>
    <label class="mt-4 flex items-start justify-between gap-4 border-0 border-b border-solid border-table-border py-3">
      <span><strong><?= __("cookie_pref_necessary_title") ?></strong><br><span class="text-[0.85rem] text-text-secondary"><?= __("cookie_pref_necessary_desc") ?></span></span>
      <input type="checkbox" checked disabled>
    </label>
    <label class="mt-2 flex items-start justify-between gap-4 py-3">
      <span><strong><?= __("cookie_pref_analytics_title") ?></strong><br><span class="text-[0.85rem] text-text-secondary"><?= __("cookie_pref_analytics_desc") ?></span></span>
      <input type="checkbox" id="cookie-pref-analytics">
    </label>
    <div class="mt-4 flex flex-wrap justify-end gap-2">
      <button id="save-cookie-preferences" type="button" class="cursor-pointer rounded-md border-0 bg-primary-color px-5 py-2.5 font-semibold text-white"><?= __("cookie_pref_save") ?></button>
      <button id="close-cookie-preferences" type="button" class="cursor-pointer rounded-md border border-solid border-table-border bg-transparent px-5 py-2.5 text-text"><?= __("cookie_pref_close") ?></button>
    </div>
  </div>
</div>
<?php } ?>
<script src="/assets/js/consent.js?v=<?= asset_version('assets/js/consent.js') ?>"></script>
