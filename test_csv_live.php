<?php
include "db_connect.php";
session_start();
$_SESSION['admin_id'] = 1;
$_SESSION['admin_name'] = 'Admin';
$_POST['download_sample_csv'] = true;
$_POST['sample_course'] = 'BBA';
$_POST['sample_sem'] = 2;
ob_start();
include "admin/assign_student_subject.php";
$output = ob_get_clean();
file_put_contents('test_output.csv', $output);
echo "Output saved. Length: " . strlen($output) . "\n";
