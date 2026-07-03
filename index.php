<?php
require_once("lib/db.php");
require_once("models/User.php");
require_once("models/Game.php");
require_once("models/Player.php");
require_once("models/Score.php");

// Get the ordered stats
$stats = [
  "scores"=>    [ "label" => __("index_stat_scores"), "count" => Score::count() ],  
  "players" =>  [ "label" => __("index_stat_players"), "count" => Player::count() ],
  "games" =>    [ "label" => __("index_stat_games"), "count" => Game::count() ],  
  "active-games" => [ "label" => __("index_stat_active_games"), "count" => Score::getActiveGames() ],
  "unique-scores-countries" => [ "label" => __("index_stat_countries"), "count" => Score::getUniqueCountriesCount() ],
  "users" =>    [ "label" => __("index_stat_developers"), "count" => User::count() ],  
];

$view = "index";
$pageName = $config["platformTitle"];
require_once("includes/layout.php");
