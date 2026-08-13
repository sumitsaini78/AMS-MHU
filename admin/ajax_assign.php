<?php
include "../db_connect.php";
session_start();

if (!isset($_SESSION['admin_id'])) {
    echo json_encode(["status" => "error", "message" => "Unauthorized"]);
    exit;
}

header('Content-Type: application/json');
$action = $_GET['action'] ?? '';

// Helper to normalize course matching
function getCourseConditions($course_name) {
    global $conn;
    $clean_course = str_replace(['.', ' '], ['', ''], strtoupper(trim($course_name ?? '')));
    $clean_course = str_replace(['(HONS)', 'HONS', '(H)'], ['H', 'H', 'H'], $clean_course);
    $clean_course = str_replace(['(INTEGRATED)', 'INTEGRATED'], ['INT', 'INT'], $clean_course);
    
    if ($clean_course === 'BCOM') {
        $student_cond = "REPLACE(UPPER(course), '.', '') = 'BCOM'";
        $subject_cond = "REPLACE(UPPER(course_name), '.', '') = 'BCOM'";
    } elseif ($clean_course === 'MCOM') {
        $student_cond = "REPLACE(UPPER(course), '.', '') = 'MCOM'";
        $subject_cond = "REPLACE(UPPER(course_name), '.', '') = 'MCOM'";
    } elseif ($clean_course === 'BCOMH') {
        $student_cond = "REPLACE(UPPER(course), '.', '') LIKE 'BCOM%H%'";
        $subject_cond = "REPLACE(UPPER(course_name), '.', '') LIKE 'BCOM%H%'";
    } elseif ($clean_course === 'MBAINT') {
        $student_cond = "REPLACE(UPPER(course), '.', '') LIKE 'MBA%INT%'";
        $subject_cond = "REPLACE(UPPER(course_name), '.', '') LIKE 'MBA%INT%'";
    } else {
        $safe_course = mysqli_real_escape_string($conn, trim($course_name ?? ''));
        $student_cond = "TRIM(UPPER(course)) = UPPER('$safe_course')";
        $subject_cond = "TRIM(UPPER(course_name)) = UPPER('$safe_course')";
    }
    return [$student_cond, $subject_cond];
}

if ($action === 'get_courses') {
    $faculty = $_GET['faculty'] ?? '';
    $stmt = $conn->prepare("SELECT course_name FROM `courses_list` WHERE TRIM(faculty_name) = TRIM(?) ORDER BY course_name ASC");
    $stmt->bind_param("s", $faculty);
    $stmt->execute();
    $res = $stmt->get_result();
    $courses = [];
    while ($row = $res->fetch_assoc()) {
        $courses[] = $row['course_name'];
    }
    echo json_encode(["status" => "success", "courses" => $courses]);
} elseif ($action === 'get_semesters') {
    $course = $_GET['course'] ?? '';
    list($stu_cond, $sub_cond) = getCourseConditions($course);
    
    $stmt = $conn->prepare("SELECT DISTINCT sem AS semester FROM `students` WHERE $stu_cond UNION SELECT DISTINCT semester FROM `subjects` WHERE $sub_cond ORDER BY semester ASC");
    $stmt->execute();
    $res = $stmt->get_result();
    $semesters = [];
    while ($row = $res->fetch_assoc()) {
        if (!empty($row['semester'])) {
            $semesters[] = $row['semester'];
        }
    }
    echo json_encode(["status" => "success", "semesters" => $semesters]);
} elseif ($action === 'get_subjects_and_students') {
    $course = $_GET['course'] ?? '';
    $sem = $_GET['sem'] ?? '';
    list($stu_cond, $sub_cond) = getCourseConditions($course);
    
    // Subjects
    $subject_query = "SELECT DISTINCT subject_name, semester FROM `subjects` WHERE $sub_cond";
    if (!empty($sem)) {
        $subject_query .= " AND semester = ?";
    }
    $subject_query .= " ORDER BY subject_name ASC";
    $stmt_sub = $conn->prepare($subject_query);
    if (!empty($sem)) {
        $stmt_sub->bind_param("s", $sem);
    }
    $stmt_sub->execute();
    $res_sub = $stmt_sub->get_result();
    $subjects = [];
    while ($row = $res_sub->fetch_assoc()) {
        $subjects[] = $row;
    }
    
    // Students
    $student_query = "SELECT id, name, roll_number, enrollment_number, sem FROM `students` WHERE $stu_cond";
    if (!empty($sem)) {
        $student_query .= " AND sem = ?";
    }
    $student_query .= " ORDER BY name ASC";
    $stmt_stu = $conn->prepare($student_query);
    if (!empty($sem)) {
        $stmt_stu->bind_param("s", $sem);
    }
    $stmt_stu->execute();
    $res_stu = $stmt_stu->get_result();
    $students = [];
    while ($row = $res_stu->fetch_assoc()) {
        $students[] = $row;
    }
    
    echo json_encode(["status" => "success", "subjects" => $subjects, "students" => $students]);
} else {
    echo json_encode(["status" => "error", "message" => "Unknown action"]);
}
