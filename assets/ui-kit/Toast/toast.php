<?php

function ui_toast($title, $options = []) {
  $message = $options['message'] ?? '';
  $variant = $options['variant'] ?? 'info';
  $closable = $options['closable'] ?? true;
  $duration = $options['duration'] ?? 5000;
  $id = $options['id'] ?? ('toast-' . substr(md5(uniqid('', true)), 0, 8));
  $class = $options['class'] ?? '';

  $map = ['success' => 'success', 'error' => 'error', 'danger' => 'error', 'warning' => 'warning', 'info' => 'info'];
  $v = $map[$variant] ?? 'info';
  $icons = ['success' => 'fa-check-circle', 'error' => 'fa-times-circle', 'warning' => 'fa-exclamation-triangle', 'info' => 'fa-info-circle'];
  $iconClass = $icons[$v];

  $html = '<div id="' . htmlspecialchars($id) . '" class="ds-toast ds-toast--' . $v . ' ' . htmlspecialchars($class) . '" role="' . ($v === 'error' ? 'alert' : 'status') . '">';
  $html .= '<span class="ds-toast__icon"><i class="fas ' . $iconClass . '"></i></span>';
  $html .= '<div class="ds-toast__body">';
  $html .= '<div class="ds-toast__title">' . htmlspecialchars($title) . '</div>';
  if ($message) $html .= '<div class="ds-toast__message">' . htmlspecialchars($message) . '</div>';
  $html .= '</div>';
  if ($closable) {
    $html .= '<button type="button" class="ds-toast__close" onclick="uiToastDismiss(\'' . htmlspecialchars($id) . '\')" aria-label="Close">&times;</button>';
  }
  $html .= '</div>';

  if ($duration > 0) {
    $html .= '<script>(function(){var t=document.getElementById("' . htmlspecialchars($id) . '");if(!t)return;setTimeout(function(){uiToastDismiss("' . htmlspecialchars($id) . '");},' . intval($duration) . ');})();</script>';
  }
  return $html;
}

function ui_toast_container() {
  return '<div id="ui-toast-container" class="ds-toast-container"></div>';
}
