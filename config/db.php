<?php

$host = getenv("DB_HOST");
$username = getenv("DB_USER");
$password = getenv("DB_PASSWORD");
$database = getenv("DB_NAME");

$conn = mysqli_init();

mysqli_options($conn, MYSQLI_OPT_CONNECT_TIMEOUT, 10);

if (
    !mysqli_real_connect(
        $conn,
        $host,
        $username,
        $password,
        $database,
        (int) (getenv("DB_PORT") ?: 3306)
    )
) {
    error_log("Database connection failed: " . mysqli_connect_error());
    http_response_code(500);
    exit("Database connection failed. Please try again later.");
}

mysqli_set_charset($conn, "utf8mb4");