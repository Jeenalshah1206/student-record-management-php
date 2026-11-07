<?php
ob_start();

// Start session first
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Database connection
require_once 'db.php';

$pageTitle = 'View Students | StudentHub';

// Handle delete or success messages if any
$added = $_GET['added'] ?? 0;

// Fetch all students from database
$result = $conn->query("SELECT * FROM students ORDER BY id DESC");

// Include header
require 'header.php';
?>

<div class="page-intro">
    <span class="eyebrow">STUDENT MANAGEMENT / DIRECTORY</span>
    <h1>Student Records</h1>
    <p>View, search and manage all registered student information.</p>
</div>

<?php if ($added): ?>
    <div class="alert alert-success" style="margin-bottom: 20px; padding: 10px; background: #d4edda; color: #155724; border-radius: 4px;">
        Student successfully registered!
    </div>
<?php endif; ?>

<section class="form-panel">
    <div class="form-panel-heading">
        <div>
            <h2>All Students</h2>
            <p><?php echo $result->num_rows; ?> matching record(s)</p>
        </div>
    </div>

    <div style="overflow-x: auto;">
        <table style="width: 100%; border-collapse: collapse; margin-top: 15px;">
            <thead>
                <tr style="border-bottom: 2px solid #ddd; text-align: left;">
                    <th style="padding: 12px;">ID</th>
                    <th style="padding: 12px;">STUDENT NAME</th>
                    <th style="padding: 12px;">EMAIL</th>
                    <th style="padding: 12px;">AGE</th>
                    <th style="padding: 12px;">COURSE</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result && $result->num_rows > 0): ?>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr style="border-bottom: 1px solid #eee;">
                            <td style="padding: 12px;">#<?php echo htmlspecialchars($row['id']); ?></td>
                            <td style="padding: 12px; font-weight: bold;"><?php echo htmlspecialchars($row['name']); ?></td>
                            <td style="padding: 12px;"><?php echo htmlspecialchars($row['email']); ?></td>
                            <td style="padding: 12px;"><?php echo htmlspecialchars($row['age']); ?></td>
                            <td style="padding: 12px;">
                                <span style="background: #e2f0cb; padding: 4px 8px; border-radius: 4px; font-size: 0.9em;">
                                    <?php echo htmlspecialchars($row['course']); ?>
                                </span>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" style="padding: 20px; text-align: center; color: #777;">No student records found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</section>

<?php require 'footer.php'; ?>