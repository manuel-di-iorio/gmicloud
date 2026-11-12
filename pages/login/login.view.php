<div class="flex min-h-[calc(100vh-212px)] items-center justify-center px-5 pb-10 pt-16">
  <div class="ds-login-card w-full" style="text-align:center">
    <div class="ds-stat__icon" style="margin:0 auto 1.5rem auto;width:4.5rem;height:4.5rem;font-size:1.875rem"><i class="fab fa-discord"></i></div>
    <h2 style="margin:0 0 1.75rem 0;font-size:1.45rem;font-weight:700"><?= __('login_title') ?></h2>

    <?= ui_button(__('login_button'), 'primary', 'lg', [
      'icon' => 'fab fa-discord',
      'href' => $loginRedirectUrl,
      'full' => true,
    ]) ?>

    <div class="ds-hint" style="margin-top:1.5rem;line-height:1.6">
      <?= __('login_disclaimer') ?>
      <a class="ds-link" href="terms.php" target="_blank"><?= __('login_terms') ?></a>,
      <a class="ds-link" href="privacy.php" target="_blank"><?= __('login_privacy') ?></a>,
      <a class="ds-link" href="cookie.php" target="_blank"><?= __('login_cookie') ?></a>.
    </div>
  </div>
</div>
