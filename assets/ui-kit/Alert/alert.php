<?php

function ui_alert(string $message, string $variant = 'info', array $options = []): string {
  $title = $options['title'] ?? '';
  $icon = $options['icon'] ?? null;
  $class = $options['class'] ?? '';

  $variants = [
    'info' => ['border-blue-500/30 bg-blue-500/10 text-blue-700 dark:text-blue-300', 'fas fa-info-circle'],
    'success' => ['border-emerald-500/30 bg-emerald-500/10 text-emerald-700 dark:text-emerald-300', 'fas fa-check-circle'],
    'warning' => ['border-amber-500/30 bg-amber-500/10 text-amber-700 dark:text-amber-300', 'fas fa-exclamation-triangle'],
    'danger' => ['border-red-500/30 bg-red-500/10 text-red-700 dark:text-red-300', 'fas fa-exclamation-circle'],
  ];

  [$variantClass, $defaultIcon] = $variants[$variant] ?? $variants['info'];
  $iconClass = $icon ?? $defaultIcon;
  $titleHtml = $title
    ? '<div class="mb-1 font-semibold">' . htmlspecialchars((string) $title, ENT_QUOTES, 'UTF-8') . '</div>'
    : '';

  return '<div class="' . htmlspecialchars(trim('mb-4 flex items-start gap-3 rounded-lg border border-solid px-4 py-3 text-sm ' . $variantClass . ' ' . $class), ENT_QUOTES, 'UTF-8') . '" role="alert">' .
    '<i class="' . htmlspecialchars((string) $iconClass, ENT_QUOTES, 'UTF-8') . ' mt-0.5 shrink-0" aria-hidden="true"></i>' .
    '<div>' . $titleHtml . '<div>' . htmlspecialchars($message, ENT_QUOTES, 'UTF-8') . '</div></div>' .
  '</div>';
}
