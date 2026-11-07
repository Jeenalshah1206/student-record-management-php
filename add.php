
<?php
require_once 'db.php';

ob_start();

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$pageTitle = 'Add Student | StudentHub';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // CSRF validation
    if (!hash_equals(
        $_SESSION['csrf_token'] ?? '',
        $_POST['csrf_token'] ?? ''
    )) {
        $error = "Invalid request. Please refresh and try again.";
    } else {

        // Get form data
        $name = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $age = filter_var($_POST['age'] ?? '', FILTER_VALIDATE_INT);
        $course = trim($_POST['course'] ?? '');

        // Validate fields
        if (
            $name === '' ||
            $email === '' ||
            $age === false ||
            $age === null ||
            $age < 1 ||
            $age > 120 ||
            $course === ''
        ) {
            $error = "Please enter valid information in all fields.";

        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = "Please enter a valid email address.";

        } else {

            // Insert student using prepared statement
            $stmt = $conn->prepare(
                "INSERT INTO students (name, email, age, course)
                 VALUES (?, ?, ?, ?)"
            );

            if ($stmt) {
                $stmt->bind_param(
                    "ssis",
                    $name,
                    $email,
                    $age,
                    $course
                );

                if ($stmt->execute()) {
                    $stmt->close();

                    header("Location: view.php?added=1");
                    exit;
                } else {
                    $error = "Unable to register student. Please try again.";
                }

                $stmt->close();

            } else {
                $error = "Database query failed. Please try again.";
            }
        }
    }
}

require 'header.php';
?>

<div class="page-intro">
    <span class="eyebrow">STUDENT MANAGEMENT / REGISTRATION</span>
    <h1>Register a Student</h1>
    <p>Add a new student to the institutional records.</p>
</div>

<section class="form-panel">

    <div class="form-panel-heading">
        <div class="form-heading-icon">✎</div>

        <div>
            <h2>Student Information</h2>
            <p>Enter the required details below.</p>
        </div>
    </div>

    <?php if ($error): ?>
        <div class="alert alert-error">
            <?= htmlspecialchars($error) ?>
        </div>
    <?php endif; ?>

    <form method="POST" action="">

        <input
            type="hidden"
            name="csrf_token"
            value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>"
        >

        <div class="form-grid">

            <div class="form-group full-width">
                <label>
                    Full Name <span>*</span>
                </label>

                <input
                    type="text"
                    name="name"
                    placeholder="Enter student's full name"
                    maxlength="100"
                    value="<?= htmlspecialchars($_POST['name'] ?? '') ?>"
                    required
                >
            </div>

            <div class="form-group full-width">
                <label>
                    Email Address <span>*</span>
                </label>

                <input
                    type="email"
                    name="email"
                    placeholder="student@example.com"
                    maxlength="100"
                    value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
                    required
                >
            </div>

            <div class="form-group">
                <label>
                    Age <span>*</span>
                </label>

                <input
                    type="number"
                    name="age"
                    min="1"
                    max="120"
                    placeholder="Enter age"
                    value="<?= htmlspecialchars($_POST['age'] ?? '') ?>"
                    required
                >
            </div>

            <div class="form-group">
                <label>
                    Course <span>*</span>
                </label>

                <input
                    type="text"
                    name="course"
                    maxlength="100"
                    placeholder="e.g. B.Tech"
                    value="<?= htmlspecialchars($_POST['course'] ?? '') ?>"
                    required
                >
            </div>

        </div>

        <div class="form-bottom">

            <p><span>*</span> Required fields</p>

            <div class="form-buttons">

                <a href="index.php" class="btn btn-outline">
                    Cancel
                </a>

                <button type="submit" class="btn btn-primary">
                    Register Student →
                </button>

            </div>
        </div>

    </form>

</section>

<?php require 'footer.php'; ?>
