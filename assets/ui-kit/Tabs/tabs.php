<?php

function ui_tabs($tabs, $options = []) {
  $class = $options['class'] ?? '';
  $activeTab = $options['active'] ?? ($tabs[0]['id'] ?? '');

  $html = '<div class="ds-tabs ' . htmlspecialchars($class) . '">';
  $html .= '<div class="ds-tabs__nav" role="tablist">';

  foreach ($tabs as $i => $tab) {
    $tabId = $tab['id'] ?? 'tab-' . $i;
    $isActive = $tabId === $activeTab;
    $icon = isset($tab['icon']) ? '<i class="' . htmlspecialchars($tab['icon']) . '"></i>' : '';

    $html .= '<button class="ds-tabs__btn' . ($isActive ? ' is-active' : '') . '"';
    $html .= ' role="tab" aria-selected="' . ($isActive ? 'true' : 'false') . '"';
    $html .= ' data-tab="' . htmlspecialchars($tabId) . '">';
    $html .= $icon . '<span>' . htmlspecialchars($tab['label'] ?? 'Tab') . '</span>';
    $html .= '</button>';
  }

  $html .= '</div>';
  $html .= '<div class="ds-tabs__panels">';

  foreach ($tabs as $i => $tab) {
    $tabId = $tab['id'] ?? 'tab-' . $i;
    $isActive = $tabId === $activeTab;

    $html .= '<div class="ds-tabs__panel' . ($isActive ? ' is-active' : '') . '"';
    if (!$isActive) $html .= ' hidden';
    $html .= ' role="tabpanel" id="panel-' . htmlspecialchars($tabId) . '"';
    if (isset($tab['url'])) {
      $html .= ' data-url="' . htmlspecialchars($tab['url']) . '"';
      $html .= ' data-loaded="false"';
    }
    $html .= '>';
    $html .= $tab['content'] ?? '';
    $html .= '</div>';
  }

  $html .= '</div>';
  $html .= '</div>';

  return $html;
}
