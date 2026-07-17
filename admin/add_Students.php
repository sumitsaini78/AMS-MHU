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
    $enroll_number = $_POST['enroll_number'];
    $faculty = $_POST['faculty'];
    $course = $_POST['course'];
    $year = $_POST['year'];
    $semester = $_POST['semester'];
    $query = "INSERT INTO students (name, enrollment_number, roll_number, faculty, course, year, sem) VALUES ('$student_name','$enroll_number' ,'$roll_number', '$faculty', '$course', '$year', '$semester')";

    if (mysqli_query($conn, $query)) {
        echo "<script>alert('Student added successfully!');</script>";
    } else {
        echo "<script>alert('Error: " . mysqli_error($conn) . "');</script>";
    }
}

// NEW FEATURE: Bulk CSV Import Backend (Bina kisi external library ke)
if (isset($_POST['import_csv'])) {
    if (isset($_FILES['csv_file']['tmp_name']) && $_FILES['csv_file']['size'] > 0) {
        $fileTmpPath = $_FILES['csv_file']['tmp_name'];
        
        if (($handle = fopen($fileTmpPath, "r")) !== FALSE) {
            $success_count = 0;
            $error_count = 0;
            $is_header = true;

            // Line by line CSV read karein
            while (($row = fgetcsv($handle, 1000, ",")) !== FALSE) {
                // Pehli row (heading) ko skip karein
                if ($is_header) {
                    $is_header = false;
                    continue;
                }

                // CSV Columns Mapping (A=0:Name, B=1:Enroll, C=2:Roll, D=3:Faculty, E=4:Course, F=5:Year, G=6:Sem)
                $student_name  = isset($row[0]) ? mysqli_real_escape_string($conn, trim($row[0])) : '';
                $enroll_number = isset($row[1]) ? mysqli_real_escape_string($conn, trim($row[1])) : '';
                $roll_number   = isset($row[2]) ? mysqli_real_escape_string($conn, trim($row[2])) : '';
                $faculty       = isset($row[3]) ? mysqli_real_escape_string($conn, trim($row[3])) : '';
                $course        = isset($row[4]) ? mysqli_real_escape_string($conn, trim($row[4])) : '';
                $year          = isset($row) ? mysqli_real_escape_string($conn, trim($row[5])) : '';
                $semester      = isset($row[6]) ? mysqli_real_escape_string($conn, trim($row[6])) : '';

                // Khali rows ko skip karein
                if (empty($student_name) && empty($roll_number)) {
                    continue;
                }

                $import_query = "INSERT INTO students (name, enrollment_number, roll_number, faculty, course, year, sem) 
                                 VALUES ('$student_name', '$enroll_number', '$roll_number', '$faculty', '$course', '$year', '$semester')";

                if (mysqli_query($conn, $import_query)) {
                    $success_count++;
                } else {
                    $error_count++;
                }
            }
            fclose($handle);
            echo "<script>alert('Import Finished! Successfully added: $success_count students. Failed: $error_count'); window.location.href=window.location.href;</script>";
        } else {
            echo "<script>alert('Error opening file.');</script>";
        }
    } else {
        echo "<script>alert('Please upload a valid CSV file.');</script>";
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
                
                <!-- NEW FEATURE: Bulk CSV Import Card (Aapke Form Ke Thik Upar) -->
                <div class="col-12 mt-4">
                    <div class="card">
                        <div class="card-header bg-dark text-white fw-bold">
                            Bulk Student Import (.CSV Format Only)
                        </div>
                        <div class="card-body">
                            <form method="POST" enctype="multipart/form-data" class="row g-3 align-items-center">
                                <div class="col-md-9">
                                    <label class="form-label fw-semibold">Select CSV File (Columns: Name, Enroll No, Roll No, Faculty, Course, Year, Sem)</label>
                                    <input type="file" name="csv_file" class="form-control" accept=".csv" required>
                                </div>
                                <div class="col-md-3 mt-md-4 pt-md-2">
                                    <button type="submit" name="import_csv" class="btn btn-success w-100 fw-bold">Upload & Import</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

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
    <div class="col-md-6">
      <label for="rollNumber" class="form-label">Enroll Number</label>
      <input type="text" class="form-control" name="enroll_number" id="enroll_number" placeholder="Enter Enroll number" required>
    </div>

    <!-- Faculty -->
    <div class="col-md-6">
       <label for="Dept_name" class="form-label">Faculty Name</label>
                    <select class="form-select" name="faculty" id="faculty_name" required>
                        <option selected disabled value="">Open this select menu</option>
                        <?php
                        $query = "SELECT faculty_name FROM `faculty`";
                        $result = mysqli_query($conn, $query);
                        while ($row = mysqli_fetch_assoc($result)) {
                            $val = $row['faculty_name'];
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
                        $query = "SELECT course_name FROM `courses_list`";
                        $result = mysqli_query($conn, $query);
                        while ($row = mysqli_fetch_assoc($result)) {
                            $val = $row['course_name'];
                            echo "<option value='" . htmlspecialchars($val) . "'>" . htmlspecialchars($val) . "</option>";
                        }
                        ?>
                    </select>
    </div>

   <!-- Year -->
                        <div class="col-md-6">
                            <label for="year" class="form-label">Year</label>
                            <input type="text" class="form-control" name="year" id="year" placeholder="e.g. 2026" required>
                        </div>

                        <!-- Semester -->
                        <div class="col-md-6">
                            <label for="semester" class="form-label">Semester</label>
                            <input type="text" class="form-control" name="semester" id="semester" placeholder="e.g. 4" required>
                        </div>

                        <div class="col-12 mt-4">
                            <button type="submit" name="insert_student" class="btn btn-primary w-25">Add Student</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </main>

    <!-- Bootstrap JS (Optional, for validation features) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>