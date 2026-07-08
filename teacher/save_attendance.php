<?php
include "../db_connect.php";
session_start();

// Secure layout filter restriction
if (!isset($_SESSION['teacher_id'])) {
    header("Location: ../index.php");
    exit;
}

if (isset($_POST['insert_attendance'])) {
    // Escaping main context strings
    $subject_name = mysqli_real_escape_string($conn, $_POST['subject_name']);
    $subject_code = mysqli_real_escape_string($conn, $_POST['subject_code']);
    $course       = mysqli_real_escape_string($conn, $_POST['course_name']);
    $year         = mysqli_real_escape_string($conn, $_POST['year']);
    $semester     = mysqli_real_escape_string($conn, $_POST['semester']);
    
    $attendance_array = $_POST['attendance'];
    $student_names    = $_POST['student_names'];

  $date_of_attendance =$_POST['date_of_attendance']; // Use the date passed from the form
    echo "<script>alert('Date of Attendance: " . $date_of_attendance . "');</script>";
    $success = true;

    // Loop through associative parameters
    foreach ($attendance_array as $roll_number => $status) {
        $roll_number  = mysqli_real_escape_string($conn, $roll_number);
        $student_name = mysqli_real_escape_string($conn, $student_names[$roll_number]);
        $status       = mysqli_real_escape_string($conn, $status); 

        // Match field identifiers to database schema configuration
        $query = "INSERT INTO attendance
                  (student_name, roll_number, subject_name, subject_code, course, year, semester, date_of_attendence, attendance_status) 
                  VALUES 
                  ('$student_name', '$roll_number', '$subject_name', '$subject_code', '$course', '$year', '$semester', '$date_of_attendance', '$status')";
        
        if (!mysqli_query($conn, $query)) {
            $success = false;
        }
    }

    if ($success) {
        echo "<script>alert('Attendance uploaded successfully!'); window.location.href='index.php';</script>";
    } else {
        echo "<script>alert('Error encountered during insertion.'); window.history.back();</script>";
    }
} else {
    header("Location: index.php");
    exit;
}
?>
