<?php
/**
 * Renders a reusable table filters form.
 *
 * Usage:
 *  $fields = [
 *    [ 'name' => 'player', 'label' => 'Giocatore', 'type' => 'text', 'placeholder' => 'Nome giocatore' ],
 *    [ 'name' => 'date_from', 'label' => 'Da', 'type' => 'date' ],
 *  ];
 *  render_table_filters($fields);
 *
 * Supported field types: text, number, date, select
 */
function render_table_filters(array $fields, array $options = []) {
    $filterNames = array_map(function($f){ return $f['name']; }, $fields);
    $exclude = array_merge($filterNames, ['page']);
    $resetPreserve = $options['reset_preserve'] ?? ['id', 'sort', 'dir'];
    $formAction = $options['action'] ?? '';

    echo '<form method="GET"' . ($formAction ? ' action="' . htmlspecialchars($formAction) . '"' : '') . ' class="ds-table-filters">';
    echo '<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">';

    foreach ($_GET as $k => $v) {
        if (in_array($k, $exclude, true)) continue;
        if ($formAction && in_array($k, ['id', 'tab'])) continue;
        if (is_array($v)) {
            foreach ($v as $sub) {
                echo '<input type="hidden" name="' . htmlspecialchars($k) . '[]" value="' . htmlspecialchars($sub) . '">';
            }
            continue;
        }
        echo '<input type="hidden" name="' . htmlspecialchars($k) . '" value="' . htmlspecialchars($v) . '">';
    }

    foreach ($fields as $field) {
        $name = $field['name'];
        $label = $field['label'] ?? ucfirst($name);
        $type = $field['type'] ?? 'text';
        $placeholder = $field['placeholder'] ?? '';
        $value = isset($_GET[$name]) ? $_GET[$name] : ($field['default'] ?? '');

        echo '<div>';
        echo '<label class="ds-field__label">' . htmlspecialchars($label) . '</label>';

        if ($type === 'select' && isset($field['options']) && is_array($field['options'])) {
            echo '<select name="' . htmlspecialchars($name) . '" class="ds-select ds-input" style="height:2.5rem">';
            echo '<option value="">' . __('filter_all') . '</option>';
            foreach ($field['options'] as $optValue => $optLabel) {
                $sel = ((string)$optValue === (string)$value) ? ' selected' : '';
                echo '<option value="' . htmlspecialchars($optValue) . '"' . $sel . '>' . htmlspecialchars($optLabel) . '</option>';
            }
            echo '</select>';
        } else {
            $htmlType = 'text';
            if ($type === 'number') $htmlType = 'number';
            if ($type === 'date') $htmlType = 'date';
            echo '<input type="' . $htmlType . '" name="' . htmlspecialchars($name) . '" value="' . htmlspecialchars($value) . '" placeholder="' . htmlspecialchars($placeholder) . '" class="ds-input" style="height:2.5rem">';
        }

        echo '</div>';
    }

    echo '<div class="flex items-end">';
    echo '<div class="flex items-end gap-5">';
    echo ui_button(__('filter_apply'), 'primary', 'md', ['type' => 'submit']);

    $hasFilter = false;
    foreach ($fields as $field) {
        $name = $field['name'];
        if (isset($_GET[$name]) && $_GET[$name] !== '' && $_GET[$name] !== null) {
            $hasFilter = true;
            break;
        }
    }

    if ($hasFilter) {
        $resetParams = [];
        foreach ($resetPreserve as $k) {
            if (isset($_GET[$k]) && $_GET[$k] !== '') {
                $resetParams[$k] = $_GET[$k];
            }
        }
        $resetUrl = ($formAction ?: $_SERVER['PHP_SELF']) . (count($resetParams) ? ('?' . http_build_query($resetParams)) : '');
        echo ui_button(__('filter_reset'), 'secondary', 'md', ['icon' => 'fas fa-times', 'href' => $resetUrl]);
    }

    echo '</div>';
    echo '</div>';

    echo '</div>'; // close grid
    echo '</form>';
}

?>
