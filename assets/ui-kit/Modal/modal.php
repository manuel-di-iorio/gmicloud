<?php

function ui_modal($id, $options = []) {
  $title = $options['title'] ?? '';
  $content = $options['content'] ?? '';
  $footer = $options['footer'] ?? '';
  $size = $options['size'] ?? 'md';
  $class = $options['class'] ?? '';
  $closeButton = $options['close_button'] ?? true;

  $sizeWidths = ['sm' => 'max-w-[400px]', 'md' => 'max-w-[540px]', 'lg' => 'max-w-[700px]', 'xl' => 'max-w-[900px]'];

  $widthClass = $sizeWidths[$size] ?? $sizeWidths['md'];

  $html = '<div id="' . htmlspecialchars($id) . '" class="ui-modal-overlay fixed inset-0 z-[10000] hidden box-border flex items-center justify-center bg-black/50 p-6" onmousedown="if (event.target === this) this._dismissOnMouseup = true;" onmouseup="if (this._dismissOnMouseup && event.target === this) { this._dismissOnMouseup = false; closeModal(\'' . htmlspecialchars($id) . '\'); } else { this._dismissOnMouseup = false; }">';
  $html .= '<div class="ui-modal flex max-h-[90vh] w-full ' . $widthClass . ' animate-modal-in flex-col overflow-hidden rounded-2xl bg-surface-card shadow-2xl ' . htmlspecialchars($class) . '">';

  if ($title) {
    $html .= '<div class="flex items-center justify-between px-6 py-4 border-0 border-b border-solid border-border-color flex-shrink-0">';
    $html .= '<h3 class="font-semibold text-[1.1rem] text-headings m-0">' . htmlspecialchars($title) . '</h3>';
    if ($closeButton) {
      $html .= '<button type="button" class="w-9 h-9 flex items-center justify-center border-none bg-transparent text-[1.6rem] text-[var(--text-color-secondary)] cursor-pointer rounded-lg transition-colors duration-150 hover:bg-surface-offset hover:text-[var(--text-color)] p-0 leading-none" onclick="closeModal(\'' . htmlspecialchars($id) . '\')">&times;</button>';
    }
    $html .= '</div>';
  }

  $html .= '<div class="p-6 overflow-y-auto overflow-x-hidden flex-1 text-[var(--text-color)]">' . $content . '</div>';

  if ($footer) {
    $footerClasses = 'flex items-center gap-3 px-6 py-4 border-0 border-t border-solid border-border-color flex-shrink-0' . (!empty($options['footer_right']) ? ' justify-end' : '');
    $html .= '<div class="' . $footerClasses . '">' . $footer . '</div>';
  }

  $html .= '</div>';
  $html .= '</div>';

  return $html;
}
