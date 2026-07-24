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

// Dynamically check if section or sec column exists in database tables to prevent errors
$section_col = '';
$check_sec_students = $conn->query("SHOW COLUMNS FROM students LIKE 'section'");
if ($check_sec_students && $check_sec_students->num_rows > 0) {
    $section_col = 's.section';
} else {
    $check_sec_att = $conn->query("SHOW COLUMNS FROM attendance LIKE 'section'");
    if ($check_sec_att && $check_sec_att->num_rows > 0) {
        $section_col = 'a.section';
    } else {
        $check_sec_alt = $conn->query("SHOW COLUMNS FROM students LIKE 'sec'");
        if ($check_sec_alt && $check_sec_alt->num_rows > 0) {
            $section_col = 's.sec';
        }
    }
}

// Helper function to build dynamic attendance query and parameters
function buildAttendanceQuery($section_col) {
    global $conn;
    $course_f = $_GET['course'] ?? '';
    $subject_f = $_GET['subject_name'] ?? '';
    $status_f = $_GET['status'] ?? '';
    $session_f = $_GET['session'] ?? '';
    $semester_f = $_GET['semester'] ?? '';
    $section_f = $_GET['section'] ?? '';
    $date_from_f = $_GET['date_from'] ?? '';
    $date_to_f = $_GET['date_to'] ?? '';
    $month_f = $_GET['month'] ?? '';
    $year_f = $_GET['year'] ?? '';
    $student_search = $_GET['student_search'] ?? '';

    // Handle Today's Report quick action button
    if (isset($_GET['today']) && $_GET['today'] == '1') {
        $date_from_f = date('Y-m-d');
        $date_to_f = date('Y-m-d');
    }

    $sql_base = "FROM attendance a LEFT JOIN students s ON a.roll_number = s.roll_number AND a.course = s.course WHERE 1=1";
    $p_arr = [];
    $t_str = "";

    // Student Name or Roll Number Filter
    if (!empty($student_search)) {
        $sql_base .= " AND (a.roll_number LIKE ? OR a.student_name LIKE ?)";
        $search_term = "%" . $student_search . "%";
        $p_arr[] = $search_term;
        $p_arr[] = $search_term;
        $t_str .= "ss";
    }

    if (!empty($course_f)) {
        $sql_base .= " AND a.course = ?";
        $p_arr[] = $course_f;
        $t_str .= "s";
    }
    if (!empty($subject_f)) {
        $sql_base .= " AND a.subject_name = ?";
        $p_arr[] = $subject_f;
        $t_str .= "s";
    }
    if (!empty($status_f)) {
        $sql_base .= " AND a.attendance_status = ?";
        $p_arr[] = $status_f;
        $t_str .= "s";
    }
    if (!empty($session_f)) {
        $sql_base .= " AND a.session = ?";
        $p_arr[] = $session_f;
        $t_str .= "s";
    }
    if (!empty($semester_f)) {
        $sql_base .= " AND s.sem = ?";
        $p_arr[] = $semester_f;
        $t_str .= "s";
    }
    
    // Section Filter (Applied only if section column exists)
    if (!empty($section_f) && !empty($section_col)) {
        $sql_base .= " AND $section_col = ?";
        $p_arr[] = $section_f;
        $t_str .= "s";
    }

    // Date Range Filter (From & To)
    if (!empty($date_from_f) && !empty($date_to_f)) {
        $start = new DateTime($date_from_f);
        $end = new DateTime($date_to_f);
        $end->modify('+1 day');
        $period = new DatePeriod($start, new DateInterval('P1D'), $end);
        $dmy_codes = [];
        foreach ($period as $dt) {
            $dmy_codes[] = (int) $dt->format('dmy');
        }
        if (!empty($dmy_codes)) {
            $placeholders = implode(',', array_fill(0, count($dmy_codes), '?'));
            $sql_base .= " AND a.date_of_attendence IN ($placeholders)";
            foreach ($dmy_codes as $code) {
                $p_arr[] = $code;
                $t_str .= "i";
            }
        }
    } elseif (!empty($date_from_f)) {
        $dmy_val = (int) date('dmy', strtotime($date_from_f));
        $sql_base .= " AND a.date_of_attendence = ?";
        $p_arr[] = $dmy_val;
        $t_str .= "i";
    }

    // Month & Year Filter
    if (empty($date_from_f) && !empty($month_f) && !empty($year_f)) {
        $yy = strlen($year_f) == 4 ? substr($year_f, 2, 2) : $year_f;
        $mm = str_pad($month_f, 2, '0', STR_PAD_LEFT);
        $days_in_month = cal_days_in_month(CAL_GREGORIAN, (int)$mm, (int)('20' . $yy));
        $dmy_codes = [];
        for ($d = 1; $d <= $days_in_month; $d++) {
            $dd = str_pad($d, 2, '0', STR_PAD_LEFT);
            $dmy_codes[] = (int) ($dd . $mm . $yy);
        }
        if (!empty($dmy_codes)) {
            $placeholders = implode(',', array_fill(0, count($dmy_codes), '?'));
            $sql_base .= " AND a.date_of_attendence IN ($placeholders)";
            foreach ($dmy_codes as $code) {
                $p_arr[] = $code;
                $t_str .= "i";
            }
        }
    } elseif (empty($date_from_f) && !empty($year_f)) {
        $yy = strlen($year_f) == 4 ? substr($year_f, 2, 2) : $year_f;
        $start_date = new DateTime("20$yy-01-01");
        $end_date = new DateTime("20$yy-12-31");
        $end_date->modify('+1 day');
        $period = new DatePeriod($start_date, new DateInterval('P1D'), $end_date);
        $dmy_codes = [];
        foreach ($period as $dt) {
            $dmy_codes[] = (int) $dt->format('dmy');
        }
        if (!empty($dmy_codes)) {
            $placeholders = implode(',', array_fill(0, count($dmy_codes), '?'));
            $sql_base .= " AND a.date_of_attendence IN ($placeholders)";
            foreach ($dmy_codes as $code) {
                $p_arr[] = $code;
                $t_str .= "i";
            }
        }
    }

    return [$sql_base, $p_arr, $t_str];
}

// Handle CSV Export Request
if (isset($_GET['export']) && $_GET['export'] == 'csv') {
    list($sql_base, $p_arr, $t_str) = buildAttendanceQuery($section_col);
    $select_sec_sql = !empty($section_col) ? ", $section_col AS student_section" : "";
    $query = "SELECT a.* $select_sec_sql " . $sql_base . " ORDER BY a.id DESC";

    $stmt_exp = $conn->prepare($query);
    if (!empty($p_arr)) {
        $stmt_exp->bind_param($t_str, ...$p_arr);
    }
    $stmt_exp->execute();
    $result_exp = $stmt_exp->get_result();

    // Set headers for file download
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename=Attendance_Report_' . date('Y-m-d') . '.csv');

    $output = fopen('php://output', 'w');
    
    fputcsv($output, ['Student Name', 'Roll Number', 'Course', 'Section', 'Subject Name', 'Date Calendar', 'Status', 'Session']);

    $total_classes = 0;
    $present_count = 0;
    $absent_count = 0;

    while ($row = $result_exp->fetch_assoc()) {
        $total_classes++;
        if (strcasecmp($row['attendance_status'], 'Present') == 0) {
            $present_count++;
        } elseif (strcasecmp($row['attendance_status'], 'Absent') == 0) {
            $absent_count++;
        }

        $sec_val = $row['student_section'] ?? 'N/A';

        fputcsv($output, [
            $row['student_name'],
            $row['roll_number'],
            $row['course'],
            $sec_val,
            $row['subject_name'],
            $row['date_of_attendence'],
            $row['attendance_status'],
            $row['session']
        ]);
    }

    $percentage = $total_classes > 0 ? number_format(($present_count / $total_classes) * 100, 2) . '%' : '0.00%';

    // Summary Statistics 
    fputcsv($output, []);
    fputcsv($output, ['--- SUMMARY STATISTICS ---']);
    fputcsv($output, ['Total Classes', 'Present', 'Absent', 'Percentage']);
    fputcsv($output, [$total_classes, $present_count, $absent_count, $percentage]);

    fclose($output);
    exit;
}

// Fetch filter options dropdowns safely
$courses_result = $conn->query("SELECT DISTINCT course_name FROM courses_list ORDER BY course_name");
$subjects_result = $conn->query("SELECT DISTINCT subject_name FROM subjects ORDER BY subject_name");
$sessions_result = $conn->query("SELECT DISTINCT session FROM attendance WHERE session != '' ORDER BY session DESC");
$semesters_result = $conn->query("SELECT DISTINCT sem AS semester FROM students WHERE sem IS NOT NULL AND sem != '' UNION SELECT DISTINCT semester FROM subjects WHERE semester IS NOT NULL AND semester != '' ORDER BY semester");

$sections_result = null;
if (!empty($section_col)) {
    $sections_result = $conn->query("SELECT DISTINCT " . str_replace('s.', '', $section_col) . " AS section FROM students WHERE " . str_replace('s.', '', $section_col) . " IS NOT NULL AND " . str_replace('s.', '', $section_col) . " != '' ORDER BY section");
}

$months = [
    1 => 'January', 2 => 'February', 3 => 'March', 4 => 'April',
    5 => 'May', 6 => 'June', 7 => 'July', 8 => 'August',
    9 => 'September', 10 => 'October', 11 => 'November', 12 => 'December'
];
$years = [2024, 2025, 2026, 2027];
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
                <p class="text-muted">Filter and export institutional attendance data using student details, courses, sections, and dates.</p>
            </div>
        </div>

        <div class="report-card p-4 p-md-5 mb-5">
            <form method="GET" action="" class="row g-3">
                <!-- Row 1 -->
                <div class="col-md-3">
                    <label class="form-label fw-semibold small text-muted">Quick Report</label>
                    <div>
                        <a href="?today=1" class="btn btn-outline-dark btn-sm w-100 py-2"><i class="fa-solid fa-calendar-day me-1"></i> Today's Report</a>
                    </div>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold small text-muted">Date From</label>
                    <input type="date" class="form-control" name="date_from" value="<?= htmlspecialchars($_GET['date_from'] ?? '') ?>">
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold small text-muted">Date To</label>
                    <input type="date" class="form-control" name="date_to" value="<?= htmlspecialchars($_GET['date_to'] ?? '') ?>">
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold small text-muted">Month Filter</label>
                    <select class="form-select" name="month">
                        <option value="">All Months</option>
                        <?php foreach($months as $m_num => $m_name): ?>
                            <option value="<?= $m_num ?>" <?= (isset($_GET['month']) && $_GET['month'] == $m_num) ? 'selected' : '' ?>><?= $m_name ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Row 2 -->
                <div class="col-md-3">
                    <label class="form-label fw-semibold small text-muted">Year Filter</label>
                    <select class="form-select" name="year">
                        <option value="">All Years</option>
                        <?php foreach($years as $yr): ?>
                            <option value="<?= $yr ?>" <?= (isset($_GET['year']) && $_GET['year'] == $yr) ? 'selected' : '' ?>><?= $yr ?></option>
                        <?php endforeach; ?>
                    </select>
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
                    <label class="form-label fw-semibold small text-muted">Semester</label>
                    <select class="form-select" name="semester">
                        <option value="">All Semesters</option>
                        <?php while($sem_row = $semesters_result->fetch_assoc()): ?>
                            <option value="<?= htmlspecialchars($sem_row['semester']) ?>" <?= (isset($_GET['semester']) && $_GET['semester'] == $sem_row['semester']) ? 'selected' : '' ?>>Semester <?= htmlspecialchars($sem_row['semester']) ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold small text-muted">Section</label>
                    <select class="form-select" name="section">
                        <option value="">All Sections</option>
                        <?php if ($sections_result && $sections_result->num_rows > 0): ?>
                            <?php while($sec_row = $sections_result->fetch_assoc()): ?>
                                <option value="<?= htmlspecialchars($sec_row['section']) ?>" <?= (isset($_GET['section']) && $_GET['section'] == $sec_row['section']) ? 'selected' : '' ?>>Section <?= htmlspecialchars($sec_row['section']) ?></option>
                            <?php endwhile; ?>
                        <?php endif; ?>
                    </select>
                </div>

                <!-- Row 3 -->
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
                    <label class="form-label fw-semibold small text-muted text-primary"><i class="fa-solid fa-user-magnifying-glass me-1"></i> Student Search</label>
                    <input type="text" class="form-control border-primary" name="student_search" placeholder="Name or Roll No" value="<?= htmlspecialchars($_GET['student_search'] ?? '') ?>">
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

                <!-- Row 4: Action Buttons -->
                <div class="col-12 d-flex align-items-end gap-2 mt-4 pt-2 border-top">
                    <button type="submit" class="btn btn-dark px-4 py-2 fw-semibold"><i class="fa-solid fa-filter me-2"></i>Filter Preview</button>
                    <button type="submit" name="export" value="csv" class="btn btn-success px-4 py-2 fw-semibold"><i class="fa-solid fa-file-excel me-2"></i>Download CSV Report</button>
                    <a href="attendance_report.php" class="btn btn-outline-secondary px-4 py-2">Reset All</a>
                </div>
            </form>
        </div>

        <!-- Live Preview Table -->
        <?php
        list($sql_base, $p_arr, $t_str) = buildAttendanceQuery($section_col);
        $select_sec_sql = !empty($section_col) ? ", $section_col AS student_section" : "";
        $sql_prev = "SELECT a.* $select_sec_sql " . $sql_base . " ORDER BY a.id DESC LIMIT 100";
        
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
                            <th>Student Name</th>
                            <th>Roll Number</th>
                            <th>Course</th>
                            <th>Section</th>
                            <th>Subject Name</th>
                            <th>Date Calendar</th>
                            <th>Status</th>
                            <th>Session</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($preview_res->num_rows > 0): ?>
                            <?php while($row = $preview_res->fetch_assoc()): ?>
                                <tr>
                                    <td class="fw-semibold"><?= htmlspecialchars($row['student_name']) ?></td>
                                    <td><code><?= htmlspecialchars($row['roll_number']) ?></code></td>
                                    <td><?= htmlspecialchars($row['course']) ?></td>
                                    <td><span class="badge bg-primary-subtle text-primary"><?= htmlspecialchars($row['student_section'] ?? 'N/A') ?></span></td>
                                    <td><?= htmlspecialchars($row['subject_name']) ?></td>
                                    <td><code><?= htmlspecialchars($row['date_of_attendence']) ?></code></td>
                                    <td>
                                        <span class="badge bg-<?= $row['attendance_status'] == 'Present' ? 'success' : 'danger' ?>-subtle text-<?= $row['attendance_status'] == 'Present' ? 'success' : 'danger' ?> px-3 py-1">
                                            <?= htmlspecialchars($row['attendance_status']) ?>
                                        </span>
                                    </td>
                                    <td><span class="badge bg-secondary-subtle text-secondary"><?= htmlspecialchars($row['session']) ?></span></td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="8" class="text-center text-muted py-4">No attendance records found for the selected criteria.</td>
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