<?php
/**
 * ui_page_header($title, $options) — flat editorial header, bottom border only.
 */
function ui_page_header(string $title, array $options = []): string {
  $desc       = $options['desc']        ?? '';
  $badge      = $options['badge']       ?? '';
  $badgeColor = $options['badge_color'] ?? '';
  $backUrl    = $options['back_url']    ?? '';
  $backLabel  = $options['back_label']  ?? '';
  $extraClass = $options['class']       ?? '';

  $backHtml = '';
  if ($backUrl) {
    $label = htmlspecialchars((string) $backLabel, ENT_QUOTES, 'UTF-8');
    $backHtml = '<a href="' . htmlspecialchars($backUrl, ENT_QUOTES, 'UTF-8') . '" class="ds-page-header__back"'
      . ($label ? ' title="' . $label . '" aria-label="' . $label . '" data-tippy-content="' . $label . '"' : '') . '>'
      . '<i class="fas fa-arrow-left" aria-hidden="true"></i></a>';
  }

  $badgeHtml = '';
  if ($badge) {
    $badgeHtml = '<div class="ds-page-header__badge">' . ui_badge($badge, 'neutral') . '</div>';
  }

  $descHtml = '';
  if ($desc) {
    $descHtml = '<p class="ds-page-header__desc">' . htmlspecialchars((string) $desc, ENT_QUOTES, 'UTF-8') . '</p>';
  }

  return '<div class="ds-page-header ui-page-header ' . htmlspecialchars($extraClass, ENT_QUOTES, 'UTF-8') . '">'
    . '<div class="ds-page-header__row">'
    . $backHtml
    . '<div class="min-w-0">' . $badgeHtml
    . '<h1 class="ui-page-header_h1">' . htmlspecialchars($title, ENT_QUOTES, 'UTF-8') . '</h1>'
    . $descHtml . '</div>'
    . '<div class="ds-page-header__slot ui-page-header__slot" id="page-header-slot"></div>'
    . '</div></div>';
}

function ui_breadcrumbs(array $items, array $options = []): string {
  $class = $options['class'] ?? '';
  $id = $options['id'] ?? '';
  if (empty($items)) return '';
  $html = '<nav aria-label="Breadcrumb"'
    . ($id ? ' id="' . htmlspecialchars($id) . '"' : '')
    . ' class="ds-breadcrumbs ' . htmlspecialchars($class) . '"><ol>';
  $n = count($items);
  foreach ($items as $i => $item) {
    $label = $item['label'] ?? '';
    $href = $item['href'] ?? null;
    $isLast = $i === $n - 1;
    $isCurrent = $item['current'] ?? $isLast;
    $forceLink = !empty($item['link']);
    $hidden = !empty($item['hidden']);
    $attrs = $item['attrs'] ?? [];
    $attrStr = '';
    foreach ($attrs as $k => $v) {
      $attrStr .= ' ' . $k . ($v === '' ? '' : '="' . htmlspecialchars((string) $v) . '"');
    }
    $html .= '<li' . ($hidden ? ' hidden' : '') . '>';
    if ($i > 0) $html .= '<span aria-hidden="true">/</span>';
    if ($href && (!$isCurrent || $forceLink)) {
      $html .= '<a href="' . htmlspecialchars($href) . '"'
        . ($isCurrent ? ' aria-current="page"' : '') . $attrStr . '>'
        . htmlspecialchars($label) . '</a>';
    } else {
      $html .= '<span' . ($isCurrent ? ' aria-current="page"' : '') . $attrStr . '>'
        . htmlspecialchars($label) . '</span>';
    }
    $html .= '</li>';
  }
  $html .= '</ol></nav>';
  return $html;
}

function ui_area_title(string $title, array $options = []): string {
  $inverted = $options['inverted'] ?? false;
  $class = $options['class'] ?? '';
  return '<h3 class="ds-area-title' . ($inverted ? ' ds-area-title--inverted' : '') . ' ' . htmlspecialchars($class) . '">' . htmlspecialchars($title) . '</h3>';
}

function ui_social_icon(string $href, string $icon, array $options = []): string {
  $label = $options['label'] ?? '';
  $class = $options['class'] ?? '';
  return '<a href="' . htmlspecialchars($href) . '" class="ds-social ' . htmlspecialchars($class) . '" target="_blank" rel="noopener noreferrer"' . ($label ? ' aria-label="' . htmlspecialchars($label) . '"' : '') . '><i class="' . htmlspecialchars($icon) . '"></i></a>';
}

function ui_logo(array $options = []): string {
  $class = $options['class'] ?? '';
  return '<a href="/" class="inline-block ' . htmlspecialchars($class) . '"><img src="/assets/images/logo.png" alt="Logo" width="451" height="109" style="max-width:120px;height:auto"></a>';
}

function ui_prose(string $html, array $options = []): string {
  $class = $options['class'] ?? '';
  return '<div class="ds-prose prose max-w-none ' . htmlspecialchars($class) . '">' . $html . '</div>';
}
