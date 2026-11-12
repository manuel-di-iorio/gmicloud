<?php

function ui_checkbox($name, $checked, $options = []) {
  $label = $options['label'] ?? '';
  $description = $options['description'] ?? '';
  $icon = $options['icon'] ?? '';
  $value = $options['value'] ?? '1';
  $disabled = $options['disabled'] ?? false;
  $class = $options['class'] ?? '';

  $html = '<div class="ds-field ' . htmlspecialchars($class) . '">';
  $html .= '<label class="ds-checkbox">';
  $html .= '<input type="checkbox" name="' . htmlspecialchars($name) . '" value="' . htmlspecialchars($value) . '"' . ($checked ? ' checked' : '') . ($disabled ? ' disabled' : '') . '>';
  if ($label || $description) {
    $html .= '<span><b>' . htmlspecialchars($label) . '</b>';
    if ($description) {
      $html .= '<br><small class="ds-hint">';
      if ($icon) $html .= '<i class="' . htmlspecialchars($icon) . '"></i> ';
      $html .= htmlspecialchars($description) . '</small>';
    }
    $html .= '</span>';
  }
  $html .= '</label></div>';
  return $html;
}
