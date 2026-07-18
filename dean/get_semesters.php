<?php
// get_semesters.php

// Check if 'year' is received via POST request
if (isset($_POST['year'])) {
    $selected_year = $_POST['year'];
    
    $semesters = [];

    // Logic to assign semesters based on the selected year
    if ($selected_year == '1') {
        $semesters = [
            '1' => 'Semester 1',
            '2' => 'Semester 2'
        ];
    } elseif ($selected_year == '2') {
        $semesters = [
            '3' => 'Semester 3',
            '4' => 'Semester 4'
        ];
    } elseif ($selected_year == '3') {
        $semesters = [
            '5' => 'Semester 5',
            '6' => 'Semester 6'
        ];
    } elseif ($selected_year == '4') {
        $semesters = [
            '7' => 'Semester 7',
            '8' => 'Semester 8'
        ];
    }

    // Return the data as a JSON response
    header('Content-Type: application/json');
    echo json_encode($semesters);
    exit;
}
?>