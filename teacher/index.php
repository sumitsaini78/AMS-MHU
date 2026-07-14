<?php
include "../db_connect.php";
session_start();

// 1. Secure the page: Check if the teacher is actually logged in
if (!isset($_SESSION['teacher_id'])) {
    // Redirect them to your login page if the session is missing
    header("Location: ./index.php");
    exit;
}

// 2. Safely assign the variable now that we know it exists
$id = $_SESSION['teacher_id'];
$query = "SELECT * FROM teachers WHERE id = '$id'";
$result = mysqli_query($conn, $query);

if ($result && mysqli_num_rows($result) == 1) {
    $teacher = mysqli_fetch_assoc($result);
    $teacher_name = $teacher['name'];
    $_SESSION['teacher_name'] = $teacher_name;

}
// putting teacher details in session for all file use
?>

<!doctype html>
<html lang="en" data-bs-theme="light">

<head>
    <title>Teacher Dashboard</title>
    <!-- Required meta tags -->
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />

    <!-- Bootstrap CSS v5.3.8 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous" />
</head>

<body>
    <header>
        <!-- place navbar here -->
        <nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm">
            <div class="container-fluid">
                <a class="navbar-brand text-warning fw-bold" href="index.php"><i
                        class="fa-solid fa-graduation-cap me-2"></i>MOTHERHOOD UNIVERSITY</a>
                <!-- Example single danger button -->

                <ul class="navbar-nav">
                    <li class="nav-item mx-2">
                        <div class="dropdown">
                            <button class="btn  dropdown-toggle" type="button" data-bs-toggle="dropdown"
                                aria-expanded="false">
                                <a href="teacher_subjects.php" class="btn btn-primary">Mark Attendence</a>
                            </button>
                            <ul class="dropdown-menu">
                                <?php
                                $query = "SELECT subject_name FROM `subjected_teacher` WHERE teacher_id = '$id'";
                                $result = mysqli_query($conn, $query);
                                while ($row = mysqli_fetch_assoc($result)) {
                                    $val = $row['subject_name'];
                                    echo "<form action='select_attendence_details.php' method='POST'>";
                                    echo "<input type='hidden' name='subject_name' value='" . htmlspecialchars($val) . "'>";
                                    echo "<button type='submit' class='btn  w-100 '>" . htmlspecialchars($val) . "</button><hr>";
                                    echo "</form>";
                                }
                                ?>
                            </ul>
                        </div>
                    </li>

                    <li class="nav-item mx-2">
                        <div class="dropdown">
                            <button class="btn  dropdown-toggle" type="button" data-bs-toggle="dropdown"
                                aria-expanded="false">
                                <a href="teacher_subjects.php" class="btn btn-primary">View-Attendence</a>
                            </button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="subject_attendence.php">Subject-Attendence</a></li>
                                <li><a class="dropdown-item" href="student_attendence.php">Students-Attendence</a></li>
                            </ul>
                        </div>
                    </li>
                    <li class="nav-item mx-2">
                        <a href="request_correction.php" class="btn btn-primary">Request For Correction</a>
                    </li>
                    <li class="nav-item mx-2">
                        <a href="assign_student_subject.php" class="btn btn-primary">Assign Student Subject</a>
                    </li>

                </ul>
                <span class="btn btn-secondary px-3 py-1 rounded-pill small disabled"
                    style="cursor: default; opacity: 1;">
                    <!-- get faculty name  -->
                    <?php
                    $query = "select faculty from teachers where id='$id' and name='$teacher_name'";
                    $result = mysqli_query($conn, $query);
                    $row = mysqli_fetch_assoc($result);
                    $faculty = $row['faculty'];
                    ?>
                    <i class="fa-solid fa-user-tie me-2 text-warning"></i> Welcome, <?php echo $teacher_name; ?>
                    <span class="badge bg-light text-dark ms-2 rounded-pill"><?php echo $faculty; ?></span>
                </span>

                <a href="../logout.php" class="btn btn-outline-light ms-3">Logout</a>
            </div>
        </nav>
    </header>
    <main>

    </main>
    <footer>
        <!-- place footer here -->
    </footer>
    <!-- Bootstrap JavaScript Bundle (includes Popper) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI"
        crossorigin="anonymous"></script>
</body>

</html>