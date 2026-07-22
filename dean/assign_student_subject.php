<?php
include "../db_connect.php";
session_start();

// 1. Secure the page: Check if the dean is actually logged in
if (!isset($_SESSION['dean_id']) || !isset($_SESSION['dean_name'])) {
    header("Location: ../index.php");
    exit;
}

$id = $_SESSION['dean_id'];
$teacher_name = $_SESSION['dean_name'];

// Fetch faculty_name safely from the deans table if not already in session
$dean_query = "SELECT faculty_name FROM `deans` WHERE id = '$id' LIMIT 1";
$dean_result = mysqli_query($conn, $dean_query);
if ($dean_result && mysqli_num_rows($dean_result) > 0) {
    $dean_data = mysqli_fetch_assoc($dean_result);
    $faculty_name = $dean_data['faculty_name'];
    $_SESSION['faculty_name'] = $faculty_name;
} else {
    $faculty_name = $_SESSION['faculty_name'] ?? '';
}

// Getting selected course to insert or retaining it from session
if (isset($_POST['course_submit']) && !empty($_POST['course_name'])) {
    $course_name = $_POST['course_name'];
    $_SESSION['course_name'] = $course_name;
} elseif (isset($_SESSION['course_name'])) {
    $course_name = $_SESSION['course_name'];
} else {
    $course_name = "";
}

// ==========================================
// 2A. SINGLE ASSIGNMENT PROCESSOR
// ==========================================
if (isset($_POST['assign_subject'])) {
    $student_id = mysqli_real_escape_string($conn, $_POST['student_id']);
    $subject_name = mysqli_real_escape_string($conn, $_POST['subject_name']);

    // Step A: Fetch student complete details using the submitted ID
    $student_info_query = "SELECT name, roll_number, faculty, course, year, sem FROM `students` WHERE id = '$student_id' AND course= '$course_name'";
    $student_info_result = mysqli_query($conn, $student_info_query);

    if ($student_info_result && mysqli_num_rows($student_info_result) > 0) {
        $student_data = mysqli_fetch_assoc($student_info_result);
        $s_name = mysqli_real_escape_string($conn, $student_data['name']);
        $s_roll = (int) $student_data['roll_number'];
        $s_faculty = mysqli_real_escape_string($conn, $student_data['faculty']);
        $s_course = mysqli_real_escape_string($conn, $student_data['course']);
        $s_year = (int) $student_data['year'];
        $s_sem = (int) $student_data['sem'];

        // Step B: Fetch subject_code from subjected_teacher, fallback to subjects table if empty
        $code_query = "SELECT subject_code FROM `subjected_teacher` WHERE subject_name = '$subject_name' AND teacher_id = '$id' LIMIT 1";
        $code_result = mysqli_query($conn, $code_query);
        $subject_code = "";
        
        if ($code_result && mysqli_num_rows($code_result) > 0) {
            $code_data = mysqli_fetch_assoc($code_result);
            $subject_code = mysqli_real_escape_string($conn, $code_data['subject_code']);
        } else {
            // Fallback lookup from subjects table
            $sub_fallback = "SELECT subject_code FROM `subjects` WHERE subject_name = '$subject_name' AND course_name = '$course_name' LIMIT 1";
            $sub_res = mysqli_query($conn, $sub_fallback);
            if ($sub_res && mysqli_num_rows($sub_res) > 0) {
                $sub_data = mysqli_fetch_assoc($sub_res);
                $subject_code = mysqli_real_escape_string($conn, $sub_data['subject_code']);
            }
        }

        // Step C: Check if this specific student roll number is already assigned to this subject for this semester
        $check_query = "SELECT id FROM `subjected_student` WHERE roll_number = '$s_roll' AND subject_name = '$subject_name' AND semester = '$s_sem'";
        $check_result = mysqli_query($conn, $check_query);

        if (mysqli_num_rows($check_result) > 0) {
            echo "<script>alert('⚠️ This student is already assigned to this subject for this semester!'); window.location.href='assign_student_subject.php';</script>";
            exit;
        } else {
            // Step D: Insert matching records including roll_number
            $insert_query = "INSERT INTO `subjected_student` (student_name, subject_name, subject_code, faculty, course, year, semester, roll_number) 
                             VALUES ('$s_name', '$subject_name', '$subject_code', '$s_faculty', '$s_course', '$s_year', '$s_sem', '$s_roll')";

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

// ==========================================
// 2B. BULK ASSIGNMENT PROCESSOR (CSV)
// ==========================================
if (isset($_POST['bulk_assign_subject'])) {
    if (isset($_FILES['excel_file']['tmp_name']) && $_FILES['excel_file']['tmp_name'] != "") {
        $handle = fopen($_FILES['excel_file']['tmp_name'], "r");

        $success_count = 0;
        $error_count = 0;
        $first_row = true; // Flag to skip header

        while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
            if ($first_row || empty(trim($data[0]))) {
                $first_row = false;
                continue;
            }

            // Map data from CSV (Columns A, B, C, D, E)
            $roll_number = mysqli_real_escape_string($conn, trim($data[0]));
            $csv_subject = mysqli_real_escape_string($conn, trim($data[2]));
            $csv_semester = isset($data[3]) ? (int) trim($data[3]) : 0;
            $csv_year = isset($data[4]) ? (int) trim($data[4]) : 0;

            // 1. Fetch Student Details from DB
            $student_query = "SELECT name, roll_number, faculty, course, year, sem FROM `students` WHERE roll_number = '$roll_number'";
            $student_result = mysqli_query($conn, $student_query);

            if ($student_result && mysqli_num_rows($student_result) > 0) {
                $student = mysqli_fetch_assoc($student_result);
                $student_name_safe = mysqli_real_escape_string($conn, $student['name']);
                $student_roll = (int) $student['roll_number'];

                $final_sem = ($csv_semester > 0) ? $csv_semester : (int) $student['sem'];
                $final_year = ($csv_year > 0) ? $csv_year : (int) $student['year'];

                // 2. Fetch Subject Code for this Teacher or fallback to subjects table
                $code_query = "SELECT subject_code FROM `subjected_teacher` 
                               WHERE TRIM(subject_name) = '$csv_subject' 
                               AND teacher_id = '$id' LIMIT 1";
                $code_result = mysqli_query($conn, $code_query);
                $subject_code = "";

                if ($code_result && mysqli_num_rows($code_result) > 0) {
                    $code_data = mysqli_fetch_assoc($code_result);
                    $subject_code = mysqli_real_escape_string($conn, $code_data['subject_code']);
                } else {
                    $sub_fallback = "SELECT subject_code FROM `subjects` WHERE TRIM(subject_name) = '$csv_subject' AND course_name = '$course_name' LIMIT 1";
                    $sub_res = mysqli_query($conn, $sub_fallback);
                    if ($sub_res && mysqli_num_rows($sub_res) > 0) {
                        $sub_data = mysqli_fetch_assoc($sub_res);
                        $subject_code = mysqli_real_escape_string($conn, $sub_data['subject_code']);
                    }
                }

                // 3. Check for Duplicate
                $check_dup = "SELECT id FROM `subjected_student` 
                              WHERE roll_number = '$student_roll' 
                              AND subject_name = '$csv_subject'
                              AND semester = '$final_sem'";
                $dup_result = mysqli_query($conn, $check_dup);

                if (mysqli_num_rows($dup_result) == 0) {
                    // 4. Insert Record including roll_number
                    $insert = "INSERT INTO `subjected_student` (student_name, subject_name, subject_code, faculty, course, year, semester, roll_number) 
                               VALUES ('$student_name_safe', 
                                       '$csv_subject', 
                                       '$subject_code', 
                                       '" . mysqli_real_escape_string($conn, $student['faculty']) . "', 
                                       '" . mysqli_real_escape_string($conn, $student['course']) . "', 
                                       '$final_year', 
                                       '$final_sem',
                                       '$student_roll')";

                    if (mysqli_query($conn, $insert)) {
                        $success_count++;
                    } else {
                        $error_count++;
                    }
                } else {
                    $error_count++; // Duplicate found
                }
            } else {
                $error_count++; // Student not found
            }
        }
        fclose($handle);
        echo "<script>alert('✅ Upload complete! Successfully assigned: $success_count. Failed/Skipped: $error_count.'); window.location.href='assign_student_subject.php';</script>";
        exit;
    } else {
        echo "<script>alert('❌ Please select a file first.'); window.location.href='assign_student_subject.php';</script>";
        exit;
    }
}

// Fetch general students and subjects for single assign dropdowns
$query = "SELECT * FROM `students` WHERE course='$course_name'";
$result = mysqli_query($conn, $query);
$subject_query = "SELECT DISTINCT subject_name FROM `subjects` WHERE faculty_name = '$faculty_name' AND course_name='$course_name'";
$subject_result = mysqli_query($conn, $subject_query);

$subjects_list = [];
if ($subject_result && mysqli_num_rows($subject_result) > 0) {
    while ($subject_row = mysqli_fetch_assoc($subject_result)) {
        $subjects_list[] = $subject_row['subject_name'];
    }
}
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
        body {
            background-color: #f4f6f9;
        }

        .form-card {
            background: #ffffff;
            border: none;
            border-radius: 14px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03);
        }

        .nav-tabs .nav-link {
            color: #6c757d;
            font-weight: 500;
        }

        .nav-tabs .nav-link.active {
            color: #0d6efd;
            font-weight: 600;
            border-bottom: 3px solid #0d6efd;
        }

        .csv-format-table th {
            background-color: #f8f9fa;
            font-size: 0.85rem;
        }

        .csv-format-table td {
            font-size: 0.85rem;
            font-family: monospace;
        }
    </style>
</head>

<body>
    <header>
        <nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm py-2">
            <div class="container-fluid">
                <a class="navbar-brand text-warning fw-bold fs-4 d-flex align-items-center" href="index.php">
                    <i class="fa-solid fa-graduation-cap me-2"></i>MOTHERHOOD UNIVERSITY
                </a>

                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#dashboardNavbar"
                    aria-controls="dashboardNavbar" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse justify-content-end" id="dashboardNavbar">
                    <div
                        class="d-flex flex-column flex-lg-row align-items-stretch align-items-lg-center gap-2 mt-2 mt-lg-0 w-100 w-lg-auto">
                        <span
                            class="navbar-text text-white bg-secondary bg-opacity-25 border border-secondary px-3 py-1.5 rounded-pill small d-inline-flex align-items-center justify-content-center">
                            <i class="fa-solid fa-user-tie me-2 text-warning"></i> Welcome,
                            <?php echo htmlspecialchars($teacher_name); ?>
                        </span>
                        <a href="index.php" class="btn btn-sm btn-outline-info px-3 shadow-sm"><i
                                class="fa-solid fa-house me-1"></i> Dashboard</a>
                        <a href="../logout.php" class="btn btn-sm btn-danger shadow-sm px-3"><i
                                class="fa-solid fa-power-off me-1"></i> Logout</a>
                    </div>
                </div>
            </div>
        </nav>
    </header>

    <main class="container py-5">
        <div class="row justify-content-center">
            <div class="col-12 col-md-10 col-lg-8">
                <div class="form-card card p-4 p-md-5">

                    <div class="text-center mb-4">
                        <span
                            class="badge bg-primary-subtle text-primary border border-primary-subtle px-3 py-1.5 rounded-pill uppercase fw-bold tracking-wider mb-2"><?php echo htmlspecialchars($course_name); ?></span>
                        <h2 class="fw-bold text-dark">Assign Subjects to Students</h2>
                        <p class="text-muted small">Link specific database profiles to your assigned university courses
                            manually or via bulk upload.</p>
                    </div>

                    <?php if (empty($subjects_list)): ?>
                        <div class="alert alert-warning border-warning-subtle d-flex align-items-center" role="alert">
                            <i class="fa-solid fa-triangle-exclamation me-2 fs-5"></i>
                            <div>No subjects are currently configured or mapped to your faculty profile record.</div>
                        </div>
                    <?php else: ?>

                        <!-- Bootstrap Tabs Navigation -->
                        <ul class="nav nav-tabs mb-4" id="assignmentTabs" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active w-100 px-4" id="single-tab" data-bs-toggle="tab"
                                    data-bs-target="#single-pane" type="button" role="tab" aria-controls="single-pane"
                                    aria-selected="true">
                                    <i class="fa-solid fa-user me-2"></i>Single Assign
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link w-100 px-4" id="bulk-tab" data-bs-toggle="tab"
                                    data-bs-target="#bulk-pane" type="button" role="tab" aria-controls="bulk-pane"
                                    aria-selected="false">
                                    <i class="fa-solid fa-file-excel me-2"></i>Bulk Assign (CSV)
                                </button>
                            </li>
                        </ul>

                        <!-- Tabs Content -->
                        <div class="tab-content" id="myTabContent">

                            <!-- TAB 1: Single Assignment -->
                            <div class="tab-pane fade show active" id="single-pane" role="tabpanel"
                                aria-labelledby="single-tab" tabindex="0">
                                <?php if ($result && mysqli_num_rows($result) > 0): ?>
                                    <form method="POST" action="assign_student_subject.php">
                                        <div class="mb-4">
                                            <label for="student"
                                                class="form-label fw-semibold text-secondary small text-uppercase">Select
                                                Student Profile:</label>
                                            <div class="input-group">
                                                <span class="input-group-text bg-light text-muted"><i
                                                        class="fa-solid fa-user"></i></span>
                                                <select class="form-select border-start-0 ps-1" id="student" name="student_id"
                                                    required>
                                                    <option value="" disabled selected>Choose a student...</option>
                                                    <?php
                                                    while ($row = mysqli_fetch_assoc($result)) {
                                                        echo '<option value="' . htmlspecialchars($row['id']) . '">' . htmlspecialchars($row['name']) . ' (' . htmlspecialchars($row['roll_number']) . ')</option>';
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="mb-4">
                                            <label for="subject"
                                                class="form-label fw-semibold text-secondary small text-uppercase">Select Course
                                                Subject:</label>
                                            <div class="input-group">
                                                <span class="input-group-text bg-light text-muted"><i
                                                        class="fa-solid fa-book"></i></span>
                                                <select class="form-select border-start-0 ps-1" id="subject" name="subject_name"
                                                    required>
                                                    <option value="" disabled selected>Choose a subject...</option>
                                                    <?php
                                                    foreach ($subjects_list as $subject) {
                                                        echo '<option value="' . htmlspecialchars($subject) . '">' . htmlspecialchars($subject) . '</option>';
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                        </div>

                                        <button type="submit" name="assign_subject"
                                            class="btn btn-primary w-100 py-2.5 fw-bold shadow-sm">
                                            <i class="fa-solid fa-circle-plus me-2"></i>Confirm Assignment Mapping
                                        </button>
                                    </form>
                                <?php else: ?>
                                    <div class="alert alert-danger border-danger-subtle d-flex align-items-center" role="alert">
                                        <i class="fa-solid fa-ban me-2 fs-5"></i>
                                        <div>No students discovered within the active database directory registry.</div>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <!-- TAB 2: Bulk Assignment -->
                            <div class="tab-pane fade" id="bulk-pane" role="tabpanel" aria-labelledby="bulk-tab"
                                tabindex="0">
                                <div class="alert alert-info small border-info-subtle mb-4 position-relative">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <strong><i class="fa-solid fa-circle-info me-1"></i> CSV Format
                                            Instructions:</strong>
                                        <a href="data:text/csv;charset=utf-8,Roll%20Number%2CStudent%20Name%2CSubject%20Name%2CSemester%2CYear%0A26CSE001%2CAarav%20Sharma%2CPrinciples%20and%20Practice%20of%20Management%2C4%2C4%0A25ECE024%2CPriya%20Singh%2CDigital%20Electronics%2C3%2C2"
                                            download="sample_mapping_format.csv"
                                            class="btn btn-sm btn-outline-info bg-white shadow-sm fw-bold">
                                            <i class="fa-solid fa-file-arrow-down me-1"></i> Download Sample CSV
                                        </a>
                                    </div>
                                    Your uploaded file <strong>must</strong> be saved as a <code>.csv</code> (Comma
                                    delimited) and follow the exact column order below. The first row is automatically
                                    skipped (used for headers).

                                    <div class="table-responsive mt-3">
                                        <table class="table table-bordered table-sm csv-format-table bg-white">
                                            <thead>
                                                <tr>
                                                    <th>A: Roll Number</th>
                                                    <th>B: Student Name</th>
                                                    <th>C: Subject Name</th>
                                                    <th>D: Semester</th>
                                                    <th>E: Year</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td>26CSE001</td>
                                                    <td>Aarav Sharma</td>
                                                    <td>Principles and Practice of Management</td>
                                                    <td>4</td>
                                                    <td>4</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                    <p class="mb-0 text-muted"><i class="fa-solid fa-lightbulb text-warning me-1"></i>
                                        <em>Note: "Faculty" and "Course" constraints are dynamically pulled from the
                                            database using the provided Roll Number.</em>
                                    </p>
                                </div>

                                <form method="POST" action="assign_student_subject.php" enctype="multipart/form-data">
                                    <div class="mb-4">
                                        <label for="excel_file"
                                            class="form-label fw-semibold text-secondary small text-uppercase">Upload Mapped
                                            CSV File:</label>
                                        <input class="form-control form-control-lg" type="file" id="excel_file"
                                            name="excel_file" accept=".csv" required>
                                    </div>

                                    <button type="submit" name="bulk_assign_subject"
                                        class="btn btn-success w-100 py-2.5 fw-bold shadow-sm">
                                        <i class="fa-solid fa-cloud-arrow-up me-2"></i>Process Bulk Upload
                                    </button>
                                </form>
                            </div>

                        </div>
                    <?php endif; ?>
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