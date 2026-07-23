<?php
include "../db_connect.php";
session_start();

// Check if Dean is logged in
if (!isset($_SESSION['dean_id'])) {
    header("Location: ../index.php");
    exit;
}

$dean_id = $_SESSION['dean_id'];

// Fetch Dean details
$stmt = $conn->prepare("SELECT * FROM deans WHERE id = ?");
$stmt->bind_param("i", $dean_id);
$stmt->execute();
$dean = $stmt->get_result()->fetch_assoc();
$dean_name = $dean['Dean_name'] ?? 'Dean';

// Handle CSV Export Request
if (isset($_GET['export']) && $_GET['export'] == 'csv') {
    $course = $_GET['course'] ?? '';
    $subject_name = $_GET['subject_name'] ?? '';
    $status = $_GET['status'] ?? '';
    $date_input = $_GET['date'] ?? '';
    $session_filter = $_GET['session'] ?? '';

    $query = "SELECT * FROM attendance WHERE 1=1";
    $params = [];
    $types = "";

    if (!empty($course)) {
        $query .= " AND course = ?";
        $params[] = $course;
        $types .= "s";
    }
    if (!empty($subject_name)) {
        $query .= " AND subject_name = ?";
        $params[] = $subject_name;
        $types .= "s";
    }
    if (!empty($status)) {
        $query .= " AND attendance_status = ?";
        $params[] = $status;
        $types .= "s";
    }
    if (!empty($date_input)) {
        $dmy = (int) date('dmy', strtotime($date_input));
        $query .= " AND date_of_attendence = ?";
        $params[] = $dmy;
        $types .= "i";
    }
    if (!empty($session_filter)) {
        $query .= " AND session = ?";
        $params[] = $session_filter;
        $types .= "s";
    }

    $query .= " ORDER BY id DESC";

    $stmt_exp = $conn->prepare($query);
    if (!empty($params)) {
        $stmt_exp->bind_param($types, ...$params);
    }
    $stmt_exp->execute();
    $result_exp = $stmt_exp->get_result();

    // Set headers for file download
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename=Attendance_Report_' . date('Y-m-d') . '.csv');

    $output = fopen('php://output', 'w');
    fputcsv($output, ['Attendance ID', 'Date Code', 'Roll Number', 'Student Name', 'Course', 'Subject Name', 'Status', 'Session']);

    while ($row = $result_exp->fetch_assoc()) {
        fputcsv($output, [
            $row['id'],
            $row['date_of_attendence'],
            $row['roll_number'],
            $row['student_name'],
            $row['course'],
            $row['subject_name'],
            $row['attendance_status'],
            $row['session']
        ]);
    }
    fclose($output);
    exit;
}

// Fetch filter options dropdowns
$courses_result = $conn->query("SELECT DISTINCT course_name FROM courses_list ORDER BY course_name");
$subjects_result = $conn->query("SELECT DISTINCT subject_name FROM subjects ORDER BY subject_name");
$sessions_result = $conn->query("SELECT DISTINCT session FROM attendance WHERE session != '' ORDER BY session DESC");
?>

<!doctype html>
<html lang="en" data-bs-theme="light">
<head>
    <title>Dean Attendance Reports | MHU-AMS</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        body { background-color: #f4f6f9; font-family: 'Segoe UI', system-ui, sans-serif; }
        .report-card { border: none; border-radius: 16px; background: #ffffff; box-shadow: 0 8px 20px rgba(0, 0, 0, 0.04); }
    </style>
</head>
<body>
    <header class="navbar navbar-dark bg-dark shadow-sm py-3">
        <div class="container">
            <a class="navbar-brand fw-bold fs-5" href="index.php">
                <i class="fa-solid fa-user-tie text-info me-2"></i> Dean Portal &bull; Attendance Reports
            </a>
            <div class="d-flex align-items-center gap-3">
                <span class="text-white small"><i class="fa-solid fa-user-shield me-1 text-info"></i><strong><?= htmlspecialchars($dean_name) ?></strong></span>
                <a href="../logout.php" class="btn btn-sm btn-outline-danger px-3"><i class="fa-solid fa-power-off"></i></a>
            </div>
        </div>
    </header>

    <main class="container py-5">
        <div class="row mb-4">
            <div class="col-12">
                <h2 class="fw-bold text-dark mb-1">Download Attendance Reports</h2>
                <p class="text-muted">Filter and export institutional attendance data including session criteria.</p>
            </div>
        </div>

        <div class="report-card p-4 p-md-5 mb-5">
            <form method="GET" action="" class="row g-3">
                <div class="col-md-3">
                    <label class="form-label fw-semibold small text-muted">Filter Date</label>
                    <input type="date" class="form-control" name="date" value="<?= htmlspecialchars($_GET['date'] ?? '') ?>">
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold small text-muted">Course / Program</label>
                    <select class="form-select" name="course">
                        <option value="">All Courses</option>
                        <?php while($c = $courses_result->fetch_assoc()): ?>
                            <option value="<?= htmlspecialchars($c['course_name']) ?>" <?= (isset($_GET['course']) && $_GET['course'] == $c['course_name']) ? 'selected' : '' ?>><?= htmlspecialchars($c['course_name']) ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold small text-muted">Subject</label>
                    <select class="form-select" name="subject_name">
                        <option value="">All Subjects</option>
                        <?php while($s = $subjects_result->fetch_assoc()): ?>
                            <option value="<?= htmlspecialchars($s['subject_name']) ?>" <?= (isset($_GET['subject_name']) && $_GET['subject_name'] == $s['subject_name']) ? 'selected' : '' ?>><?= htmlspecialchars($s['subject_name']) ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold small text-muted">Attendance Status</label>
                    <select class="form-select" name="status">
                        <option value="">All Status</option>
                        <option value="Present" <?= (isset($_GET['status']) && $_GET['status'] == 'Present') ? 'selected' : '' ?>>Present</option>
                        <option value="Absent" <?= (isset($_GET['status']) && $_GET['status'] == 'Absent') ? 'selected' : '' ?>>Absent</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold small text-muted">Session</label>
                    <select class="form-select" name="session">
                        <option value="">All Sessions</option>
                        <?php while($sess = $sessions_result->fetch_assoc()): ?>
                            <option value="<?= htmlspecialchars($sess['session']) ?>" <?= (isset($_GET['session']) && $_GET['session'] == $sess['session']) ? 'selected' : '' ?>><?= htmlspecialchars($sess['session']) ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="col-md-9 d-flex align-items-end gap-2 mt-3">
                    <button type="submit" class="btn btn-dark px-4 py-2 fw-semibold"><i class="fa-solid fa-filter me-2"></i>Filter Preview</button>
                    <button type="submit" name="export" value="csv" class="btn btn-success px-4 py-2 fw-semibold"><i class="fa-solid fa-file-excel me-2"></i>Download CSV Report</button>
                    <a href="attendance_report.php" class="btn btn-outline-secondary px-3 py-2">Reset</a>
                </div>
            </form>
        </div>

        <!-- Live Preview Table -->
        <?php
        $course_f = $_GET['course'] ?? '';
        $subject_f = $_GET['subject_name'] ?? '';
        $status_f = $_GET['status'] ?? '';
        $date_f = $_GET['date'] ?? '';
        $session_f = $_GET['session'] ?? '';

        $sql_prev = "SELECT * FROM attendance WHERE 1=1";
        $p_arr = [];
        $t_str = "";

        if (!empty($course_f)) {
            $sql_prev .= " AND course = ?";
            $p_arr[] = $course_f;
            $t_str .= "s";
        }
        if (!empty($subject_f)) {
            $sql_prev .= " AND subject_name = ?";
            $p_arr[] = $subject_f;
            $t_str .= "s";
        }
        if (!empty($status_f)) {
            $sql_prev .= " AND attendance_status = ?";
            $p_arr[] = $status_f;
            $t_str .= "s";
        }
        if (!empty($date_f)) {
            $dmy_val = (int) date('dmy', strtotime($date_f));
            $sql_prev .= " AND date_of_attendence = ?";
            $p_arr[] = $dmy_val;
            $t_str .= "i";
        }
        if (!empty($session_f)) {
            $sql_prev .= " AND session = ?";
            $p_arr[] = $session_f;
            $t_str .= "s";
        }

        $sql_prev .= " ORDER BY id DESC LIMIT 100";
        $stmt_p = $conn->prepare($sql_prev);
        if (!empty($p_arr)) {
            $stmt_p->bind_param($t_str, ...$p_arr);
        }
        $stmt_p->execute();
        $preview_res = $stmt_p->get_result();
        ?>
        <div class="report-card p-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="fw-bold text-dark mb-0"><i class="fa-solid fa-table-list text-info me-2"></i>Report Preview (Showing up to 100 records)</h5>
                <span class="badge bg-info-subtle text-info px-3 py-2">Records Found: <?= $preview_res->num_rows ?></span>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Date Code</th>
                            <th>Roll Number</th>
                            <th>Student Name</th>
                            <th>Course</th>
                            <th>Subject Name</th>
                            <th>Session</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($preview_res->num_rows > 0): ?>
                            <?php while($row = $preview_res->fetch_assoc()): ?>
                                <tr>
                                    <td><code><?= htmlspecialchars($row['date_of_attendence']) ?></code></td>
                                    <td><code><?= htmlspecialchars($row['roll_number']) ?></code></td>
                                    <td class="fw-semibold"><?= htmlspecialchars($row['student_name']) ?></td>
                                    <td><?= htmlspecialchars($row['course']) ?></td>
                                    <td><?= htmlspecialchars($row['subject_name']) ?></td>
                                    <td><span class="badge bg-secondary-subtle text-secondary"><?= htmlspecialchars($row['session']) ?></span></td>
                                    <td>
                                        <span class="badge bg-<?= $row['attendance_status'] == 'Present' ? 'success' : 'danger' ?>-subtle text-<?= $row['attendance_status'] == 'Present' ? 'success' : 'danger' ?> px-3 py-1">
                                            <?= htmlspecialchars($row['attendance_status']) ?>
                                        </span>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" class="text-center text-muted py-4">No attendance records found.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>

    <footer class="text-center py-4 text-muted border-top bg-white mt-5">
        <small>&copy; 2026 Motherhood University Attendance Management System &bull; Dean Report Portal</small>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>