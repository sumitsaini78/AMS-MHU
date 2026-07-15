<?php
include "../db_connect.php";

session_start();
if ($_SESSION['teacher_name'] == null) {
    header("Location: ../index.php");
    exit();
}

// 1. Process incoming context metrics via POST and shift them into Session storage (PRG Pattern)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['student_roll'])) {
        $_SESSION['student_roll'] = $_POST['student_roll'];
        $_SESSION['student_name'] = $_POST['student_name'];
        unset($_SESSION['calendar_month']); // Reset month filter on new student select
    }
    
    // Handle month filter switching
    if (isset($_POST['filter_month'])) {
        $_SESSION['calendar_month'] = $_POST['selected_month'];
    }
    
    header("Location: student_calendar.php");
    exit();
}

// 2. Fallback protection routing check
if (!isset($_SESSION['student_roll']) || !isset($_SESSION['subject_name'])) {
    header("Location: student_attendence.php");
    exit();
}

$student_roll = $_SESSION['student_roll'];
$student_name = $_SESSION['student_name'];
$subject_name = $_SESSION['subject_name'];

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
    
    // Check if record falls inside the currently viewed month window scope
    if ((int)$m === $month && (int)$y === $year) {
        $day_key = (int)$d;
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
$total_lectures = $present_count + $absent_count;
$attendance_rate = ($total_lectures > 0) ? round(($present_count / $total_lectures) * 100, 1) : 0;
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
        
        /* Modern CSS Grid Calendar Structural View */
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
        
        /* Attendance Dynamic Colors Indicators */
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
                    <span class="navbar-text text-white bg-secondary px-3 py-1 rounded-pill small me-3">
                        <i class="fa-solid fa-user-tie me-1"></i> Welcome, <?php echo htmlspecialchars($_SESSION['teacher_name']); ?>
                    </span>
                    <a href="view_student_attendance.php" class="btn btn-sm btn-outline-info me-2">
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
                            <span class="badge bg-secondary-subtle text-secondary mb-1">Student Focus Profile</span>
                            <h2 class="fw-bold text-dark mb-1"><i class="fa-solid fa-circle-user text-muted me-2"></i><?php echo htmlspecialchars($student_name); ?></h2>
                            <p class="text-muted mb-0 font-monospace small">Roll Number: <?php echo htmlspecialchars($student_roll); ?></p>
                            <div class="mt-2 text-primary fw-medium small">
                                <i class="fa-solid fa-book-open me-1"></i> Subject Track Target: <strong class="text-dark"><?php echo htmlspecialchars($subject_name); ?></strong>
                            </div>
                        </div>
                        <div class="col-md-5 ps-md-4 mt-3 mt-md-0">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <span class="text-secondary small">Month Attendance Metrics Rate:</span>
                                <span class="fw-bold fs-5 <?php echo $attendance_rate >= 75 ? 'text-success' : ($attendance_rate >= 50 ? 'text-warning' : 'text-danger'); ?>">
                                    <?php echo $attendance_rate; ?>%
                                </span>
                            </div>
                            <div class="d-flex gap-2">
                                <div class="bg-success-subtle p-2 rounded text-center flex-fill border border-success-subtle">
                                    <div class="text-success small fw-semibold">Present</div>
                                    <div class="fs-5 fw-bold text-success"><?php echo $present_count; ?></div>
                                </div>
                                <div class="bg-danger-subtle p-2 rounded text-center flex-fill border border-danger-subtle">
                                    <div class="text-danger small fw-semibold">Absent</div>
                                    <div class="fs-5 fw-bold text-danger"><?php echo $absent_count; ?></div>
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
                    
                    <!-- Month Context Filtering Ribbon Controls Line -->
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 border-bottom pb-3 mb-4">
                        <h4 class="fw-bold text-secondary mb-0">
                            <i class="fa-regular fa-calendar-check text-primary me-2"></i><?php echo date('F Y', $first_day_timestamp); ?>
                        </h4>
                        <form method="POST" action="student_calendar.php" class="d-flex align-items-center gap-2">
                            <label for="selected_month" class="small text-muted text-nowrap fw-medium">Switch Month View:</label>
                            <input type="month" id="selected_month" name="selected_month" class="form-control form-control-sm border-secondary-subtle" 
                                   value="<?php echo $target_month_str; ?>">
                            <button type="submit" name="filter_month" class="btn btn-sm btn-primary px-3 shadow-sm">Load</button>
                        </form>
                    </div>

                    <!-- Interactive Calendar View Grid Module -->
                    <div class="calendar-grid">
                        <!-- Weekdays Header Rows Initialization -->
                        <div class="calendar-weekday">Sun</div>
                        <div class="calendar-weekday">Mon</div>
                        <div class="calendar-weekday">Tue</div>
                        <div class="calendar-weekday">Wed</div>
                        <div class="calendar-weekday">Thu</div>
                        <div class="calendar-weekday">Fri</div>
                        <div class="calendar-weekday">Sat</div>

                        <?php
                        // 1. Render empty offset tracking boxes for week launch alignment
                        for ($i = 0; $i < $first_day_of_week; $i++) {
                            echo "<div class='calendar-day empty'></div>";
                        }

                        // 2. Render actual month numeric operational calendar date blocks
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

                    <!-- Operational Color Legends Explanations Strip Footer -->
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