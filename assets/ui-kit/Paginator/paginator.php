<?php

function ui_paginator($currentPage, $totalPages, $options = []) {
  if ($totalPages <= 1) return '';

  $urlPattern = $options['url'] ?? '?page={page}';
  $prevLabel = $options['prevLabel'] ?? '&laquo;';
  $nextLabel = $options['nextLabel'] ?? '&raquo;';
  $firstLabel = $options['firstLabel'] ?? '&laquo;&laquo;';
  $lastLabel = $options['lastLabel'] ?? '&raquo;&raquo;';
  $maxButtons = $options['maxButtons'] ?? 3;
  $showFirstLast = $options['showFirstLast'] ?? true;
  $class = $options['class'] ?? '';

  $btn = 'ds-pagination__btn';
  $html = '<nav class="ds-pagination ' . htmlspecialchars($class) . '"><div class="ds-pagination__list">';

  if ($showFirstLast && $currentPage > 0) {
    $html .= '<a href="' . htmlspecialchars(str_replace('{page}', '0', $urlPattern)) . '" class="' . $btn . '">' . $firstLabel . '</a>';
  }
  if ($currentPage > 0) {
    $html .= '<a href="' . htmlspecialchars(str_replace('{page}', $currentPage - 1, $urlPattern)) . '" class="' . $btn . '" rel="prev">' . $prevLabel . '</a>';
  } else {
    $html .= '<span class="' . $btn . ' ds-pagination__btn--disabled">' . $prevLabel . '</span>';
  }

  $half = floor(($maxButtons - 1) / 2);
  $start = max(1, $currentPage - $half);
  $end = min($totalPages - 1, $currentPage + $half);
  $visibleCount = $end - $start + 1;
  if ($visibleCount < $maxButtons && $start > 1) { $start = max(1, $start - ($maxButtons - $visibleCount)); $end = min($totalPages - 1, $start + $maxButtons - 1); $start = max(1, $end - $maxButtons + 1); }
  elseif ($visibleCount < $maxButtons && $end < $totalPages - 1) { $end = min($totalPages - 1, $end + ($maxButtons - $visibleCount)); $start = max(1, $end - $maxButtons + 1); }

  if ($start > 0) {
    $html .= $currentPage == 0 ? '<button class="' . $btn . ' ds-pagination__btn--active">1</button>' : '<a href="' . htmlspecialchars(str_replace('{page}', '0', $urlPattern)) . '" class="' . $btn . '">1</a>';
    if ($start > 1) $html .= '<span class="' . $btn . ' ds-pagination__btn--disabled">&hellip;</span>';
  }
  for ($i = $start; $i <= $end; $i++) {
    $pageUrl = str_replace('{page}', $i, $urlPattern);
    $html .= ($i == $currentPage) ? '<button class="' . $btn . ' ds-pagination__btn--active">' . ($i + 1) . '</button>' : '<a href="' . htmlspecialchars($pageUrl) . '" class="' . $btn . '">' . ($i + 1) . '</a>';
  }
  if ($end < $totalPages - 1) {
    if ($end < $totalPages - 2) $html .= '<span class="' . $btn . ' ds-pagination__btn--disabled">&hellip;</span>';
    $html .= ($currentPage == $totalPages - 1) ? '<button class="' . $btn . ' ds-pagination__btn--active">' . $totalPages . '</button>' : '<a href="' . htmlspecialchars(str_replace('{page}', $totalPages - 1, $urlPattern)) . '" class="' . $btn . '">' . $totalPages . '</a>';
  }

  if ($currentPage < $totalPages - 1) {
    $html .= '<a href="' . htmlspecialchars(str_replace('{page}', $currentPage + 1, $urlPattern)) . '" class="' . $btn . '" rel="next">' . $nextLabel . '</a>';
  } else {
    $html .= '<span class="' . $btn . ' ds-pagination__btn--disabled">' . $nextLabel . '</span>';
  }
  if ($showFirstLast && $currentPage < $totalPages - 1) {
    $html .= '<a href="' . htmlspecialchars(str_replace('{page}', $totalPages - 1, $urlPattern)) . '" class="' . $btn . '">' . $lastLabel . '</a>';
  }

  $html .= '</div></nav>';
  return $html;
}
