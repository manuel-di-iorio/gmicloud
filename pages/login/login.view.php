<div class="flex min-h-[calc(100vh-212px)] items-center justify-center bg-[radial-gradient(ellipse_at_20%_50%,rgba(88,101,242,0.06)_0%,transparent_50%),radial-gradient(ellipse_at_80%_20%,rgba(129,140,248,0.04)_0%,transparent_50%),var(--bg-color-offset)] px-5 pb-10 pt-16">
  <div class="w-full max-w-[500px] rounded-[20px] border border-solid border-[var(--border-color)] bg-[var(--bg-color-card)] px-8 pb-11 pt-12 text-center shadow-[0_1px_3px_rgba(0,0,0,0.04),0_8px_32px_rgba(0,0,0,0.06)] sm:px-12 sm:pt-[52px]">
    <div class="mx-auto mb-6 flex h-[72px] w-[72px] items-center justify-center rounded-[20px] bg-gradient-to-br from-[#5865f2] to-indigo-400 shadow-[0_4px_16px_rgba(88,101,242,0.3)]">
      <i class="fab fa-discord text-3xl text-white"></i>
    </div>
    <h2 class="mb-7 text-[1.45rem] font-bold text-[var(--text-color-headings)]"><?= __('login_title') ?></h2>

    <?= ui_button(__('login_button'), 'primary', 'lg', [
      'icon' => 'fab fa-discord',
      'href' => $loginRedirectUrl,
      'full' => true,
      'class' => '!border-0 !bg-[#5865f2] shadow-[0_2px_8px_rgba(88,101,242,0.25)] hover:!-translate-y-0.5 hover:!bg-[#4752c4] hover:shadow-[0_6px_20px_rgba(88,101,242,0.35)]'
    ]) ?>

    <div class="mt-6 text-[0.78rem] leading-6 text-[var(--text-color-secondary)] [&_a]:text-[#5865f2] [&_a]:no-underline hover:[&_a]:underline">
      <?= __('login_disclaimer') ?>
      <a href="terms.php" target="_blank"><?= __('login_terms') ?></a>,
      <a href="privacy.php" target="_blank"><?= __('login_privacy') ?></a> e
      <a href="cookie.php" target="_blank"><?= __('login_cookie') ?></a>.
    </div>
  </div>
</div>
