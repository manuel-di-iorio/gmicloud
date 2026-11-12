<?php

function ui_alert(string $message, string $variant = 'info', array $options = []): string {
  $title = $options['title'] ?? '';
  $icon = $options['icon'] ?? null;
  $class = $options['class'] ?? '';

  $allowed = ['info' => 'info', 'success' => 'success', 'warning' => 'warning', 'danger' => 'danger', 'error' => 'danger'];
  $variantKey = $allowed[$variant] ?? 'info';
  $defaultIcons = ['info' => 'fas fa-info-circle', 'success' => 'fas fa-check-circle', 'warning' => 'fas fa-exclamation-triangle', 'danger' => 'fas fa-exclamation-circle'];
  $iconClass = $icon ?? $defaultIcons[$variantKey];
  $titleHtml = $title ? '<div class="ds-alert__title">' . htmlspecialchars((string) $title, ENT_QUOTES, 'UTF-8') . '</div>' : '';

  return '<div class="' . htmlspecialchars(trim('ds-alert ds-alert--' . $variantKey . ' ' . $class), ENT_QUOTES, 'UTF-8') . '" role="alert">' .
    '<i class="' . htmlspecialchars((string) $iconClass, ENT_QUOTES, 'UTF-8') . ' ds-alert__icon" aria-hidden="true"></i>' .
    '<div>' . $titleHtml . '<div>' . htmlspecialchars($message, ENT_QUOTES, 'UTF-8') . '</div></div>' .
  '</div>';
}
