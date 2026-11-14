<?php
// Temporary screenshot receiver for visual verification. Safe to delete.
$in = file_get_contents('php://input');
$data = json_decode($in, true);
$b64 = is_array($data) && isset($data['image']) ? (string)$data['image'] : '';
$b64 = preg_replace('#^data:image/[a-z]+;base64,#i', '', $b64);
$bin = $b64 !== '' ? base64_decode($b64) : false;
header('Content-Type: application/json');
if ($bin === false || $bin === '') {
    echo json_encode(['ok' => false, 'reason' => 'no image']);
    exit;
}
$name = isset($data['name']) ? preg_replace('/[^a-z0-9_-]/i', '', (string)$data['name']) : 'shot';
if ($name === '') $name = 'shot';
$path = __DIR__ . '/_tmp_' . $name . '.jpg';
$ok = file_put_contents($path, $bin);
echo json_encode(['ok' => $ok !== false, 'bytes' => strlen($bin), 'path' => basename($path)]);
