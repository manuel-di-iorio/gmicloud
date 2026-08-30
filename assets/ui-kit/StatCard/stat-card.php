<?php

function ui_stat_card($value, string $label, array $options = []): string {
  $icon = $options['icon'] ?? null;
  $variant = $options['variant'] ?? 'primary';
  $class = $options['class'] ?? '';

  $variants = [
    'primary' => 'bg-indigo-500/10 text-indigo-500',
    'success' => 'bg-emerald-500/10 text-emerald-500',
    'warning' => 'bg-amber-500/10 text-amber-500',
    'info' => 'bg-blue-500/10 text-blue-500',
    'pink' => 'bg-pink-500/10 text-pink-500',
    'purple' => 'bg-purple-500/10 text-purple-500',
    'danger' => 'bg-red-500/10 text-red-500',
  ];

  $variantClass = $variants[$variant] ?? $variants['primary'];
  $rootClass = trim('flex items-center gap-4 rounded-xl border border-solid border-border-color bg-surface-card p-5 transition duration-200 hover:border-glass-border-hover hover:shadow-card-subtle ' . $class);
  $iconHtml = $icon
    ? '<div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl text-xl ' . $variantClass . '"><i class="' . htmlspecialchars((string) $icon, ENT_QUOTES, 'UTF-8') . '" aria-hidden="true"></i></div>'
    : '';

  return '<div class="' . htmlspecialchars($rootClass, ENT_QUOTES, 'UTF-8') . '">' .
    $iconHtml .
    '<div class="min-w-0">' .
      '<div class="text-[1.6rem] font-extrabold leading-tight tabular-nums text-text-headings">' . htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8') . '</div>' .
      '<div class="text-[0.82rem] text-text-secondary">' . htmlspecialchars($label, ENT_QUOTES, 'UTF-8') . '</div>' .
    '</div>' .
  '</div>';
}
