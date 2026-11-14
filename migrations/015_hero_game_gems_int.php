<?php
return [
  'description' => 'Allow endless hero-game runs to collect more than 255 gears',
  'sql' => [
    "ALTER TABLE hero_game_scores MODIFY gems INT UNSIGNED NOT NULL DEFAULT 0"
  ],
];
