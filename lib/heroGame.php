<?php

/**
 * Rules for the landing-page hero mini-game.
 *
 * These values mirror the constants at the top of assets/js/hero-game.js and
 * are used by the server to recompute the score instead of trusting the client.
 *
 * Score = 1200 + gems * 150 - time * 10 (minimum 50).
 *
 * The fastest possible run is limited by the auto-run speed and the distance
 * between the spawn point and the portal (the finish check fires 0.9 units
 * before the portal centre), so the maximum obtainable score is capped by
 * `hero_game_max_points()`.
 */
const HERO_GAME_DIAMONDS = 13;
const HERO_GAME_RUN_SPEED = 4.4;
const HERO_GAME_START_X = -3.0;
const HERO_GAME_PORTAL_X = 69.0;
const HERO_GAME_FINISH_RADIUS = 0.9;
const HERO_GAME_POINTS_BASE = 1200;
const HERO_GAME_POINTS_PER_DIAMOND = 150;
const HERO_GAME_POINTS_PER_SECOND = 10;
const HERO_GAME_MIN_POINTS = 50;
const HERO_GAME_MAX_TIME = 3600;

/** Fastest time physically possible, in seconds. */
function hero_game_min_time(): float {
  $distance = HERO_GAME_PORTAL_X - HERO_GAME_FINISH_RADIUS - HERO_GAME_START_X;
  return $distance / HERO_GAME_RUN_SPEED;
}

/** Hard cap for a submitted score: all diamonds collected at the fastest time. */
function hero_game_max_points(): int {
  $points = HERO_GAME_POINTS_BASE
    + HERO_GAME_DIAMONDS * HERO_GAME_POINTS_PER_DIAMOND
    - hero_game_min_time() * HERO_GAME_POINTS_PER_SECOND;

  return (int) round($points);
}

/** Recompute a score from the run data, clamped to the allowed range. */
function hero_game_points(float $timeSeconds, int $gems): int {
  $gems = max(0, min(HERO_GAME_DIAMONDS, $gems));
  $points = (int) round(
    HERO_GAME_POINTS_BASE
    + $gems * HERO_GAME_POINTS_PER_DIAMOND
    - $timeSeconds * HERO_GAME_POINTS_PER_SECOND
  );

  return max(HERO_GAME_MIN_POINTS, min(hero_game_max_points(), $points));
}
