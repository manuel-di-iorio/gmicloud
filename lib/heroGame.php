<?php

/**
 * Rules for the endless landing-page hero mini-game ("Aether Run").
 *
 * The runner is infinite: the score grows with distance and collected gears,
 * while the client ramps speed/difficulty over time. The server recomputes the
 * score from the run data instead of trusting the client.
 *
 * Score = 100 + distance(m) * 10 + gears * 150 (minimum 50).
 *
 * Distance is validated against the maximum possible speed over the submitted
 * survival time, so a tampered client cannot invent free metres.
 */
const HERO_GAME_MAX_SPEED = 9.6;
const HERO_GAME_POINTS_BASE = 100;
const HERO_GAME_POINTS_PER_METER = 10;
const HERO_GAME_POINTS_PER_GEAR = 150;
const HERO_GAME_MIN_POINTS = 50;
const HERO_GAME_MAX_TIME = 3600;
const HERO_GAME_MAX_GEARS = 100000;

/** Upper bound for the distance reachable in a run of $timeSeconds seconds. */
function hero_game_max_distance(float $timeSeconds): float {
  $timeSeconds = max(0.0, min($timeSeconds, HERO_GAME_MAX_TIME));
  // A little slack for spawning, counting and frame-time jitter.
  return $timeSeconds * (HERO_GAME_MAX_SPEED + 0.8) + 25.0;
}

/** Score for a validated run. */
function hero_game_points(float $distanceMeters, int $gears): int {
  $distanceMeters = max(0.0, min($distanceMeters, hero_game_max_distance(HERO_GAME_MAX_TIME)));
  $gears = max(0, min($gears, HERO_GAME_MAX_GEARS));

  $points = HERO_GAME_POINTS_BASE
    + $distanceMeters * HERO_GAME_POINTS_PER_METER
    + $gears * HERO_GAME_POINTS_PER_GEAR;

  return max(HERO_GAME_MIN_POINTS, (int) round($points));
}

/** Absolute score cap for a given survival time and gear count. */
function hero_game_score_cap(float $timeSeconds, int $gears): int {
  return hero_game_points(hero_game_max_distance($timeSeconds), $gears);
}

/**
 * Legacy helper used by the public config endpoint: the theoretical cap for a
 * maximum-length run with no gears. Individual runs use hero_game_score_cap().
 */
function hero_game_max_points(float $timeSeconds = HERO_GAME_MAX_TIME): int {
  return hero_game_score_cap($timeSeconds, 0);
}
