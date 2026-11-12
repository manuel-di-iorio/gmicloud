<?php

function ui_card_classes(array $options = []): string {
  $class = $options['class'] ?? '';
  $padding = $options['padding'] ?? null;

  // Padding is opt-in: cards hosting tables or full-bleed content can omit it.
  $padMap = [
    'none' => '',
    'sm' => 'ds-card--pad-sm',
    'md' => 'ds-card--pad-md',
    'lg' => 'ds-card--pad-lg',
  ];
  $padClass = $padding !== null ? ($padMap[$padding] ?? $padMap['md']) : '';

  return trim('ds-card ' . $padClass . ' ' . $class);
}

function ui_card_title(string $title, array $options = []): string {
  $icon = $options['icon'] ?? null;
  $class = $options['class'] ?? '';
  $iconHtml = $icon ? '<i class="' . htmlspecialchars((string) $icon, ENT_QUOTES, 'UTF-8') . '" aria-hidden="true"></i> ' : '';
  return '<div class="ds-card__title ' . htmlspecialchars($class, ENT_QUOTES, 'UTF-8') . '">' . $iconHtml . htmlspecialchars($title, ENT_QUOTES, 'UTF-8') . '</div>';
}

function ui_card($content, $options = []) {
  $class = $options['class'] ?? '';
  $padding = $options['padding'] ?? 'md';
  $title = $options['title'] ?? null;
  $header = $options['header'] ?? null;
  $footer = $options['footer'] ?? null;
  $media = $options['media'] ?? null;
  $id = isset($options['id']) ? ' id="' . htmlspecialchars($options['id']) . '"' : '';

  $padMap = ['sm' => 'ds-card__body--sm', 'md' => '', 'lg' => 'ds-card__body--lg'];
  $bodyPad = $padMap[$padding] ?? '';

  $html = '<div class="ds-card ' . htmlspecialchars($class, ENT_QUOTES, 'UTF-8') . '"' . $id . '>';
  if ($media) {
    $html .= $media;
  }
  if ($header) {
    $html .= '<div class="ds-card__header">' . $header . '</div>';
  } elseif ($title) {
    $html .= '<div class="ds-card__header">' . htmlspecialchars($title) . '</div>';
  }
  $html .= '<div class="ds-card__body ' . $bodyPad . '">' . $content . '</div>';
  if ($footer) {
    $html .= '<div class="ds-card__footer">' . $footer . '</div>';
  }
  $html .= '</div>';
  return $html;
}
