<?php

function ui_input($name, $options = []) {
  $type = $options['type'] ?? 'text';
  $label = $options['label'] ?? '';
  $value = $options['value'] ?? '';
  $placeholder = $options['placeholder'] ?? '';
  $required = $options['required'] ?? false;
  $disabled = $options['disabled'] ?? false;
  $class = $options['class'] ?? '';
  $hint = $options['hint'] ?? '';
  $error = $options['error'] ?? '';
  $id = $options['id'] ?? $name;
  $isSelect = ($type === 'select' && isset($options['options']) && is_array($options['options']));
  $isCheckbox = ($type === 'checkbox');
  $isTextarea = ($type === 'textarea');

  $base = 'ds-input';
  if ($isTextarea) $base = 'ds-textarea';
  if ($isSelect) $base = 'ds-select ds-input';
  if ($error) $base .= ' ds-input--error';
  if ($class) $base .= ' ' . $class;

  $requiredMark = $required ? ' ds-field__label--required' : '';
  $disabledAttr = $disabled ? ' disabled' : '';
  $requiredAttr = $required ? ' required' : '';
  $idAttr = ' id="' . htmlspecialchars($id) . '"';
  $nameAttr = ' name="' . htmlspecialchars($name) . '"';

  $html = '<div class="ds-field">';
  if ($label) {
    $html .= '<label class="ds-field__label' . $requiredMark . '" for="' . htmlspecialchars($id) . '">' . htmlspecialchars($label) . '</label>';
  }

  if ($isCheckbox) {
    $checked = $value ? ' checked' : '';
    $html .= '<label class="ds-checkbox">';
    $html .= '<input type="checkbox"' . $idAttr . $nameAttr . $checked . $disabledAttr . $requiredAttr . '>';
    if ($placeholder) $html .= '<span>' . htmlspecialchars($placeholder) . '</span>';
    $html .= '</label>';
  } elseif ($isSelect) {
    $html .= '<select class="' . htmlspecialchars($base) . '"' . $idAttr . $nameAttr . $disabledAttr . $requiredAttr . '>';
    if (isset($options['placeholderOption'])) {
      $html .= '<option value="">' . htmlspecialchars($options['placeholderOption']) . '</option>';
    }
    foreach ($options['options'] as $optValue => $optLabel) {
      $selected = ((string)$optValue === (string)$value) ? ' selected' : '';
      $html .= '<option value="' . htmlspecialchars($optValue) . '"' . $selected . '>' . htmlspecialchars($optLabel) . '</option>';
    }
    $html .= '</select>';
  } elseif ($isTextarea) {
    $html .= '<textarea class="' . htmlspecialchars($base) . '"' . $idAttr . $nameAttr . $disabledAttr . $requiredAttr . ' placeholder="' . htmlspecialchars($placeholder) . '">' . htmlspecialchars($value) . '</textarea>';
  } else {
    $htmlType = in_array($type, ['number','password','email','date']) ? $type : 'text';
    $html .= '<input type="' . $htmlType . '" class="' . htmlspecialchars($base) . '"' . $idAttr . $nameAttr . ' value="' . htmlspecialchars($value) . '" placeholder="' . htmlspecialchars($placeholder) . '"' . $disabledAttr . $requiredAttr . '>';
  }

  if ($hint) $html .= '<div class="ds-hint">' . htmlspecialchars($hint) . '</div>';
  if ($error) $html .= '<div class="ds-error">' . htmlspecialchars($error) . '</div>';
  $html .= '</div>';
  return $html;
}

function ui_search_bar($value = '', array $options = []): string {
  $name = $options['name'] ?? 'q';
  $placeholder = $options['placeholder'] ?? 'Cerca...';
  $ariaLabel = $options['aria_label'] ?? $options['ariaLabel'] ?? 'Cerca';
  $class = $options['class'] ?? '';
  $id = $options['id'] ?? 'ds-search-' . substr(md5(uniqid('', true)), 0, 6);
  $clear = $value !== '';
  $html = '<div class="ds-search ' . htmlspecialchars($class) . '">';
  $html .= '<span class="ds-search__icon"><i class="fas fa-search"></i></span>';
  $html .= '<input id="' . htmlspecialchars($id) . '" type="text" name="' . htmlspecialchars($name) . '" value="' . htmlspecialchars($value) . '" placeholder="' . htmlspecialchars($placeholder) . '" aria-label="' . htmlspecialchars($ariaLabel) . '" class="ds-search__input" data-ds-search-input>';
  if ($clear) {
    $html .= '<button type="button" class="ds-search__clear" aria-label="Cancella ricerca" onclick="var i=document.getElementById(\'' . htmlspecialchars($id) . '\');if(i){i.value=\'\';i.focus();i.form&&i.form.submit();}"><i class="fas fa-times"></i></button>';
  }
  $html .= '</div>';
  return $html;
}
