<?php
session_start();
include "../db_connect.php";

// On view attendance submission
if (isset($_POST['subject_name'])) {
    $subject_name = $_POST['subject_name'];
} else {
    header("Location: subject_attendence.php");
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
        <blockquote class="blockquote">
            <p class="text-center"><?php echo $subject_name; ?></p>
        </blockquote>
        <div class="container w-100 d-flex border">
            <div class="container w-50 border border-warning mt-4">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">Student Name</th>
                            <th scope="col">Date</th>
                            <th scope="col">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // Added ORDER BY to sort by date_of_attendence in descending order (latest first)
                        $query = "SELECT * FROM `attendance` WHERE subject_name = '$subject_name' ORDER BY date_of_attendence DESC";
                        $result = mysqli_query($conn, $query);
                        $index = 1; // Start numbering table rows at 1
                        
                        // Loop through each distinct subject
                        while ($val = mysqli_fetch_assoc($result)) {
                           
                            echo "<tr>";
                            echo "<th scope='row'>" . $index . "</th>";
                            echo "<td>" . htmlspecialchars($val['student_name']) . "</td>";
                            // date in format d-m-Y
                             // Parse the 8-digit string using the DMY format, then output it with slashes
                            $dateStr = $val['date_of_attendence'];
                            $dateObj = DateTime::createFromFormat('dmY', $dateStr);
                            $formattedDate = $dateObj ? $dateObj->format('d/m/y') : $dateStr;
                            echo "<td>" . htmlspecialchars($formattedDate) . "</td>";
                            // echo "<td>" . htmlspecialchars($val['date_of_attendence']) . "</td>";
                            echo "<td>" . htmlspecialchars($val['attendance_status']) . "</td>";
                            echo "</tr>";

                            $index++; // Increment the index for the next row
                        }
                        ?>

                    </tbody>
                </table>
            </div>
        </div>
    </main>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI"
        crossorigin="anonymous"></script>
</body>

</html>