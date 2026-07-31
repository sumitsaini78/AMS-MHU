<?php
include "../db_connect.php";
session_start();

// Security Check
if (!isset($_SESSION['admin_id'])) {
    header("Location: ../index.php");
    exit;
}

$msg = "";
$error = "";

// --- HANDLER: UPDATE ROLL & ENROLLMENT NUMBER ---
if (isset($_POST['update_student_numbers'])) {
    $student_id = intval($_POST['student_id']);
    $roll_number = trim($_POST['roll_number']);
    $enrollment_number = trim($_POST['enrollment_number']);

    if ($student_id > 0 && !empty($roll_number) && !empty($enrollment_number)) {
        // Unique Check for Enrollment Number (Since it's Unique in DB)
        $check_stmt = $conn->prepare("SELECT id FROM students WHERE enrollment_number = ? AND id != ?");
        $check_stmt->bind_param("si", $enrollment_number, $student_id);
        $check_stmt->execute();
        $check_stmt->store_result();

        if ($check_stmt->num_rows > 0) {
            $error = "Enrollment Number '$enrollment_number' already assigned to another student!";
        } else {
            // Update Query
            $stmt = $conn->prepare("UPDATE students SET roll_number = ?, enrollment_number = ? WHERE id = ?");
            $stmt->bind_param("ssi", $roll_number, $enrollment_number, $student_id);

            if ($stmt->execute()) {
                $msg = "Roll Number and Enrollment Number updated successfully!";
            } else {
                $error = "Update Error: " . $conn->error;
            }
            $stmt->close();
        }
        $check_stmt->close();
    } else {
        $error = "Please fill in both Roll Number and Enrollment Number.";
    }
}

// --- FILTER LOGIC ---
$filter_course = trim($_GET['course'] ?? '');
$filter_session = trim($_GET['session'] ?? '');
$filter_sem = trim($_GET['sem'] ?? '');

$where_clauses = [];
if (!empty($filter_course)) {
    $where_clauses[] = "course = '" . $conn->real_escape_string($filter_course) . "'";
}
if (!empty($filter_session)) {
    $where_clauses[] = "session = '" . $conn->real_escape_string($filter_session) . "'";
}
if (!empty($filter_sem)) {
    $where_clauses[] = "sem = " . intval($filter_sem);
}

$sql_where = "";
if (count($where_clauses) > 0) {
    $sql_where = " WHERE " . implode(" AND ", $where_clauses);
}

// Fetch Students according to Filter
$students_query = "SELECT * FROM students" . $sql_where . " ORDER BY id DESC";
$students_result = $conn->query($students_query);

// Fetch Dropdown Options Dynamically
$courses_res = $conn->query("SELECT DISTINCT course_name FROM courses_list ORDER BY course_name ASC");
$sessions_res = $conn->query("SELECT DISTINCT session FROM students WHERE session != '' ORDER BY session DESC");
?>
<!doctype html>
<html lang="en">
<head>
    <title>Update Roll & Enrollment Numbers | Admin</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        body { background-color: #f4f6f9; font-family: 'Segoe UI', system-ui, sans-serif; }
    </style>
</head>
<body>
    <nav class="navbar navbar-dark bg-dark shadow-sm py-3">
        <div class="container">
            <a class="navbar-brand fw-bold" href="index.php"><i class="fa-solid fa-shield-halved text-danger me-2"></i> MHU-AMS ADMIN</a>
            <a href="index.php" class="btn btn-sm btn-outline-light"><i class="fa-solid fa-arrow-left me-1"></i> Dashboard</a>
        </div>
    </nav>

    <main class="container py-5">
        <h2 class="fw-bold text-dark mb-4"><i class="fa-solid fa-id-card text-warning me-2"></i>Update Roll & Enrollment Numbers</h2>

        <?php if (!empty($msg)): ?>
            <div class="alert alert-success rounded-4"><?= htmlspecialchars($msg) ?></div>
        <?php endif; ?>
        <?php if (!empty($error)): ?>
            <div class="alert alert-danger rounded-4"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <!-- FILTER FORM CARD -->
        <div class="card border-0 shadow-sm p-4 rounded-4 bg-white mb-4">
            <h6 class="fw-bold mb-3"><i class="fa-solid fa-filter me-2 text-primary"></i>Filter Students</h6>
            <form method="GET" class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label class="form-label small fw-semibold">Course</label>
                    <select name="course" class="form-select">
                        <option value="">All Courses</option>
                        <?php while ($c = $courses_res->fetch_assoc()): ?>
                            <option value="<?= htmlspecialchars($c['course_name']) ?>" <?= ($filter_course == $c['course_name']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($c['course_name']) ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="form-label small fw-semibold">Session</label>
                    <select name="session" class="form-select">
                        <option value="">All Sessions</option>
                        <?php while ($s = $sessions_res->fetch_assoc()): ?>
                            <option value="<?= htmlspecialchars($s['session']) ?>" <?= ($filter_session == $s['session']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($s['session']) ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="form-label small fw-semibold">Semester</label>
                    <select name="sem" class="form-select">
                        <option value="">All Semesters</option>
                        <?php for ($i = 1; $i <= 8; $i++): ?>
                            <option value="<?= $i ?>" <?= ($filter_sem == (string)$i) ? 'selected' : '' ?>>Semester <?= $i ?></option>
                        <?php endfor; ?>
                    </select>
                </div>

                <div class="col-md-3 d-flex gap-2">
                    <button type="submit" class="btn btn-primary rounded-pill w-100 fw-semibold">
                        <i class="fa-solid fa-magnifying-glass me-1"></i> Filter
                    </button>
                    <a href="add_roll_enroll_number.php" class="btn btn-outline-secondary rounded-pill w-100 fw-semibold">
                        Reset
                    </a>
                </div>
            </form>
        </div>

        <!-- STUDENTS TABLE CARD -->
        <div class="card border-0 shadow-sm p-4 rounded-4 bg-white">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="fw-bold mb-0">Students List</h5>
                <span class="badge bg-secondary rounded-pill px-3"><?= $students_result ? $students_result->num_rows : 0 ?> Records</span>
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Student Name</th>
                            <th>Faculty</th>
                            <th>Course</th>
                            <th>Sem / Year</th>
                            <th>Session</th>
                            <th>Roll Number</th>
                            <th>Enrollment Number</th>
                            <th class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($students_result && $students_result->num_rows > 0): $i = 1; ?>
                            <?php while ($row = $students_result->fetch_assoc()): ?>
                                <tr>
                                    <td><?= $i++ ?></td>
                                    <td class="fw-semibold"><?= htmlspecialchars($row['name']) ?></td>
                                    <td><span class="badge bg-primary-subtle text-primary"><?= htmlspecialchars($row['faculty']) ?></span></td>
                                    <td><?= htmlspecialchars($row['course']) ?></td>
                                    <td>Sem: <?= htmlspecialchars($row['sem']) ?> (Year: <?= htmlspecialchars($row['year']) ?>)</td>
                                    <td><?= htmlspecialchars($row['session']) ?></td>
                                    
                                    <form method="POST" class="d-inline">
                                        <input type="hidden" name="student_id" value="<?= $row['id'] ?>">
                                        <td>
                                            <input type="text" name="roll_number" value="<?= htmlspecialchars($row['roll_number']) ?>" class="form-control form-control-sm" placeholder="Roll No" required>
                                        </td>
                                        <td>
                                            <input type="text" name="enrollment_number" value="<?= htmlspecialchars($row['enrollment_number']) ?>" class="form-control form-control-sm" placeholder="Enrollment No" required>
                                        </td>
                                        <td class="text-center">
                                            <button type="submit" name="update_student_numbers" class="btn btn-sm btn-success rounded-pill px-3">
                                                <i class="fa-solid fa-check me-1"></i> Update
                                            </button>
                                        </td>
                                    </form>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="9" class="text-center text-muted py-4">No students found matching the selected filter criteria.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>