<?php
$host = getenv('DB_HOST') ?: 'mysql-1598f483-bhagwagardenish-371d.g.aivencloud.com';
$user = getenv('DB_USER') ?: 'avnadmin';
$pass = getenv('DB_PASS'); // Loaded from Vercel / environment variable
$db = getenv('DB_NAME') ?: 'defaultdb';
$port = (int) (getenv('DB_PORT') ?: 21840);

$ssl_ca = dirname(__DIR__) . '/certs/ca.pem';

$conn = mysqli_init();
$conn->ssl_set(NULL, NULL, $ssl_ca, NULL, NULL);

$connected = $conn->real_connect($host, $user, $pass, $db, $port, NULL, MYSQLI_CLIENT_SSL);

if (!$connected) {
    die("Connection failed: " . mysqli_connect_error());
}
?>