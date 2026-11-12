<?php

function ui_stat_card($value, string $label, array $options = []): string {
  $icon = $options['icon'] ?? null;
  $class = $options['class'] ?? '';

  $rootClass = trim('ds-stat ' . $class);
  $iconHtml = $icon
    ? '<div class="ds-stat__icon"><i class="' . htmlspecialchars((string) $icon, ENT_QUOTES, 'UTF-8') . '" aria-hidden="true"></i></div>'
    : '';

  return '<div class="' . htmlspecialchars($rootClass, ENT_QUOTES, 'UTF-8') . '">'
    . $iconHtml
    . '<div class="min-w-0">'
    . '<div class="ds-stat__value">' . htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8') . '</div>'
    . '<div class="ds-stat__label">' . htmlspecialchars($label, ENT_QUOTES, 'UTF-8') . '</div>'
    . '</div></div>';
}
