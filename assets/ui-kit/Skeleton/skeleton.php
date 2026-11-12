<?php

function ui_skeleton($type = 'text', $count = 1, $options = []) {
  $variants = [
    'text'  => '<div class="ds-skeleton" style="height:0.875rem;width:100%"></div>',
    'title' => '<div class="ds-skeleton" style="height:1.25rem;width:60%"></div>',
    'avatar' => '<div class="ds-skeleton" style="height:2.75rem;width:2.75rem"></div>',
    'stat'  => '<div class="ds-stat"><div class="ds-skeleton" style="height:2.75rem;width:2.75rem;flex-shrink:0"></div><div style="flex:1;display:flex;flex-direction:column;gap:0.5rem"><div class="ds-skeleton" style="height:1.25rem;width:50%"></div><div class="ds-skeleton" style="height:0.875rem;width:70%"></div></div></div>',
    'chart' => '<div class="ds-skeleton" style="height:16rem;width:100%"></div>',
    'table-row' => '<div style="display:flex;align-items:center;gap:1rem;padding:0.75rem 1rem"><div class="ds-skeleton" style="height:0.75rem;width:6%"></div><div class="ds-skeleton" style="height:0.75rem;width:22%"></div><div class="ds-skeleton" style="height:0.75rem;width:16%"></div><div class="ds-skeleton" style="height:0.75rem;width:14%"></div><div class="ds-skeleton" style="height:0.75rem;width:18%"></div><div class="ds-skeleton" style="height:0.75rem;width:8%"></div></div>',
  ];
  if (!isset($variants[$type])) $type = 'text';
  $class = $options['class'] ?? '';
  $html = '<div style="display:flex;flex-direction:column;gap:0.75rem;padding:0.5rem 0" class="' . htmlspecialchars($class) . '">';
  for ($i = 0; $i < $count; $i++) $html .= $variants[$type];
  $html .= '</div>';
  return $html;
}
