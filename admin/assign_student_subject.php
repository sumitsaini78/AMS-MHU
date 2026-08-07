<?php
include "../db_connect.php";
session_start();

// 1. Secure the page
if (!isset($_SESSION['dean_id']) || !isset($_SESSION['dean_name'])) {
    header("Location: ../index.php");
    exit;
} 

$id = $_SESSION['dean_id'];
$teacher_name = $_SESSION['dean_name'];

// Handle Faculty Selection
if (isset($_POST['faculty_name'])) {
    $selected_faculty = $_POST['faculty_name'];
    $_SESSION['selected_faculty'] = $selected_faculty;
    unset($_SESSION['course_name']);
    unset($_SESSION['filter_sem']);
} else {
    $selected_faculty = $_SESSION['selected_faculty'] ?? '';
}

// Handle Course Selection
if (isset($_POST['course_name']) && $_POST['course_name'] !== '') {
    $course_name = $_POST['course_name'];
    $_SESSION['course_name'] = $course_name;
    unset($_SESSION['filter_sem']);
} elseif (isset($_SESSION['course_name'])) {
    $course_name = $_SESSION['course_name'];
} else {
    $course_name = "";
}

// Handle Semester Filter Selection
if (isset($_POST['filter_sem'])) {
    $selected_sem = $_POST['filter_sem'];
    $_SESSION['filter_sem'] = $selected_sem;
} else {
    $selected_sem = $_SESSION['filter_sem'] ?? '';
}

// ==========================================
// 2A. SINGLE ASSIGNMENT PROCESSOR
// ==========================================
if (isset($_POST['assign_subject'])) {
    $student_id = mysqli_real_escape_string($conn, $_POST['student_id']);
    $subject_name = trim($_POST['subject_name']);
    $course_name = $_SESSION['course_name'] ?? '';

    $student_info_query = "SELECT name, roll_number, enrollment_number, faculty, course, year, sem, session FROM `students` WHERE id = '$student_id' AND course = '$course_name'";
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
        $code_query = "SELECT subject_code FROM `subjected_teacher` WHERE TRIM(subject_name) = '$subject_name' AND teacher_id = '$id' LIMIT 1";
        $code_result = mysqli_query($conn, $code_query);
        $subject_code = "";
        
        if ($code_result && mysqli_num_rows($code_result) > 0) {
            $code_data = mysqli_fetch_assoc($code_result);
            $subject_code = mysqli_real_escape_string($conn, $code_data['subject_code']);
        } else {
            $sub_fallback = "SELECT subject_code FROM `subjects` WHERE TRIM(subject_name) = '$subject_name' AND course_name = '$course_name' LIMIT 1";
            $sub_res = mysqli_query($conn, $sub_fallback);
            if ($sub_res && mysqli_num_rows($sub_res) > 0) {
                $sub_data = mysqli_fetch_assoc($sub_res);
                $subject_code = mysqli_real_escape_string($conn, $sub_data['subject_code']);
            }
        }

        $safe_subject_name = mysqli_real_escape_string($conn, $subject_name);

        $check_query = "SELECT id FROM `subjected_student` 
                        WHERE semester = '$s_sem' 
                        AND TRIM(subject_name) = '$safe_subject_name' 
                        AND (
                            ('$s_roll' != '' AND TRIM(roll_number) = '$s_roll') OR 
                            ('$s_enroll' != '' AND TRIM(enrollment_number) = '$s_enroll')
                        )";
        $check_result = mysqli_query($conn, $check_query);

        if (mysqli_num_rows($check_result) > 0) {
            echo "<script>alert('⚠️ This student is already assigned to this subject for this semester!'); window.location.href='assign_student_subject.php';</script>";
            exit;
        } else {
            $insert_query = "INSERT INTO `subjected_student` (student_name, subject_name, subject_code, faculty, course, year, semester, roll_number, enrollment_number, session) 
                             VALUES ('$s_name', '$safe_subject_name', '$subject_code', '$s_faculty', '$s_course', '$s_year', '$s_sem', '$s_roll', '$s_enroll', '$s_session')";

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
        $first_row = true;
        $course_name = $_SESSION['course_name'] ?? '';

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
            if (!empty($course_name)) {
                $student_query .= " AND course = '" . mysqli_real_escape_string($conn, $course_name) . "'";
            }
            
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
                    $sub_fallback = "SELECT subject_code FROM `subjects` WHERE TRIM(subject_name) = '$safe_csv_subject' AND course_name = '$course_name' LIMIT 1";
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
                              )";
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
        echo "<script>alert('✅ Upload complete! Successfully assigned: $success_count. Failed/Skipped: $error_count.'); window.location.href='assign_student_subject.php';</script>";
        exit;
    } else {
        echo "<script>alert('❌ Please select a file first.'); window.location.href='assign_student_subject.php';</script>";
        exit;
    }
}

// Fetch Faculty List
$faculty_list_query = "SELECT DISTINCT faculty_name FROM `courses_list` ORDER BY faculty_name ASC";
$faculty_list_result = mysqli_query($conn, $faculty_list_query);

// Fetch Courses for selected Faculty
$courses_array = [];
if (!empty($selected_faculty)) {
    $stmt = $conn->prepare("SELECT course_name FROM `courses_list` WHERE faculty_name = ? ORDER BY course_name ASC");
    $stmt->bind_param("s", $selected_faculty);
    $stmt->execute();
    $courses_res = $stmt->get_result();
    while ($row = $courses_res->fetch_assoc()) {
        $courses_array[] = $row['course_name'];
    }
}

// Fetch Semesters
$available_semesters = [];
if (!empty($course_name)) {
    $stmt = $conn->prepare("SELECT DISTINCT sem AS semester FROM `students` WHERE course = ? UNION SELECT DISTINCT semester FROM `subjects` WHERE course_name = ? ORDER BY semester ASC");
    $stmt->bind_param("ss", $course_name, $course_name);
    $stmt->execute();
    $sem_res = $stmt->get_result();
    while ($s_row = $sem_res->fetch_assoc()) {
        if (!empty($s_row['semester'])) {
            $available_semesters[] = $s_row['semester'];
        }
    }
}

// Fetch Students
$result = null;
if (!empty($course_name)) {
    $student_query = "SELECT * FROM `students` WHERE course = ?";
    if (!empty($selected_sem)) {
        $student_query .= " AND sem = ?";
    }
    $student_query .= " ORDER BY name ASC";
    
    $stmt = $conn->prepare($student_query);
    if (!empty($selected_sem)) {
        $stmt->bind_param("si", $course_name, $selected_sem);
    } else {
        $stmt->bind_param("s", $course_name);
    }
    $stmt->execute();
    $result = $stmt->get_result();
}

// Fetch Subjects
$subjects_list = [];
if (!empty($course_name)) {
    $subject_query = "SELECT DISTINCT subject_name, semester FROM `subjects` WHERE course_name = ?";
    if (!empty($selected_sem)) {
        $subject_query .= " AND semester = ?";
    }
    $subject_query .= " ORDER BY subject_name ASC";
    
    $stmt = $conn->prepare($subject_query);
    if (!empty($selected_sem)) {
        $stmt->bind_param("si", $course_name, $selected_sem);
    } else {
        $stmt->bind_param("s", $course_name);
    }
    $stmt->execute();
    $subject_result = $stmt->get_result();
    while ($subject_row = $subject_result->fetch_assoc()) {
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

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
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

        .csv-format-table th, .csv-format-table td {
            font-size: 0.85rem;
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
                <div class="d-flex align-items-center gap-2">
                    <span class="navbar-text text-white bg-secondary bg-opacity-25 border border-secondary px-3 py-1.5 rounded-pill small d-none d-lg-inline-flex">
                        <i class="fa-solid fa-user-tie me-2 text-warning"></i> Welcome, <?php echo htmlspecialchars($teacher_name); ?>
                    </span>
                    <a href="index.php" class="btn btn-sm btn-outline-info px-3 shadow-sm"><i class="fa-solid fa-house me-1"></i> Dashboard</a>
                    <a href="../logout.php" class="btn btn-sm btn-danger shadow-sm px-3"><i class="fa-solid fa-power-off me-1"></i> Logout</a>
                </div>
            </div>
        </nav>
    </header>

    <main class="container py-5">
        <div class="row justify-content-center">
            <div class="col-12 col-md-10 col-lg-8">
                <div class="form-card card p-4 p-md-5">

                    <div class="text-center mb-4">
                        <h2 class="fw-bold text-dark">Assign Subjects to Students</h2>
                        <p class="text-muted small">Select Faculty, Course, Subject, and Student sequentially to create assignment mappings.</p>
                    </div>

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
                            
                            <!-- Step 1: Faculty & Course Selection Form -->
                            <form method="POST" action="assign_student_subject.php" class="bg-light p-3 rounded-3 border mb-4">
                                <div class="mb-3">
                                    <label for="faculty_name" class="form-label fw-semibold text-secondary small text-uppercase">1. Select Faculty:</label>
                                    <select class="form-select form-select-sm" id="faculty_name" name="faculty_name" onchange="this.form.submit()" required>
                                        <option value="" disabled selected>-- Choose Faculty --</option>
                                        <?php if ($faculty_list_result): ?>
                                            <?php while ($f_row = mysqli_fetch_assoc($faculty_list_result)): ?>
                                                <option value="<?= htmlspecialchars($f_row['faculty_name']) ?>" <?= ($selected_faculty == $f_row['faculty_name']) ? 'selected' : '' ?>>
                                                    <?= htmlspecialchars($f_row['faculty_name']) ?>
                                                </option>
                                            <?php endwhile; ?>
                                        <?php endif; ?>
                                    </select>
                                </div>

                                <div class="mb-0">
                                    <label for="course_name" class="form-label fw-semibold text-secondary small text-uppercase">2. Select Course:</label>
                                    <select class="form-select form-select-sm" id="course_name" name="course_name" onchange="this.form.submit()" <?= empty($selected_faculty) ? 'disabled' : '' ?> required>
                                        <option value="" disabled selected>-- Choose Course --</option>
                                        <?php foreach ($courses_array as $c_name): ?>
                                            <option value="<?= htmlspecialchars($c_name) ?>" <?= ($course_name == $c_name) ? 'selected' : '' ?>>
                                                <?= htmlspecialchars($c_name) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </form>

                            <?php if (!empty($course_name)): ?>
                                <!-- Optional Semester Filter -->
                                <form method="POST" action="assign_student_subject.php" class="row g-2 align-items-center mb-3 bg-white p-2 rounded-3 border">
                                    <div class="col-12 col-md-4">
                                        <label for="filter_sem" class="form-label fw-semibold text-secondary small mb-0">Filter Sem:</label>
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

                                <!-- Step 3 & 4: Subject, Student & Final Assign Form -->
                                <form method="POST" action="assign_student_subject.php">
                                    <div class="mb-3">
                                        <label for="subject" class="form-label fw-semibold text-secondary small text-uppercase">3. Select Subject:</label>
                                        <select class="form-select" id="subject" name="subject_name" required>
                                            <option value="" disabled selected>Choose a subject...</option>
                                            <?php if (!empty($subjects_list)): ?>
                                                <?php foreach ($subjects_list as $sub_item): ?>
                                                    <option value="<?= htmlspecialchars($sub_item['subject_name']) ?>">
                                                        <?= htmlspecialchars($sub_item['subject_name']) ?> (Sem <?= htmlspecialchars($sub_item['semester']) ?>)
                                                    </option>
                                                <?php endforeach; ?>
                                            <?php else: ?>
                                                <option value="" disabled>No subjects found for this course/semester</option>
                                            <?php endif; ?>
                                        </select>
                                    </div>

                                    <div class="mb-4">
                                        <label for="student" class="form-label fw-semibold text-secondary small text-uppercase">4. Select Student Profile:</label>
                                        <select class="form-select" id="student" name="student_id" required>
                                            <option value="" disabled selected>Choose a student...</option>
                                            <?php if ($result && mysqli_num_rows($result) > 0): ?>
                                                <?php while ($row = mysqli_fetch_assoc($result)): ?>
                                                    <?php $display_id = !empty(trim($row['roll_number'])) ? 'Roll: ' . $row['roll_number'] : 'Enroll: ' . $row['enrollment_number']; ?>
                                                    <option value="<?= htmlspecialchars($row['id']) ?>">
                                                        <?= htmlspecialchars($row['name']) ?> — <?= htmlspecialchars($display_id) ?> (Sem <?= htmlspecialchars($row['sem']) ?>)
                                                    </option>
                                                <?php endwhile; ?>
                                            <?php else: ?>
                                                <option value="" disabled>No students found</option>
                                            <?php endif; ?>
                                        </select>
                                    </div>

                                    <button type="submit" name="assign_subject" class="btn btn-primary w-100 py-2.5 fw-bold shadow-sm">
                                        <i class="fa-solid fa-circle-plus me-2"></i>Confirm Assignment Mapping
                                    </button>
                                </form>
                            <?php else: ?>
                                <div class="alert alert-info text-center small mb-0">
                                    <i class="fa-solid fa-arrow-up me-1"></i> Please select Faculty and Course above to proceed with Subject and Student selection.
                                </div>
                            <?php endif; ?>
                        </div>

                        <!-- TAB 2: Bulk Assignment -->
                        <div class="tab-pane fade" id="bulk-pane" role="tabpanel" aria-labelledby="bulk-tab" tabindex="0">
                            <?php if (!empty($course_name)): ?>
                                <div class="alert alert-info small border-info-subtle mb-4">
                                    <strong><i class="fa-solid fa-circle-info me-1"></i> Selected Course: <?= htmlspecialchars($course_name) ?></strong>
                                    <div class="mt-2">Upload your CSV file containing columns: Roll Number, Enrollment Number, Subject Name, Semester, Year.</div>
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
                            <?php else: ?>
                                <div class="alert alert-warning text-center small mb-0">
                                    Please select a Faculty and Course in the "Single Assign" tab first before performing bulk upload.
                                </div>
                            <?php endif; ?>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </main>

    <footer class="text-center py-4 mt-5 text-muted small bg-white border-top">
        <p class="mb-0">&copy; 2026 Motherhood University Attendance Management System (AMS).</p>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>