<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
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
    
    // The dropdown value contains "subject_name|year|semester" combined using a delimiter
    $selected_subject_data = $_POST['subject_data'];
    list($subject_name, $year, $semester) = explode('|', $selected_subject_data);

    $subject_code = $_POST['subject_code'];
    $faculty = $_POST['faculty'];
    $course = $_POST['course'];
    $roll_number = $_POST['roll_number'];

    // Update query now includes the extracted year and semester automatically
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

// Fetch unique combinations of subject name, year, and semester from database
$subjectsStmt = $pdo->query("SELECT DISTINCT subject_name, year, semester FROM subjected_student WHERE subject_name IS NOT NULL AND subject_name != ''");
$availableSubjects = $subjectsStmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch available courses for filter (normalized to group 'B.Com' and 'BCOM' together)
$coursesStmt = $pdo->query("SELECT DISTINCT REPLACE(UPPER(course), '.', '') as norm_course, MAX(course) as course FROM subjected_student WHERE course IS NOT NULL AND course != '' GROUP BY norm_course ORDER BY course ASC");
$availableCourses = $coursesStmt->fetchAll(PDO::FETCH_ASSOC);

// Filter Logic
$filter_course = isset($_GET['filter_course']) ? trim($_GET['filter_course']) : '';
$where_clause = "";
$params = [];
if (!empty($filter_course)) {
    // Check both normalized format to match B.Com with BCOM
    $where_clause = "WHERE REPLACE(UPPER(course), '.', '') = REPLACE(UPPER(?), '.', '')";
    $params[] = $filter_course;
}

// Export Logic
if (isset($_GET['export'])) {
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename=subjected_students_' . date('Y-m-d') . '.csv');
    $output = fopen('php://output', 'w');
    fputcsv($output, ['ID', 'Student Name', 'Subject Name', 'Subject Code', 'Faculty', 'Course', 'Year', 'Semester', 'Roll Number']);
    
    $exportStmt = $pdo->prepare("SELECT id, student_name, subject_name, subject_code, faculty, course, year, semester, roll_number FROM subjected_student $where_clause");
    $exportStmt->execute($params);
    while ($row = $exportStmt->fetch(PDO::FETCH_ASSOC)) {
        fputcsv($output, $row);
    }
    fclose($output);
    exit;
}

// Pagination Logic
$limit = 50;
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) $page = 1;
$offset = ($page - 1) * $limit;

// Count total records for pagination
$countStmt = $pdo->prepare("SELECT COUNT(*) FROM subjected_student $where_clause");
$countStmt->execute($params);
$total_records = $countStmt->fetchColumn();
$total_pages = ceil($total_records / $limit);

// Fetch paginated results
$fetchStmt = $pdo->prepare("SELECT * FROM subjected_student $where_clause LIMIT $limit OFFSET $offset");
$fetchStmt->execute($params);
$students = $fetchStmt->fetchAll(PDO::FETCH_ASSOC);
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

                    <!-- Subject Name Dropdown (Handles Subject, Year, and Semester together) -->
                    <div class="mb-3">
                        <label class="form-label">Select Subject (Includes Year & Semester)</label>
                        <select class="form-select" name="subject_data" required>
                            <option value="">-- Choose Subject, Year & Semester --</option>
                            <?php foreach ($availableSubjects as $sub): ?>
                                <?php 
                                    // Package values together using a pipe separator
                                    $optionValue = $sub['subject_name'] . '|' . $sub['year'] . '|' . $sub['semester'];
                                    
                                    // Label shown to user
                                    $displayLabel = $sub['subject_name'] . ' (Year: ' . $sub['year'] . ', Semester: ' . $sub['semester'] . ')';
                                    
                                    // Check if this matches current record
                                    $isSelected = ($editData['subject_name'] === $sub['subject_name'] && $editData['year'] == $sub['year'] && $editData['semester'] == $sub['semester']);
                                ?>
                                <option value="<?= htmlspecialchars($optionValue) ?>" <?= $isSelected ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($displayLabel) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
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
                        <label class="form-label">Roll Number</label>
                        <input type="number" class="form-control" name="roll_number" value="<?= htmlspecialchars($editData['roll_number']) ?>" required>
                    </div>
                    
                    <button type="submit" name="update_student" class="btn btn-success">Update Record</button>
                    <a href="dean_subjected_students.php" class="btn btn-secondary">Cancel</a>
                </form>
            </div>
        </div>
    <?php endif; ?>

    <!-- Filter and Export Options -->
    <div class="card shadow-sm mb-4">
        <div class="card-body bg-white d-flex flex-wrap justify-content-between align-items-center">
            <form method="GET" action="dean_subjected_students.php" class="d-flex align-items-center gap-2 mb-2 mb-md-0">
                <label for="filter_course" class="fw-bold mb-0 text-nowrap">Filter by Course:</label>
                <select name="filter_course" id="filter_course" class="form-select form-select-sm" style="min-width: 200px;">
                    <option value="">-- All Courses --</option>
                    <?php foreach ($availableCourses as $c): ?>
                        <option value="<?= htmlspecialchars($c['course']) ?>" <?= ($filter_course === $c['course']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($c['course']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <button type="submit" class="btn btn-primary btn-sm">Filter</button>
                <?php if (!empty($filter_course)): ?>
                    <a href="dean_subjected_students.php" class="btn btn-outline-secondary btn-sm">Clear</a>
                <?php endif; ?>
            </form>

            <a href="dean_subjected_students.php?export=1<?= !empty($filter_course) ? '&filter_course=' . urlencode($filter_course) : '' ?>" class="btn btn-success btn-sm fw-bold">
                Export to CSV
            </a>
        </div>
    </div>

    <!-- Data Table View -->
    <div class="card shadow-sm">
        <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
            <span>Subjected Students List</span>
            <span class="badge bg-light text-dark">Total: <?= $total_records ?></span>
        </div>
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

            <!-- Pagination Controls -->
            <?php if ($total_pages > 1): ?>
                <?php include 'dean_navbar.php'; ?>
            <?php endif; ?>
        </div>
    </div>
</div>
</body>
</html>


