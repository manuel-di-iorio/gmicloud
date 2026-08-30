<div class="fixed left-8 top-7 z-10">
  <img src="/assets/images/logo<?= $theme === 'dark' ? 'White' : '' ?>.svg" alt="Platform Logo" class="h-10 opacity-[0.85] transition-opacity hover:opacity-100">
</div>

<div class="relative flex h-screen min-h-screen items-center justify-center overflow-hidden bg-[radial-gradient(ellipse_at_20%_50%,rgba(239,68,68,0.08)_0%,transparent_50%),radial-gradient(ellipse_at_80%_20%,rgba(248,113,113,0.06)_0%,transparent_50%),radial-gradient(ellipse_at_50%_80%,rgba(239,68,68,0.05)_0%,transparent_50%),var(--bg-color)] px-5 py-10">
  <div class="relative z-[1] w-full max-w-[500px] rounded-[20px] border border-solid border-[var(--border-color)] bg-[var(--bg-color-card)] px-10 pb-6 pt-16 text-center shadow-[0_1px_3px_rgba(0,0,0,0.04),0_8px_32px_rgba(0,0,0,0.06)]">
    <div class="mx-auto mb-7 flex h-[88px] w-[88px] animate-pulse items-center justify-center rounded-full bg-red-500/10">
      <i class="fas fa-times text-5xl text-red-500"></i>
    </div>
    <h2 class="mb-2.5 mt-0 text-2xl font-bold text-[var(--text-color-headings)]"><?= __('error_login') ?></h2>
    <p class="mb-9 mt-0 text-[0.92rem] leading-6 text-[var(--text-color-secondary)]"><?= htmlspecialchars($loginError) ?></p>
  </div>
</div>