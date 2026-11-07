<?php

ob_start();

// Start session before any output
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Generate CSRF token
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Database connection
require_once 'db.php';

$pageTitle = 'Add Student | StudentHub';

$error = '';
$name = '';
$email = '';
$age = '';
$course = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // CSRF validation
    if (
        !isset($_POST['csrf_token']) ||
        !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])
    ) {
        $error = 'Invalid request. Please refresh the page and try again.';
    } else {

        // Get form data
        $name = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $age = trim($_POST['age'] ?? '');
        $course = trim($_POST['course'] ?? '');

        // Validate fields
        if ($name === '' || $email === '' || $age === '' || $course === '') {
            $error = 'Please fill in all fields.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = 'Please enter a valid email address.';
        } elseif (!ctype_digit($age) || (int)$age < 1 || (int)$age > 120) {
            $error = 'Please enter a valid age between 1 and 120.';
        } else {

            // Insert student record
            $stmt = $conn->prepare(
                "INSERT INTO students (name, email, age, course)
                 VALUES (?, ?, ?, ?)"
            );

            if (!$stmt) {
                error_log("Insert preparation failed: " . $conn->error);
                $error = 'Unable to add student. Please try again.';
            } else {

                $ageValue = (int)$age;

                $stmt->bind_param(
                    "ssis",
                    $name,
                    $email,
                    $ageValue,
                    $course
                );

                if ($stmt->execute()) {

                    $stmt->close();

                    // Prevent form resubmission
                    header("Location: view.php?added=1");
                    exit;

                } else {

                    if ($stmt->errno === 1062) {
                        $error = 'This email address is already registered.';
                    } else {
                        error_log("Student insert failed: " . $stmt->error);
                        $error = 'Unable to add student. Please try again.';
                    }

                    $stmt->close();
                }
            }
        }
    }
}

// Include header
require 'header.php';

?>

<div class="page-intro">
    <span class="eyebrow">STUDENT MANAGEMENT / REGISTRATION</span>
    <h1>Add Student</h1>
    <p>Enter the student details below to register a new student.</p>
</div>

<section class="content-panel">

    <div class="directory-toolbar">
        <div>
            <h2>Student Information</h2>
            <p>Fill in all the required details.</p>
        </div>

        <a href="view.php" class="btn btn-primary">
            View Students
        </a>
    </div>

    <?php if ($error !== ''): ?>
        <div class="alert alert-error">
            <?= htmlspecialchars($error) ?>
        </div>
    <?php endif; ?>

    <form method="POST" action="add.php" class="student-form">

        <input
            type="hidden"
            name="csrf_token"
            value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>"
        >

        <div class="form-group">
            <label for="name">Student Name</label>
            <input
                type="text"
                id="name"
                name="name"
                placeholder="Enter student name"
                value="<?= htmlspecialchars($name) ?>"
                maxlength="100"
                required
            >
        </div>

        <div class="form-group">
            <label for="email">Email Address</label>
            <input
                type="email"
                id="email"
                name="email"
                placeholder="Enter email address"
                value="<?= htmlspecialchars($email) ?>"
                maxlength="255"
                required
            >
        </div>

        <div class="form-group">
            <label for="age">Age</label>
            <input
                type="number"
                id="age"
                name="age"
                placeholder="Enter age"
                value="<?= htmlspecialchars($age) ?>"
                min="1"
                max="120"
                required
            >
        </div>

        <div class="form-group">
            <label for="course">Course</label>
            <input
                type="text"
                id="course"
                name="course"
                placeholder="Enter course"
                value="<?= htmlspecialchars($course) ?>"
                maxlength="100"
                required
            >
        </div>

        <div class="form-actions">

            <button type="submit" class="btn btn-primary">
                Register Student
            </button>

            <a href="view.php" class="btn btn-secondary">
                Cancel
            </a>

        </div>

    </form>

</section>

<?php require 'footer.php'; ?>
