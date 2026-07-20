<?php
include "../db_connect.php";
session_start();

if (!isset($_SESSION['dean_id'])) {
    header("Location: ./index.php");
    exit;
}

$id = $_SESSION['dean_id'];
$query = "SELECT * FROM deans WHERE id = '$id'";
$result = mysqli_query($conn, $query);
$dean_name = ($result && mysqli_num_rows($result) == 1) ? mysqli_fetch_assoc($result)['Dean_name'] : 'Admin';

// 1. Handle Sample CSV Download
if (isset($_GET['download_sample'])) {
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="sample_students.csv"');
    $output = fopen("php://output", "w");
    fputcsv($output, ['Name', 'Enroll', 'Roll', 'Faculty', 'Course', 'Year', 'Sem']);
    fputcsv($output, ['John Doe', '12345678', 'A-101', 'Computer Science', 'B.Tech', '2026', '4']);
    fclose($output);
    exit;
}

// 2. Handle Manual Insert
if (isset($_POST['insert_student'])) {
    $name = mysqli_real_escape_string($conn, $_POST['student_name']);
    $roll = mysqli_real_escape_string($conn, $_POST['roll_number']);
    $enroll = (int)$_POST['enroll_number'];
    $faculty = mysqli_real_escape_string($conn, $_POST['faculty']);
    $course = mysqli_real_escape_string($conn, $_POST['course']);
    $year = (int)$_POST['year'];
    $sem = (int)$_POST['semester'];

    $sql = "INSERT INTO students (name, enrollment_number, roll_number, faculty, course, year, sem) 
            VALUES ('$name', $enroll, '$roll', '$faculty', '$course', $year, $sem)";

    if (mysqli_query($conn, $sql)) {
        echo "<script>alert('Student added successfully!');</script>";
    } else {
        echo "<script>alert('Error: " . mysqli_error($conn) . "');</script>";
    }
}

// 3. Handle CSV Import
if (isset($_POST['import_csv'])) {
    $filename = $_FILES["csv_file"]["tmp_name"];
    if ($_FILES["csv_file"]["size"] > 0) {
        $file = fopen($filename, "r");
        fgetcsv($file); // Skip header
        while (($row = fgetcsv($file, 10000, ",")) !== FALSE) {
            $name = mysqli_real_escape_string($conn, $row[0]);
            $enroll = mysqli_real_escape_string($conn, $row[1]);
            $roll = mysqli_real_escape_string($conn, $row[2]);
            $faculty = mysqli_real_escape_string($conn, $row[3]);
            $course = mysqli_real_escape_string($conn, $row[4]);
            $year = (int)$row[5];
            $sem = (int)$row[6];

            $sql = "INSERT INTO students (name, enrollment_number, roll_number, faculty, course, year, sem) 
                    VALUES ('$name', '$enroll', '$roll', '$faculty', '$course', $year, $sem)";
            mysqli_query($conn, $sql);
        }
        fclose($file);
        echo "<script>alert('CSV Imported Successfully!'); window.location.href='add_students.php';</script>";
    }
}
?>

<!doctype html>
<html lang="en">
<head>
    <title>Dean Panel | Student Management</title>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" />
    <style>
        body { background-color: #f8f9fa; }
        .card { border: none; border-radius: 12px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); }
        .card-header { background: #fff; border-bottom: 1px solid #eee; padding: 1.5rem; font-weight: 700; border-radius: 12px 12px 0 0 !important; }
    </style>
</head>
<body>
    <nav class="navbar navbar-dark bg-dark shadow-sm py-3">
        <div class="container">
            <span class="navbar-brand fw-bold">MHU-AMS <span class="text-primary fs-6">DEAN</span></span>
            <a href="../logout.php" class="btn btn-outline-light btn-sm"><i class="fa-solid fa-right-from-bracket me-1"></i> Logout</a>
        </div>
    </nav>

    <main class="container my-5">
        <div class="row g-4">
            <div class="col-lg-4">
                <div class="card h-100">
                    <div class="card-header"><i class="fa-solid fa-file-csv me-2"></i> Bulk Import</div>
                    <div class="card-body">
                        <a href="?download_sample=true" class="btn btn-outline-secondary w-100 mb-3">
                            <i class="fa-solid fa-download me-2"></i> Download Sample CSV
                        </a>
                        <form method="POST" enctype="multipart/form-data">
                            <input type="file" name="csv_file" class="form-control mb-3" accept=".csv" required>
                            <button type="submit" name="import_csv" class="btn btn-success w-100 fw-bold">Upload Data</button>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header"><i class="fa-solid fa-user-plus me-2"></i> Add New Student</div>
                    <div class="card-body p-4">
                        <form method="post" class="row g-3">
                            <div class="col-md-6"><label class="form-label">Full Name</label><input type="text" class="form-control" name="student_name" required></div>
                            <div class="col-md-6"><label class="form-label">Roll Number</label><input type="text" class="form-control" name="roll_number" required></div>
                            <div class="col-md-6"><label class="form-label">Enrollment Number</label><input type="text" class="form-control" name="enroll_number" required></div>
                            <div class="col-md-6"><label class="form-label">Faculty</label>
                                <select class="form-select" name="faculty" required>
                                    <option selected disabled value="">Choose...</option>
                                    <?php 
                                    $res = mysqli_query($conn, "SELECT dep_name FROM departments");
                                    while($r = mysqli_fetch_assoc($res)) echo "<option value='{$r['dep_name']}'>{$r['dep_name']}</option>";
                                    ?>
                                </select>
                            </div>
                            <div class="col-md-6"><label class="form-label">Course</label>
                                <select class="form-select" name="course" required>
                                    <option selected disabled value="">Choose...</option>
                                    <?php 
                                    $res = mysqli_query($conn, "SELECT course_name FROM subjects");
                                    while($r = mysqli_fetch_assoc($res)) echo "<option value='{$r['course_name']}'>{$r['course_name']}</option>";
                                    ?>
                                </select>
                            </div>
                            <div class="col-md-3"><label class="form-label">Year</label><input type="number" class="form-control" name="year" required></div>
                            <div class="col-md-3"><label class="form-label">Semester</label><input type="number" class="form-control" name="semester" required></div>
                            <div class="col-12 mt-4"><button type="submit" name="insert_student" class="btn btn-primary px-5 fw-bold">Save Student</button></div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </main>
</body>
</html>