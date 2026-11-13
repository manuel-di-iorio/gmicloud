<?php

/**
 * Online leaderboard for the landing-page hero mini-game.
 *
 * `player_key` is the base64-encoded player name and is the unique key: the
 * column uses an ASCII binary collation so two different base64 strings that
 * only differ by letter case are not merged together.
 */
class HeroGameScore {
  public static array $schema = [
    'table'      => 'hero_game_scores',
    'primaryKey' => 'id',
    'timestamps' => true,
    'columns'    => [
      'id'           => ['type' => 'int',    'auto' => true],
      'player_key'   => ['type' => 'string', 'unique' => true],
      'player_name'  => ['type' => 'string'],
      'score'        => ['type' => 'int'],
      'gems'         => ['type' => 'int'],
      'time_seconds' => ['type' => 'double'],
      'ip'           => ['type' => 'string', 'nullable' => true],
      'created_at'   => ['type' => 'datetime'],
      'updated_at'   => ['type' => 'datetime'],
    ],
    'indexes'    => [
      ['columns' => ['player_key'], 'unique' => true],
      ['columns' => ['score']],
      ['columns' => ['updated_at']],
    ],
  ];

  /**
   * Best scores, highest first. Faster time wins on equal scores.
   */
  public static function top(int $limit = 10) {
    global $dbTableHeroGameScores;

    $limit = max(1, min(20, $limit));
    $sql = "SELECT player_name, score, gems, time_seconds
            FROM $dbTableHeroGameScores
            ORDER BY score DESC, time_seconds ASC, updated_at ASC
            LIMIT ?";

    return exec_query($sql, [ "i", $limit ]);
  }

  /**
   * Fetch the current best row for a base64 player key.
   */
  public static function getByPlayerKey(string $playerKey) {
    global $dbTableHeroGameScores;

    $sql = "SELECT id, player_name, score, gems, time_seconds
            FROM $dbTableHeroGameScores
            WHERE player_key=?
            LIMIT 1";

    return exec_query($sql, [ "s", $playerKey ]);
  }

  /**
   * 1-based position of a score in the leaderboard (score DESC, time ASC).
   */
  public static function getRank(int $score, float $timeSeconds): int {
    global $dbTableHeroGameScores;

    $sql = "SELECT COUNT(*) + 1 AS position
            FROM $dbTableHeroGameScores
            WHERE score > ? OR (score = ? AND time_seconds < ?)";

    $result = exec_query($sql, [ "iid", $score, $score, $timeSeconds ]);
    $row = $result ? $result->fetch_assoc() : null;

    return $row ? (int)$row["position"] : 1;
  }

  /**
   * Insert a first score or overwrite the stored best.
   * Only call this after checking that the new score is an improvement.
   */
  public static function saveBest(string $playerKey, string $playerName, int $score, int $gems,
  float $timeSeconds, ?string $ip = null) {
    global $dbTableHeroGameScores;

    $sql = "INSERT INTO $dbTableHeroGameScores
              (player_key, player_name, score, gems, time_seconds, ip, created_at, updated_at)
            VALUES (?, ?, ?, ?, ?, ?, NOW(), NOW())
            ON DUPLICATE KEY UPDATE
              player_name=VALUES(player_name),
              score=VALUES(score),
              gems=VALUES(gems),
              time_seconds=VALUES(time_seconds),
              ip=VALUES(ip),
              updated_at=NOW()";

    exec_query($sql, [ "ssiids", $playerKey, $playerName, $score, $gems, $timeSeconds, $ip ]);
  }
}
