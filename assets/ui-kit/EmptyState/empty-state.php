<?php

function ui_empty_state(string $title, array $options = []): string {
  $icon = $options['icon'] ?? null;
  $description = $options['description'] ?? null;
  $action = $options['action'] ?? '';
  $spacious = $options['spacious'] ?? false;
  $class = $options['class'] ?? '';
  $image = $options['image'] ?? '/assets/images/empty_state.svg';

  $descriptions = is_array($description) ? $description : [$description];
  $descriptionHtml = '';
  foreach ($descriptions as $line) {
    if ($line === null || $line === '') continue;
    $descriptionHtml .= '<span class="block">' . htmlspecialchars((string) $line, ENT_QUOTES, 'UTF-8') . '</span>';
  }

  if ($icon) {
    $iconHtml = '<i class="' . htmlspecialchars((string) $icon, ENT_QUOTES, 'UTF-8') . ' mb-3 text-5xl text-content-muted" style="opacity:.35" aria-hidden="true"></i>';
  } else {
    $iconHtml = '<img src="' . htmlspecialchars($image, ENT_QUOTES, 'UTF-8') . '" alt="" aria-hidden="true" class="ds-empty__icon" loading="lazy" onerror="this.style.display=\'none\'">';
  }
  $descriptionBlock = $descriptionHtml ? '<p class="ds-empty__desc">' . $descriptionHtml . '</p>' : '';
  $actionHtml = $action ? '<div class="flex justify-center">' . $action . '</div>' : '';

  return '<div class="' . htmlspecialchars(trim('ds-empty' . ($spacious ? ' ds-empty--spacious' : '') . ' ' . $class), ENT_QUOTES, 'UTF-8') . '">'
    . $iconHtml
    . '<h4 class="ds-empty__title">' . htmlspecialchars($title, ENT_QUOTES, 'UTF-8') . '</h4>'
    . $descriptionBlock . $actionHtml . '</div>';
}
