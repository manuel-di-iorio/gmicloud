<?php
function ui_spinner(string $size = 'md', array $attrs = []): string {
  $sizes = ['sm' => 'ds-spinner--sm', 'md' => 'ds-spinner--md', 'lg' => 'ds-spinner--lg', 'xl' => 'ds-spinner--xl'];
  $sizeClass = $sizes[$size] ?? $sizes['md'];
  $attrStr = '';
  foreach ($attrs as $k => $v) $attrStr .= ' ' . htmlspecialchars($k) . '="' . htmlspecialchars($v) . '"';
  return '<span class="ds-spinner ' . $sizeClass . '"' . $attrStr . '></span>';
}

function ui_spinner_block(string $label = '', string $size = 'xl', array $attrs = []): string {
  $attrStr = '';
  foreach ($attrs as $k => $v) $attrStr .= ' ' . htmlspecialchars($k) . '="' . htmlspecialchars($v) . '"';
  $labelHtml = $label ? '<span class="ds-muted">' . htmlspecialchars($label) . '</span>' : '';
  return '<div style="display:flex;flex-direction:column;align-items:center;gap:1rem"' . $attrStr . '>' . ui_spinner($size) . $labelHtml . '</div>';
}
