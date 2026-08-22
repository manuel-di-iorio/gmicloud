<?php
$NEW_DOMAIN = "https://gmicloud.altervista.org";
$path = $_GET["path"] ?? "";

if (empty($path)) {
    http_response_code(400);
    echo json_encode(["error" => "Missing path parameter"]);
    exit;
}

$targetUrl = $NEW_DOMAIN . "/proxy.php?path=" . urlencode($path);

if (!empty($_SERVER["QUERY_STRING"])) {
    $params = $_GET;
    unset($params["path"]);
    if (!empty($params)) {
        $targetUrl .= "&" . http_build_query($params);
    }
}

$method = $_SERVER["REQUEST_METHOD"];

$clientIP = $_SERVER["HTTP_X_FORWARDED_FOR"] ?? $_SERVER["REMOTE_ADDR"];

$headers = [];
foreach (["CONTENT_TYPE", "CONTENT_LENGTH", "HTTP_AUTHORIZATION", "HTTP_ACCEPT"] as $h) {
    if (isset($_SERVER[$h])) {
        $key = str_replace("_", "-", strtolower(str_replace("HTTP_", "", $h)));
        if ($h === "CONTENT_TYPE") $key = "Content-Type";
        if ($h === "CONTENT_LENGTH") $key = "Content-Length";
        if ($h === "HTTP_AUTHORIZATION") $key = "Authorization";
        if ($h === "HTTP_ACCEPT") $key = "Accept";
        $headers[] = "$key: " . $_SERVER[$h];
    }
}
$headers[] = "X-Forwarded-For: $clientIP";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $targetUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_MAXREDIRS, 5);
curl_setopt($ch, CURLOPT_TIMEOUT, 30);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

if (in_array($method, ["POST", "PUT", "PATCH"])) {
    $body = file_get_contents("php://input");
    curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
}

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$responseHeaders = curl_getinfo($ch, CURLINFO_HEADER_ALL);
curl_close($ch);

http_response_code($httpCode);

foreach (explode("\r\n", $responseHeaders) as $header) {
    if (empty(trim($header))) continue;
    if (strpos($header, 'HTTP/') === 0) continue;
    header($header, false);
}

echo $response;
