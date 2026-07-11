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

// Insert Subject/Course data into the database
if (isset($_POST['insert_subject'])) {
    $dept_name    = mysqli_real_escape_string($conn, $_POST['dept_name']);
    $course_name  = mysqli_real_escape_string($conn, $_POST['course_name']);
    $subject_name = mysqli_real_escape_string($conn, $_POST['subject_name']);
    $year         = mysqli_real_escape_string($conn, $_POST['year']);
    $semester     = mysqli_real_escape_string($conn, $_POST['semester']);
    $subject_code = mysqli_real_escape_string($conn, $_POST['subject_code']);
    
    // NOTE: Apne table ka naam database ke hisab se 'subjects' ya jo bhi ho badal lein
    $query = "INSERT INTO courses (dept_name, course_name, subject_name, year, semester, subject_code) 
              VALUES ('$dept_name', '$course_name', '$subject_name', '$year', '$semester', '$subject_code')";

    if (mysqli_query($conn, $query)) {
        echo "<script>alert('Subject added successfully!');</script>";
    } else {
        echo "<script>alert('Error: " . mysqli_error($conn) . "');</script>";
    }
}

// Bulk CSV Import Backend
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

                // CSV Columns Mapping (0:Dept, 1:Course, 2:Subject, 3:Year, 4:Sem, 5:Code)
                $dept_name    = isset($row[0]) ? mysqli_real_escape_string($conn, trim($row[0])) : '';
                $course_name  = isset($row[1]) ? mysqli_real_escape_string($conn, trim($row[1])) : '';
                $subject_name = isset($row[2]) ? mysqli_real_escape_string($conn, trim($row[2])) : '';
                $year         = isset($row[3]) ? mysqli_real_escape_string($conn, trim($row[3])) : '';
                $semester     = isset($row[4]) ? mysqli_real_escape_string($conn, trim($row[4])) : '';
                $subject_code = isset($row[5]) ? mysqli_real_escape_string($conn, trim($row[5])) : '';

                // Khali rows ko skip karein
                if (empty($subject_name) && empty($subject_code)) {
                    continue;
                }

                $import_query = "INSERT INTO courses (dept_name, course_name, subject_name, year, semester, subject_code) 
                                 VALUES ('$dept_name', '$course_name', '$subject_name', '$year', '$semester', '$subject_code')";

                if (mysqli_query($conn, $import_query)) {
                    $success_count++;
                } else {
                    $error_count++;
                }
            }
            fclose($handle);
            echo "<script>alert('Import Finished! Successfully added: $success_count entries. Failed: $error_count'); window.location.href=window.location.href;</script>";
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
    <title>Dean-Panel | Subject Management</title>
    <!-- Required meta tags -->
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />

    <!-- Bootstrap CSS v5.3.8 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" />
</head>

<body>
    <header>
        <nav class="navbar navbar-dark bg-dark shadow">
            <div class="container-fluid">
                <span class="navbar-brand mb-0 h1 fs-3 fw-bold">Mhu-AMS <sub class="text-primary">Dean</sub></span>
                <div class="right"> 
                    <span class="navbar-text text-white bg-secondary px-3 py-1 rounded-pill small">
                        Welcome, <mark><?php echo htmlspecialchars($dean_name); ?></mark>
                    </span>
                    <a href="../logout.php" class="btn btn-outline-light ms-3">Logout</a>
                </div>
            </div>
        </nav>
    </header>

    <main>
        <div class="container">
            <div class="row">
                
                <!-- Bulk CSV Import Card -->
                <div class="col-12 mt-4">
                    <div class="card">
                        <div class="card-header bg-dark text-white fw-bold">
                            Bulk Subject Import (.CSV Format Only)
                        </div>
                        <div class="card-body">
                            <form method="POST" enctype="multipart/form-data" class="row g-3 align-items-center">
                                <div class="col-md-9">
                                    <label class="form-label fw-semibold">Select CSV File (Columns: Department, Course, Subject Name, Year, Semester, Subject Code)</label>
                                    <input type="file" name="csv_file" class="form-control" accept=".csv" required>
                                </div>
                                <div class="col-md-3 mt-md-4 pt-md-2">
                                    <button type="submit" name="import_csv" class="btn btn-success w-100 fw-bold">Upload & Import</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Main Subject Form -->
                <form class="container mt-4 needs-validation" novalidate method="post">
                    <h2 class="mb-4">Subject Information Form</h2>
                    
                    <div class="row g-3">
                        <!-- Department Name (Dynamic Select) -->
                        <div class="col-md-6">
                            <label for="dept_name" class="form-label">Department Name</label>
                            <select class="form-select" name="dept_name" id="dept_name" required>
                                <option selected disabled value="">Select Department</option>
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

                        <!-- Course Name (Dynamic Select) -->
                        <div class="col-md-6">
                            <label for="course_name" class="form-label">Course Name</label>
                            <select class="form-select" name="course_name" id="course_name" required>
                                <option selected disabled value="">Select Course</option>
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

                        <!-- Subject Name -->
                        <div class="col-md-6">
                            <label for="subject_name" class="form-label">Subject Name</label>
                            <input type="text" class="form-control" name="subject_name" id="subject_name" placeholder="Enter Subject Name" required>
                        </div>

                        <!-- Subject Code -->
                        <div class="col-md-6">
                            <label for="subject_code" class="form-label">Subject Code</label>
                            <input type="text" class="form-control" name="subject_code" id="subject_code" placeholder="e.g. SUB101" required>
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
                            <button type="submit" name="insert_subject" class="btn btn-primary w-25">Add Subject</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>