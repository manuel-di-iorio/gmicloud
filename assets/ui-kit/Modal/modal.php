<?php

function ui_modal($id, $options = []) {
  $title = $options['title'] ?? '';
  $content = $options['content'] ?? '';
  $footer = $options['footer'] ?? '';
  $size = $options['size'] ?? 'md';
  $class = $options['class'] ?? '';
  $closeButton = $options['close_button'] ?? true;

  $sizes = ['sm' => 'ds-modal--sm', 'md' => 'ds-modal--md', 'lg' => 'ds-modal--lg', 'xl' => 'ds-modal--xl'];
  $sizeClass = $sizes[$size] ?? $sizes['md'];

  $html = '<div id="' . htmlspecialchars($id) . '" class="ds-modal-overlay hidden" onmousedown="if (event.target === this) this._dismissOnMouseup = true;" onmouseup="if (this._dismissOnMouseup && event.target === this) { this._dismissOnMouseup = false; closeModal(\'' . htmlspecialchars($id) . '\'); } else { this._dismissOnMouseup = false; }">';
  $html .= '<div class="ds-modal ' . $sizeClass . ' ' . htmlspecialchars($class) . '" role="dialog" aria-modal="true">';

  if ($title) {
    $html .= '<div class="ds-modal__header">';
    $html .= '<h3 class="ds-modal__title">' . htmlspecialchars($title) . '</h3>';
    if ($closeButton) {
      $html .= '<button type="button" class="ds-modal__close" onclick="closeModal(\'' . htmlspecialchars($id) . '\')" aria-label="Chiudi">&times;</button>';
    }
    $html .= '</div>';
  }

  $html .= '<div class="ds-modal__body">' . $content . '</div>';

  if ($footer) {
    $footerClasses = 'ds-modal__footer' . (!empty($options['footer_right']) ? ' ds-modal__footer--right' : '');
    $html .= '<div class="' . $footerClasses . '">' . $footer . '</div>';
  }

  $html .= '</div>';
  $html .= '</div>';

  return $html;
}
