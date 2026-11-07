```php
<?php
// Get current page
$page = basename($_SERVER['PHP_SELF']);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title><?= htmlspecialchars($pageTitle ?? 'StudentHub') ?></title>

    <link rel="stylesheet" href="style.css">
</head>

<body>

<header class="site-header">
    <div class="header-inner">

        <a href="index.php" class="brand">
            <span class="brand-icon">S</span>

            <span>
                Student<span class="brand-highlight">Hub</span>
                <small>RECORD MANAGEMENT PORTAL</small>
            </span>
        </a>

        <div class="header-right">

            <span class="admin-status">
                <span class="online-dot"></span>
                Management Portal
            </span>

            <div class="admin-profile">
                <span class="profile-avatar">A</span>
                <span>Administrator</span>
            </div>

        </div>
    </div>
</header>

<nav class="navbar">
    <div class="nav-inner">

        <a href="index.php"
           class="<?= $page === 'index.php' ? 'active' : '' ?>">
            Home
        </a>

        <a href="index.php"
           class="<?= $page === 'index.php' ? 'active' : '' ?>">
            Dashboard
        </a>

        <a href="add.php"
           class="<?= $page === 'add.php' ? 'active' : '' ?>">
            + Add Student
        </a>

        <a href="view.php"
           class="<?= $page === 'view.php' ? 'active' : '' ?>">
            Student Records
        </a>

        <a href="delete.php"
           class="<?= $page === 'delete.php' ? 'active' : '' ?>">
            Delete Records
        </a>

    </div>
</nav>

<main class="page-container">
``` 