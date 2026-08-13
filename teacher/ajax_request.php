<?php
include "../db_connect.php";
if (session_status() === PHP_SESSION_NONE) { session_start(); }

if (!isset($_SESSION['teacher_id'])) {
    echo json_encode(["status" => "error", "message" => "Unauthorized"]);
    exit;
}

header('Content-Type: application/json');
$action = $_GET['action'] ?? '';
$faculty = $_GET['faculty_name'] ?? '';

if (empty($faculty)) {
    echo json_encode(["status" => "error", "message" => "Faculty name missing"]);
    exit;
}

if ($action === 'get_courses') {
    $stmt = $conn->prepare("SELECT DISTINCT course_name FROM courses_list WHERE faculty_name = ? ORDER BY course_name ASC");
    $stmt->bind_param("s", $faculty);
    $stmt->execute();
    $res = $stmt->get_result();
    
    $data = [];
    while ($row = $res->fetch_assoc()) {
        $data[] = ["course_name" => $row['course_name']];
    }
    echo json_encode(["status" => "success", "data" => $data]);
    
} elseif ($action === 'get_years') {
    $course = $_GET['course_name'] ?? '';
    
    $stmt = $conn->prepare("SELECT DISTINCT Year FROM subjects WHERE course_name = ? ORDER BY Year ASC");
    $stmt->bind_param("s", $course);
    $stmt->execute();
    $res = $stmt->get_result();
    
    $data = [];
    while ($row = $res->fetch_assoc()) {
        $data[] = ["Year" => $row['Year']];
    }
    echo json_encode(["status" => "success", "data" => $data]);
    
} elseif ($action === 'get_semesters') {
    $course = $_GET['course_name'] ?? '';
    $year = $_GET['year'] ?? '';
    
    $stmt = $conn->prepare("SELECT DISTINCT semester FROM subjects WHERE course_name = ? AND Year = ? ORDER BY semester ASC");
    $stmt->bind_param("ss", $course, $year);
    $stmt->execute();
    $res = $stmt->get_result();
    
    $data = [];
    while ($row = $res->fetch_assoc()) {
        $data[] = ["semester" => $row['semester']];
    }
    echo json_encode(["status" => "success", "data" => $data]);
    
} elseif ($action === 'get_subjects') {
    $course = $_GET['course_name'] ?? '';
    $year = $_GET['year'] ?? '';
    $sem = $_GET['semester'] ?? '';
    
    $query = "SELECT s.course_id, s.subject_name, s.subject_code, st.teacher_name AS assigned_to 
              FROM subjects s 
              LEFT JOIN subjected_teacher st ON s.course_id = st.sub_id 
              WHERE s.course_name = ? AND s.Year = ? AND s.semester = ? 
              ORDER BY s.subject_name ASC";
    
    $stmt = $conn->prepare($query);
    $stmt->bind_param("sss", $course, $year, $sem);
    $stmt->execute();
    $res = $stmt->get_result();
    
    $data = [];
    while ($row = $res->fetch_assoc()) {
        $data[] = $row;
    }
    echo json_encode(["status" => "success", "data" => $data]);
    
} else {
    echo json_encode(["status" => "error", "message" => "Unknown action"]);
}
