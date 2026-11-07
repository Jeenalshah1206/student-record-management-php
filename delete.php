
<?php
require_once 'db.php';
ob_start();

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$pageTitle = 'Delete Student | StudentHub';
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'] ?? '')) {
        $message = "Invalid request. Please refresh the page.";
    } else {
        $id = filter_var($_POST['id'] ?? '', FILTER_VALIDATE_INT);

        if (!$id || $id < 1) {
            $message = "Please enter a valid student ID.";
        } else {
            $stmt = $conn->prepare("DELETE FROM students WHERE id = ?");
            $stmt->bind_param("i", $id);
            $stmt->execute();

            if ($stmt->affected_rows > 0) {
                header("Location: view.php?deleted=1");
                exit;
            } else {
                $message = "No student found with this ID.";
            }
            $stmt->close();
        }
    }
}

require 'header.php';
?>

<div class="page-intro">
    <span class="eyebrow">STUDENT MANAGEMENT / RECORD REMOVAL</span>
    <h1>Delete Student</h1>
    <p>Remove an existing student record from the database.</p>
</div>

<section class="delete-panel">
    <div class="delete-symbol">!</div>

    <h2>Remove Student Record</h2>
    <p>Enter the student ID you want to remove. This action cannot be undone.</p>

    <?php if ($message): ?>
        <div class="alert alert-error"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>

    <form method="POST" onsubmit="return confirm('Are you sure you want to permanently delete this student?');">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">

        <div class="form-group">
            <label>Student ID</label>
            <input type="number" name="id" min="1" placeholder="Enter student ID" required>
        </div>

        <button type="submit" class="btn btn-danger">Delete Student Record</button>
    </form>

    <a href="view.php" class="back-link">← Return to Student Directory</a>
</section>

<?php require 'footer.php'; ?>
