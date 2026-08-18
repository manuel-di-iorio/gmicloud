<?php
require_once("lib/db.php");
require_once("lib/checkSession.php");
require_once("lib/maintenance.php"); check_maintenance();
require_once("lib/csrf.php");
require_once("models/Score.php");
require_once("models/Game.php");

header("Content-Type: application/json");

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  echo json_encode(['success' => false, 'error' => 'Method not allowed']);
  exit;
}

csrf_validate_request();

$scoreId = isset($_POST['score_id']) ? (int)$_POST['score_id'] : 0;
$gameId  = isset($_POST['game_id'])  ? (int)$_POST['game_id']  : 0;
$score   = isset($_POST['score'])    ? (float)$_POST['score']  : null;
$tags    = isset($_POST['tags'])    && $_POST['tags']    !== '' ? (string)$_POST['tags']    : null;
$data    = isset($_POST['data'])    && $_POST['data']    !== '' ? (string)$_POST['data']    : null;
$country = isset($_POST['country']) && $_POST['country'] !== '' ? (string)$_POST['country'] : null;
$env     = isset($_POST['env'])     && $_POST['env'] === 'test' ? 'test' : 'production';

if (!$scoreId || !$gameId || $score === null) {
  echo json_encode(['success' => false, 'error' => 'Missing parameters']);
  exit;
}

$result = Game::getByIdWithAccess($gameId, $user['id']);
if (!$result || !$result->num_rows) {
  echo json_encode(['success' => false, 'error' => 'Unauthorized']);
  exit;
}

$existing = Score::getForEdit($scoreId);
if (!$existing || (int)$existing['game_id'] !== $gameId) {
  echo json_encode(['success' => false, 'error' => 'Score not found']);
  exit;
}

// preserve ip and sign; update all other editable fields
Score::update($scoreId, $score, $existing['ip'], $country, $existing['sign'], $data, $tags, $env);

echo json_encode(['success' => true]);
