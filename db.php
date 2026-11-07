
<?php

// Database error reporting
mysqli_report(MYSQLI_REPORT_OFF);

// Database credentials from Render environment variables
$host = getenv("DB_HOST");
$user = getenv("DB_USER");
$password = getenv("DB_PASSWORD");
$database = getenv("DB_NAME");
$port = (int)(getenv("DB_PORT") ?: 4000);

// Check database credentials
if (!$host || !$user || !$password || !$database) {
    error_log("Database environment variables are missing.");
    die("Database configuration error. Please contact the administrator.");
}

// Initialize MySQL connection
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
if (!$conn->real_connect(
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

// Set character encoding
if (!$conn->set_charset("utf8mb4")) {
    error_log("Failed to set database character encoding: " . $conn->error);
    die("Database configuration failed.");
}
