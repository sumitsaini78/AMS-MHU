<?php
include "../db_connect.php";

header('Content-Type: application/json');

if (isset($_GET['roll_number'])) {
    $roll = mysqli_real_escape_string($conn, $_GET['roll_number']);
    
    // Fetch details
    $query = "SELECT name, course, year, sem FROM `students` WHERE roll_number = '$roll' LIMIT 1";
    $result = mysqli_query($conn, $query);
    
    if ($result && mysqli_num_rows($result) > 0) {
        echo json_encode(mysqli_fetch_assoc($result));
    } else {
        echo json_encode(['error' => 'Not Found']);
    }
}
?>