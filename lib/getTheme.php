<?php

$theme = isset($_COOKIE["theme"]) ? $_COOKIE["theme"] : "dark";

if ($theme !== "light" && $theme !== "dark") $theme = "dark";
