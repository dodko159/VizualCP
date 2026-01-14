<?php
// =======================
// CONFIG
// =======================
$baseDir = realpath(__DIR__ . '/assets/videos');

// =======================
// INPUT VALIDATION
// =======================
if (!isset($_GET['filename'])) {
    http_response_code(400);
    exit;
}

$requestPath = $_GET['filename'];

// Resolve the real path
$fullPath = realpath(__DIR__ . '/' . $requestPath);

// Reject invalid paths
if (
    $fullPath === false ||
    strpos($fullPath, $baseDir) !== 0 ||
    !is_file($fullPath)
) {
    http_response_code(403);
    exit;
}

// =======================
// FILE INFO
// =======================
$size = filesize($fullPath);
$start = 0;
$end = $size - 1;

// =======================
// HEADERS
// =======================
header("Content-Type: video/mp4");
header("Accept-Ranges: bytes");

// =======================
// RANGE HANDLING
// =======================
if (isset($_SERVER['HTTP_RANGE'])) {
    if (preg_match('/bytes=(\d*)-(\d*)/', $_SERVER['HTTP_RANGE'], $matches)) {

        if ($matches[1] !== '') {
            $start = (int)$matches[1];
        }

        if ($matches[2] !== '') {
            $end = (int)$matches[2];
        }

        if ($matches[2] === '') {
            $end = $size - 1;
        }

        if ($matches[1] === '' && $matches[2] !== '') {
            $start = $size - (int)$matches[2];
            $end = $size - 1;
        }

        if ($start > $end || $start >= $size) {
            http_response_code(416);
            exit;
        }

        header("HTTP/1.1 206 Partial Content");
    }
}

// =======================
// OUTPUT
// =======================
$length = $end - $start + 1;

header("Content-Length: $length");
header("Content-Range: bytes $start-$end/$size");

$fp = fopen($fullPath, 'rb');
fseek($fp, $start);

$bufferSize = 8192;
while (!feof($fp) && $length > 0) {
    $read = min($bufferSize, $length);
    echo fread($fp, $read);
    $length -= $read;
    flush();
}

fclose($fp);
exit;