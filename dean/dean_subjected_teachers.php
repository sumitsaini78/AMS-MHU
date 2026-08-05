<?php
// Temporarily enable error reporting to debug (Remove these 3 lines once it works)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Include your database connection
include '../db_connect.php'; 

// Ensure the connection variable is strictly $conn for MySQLi
if (isset($pdo) && !isset($conn)) {
    $conn = $pdo; 
}

if (!isset($conn)) {
    die("<div style='color:red; font-weight:bold; padding:20px;'>Error: Could not find the database connection.</div>");
}

$error_message = "";
$success_message = isset($_GET['success']) ? "Record successfully updated!" : "";
$editData = null;
$availableSubjects = [];
$teachers_list = [];

try {
    // Tell MySQLi to throw exceptions on errors so we can catch them cleanly
    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

    // 1. Handle Form Submission for Update
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_teacher_subject'])) {
        $id = $_POST['id'];
        $teacher_name = $_POST['teacher_name'];
        
        $selected_subject_data = $_POST['subject_data'];
        
        // Ensure the dropdown data is formatted correctly before exploding
        if (strpos($selected_subject_data, '|') !== false) {
            list($subject_name, $year, $semester) = explode('|', $selected_subject_data);
            
            $course_name = $_POST['course_name'];
            $subject_code = $_POST['subject_code'];

            // MySQLi Prepared Statement for UPDATE
            $stmt = $conn->prepare("UPDATE subjected_teacher SET teacher_name = ?, subject_name = ?, course_name = ?, year = ?, semester = ?, subject_code = ? WHERE id = ?");
            // "ssssssi" means 6 strings, 1 integer (the ID at the end)
            $stmt->bind_param("ssssssi", $teacher_name, $subject_name, $course_name, $year, $semester, $subject_code, $id);
            $stmt->execute();
            $stmt->close();
            
            header("Location: dean_subjected_teachers.php?success=1");
            exit();
        } else {
            $error_message = "Invalid subject data format submitted.";
        }
    }

    // 2. Fetch record for editing if edit ID is provided
    if (isset($_GET['edit_id'])) {
        $stmt = $conn->prepare("SELECT * FROM subjected_teacher WHERE id = ?");
        $stmt->bind_param("i", $_GET['edit_id']);
        $stmt->execute();
        $result = $stmt->get_result();
        $editData = $result->fetch_assoc();
        $stmt->close();
    }

    // 3. Fetch unique combinations of subject name, year, and semester
    $subjectsResult = $conn->query("SELECT DISTINCT subject_name, year, semester FROM subjected_teacher WHERE subject_name IS NOT NULL AND subject_name != ''");
    if ($subjectsResult) {
        while ($row = $subjectsResult->fetch_assoc()) {
            $availableSubjects[] = $row;
        }
    }

    // 4. Fetch all subjected teachers
    $teachersResult = $conn->query("SELECT * FROM subjected_teacher");
    if ($teachersResult) {
        while ($row = $teachersResult->fetch_assoc()) {
            $teachers_list[] = $row;
        }
    }

} catch (mysqli_sql_exception $e) {
    // Catch MySQLi specific database errors
    $error_message = "Database Error: " . $e->getMessage();
} catch (Exception $e) {
    $error_message = "General Error: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Dean Panel - Manage Subjected Teachers</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container my-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Dean Panel: Subjected Teachers Management</h2>
        <a href="dean_dashboard.php" class="btn btn-secondary btn-sm">Back to Dashboard</a>
    </div>

    <!-- Error/Success Messages -->
    <?php if (!empty($error_message)): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error_message) ?></div>
    <?php endif; ?>

    <?php if (!empty($success_message) && empty($error_message)): ?>
        <div class="alert alert-success"><?= htmlspecialchars($success_message) ?></div>
    <?php endif; ?>

    <?php if ($editData && empty($error_message)): ?>
        <!-- Edit Form -->
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-primary text-white">Edit Subjected Teacher Record</div>
            <div class="card-body">
                <form method="POST" action="dean_subjected_teachers.php">
                    <input type="hidden" name="id" value="<?= htmlspecialchars($editData['id']) ?>">
                    
                    <div class="mb-3">
                        <label class="form-label">Teacher Name</label>
                        <input type="text" class="form-control" name="teacher_name" value="<?= htmlspecialchars($editData['teacher_name']) ?>" required>
                    </div>

                    <!-- Subject Name Dropdown -->
                    <div class="mb-3">
                        <label class="form-label">Select Subject (Includes Year & Semester)</label>
                        <select class="form-select" name="subject_data" required>
                            <option value="">-- Choose Subject, Year & Semester --</option>
                            <?php foreach ($availableSubjects as $sub): ?>
                                <?php 
                                    $optionValue = $sub['subject_name'] . '|' . $sub['year'] . '|' . $sub['semester'];
                                    $displayLabel = $sub['subject_name'] . ' (Year: ' . $sub['year'] . ', Semester: ' . $sub['semester'] . ')';
                                    $isSelected = ($editData['subject_name'] === $sub['subject_name'] && $editData['year'] == $sub['year'] && $editData['semester'] == $sub['semester']);
                                ?>
                                <option value="<?= htmlspecialchars($optionValue) ?>" <?= $isSelected ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($displayLabel) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Course Name</label>
                        <input type="text" class="form-control" name="course_name" value="<?= htmlspecialchars($editData['course_name']) ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Subject Code</label>
                        <input type="text" class="form-control" name="subject_code" value="<?= htmlspecialchars($editData['subject_code']) ?>" required>
                    </div>
                    
                    <button type="submit" name="update_teacher_subject" class="btn btn-success">Update Record</button>
                    <a href="dean_subjected_teachers.php" class="btn btn-secondary">Cancel</a>
                </form>
            </div>
        </div>
    <?php endif; ?>

    <!-- Data Table View -->
    <div class="card shadow-sm">
        <div class="card-header bg-dark text-white">Subjected Teachers List</div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped align-middle">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Teacher Name</th>
                            <th>Subject Name</th>
                            <th>Course Name</th>
                            <th>Year</th>
                            <th>Semester</th>
                            <th>Subject Code</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($teachers_list) > 0): ?>
                            <?php foreach ($teachers_list as $row): ?>
                                <tr>
                                    <td><?= htmlspecialchars($row['id']) ?></td>
                                    <td><?= htmlspecialchars($row['teacher_name']) ?></td>
                                    <td><?= htmlspecialchars($row['subject_name']) ?></td>
                                    <td><?= htmlspecialchars($row['course_name']) ?></td>
                                    <td><?= htmlspecialchars($row['year']) ?></td>
                                    <td><?= htmlspecialchars($row['semester']) ?></td>
                                    <td><?= htmlspecialchars($row['subject_code']) ?></td>
                                    <td>
                                        <a href="dean_subjected_teachers.php?edit_id=<?= $row['id'] ?>" class="btn btn-sm btn-primary">Edit</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="8" class="text-center">No records found.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
</body>
</html>