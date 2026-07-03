<?php

// Check if the user is logged in
if (!isset($user)) {
  // Store the go path (relative only)
  $go = $_SERVER["REQUEST_URI"] ?? "/";
  $go = str_replace(["\r", "\n"], "", $go);
  if (strlen($go) < 1 || $go[0] !== "/" || (strlen($go) > 1 && $go[1] === "/")) {
    $go = "/";
  }
  header("Location: login.php?go=" . urlencode($go));
  exit;
}
