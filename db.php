<?php

mysqli_report(MYSQLI_REPORT_OFF);

// Database credentials from Render environment variables
$host = getenv("DB_HOST");
$user = getenv("DB_USER");
$password = getenv("DB_PASSWORD");
$database = getenv("DB_NAME");
$port = (int)(getenv("DB_PORT") ?: 4000);

// Initialize MySQL connection
$conn = mysqli_init();

// Enable SSL certificate verification
$conn->options(MYSQLI_OPT_SSL_VERIFY_SERVER_CERT, true);

$conn->ssl_set(
    null,
    null,
    __DIR__ . "/isrgrootx1.pem",
    null,
    null
);

// Connect to TiDB Cloud
if (!$conn->real_connect(
    $host,
    $user,
    $password,
    $database,
    $port,
    null,
    MYSQLI_CLIENT_SSL
)) {
    die("Database connection failed.");
}

// Set character encoding
$conn->set_charset("utf8mb4");

?>