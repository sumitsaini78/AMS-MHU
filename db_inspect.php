<?php
include "db_connect.php";
$result = $conn->query("SHOW CREATE TABLE subjects");
$row = $result->fetch_row();
echo $row[1];
