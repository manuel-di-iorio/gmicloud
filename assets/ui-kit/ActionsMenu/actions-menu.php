<?php

function ui_actions_menu($actions, $options = []) {
  $id = $options['id'] ?? 'actions-menu-' . uniqid();
  $class = $options['class'] ?? '';
  $triggerIcon = $options['triggerIcon'] ?? 'fas fa-ellipsis-v';
  $triggerLabel = $options['triggerLabel'] ?? '';

  $html = '<div id="' . htmlspecialchars($id) . '" class="ui-actions-menu relative inline-flex ' . htmlspecialchars($class) . '">';
  $html .= '<button type="button" class="ui-actions-menu__trigger inline-flex h-9 w-9 cursor-pointer items-center justify-center rounded-lg border-0 bg-transparent p-0 text-xl leading-none text-text-secondary transition-colors hover:bg-surface-offset hover:text-text focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-primary-color" aria-expanded="false"' . ($triggerLabel ? ' aria-label="' . htmlspecialchars($triggerLabel) . '"' : '') . ($triggerLabel ? ' data-tippy-content="' . htmlspecialchars($triggerLabel) . '"' : '') . '>';
  $html .= '<i class="' . htmlspecialchars($triggerIcon) . '"></i>';
  $html .= '</button>';
  $html .= '<div class="ui-actions-menu__dropdown absolute right-0 top-[calc(100%+4px)] z-[1000] hidden min-w-[200px] translate-y-0 rounded-[10px] border border-solid border-border-color bg-surface-card p-1 shadow-[0_10px_30px_rgba(0,0,0,0.15),0_4px_8px_rgba(0,0,0,0.08)]">';

  foreach ($actions as $action) {
    if (isset($action['divider']) && $action['divider']) {
      $html .= '<div class="mx-2 my-1 h-px bg-border-color"></div>';
      continue;
    }

    $label = $action['label'] ?? '';
    $icon = $action['icon'] ?? '';
    $variant = $action['variant'] ?? 'default';
    $href = $action['href'] ?? null;
    $onclick = $action['onclick'] ?? '';
    $disabled = $action['disabled'] ?? false;
    $tooltip = $action['tooltip'] ?? '';
    $itemClass = isset($action['class']) ? ' ' . $action['class'] : '';
    $attrs = $action['attrs'] ?? [];

    $itemClasses = 'flex w-full box-border cursor-pointer items-center gap-2.5 whitespace-nowrap rounded-md border-0 bg-transparent px-3 py-2 text-left text-sm font-medium text-text no-underline transition-colors hover:bg-surface-offset [&_i]:w-4 [&_i]:shrink-0 [&_i]:text-center [&_i]:text-[0.9rem] [&_i]:text-text-secondary';
    if ($variant === 'danger') {
      $itemClasses .= ' text-red-600 hover:bg-red-50 dark:hover:bg-red-600/10 [&_i]:!text-red-600';
    }
    $itemClasses .= $itemClass;

    $attrStr = '';
    foreach ($attrs as $key => $value) {
      $attrStr .= ' ' . htmlspecialchars($key) . '="' . htmlspecialchars($value) . '"';
    }

    $iconHtml = $icon ? '<i class="' . htmlspecialchars($icon) . '"></i>' : '';
    $labelHtml = '<span>' . htmlspecialchars($label) . '</span>';
    $tipAttr = $tooltip ? ' data-tippy-content="' . htmlspecialchars($tooltip) . '"' : '';

    if ($disabled) {
      $html .= '<button type="button" class="' . $itemClasses . ' cursor-not-allowed opacity-50" disabled' . $tipAttr . $attrStr . '>' . $iconHtml . $labelHtml . '</button>';
    } elseif ($href) {
      $html .= '<a href="' . htmlspecialchars($href) . '" class="' . $itemClasses . '"' . $tipAttr . $attrStr . '>' . $iconHtml . $labelHtml . '</a>';
    } else {
      $html .= '<button type="button" class="' . $itemClasses . '" onclick="' . htmlspecialchars($onclick) . '"' . $tipAttr . $attrStr . '>' . $iconHtml . $labelHtml . '</button>';
    }
  }

  $html .= '</div></div>';

  return $html;
}
