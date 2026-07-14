<?php
include "../db_connect.php";

session_start();
if ($_SESSION['teacher_name'] == null) {
    header("Location: ../index.php");
}
if (!isset($_POST['subject_name'])) {
    header("Location: student_attendence.php");
    exit();
} else {
    $_SESSION['subject_name'] = $_POST['subject_name'];
}

// Getting distinct subject data using GROUP BY to prevent duplicate subject rows
$query = "SELECT * FROM `attendance` GROUP BY subject_name";
$result = mysqli_query($conn, $query);
?>
<!doctype html>
<html lang="en" data-bs-theme="light">

<head>
    <title>View-Student-Attendance</title>
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
        <h4 class="text-center mt-4">Students of <mark>
                <?php echo isset($_SESSION['subject_name']) ? htmlspecialchars($_SESSION['subject_name']) : 'Subject'; ?>
            </mark></mark></h4>
        <div class="container w-50 border border-warning mt-4">
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">Student Name</th>
                        <th scope="col">Roll No.</th>
                        <th scope="col">Status</th>
                    </tr>
                </thead>
                <tbody>
    <?php
// 1. Query to calculate total days and present days per student for the selected subject
$query = "SELECT 
            s.name, 
            s.roll_number,
            COUNT(a.attendance_status) AS total_days,
            SUM(CASE WHEN a.attendance_status = 'Present' THEN 1 ELSE 0 END) AS present_days
          FROM `students` s
          LEFT JOIN `attendance` a 
            ON s.roll_number = a.roll_number 
            AND a.subject_name = ?
          GROUP BY s.roll_number, s.name";

$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "s", $_SESSION['subject_name']);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

// 2. Loop through results and calculate percentage
$index = 1;
while ($row = mysqli_fetch_assoc($result)) {
    $total = (int)$row['total_days'];
    $present = (int)$row['present_days'];
    
    // Avoid division by zero if no attendance has been marked yet
    if ($total > 0) {
        $percentage = round(($present / $total) * 100, 2) . "%";
    } else {
        $percentage = "0% (No classes)";
    }

    echo "<tr>";
    echo "<td>" . $index . "</td>";
    echo "<td>" . htmlspecialchars($row['name']) . "</td>";
    echo "<td>" . htmlspecialchars($row['roll_number']) . "</td>";
    echo "<td>" . $percentage . "</td>"; // Outputs the calculated percentage
    echo "</tr>";
    
    $index++;
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