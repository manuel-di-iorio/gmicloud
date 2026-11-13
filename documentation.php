<?php
require_once("lib/db.php");
$view = "documentation";
$pageName = __('docs_page_title');
$breadcrumbs = [
  ['label' => __('breadcrumb_home'), 'href' => isset($user) ? 'home.php' : 'index.php'],
  ['label' => __('docs_page_title')],
];
$baseApiPath = $config["host"] . "/api/v1";
require_once("includes/layout.php");
