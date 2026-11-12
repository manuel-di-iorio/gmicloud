<?php
function ui_button($label, $variant = 'primary', $size = 'md', $options = []) {
  $icon = $options['icon'] ?? null;
  $iconRight = $options['iconRight'] ?? null;
  $disabled = $options['disabled'] ?? false;
  $loading = $options['loading'] ?? false;
  $type = $options['type'] ?? 'button';
  $href = $options['href'] ?? null;
  $class = $options['class'] ?? '';
  $attrs = $options['attrs'] ?? [];
  $full = $options['full'] ?? false;

  $map = [
    'primary' => 'ds-btn--primary', 'secondary' => 'ds-btn--secondary',
    'accent' => 'ds-btn--accent', 'light' => 'ds-btn--light',
    'outline-light' => 'ds-btn--outline-light', 'ghost' => 'ds-btn--ghost',
    'ghost-light' => 'ds-btn--ghost-light', 'text' => 'ds-btn--text',
    'danger' => 'ds-btn--danger', 'success' => 'ds-btn--success',
    // legacy aliases
    'warning' => 'ds-btn--accent',
  ];
  $sizes = ['sm' => 'ds-btn--sm', 'md' => 'ds-btn--md', 'lg' => 'ds-btn--lg', 'icon' => 'ds-btn--icon'];

  $variantClass = $map[$variant] ?? $map['primary'];
  $sizeClass = $sizes[$size] ?? $sizes['md'];
  $classes = trim("ds-btn $variantClass $sizeClass" . ($full ? ' ds-btn--full' : '') . " $class");

  $loadingHtml = $loading ? '<span class="ds-spinner ds-spinner--sm" aria-hidden="true"></span> ' : '';
  $iconHtml = $icon ? "<i class=\"$icon\"></i> " : '';
  $iconRightHtml = $iconRight ? " <i class=\"$iconRight\"></i>" : '';
  $content = $loadingHtml . $iconHtml . '<span>' . htmlspecialchars($label) . '</span>' . $iconRightHtml;

  $attrStr = '';
  foreach ($attrs as $key => $value) {
    $attrStr .= ' ' . htmlspecialchars($key) . '="' . htmlspecialchars($value) . '"';
  }
  if ($disabled) $attrStr .= ' disabled';

  if ($href && !$disabled) {
    return "<a href=\"" . htmlspecialchars($href) . "\" class=\"$classes\"$attrStr>$content</a>";
  }
  return "<button type=\"" . htmlspecialchars($type) . "\" class=\"$classes\"$attrStr>$content</button>";
}
