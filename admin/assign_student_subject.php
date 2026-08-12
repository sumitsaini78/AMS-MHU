<?php
include "../db_connect.php";
session_start();

// 1. Secure the page
if (!isset($_SESSION['admin_id']) || !isset($_SESSION['admin_name'])) {
    header("Location: ../index.php");
    exit;
} 

$id = $_SESSION['admin_id'];
$teacher_name = $_SESSION['admin_name'];

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
// 1B. BULK TAB STATE HANDLING
// ==========================================
if (isset($_POST['sample_faculty'])) {
    $_SESSION['sample_faculty'] = $_POST['sample_faculty'];
    unset($_SESSION['sample_course']);
}
$sample_faculty = $_SESSION['sample_faculty'] ?? '';

if (isset($_POST['sample_course']) && $_POST['sample_course'] !== '') {
    $_SESSION['sample_course'] = $_POST['sample_course'];
}
$sample_course = $_SESSION['sample_course'] ?? '';

// Normalize course matching for inconsistencies between tables
$clean_course = str_replace(['.', ' '], ['', ''], strtoupper(trim($course_name ?? '')));
$clean_course = str_replace(['(HONS)', 'HONS', '(H)'], ['H', 'H', 'H'], $clean_course);
$clean_course = str_replace(['(INTEGRATED)', 'INTEGRATED'], ['INT', 'INT'], $clean_course);

if ($clean_course === 'BCOM') {
    $student_course_cond = "REPLACE(UPPER(course), '.', '') = 'BCOM'";
    $subject_course_cond = "REPLACE(UPPER(course_name), '.', '') = 'BCOM'";
} elseif ($clean_course === 'MCOM') {
    $student_course_cond = "REPLACE(UPPER(course), '.', '') = 'MCOM'";
    $subject_course_cond = "REPLACE(UPPER(course_name), '.', '') = 'MCOM'";
} elseif ($clean_course === 'BCOMH') {
    $student_course_cond = "REPLACE(UPPER(course), '.', '') LIKE 'BCOM%H%'";
    $subject_course_cond = "REPLACE(UPPER(course_name), '.', '') LIKE 'BCOM%H%'";
} elseif ($clean_course === 'MBAINT') {
    $student_course_cond = "REPLACE(UPPER(course), '.', '') LIKE 'MBA%INT%'";
    $subject_course_cond = "REPLACE(UPPER(course_name), '.', '') LIKE 'MBA%INT%'";
} else {
    $safe_course = mysqli_real_escape_string($conn, $course_name ?? '');
    $student_course_cond = "TRIM(UPPER(course)) = UPPER('$safe_course')";
    $subject_course_cond = "TRIM(UPPER(course_name)) = UPPER('$safe_course')";
}

// ==========================================
// 2A. SAMPLE CSV GENERATOR
// ==========================================
if (isset($_POST['download_sample_csv'])) {
    if (empty($sample_course)) {
        echo "<script>alert('Please select a Course first.'); window.location.href='assign_student_subject.php';</script>";
        exit;
    }
    
    $req_sem = isset($_POST['sample_sem']) ? (int)$_POST['sample_sem'] : 0;
    $req_year = isset($_POST['sample_year']) ? (int)$_POST['sample_year'] : 1;

    // Normalize course matching for inconsistencies between tables for the bulk export
    $clean_sample = str_replace(['.', ' '], ['', ''], strtoupper(trim($sample_course ?? '')));
    $clean_sample = str_replace(['(HONS)', 'HONS', '(H)'], ['H', 'H', 'H'], $clean_sample);
    $clean_sample = str_replace(['(INTEGRATED)', 'INTEGRATED'], ['INT', 'INT'], $clean_sample);

    if ($clean_sample === 'BCOM') {
        $sample_course_cond = "REPLACE(UPPER(course), '.', '') = 'BCOM'";
        $sample_subject_cond = "REPLACE(UPPER(course_name), '.', '') = 'BCOM'";
    } elseif ($clean_sample === 'MCOM') {
        $sample_course_cond = "REPLACE(UPPER(course), '.', '') = 'MCOM'";
        $sample_subject_cond = "REPLACE(UPPER(course_name), '.', '') = 'MCOM'";
    } elseif ($clean_sample === 'BCOMH') {
        $sample_course_cond = "REPLACE(UPPER(course), '.', '') LIKE 'BCOM%H%'";
        $sample_subject_cond = "REPLACE(UPPER(course_name), '.', '') LIKE 'BCOM%H%'";
    } elseif ($clean_sample === 'MBAINT') {
        $sample_course_cond = "REPLACE(UPPER(course), '.', '') LIKE 'MBA%INT%'";
        $sample_subject_cond = "REPLACE(UPPER(course_name), '.', '') LIKE 'MBA%INT%'";
    } else {
        $safe_sample = mysqli_real_escape_string($conn, $sample_course ?? '');
        $sample_course_cond = "TRIM(UPPER(course)) = UPPER('$safe_sample')";
        $sample_subject_cond = "TRIM(UPPER(course_name)) = UPPER('$safe_sample')";
    }
    
    // Fetch students
    $students = [];
    $student_query = "SELECT name, roll_number, enrollment_number FROM `students` WHERE $sample_course_cond";
    if ($req_sem > 0) {
        $student_query .= " AND sem = ?";
    }
    $student_query .= " ORDER BY name ASC";
    $stmt_stu = $conn->prepare($student_query);
    if ($req_sem > 0) {
        $stmt_stu->bind_param("i", $req_sem);
    }
    $stmt_stu->execute();
    $res_stu = $stmt_stu->get_result();
    while ($row = $res_stu->fetch_assoc()) {
        $students[] = $row;
    }
    
    // Fetch subjects
    $subjects = [];
    $subject_query = "SELECT DISTINCT subject_name FROM `subjects` WHERE $sample_subject_cond";
    if ($req_sem > 0) {
        $subject_query .= " AND semester = ?";
    }
    $subject_query .= " ORDER BY subject_name ASC";
    $stmt_sub = $conn->prepare($subject_query);
    if ($req_sem > 0) {
        $stmt_sub->bind_param("i", $req_sem);
    }
    $stmt_sub->execute();
    $res_sub = $stmt_sub->get_result();
    while ($row = $res_sub->fetch_assoc()) {
        $subjects[] = $row['subject_name'];
    }
    
    // Generate CSV
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename=sample_bulk_assign_' . preg_replace('/[^a-zA-Z0-9]/', '_', $sample_course) . '_sem' . $req_sem . '.csv');
    
    $output = fopen('php://output', 'w');
    fputcsv($output, ['Roll Number', 'Enrollment Number', 'Subject Name', 'Semester', 'Year']);
    
    foreach ($students as $student) {
        if (!empty($subjects)) {
            foreach ($subjects as $subject) {
                fputcsv($output, [
                    $student['roll_number'],
                    $student['enrollment_number'],
                    $subject,
                    $req_sem,
                    $req_year
                ]);
            }
        } else {
            // Output the student even if there are no subjects found
            fputcsv($output, [
                $student['roll_number'],
                $student['enrollment_number'],
                '',
                $req_sem,
                $req_year
            ]);
        }
    }
    fclose($output);
    exit;
}

// ==========================================
// 2B. SINGLE ASSIGNMENT PROCESSOR
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
            $sub_fallback = "SELECT subject_code FROM `subjects` WHERE TRIM(subject_name) = '$subject_name' AND $subject_course_cond LIMIT 1";
            $sub_res = mysqli_query($conn, $sub_fallback);
            if ($sub_res && mysqli_num_rows($sub_res) > 0) {
                $sub_data = mysqli_fetch_assoc($sub_res);
                $subject_code = mysqli_real_escape_string($conn, $sub_data['subject_code']);
            }
        }

        if (empty($subject_code)) {
            echo "<script>alert('❌ Wrong Subject: " . addslashes($subject_name) . " was not found for this course.'); window.location.href='assign_student_subject.php';</script>";
            exit;
        }

        $safe_subject_name = mysqli_real_escape_string($conn, $subject_name);

        $check_query = "SELECT id FROM `subjected_student` 
                        WHERE semester = '$s_sem' 
                        AND year = '$s_year'
                        AND course = '$s_course'
                        AND session = '$s_session'
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
                    $sub_fallback = "SELECT subject_code FROM `subjects` WHERE TRIM(subject_name) = '$safe_csv_subject' AND $subject_course_cond LIMIT 1";
                    $sub_res = mysqli_query($conn, $sub_fallback);
                    if ($sub_res && mysqli_num_rows($sub_res) > 0) {
                        $sub_data = mysqli_fetch_assoc($sub_res);
                        $subject_code = mysqli_real_escape_string($conn, $sub_data['subject_code']);
                    }
                }

                if (empty($subject_code)) {
                    fclose($handle);
                    echo "<script>alert('❌ Wrong Subject found in CSV: " . addslashes($csv_subject) . "'); window.location.href='assign_student_subject.php';</script>";
                    exit;
                }

                $safe_course_dup = mysqli_real_escape_string($conn, $student['course']);
                $check_dup = "SELECT id FROM `subjected_student` 
                              WHERE semester = '$final_sem' 
                              AND year = '$final_year'
                              AND course = '$safe_course_dup'
                              AND session = '$student_session'
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
$faculty_list_query = "SELECT DISTINCT faculty_full_name AS faculty_name FROM `faculty` ORDER BY faculty_name ASC";
$faculty_list_result = mysqli_query($conn, $faculty_list_query);

// Fetch Faculty List specifically for Sample CSV (since we need to re-iterate)
$sample_faculty_list_result = mysqli_query($conn, $faculty_list_query);

// Fetch Courses for selected Faculty
$courses_array = [];
if (!empty($selected_faculty)) {
    $stmt = $conn->prepare("SELECT course_name FROM `courses_list` WHERE TRIM(faculty_name) = TRIM(?) ORDER BY course_name ASC");
    $stmt->bind_param("s", $selected_faculty);
    $stmt->execute();
    $courses_res = $stmt->get_result();
    while ($row = $courses_res->fetch_assoc()) {
        $courses_array[] = $row['course_name'];
    }
}

// Fetch Courses for selected Sample Faculty
$sample_courses_array = [];
if (!empty($sample_faculty)) {
    $stmt = $conn->prepare("SELECT course_name FROM `courses_list` WHERE TRIM(faculty_name) = TRIM(?) ORDER BY course_name ASC");
    $stmt->bind_param("s", $sample_faculty);
    $stmt->execute();
    $courses_res = $stmt->get_result();
    while ($row = $courses_res->fetch_assoc()) {
        $sample_courses_array[] = $row['course_name'];
    }
}

// Normalize course matching for inconsistencies between tables
$clean_course = str_replace(['.', ' '], ['', ''], strtoupper(trim($course_name ?? '')));
$clean_course = str_replace(['(HONS)', 'HONS', '(H)'], ['H', 'H', 'H'], $clean_course);
$clean_course = str_replace(['(INTEGRATED)', 'INTEGRATED'], ['INT', 'INT'], $clean_course);

if ($clean_course === 'BCOM') {
    $student_course_cond = "REPLACE(UPPER(course), '.', '') = 'BCOM'";
    $subject_course_cond = "REPLACE(UPPER(course_name), '.', '') = 'BCOM'";
} elseif ($clean_course === 'MCOM') {
    $student_course_cond = "REPLACE(UPPER(course), '.', '') = 'MCOM'";
    $subject_course_cond = "REPLACE(UPPER(course_name), '.', '') = 'MCOM'";
} elseif ($clean_course === 'BCOMH') {
    $student_course_cond = "REPLACE(UPPER(course), '.', '') LIKE 'BCOM%H%'";
    $subject_course_cond = "REPLACE(UPPER(course_name), '.', '') LIKE 'BCOM%H%'";
} elseif ($clean_course === 'MBAINT') {
    $student_course_cond = "REPLACE(UPPER(course), '.', '') LIKE 'MBA%INT%'";
    $subject_course_cond = "REPLACE(UPPER(course_name), '.', '') LIKE 'MBA%INT%'";
} else {
    $safe_course = mysqli_real_escape_string($conn, $course_name ?? '');
    $student_course_cond = "TRIM(UPPER(course)) = UPPER('$safe_course')";
    $subject_course_cond = "TRIM(UPPER(course_name)) = UPPER('$safe_course')";
}

// Fetch Semesters for Single Assign
$available_semesters = [];
if (!empty($course_name)) {
    $stmt = $conn->prepare("SELECT DISTINCT sem AS semester FROM `students` WHERE $student_course_cond UNION SELECT DISTINCT semester FROM `subjects` WHERE $subject_course_cond ORDER BY semester ASC");
    $stmt->execute();
    $sem_res = $stmt->get_result();
    while ($s_row = $sem_res->fetch_assoc()) {
        if (!empty($s_row['semester'])) {
            $available_semesters[] = $s_row['semester'];
        }
    }
}

// Fetch Semesters for Sample Export
$sample_available_semesters = [];
if (!empty($sample_course)) {
    // We can reuse the $sample_course_cond built in the download interceptor
    // But since it exited, we rebuild it for the view rendering
    $clean_sample = str_replace(['.', ' '], ['', ''], strtoupper(trim($sample_course ?? '')));
    $clean_sample = str_replace(['(HONS)', 'HONS', '(H)'], ['H', 'H', 'H'], $clean_sample);
    $clean_sample = str_replace(['(INTEGRATED)', 'INTEGRATED'], ['INT', 'INT'], $clean_sample);

    if ($clean_sample === 'BCOM') {
        $sample_course_cond_v = "REPLACE(UPPER(course), '.', '') = 'BCOM'";
        $sample_subject_cond_v = "REPLACE(UPPER(course_name), '.', '') = 'BCOM'";
    } elseif ($clean_sample === 'MCOM') {
        $sample_course_cond_v = "REPLACE(UPPER(course), '.', '') = 'MCOM'";
        $sample_subject_cond_v = "REPLACE(UPPER(course_name), '.', '') = 'MCOM'";
    } elseif ($clean_sample === 'BCOMH') {
        $sample_course_cond_v = "REPLACE(UPPER(course), '.', '') LIKE 'BCOM%H%'";
        $sample_subject_cond_v = "REPLACE(UPPER(course_name), '.', '') LIKE 'BCOM%H%'";
    } elseif ($clean_sample === 'MBAINT') {
        $sample_course_cond_v = "REPLACE(UPPER(course), '.', '') LIKE 'MBA%INT%'";
        $sample_subject_cond_v = "REPLACE(UPPER(course_name), '.', '') LIKE 'MBA%INT%'";
    } else {
        $safe_sample = mysqli_real_escape_string($conn, $sample_course ?? '');
        $sample_course_cond_v = "TRIM(UPPER(course)) = UPPER('$safe_sample')";
        $sample_subject_cond_v = "TRIM(UPPER(course_name)) = UPPER('$safe_sample')";
    }

    $stmt = $conn->prepare("SELECT DISTINCT sem AS semester FROM `students` WHERE $sample_course_cond_v UNION SELECT DISTINCT semester FROM `subjects` WHERE $sample_subject_cond_v ORDER BY semester ASC");
    $stmt->execute();
    $sem_res = $stmt->get_result();
    while ($s_row = $sem_res->fetch_assoc()) {
        if (!empty($s_row['semester'])) {
            $sample_available_semesters[] = $s_row['semester'];
        }
    }
}

// Fetch Students
$result = null;
if (!empty($course_name)) {
    $student_query = "SELECT * FROM `students` WHERE $student_course_cond";
    if (!empty($selected_sem)) {
        $student_query .= " AND sem = ?";
    }
    $student_query .= " ORDER BY name ASC";
    
    $stmt = $conn->prepare($student_query);
    if (!empty($selected_sem)) {
        $stmt->bind_param("s", $selected_sem);
    }
    $stmt->execute();
    $result = $stmt->get_result();
}

// Fetch Subjects
$subjects_list = [];
if (!empty($course_name)) {
    $subject_query = "SELECT DISTINCT subject_name, semester FROM `subjects` WHERE $subject_course_cond";
    if (!empty($selected_sem)) {
        $subject_query .= " AND semester = ?";
    }
    $subject_query .= " ORDER BY subject_name ASC";
    
    $stmt = $conn->prepare($subject_query);
    if (!empty($selected_sem)) {
        $stmt->bind_param("s", $selected_sem);
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
                            <!-- Generate Sample CSV Box -->
                            <div class="card border-primary mb-4 shadow-sm">
                                <div class="card-header bg-primary text-white fw-bold">
                                    <i class="fa-solid fa-file-csv me-2"></i>Generate Sample Data (Cartesian Product)
                                </div>
                                <div class="card-body bg-light">
                                    <p class="small text-muted mb-3">Download a ready-to-upload CSV file mapped to your specific course structure. Just delete any extra rows before uploading!</p>
                                    
                                    <form method="POST" action="assign_student_subject.php" class="mb-3 border-bottom pb-3">
                                        <div class="row g-2">
                                            <div class="col-md-6">
                                                <label class="form-label small fw-bold">1. Select Faculty:</label>
                                                <select class="form-select form-select-sm" name="sample_faculty" onchange="this.form.submit()" required>
                                                    <option value="" disabled selected>-- Choose Faculty --</option>
                                                    <?php if ($sample_faculty_list_result): ?>
                                                        <?php while ($f_row = mysqli_fetch_assoc($sample_faculty_list_result)): ?>
                                                            <option value="<?= htmlspecialchars($f_row['faculty_name']) ?>" <?= ($sample_faculty == $f_row['faculty_name']) ? 'selected' : '' ?>>
                                                                <?= htmlspecialchars($f_row['faculty_name']) ?>
                                                            </option>
                                                        <?php endwhile; ?>
                                                    <?php endif; ?>
                                                </select>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label small fw-bold">2. Select Course:</label>
                                                <select class="form-select form-select-sm" name="sample_course" onchange="this.form.submit()" <?= empty($sample_faculty) ? 'disabled' : '' ?> required>
                                                    <option value="" disabled selected>-- Choose Course --</option>
                                                    <?php foreach ($sample_courses_array as $c_name): ?>
                                                        <option value="<?= htmlspecialchars($c_name) ?>" <?= ($sample_course == $c_name) ? 'selected' : '' ?>>
                                                            <?= htmlspecialchars($c_name) ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                        </div>
                                    </form>

                                    <form method="POST" action="assign_student_subject.php">
                                        <div class="row g-2 mb-3">
                                            <div class="col-md-6">
                                                <label class="form-label small fw-bold">3. Semester:</label>
                                                <select class="form-select form-select-sm" name="sample_sem" <?= empty($sample_course) ? 'disabled' : '' ?> required>
                                                    <option value="" disabled selected>-- Select Semester --</option>
                                                    <?php foreach ($sample_available_semesters as $sem_val): ?>
                                                        <option value="<?= $sem_val ?>">Semester <?= $sem_val ?></option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label small fw-bold">4. Academic Year:</label>
                                                <select class="form-select form-select-sm" name="sample_year" <?= empty($sample_course) ? 'disabled' : '' ?> required>
                                                    <option value="1">Year 1</option>
                                                    <option value="2">Year 2</option>
                                                    <option value="3">Year 3</option>
                                                    <option value="4">Year 4</option>
                                                    <option value="5">Year 5</option>
                                                </select>
                                            </div>
                                        </div>
                                        <button type="submit" name="download_sample_csv" class="btn btn-primary btn-sm w-100 fw-semibold" <?= empty($sample_course) ? 'disabled' : '' ?>>
                                            <i class="fa-solid fa-download me-1"></i> Download Sample CSV
                                        </button>
                                    </form>
                                </div>
                            </div>
                            
                            <hr class="mb-4">

                            <?php if (!empty($course_name)): ?>
                                <div class="alert alert-info small border-info-subtle mb-4">
                                    <strong><i class="fa-solid fa-circle-info me-1"></i> Ready for Upload: <?= htmlspecialchars($course_name) ?></strong>
                                    <div class="mt-2">Uploading for the course selected in the Single Assign tab. Required columns: Roll Number, Enrollment Number, Subject Name, Semester, Year.</div>
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
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // Restore active tab from localStorage
            let activeTabId = localStorage.getItem('assign_student_active_tab');
            if (activeTabId) {
                let triggerEl = document.querySelector(`button[data-bs-target="${activeTabId}"]`);
                if (triggerEl) {
                    let tabInstance = new bootstrap.Tab(triggerEl);
                    tabInstance.show();
                }
            }

            // Save active tab to localStorage on click
            let tabButtons = document.querySelectorAll('button[data-bs-toggle="tab"]');
            tabButtons.forEach(function(btn) {
                btn.addEventListener('shown.bs.tab', function (event) {
                    localStorage.setItem('assign_student_active_tab', event.target.getAttribute('data-bs-target'));
                });
            });
        });
    </script>
</body>

</html>