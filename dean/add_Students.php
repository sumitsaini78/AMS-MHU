<?php
include "../db_connect.php";
session_start();

// 1. Secure the page: Check if the Dean is actually logged in
if (!isset($_SESSION['dean_id'])) {
    // Redirect them to your login page if the session is missing
    header("Location: ./index.php");
    exit;
}

// 2. Safely assign the variable now that we know it exists
$id = $_SESSION['dean_id'];
$query = "SELECT * FROM deans WHERE id = '$id'";
$result = mysqli_query($conn, $query);

if ($result && mysqli_num_rows($result) == 1) {
    $dean = mysqli_fetch_assoc($result);
    $dean_name = $dean['Dean_name'];
}
// insert student data into the database
if (isset($_POST['insert_student'])) {
    $student_name = $_POST['student_name'];
    $roll_number = $_POST['roll_number'];
    $faculty = $_POST['faculty'];
    $course = $_POST['course'];
    $year = $_POST['year'];
    $semester = $_POST['semester'];
    $subject_code = $_POST['subject_code'];
    $subject_name = $_POST['subject_name'];

  $query = "INSERT INTO students (name, roll_number, faculty, course, year, sem, subject_code, subject_name) VALUES ('$student_name', '$roll_number', '$faculty', '$course', '$year', '$semester', '$subject_code', '$subject_name')";

    if (mysqli_query($conn, $query)) {
        echo "<script>alert('Student added successfully!');</script>";
    } else {
        echo "<script>alert('Error: " . mysqli_error($conn) . "');</script>";
    }
}

?>

<!doctype html>
<html lang="en" data-bs-theme="light">

<head>
    <title>Dean-Panel</title>
    <!-- Required meta tags -->
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />

    <!-- Bootstrap CSS v5.3.8 -->
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css"
        rel="stylesheet"
        integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB"
        crossorigin="anonymous" />
</head>

<body>
    <header>
        <nav class="navbar navbar-dark bg-dark shadow">
            <div class="container-fluid">
                <span class="navbar-brand mb-0 h1 fs-3 fw-bold">Mhu-AMS <sub class="text-primary">Dean</sub></span>

                <div class="right"> <span class="navbar-text text-white bg-secondary px-3 py-1 rounded-pill small">
                        <i class="fa-solid fa-user-tie me-1"></i> Welcome, <mark><?php echo $dean_name; ?></mark>
                    </span>
                    <a href="../logout.php" class="btn btn-outline-light ms-3">Logout</a><a href=""></a>
                </div>
            </div>
        </nav>
    </header>

    <main>
        <div class="container">
            <div class="row">
 <form class="container mt-4 needs-validation" novalidate method="post">
  <h2 class="mb-4">Student Information Form</h2>
  
  <div class="row g-3">
    <!-- Student Name -->
    <div class="col-md-6">
      <label for="studentName" class="form-label">Name</label>
      <input type="text" class="form-control" name="student_name" id="student_name" placeholder="Enter full name" required>
    </div>

    <!-- Roll Number -->
    <div class="col-md-6">
      <label for="rollNumber" class="form-label">Roll Number</label>
      <input type="text" class="form-control" name="roll_number" id="roll_number" placeholder="Enter roll number" required>
    </div>

    <!-- Faculty -->
    <div class="col-md-6">
       <label for="Dept_name" class="form-label">Faculty Name</label>
                    <select class="form-select" name="faculty" id="faculty_name" required>
                        <option selected disabled value="">Open this select menu</option>
                        <?php
                        $query = "SELECT dep_name FROM `departments`";
                        $result = mysqli_query($conn, $query);
                        while ($row = mysqli_fetch_assoc($result)) {
                            $val = $row['dep_name'];
                            echo "<option value='" . htmlspecialchars($val) . "'>" . htmlspecialchars($val) . "</option>";
                        }
                        ?>
                    </select>
    </div>

    <!-- Course -->
    <div class="col-md-6">
      <label for="Dept_name" class="form-label">Course Name</label>
                    <select class="form-select" name="course" id="course_name" required>
                        <option selected disabled value="">Open this select menu</option>
                        <?php
                        $query = "SELECT course_name FROM `courses`";
                        $result = mysqli_query($conn, $query);
                        while ($row = mysqli_fetch_assoc($result)) {
                            $val = $row['course_name'];
                            echo "<option value='" . htmlspecialchars($val) . "'>" . htmlspecialchars($val) . "</option>";
                        }
                        ?>
                    </select>
    </div>

    <!-- Year -->
    <div class="col-md-3">
      <label for="year" class="form-label">Year</label>
      <select class="form-select" id="year" name="year" required>
        <option value="" selected disabled>Choose year...</option>
        <option value="1">1st Year</option>
        <option value="2">2nd Year</option>
        <option value="3">3rd Year</option>
        <option value="4">4th Year</option>
      </select>
    </div>

    <!-- Semester -->
    <div class="col-md-3">
      <label for="semester" class="form-label">Semester</label>
      <select class="form-select" id="semester" name="semester" required>
        <option value="" selected disabled>Choose sem...</option>
        <option value="1">1st Sem</option>
        <option value="2">2nd Sem</option>
        <option value="3">3rd Sem</option>
        <option value="4">4th Sem</option>
        <option value="5">5th Sem</option>
        <option value="6">6th Sem</option>
        <option value="7">7th Sem</option>
        <option value="8">8th Sem</option>
      </select>
    </div>

  
  </div>

  <!-- Submit Button -->
  <div class="col-12 mt-4">
    <input class="btn btn-primary" type="submit" name="insert_student">
  </div>
</form>

        </div>
    </main>

    <script
        src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI"
        crossorigin="anonymous"></script>
</body>

</html>