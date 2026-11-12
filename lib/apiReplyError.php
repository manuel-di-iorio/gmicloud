<?php

/**
 * Exit with a JSON API error
 */
function api_reply_error(string $message, string $code, int $status) {
  global $db;

  // Log only server-side/internal errors on API v1 endpoints (skip during test runs).
  // Client-side mistakes (validation, wrong method, not found, auth) are the dev's fault and are not stored.
  $skipCodes = ["ValidationError", "MethodNotAllowed", "NotFoundError", "AuthorizationError", "AuthenticationRequired", "RateLimitExceeded"];
  if (!in_array($code, $skipCodes, true) && isset($db) && empty($_SERVER["HTTP_X_TEST_RUN"])) {
    $uri = $_SERVER["REQUEST_URI"] ?? "";
    $uriPath = parse_url($uri, PHP_URL_PATH);
    if (strpos($uriPath, "/api/v1/") === 0) {
      $method = $_SERVER["REQUEST_METHOD"] ?? "N/A";
      $ip = $_SERVER["HTTP_CF_CONNECTING_IP"] ?? $_SERVER["REMOTE_ADDR"] ?? "N/A";
      $gameId = isset($_POST["game"]) ? (int)$_POST["game"] : (isset($_GET["game"]) ? (int)$_GET["game"] : null);

      $requestData = null;
      if ($method === "POST") {
        $sanitized = $_POST;
        unset($sanitized["hash"], $sanitized["token"], $sanitized["client_secret"]);
        $requestData = json_encode($sanitized);
      } elseif ($method === "GET") {
        $sanitized = $_GET;
        unset($sanitized["hash"], $sanitized["token"]);
        $requestData = json_encode($sanitized);
      }

      $stmt = $db->prepare("INSERT INTO api_errors (error_code, message, status, endpoint, method, ip, game_id, request_data) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
      if ($stmt) {
        $stmt->bind_param("ssisssis", $code, $message, $status, $uriPath, $method, $ip, $gameId, $requestData);
        $stmt->execute();
        $stmt->close();
      }
    }
  }

  http_response_code($status);
  header('Content-Type: application/json');

  exit(json_encode([
    "message" => $message,
    "code" => $code,
    "status" => $status,
  ]));
}
