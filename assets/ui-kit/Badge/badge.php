<?php

function ui_badge($label, $variant = 'default', $options = []) {
  $icon = $options['icon'] ?? null;
  $class = $options['class'] ?? '';

  $map = [
    'default' => 'ds-badge--neutral', 'neutral' => 'ds-badge--neutral',
    'primary' => 'ds-badge--accent', 'accent' => 'ds-badge--accent',
    'success' => 'ds-badge--success', 'danger' => 'ds-badge--danger',
    'warning' => 'ds-badge--warning', 'info' => 'ds-badge--neutral',
  ];
  $variantClass = $map[$variant] ?? $map['default'];
  $iconHtml = $icon ? '<i class="' . htmlspecialchars($icon) . '"></i> ' : '';

  return '<span class="' . trim("ds-badge $variantClass " . htmlspecialchars($class)) . '">' . $iconHtml . htmlspecialchars($label) . '</span>';
}

function ui_category_label($label, $category = 'tutorial', array $options = []): string {
  $class = $options['class'] ?? '';
  return '<span class="ds-category-label ds-category-label--' . htmlspecialchars($category) . ' ' . htmlspecialchars($class) . '">' . htmlspecialchars($label) . '</span>';
}
