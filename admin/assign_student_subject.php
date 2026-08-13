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
    $safe_course = mysqli_real_escape_string($conn, trim($course_name ?? ''));
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
    $req_year = ceil($req_sem / 2);
    if ($req_year == 0) $req_year = 1;

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
        $safe_sample = mysqli_real_escape_string($conn, trim($sample_course ?? ''));
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
    $subject_query = "SELECT DISTINCT subject_name, semester FROM `subjects` WHERE $sample_subject_cond";
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
    fputcsv($output, ['Roll Number', 'Enrollment Number', 'Subject Name', 'Semester']);
    
    foreach ($students as $student) {
        if (!empty($subjects)) {
            foreach ($subjects as $subject) {
                fputcsv($output, [  
                    $student['roll_number'],
                    $student['enrollment_number'],
                    $subject,
                    $req_sem
                ]);
            }
        } else {
            // Output the student even if there are no subjects found
            fputcsv($output, [
                $student['roll_number'],
                $student['enrollment_number'],
                '',
                $req_sem
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
    $course_name = trim($_POST['course_name']);

    $student_info_query = "SELECT name, roll_number, enrollment_number, faculty, course, year, sem, session FROM `students` WHERE id = '$student_id' AND course = '$course_name'";
    $student_info_result = mysqli_query($conn, $student_info_query);

    if ($student_info_result && mysqli_num_rows($student_info_result) > 0) {
        $student_data = mysqli_fetch_assoc($student_info_result);
        $s_name = mysqli_real_escape_string($conn, $student_data['name']);
        
        $s_roll = mysqli_real_escape_string($conn, trim($student_data['roll_number']));
        $s_enroll = mysqli_real_escape_string($conn, trim($student_data['enrollment_number']));

        $s_faculty = mysqli_real_escape_string($conn, $student_data['faculty']);
        $s_course = mysqli_real_escape_string($conn, $student_data['course']);
        $s_sem = (int) $student_data['sem'];
        $s_year = ceil($s_sem / 2);
        if ($s_year == 0) $s_year = (int) $student_data['year'];
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
        $course_name = trim($_POST['bulk_course_name'] ?? '');

        // Recalculate subject course conditions based on bulk upload dropdown
        $clean_bulk_course = str_replace(['.', ' '], ['', ''], strtoupper($course_name));
        $clean_bulk_course = str_replace(['(HONS)', 'HONS', '(H)'], ['H', 'H', 'H'], $clean_bulk_course);
        $clean_bulk_course = str_replace(['(INTEGRATED)', 'INTEGRATED'], ['INT', 'INT'], $clean_bulk_course);

        if ($clean_bulk_course === 'BCOM') {
            $subject_course_cond = "REPLACE(UPPER(course_name), '.', '') = 'BCOM'";
        } elseif ($clean_bulk_course === 'MCOM') {
            $subject_course_cond = "REPLACE(UPPER(course_name), '.', '') = 'MCOM'";
        } elseif ($clean_bulk_course === 'BCOMH') {
            $subject_course_cond = "REPLACE(UPPER(course_name), '.', '') LIKE 'BCOM%H%'";
        } elseif ($clean_bulk_course === 'MBAINT') {
            $subject_course_cond = "REPLACE(UPPER(course_name), '.', '') LIKE 'MBA%INT%'";
        } else {
            $safe_bulk = mysqli_real_escape_string($conn, $course_name);
            $subject_course_cond = "TRIM(UPPER(course_name)) = UPPER('$safe_bulk')";
        }

        while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
            if ($first_row || (empty(trim($data[0])) && empty(trim($data[1])))) {
                $first_row = false;
                continue;
            }

            $csv_roll = mysqli_real_escape_string($conn, trim($data[0]));
            $csv_enroll = mysqli_real_escape_string($conn, trim($data[1]));
            $csv_subject = isset($data[2]) ? trim($data[2]) : '';
            $csv_semester = isset($data[3]) ? (int) trim($data[3]) : 0;

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
                $final_year = ceil($final_sem / 2);
                if ($final_year == 0) $final_year = (int) $student['year'];
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
    <?php include 'admin_navbar.php'; ?>

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
                            
                            <form method="POST" action="assign_student_subject.php" id="singleAssignForm">
                                <!-- Step 1 & 2 -->
                                <div class="bg-light p-3 rounded-3 border mb-4">
                                    <div class="mb-3">
                                        <label for="faculty_name" class="form-label fw-semibold text-secondary small text-uppercase">1. Select Faculty:</label>
                                        <select class="form-select form-select-sm" id="faculty_name" name="faculty_name" required>
                                            <option value="" disabled selected>-- Choose Faculty --</option>
                                            <?php if ($faculty_list_result): mysqli_data_seek($faculty_list_result, 0); ?>
                                                <?php while ($f_row = mysqli_fetch_assoc($faculty_list_result)): ?>
                                                    <option value="<?= htmlspecialchars($f_row['faculty_name']) ?>"><?= htmlspecialchars($f_row['faculty_name']) ?></option>
                                                <?php endwhile; ?>
                                            <?php endif; ?>
                                        </select>
                                    </div>
                                    <div class="mb-0">
                                        <label for="course_name" class="form-label fw-semibold text-secondary small text-uppercase">2. Select Course:</label>
                                        <select class="form-select form-select-sm" id="course_name" name="course_name" disabled required>
                                            <option value="" disabled selected>-- Choose Course --</option>
                                        </select>
                                    </div>
                                </div>
                                
                                <!-- Step 3 & 4 Filter -->
                                <div class="row g-2 align-items-center mb-3 bg-white p-2 rounded-3 border">
                                    <div class="col-12 col-md-4">
                                        <label for="filter_sem" class="form-label fw-semibold text-secondary small mb-0">Filter Sem:</label>
                                    </div>
                                    <div class="col-12 col-md-8">
                                        <select class="form-select form-select-sm" id="filter_sem" name="filter_sem">
                                            <option value="">-- All Semesters --</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="subject" class="form-label fw-semibold text-secondary small text-uppercase">3. Select Subject:</label>
                                    <select class="form-select" id="subject" name="subject_name" required>
                                        <option value="" disabled selected>Choose a subject...</option>
                                    </select>
                                </div>

                                <div class="mb-4">
                                    <label for="student" class="form-label fw-semibold text-secondary small text-uppercase">4. Select Student Profile:</label>
                                    <select class="form-select" id="student" name="student_id" required>
                                        <option value="" disabled selected>Choose a student...</option>
                                    </select>
                                </div>

                                <button type="submit" name="assign_subject" class="btn btn-primary w-100 py-2.5 fw-bold shadow-sm">
                                    <i class="fa-solid fa-circle-plus me-2"></i>Confirm Assignment Mapping
                                </button>
                            </form>
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
                                                <select class="form-select form-select-sm" id="sample_faculty" name="sample_faculty" required>
                                                    <option value="" disabled selected>-- Choose Faculty --</option>
                                                    <?php if ($sample_faculty_list_result): mysqli_data_seek($sample_faculty_list_result, 0); ?>
                                                        <?php while ($f_row = mysqli_fetch_assoc($sample_faculty_list_result)): ?>
                                                            <option value="<?= htmlspecialchars($f_row['faculty_name']) ?>"><?= htmlspecialchars($f_row['faculty_name']) ?></option>
                                                        <?php endwhile; ?>
                                                    <?php endif; ?>
                                                </select>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label small fw-bold">2. Select Course:</label>
                                                <select class="form-select form-select-sm" id="sample_course" name="sample_course" disabled required>
                                                    <option value="" disabled selected>-- Choose Course --</option>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="row g-2 mt-2 mb-3">
                                            <div class="col-md-12">
                                                <label class="form-label small fw-bold">3. Semester:</label>
                                                <select class="form-select form-select-sm" id="sample_sem" name="sample_sem" disabled required>
                                                    <option value="" disabled selected>-- Select Semester --</option>
                                                </select>
                                            </div>
                                        </div>
                                        <button type="submit" name="download_sample_csv" id="btn_sample_csv" class="btn btn-primary btn-sm w-100 fw-semibold" disabled>
                                            <i class="fa-solid fa-download me-1"></i> Download Sample CSV
                                        </button>
                                    </form>
                                </div>
                            </div>
                            
                            <hr class="mb-4">

                            <div class="alert alert-info small border-info-subtle mb-4">
                                <strong><i class="fa-solid fa-circle-info me-1"></i> Ready for Upload</strong>
                                <div class="mt-2">Required columns: Roll Number, Enrollment Number, Subject Name, Semester. Make sure the subjects match the ones available for the course.</div>
                            </div>
                            <form method="POST" action="assign_student_subject.php" enctype="multipart/form-data">
                                <div class="mb-3">
                                    <label class="form-label fw-semibold text-secondary small">Course (for which you are uploading):</label>
                                    <select class="form-select form-select-sm" id="bulk_course_name" name="bulk_course_name" required>
                                        <option value="" disabled selected>-- Select Course --</option>
                                        <!-- Populated via the same faculty-course AJAX logically or explicitly on form -->
                                    </select>
                                </div>
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

            // AJAX Handlers
            const singleFaculty = document.getElementById('faculty_name');
            const singleCourse = document.getElementById('course_name');
            const singleSem = document.getElementById('filter_sem');
            const singleSubject = document.getElementById('subject');
            const singleStudent = document.getElementById('student');

            const bulkFaculty = document.getElementById('sample_faculty');
            const bulkCourse = document.getElementById('sample_course');
            const bulkSem = document.getElementById('sample_sem');
            
            const bulkUploadCourse = document.getElementById('bulk_course_name');

            function populateCourses(facultyVal, targetCourseElem, callback) {
                if(!facultyVal) return;
                fetch('ajax_assign.php?action=get_courses&faculty=' + encodeURIComponent(facultyVal))
                    .then(res => res.json())
                    .then(data => {
                        targetCourseElem.innerHTML = '<option value="" disabled selected>-- Choose Course --</option>';
                        targetCourseElem.disabled = false;
                        if(data.status === 'success') {
                            data.courses.forEach(c => {
                                targetCourseElem.innerHTML += `<option value="${c}">${c}</option>`;
                            });
                            // Also sync bulk upload course dropdown to keep it updated globally
                            if (targetCourseElem === bulkCourse || targetCourseElem === singleCourse) {
                                bulkUploadCourse.innerHTML = targetCourseElem.innerHTML;
                                bulkUploadCourse.disabled = false;
                            }
                        }
                        if(callback) callback();
                    });
            }

            // Faculty Change (Single)
            if(singleFaculty) {
                singleFaculty.addEventListener('change', function() {
                    populateCourses(this.value, singleCourse, () => {
                        singleSem.innerHTML = '<option value="">-- All Semesters --</option>';
                        singleSubject.innerHTML = '<option value="" disabled selected>Choose a subject...</option>';
                        singleStudent.innerHTML = '<option value="" disabled selected>Choose a student...</option>';
                    });
                });
            }

            // Course Change (Single)
            if(singleCourse) {
                singleCourse.addEventListener('change', function() {
                    fetch('ajax_assign.php?action=get_semesters&course=' + encodeURIComponent(this.value))
                        .then(res => res.json())
                        .then(data => {
                            singleSem.innerHTML = '<option value="">-- All Semesters --</option>';
                            if(data.status === 'success') {
                                data.semesters.forEach(s => {
                                    singleSem.innerHTML += `<option value="${s}">Semester ${s}</option>`;
                                });
                            }
                            loadSubjectsAndStudents();
                        });
                });
            }

            // Semester Change (Single)
            if(singleSem) {
                singleSem.addEventListener('change', loadSubjectsAndStudents);
            }

            function loadSubjectsAndStudents() {
                const course = singleCourse.value;
                const sem = singleSem.value;
                if (!course) return;
                fetch(`ajax_assign.php?action=get_subjects_and_students&course=${encodeURIComponent(course)}&sem=${encodeURIComponent(sem)}`)
                    .then(res => res.json())
                    .then(data => {
                        singleSubject.innerHTML = '<option value="" disabled selected>Choose a subject...</option>';
                        singleStudent.innerHTML = '<option value="" disabled selected>Choose a student...</option>';
                        if(data.status === 'success') {
                            if(data.subjects.length > 0) {
                                data.subjects.forEach(sub => {
                                    singleSubject.innerHTML += `<option value="${sub.subject_name}">${sub.subject_name} (Sem ${sub.semester})</option>`;
                                });
                            } else {
                                singleSubject.innerHTML = '<option value="" disabled>No subjects found for this course/semester</option>';
                            }
                            if(data.students.length > 0) {
                                data.students.forEach(st => {
                                    let display = (st.roll_number && st.roll_number.trim() !== '') ? 'Roll: ' + st.roll_number : 'Enroll: ' + st.enrollment_number;
                                    singleStudent.innerHTML += `<option value="${st.id}">${st.name} — ${display} (Sem ${st.sem})</option>`;
                                });
                            } else {
                                singleStudent.innerHTML = '<option value="" disabled>No students found</option>';
                            }
                        }
                    });
            }

            // Bulk assign handlers
            if(bulkFaculty) {
                bulkFaculty.addEventListener('change', function() {
                    populateCourses(this.value, bulkCourse, () => {
                        if(bulkSem) bulkSem.innerHTML = '<option value="" disabled selected>-- Select Semester --</option>';
                    });
                });

                bulkCourse.addEventListener('change', function() {
                    fetch('ajax_assign.php?action=get_semesters&course=' + encodeURIComponent(this.value))
                        .then(res => res.json())
                        .then(data => {
                            if(bulkSem) {
                                bulkSem.innerHTML = '<option value="" disabled selected>-- Select Semester --</option>';
                                bulkSem.disabled = false;
                                if(data.status === 'success') {
                                    data.semesters.forEach(s => {
                                        bulkSem.innerHTML += `<option value="${s}">Semester ${s}</option>`;
                                    });
                                }
                            }
                            const btn = document.getElementById('btn_sample_csv');
                            if(btn) btn.disabled = false;
                        });
                });
            }
        });
    </script>
</body>

</html>
