<?php
include "../db_connect.php";
session_start();

// 1. Secure the page: Check if the dean is logged in
if (!isset($_SESSION['dean_id']) || !isset($_SESSION['dean_name'])) {
    header("Location: ../index.php");
    exit;
}

$course_name = $_SESSION['course_name'] ?? '';

// 2. Set headers to force download the CSV file
header('Content-Type: text/csv; charset=utf-8');
$filename = "assigned_students_" . (!empty($course_name) ? preg_replace('/[^a-zA-Z0-9]/', '_', $course_name) : "report") . ".csv";
header('Content-Disposition: attachment; filename="' . $filename . '"');

// 3. Open output stream
$output = fopen('php://output', 'w');

// 4. Fetch assigned students data for the active course
$query = "SELECT roll_number, enrollment_number, student_name, subject_name, subject_code, faculty, course, year, semester, session FROM `subjected_student`";
if (!empty($course_name)) {
    $query .= " WHERE course = '" . mysqli_real_escape_string($conn, $course_name) . "'";
}
$query .= " ORDER BY student_name ASC";

$result = mysqli_query($conn, $query);

// 5. Output CSV column headers
fputcsv($output, array('Roll Number', 'Enrollment Number', 'Student Name', 'Subject Name', 'Subject Code', 'Faculty', 'Course', 'Year', 'Semester', 'Session'));

// 6. Loop through rows and output data
if ($result && mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        fputcsv($output, array(
            $row['roll_number'], 
            $row['enrollment_number'], 
            $row['student_name'], 
            $row['subject_name'], 
            $row['subject_code'], 
            $row['faculty'], 
            $row['course'], 
            $row['year'], 
            $row['semester'], 
            $row['session']
        ));
    }
}

// 7. Close output stream
fclose($output);
exit;
?>