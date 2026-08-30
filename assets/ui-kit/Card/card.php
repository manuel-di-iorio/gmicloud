<?php

function ui_card_classes(array $options = []): string {
  $class = $options['class'] ?? '';
  $variant = $options['variant'] ?? 'default';
  $padding = $options['padding'] ?? null;

  $variants = [
    'default' => '',
    'flat' => 'shadow-none',
    'outlined' => 'border-2 shadow-none',
    'interactive' => 'cursor-pointer hover:shadow-card-prominent hover:-translate-y-0.5 active:translate-y-0',
    'elevated' => 'shadow-md hover:shadow-lg',
  ];
  $paddings = ['sm' => 'p-3', 'md' => 'p-5', 'lg' => 'p-7'];
  $paddingClass = $padding ? ($paddings[$padding] ?? $paddings['md']) : '';

  return trim('bg-surface-card border border-solid border-border-color rounded-xl shadow-sm overflow-hidden transition duration-200 ' .
    ($variants[$variant] ?? $variants['default']) . ' ' . $paddingClass . ' ' . $class);
}

function ui_card_title(string $title, array $options = []): string {
  $icon = $options['icon'] ?? null;
  $class = $options['class'] ?? '';
  $iconHtml = $icon
    ? '<i class="' . htmlspecialchars((string) $icon, ENT_QUOTES, 'UTF-8') . ' text-primary-color" aria-hidden="true"></i>'
    : '';

  return '<div class="' . htmlspecialchars(trim('mb-4 flex items-center gap-2 text-[1.05rem] font-semibold text-text-headings ' . $class), ENT_QUOTES, 'UTF-8') . '">' .
    $iconHtml . htmlspecialchars($title, ENT_QUOTES, 'UTF-8') . '</div>';
}

function ui_card($content, $options = []) {
  $class = $options['class'] ?? '';
  $padding = $options['padding'] ?? 'md';
  $variant = $options['variant'] ?? 'default';
  $title = $options['title'] ?? null;
  $header = $options['header'] ?? null;
  $footer = $options['footer'] ?? null;
  $id = isset($options['id']) ? ' id="' . htmlspecialchars($options['id']) . '"' : '';

  $bodyPaddings = [
    'sm' => 'p-3',
    'md' => 'p-5',
    'lg' => 'p-7',
  ];

  $classes = htmlspecialchars(ui_card_classes(['variant' => $variant, 'class' => $class]), ENT_QUOTES, 'UTF-8');
  $bodyPad = $bodyPaddings[$padding] ?? $bodyPaddings['md'];

  $html = '<div class="' . $classes . '"' . $id . '>';

  if ($header) {
    $html .= '<div class="px-5 pt-4 font-semibold text-headings flex items-center gap-2">' . $header . '</div>';
  } elseif ($title) {
    $html .= '<div class="px-5 pt-4 font-semibold text-headings flex items-center gap-2">' . htmlspecialchars($title) . '</div>';
  }

  $html .= '<div class="' . $bodyPad . '">' . $content . '</div>';

  if ($footer) {
    $html .= '<div class="px-5 py-3 border-0 border-t border-solid border-border-color flex items-center gap-2">' . $footer . '</div>';
  }

  $html .= '</div>';

  return $html;
}
