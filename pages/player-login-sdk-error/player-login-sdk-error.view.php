<div style="position:fixed;left:2rem;top:1.75rem;z-index:10">
  <img src="/assets/images/logo.png" alt="Platform Logo" style="height:2.5rem;opacity:0.85">
</div>

<div class="flex min-h-screen items-center justify-center px-5 py-10">
  <div class="ds-card" style="width:100%;max-width:500px;text-align:center">
    <div class="ds-card__body" style="padding:4rem 2.5rem 1.5rem 2.5rem">
      <div class="ds-stat__icon" style="margin:0 auto 1.75rem auto;width:5.5rem;height:5.5rem;font-size:3rem;color:var(--ds-color-error)"><i class="fas fa-times"></i></div>
      <h2 style="margin:0 0 0.625rem 0;font-size:1.5rem;font-weight:700"><?= __('error_login') ?></h2>
      <p class="ds-muted" style="margin:0 0 2.25rem 0;font-size:0.92rem;line-height:1.6"><?= htmlspecialchars($loginError) ?></p>
    </div>
  </div>
</div>
