<?php
// Forward web traffic to the appropriate page in the root directory
$request_uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$path = trim($request_uri, '/');

if (empty($path)) {
    require __DIR__ . '/../index.php';
    exit;
}

$file = __DIR__ . '/../' . $path;

if (is_file($file)) {
    require $file;
    exit;
}

if (is_file($file . '.php')) {
    require $file . '.php';
    exit;
}

require __DIR__ . '/../index.php';