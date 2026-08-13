<?php
include "db_connect.php";
$_POST['download_sample_csv'] = true;
$_POST['sample_course'] = 'BBA';
$_POST['sample_sem'] = 2;
$_POST['sample_year'] = 1;

$sample_course = 'BBA';
$req_sem = 2;
$req_year = 1;

$clean_sample = str_replace(['.', ' '], ['', ''], strtoupper(trim($sample_course ?? '')));
$safe_sample = mysqli_real_escape_string($conn, $sample_course ?? '');
$sample_course_cond = "TRIM(UPPER(course)) = UPPER('$safe_sample')";
$sample_subject_cond = "TRIM(UPPER(course_name)) = UPPER('$safe_sample')";

$students = [];
$student_query = "SELECT name, roll_number, enrollment_number FROM `students` WHERE $sample_course_cond AND sem = ?";
$stmt_stu = $conn->prepare($student_query);
$stmt_stu->bind_param("i", $req_sem);
$stmt_stu->execute();
$res_stu = $stmt_stu->get_result();
while ($row = $res_stu->fetch_assoc()) {
    $students[] = $row;
}

$subjects = [];
$subject_query = "SELECT DISTINCT subject_name FROM `subjects` WHERE $sample_subject_cond AND semester = ?";
$stmt_sub = $conn->prepare($subject_query);
$stmt_sub->bind_param("i", $req_sem);
$stmt_sub->execute();
$res_sub = $stmt_sub->get_result();
while ($row = $res_sub->fetch_assoc()) {
    $subjects[] = $row['subject_name'];
}

$csv_data = [];
$csv_data[] = ['Roll Number', 'Enrollment Number', 'Subject Name', 'Semester', 'Year'];

foreach ($students as $student) {
    if (!empty($subjects)) {
        foreach ($subjects as $subject) {
            $csv_data[] = [
                $student['roll_number'],
                $student['enrollment_number'],
                $subject,
                $req_sem,
                $req_year
            ];
        }
    } else {
        $csv_data[] = [
            $student['roll_number'],
            $student['enrollment_number'],
            '',
            $req_sem,
            $req_year
        ];
    }
}
echo "Total Rows: " . count($csv_data) . "\n";
print_r($csv_data[1] ?? 'No data');
