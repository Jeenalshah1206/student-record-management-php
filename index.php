```php
<?php

// Start session before any output
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Database connection
require_once 'db.php';

$pageTitle = 'Dashboard | StudentHub';

// Fetch dashboard statistics
$totalResult = $conn->query("SELECT COUNT(*) AS total FROM students");
$total = $totalResult ? $totalResult->fetch_assoc()['total'] : 0;

$courseResult = $conn->query("SELECT COUNT(DISTINCT course) AS total FROM students");
$courses = $courseResult ? $courseResult->fetch_assoc()['total'] : 0;

$latestResult = $conn->query("SELECT MAX(id) AS id FROM students");
$latest = $latestResult ? $latestResult->fetch_assoc()['id'] : 0;

// Fetch recent students
$recent = $conn->query(
    "SELECT * FROM students ORDER BY id DESC LIMIT 5"
);

// Include header
require 'header.php';

?>

<section class="welcome-banner">
    <div>
        <span class="eyebrow">STUDENT ADMINISTRATION</span>
        <h1>Welcome to StudentHub.</h1>
        <p>Manage student information, registrations and academic records from one place.</p>
        <a href="add.php" class="btn btn-light">+ Register New Student</a>
    </div>
    <div class="banner-decoration">S</div>
</section>

<div class="section-heading">
    <div>
        <span class="eyebrow">OVERVIEW</span>
        <h2>Dashboard Overview</h2>
        <p>Here's what's happening with your student records.</p>
    </div>
    <span class="date-label"><?= date('d M Y') ?></span>
</div>

<section class="stats-grid">

    <div class="stat-card">
        <div class="stat-top">
            <span>Total Students</span>
            <span class="stat-icon green">♙</span>
        </div>
        <h2><?= htmlspecialchars((string)$total) ?></h2>
        <p>Registered student records</p>
    </div>

    <div class="stat-card">
        <div class="stat-top">
            <span>Active Courses</span>
            <span class="stat-icon gold">▤</span>
        </div>
        <h2><?= htmlspecialchars((string)$courses) ?></h2>
        <p>Unique courses registered</p>
    </div>

    <div class="stat-card">
        <div class="stat-top">
            <span>Latest Student ID</span>
            <span class="stat-icon purple">#</span>
        </div>
        <h2><?= htmlspecialchars((string)($latest ?? 0)) ?></h2>
        <p>Most recently assigned ID</p>
    </div>

</section>

<section class="content-panel">

    <div class="panel-heading">
        <div>
            <h2>Recently Registered Students</h2>
            <p>Latest five student entries</p>
        </div>

        <a href="view.php" class="btn btn-outline">
            View All Students →
        </a>
    </div>

    <div class="table-scroll">
        <table>
            <thead>
                <tr>
                    <th>Student ID</th>
                    <th>Student Name</th>
                    <th>Email Address</th>
                    <th>Age</th>
                    <th>Course</th>
                </tr>
            </thead>

            <tbody>

                <?php if ($recent && $recent->num_rows > 0): ?>

                    <?php while ($row = $recent->fetch_assoc()): ?>

                        <tr>
                            <td>#<?= htmlspecialchars((string)$row['id']) ?></td>
                            <td><strong><?= htmlspecialchars($row['name']) ?></strong></td>
                            <td><?= htmlspecialchars($row['email']) ?></td>
                            <td><?= htmlspecialchars((string)$row['age']) ?></td>
                            <td>
                                <span class="course-badge">
                                    <?= htmlspecialchars($row['course']) ?>
                                </span>
                            </td>
                        </tr>

                    <?php endwhile; ?>

                <?php else: ?>

                    <tr>
                        <td colspan="5" class="empty-state">
                            No student records yet. Add your first student!
                        </td>
                    </tr>

                <?php endif; ?>

            </tbody>
        </table>
    </div>

</section>

<?php require 'footer.php'; ?>
```