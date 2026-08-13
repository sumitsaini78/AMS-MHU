<?php
$ch = curl_init('http://localhost/AMS-MHU/admin/assign_student_subject.php');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
// Mock the session cookie? We might need to login first.
// Let's create a script that bypasses login for this test, or log in first.
