<?php
include "../db_connect.php";
session_start();

// 1. Secure the page: Check if the teacher is actually logged in
if (!isset($_SESSION['teacher_id']) || !isset($_SESSION['teacher_name'])) {
    header("Location: ../index.php");
    exit;
}

$id = $_SESSION['teacher_id'];
$teacher_name = $_SESSION['teacher_name'];

// 2. ACTIVE DB ASSIGNMENT PROCESSOR WITH SUCCESS NOTIFICATION ACTION
if (isset($_POST['assign_subject'])) {
    $student_id = mysqli_real_escape_string($conn, $_POST['student_id']);
    $subject_name = mysqli_real_escape_string($conn, $_POST['subject_name']);
    
    // Step A: Fetch student complete details using the submitted ID
    $student_info_query = "SELECT name, faculty, course FROM `students` WHERE id = '$student_id'";
    $student_info_result = mysqli_query($conn, $student_info_query);
    
    if ($student_info_result && mysqli_num_rows($student_info_result) > 0) {
        $student_data = mysqli_fetch_assoc($student_info_result);
        $s_name = mysqli_real_escape_string($conn, $student_data['name']);
        $s_faculty = mysqli_real_escape_string($conn, $student_data['faculty']);
        $s_course = mysqli_real_escape_string($conn, $student_data['course']);
        
        // Step B: Fetch subject_code from subjected_teacher table to keep schema aligned
        $code_query = "SELECT subject_code FROM `subjected_teacher` WHERE subject_name = '$subject_name' AND teacher_id = '$id' LIMIT 1";
        $code_result = mysqli_query($conn, $code_query);
        $subject_code = "";
        if ($code_result && mysqli_num_rows($code_result) > 0) {
            $code_data = mysqli_fetch_assoc($code_result);
            $subject_code = mysqli_real_escape_string($conn, $code_data['subject_code']);
        }

        // Step C: Check if this specific student name is already assigned to this subject name
        $check_query = "SELECT * FROM `subjected_student` WHERE student_name = '$s_name' AND subject_name = '$subject_name'";
        $check_result = mysqli_query($conn, $check_query);
        
        if (mysqli_num_rows($check_result) > 0) {
            echo "<script>alert('⚠️ This student is already assigned to this subject!'); window.location.href='assign_student_subject.php';</script>";
            exit;
        } else {
            // Step D: Insert matching records into the true schema columns
            $insert_query = "INSERT INTO `subjected_student` (student_name, subject_name, subject_code, faculty, course) 
                             VALUES ('$s_name', '$subject_name', '$subject_code', '$s_faculty', '$s_course')";
            
            if (mysqli_query($conn, $insert_query)) {
                echo "<script>alert('✅ Subject mapping successfully assigned!'); window.location.href='assign_student_subject.php';</script>";
                exit;
            } else {
                echo "<script>alert('❌ Error inserting record: " . addslashes(mysqli_error($conn)) . "'); window.location.href='assign_student_subject.php';</script>";
                exit;
            }
        }
    } else {
        echo "<script>alert('❌ Selected student record not found.'); window.location.href='assign_student_subject.php';</script>";
        exit;
    }
}

// Getting distinct subject data using GROUP BY to prevent duplicate subject rows
$query = "SELECT subject_name FROM `attendance` GROUP BY subject_name";
$result = mysqli_query($conn, $query);
?>
<!doctype html>
<html lang="en" data-bs-theme="light">

<head>
    <title>Assign Mapping | MHU-AMS</title>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />

    <!-- Bootstrap CSS v5.3.8 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" />

    <style>
        body { background-color: #f4f6f9; }
        .form-card { background: #ffffff; border: none; border-radius: 14px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03); }
    </style>
</head>

<body>
    <header>
        <nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm py-2">
            <div class="container-fluid">
                <a class="navbar-brand text-warning fw-bold fs-4 d-flex align-items-center" href="index.php">
                    <i class="fa-solid fa-graduation-cap me-2"></i>MOTHERHOOD UNIVERSITY
                </a>
                
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#dashboardNavbar" aria-controls="dashboardNavbar" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse justify-content-end" id="dashboardNavbar">
                    <div class="d-flex flex-column flex-lg-row align-items-stretch align-items-lg-center gap-2 mt-2 mt-lg-0 w-100 w-lg-auto">
                        <span class="navbar-text text-white bg-secondary bg-opacity-25 border border-secondary px-3 py-1.5 rounded-pill small d-inline-flex align-items-center justify-content-center">
                            <i class="fa-solid fa-user-tie me-2 text-warning"></i> Welcome, <?php echo htmlspecialchars($teacher_name); ?>
                        </span>
                        <a href="index.php" class="btn btn-sm btn-outline-info px-3 shadow-sm"><i class="fa-solid fa-house me-1"></i> Dashboard</a>
                        <a href="../logout.php" class="btn btn-sm btn-danger shadow-sm px-3"><i class="fa-solid fa-power-off me-1"></i> Logout</a>
                    </div>
                </div>
            </div>
        </nav>
    </header>

    <main class="container py-5">
        <div class="row justify-content-center">
            <div class="col-12 col-md-10 col-lg-7">
                <div class="form-card card p-4 p-md-5">
                    
                    <div class="text-center mb-4">
                        <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-3 py-1.5 rounded-pill uppercase fw-bold tracking-wider mb-2">Roster Mapping</span>
                        <h2 class="fw-bold text-dark">Assign Subjects to Students</h2>
                        <p class="text-muted small">Link specific individual database profiles to your assigned operational university courses.</p>
                    </div>

                    <?php
                    $query = "SELECT * FROM `students`";
                    $result = mysqli_query($conn, $query);
                    if ($result && mysqli_num_rows($result) > 0) {
                        ?>
                        <form method="POST" action="assign_student_subject.php">
                            <div class="mb-4">
                                <label for="student" class="form-label fw-semibold text-secondary small text-uppercase">Select Student Profile:</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light text-muted"><i class="fa-solid fa-user"></i></span>
                                    <select class="form-select border-start-0 ps-1" id="student" name="student_id" required>
                                        <option value="" disabled selected>Choose a student...</option>
                                        <?php
                                        while ($row = mysqli_fetch_assoc($result)) {
                                            echo '<option value="' . htmlspecialchars($row['id']) . '">' . htmlspecialchars($row['name']) . ' (' . htmlspecialchars($row['roll_number']) . ')</option>';
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>

                            <?php
                            // Fetch subjects mapped to this specific teacher
                            $subject_query = "SELECT DISTINCT subject_name FROM `subjected_teacher` WHERE teacher_id = '$id'";
                            $subject_result = mysqli_query($conn, $subject_query);

                            if ($subject_result && mysqli_num_rows($subject_result) > 0) {
                                ?>
                                <div class="mb-4">
                                    <label for="subject" class="form-label fw-semibold text-secondary small text-uppercase">Select Course Subject:</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light text-muted"><i class="fa-solid fa-book"></i></span>
                                        <select class="form-select border-start-0 ps-1" id="subject" name="subject_name" required>
                                            <option value="" disabled selected>Choose a subject...</option>
                                            <?php
                                            while ($subject_row = mysqli_fetch_assoc($subject_result)) {
                                                echo '<option value="' . htmlspecialchars($subject_row['subject_name']) . '">' . htmlspecialchars($subject_row['subject_name']) . '</option>';
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                                
                                <button type="submit" name="assign_subject" class="btn btn-primary w-100 py-2.5 fw-bold shadow-sm">
                                    <i class="fa-solid fa-circle-plus me-2"></i>Confirm Assignment Mapping
                                </button>
                                <?php
                            } else {
                                ?>
                                <div class="alert alert-warning border-warning-subtle d-flex align-items-center" role="alert">
                                    <i class="fa-solid fa-triangle-exclamation me-2 fs-5"></i>
                                    <div>No subjects are currently configured or mapped to your faculty profile record.</div>
                                </div>
                                <?php
                            }
                            ?>
                        </form>
                        <?php
                    } else {
                        ?>
                        <div class="alert alert-danger border-danger-subtle d-flex align-items-center" role="alert">
                            <i class="fa-solid fa-ban me-2 fs-5"></i>
                            <div>No students discovered within the active database directory registry.</div>
                        </div>
                        <?php
                    }   
                    ?>
                </div>
            </div>
        </div>
    </main>

    <footer class="text-center py-4 mt-5 text-muted small bg-white border-top">
        <p class="mb-0">&copy; 2026 Motherhood University Attendance Management System (AMS).</p>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI"
        crossorigin="anonymous"></script>
</body>
</html>