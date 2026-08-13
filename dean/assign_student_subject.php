<?php
include "../db_connect.php";

session_start();

// 1. Secure the page: Check if the dean is actually logged in
if (!isset($_SESSION['dean_id']) || !isset($_SESSION['dean_name'])) {
    header("Location: ../index.php");
    exit;
} 

$id = intval($_SESSION['dean_id']);
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
    $course_name = trim($_POST['course_name']);
    $_SESSION['course_name'] = $course_name;
    unset($_SESSION['filter_sem']);
} elseif (isset($_SESSION['course_name'])) {
    $course_name = $_SESSION['course_name'];
} else {
    $course_name = "";
}

// Handling Semester Filter Selection
if (isset($_POST['filter_sem'])) {
    $selected_sem = trim($_POST['filter_sem']);
    $_SESSION['filter_sem'] = $selected_sem;
} else {
    $selected_sem = $_SESSION['filter_sem'] ?? '';
}

$safe_course_name = mysqli_real_escape_string($conn, $course_name);

// ==========================================
// 2A. SINGLE ASSIGNMENT PROCESSOR
// ==========================================
if (isset($_POST['assign_subject'])) {
    $student_id = intval($_POST['student_id']);
    $subject_name = trim($_POST['subject_name']);

    // Fetch student complete details including both roll and enrollment numbers
    $student_info_query = "SELECT name, roll_number, enrollment_number, faculty, course, year, sem, session FROM `students` WHERE id = '$student_id' AND course = '$safe_course_name' LIMIT 1";
    $student_info_result = mysqli_query($conn, $student_info_query);

    if ($student_info_result && mysqli_num_rows($student_info_result) > 0) {
        $student_data = mysqli_fetch_assoc($student_info_result);
        $s_name = mysqli_real_escape_string($conn, $student_data['name']);
        
        $s_roll = mysqli_real_escape_string($conn, trim($student_data['roll_number']));
        $s_enroll = mysqli_real_escape_string($conn, trim($student_data['enrollment_number']));

        $s_faculty = mysqli_real_escape_string($conn, $student_data['faculty']);
        $s_course = mysqli_real_escape_string($conn, $student_data['course']);
        $s_year = (int) $student_data['year'];
        $s_sem = (int) $student_data['sem'];
        $s_session = mysqli_real_escape_string($conn, $student_data['session']);
        
        // Fetch subject_code
        $safe_subject_name = mysqli_real_escape_string($conn, $subject_name);
        $code_query = "SELECT subject_code FROM `subjected_teacher` WHERE TRIM(subject_name) = '$safe_subject_name' AND teacher_id = '$id' LIMIT 1";
        $code_result = mysqli_query($conn, $code_query);
        $subject_code = "";
        
        if ($code_result && mysqli_num_rows($code_result) > 0) {
            $code_data = mysqli_fetch_assoc($code_result);
            $subject_code = mysqli_real_escape_string($conn, $code_data['subject_code']);
        } else {
            $sub_fallback = "SELECT subject_code FROM `subjects` WHERE TRIM(subject_name) = '$safe_subject_name' AND course_name = '$safe_course_name' LIMIT 1";
            $sub_res = mysqli_query($conn, $sub_fallback);
            if ($sub_res && mysqli_num_rows($sub_res) > 0) {
                $sub_data = mysqli_fetch_assoc($sub_res);
                $subject_code = mysqli_real_escape_string($conn, $sub_data['subject_code']);
            }
        }

        // Robust Duplicate Check using either roll_number or enrollment_number
        $check_query = "SELECT id FROM `subjected_student` 
                        WHERE semester = '$s_sem' 
                        AND TRIM(subject_name) = '$safe_subject_name' 
                        AND (
                            ('$s_roll' != '' AND TRIM(roll_number) = '$s_roll') OR 
                            ('$s_enroll' != '' AND TRIM(enrollment_number) = '$s_enroll')
                        ) LIMIT 1";
        $check_result = mysqli_query($conn, $check_query);

        if (mysqli_num_rows($check_result) > 0) {
            $_SESSION['flash_message'] = "⚠️ This student is already assigned to this subject for this semester!";
            $_SESSION['flash_type'] = "warning";
        } else {
            $insert_query = "INSERT INTO `subjected_student` (student_name, subject_name, subject_code, faculty, course, year, semester, roll_number, enrollment_number, session) 
                             VALUES ('$s_name', '$safe_subject_name', '$subject_code', '$s_faculty', '$s_course', '$s_year', '$s_sem', '$s_roll', '$s_enroll', '$s_session')";

            if (mysqli_query($conn, $insert_query)) {
                $_SESSION['flash_message'] = "✅ Subject mapping successfully assigned!";
                $_SESSION['flash_type'] = "success";
            } else {
                $_SESSION['flash_message'] = "❌ Error inserting record: " . mysqli_error($conn);
                $_SESSION['flash_type'] = "danger";
            }
        }
    } else {
        $_SESSION['flash_message'] = "❌ Selected student record not found.";
        $_SESSION['flash_type'] = "danger";
    }
    header("Location: assign_student_subject.php");
    exit();
}

// ==========================================
// 2B. BULK ASSIGNMENT PROCESSOR (CSV)
// ==========================================
if (isset($_POST['bulk_assign_subject'])) {
    if (isset($_FILES['excel_file']['tmp_name']) && $_FILES['excel_file']['tmp_name'] != "") {
        $handle = fopen($_FILES['excel_file']['tmp_name'], "r");

        $success_count = 0;
        $error_count = 0;
        $first_row = true;

        while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
            if ($first_row || (empty(trim($data[0])) && empty(trim($data[1])))) {
                $first_row = false;
                continue;
            }

            $csv_roll = mysqli_real_escape_string($conn, trim($data[0]));
            $csv_enroll = mysqli_real_escape_string($conn, trim($data[1]));
            $csv_subject = isset($data[2]) ? trim($data[2]) : '';
            $csv_semester = isset($data[3]) ? (int) trim($data[3]) : 0;
            $csv_year = isset($data[4]) ? (int) trim($data[4]) : 0;

            if (empty($csv_subject)) {
                $error_count++;
                continue;
            }

            $student_query = "SELECT name, roll_number, enrollment_number, faculty, course, year, sem, session FROM `students` WHERE 1=1";
            if (!empty($csv_roll) && !empty($csv_enroll)) {
                $student_query .= " AND (TRIM(roll_number) = '$csv_roll' OR TRIM(enrollment_number) = '$csv_enroll')";
            } elseif (!empty($csv_roll)) {
                $student_query .= " AND TRIM(roll_number) = '$csv_roll'";
            } elseif (!empty($csv_enroll)) {
                $student_query .= " AND TRIM(enrollment_number) = '$csv_enroll'";
            } else {
                $error_count++;
                continue;
            }
            $student_query .= " LIMIT 1";

            $student_result = mysqli_query($conn, $student_query);

            if ($student_result && mysqli_num_rows($student_result) > 0) {
                $student = mysqli_fetch_assoc($student_result);
                $student_name_safe = mysqli_real_escape_string($conn, $student['name']);
                
                $student_roll = mysqli_real_escape_string($conn, trim($student['roll_number']));
                $student_enroll = mysqli_real_escape_string($conn, trim($student['enrollment_number']));
                $student_session = mysqli_real_escape_string($conn, $student['session']);

                $final_sem = ($csv_semester > 0) ? $csv_semester : (int) $student['sem'];
                $final_year = ($csv_year > 0) ? $csv_year : (int) $student['year'];

                $safe_csv_subject = mysqli_real_escape_string($conn, $csv_subject);

                $code_query = "SELECT subject_code FROM `subjected_teacher` WHERE TRIM(subject_name) = '$safe_csv_subject' AND teacher_id = '$id' LIMIT 1";
                $code_result = mysqli_query($conn, $code_query);
                $subject_code = "";

                if ($code_result && mysqli_num_rows($code_result) > 0) {
                    $code_data = mysqli_fetch_assoc($code_result);
                    $subject_code = mysqli_real_escape_string($conn, $code_data['subject_code']);
                } else {
                    $sub_fallback = "SELECT subject_code FROM `subjects` WHERE TRIM(subject_name) = '$safe_csv_subject' AND course_name = '$safe_course_name' LIMIT 1";
                    $sub_res = mysqli_query($conn, $sub_fallback);
                    if ($sub_res && mysqli_num_rows($sub_res) > 0) {
                        $sub_data = mysqli_fetch_assoc($sub_res);
                        $subject_code = mysqli_real_escape_string($conn, $sub_data['subject_code']);
                    }
                }

                $check_dup = "SELECT id FROM `subjected_student` 
                              WHERE semester = '$final_sem' 
                              AND TRIM(subject_name) = '$safe_csv_subject'
                              AND (
                                  ('$student_roll' != '' AND TRIM(roll_number) = '$student_roll') OR 
                                  ('$student_enroll' != '' AND TRIM(enrollment_number) = '$student_enroll')
                              ) LIMIT 1";
                $dup_result = mysqli_query($conn, $check_dup);

                if (mysqli_num_rows($dup_result) == 0) {
                    $insert = "INSERT INTO `subjected_student` (student_name, subject_name, subject_code, faculty, course, year, semester, roll_number, enrollment_number, session) 
                               VALUES ('$student_name_safe', 
                                       '$safe_csv_subject', 
                                       '$subject_code', 
                                       '" . mysqli_real_escape_string($conn, $student['faculty']) . "', 
                                       '" . mysqli_real_escape_string($conn, $student['course']) . "', 
                                       '$final_year', 
                                       '$final_sem', 
                                       '$student_roll',
                                       '$student_enroll',
                                       '$student_session')";

                    if (mysqli_query($conn, $insert)) {
                        $success_count++;
                    } else {
                        $error_count++;
                    }
                } else {
                    $error_count++;
                }
            } else {
                $error_count++;
            }
        }
        fclose($handle);
        $_SESSION['flash_message'] = "✅ Upload complete! Successfully assigned: $success_count. Failed/Skipped: $error_count.";
        $_SESSION['flash_type'] = $error_count > 0 ? "warning" : "success";
    } else {
        $_SESSION['flash_message'] = "❌ Please select a valid CSV file first.";
        $_SESSION['flash_type'] = "danger";
    }
    header("Location: assign_student_subject.php");
    exit();
}

// Fetch distinct semesters available for this course
$sem_list_query = "SELECT DISTINCT sem AS semester FROM `students` WHERE course = '$safe_course_name' UNION SELECT DISTINCT semester FROM `subjects` WHERE course_name = '$safe_course_name' ORDER BY semester ASC";
$sem_list_result = mysqli_query($conn, $sem_list_query);
$available_semesters = [];
if ($sem_list_result) {
    while ($s_row = mysqli_fetch_assoc($sem_list_result)) {
        if (!empty($s_row['semester'])) {
            $available_semesters[] = $s_row['semester'];
        }
    }
}

// Fetch students for single assign dropdown with optional semester filter applied
$query = "SELECT * FROM `students` WHERE course = '$safe_course_name'";
if (!empty($selected_sem)) {
    $query .= " AND sem = '" . mysqli_real_escape_string($conn, $selected_sem) . "'";
}
$query .= " ORDER BY name ASC";
$result = mysqli_query($conn, $query);

// Fetch subjects for single assign dropdown with optional semester filter applied
$safe_faculty_name = mysqli_real_escape_string($conn, $faculty_name);
$subject_query = "SELECT DISTINCT subject_name, semester FROM `subjects` WHERE faculty_name = '$safe_faculty_name' AND course_name = '$safe_course_name'";
if (!empty($selected_sem)) {
    $subject_query .= " AND semester = '" . mysqli_real_escape_string($conn, $selected_sem) . "'";
}
$subject_query .= " ORDER BY subject_name ASC";
$subject_result = mysqli_query($conn, $subject_query);

$subjects_list = [];
if ($subject_result && mysqli_num_rows($subject_result) > 0) {
    while ($subject_row = mysqli_fetch_assoc($subject_result)) {
        $subjects_list[] = [
            'subject_name' => $subject_row['subject_name'],
            'semester' => $subject_row['semester'] ?? 'N/A'
        ];
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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" />

    <style>
        body { background-color: #f4f6f9; }
        .form-card { background: #ffffff; border: none; border-radius: 14px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03); }
        .nav-tabs .nav-link { color: #6c757d; font-weight: 500; }
        .nav-tabs .nav-link.active { color: #0d6efd; font-weight: 600; border-bottom: 3px solid #0d6efd; }
        .csv-format-table th { background-color: #f8f9fa; font-size: 0.85rem; }
        .csv-format-table td { font-size: 0.85rem; font-family: monospace; }
    </style>
</head>

<body>
    <?php include 'dean_navbar.php'; ?>

    <main class="container py-5">
        <!-- Flash Message Notification -->
        <?php if (isset($_SESSION['flash_message'])): ?>
            <div class="row justify-content-center mb-4">
                <div class="col-12 col-md-10 col-lg-8">
                    <div class="alert alert-<?php echo $_SESSION['flash_type']; ?> alert-dismissible fade show shadow-sm" role="alert">
                        <?php echo $_SESSION['flash_message']; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                </div>
            </div>
            <?php unset($_SESSION['flash_message'], $_SESSION['flash_type']); ?>
        <?php endif; ?>

        <div class="row justify-content-center">
            <div class="col-12 col-md-10 col-lg-8">
                <div class="form-card card p-4 p-md-5">

                    <div class="text-center mb-4">
                        <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-3 py-1.5 rounded-pill uppercase fw-bold tracking-wider mb-2"><?php echo htmlspecialchars($course_name); ?></span>
                        <h2 class="fw-bold text-dark">Assign Subjects to Students</h2>
                        <p class="text-muted small">Link specific database profiles to your assigned university courses manually or via bulk upload.</p>
                    </div>

                    <?php if (empty($subjects_list) && empty($available_semesters)): ?>
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
                            <div class="tab-pane fade show active" id="single-pane" role="tabpanel" aria-labelledby="single-tab" tabindex="0">
                                
                                <!-- Semester Filter Form -->
                                <form method="POST" action="assign_student_subject.php" class="row g-2 align-items-center mb-4 bg-light p-3 rounded-3 border">
                                    <div class="col-12 col-md-4">
                                        <label for="filter_sem" class="form-label fw-semibold text-secondary small mb-1">Filter by Semester:</label>
                                    </div>
                                    <div class="col-12 col-md-6">
                                        <select class="form-select form-select-sm" id="filter_sem" name="filter_sem" onchange="this.form.submit()">
                                            <option value="">-- All Semesters --</option>
                                            <?php foreach ($available_semesters as $sem_val): ?>
                                                <option value="<?= $sem_val ?>" <?= ($selected_sem == $sem_val) ? 'selected' : '' ?>>
                                                    Semester <?= $sem_val ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="col-12 col-md-2">
                                        <?php if (!empty($selected_sem)): ?>
                                            <a href="assign_student_subject.php" onclick="document.getElementById('filter_sem').value=''; this.form.submit();" class="btn btn-sm btn-outline-secondary w-100">Reset</a>
                                        <?php endif; ?>
                                    </div>
                                </form>

                                <form method="POST" action="assign_student_subject.php">
                                    <div class="mb-4">
                                        <label for="student" class="form-label fw-semibold text-secondary small text-uppercase">Select Student Profile:</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-light text-muted"><i class="fa-solid fa-user"></i></span>
                                            <select class="form-select border-start-0 ps-1" id="student" name="student_id" required>
                                                <option value="" disabled selected>Choose a student (Name - Roll/Enroll - Sem)...</option>
                                                <?php
                                                if ($result && mysqli_num_rows($result) > 0) {
                                                    while ($row = mysqli_fetch_assoc($result)) {
                                                        $display_id = !empty(trim($row['roll_number'])) ? 'Roll: ' . $row['roll_number'] : 'Enroll: ' . $row['enrollment_number'];
                                                        echo '<option value="' . htmlspecialchars($row['id']) . '">' 
                                                            . htmlspecialchars($row['name']) 
                                                            . ' — ' . htmlspecialchars($display_id) 
                                                            . ' (Sem ' . htmlspecialchars($row['sem']) . ')</option>';
                                                    }
                                                }
                                                ?>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="mb-4">
                                        <label for="subject" class="form-label fw-semibold text-secondary small text-uppercase">Select Course Subject:</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-light text-muted"><i class="fa-solid fa-book"></i></span>
                                            <select class="form-select border-start-0 ps-1" id="subject" name="subject_name" required>
                                                <option value="" disabled selected>Choose a subject (Subject Name - Sem)...</option>
                                                <?php
                                                if (!empty($subjects_list)) {
                                                    foreach ($subjects_list as $sub_item) {
                                                        echo '<option value="' . htmlspecialchars($sub_item['subject_name']) . '">' 
                                                            . htmlspecialchars($sub_item['subject_name']) 
                                                            . ' (Sem ' . htmlspecialchars($sub_item['semester']) . ')</option>';
                                                    }
                                                }
                                                ?>
                                            </select>
                                        </div>
                                    </div>

                                    <button type="submit" name="assign_subject" class="btn btn-primary w-100 py-2.5 fw-bold shadow-sm">
                                        <i class="fa-solid fa-circle-plus me-2"></i>Confirm Assignment Mapping
                                    </button>
                                </form>
                            </div>

                            <!-- TAB 2: Bulk Assignment -->
                            <div class="tab-pane fade" id="bulk-pane" role="tabpanel" aria-labelledby="bulk-tab" tabindex="0">
                                <div class="alert alert-info small border-info-subtle mb-4 position-relative">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <strong><i class="fa-solid fa-circle-info me-1"></i> CSV Format Instructions:</strong>
                                        <a href="data:text/csv;charset=utf-8,Roll%20Number%2CEnrollment%20Number%2CSubject%20Name%2CSemester%2CYear%0A26CSE001%2C%2CPrinciples%20and%20Practice%20of%20Management%2C4%2C4%0A%2CEN2026001%2CDigital%20Electronics%2C3%2C2"
                                            download="sample_mapping_format.csv"
                                            class="btn btn-sm btn-outline-info bg-white shadow-sm fw-bold">
                                            <i class="fa-solid fa-file-arrow-down me-1"></i> Download Sample CSV
                                        </a>
                                    </div>
                                    Your uploaded file <strong>must</strong> follow the exact column order below (A: Roll Number, B: Enrollment Number, C: Subject Name, D: Semester, E: Year). Fill out whichever identifier is available; leave the other blank.

                                    <div class="table-responsive mt-3">
                                        <table class="table table-bordered table-sm csv-format-table bg-white">
                                            <thead>
                                                <tr>
                                                    <th>A: Roll Number</th>
                                                    <th>B: Enrollment Number</th>
                                                    <th>C: Subject Name</th>
                                                    <th>D: Semester</th>
                                                    <th>E: Year</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td>26CSE001</td>
                                                    <td></td>
                                                    <td>Principles and Practice of Management</td>
                                                    <td>4</td>
                                                    <td>4</td>
                                                </tr>
                                                <tr>
                                                    <td></td>
                                                    <td>EN2026001</td>
                                                    <td>Digital Electronics</td>
                                                    <td>3</td>
                                                    <td>2</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                <form method="POST" action="assign_student_subject.php" enctype="multipart/form-data">
                                    <div class="mb-4">
                                        <label for="excel_file" class="form-label fw-semibold text-secondary small text-uppercase">Upload Mapped CSV File:</label>
                                        <input class="form-control form-control-lg" type="file" id="excel_file" name="excel_file" accept=".csv" required>
                                    </div>

                                    <button type="submit" name="bulk_assign_subject" class="btn btn-success w-100 py-2.5 fw-bold shadow-sm">
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
</body>

</html>
