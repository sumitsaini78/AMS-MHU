<?php
include "../db_connect.php";

session_start();
if (!isset($_SESSION['teacher_name']) || $_SESSION['teacher_name'] == null) {
    header("Location: ../index.php");
    exit();
}

// On view attendance submission
if (isset($_POST['post_faculty'])) {
    $faculty_name = $_POST['faculty'];
    echo "<script>alert('" . addslashes($faculty_name) . "');</script>";
}

// ==========================================
// PRG PATTERN: PROCESS POST AND REDIRECT
// ==========================================
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 0. Handle Session Filter Submission
    if (isset($_POST['session_filter'])) {
        $html_session = $_POST['session_filter'] ?? '';
        if (!empty($html_session)) {
            $_SESSION['selected_session'] = $html_session;
        } else {
            unset($_SESSION['selected_session']);
        }
    }

    // 1. Handle Single Date Search Submission
    if (isset($_POST['search_single_date'])) {
        $html_single = $_POST['single-date'] ?? '';
        if (!empty($html_single)) {
            $_SESSION['search_type'] = 'single';
            $_SESSION['single_date'] = $html_single;

            // Clear conflicting date ranges
            unset($_SESSION['start_date']);
            unset($_SESSION['end_date']);
        } else {
            $_SESSION['flash_error'] = 'Please select a date.';
            unset($_SESSION['single_date']);
            unset($_SESSION['search_type']);
        }
    }
    // 2. Handle Date Range Search Submission
    elseif (isset($_POST['search_in_dates'])) {
        $html_start = $_POST['trip-start'] ?? '';
        $html_end = $_POST['trip-end'] ?? '';

        if (!empty($html_start) && !empty($html_end)) {
            $_SESSION['search_type'] = 'range';
            $_SESSION['start_date'] = $html_start;
            $_SESSION['end_date'] = $html_end;

            // Clear conflicting single date
            unset($_SESSION['single_date']);
        } else {
            $_SESSION['flash_error'] = 'Please select both start and end dates.';
            unset($_SESSION['start_date']);
            unset($_SESSION['end_date']);
            unset($_SESSION['search_type']);
        }
    }

    // Redirect back to itself to eliminate the POST state
    header("Location: subject_attendence.php");
    exit();
}

// ==========================================
// READ FILTERS FROM SESSION (GET STATE)
// ==========================================
$is_set = 0;
$start_search_int = null;
$end_search_int = null;
$single_search_int = null;
$search_type = $_SESSION['search_type'] ?? '';
$selected_session = $_SESSION['selected_session'] ?? '';

if ($search_type === 'single' && isset($_SESSION['single_date'])) {
    $is_set = 1;
    $single_search_int = (int) date('dmy', strtotime($_SESSION['single_date']));
} elseif ($search_type === 'range' && isset($_SESSION['start_date']) && isset($_SESSION['end_date'])) {
    $is_set = 1;
    $start_search_int = (int) date('dmy', strtotime($_SESSION['start_date']));
    $end_search_int = (int) date('dmy', strtotime($_SESSION['end_date']));
}

// Fetch distinct sessions for dropdown
$sessions_result = mysqli_query($conn, "SELECT DISTINCT session FROM attendance WHERE session != '' ORDER BY session DESC");

// Session condition for queries
$session_condition = "";
if (!empty($selected_session)) {
    $session_esc = mysqli_real_escape_string($conn, $selected_session);
    $session_condition = "AND session = '$session_esc'";
}

// Getting distinct subject data using GROUP BY and Session Filter
if (!empty($selected_session)) {
    $query = "SELECT subject_name FROM `attendance` WHERE session = '$session_esc' GROUP BY subject_name";
} else {
    $query = "SELECT subject_name FROM `attendance` GROUP BY subject_name";
}
$result = mysqli_query($conn, $query);
?>
<!doctype html>
<html lang="en" data-bs-theme="light">

<head>
    <title>Subject Attendance</title>
    <!-- Required meta tags -->
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />

    <!-- Bootstrap CSS v5.3.8 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous" />

    <!-- FontAwesome for cleaner UI Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" />

    <style>
        #mhu-text {
            color: #a2c250;
            text-shadow: 1px 2px 14px rgb(46 195 41);
        }

        .filter-card-session {
            background-color: #fff9db;
            border-left: 4px solid #f59e0b;
        }

        .filter-card-single {
            background-color: #f0f4f8;
            border-left: 4px solid #3b82f6;
        }

        .filter-card-range {
            background-color: #f4fbf7;
            border-left: 4px solid #10b981;
        }

        .divider-vertical {
            width: 2px;
            background-color: #e2e8f0;
            align-self: stretch;
        }
    </style>
</head>

<body>
    <?php
    // Handle error alerts securely after redirect
    if (isset($_SESSION['flash_error'])) {
        echo "<script>alert('" . addslashes($_SESSION['flash_error']) . "');</script>";
        unset($_SESSION['flash_error']);
    }
    ?>
    <header>
        <nav class="navbar navbar-dark bg-dark shadow">
            <div class="container-fluid">
                <span class="navbar-brand mb-0 h1 fs-3 fw-bold" id="mhu-text"> MHU-AMS
                    <sub>Subject-Attendance</sub></span>
                <span class="navbar-text text-white bg-secondary px-3 py-1 rounded-pill small">
                    <i class="fa-solid fa-user-tie me-1"></i> Welcome,
                    <?php echo isset($_SESSION['teacher_name']) ? htmlspecialchars($_SESSION['teacher_name']) : 'Teacher'; ?>
                </span>
                <a href="../logout.php" class="btn btn-outline-light ms-3">Logout</a>
            </div>
        </nav>

        <!-- Optimized Toolbar Container -->
        <nav class="navbar navbar-light bg-white shadow-sm py-3">
            <div class="container-fluid justify-content-center">
                <div class="d-flex flex-wrap align-items-center justify-content-center gap-3 w-100"
                    style="max-width: 1200px;">

                    <!-- Session Filter Box -->
                    <div class="filter-card-session p-2 px-3 rounded shadow-sm d-flex align-items-center">
                        <form class="d-flex align-items-center" method="POST" action="subject_attendence.php">
                            <span class="text-warning me-2"><i class="fa-solid fa-calendar-days"></i></span>
                            <label for="session-filter" class="me-2 fw-semibold text-secondary small text-nowrap">Session:</label>
                            <select id="session-filter" name="session_filter"
                                class="form-select form-select-sm me-2 border-warning-subtle" onchange="this.form.submit()">
                                <option value="">All Sessions</option>
                                <?php while ($sess = mysqli_fetch_assoc($sessions_result)): ?>
                                    <option value="<?= htmlspecialchars($sess['session']) ?>" <?= ($selected_session == $sess['session']) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($sess['session']) ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </form>
                    </div>

                    <!-- Visual Separator Line -->
                    <div class="divider-vertical d-none d-md-block mx-1"></div>

                    <!-- Single Date Filter Box -->
                    <div class="filter-card-single p-2 px-3 rounded shadow-sm d-flex align-items-center">
                        <form class="d-flex align-items-center" method="POST" action="subject_attendence.php">
                            <span class="text-primary me-2"><i class="fa-solid fa-calendar-day"></i></span>
                            <label for="single-date" class="me-2 fw-semibold text-secondary small text-nowrap">Single
                                Date:</label>
                            <input type="date" id="single-date" name="single-date"
                                class="form-control form-control-sm me-2 border-primary-subtle"
                                value="<?php echo isset($_SESSION['single_date']) ? htmlspecialchars($_SESSION['single_date']) : ''; ?>">
                            <button class="btn btn-sm btn-primary text-nowrap px-3 shadow-sm" type="submit"
                                name="search_single_date">
                                <i class="fa-solid fa-filter me-1"></i> View Day
                            </button>
                        </form>
                    </div>

                    <!-- Visual Separator Line -->
                    <div class="divider-vertical d-none d-md-block mx-1"></div>

                    <!-- Date Range Filter Box -->
                    <div class="filter-card-range p-2 px-3 rounded shadow-sm d-flex align-items-center">
                        <form class="d-flex align-items-center" method="POST" action="subject_attendence.php">
                            <span class="text-success me-2"><i class="fa-solid fa-calendar-days"></i></span>
                            <label for="start-date"
                                class="me-2 fw-semibold text-secondary small text-nowrap">From:</label>
                            <input type="date" id="start-date" name="trip-start"
                                class="form-control form-control-sm border-success-subtle"
                                value="<?php echo isset($_SESSION['start_date']) ? htmlspecialchars($_SESSION['start_date']) : ''; ?>">

                            <label for="end-date" class="mx-2 fw-semibold text-secondary small text-nowrap">To:</label>
                            <input type="date" id="end-date" name="trip-end"
                                class="form-control form-control-sm border-success-subtle"
                                value="<?php echo isset($_SESSION['end_date']) ? htmlspecialchars($_SESSION['end_date']) : ''; ?>">

                            <button class="btn btn-sm btn-success text-nowrap px-3 ms-2 shadow-sm" type="submit"
                                name="search_in_dates">
                                <i class="fa-solid fa-magnifying-glass me-1"></i> Search Range
                            </button>
                        </form>
                    </div>

                </div>
            </div>
        </nav>
    </header>

    <main>
        <div class="container w-50 mt-4 text-center">
            <?php if (!empty($selected_session)): ?>
                <div class="alert alert-warning py-2 d-inline-block shadow-sm me-2">
                    <i class="fa-solid fa-calendar-days me-1"></i> Session: <strong><?php echo htmlspecialchars($selected_session); ?></strong>
                </div>
            <?php endif; ?>

            <?php if ($is_set && $search_type === 'single'): ?>
                <div class="alert alert-primary py-2 d-inline-block shadow-sm">
                    <i class="fa-solid fa-circle-info me-1"></i> Showing attendance report for date:
                    <strong><?php echo date('d-m-Y', strtotime($_SESSION['single_date'])); ?></strong>
                </div>
            <?php elseif ($is_set && $search_type === 'range'): ?>
                <div class="alert alert-success py-2 d-inline-block shadow-sm">
                    <i class="fa-solid fa-circle-info me-1"></i> Showing range:
                    <strong><?php echo date('d-m-Y', strtotime($_SESSION['start_date'])); ?></strong> to
                    <strong><?php echo date('d-m-Y', strtotime($_SESSION['end_date'])); ?></strong>
                </div>
            <?php endif; ?>
        </div>

        <table class="table table-striped table-hover d-table w-50 m-auto mt-2 shadow-sm rounded border">
            <thead class="table-dark">
                <tr>
                    <th scope="col">#</th>
                    <th scope="col">Subject Name</th>
                    <th scope="col">Total Lectures</th>
                    <th scope="col">Total Students</th>
                    <th scope="col">Students Present</th>
                    <th scope="col">Status</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $index = 1;

                if (mysqli_num_rows($result) > 0) {
                    while ($val = mysqli_fetch_assoc($result)) {
                        $subject_escaped = mysqli_real_escape_string($conn, $val['subject_name']);
                        $date_condition = "";

                        if ($is_set && $search_type === 'single' && $single_search_int !== null) {
                            $single_str = str_pad($single_search_int, 6, "0", STR_PAD_LEFT);
                            $date_condition = "AND STR_TO_DATE(LPAD(date_of_attendence, 6, '0'), '%d%m%y') = STR_TO_DATE('$single_str', '%d%m%y')";
                        } elseif ($is_set && $search_type === 'range' && $start_search_int !== null && $end_search_int !== null) {
                            $start_str = str_pad($start_search_int, 6, "0", STR_PAD_LEFT);
                            $end_str = str_pad($end_search_int, 6, "0", STR_PAD_LEFT);
                            $date_condition = "AND STR_TO_DATE(LPAD(date_of_attendence, 6, '0'), '%d%m%y') BETWEEN STR_TO_DATE('$start_str', '%d%m%y') AND STR_TO_DATE('$end_str', '%d%m%y')";
                        }

                        // 1. Get Total Lectures
                        $lecture_count_query = "SELECT COUNT(DISTINCT `date_of_attendence`) as total_lectures FROM `attendance` WHERE subject_name = '$subject_escaped' $session_condition $date_condition";
                        $lecture_count_result = mysqli_query($conn, $lecture_count_query);
                        $total_lectures = mysqli_fetch_assoc($lecture_count_result)['total_lectures'] ?? 0;

                        // 2. Get Total Unique Students enrolled in this subject
                        $student_count_query = "SELECT COUNT(DISTINCT `roll_number`) as total_students FROM `attendance` WHERE subject_name = '$subject_escaped' $session_condition";
                        $student_count_result = mysqli_query($conn, $student_count_query);
                        $total_students = mysqli_fetch_assoc($student_count_result)['total_students'] ?? 0;

                        // 3. Get Attendance Stats
                        $status_query = "SELECT attendance_status FROM `attendance` WHERE subject_name = '$subject_escaped' $session_condition $date_condition";
                        $status_result = mysqli_query($conn, $status_query);
                        $present_count = 0;
                        $total_records = 0;
                        while ($status_row = mysqli_fetch_assoc($status_result)) {
                            $total_records++;
                            if (strtolower($status_row['attendance_status']) === 'present' || $status_row['attendance_status'] == '1') {
                                $present_count++;
                            }
                        }
                        $percentage = ($total_records > 0) ? round(($present_count / $total_records) * 100, 2) : 0;

                        echo "<tr>";
                        echo "<td>" . $index++ . "</td>";
                        echo "<td>" . htmlspecialchars($val['subject_name']) . "</td>";
                        echo "<td><span class='badge bg-secondary'>" . $total_lectures . "</span></td>";
                        echo "<td><span class='badge bg-info text-dark'>" . $total_students . "</span></td>";
                        echo "<td><span class='badge bg-success'>" . $present_count . "</span></td>";
                        echo "<td>" . $percentage . "%</td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='6' class='text-center text-muted py-4'>No attendance records found for the selected filters.</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI"
        crossorigin="anonymous"></script>
</body>

</html>