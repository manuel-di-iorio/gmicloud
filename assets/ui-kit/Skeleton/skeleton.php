<?php

function ui_skeleton($type = 'text', $count = 1, $options = []) {
  $shimmerClasses = 'bg-[linear-gradient(90deg,#e5e7eb_25%,#f3f4f6_50%,#e5e7eb_75%)] bg-[length:200%_100%] animate-skeleton-shimmer dark:bg-[linear-gradient(90deg,#252838_25%,#4f5577_50%,#252838_75%)]';
  $variants = [
    'text'  => '<div class="h-3.5 w-full rounded-md ' . $shimmerClasses . '"></div>',
    'title' => '<div class="h-5 w-3/5 rounded-md ' . $shimmerClasses . '"></div>',
    'avatar' => '<div class="h-11 w-11 rounded-full ' . $shimmerClasses . '"></div>',
    'stat'  => '<div class="flex items-center gap-4 rounded-xl border border-solid border-border-color bg-surface-card p-5 dark:border-[#2d3142] dark:bg-[#1e1e2e]"><div class="h-11 w-11 flex-shrink-0 rounded-xl ' . $shimmerClasses . '"></div><div class="flex flex-1 flex-col gap-2"><div class="h-5 w-1/2 rounded-md ' . $shimmerClasses . '"></div><div class="h-3.5 w-[70%] rounded-md ' . $shimmerClasses . '"></div></div></div>',
    'chart' => '<div class="h-64 w-full rounded-lg border-0 dark:border dark:border-solid dark:border-[#2d3142] ' . $shimmerClasses . '"></div>',
    'table-row' => '<div class="flex items-center gap-4 bg-transparent px-4 py-3 dark:border-[#2d3142]"><div class="h-3 w-[6%] rounded ' . $shimmerClasses . '"></div><div class="h-3 w-[22%] rounded ' . $shimmerClasses . '"></div><div class="h-3 w-[16%] rounded ' . $shimmerClasses . '"></div><div class="h-3 w-[14%] rounded ' . $shimmerClasses . '"></div><div class="h-3 w-[18%] rounded ' . $shimmerClasses . '"></div><div class="h-3 w-[8%] rounded ' . $shimmerClasses . '"></div></div>',
  ];

  if (!isset($variants[$type])) $type = 'text';

  $class = $options['class'] ?? '';
  $html = '<div class="flex flex-col gap-3 py-2 ' . htmlspecialchars($class) . '">';
  for ($i = 0; $i < $count; $i++) {
    $html .= $variants[$type];
  }
  $html .= '</div>';
  return $html;
}
