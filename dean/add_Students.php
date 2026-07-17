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

// Processing logic remains the same...
if (isset($_POST['insert_student'])) {
    // 1. Sanitize input
    $name = mysqli_real_escape_string($conn, $_POST['student_name']);
    $roll = mysqli_real_escape_string($conn, $_POST['roll_number']);
    $enroll = (int)$_POST['enroll_number']; // Ensure it's an integer as per schema
    $faculty = mysqli_real_escape_string($conn, $_POST['faculty']);
    $course = mysqli_real_escape_string($conn, $_POST['course']);
    $year = (int)$_POST['year'];
    $sem = (int)$_POST['semester']; // Your DB uses 'sem'

    // 2. Perform Insert
    $sql = "INSERT INTO students (name, enrollment_number, roll_number, faculty, course, year, sem) 
            VALUES ('$name', $enroll, '$roll', '$faculty', '$course', $year, $sem)";

    if (mysqli_query($conn, $sql)) {
        echo "<script>alert('Student added successfully!');</script>";
    } else {
        // This will print the actual error if the insert fails
        echo "Database Error: " . mysqli_error($conn);
    }
}

if (isset($_POST['import_csv'])) {
    $filename = $_FILES["csv_file"]["tmp_name"];
    
    if ($_FILES["csv_file"]["size"] > 0) {
        $file = fopen($filename, "r");
        
        // Skip the header row if your CSV has one
        fgetcsv($file); 

        while (($row = fgetcsv($file, 10000, ",")) !== FALSE) {
            // Mapping based on your table: name, enroll, roll, faculty, course, year, sem
            $name = mysqli_real_escape_string($conn, $row[0]);
            $enroll = mysqli_real_escape_string($conn, $row[1]);
            $roll = mysqli_real_escape_string($conn, $row[2]);
            $faculty = mysqli_real_escape_string($conn, $row[3]);
            $course = mysqli_real_escape_string($conn, $row[4]);
            $year = (int)$row[5];
            $sem = (int)$row[6];

            // Use the correct column names from your SQL file
            $sql = "INSERT INTO students (name, enrollment_number, roll_number, faculty, course, year, sem) 
                    VALUES ('$name', '$enroll', '$roll', '$faculty', '$course', $year, $sem)";
            
            if (!mysqli_query($conn, $sql)) {
                // If it fails, print the error to see exactly which column is wrong
                echo "Error inserting row: " . mysqli_error($conn);
                exit;
            }
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
        .form-label { font-weight: 600; color: #495057; }
    </style>
</head>

<body>
    <nav class="navbar navbar-dark bg-dark shadow-sm py-3">
        <div class="container">
            <span class="navbar-brand fs-3 fw-bold">MHU-AMS <span class="text-primary fs-6">DEAN</span></span>
            <div class="d-flex align-items-center">
                <span class="text-white me-4"><i class="fa-solid fa-user-tie me-2"></i><?php echo htmlspecialchars($dean_name); ?></span>
                <a href="../logout.php" class="btn btn-outline-light btn-sm"><i class="fa-solid fa-right-from-bracket me-1"></i> Logout</a>
            </div>
        </div>
    </nav>

    <main class="container my-5">
        <div class="row g-4">
            <!-- Bulk Import Section -->
            <div class="col-lg-4">
                <div class="card h-100">
                    <div class="card-header"><i class="fa-solid fa-file-csv me-2"></i> Bulk Import</div>
                    <div class="card-body">
                        <form method="POST" enctype="multipart/form-data">
                            <p class="small text-muted">Upload a CSV with columns: Name, Enroll, Roll, Faculty, Course, Year, Sem.</p>
                            <input type="file" name="csv_file" class="form-control mb-3" accept=".csv" required>
                            <button type="submit" name="import_csv" class="btn btn-success w-100 fw-bold">
                                <i class="fa-solid fa-upload me-2"></i> Upload Data
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Manual Form Section -->
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header"><i class="fa-solid fa-user-plus me-2"></i> Add New Student</div>
                    <div class="card-body p-4">
                        <form method="post" class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Full Name</label>
                                <input type="text" class="form-control" name="student_name" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Roll Number</label>
                                <input type="text" class="form-control" name="roll_number" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Enrollment Number</label>
                                <input type="text" class="form-control" name="enroll_number" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Faculty</label>
                                <select class="form-select" name="faculty" required>
                                    <option selected disabled value="">Choose...</option>
                                    <?php 
                                    $res = mysqli_query($conn, "SELECT dep_name FROM departments");
                                    while($r = mysqli_fetch_assoc($res)) echo "<option value='{$r['dep_name']}'>{$r['dep_name']}</option>";
                                    ?>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Course</label>
                                <select class="form-select" name="course" required>
                                    <option selected disabled value="">Choose...</option>
                                    <?php 
                                    $res = mysqli_query($conn, "SELECT course_name FROM subjects");
                                    while($r = mysqli_fetch_assoc($res)) echo "<option value='{$r['course_name']}'>{$r['course_name']}</option>";
                                    ?>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Year</label>
                                <input type="number" class="form-control" name="year" placeholder="2026" required>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Semester</label>
                                <input type="number" class="form-control" name="semester" placeholder="4" required>
                            </div>
                            <div class="col-12 mt-4">
                                <button type="submit" name="insert_student" class="btn btn-primary px-5 fw-bold">
                                    <i class="fa-solid fa-check me-2"></i> Save Student
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>