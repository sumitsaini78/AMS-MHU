<?php
include "../db_connect.php";

session_start();
if ($_SESSION['teacher_name'] == null) {
    header("Location: ../index.php");
}

// On view attendance submission
if (isset($_POST['post_faculty'])) {
    $faculty_name = $_POST['faculty'];
    // Fixed: Added quotes and addslashes to prevent JavaScript syntax errors with strings
    echo "<script>alert('" . addslashes($faculty_name) . "');</script>";
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
    </header>

    <main>
        <div class="container w-50 border border-warning mt-4">
            <h4 class="text-center bg-info ">Select A Subject to Get Students</h4>
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">Subject Name</th>
                        <th scope="col">Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $index = 1; // Start numbering table rows at 1
                    
                    // Loop through each distinct subject
                    while ($val = mysqli_fetch_assoc($result)) {

                        // 1. Fetch all attendance records for this specific subject using a unique variable name
                        $subject_escaped = mysqli_real_escape_string($conn, $val['subject_name']);
                        $status_query = "SELECT attendance_status FROM `attendance` WHERE subject_name = '$subject_escaped'";
                        $status_result = mysqli_query($conn, $status_query);

                        $total_records = 0;
                        $present_count = 0;

                        // 2. Fetch the individual status rows from the secondary query
                        while ($status_row = mysqli_fetch_assoc($status_result)) {
                            $attendance_status = $status_row['attendance_status'];

                            // Count total records and check if the student was present
                            $total_records++;
                            if (strtolower($attendance_status) === 'present' || $attendance_status == '1') {
                                $present_count++;
                            }
                        }

                        // 3. Calculate the attendance percentage safely to avoid division by zero
                        $percentage = ($total_records > 0) ? round(($present_count / $total_records) * 100, 2) : 0;

                        // 4. Output the completed table row HTML
                        echo "<tr>";
                        echo "<td>" . $index . "</td>";
                        echo "<td><form method='post' action='view_student_attendance.php'><input type='submit' name='subject_name' value='" . htmlspecialchars($val['subject_name'], ENT_QUOTES, 'UTF-8') . "'></form></td>";
                        echo "<td>" . $percentage . "%</td>";
                        echo "</tr>";

                        $index++; // Increment the counter for the next subject row
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI"
        crossorigin="anonymous"></script>
</body>

</html>