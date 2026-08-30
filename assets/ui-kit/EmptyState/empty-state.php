<?php

function ui_empty_state(string $title, array $options = []): string {
  $icon = $options['icon'] ?? null;
  $description = $options['description'] ?? null;
  $action = $options['action'] ?? '';
  $spacious = $options['spacious'] ?? false;
  $class = $options['class'] ?? '';

  $descriptions = is_array($description) ? $description : [$description];
  $descriptionHtml = '';

  foreach ($descriptions as $line) {
    if ($line === null || $line === '') continue;
    $descriptionHtml .= '<span class="block">' . htmlspecialchars((string) $line, ENT_QUOTES, 'UTF-8') . '</span>';
  }

  $iconHtml = $icon
    ? '<i class="' . htmlspecialchars((string) $icon, ENT_QUOTES, 'UTF-8') . ' mb-3 text-5xl text-text-secondary opacity-35" aria-hidden="true"></i>'
    : '';
  $descriptionBlock = $descriptionHtml
    ? '<p class="mb-5 mt-0 text-[0.95rem] leading-6 text-text-secondary">' . $descriptionHtml . '</p>'
    : '';
  $actionHtml = $action
    ? '<div class="flex justify-center">' . $action . '</div>'
    : '';
  $padding = $spacious ? 'px-8 py-20' : 'px-8 py-14';
  $classes = trim('mt-4 rounded-xl border border-dashed border-border-color bg-surface-card text-center ' . $padding . ' ' . $class);

  return '<div class="' . htmlspecialchars($classes, ENT_QUOTES, 'UTF-8') . '">' .
    $iconHtml .
    '<h4 class="mb-2 mt-0 font-semibold text-text-headings">' . htmlspecialchars($title, ENT_QUOTES, 'UTF-8') . '</h4>' .
    $descriptionBlock .
    $actionHtml .
    '</div>';
}
