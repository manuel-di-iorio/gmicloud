<?php
require_once("lib/db.php");
require_once("lib/checkSession.php");
require_once("models/Game.php");
require_once("models/Score.php");
require_once("models/Leaderboard.php");
require_once("assets/ui-kit/kit.php");

$baseApiPath = $config["host"] . "/api/v1";

if (!isset($_GET["id"])) {
  header("Location: games.php");
  exit;
}

$gameResult = Game::getByIdWithAccess((int)$_GET["id"], $user["id"]);
if (!$gameResult || !$gameResult->num_rows) {
  header("Location: games.php");
  exit;
}
$game = $gameResult->fetch_assoc();
$gameId = $game["game_id"];
$gameTeamId = $game["team_id"];

$gameTeam = null;
$isTeamAdmin = false;
if ($gameTeamId !== null) {
  require_once("models/Team.php");
  $gameTeam = Team::getById($gameTeamId);
  $isTeamAdmin = Team::isAdmin($gameTeamId, $user["id"]);
}

$activeTab = $_GET["tab"] ?? "config";

if ($activeTab === 'analytics') {
  $gameScoresResult = Score::countByGame($gameId, ['env' => 'production']);
  $gameTotalScores = $gameScoresResult->fetch_assoc()["count"] ?? 0;
  $gameUniquePlayers = Score::getUniquePlayersByGame($gameId);
  $gameLeaderboardCount = Game::getLeaderboardCountByGame($gameId);

  $gameScoresOverTime = [];
  $result = Score::getScoresOverTimeByGame($gameId, 30);
  while ($row = $result->fetch_assoc()) {
    $gameScoresOverTime[] = $row;
  }

  $gameCountries = [];
  $result = Score::getCountriesByGame($gameId);
  while ($row = $result->fetch_assoc()) {
    $gameCountries[] = $row;
  }
  $gameCountryCount = count($gameCountries);

  $gameScoresByLb = [];
  $result = Score::getScoresByLeaderboardByGame($gameId);
  while ($row = $result->fetch_assoc()) {
    $gameScoresByLb[] = $row;
  }
}

require_once("lib/csrf.php");

if (isset($_GET['ajax'])) {
  header('Content-Type: text/html; charset=utf-8');
  require "pages/game/game-tab-render.php";
  exit;
}

$pageName = $game["name"];
$tabCrumbs = [
  'players' => __('game_tab_players'),
  'leaderboards' => __('game_tab_leaderboards_tab'),
  'analytics' => __('game_tab_analytics'),
];
$activeTabCrumb = $tabCrumbs[$activeTab] ?? null;
$breadcrumbs = [
  ['label' => __('breadcrumb_home'), 'href' => 'home.php'],
  ['label' => __('breadcrumb_games'), 'href' => 'games.php'],
  [
    'label' => $game["name"],
    'href' => 'game.php?id=' . $gameId,
    'current' => $activeTabCrumb === null,
    'link' => true,
    'attrs' => ['data-crumb-base' => ''],
  ],
  [
    'label' => $activeTabCrumb ?? '',
    'current' => $activeTabCrumb !== null,
    'hidden' => $activeTabCrumb === null,
    'attrs' => ['data-crumb-tab' => ''],
  ],
];
$view = "game";
require_once("includes/layout.php");
