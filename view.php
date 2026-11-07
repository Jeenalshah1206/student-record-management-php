<?php
ob_start();

// Start session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
// Generate CSRF token
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Database connection
require_once 'db.php';

$pageTitle = 'Student Records | StudentHub';

$search = trim($_GET['search'] ?? '');

$stmt = $conn->prepare(
    "SELECT * FROM students
     WHERE name LIKE ?
     OR email LIKE ?
     OR course LIKE ?
     OR CAST(id AS CHAR) LIKE ?
     ORDER BY id DESC"
);

if (!$stmt) {
    error_log("Search query failed: " . $conn->error);
    die("Unable to load student records. Please try again.");
}

$term = "%$search%";

$stmt->bind_param("ssss", $term, $term, $term, $term);
$stmt->execute();

$result = $stmt->get_result();
$count = $result->num_rows;

require 'header.php';

?>

<div class="page-intro">
    <span class="eyebrow">STUDENT MANAGEMENT / DIRECTORY</span>
    <h1>Student Records</h1>
    <p>View, search and manage all registered student information.</p>
</div>

<section class="content-panel">

    <?php if (isset($_GET['added'])): ?>
        <div class="alert alert-success">
            ✓ Student registered successfully!
        </div>
    <?php endif; ?>

    <?php if (isset($_GET['deleted'])): ?>
        <div class="alert alert-success">
            ✓ Student record deleted successfully!
        </div>
    <?php endif; ?>

    <div class="directory-toolbar">

        <div>
            <h2>All Students</h2>
            <p><?= $count ?> matching record(s)</p>
        </div>

        <div class="toolbar-actions">

            <form method="GET" class="search-box">

                <input
                    type="text"
                    name="search"
                    placeholder="Search name, email, ID..."
                    value="<?= htmlspecialchars($search) ?>"
                >

                <button type="submit">Search</button>

            </form>

            <a href="add.php" class="btn btn-primary">
                + Add Student
            </a>

        </div>

    </div>

    <div class="table-scroll">

        <table>

            <thead>
                <tr>
                    <th>ID</th>
                    <th>Student Name</th>
                    <th>Email</th>
                    <th>Age</th>
                    <th>Course</th>
                    <th>Action</th>
                </tr>
            </thead>

            <tbody>

                <?php if ($count > 0): ?>

                    <?php while ($row = $result->fetch_assoc()): ?>

                        <tr>

                            <td>
                                #<?= htmlspecialchars((string)$row['id']) ?>
                            </td>

                            <td>
                                <strong>
                                    <?= htmlspecialchars($row['name']) ?>
                                </strong>
                            </td>

                            <td>
                                <?= htmlspecialchars($row['email']) ?>
                            </td>

                            <td>
                                <?= htmlspecialchars((string)$row['age']) ?>
                            </td>

                            <td>
                                <span class="course-badge">
                                    <?= htmlspecialchars($row['course']) ?>
                                </span>
                            </td>

                            <td>

                                <form
                                    method="POST"
                                    action="delete.php"
                                    onsubmit="return confirm('Permanently delete this student?');"
                                >

                                    <input
                                        type="hidden"
                                        name="csrf_token"
                                        value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>"
                                    >

                                    <input
                                        type="hidden"
                                        name="id"
                                        value="<?= htmlspecialchars((string)$row['id']) ?>"
                                    >

                                    <button type="submit" class="table-delete">
                                        Delete
                                    </button>

                                </form>

                            </td>

                        </tr>

                    <?php endwhile; ?>

                <?php else: ?>

                    <tr>
                        <td colspan="6" class="empty-state">
                            No matching student records found.
                        </td>
                    </tr>

                <?php endif; ?>

            </tbody>

        </table>

    </div>

</section>

<?php

$stmt->close();

require 'footer.php';

?>
