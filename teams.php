<?php
require_once("lib/db.php");
require_once("lib/checkSession.php");
require_once("models/Team.php");

$userId = $user["id"];
$teams = [];
$result = Team::listByUser($userId);
while ($row = $result->fetch_assoc()) {
  $teams[] = $row;
}

$view = "teams";
$pageName = __('teams_title');
$breadcrumbs = [
  ['label' => __('breadcrumb_home'), 'href' => 'home.php'],
  ['label' => __('breadcrumb_teams')],
];
require_once("includes/layout.php");
