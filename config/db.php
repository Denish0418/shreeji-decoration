<?php
$is_vercel = getenv('VERCEL') || getenv('DB_HOST');

if ($is_vercel) {
    // Cloud database (Vercel production)
    $host = getenv('DB_HOST');
    $user = getenv('DB_USER');
    $pass = getenv('DB_PASS');
    $db = getenv('DB_NAME');
    $port = (int) getenv('DB_PORT');
    $ssl_ca = dirname(__DIR__) . '/certs/ca.pem';

    $conn = mysqli_init();
    $conn->ssl_set(NULL, NULL, $ssl_ca, NULL, NULL);
    $conn->real_connect($host, $user, $pass, $db, $port, NULL, MYSQLI_CLIENT_SSL);
} else {
    // Local WAMP database
    $conn = new mysqli('localhost', 'root', '', 'shreeji_decoration');
}

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>