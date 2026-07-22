<?php
include "../db_connect.php";

session_start();
if (!isset($_SESSION['dean_name']) || $_SESSION['dean_name'] == null) {
    header("Location: ../index.php");
    exit();
}

// 1. Process incoming context metrics via POST and shift them into Session storage (PRG Pattern)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['student_roll'])) {
        $_SESSION['student_roll'] = $_POST['student_roll'];
        $_SESSION['student_name'] = $_POST['student_name'];
        
        if (isset($_POST['subject_name'])) {
            $_SESSION['subject_name'] = $_POST['subject_name'];
        }
        if (isset($_POST['course'])) {
            $_SESSION['course'] = $_POST['course'];
        }
        if (isset($_POST['semester'])) {
            $_SESSION['semester'] = $_POST['semester'];
        }
        if (isset($_POST['session'])) {
            $_SESSION['session'] = $_POST['session'];
        }
        if (isset($_POST['academic_year'])) {
            $_SESSION['academic_year'] = $_POST['academic_year'];
        }

        unset($_SESSION['calendar_month']); // Reset month filter on new student select
        unset($_SESSION['search_start']);
        unset($_SESSION['search_end']);
    }
    
    // Handle month filter switching
    if (isset($_POST['filter_month'])) {
        $_SESSION['calendar_month'] = $_POST['selected_month'];
        unset($_SESSION['search_start']); // Clear range if switching back to month view
        unset($_SESSION['search_end']);
    }
    
    // Handle date range filtering
    if (isset($_POST['search_range'])) {
        $_SESSION['search_start'] = $_POST['start_date'];
        $_SESSION['search_end'] = $_POST['end_date'];
        unset($_SESSION['calendar_month']);
    }

    // NEW: Handle Session, Year, and Semester Search/Update
    if (isset($_POST['filter_session_sem'])) {
        if (!empty($_POST['session_val'])) {
            $_SESSION['session'] = $_POST['session_val'];
        }
        if (!empty($_POST['academic_year_val'])) {
            $_SESSION['academic_year'] = $_POST['academic_year_val'];
        }
        if (!empty($_POST['semester_val'])) {
            $_SESSION['semester'] = $_POST['semester_val'];
        }
    }
    
    // NEW: If the user clicked "Export to Excel", DO NOT redirect. 
    // Let the code fall through to generate the file.
    if (!isset($_POST['export_excel'])) {
        header("Location: student_calendar.php");
        exit();
    }
}

// 2. Fallback protection routing check
if (!isset($_SESSION['student_roll']) || !isset($_SESSION['subject_name'])) {
    header("Location: student_attendence.php");
    exit();
}

$student_roll   = $_SESSION['student_roll'];
$student_name   = $_SESSION['student_name'];
$subject_name   = $_SESSION['subject_name'];
$course_val     = $_SESSION['course'] ?? 'N/A';
$semester_val   = $_SESSION['semester'] ?? 'N/A';
$session_val    = $_SESSION['session'] ?? '2025-2026';
$academic_year  = $_SESSION['academic_year'] ?? '1st Year';

// Define defaults for date inputs (Today's Date)
$start_date_default = $_SESSION['search_start'] ?? date('Y-m-d');
$end_date_default = $_SESSION['search_end'] ?? date('Y-m-d');
$is_range_search = isset($_SESSION['search_start']) && isset($_SESSION['search_end']);

// 3. Establish targeted calendar parameters (defaults to current month/year if unset)
$target_month_str = $_SESSION['calendar_month'] ?? date('Y-m');
$year = (int)date('Y', strtotime($target_month_str));
$month = (int)date('m', strtotime($target_month_str));

// Calculate calendar helper variables
$days_in_month = cal_days_in_month(CAL_GREGORIAN, $month, $year);
$first_day_timestamp = mktime(0, 0, 0, $month, 1, $year);
$first_day_of_week = (int)date('w', $first_day_timestamp); // 0 (Sunday) to 6 (Saturday)

// 4. Fetch all attendance metrics for this specific student, subject, and month
$attendance_data = [];
$present_count = 0;
$absent_count = 0;

$query = "SELECT date_of_attendence, attendance_status FROM `attendance` 
          WHERE roll_number = ? AND subject_name = ?";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "ss", $student_roll, $subject_name);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

while ($row = mysqli_fetch_assoc($result)) {
    $raw_date = str_pad($row['date_of_attendence'], 6, "0", STR_PAD_LEFT); // Ensure dmy format padding
    
    // Parse out structural datetime signatures reliably
    $d = substr($raw_date, 0, 2);
    $m = substr($raw_date, 2, 2);
    $y = "20" . substr($raw_date, 4, 2); // Assuming 21st century format standard
    
    $is_record_matched = false;
    $day_key = null;

    if ($is_range_search) {
        // Date parsing verification for YYYY-MM-DD range system
        $formatted_row_date = "$y-$m-$d";
        if ($formatted_row_date >= $_SESSION['search_start'] && $formatted_row_date <= $_SESSION['search_end']) {
            $is_record_matched = true;
            $day_key = $formatted_row_date; 
        }
    } else {
        // Check if record falls inside the currently viewed month window scope
        if ((int)$m === $month && (int)$y === $year) {
            $is_record_matched = true;
            $day_key = (int)$d;
        }
    }

    if ($is_record_matched && $day_key !== null) {
        $status = strtolower($row['attendance_status']);
        
        if ($status === 'present' || $status === '1') {
            $attendance_data[$day_key] = 'present';
            $present_count++;
        } else {
            $attendance_data[$day_key] = 'absent';
            $absent_count++;
        }
    }
}
// Sort by date key chronologically if rendering list views
if ($is_range_search) {
    ksort($attendance_data);
}

$total_lectures = $present_count + $absent_count;
$attendance_rate = ($total_lectures > 0) ? round(($present_count / $total_lectures) * 100, 1) : 0;

// =========================================================================
// EXPORT TO EXCEL LOGIC (Downloads a CSV Native to Excel)
// =========================================================================
if (isset($_POST['export_excel']) && $is_range_search) {
    if (ob_get_length()) ob_clean();
    
    $filename = "Attendance_Report_" . $student_roll . "_" . date('Ymd') . ".csv";
    
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    
    $output = fopen('php://output', 'w');
    
    fputcsv($output, ['MHU-AMS Date Range Attendance Report']);
    fputcsv($output, ['Student Name:', $student_name, 'Roll Number:', $student_roll]);
    fputcsv($output, ['Course:', $course_val, 'Academic Year:', $academic_year, 'Semester:', $semester_val, 'Session:', $session_val]);
    fputcsv($output, ['Subject:', $subject_name, 'Range:', $_SESSION['search_start'] . ' to ' . $_SESSION['search_end']]);
    fputcsv($output, ['Total Lectures:', $total_lectures, 'Present:', $present_count, 'Absent:', $absent_count, 'Attendance %:', $attendance_rate . '%']);
    fputcsv($output, []); 
    
    fputcsv($output, ['Date', 'Day', 'Attendance Status']);
    
    foreach ($attendance_data as $date_key => $status_val) {
        $display_date = date('d-M-Y', strtotime($date_key));
        $day_name = date('l', strtotime($date_key)); 
        fputcsv($output, [$display_date, $day_name, ucfirst($status_val)]);
    }
    
    fclose($output);
    exit(); 
}
?>
<!doctype html>
<html lang="en" data-bs-theme="light">

<head>
    <title>Student Attendance Calendar</title>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />

    <!-- Bootstrap CSS v5.3.8 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous" />
    
    <!-- FontAwesome for UI Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" />

    <style>
        body { background-color: #f8f9fa; }
        #mhu-text { color: #a2c250; text-shadow: 1px 2px 14px rgb(46 195 41); }
        .custom-card { background: #ffffff; border: none; border-radius: 14px; }
        
        .calendar-grid {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            gap: 8px;
        }
        .calendar-weekday {
            text-align: center;
            font-weight: 600;
            color: #6c757d;
            font-size: 0.85rem;
            padding: 8px 0;
            text-transform: uppercase;
        }
        .calendar-day {
            aspect-ratio: 1;
            background-color: #fdfdfd;
            border: 1px solid #e9ecef;
            border-radius: 8px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            padding: 6px;
            font-weight: 500;
            color: #495057;
            position: relative;
            transition: all 0.2s ease;
        }
        .calendar-day:hover { transform: translateY(-2px); box-shadow: 0 4px 8px rgba(0,0,0,0.05); }
        .calendar-day.empty { background-color: #f1f3f5; border: none; cursor: default; pointer-events: none; }
        
        .day-present { background-color: #d1e7dd !important; border-color: #a3cfbb !important; color: #0f5132 !important; }
        .day-absent { background-color: #f8d7da !important; border-color: #f5c2c7 !important; color: #842029 !important; }
        
        .status-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            display: inline-block;
            margin-right: 4px;
        }
    </style>
</head>

<body>
    <header>
        <nav class="navbar navbar-dark bg-dark shadow-sm">
            <div class="container-fluid">
                <span class="navbar-brand mb-0 h1 fs-3 fw-bold" id="mhu-text"> MHU-AMS <sub>Calendar-View</sub></span>
                <div class="d-flex align-items-center">
                    <span class="navbar-text text-light small me-3">
                        <i class="fa-solid fa-user-tie me-1 text-muted"></i> Welcome, <?php echo htmlspecialchars($_SESSION['dean_name'] ?? 'Dean'); ?>
                    </span>
                    <a href="student_attendence.php" class="btn btn-sm btn-outline-info me-2">
                        <i class="fa-solid fa-arrow-left me-1"></i> Back to List
                    </a>
                </div>
            </div>
        </nav>
    </header>

    <main class="container py-4">
        <!-- Overview Dashboard Section Header Layout Grid -->
        <div class="row g-4 mb-4 justify-content-center">
            <div class="col-12 col-lg-10">
                <div class="custom-card shadow-sm p-4 border">
                    <div class="row align-items-center">
                        <div class="col-md-7 border-end border-light">
                            <span class="text-uppercase text-muted fw-bold mb-1 d-block" style="font-size: 0.75rem;">Student Focus Profile</span>
                            <h3 class="fw-bold text-dark mb-1">
                                <i class="fa-solid fa-circle-user text-primary me-2"></i><?php echo htmlspecialchars($student_name); ?>
                            </h3>
                            <p class="text-muted mb-1 font-monospace small">Roll Number: <span class="text-dark fw-semibold"><?php echo htmlspecialchars($student_roll); ?></span></p>
                            <p class="text-muted mb-1 small">Course/Yr/Sem: <span class="text-dark fw-semibold"><?php echo htmlspecialchars($course_val); ?> | <?php echo htmlspecialchars($academic_year); ?> (Sem: <?php echo htmlspecialchars($semester_val); ?>)</span></p>
                            <p class="text-muted mb-1 small">Session: <span class="text-dark fw-semibold"><?php echo htmlspecialchars($session_val); ?></span></p>
                            <div class="text-secondary small">
                                <i class="fa-solid fa-book-open me-1 text-muted"></i> Subject Track Target: <span class="text-dark fw-semibold"><?php echo htmlspecialchars($subject_name); ?></span>
                            </div>
                        </div>
                        <div class="col-md-5 ps-md-4 mt-3 mt-md-0">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <span class="text-secondary small"><?php echo $is_range_search ? 'Filtered' : 'Month'; ?> Attendance Metrics Rate:</span>
                                <span class="fw-bold fs-5 <?php echo $attendance_rate >= 75 ? 'text-success' : ($attendance_rate >= 50 ? 'text-warning' : 'text-danger'); ?>">
                                    <?php echo $attendance_rate; ?>%
                                </span>
                            </div>
                            <div class="d-flex gap-2">
                                <div class="bg-light p-2 rounded text-center flex-fill border">
                                    <div class="text-muted small">Total</div>
                                    <div class="fs-6 fw-bold text-dark"><?php echo $present_count+$absent_count; ?></div>
                                </div>
                                <div class="bg-light p-2 rounded text-center flex-fill border">
                                    <div class="text-success small">Present</div>
                                    <div class="fs-6 fw-bold text-success"><?php echo $present_count; ?></div>
                                </div>
                                <div class="bg-light p-2 rounded text-center flex-fill border">
                                    <div class="text-danger small">Absent</div>
                                    <div class="fs-6 fw-bold text-danger"><?php echo $absent_count; ?></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Filter Toolbar + Calendar Area -->
        <div class="row justify-content-center">
            <div class="col-12 col-lg-10">
                <div class="custom-card shadow-sm p-4 border">
                    
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 border-bottom pb-3 mb-4">
                        <h4 class="fw-bold text-secondary mb-0">
                            <i class="fa-regular fa-calendar-check text-primary me-2"></i><?php echo $is_range_search ? "Range Search View" : date('F Y', $first_day_timestamp); ?>
                        </h4>
                        
                        <div class="d-flex flex-wrap align-items-center gap-3 msg-filter-container">
                            
                            <!-- NEW: Session, Academic Year & Semester Search Form -->
                            <form method="POST" action="student_calendar.php" class="d-flex align-items-center gap-2">
                                <label class="small text-muted text-nowrap fw-medium">Session/Sem:</label>
                                <input type="text" name="session_val" class="form-control form-control-sm border-secondary-subtle" style="width: 100px;" value="<?php echo htmlspecialchars($session_val); ?>" placeholder="Session" title="Session (e.g. 2025-2026)">
                                <input type="text" name="academic_year_val" class="form-control form-control-sm border-secondary-subtle" style="width: 85px;" value="<?php echo htmlspecialchars($academic_year); ?>" placeholder="Year" title="Academic Year">
                                <input type="text" name="semester_val" class="form-control form-control-sm border-secondary-subtle" style="width: 55px;" value="<?php echo htmlspecialchars($semester_val); ?>" placeholder="Sem" title="Semester">
                                <button type="submit" name="filter_session_sem" class="btn btn-sm btn-secondary px-2 shadow-sm" title="Apply Session/Sem Filter"><i class="fa-solid fa-check"></i></button>
                            </form>

                            <div class="vr d-none d-md-block text-secondary opacity-25"></div>

                            <!-- Month View Filter Form -->
                            <form method="POST" action="student_calendar.php" class="d-flex align-items-center gap-2">
                                <label for="selected_month" class="small text-muted text-nowrap fw-medium">Month:</label>
                                <input type="month" id="selected_month" name="selected_month" class="form-control form-control-sm border-secondary-subtle" 
                                       value="<?php echo $target_month_str; ?>">
                                <button type="submit" name="filter_month" class="btn btn-sm btn-primary px-2 shadow-sm">Load</button>
                            </form>

                            <div class="vr d-none d-md-block text-secondary opacity-25"></div>

                            <!-- Date Range Filter Form -->
                            <form method="POST" action="student_calendar.php" class="d-flex align-items-center gap-2">
                                <label class="small text-muted text-nowrap fw-medium">Range:</label>
                                <input type="date" name="start_date" class="form-control form-control-sm border-secondary-subtle" value="<?php echo $start_date_default; ?>" required>
                                <span class="small text-muted">to</span>
                                <input type="date" name="end_date" class="form-control form-control-sm border-secondary-subtle" value="<?php echo $end_date_default; ?>" required>
                                <button type="submit" name="search_range" class="btn btn-sm btn-success px-2 shadow-sm">Search</button>
                                <?php if ($is_range_search): ?>
                                    <a href="student_calendar.php" class="btn btn-sm btn-outline-secondary" title="Clear Filter"><i class="fa-solid fa-xmark"></i></a>
                                <?php endif; ?>
                            </form>
                        </div>
                    </div>

                    <?php if (!$is_range_search): ?>
                        <div class="calendar-grid">
                            <div class="calendar-weekday">Sun</div>
                            <div class="calendar-weekday">Mon</div>
                            <div class="calendar-weekday">Tue</div>
                            <div class="calendar-weekday">Wed</div>
                            <div class="calendar-weekday">Thu</div>
                            <div class="calendar-weekday">Fri</div>
                            <div class="calendar-weekday">Sat</div>

                            <?php
                            for ($i = 0; $i < $first_day_of_week; $i++) {
                                echo "<div class='calendar-day empty'></div>";
                            }

                            for ($day = 1; $day <= $days_in_month; $day++) {
                                $status_class = '';
                                $badge_markup = '';
                                
                                if (isset($attendance_data[$day])) {
                                    if ($attendance_data[$day] === 'present') {
                                        $status_class = 'day-present';
                                        $badge_markup = '<span class="badge bg-success small-xs d-none d-sm-inline-block shadow-sm">P</span>';
                                    } else {
                                        $status_class = 'day-absent';
                                        $badge_markup = '<span class="badge bg-danger small-xs d-none d-sm-inline-block shadow-sm">A</span>';
                                    }
                                }

                                echo "<div class='calendar-day " . $status_class . "'>";
                                echo "<span class='fw-bold fs-6'>" . $day . "</span>";
                                echo "<div class='text-end'>" . $badge_markup . "</div>";
                                echo "</div>";
                            }
                            ?>
                        </div>
                    <?php else: ?>
                        <?php if (!empty($attendance_data)): ?>
                            <div class="d-flex justify-content-end mb-3">
                                <form method="POST" action="student_calendar.php">
                                    <button type="submit" name="export_excel" class="btn btn-success btn-sm shadow-sm px-3">
                                        <i class="fa-solid fa-file-excel me-1"></i> Export to Excel
                                    </button>
                                </form>
                            </div>
                        <?php endif; ?>

                        <div class="table-responsive">
                            <table class="table table-bordered table-striped align-middle bg-white rounded shadow-sm">
                                <thead class="table-dark">
                                    <tr>
                                        <th>Date Log</th>
                                        <th class="text-center">Attendance Track Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($attendance_data)): ?>
                                        <tr>
                                            <td colspan="2" class="text-center text-muted py-3">No logged attendance sessions discovered across this target segment.</td>
                                        </tr>
                                    <?php else: ?>
                                        <?php foreach ($attendance_data as $date_key => $status_val): 
                                            $display_date = date('d-M-Y', strtotime($date_key));
                                            $is_present_check = ($status_val === 'present');
                                        ?>
                                            <tr>
                                                <td class="fw-semibold text-dark"><?php echo $display_date; ?></td>
                                                <td class="text-center">
                                                    <span class="badge <?php echo $is_present_check ? 'bg-success text-white' : 'bg-danger text-white'; ?> px-3 py-2 shadow-sm">
                                                        <i class="fa-solid <?php echo $is_present_check ? 'fa-circle-check' : 'fa-circle-xmark'; ?> me-1"></i>
                                                        <?php echo ucfirst($status_val); ?>
                                                    </span>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>

                    <div class="d-flex justify-content-center gap-4 mt-4 pt-3 border-top small text-secondary fw-medium">
                        <div><span class="status-dot bg-success-subtle border border-success"></span> Present Status</div>
                        <div><span class="status-dot bg-danger-subtle border border-danger"></span> Absent Status</div>
                        <div><span class="status-dot bg-light border"></span> No Lecture Logged</div>
                    </div>

                </div>
            </div>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI"
        crossorigin="anonymous"></script>
</body>

</html>