```php
<?php

mysqli_report(MYSQLI_REPORT_OFF);

// Database credentials
$host = getenv("DB_HOST");
$user = getenv("DB_USER");
$password = getenv("DB_PASSWORD");
$database = getenv("DB_NAME");
$port = (int)(getenv("DB_PORT") ?: 4000);

// Initialize connection
$conn = mysqli_init();

if (!$conn) {
    error_log("MySQL initialization failed.");
    die("Database connection failed.");
}

// SSL configuration
$conn->options(MYSQLI_OPT_SSL_VERIFY_SERVER_CERT, true);

$conn->ssl_set(
    null,
    null,
    __DIR__ . "/isrgrootx1.pem",
    null,
    null
);

// Connect to TiDB Cloud
if (!@$conn->real_connect(
    $host,
    $user,
    $password,
    $database,
    $port,
    null,
    MYSQLI_CLIENT_SSL
)) {
    error_log("Database connection failed: " . $conn->connect_error);
    die("Database connection failed. Please try again later.");
}

// Character encoding
$conn->set_charset("utf8mb4");

?>
```