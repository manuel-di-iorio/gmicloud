<?php
require_once("lib/db.php");
require_once("lib/checkSession.php");
require_once("lib/maintenance.php"); check_maintenance();
require_once("lib/csrf.php");
require_once("models/Team.php");

if ($_SERVER['REQUEST_METHOD'] === "POST") {
  csrf_validate_request();
  $teamName = isset($_POST["name"]) ? trim($_POST["name"]) : "";
  if (empty($teamName)) {
    header("Location: teams.php");
    exit;
  }
  if (strlen($teamName) > 50) {
    header("Location: teams.php?error=" . urlencode("Nome troppo lungo (max 50 caratteri)"));
    exit;
  }

  $teamId = Team::create($user["id"], $teamName);
  header("Location: team.php?id=$teamId");
  exit;
}

$view = "add-team";
$pageName = __('teams_create_title');
$backUrl = "teams.php";
$breadcrumbs = [
  ['label' => __('breadcrumb_home'), 'href' => 'home.php'],
  ['label' => __('breadcrumb_teams'), 'href' => 'teams.php'],
  ['label' => __('teams_create_title')],
];
require_once("includes/layout.php");
