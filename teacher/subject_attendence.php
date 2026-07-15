<?php
include "../db_connect.php";

session_start();
if ($_SESSION['teacher_name'] == null) {
    header("Location: ../index.php");
}

// On view attendance submission
if (isset($_POST['post_faculty'])) {
    $faculty_name = $_POST['faculty'];
    echo "<script>alert('" . addslashes($faculty_name) . "');</script>";
}

// Track if a date search filter is active
$is_set = 0;
$start_search_int = null;
$end_search_int = null;

if (isset($_POST['search_in_dates'])) {
    $html_start = $_POST['trip-start']; // Format: YYYY-MM-DD
    $html_end = $_POST['trip-end'];     // Format: YYYY-MM-DD

    if (!empty($html_start) && !empty($html_end)) {
        $is_set = 1;
        // Save raw HTML date strings into the session for form retention
        $_SESSION['start_date'] = $html_start;
        $_SESSION['end_date'] = $html_end;

        // FIXED CONVERSION: Convert YYYY-MM-DD to your exact DB integer format: DDMMYY
        $start_search_int = (int) date('dmy', strtotime($html_start));
        $end_search_int = (int) date('dmy', strtotime($html_end));
    } else {
        echo "<script>alert('Please select both start and end dates.');</script>";
        unset($_SESSION['start_date']);
        unset($_SESSION['end_date']);
    }
} elseif (isset($_SESSION['start_date']) && isset($_SESSION['end_date'])) {
    $is_set = 1;
    $start_search_int = (int) date('dmy', strtotime($_SESSION['start_date']));
    $end_search_int = (int) date('dmy', strtotime($_SESSION['end_date']));
}

// Getting distinct subject data using GROUP BY to prevent duplicate subject rows
$query = "SELECT subject_name FROM `attendance` GROUP BY subject_name";
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
    <style>
        #mhu-text {
            color: #a2c250;
            text-shadow: 1px 2px 14px rgb(46 195 41);
        }
    </style>
</head>

<body>
    <header>
        <nav class="navbar navbar-dark bg-dark shadow">
            <div class="container-fluid">
                <span class="navbar-brand mb-0 h1 fs-3 fw-bold" id="mhu-text"> MHU-AMS
                    <sub>Subject-Attendance</sub></span>
                <p></p>
                <div class="right"><a href=""></a></div>
                <span class="navbar-text text-white bg-secondary px-3 py-1 rounded-pill small">
                    <i class="fa-solid fa-user-tie me-1"></i> Welcome,
                    <?php echo isset($_SESSION['teacher_name']) ? htmlspecialchars($_SESSION['teacher_name']) : 'Teacher'; ?>
                </span>
                <a href="../logout.php" class="btn btn-outline-light ms-3">Logout</a>
            </div>
        </nav>
        <nav class="navbar navbar-dark bg-white shadow">
            <div class="container-fluid d-flex align-items-center justify-content-between">
                <div class="left"><a href=""></a></div>

                <div class="center d-flex align-items-center gap-3">
                    <span class="small"></span>
                    <form class="d-flex" method="POST" action="subject_attendence.php">
                        <label for="start-date" class="m-2">Date From:</label>
                        <input type="date" id="start-date" name="trip-start"
                            value="<?php echo isset($_SESSION['start_date']) ? htmlspecialchars($_SESSION['start_date']) : ''; ?>">
                        <label for="end-date" class="m-2"> To:</label>
                        <input type="date" id="end-date" name="trip-end"
                            value="<?php echo isset($_SESSION['end_date']) ? htmlspecialchars($_SESSION['end_date']) : ''; ?>">
                        <button class="btn btn-outline-success mx-2" type="submit"
                            name="search_in_dates">Search</button>
                    </form>
                </div>

                <div class="right"><a href=""></a></div>
            </div>
        </nav>
    </header>

    <main>
        <div class="container w-50 border border-warning mt-4 "></div>
        <!-- FIXED: Removed CSS display mixups. Table stays visible when filtering data -->
        <table class="table table-striped table-hover d-table w-50 m-auto mt-4">
            <thead>
                <tr>
                    <th scope="col">#</th>
                    <th scope="col">Subject Name</th>
                    <th scope="col">Total Lectures</th>
                    <th scope="col">Students Present</th>
                    <th scope="col">Status</th>
                </tr>
            </thead>
            <tbody>
            <?php
$index = 1; // Start numbering table rows at 1

// Loop through each distinct subject
while ($val = mysqli_fetch_assoc($result)) {

    $subject_escaped = mysqli_real_escape_string($conn, $val['subject_name']);

    // Set conditions ONLY if date search filters are active
    if ($is_set && $start_search_int !== null && $end_search_int !== null) {
        $start_date = $start_search_int;
        $end_date = $end_search_int;

        $start_str = str_pad($start_date, 6, "0", STR_PAD_LEFT);
        $end_str = str_pad($end_date, 6, "0", STR_PAD_LEFT);

        // Secure dynamic date conditions
        $date_condition = "AND STR_TO_DATE(LPAD(date_of_attendence, 6, '0'), '%d%m%y') BETWEEN STR_TO_DATE('$start_str', '%d%m%y') AND STR_TO_DATE('$end_str', '%d%m%y')";

        // Format search labels properly (DD-MM-YYYY) for output beside subject name
        $d_start = substr($start_str, 0, 2) . "-" . substr($start_str, 2, 2) . "-20" . substr($start_str, 4, 2);
        $d_end = substr($end_str, 0, 2) . "-" . substr($end_str, 2, 2) . "-20" . substr($end_str, 4, 2);
        
        $date_display = " <small class='text-muted ms-2'>(" . $d_start . " to " . $d_end . ")</small>";
    } else {
        // Default Fallback state (No search submitted): Calculate metrics using all records in database
        $date_condition = "";
        $date_display = "";
    }

    // Query to fetch DISTINCT lecture dates to get accurate lecture count
    $lecture_count_query = "SELECT COUNT(DISTINCT `date_of_attendence`) as total_lectures FROM `attendance` WHERE subject_name = '$subject_escaped' $date_condition";
    $lecture_count_result = mysqli_query($conn, $lecture_count_query);
    $lecture_count_row = mysqli_fetch_assoc($lecture_count_result);
    $total_lectures = isset($lecture_count_row['total_lectures']) ? $lecture_count_row['total_lectures'] : 0;

    // Query individual record markers strictly for overall raw metrics calculations
    $status_query = "SELECT attendance_status FROM `attendance` WHERE subject_name = '$subject_escaped' $date_condition";
    $status_result = mysqli_query($conn, $status_query);

    $total_student_records = 0;
    $present_count = 0;

    while ($status_row = mysqli_fetch_assoc($status_result)) {
        $attendance_status = $status_row['attendance_status'];
        $total_student_records++;
        if (strtolower($attendance_status) === 'present' || $attendance_status == '1') {
            $present_count++;
        } 
    }

    // Calculate the aggregate metrics cleanly using the student count dataset
    $percentage = ($total_student_records > 0) ? round(($present_count / $total_student_records) * 100, 2) : 0;

    echo "<tr>";
    echo "<td>" . $index . "</td>";
    echo "<td>" . htmlspecialchars($val['subject_name'])  . "</td>";
    echo "<td><span class='badge bg-secondary'>" . $total_lectures . "</span></td>";
    echo "<td><span class='badge bg-success'>" . $present_count . "</span></td>"; // Displays total student check-ins marked "present"
    echo "<td>" . $percentage . "%</td>";
    echo "</tr>";

    $index++;
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