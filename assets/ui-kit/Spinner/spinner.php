<?php
function ui_spinner(string $size = 'md', array $attrs = []): string {
  $sizeClasses = [
    'sm' => 'h-3 w-3 border-[1.5px]',
    'md' => 'h-3.5 w-3.5 border-2',
    'lg' => 'h-5 w-5 border-[2.5px]',
    'xl' => 'h-8 w-8 border-[3px] border-[var(--border-color)] border-t-[#5865f2] [animation-duration:0.8s]',
  ];
  $sizeClass = $sizeClasses[$size] ?? $sizeClasses['md'];
  $attrStr = '';
  foreach ($attrs as $k => $v) {
    $attrStr .= ' ' . htmlspecialchars($k) . '="' . htmlspecialchars($v) . '"';
  }
  return '<span class="inline-block animate-spin rounded-full border-solid border-current border-r-transparent align-middle [animation-duration:0.6s] ' . $sizeClass . '"' . $attrStr . '></span>';
}

function ui_spinner_block(string $label = '', string $size = 'xl', array $attrs = []): string {
  $attrStr = '';
  foreach ($attrs as $k => $v) {
    $attrStr .= ' ' . htmlspecialchars($k) . '="' . htmlspecialchars($v) . '"';
  }
  $labelHtml = $label ? '<span class="text-[0.95em] text-text-secondary">' . htmlspecialchars($label) . '</span>' : '';
  return '<div class="flex flex-col items-center gap-4"' . $attrStr . '>' . ui_spinner($size) . $labelHtml . '</div>';
}
