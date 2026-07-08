<?php
include "../db_connect.php";
session_start();

// 1. Secure the page: Check if the teacher is actually logged in
if (!isset($_SESSION['teacher_id'])) {
    // Redirect them to your login page if the session is missing
    header("Location: ../index.php"); 
    exit;
}

// 2. Safely assign the variable now that we know it exists
$id = $_SESSION['teacher_id'];
$query = "SELECT * FROM teachers WHERE id = '$id'";
$result = mysqli_query($conn, $query);

if ($result && mysqli_num_rows($result) == 1) {
    $teacher = mysqli_fetch_assoc($result);
    $teacher_name = $teacher['name'];
}
// get POST data
$subject_name = $_POST['subject_name'];
$subject_code = $_POST['subject_code'];
$faculty_name = $_POST['faculty_name'];
$course_name = $_POST['course_name'];
$year = $_POST['year'];
$semester = $_POST['semester'];

?>
<!doctype html>
<html lang="en" data-bs-theme="light">

<head>
    <title>Insert Attendance</title>
    <!-- Required meta tags -->
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />

    <!-- Bootstrap CSS v5.3.8 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous" />

</head>

<body>
    <header>
        <nav class="navbar navbar-dark bg-dark shadow">
            <div class="container-fluid">
                <span class="navbar-brand mb-0 h1 fs-3 fw-bold">Mhu-AMS</span>
                <a href="index.php" class="text-white text-decoration-none">Home</a>
                <div class="right"><a href=""></a></div>
            </div>
        </nav>
    </header>
    
    <main class="container my-4">
        <!-- Centering Row Wrapper -->
        <div class="row justify-content-center">
            <!-- Table and Details Column (Width restricted to 8 out of 12 columns for cleaner look) -->
            <div class="col-md-8 col-lg-6">
                
                <!-- showing details for the selected subject -->
                <div class="bg-warning-subtle border p-3 rounded mb-3 text-center">
                    <strong><?php echo "$subject_name ($subject_code)"; ?></strong><br>
                    <small class="text-muted"><?php echo "$faculty_name | $course_name | Year $year | Sem $semester"; ?></small>
                </div>

                <!-- students list for marking attendance -->
                <?php
                $query = "SELECT * FROM students";
                $result = mysqli_query($conn, $query);
                
                echo "
                <table class='table table-striped table-bordered text-center align-middle'> 
                    <thead class='table-dark'>
                        <tr>
                            <th>Student Name</th>
                            <th>Roll Number</th>
                            <th>attendance_status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>1</td>
                            <td>John Doe</td>
                            <td>[ Attendance Options ]</td>
                        </tr>
                    </tbody>
                </table>";
                ?>
                
            </div>
        </div>
    </main>

    <!-- Bootstrap JavaScript Bundle (includes Popper) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI"
        crossorigin="anonymous"></script>

</body>

</html>
