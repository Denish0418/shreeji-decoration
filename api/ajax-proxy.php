<?php
$route = $_GET['route'] ?? '';
$target = __DIR__ . '/../ajax/' . basename($route);

if (file_exists($target)) {
    require $target;
} else {
    http_response_code(404);
    echo json_encode(['error' => 'AJAX endpoint not found']);
}