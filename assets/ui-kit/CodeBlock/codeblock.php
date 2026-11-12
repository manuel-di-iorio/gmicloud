<?php

function ui_code($code, $options = []) {
  $class = $options['class'] ?? '';
  return '<pre class="ds-code ds-codeblock ' . htmlspecialchars($class) . '"><code>' . $code . '</code></pre>';
}
