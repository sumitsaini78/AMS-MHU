<?php
include "db_connect.php";
$res = $conn->query("SELECT DISTINCT course FROM students");
while($row = $res->fetch_assoc()) {
    echo $row['course'] . "\n";
}
