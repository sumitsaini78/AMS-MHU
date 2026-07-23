<?php
// Database configuration based on your provided schema
$host = '127.0.0.1';
$dbname = 'mhu-ams';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

// Handle Form Submission for Update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_student'])) {
    $id = $_POST['id'];
    $student_name = $_POST['student_name'];
    $subject_name = $_POST['subject_name'];
    $subject_code = $_POST['subject_code'];
    $faculty = $_POST['faculty'];
    $course = $_POST['course'];
    $year = $_POST['year'];
    $semester = $_POST['semester'];
    $roll_number = $_POST['roll_number'];

    $stmt = $pdo->prepare("UPDATE subjected_student SET student_name = ?, subject_name = ?, subject_code = ?, faculty = ?, course = ?, year = ?, semester = ?, roll_number = ? WHERE id = ?");
    $stmt->execute([$student_name, $subject_name, $subject_code, $faculty, $course, $year, $semester, $roll_number, $id]);
    
    header("Location: dean_subjected_students.php?success=1");
    exit();
}

// Fetch record for editing if edit ID is provided
$editData = null;
if (isset($_GET['edit_id'])) {
    $stmt = $pdo->prepare("SELECT * FROM subjected_student WHERE id = ?");
    $stmt->execute([$_GET['edit_id']]);
    $editData = $stmt->fetch(PDO::FETCH_ASSOC);
}

// Fetch all subjected students from the database
$stmt = $pdo->query("SELECT * FROM subjected_student");
$students = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Dean Panel - Manage Subjected Students</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container my-5">
    <h2 class="mb-4">Dean Panel: Subjected Students Management</h2>

    <?php if (isset($_GET['success'])): ?>
        <div class="alert alert-success">Record successfully updated!</div>
    <?php endif; ?>

    <?php if ($editData): ?>
        <!-- Edit Form -->
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-primary text-white">Edit Subjected Student Record</div>
            <div class="card-body">
                <form method="POST" action="dean_subjected_students.php">
                    <input type="hidden" name="id" value="<?= htmlspecialchars($editData['id']) ?>">
                    
                    <div class="mb-3">
                        <label class="form-label">Student Name</label>
                        <input type="text" class="form-control" name="student_name" value="<?= htmlspecialchars($editData['student_name']) ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Subject Name</label>
                        <input type="text" class="form-control" name="subject_name" value="<?= htmlspecialchars($editData['subject_name']) ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Subject Code</label>
                        <input type="text" class="form-control" name="subject_code" value="<?= htmlspecialchars($editData['subject_code']) ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Faculty</label>
                        <input type="text" class="form-control" name="faculty" value="<?= htmlspecialchars($editData['faculty']) ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Course</label>
                        <input type="text" class="form-control" name="course" value="<?= htmlspecialchars($editData['course']) ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Year</label>
                        <input type="number" class="form-control" name="year" value="<?= htmlspecialchars($editData['year']) ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Semester</label>
                        <input type="number" class="form-control" name="semester" value="<?= htmlspecialchars($editData['semester']) ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Roll Number</label>
                        <input type="number" class="form-control" name="roll_number" value="<?= htmlspecialchars($editData['roll_number']) ?>" required>
                    </div>
                    
                    <button type="submit" name="update_student" class="btn btn-success">Update Record</button>
                    <a href="dean_subjected_students.php" class="btn btn-secondary">Cancel</a>
                </form>
            </div>
        </div>
    <?php endif; ?>

    <!-- Data Table View -->
    <div class="card shadow-sm">
        <div class="card-header bg-dark text-white">Subjected Students List</div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped align-middle">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Student Name</th>
                            <th>Subject Name</th>
                            <th>Subject Code</th>
                            <th>Faculty</th>
                            <th>Course</th>
                            <th>Year</th>
                            <th>Semester</th>
                            <th>Roll Number</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($students) > 0): ?>
                            <?php foreach ($students as $student): ?>
                                <tr>
                                    <td><?= htmlspecialchars($student['id']) ?></td>
                                    <td><?= htmlspecialchars($student['student_name']) ?></td>
                                    <td><?= htmlspecialchars($student['subject_name']) ?></td>
                                    <td><?= htmlspecialchars($student['subject_code']) ?></td>
                                    <td><?= htmlspecialchars($student['faculty']) ?></td>
                                    <td><?= htmlspecialchars($student['course']) ?></td>
                                    <td><?= htmlspecialchars($student['year']) ?></td>
                                    <td><?= htmlspecialchars($student['semester']) ?></td>
                                    <td><?= htmlspecialchars($student['roll_number']) ?></td>
                                    <td>
                                        <a href="dean_subjected_students.php?edit_id=<?= $student['id'] ?>" class="btn btn-sm btn-primary">Edit</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="10" class="text-center">No records found.</td>
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