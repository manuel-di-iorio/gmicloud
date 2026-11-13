<?php
return [
  'description' => 'Create hero_game_scores table for the landing hero mini-game leaderboard',
  'sql' => [
    "CREATE TABLE hero_game_scores (
      id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
      player_key VARCHAR(512) CHARACTER SET ascii COLLATE ascii_bin NOT NULL,
      player_name VARCHAR(64) NOT NULL,
      score INT UNSIGNED NOT NULL,
      gems TINYINT UNSIGNED NOT NULL DEFAULT 0,
      time_seconds DECIMAL(9,3) NOT NULL DEFAULT 0,
      ip VARCHAR(45) DEFAULT NULL,
      created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
      updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
      UNIQUE KEY hero_game_scores_player_key_uniq (player_key),
      INDEX hero_game_scores_score_index (score),
      INDEX hero_game_scores_updated_at_index (updated_at)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4"
  ],
];
