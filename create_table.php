<?php
include "db_connect.php";

$sql = "CREATE TABLE IF NOT EXISTS `subject_requests` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `teacher_id` int(11) NOT NULL,
  `teacher_name` varchar(255) NOT NULL,
  `sub_id` int(11) NOT NULL,
  `subject_name` varchar(255) NOT NULL,
  `course_name` varchar(255) NOT NULL,
  `year` varchar(11) NOT NULL,
  `semester` varchar(11) NOT NULL,
  `faculty_name` varchar(255) NOT NULL,
  `subject_code` varchar(66) NOT NULL,
  `status` ENUM('Pending', 'Approved', 'Rejected') DEFAULT 'Pending',
  `request_date` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;";

if ($conn->query($sql) === TRUE) {
    echo "Table subject_requests created successfully";
} else {
    echo "Error creating table: " . $conn->error;
}
