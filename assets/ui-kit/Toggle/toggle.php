<?php

function ui_toggle($active, $url, $options = []) {
  $labelOn = $options['labelOn'] ?? 'Enable';
  $labelOff = $options['labelOff'] ?? 'Disable';
  $class = $options['class'] ?? '';
  $method = strtoupper($options['method'] ?? 'GET');
  $postBody = $options['postBody'] ?? null;
  $size = $options['size'] ?? 'md';
  $sizes = ['sm' => ['ds-toggle__track--sm', 13], 'md' => ['ds-toggle__track--md', 15], 'lg' => ['ds-toggle__track--lg', 19]];
  [$trackSize] = [$sizes[$size][0] ?? $sizes['md'][0]];
  $translate = $sizes[$size][1] ?? 15;

  if ($method === 'POST' && $postBody !== null) {
    $href = 'javascript:void(0)';
    $dataAttrs = ' data-post-url="' . htmlspecialchars($url) . '" data-post-body="' . htmlspecialchars($postBody) . '" data-ui-toggle-post="1"';
  } else {
    $href = htmlspecialchars($url);
    $dataAttrs = '';
  }

  $html = '<a href="' . $href . '" title="' . ($active ? htmlspecialchars($labelOn) : htmlspecialchars($labelOff)) . '" class="ds-toggle ui-toggle ' . htmlspecialchars($class) . '"' . $dataAttrs . '>';
  $html .= '<span class="ds-toggle__track ' . $trackSize . ($active ? ' ds-toggle__track--on' : ' ds-toggle__track--off') . '">';
  $html .= '<span class="ds-toggle__knob" style="width:' . ($size === 'sm' ? 16 : ($size === 'lg' ? 22 : 18)) . 'px;height:' . ($size === 'sm' ? 16 : ($size === 'lg' ? 22 : 18)) . 'px;transform:translateX(' . ($active ? $translate : 0) . 'px)"></span>';
  $html .= '</span></a>';
  return $html;
}
