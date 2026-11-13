<?php
/**
 * Online leaderboard endpoint for the landing-page hero mini-game.
 *
 * GET  /hero-scores.php?limit=10  -> best scores
 * POST /hero-scores.php           -> submit a run (JSON or form data)
 *
 * The player name is sent base64-encoded and is used as the unique key.
 * The score is always recomputed server-side from time + gems and clamped to
 * the maximum obtainable score, so a tampered client cannot submit more.
 */
$apiMode = true;
require_once("lib/db.php");
require_once("lib/rateLimit.php");
require_once("lib/heroGame.php");
require_once("models/HeroGameScore.php");

header("Content-Type: application/json; charset=utf-8");
header("X-Content-Type-Options: nosniff");

$method = $_SERVER["REQUEST_METHOD"] ?? "GET";

function hero_scores_list(int $limit): array {
  $rows = [];
  $result = HeroGameScore::top($limit);
  while ($row = $result->fetch_assoc()) {
    $rows[] = [
      "player" => $row["player_name"],
      "score"  => (int)$row["score"],
      "gems"   => (int)$row["gems"],
      "time"   => round((float)$row["time_seconds"], 2),
    ];
  }
  return $rows;
}

try {
  if ($method === "GET") {
    check_rate_limit("hero_scores_list", 60, 60);

    $limit = isset($_GET["limit"]) ? (int)$_GET["limit"] : 10;
    $limit = max(1, min(20, $limit));

    echo json_encode([
      "status"   => 200,
      "maxScore" => hero_game_max_points(),
      "scores"   => hero_scores_list($limit),
    ]);
    exit;
  }

  if ($method !== "POST") {
    api_reply_error("Request method not allowed", "MethodNotAllowed", 405);
  }

  check_rate_limit("hero_scores_submit", 10, 60);

  $contentType = $_SERVER["CONTENT_TYPE"] ?? "";
  if (stripos($contentType, "application/json") !== false) {
    $data = json_decode(file_get_contents("php://input"), true);
    if (!is_array($data)) {
      api_reply_error("Invalid request body", "ValidationError", 400);
    }
  } else {
    $data = $_POST;
  }

  if (!isset($data["player"]) || !is_string($data["player"]) || $data["player"] === "") {
    api_reply_error("Missing player", "ValidationError", 400);
  }

  // Accept only canonical base64: it is stored as the unique player key.
  $playerKey = trim($data["player"]);
  if (strlen($playerKey) > 512) {
    api_reply_error("Invalid player", "ValidationError", 400);
  }
  $decodedName = base64_decode($playerKey, true);
  if ($decodedName === false || base64_encode($decodedName) !== $playerKey) {
    api_reply_error("Invalid player encoding", "ValidationError", 400);
  }

  $playerName = trim($decodedName);
  $nameLength = function_exists("mb_strlen") ? mb_strlen($playerName, "UTF-8") : strlen($playerName);
  if ($nameLength < 1 || $nameLength > 64 || preg_match("//u", $playerName) !== 1 ||
      preg_match("/[\x00-\x1F\x7F]/", $playerName)) {
    api_reply_error("Invalid player name", "ValidationError", 400);
  }

  if (!isset($data["time"]) || !is_numeric($data["time"])) {
    api_reply_error("Invalid time", "ValidationError", 400);
  }
  $time = (float)$data["time"];
  if (!is_finite($time) || $time < hero_game_min_time() - 0.01 || $time > HERO_GAME_MAX_TIME) {
    api_reply_error("Invalid time", "ValidationError", 400);
  }

  $gems = isset($data["gems"])
    ? filter_var($data["gems"], FILTER_VALIDATE_INT, [
        "options" => ["min_range" => 0, "max_range" => HERO_GAME_DIAMONDS],
      ])
    : false;
  if ($gems === false) {
    api_reply_error("Invalid gems", "ValidationError", 400);
  }

  $maxScore = hero_game_max_points();

  // If the client sends its score, only reject impossible values; the stored
  // value is always the one recomputed here.
  if (isset($data["score"])) {
    if (!is_numeric($data["score"])) {
      api_reply_error("Invalid score", "ValidationError", 400);
    }
    $clientScore = (float)$data["score"];
    if (!is_finite($clientScore) || $clientScore < 0 || $clientScore > $maxScore) {
      api_reply_error("Score exceeds the maximum obtainable score", "ValidationError", 400);
    }
  }

  $score = hero_game_points($time, (int)$gems);

  $rawIp = $_SERVER["HTTP_CF_CONNECTING_IP"] ?? $_SERVER["REMOTE_ADDR"] ?? "";
  $ip = filter_var($rawIp, FILTER_VALIDATE_IP) ? substr($rawIp, 0, 45) : null;

  // Keep the best run per player (player_key is unique). Equal scores are
  // resolved in favour of the faster time, matching the leaderboard order.
  $existing = HeroGameScore::getByPlayerKey($playerKey)->fetch_assoc();
  $improved = !$existing
    || $score > (int)$existing["score"]
    || ($score === (int)$existing["score"] && $time < (float)$existing["time_seconds"]);

  if ($improved) {
    HeroGameScore::saveBest($playerKey, $playerName, $score, (int)$gems, $time, $ip);
  }

  $best = HeroGameScore::getByPlayerKey($playerKey)->fetch_assoc();
  $bestScore = $best ? (int)$best["score"] : $score;
  $bestTime = $best ? (float)$best["time_seconds"] : $time;
  $rank = HeroGameScore::getRank($bestScore, $bestTime);

  echo json_encode([
    "status"   => 200,
    "saved"    => true,
    "improved" => $improved,
    "score"    => $score,
    "best"     => $bestScore,
    "rank"     => $rank,
    "maxScore" => $maxScore,
    "scores"   => hero_scores_list(5),
  ]);
} catch (Throwable $e) {
  error_log("[hero-scores] " . $e->getMessage());
  api_reply_error("Unable to process the score", "InternalServerError", 500);
}
