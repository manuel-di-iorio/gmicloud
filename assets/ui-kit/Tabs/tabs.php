<?php

function ui_tabs($tabs, $options = []) {
  $class = $options['class'] ?? '';
  $activeTab = $options['active'] ?? ($tabs[0]['id'] ?? '');

  $html = '<div class="ui-tabs flex flex-col gap-0 ' . htmlspecialchars($class) . '">';
  $html .= '<div class="ui-tabs__nav relative flex flex-wrap gap-0.5 border-0 border-b-2 border-solid border-border-color" role="tablist">';

  foreach ($tabs as $i => $tab) {
    $tabId = $tab['id'] ?? 'tab-' . $i;
    $isActive = $tabId === $activeTab;
    $icon = isset($tab['icon']) ? '<i class="' . htmlspecialchars($tab['icon']) . '"></i>' : '';

    $stateClasses = $isActive ? 'is-active border-primary-color font-semibold text-primary-color' : 'border-transparent text-text-secondary';
    $html .= '<button class="ui-tabs__btn relative -mb-0.5 inline-flex cursor-pointer items-center gap-2 whitespace-nowrap border-0 border-b-2 border-solid bg-transparent px-5 py-3 font-inherit text-sm transition-colors duration-200 hover:bg-surface-offset hover:text-text ' . $stateClasses . '"';
    $html .= ' role="tab" aria-selected="' . ($isActive ? 'true' : 'false') . '"';
    $html .= ' data-tab="' . htmlspecialchars($tabId) . '">';
    $html .= $icon . '<span>' . htmlspecialchars($tab['label'] ?? 'Tab') . '</span>';
    $html .= '</button>';
  }

  $html .= '</div>';
  $html .= '<div class="ui-tabs__panels pt-5">';

  foreach ($tabs as $i => $tab) {
    $tabId = $tab['id'] ?? 'tab-' . $i;
    $isActive = $tabId === $activeTab;

    $html .= '<div class="ui-tabs__panel' . ($isActive ? ' is-active animate-tabs-in' : ' hidden') . '"';
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
