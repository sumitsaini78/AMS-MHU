<?php
// Database configuration based on your provided schema[cite: 1, 2]
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
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_teacher_subject'])) {
    $id = $_POST['id'];
    $teacher_name = $_POST['teacher_name'];
    $subject_name = $_POST['subject_name'];
    $course_name = $_POST['course_name'];
    $year = $_POST['year'];
    $semester = $_POST['semester'];
    $subject_code = $_POST['subject_code'];

    $stmt = $pdo->prepare("UPDATE subjected_teacher SET teacher_name = ?, subject_name = ?, course_name = ?, year = ?, semester = ?, subject_code = ? WHERE id = ?");
    $stmt->execute([$teacher_name, $subject_name, $course_name, $year, $semester, $subject_code, $id]);
    
    header("Location: dean_subjected_teachers.php?success=1");
    exit();
}

// Fetch record for editing if edit ID is provided
$editData = null;
if (isset($_GET['edit_id'])) {
    $stmt = $pdo->prepare("SELECT * FROM subjected_teacher WHERE id = ?");
    $stmt->execute([$_GET['edit_id']]);
    $editData = $stmt->fetch(PDO::FETCH_ASSOC);
}

// Fetch all subjected teachers from the database[cite: 1, 2]
$stmt = $pdo->query("SELECT * FROM subjected_teacher");
$teachers_list = $stmt->fetchAll(PDO::FETCH_ASSOC);
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

    <?php if (isset($_GET['success'])): ?>
        <div class="alert alert-success">Record successfully updated!</div>
    <?php endif; ?>

    <?php if ($editData): ?>
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
                    <div class="mb-3">
                        <label class="form-label">Subject Name</label>
                        <input type="text" class="form-control" name="subject_name" value="<?= htmlspecialchars($editData['subject_name']) ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Course Name</label>
                        <input type="text" class="form-control" name="course_name" value="<?= htmlspecialchars($editData['course_name']) ?>" required>
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