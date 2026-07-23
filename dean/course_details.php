<?php
include "../db_connect.php";
session_start();

// Security Check
if (!isset($_SESSION['dean_id'])) {
    header("Location: index.php");
    exit;
}

// 1. Get and sanitize parameters
$course = trim($_GET['course'] ?? $_POST['course'] ?? '');
$subject = trim($_GET['subject'] ?? $_POST['subject'] ?? '');

// If either is missing, redirect back
if (empty($course) || empty($subject)) {
    header("Location: index.php");
    exit;
}

// Date Filter & Session Filter Logic
$today_dmy = date('dmy');
$filter_text = "Showing records for: Today (" . date('d/m/Y') . ")";

$single_date = $_GET['single_date'] ?? '';
$date_from = $_GET['date_from'] ?? '';
$date_to = $_GET['date_to'] ?? '';
$selected_session = trim($_GET['session'] ?? '');

// Fetch distinct sessions for the dropdown filter
$sessions_query = "SELECT DISTINCT session FROM students WHERE session IS NOT NULL AND session != '' ORDER BY session DESC";
$sessions_res = $conn->query($sessions_query);

// 2. Initialize WHERE clauses for attendance table
$where_clauses = ["course = ?", "subject_name = ?"];
$params = [$course, $subject];
$types = "ss";

// Priority: Single Date > Date Range > Default (Today)
if (!empty($single_date)) {
    $s_date = date('dmy', strtotime($single_date));
    $where_clauses[] = "date_of_attendence = ?";
    $params[] = $s_date;
    $types .= "s";
    $filter_text = "For Date: " . htmlspecialchars($single_date);
} elseif (!empty($date_from)) {
    $d_from = date('dmy', strtotime($date_from));
    if (!empty($date_to)) {
        $d_to = date('dmy', strtotime($date_to));
        $where_clauses[] = "date_of_attendence BETWEEN ? AND ?";
        $params[] = $d_from;
        $params[] = $d_to;
        $types .= "ss";
        $filter_text = "From " . htmlspecialchars($date_from) . " to " . htmlspecialchars($date_to);
    } else {
        $where_clauses[] = "date_of_attendence = ?";
        $params[] = $d_from;
        $types .= "s";
        $filter_text = "For Date: " . htmlspecialchars($date_from);
    }
} else {
    $where_clauses[] = "date_of_attendence = ?";
    $params[] = $today_dmy;
    $types .= "s";
}

// Session filter using EXISTS to prevent any table join duplication
if (!empty($selected_session)) {
    $where_clauses[] = "EXISTS (SELECT 1 FROM students s WHERE s.roll_number = attendance.roll_number AND s.session = ?)";
    $params[] = $selected_session;
    $types .= "s";
    $filter_text .= " | Session: " . htmlspecialchars($selected_session);
}

$sql_where = "WHERE " . implode(" AND ", $where_clauses);

// 3. Summary Stats (No JOIN needed)
$stats_query = "SELECT 
                    COUNT(DISTINCT date_of_attendence) as total_lectures,
                    COUNT(*) as total_entries,
                    SUM(CASE WHEN LOWER(attendance_status) IN ('present', '1') THEN 1 ELSE 0 END) as present_count
                FROM attendance $sql_where";

$stmt = $conn->prepare($stats_query);
$stmt->bind_param($types, ...$params);
$stmt->execute();
$stats = $stmt->get_result()->fetch_assoc();

$percentage = ($stats['total_entries'] > 0)
    ? round(($stats['present_count'] / $stats['total_entries']) * 100, 2)
    : 0;

// 4. Detailed Records (Fetching session safely via subquery to avoid duplication)
$list_query = "SELECT attendance.*, 
               (SELECT s.session FROM students s WHERE s.roll_number = attendance.roll_number LIMIT 1) AS session 
               FROM attendance 
               $sql_where 
               ORDER BY date_of_attendence DESC, student_name ASC";
$stmt = $conn->prepare($list_query);
$stmt->bind_param($types, ...$params);
$stmt->execute();
$list_res = $stmt->get_result();
?>

<!doctype html>
<html lang="en">

<head>
    <title>Subject Details | <?= htmlspecialchars($subject) ?> | MHU-AMS</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f4f6f9;
        }

        .stat-card {
            transition: transform 0.2s ease, box-shadow 0.2s ease;
            border: none;
            border-radius: 10px;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.08) !important;
        }

        .table-card,
        .filter-card {
            border: none;
            border-radius: 10px;
            overflow: hidden;
        }

        .table thead th {
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.85rem;
            letter-spacing: 0.5px;
            color: #495057;
        }
    </style>
</head>

<body>
    <div class="container py-4">

        <!-- Header Section -->
        <div class="mb-4">
            <a href="view_subjects_attendance.php?course=<?= urlencode($course) ?>"
                class="btn btn-sm btn-outline-secondary mb-3 shadow-sm rounded-pill px-3">
                <i class="fa-solid fa-arrow-left me-1"></i> Back to Subjects
            </a>

            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                <div>
                    <h2 class="fw-bold text-dark mb-0"><?= htmlspecialchars($subject) ?></h2>
                    <span class="text-muted fs-5 fw-medium"><i class="fa-solid fa-graduation-cap me-1"></i>
                        <?= htmlspecialchars($course) ?></span>
                </div>
                <span class="badge bg-primary fs-6 px-4 py-2 rounded-pill shadow-sm">
                    <i class="fa-regular fa-calendar me-1"></i> <?= $filter_text ?>
                </span>
            </div>
        </div>

        <!-- Summary Stats -->
        <div class="row g-3 mb-4">
            <div class="col-md-4">
                <div class="card stat-card p-3 border-start border-primary border-4 shadow-sm h-100 bg-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <small class="text-muted fw-bold text-uppercase">Total Lectures</small>
                            <h2 class="fw-bold mt-1 text-dark mb-0"><?= $stats['total_lectures'] ?? 0 ?></h2>
                        </div>
                        <div class="text-primary opacity-50"><i class="fa-solid fa-person-chalkboard fa-2x"></i></div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card stat-card p-3 border-start border-success border-4 shadow-sm h-100 bg-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <small class="text-muted fw-bold text-uppercase">Overall Attendance</small>
                            <h2 class="fw-bold mt-1 text-success mb-0"><?= $percentage ?>%</h2>
                        </div>
                        <div class="text-success opacity-50"><i class="fa-solid fa-chart-pie fa-2x"></i></div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card stat-card p-3 border-start border-info border-4 shadow-sm h-100 bg-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <small class="text-muted fw-bold text-uppercase">Total Records</small>
                            <h2 class="fw-bold mt-1 text-dark mb-0"><?= $stats['total_entries'] ?? 0 ?></h2>
                        </div>
                        <div class="text-info opacity-50"><i class="fa-solid fa-users fa-2x"></i></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filter Form (Compact / Smaller Size) -->
        <div class="card filter-card mb-4 shadow-sm">
            <div class="card-body p-3">
                <h6 class="card-title fw-bold text-muted mb-2 small"><i class="fa-solid fa-filter me-1"></i> Filter Attendance Data</h6>
                <form method="GET" class="row g-2 align-items-end">
                    <input type="hidden" name="course" value="<?= htmlspecialchars($course) ?>">
                    <input type="hidden" name="subject" value="<?= htmlspecialchars($subject) ?>">

                    <!-- Single Date Calendar Selector -->
                    <div class="col-md-4">
                        <label class="form-label text-muted fw-medium small mb-1"><i class="fa-regular fa-calendar-days text-primary me-1"></i> Specific Date</label>
                        <input type="date" name="single_date" class="form-control form-control-sm shadow-none"
                            value="<?= htmlspecialchars($single_date) ?>">
                    </div>

                    <!-- Session Filter -->
                    <div class="col-md-4">
                        <label class="form-label text-muted fw-medium small mb-1"><i class="fa-solid fa-graduation-cap text-primary me-1"></i> Session</label>
                        <select name="session" class="form-select form-select-sm shadow-none">
                            <option value="">All Sessions</option>
                            <?php if ($sessions_res): ?>
                                <?php while($s_row = $sessions_res->fetch_assoc()): ?>
                                    <option value="<?= htmlspecialchars($s_row['session']) ?>" <?= ($selected_session === $s_row['session']) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($s_row['session']) ?>
                                    </option>
                                <?php endwhile; ?>
                            <?php endif; ?>
                        </select>
                    </div>

                    <!-- Action Buttons -->
                    <div class="col-md-4 d-flex gap-2">
                        <button type="submit" class="btn btn-sm btn-primary flex-grow-1 shadow-sm">
                            <i class="fa-solid fa-magnifying-glass me-1"></i> Search
                        </button>
                        <a href="?course=<?= urlencode($course) ?>&subject=<?= urlencode($subject) ?>"
                            class="btn btn-sm btn-outline-secondary shadow-sm px-3">Reset</a>
                    </div>

                    <!-- Optional Date Range Row Separator -->
                    <div class="col-12 mt-1">
                        <hr class="text-muted opacity-25 my-1">
                        <small class="text-muted fw-semibold" style="font-size: 0.75rem;">Or Date Range (Optional):</small>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label text-muted fw-medium small mb-1">From Date</label>
                        <input type="date" name="date_from" class="form-control form-control-sm shadow-none"
                            value="<?= htmlspecialchars($date_from) ?>">
                    </div>
                    
                    <div class="col-md-4">
                        <label class="form-label text-muted fw-medium small mb-1">To Date</label>
                        <input type="date" name="date_to" class="form-control form-control-sm shadow-none"
                            value="<?= htmlspecialchars($date_to) ?>">
                    </div>
                </form>
            </div>
        </div>

        <!-- Attendance Records -->
        <div class="card table-card shadow-sm">
            <div class="card-header bg-white py-3 d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                <strong class="fs-5 text-dark"><i class="fa-solid fa-list-check text-primary me-2"></i>Detailed Attendance Logs</strong>
                <div class="d-flex gap-2 flex-wrap">
                    <button onclick="exportTableToExcel('attendanceTable', 'Attendance_Report')"
                        class="btn btn-sm btn-success shadow-sm">
                        <i class="fa-regular fa-file-excel me-1"></i> Export to Excel
                    </button>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-hover table-striped mb-0 align-middle" id="attendanceTable">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">Date</th>
                            <th>Student Name</th>
                            <th>Roll No</th>
                            <th>Session</th>
                            <th>Subject</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($list_res && $list_res->num_rows > 0): ?>
                            <?php while ($row = $list_res->fetch_assoc()):
                                $status_class = (strtolower($row['attendance_status']) === 'present' || $row['attendance_status'] == '1')
                                    ? 'text-success bg-success-subtle' : 'text-danger bg-danger-subtle';
                                $student_session = !empty($row['session']) ? $row['session'] : 'N/A';
                            ?>
                                <tr>
                                    <td class="ps-4 text-muted fw-medium"><?= htmlspecialchars($row['date_of_attendence']) ?></td>
                                    
                                    <!-- Student Name as Clickable Form Submission Link to student_calendar.php -->
                                    <td class="fw-medium">
                                        <form action="student_calendar.php" method="POST" class="d-inline">
                                            <input type="hidden" name="student_roll" value="<?= htmlspecialchars($row['roll_number']) ?>">
                                            <input type="hidden" name="student_name" value="<?= htmlspecialchars($row['student_name']) ?>">
                                            <input type="hidden" name="subject_name" value="<?= htmlspecialchars($subject) ?>">
                                            
                                            <button type="submit" class="btn btn-link text-primary text-decoration-none p-0 fw-medium d-inline-flex align-items-center">
                                                <?= htmlspecialchars($row['student_name']) ?>
                                                <i class="fa-solid fa-circle-info ms-1 small opacity-75"></i>
                                            </button>
                                        </form>
                                    </td>

                                    <td><span class="badge bg-secondary text-white"><?= htmlspecialchars($row['roll_number']) ?></span></td>
                                    <td class="text-muted font-monospace"><?= htmlspecialchars($student_session) ?></td>
                                    <td class="text-muted"><?= htmlspecialchars($row['subject_name'] ?? 'N/A') ?></td>
                                    <td>
                                        <span class="badge <?= $status_class ?> px-3 py-2 rounded-pill">
                                            <?= htmlspecialchars($row['attendance_status']) ?>
                                        </span>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="text-center py-5 text-muted">
                                    <div class="opacity-50">
                                        <i class="fa-solid fa-box-open fa-3x mb-3"></i><br>
                                        <span class="fs-5">No attendance records found for the selected criteria.</span>
                                    </div>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- SheetJS & Bootstrap JS -->
    <script src="https://cdn.sheetjs.com/xlsx-latest/package/dist/xlsx.full.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        function exportTableToExcel(tableID, baseFilename) {
            var table = document.getElementById(tableID);
            if (!table) {
                alert("Table nahi mili!");
                return;
            }
            var d = new Date();
            var dateString = d.getDate() + "-" + (d.getMonth() + 1) + "-" + d.getFullYear();
            var finalFilename = baseFilename + "_" + dateString + ".xlsx";

            var wb = XLSX.utils.table_to_book(table, { sheet: "Attendance Record" });
            XLSX.writeFile(wb, finalFilename);
        }
    </script>
</body>

</html>