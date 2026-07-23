-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 23, 2026 at 03:17 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `mhu-ams`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `gmail` varchar(255) NOT NULL,
  `number` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`id`, `name`, `gmail`, `number`) VALUES
(1, 'admin', '', 1);

-- --------------------------------------------------------

--
-- Table structure for table `attendance`
--

CREATE TABLE `attendance` (
  `id` int(11) NOT NULL,
  `student_name` varchar(55) NOT NULL,
  `roll_number` varchar(22) NOT NULL,
  `subject_name` varchar(122) NOT NULL,
  `subject_code` varchar(55) NOT NULL,
  `course` varchar(55) NOT NULL,
  `year` int(11) NOT NULL,
  `semester` int(11) NOT NULL,
  `date_of_attendence` int(11) NOT NULL,
  `attendance_status` varchar(12) NOT NULL,
  `teacher_name` varchar(100) NOT NULL,
  `session` varchar(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `attendance`
--

INSERT INTO `attendance` (`id`, `student_name`, `roll_number`, `subject_name`, `subject_code`, `course`, `year`, `semester`, `date_of_attendence`, `attendance_status`, `teacher_name`, `session`) VALUES
(21, 'sumti', '123', 'Business Management ', 'bms1', 'BBA', 1, 1, 210726, 'Present', '', '2026-27'),
(22, 'sumti', '1232', 'Business Management ', 'bms1', 'BBA', 1, 1, 230726, 'Present', '', '2025-26'),
(23, 'sumitaaaaa', '1277', 'Business Management ', 'bms1', 'BBA', 1, 1, 230726, 'Present', '', '2026-27'),
(24, 'sumti', '123', 'Business Management ', 'bms1', 'BBA', 1, 1, 230726, 'Absent', '', '2026-27'),
(25, 'sumitaaaaa', '1277', 'Business Management ', 'bms1', 'BBA', 1, 1, 230726, 'Absent', '', '2026-27'),
(26, 'aaa', '111', 'Business Management ', 'bms1', 'BBA', 1, 1, 230726, 'Present', '', '2025-26'),
(27, 'sumti', '123', 'Business care', 'bms1', 'BBA', 1, 2, 230726, 'Present', '', '2026-27');

-- --------------------------------------------------------

--
-- Table structure for table `attendance_corrections`
--

CREATE TABLE `attendance_corrections` (
  `id` int(11) NOT NULL,
  `attendance_id` int(11) NOT NULL,
  `teacher_id` int(11) NOT NULL,
  `student_name` varchar(122) NOT NULL,
  `roll_number` varchar(22) NOT NULL,
  `subject_name` varchar(122) NOT NULL,
  `date_of_attendance` int(11) NOT NULL,
  `current_status` varchar(12) NOT NULL,
  `requested_status` varchar(12) NOT NULL,
  `reason` text NOT NULL,
  `status` enum('Pending','Approved','Rejected') DEFAULT 'Pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `attendance_corrections`
--

INSERT INTO `attendance_corrections` (`id`, `attendance_id`, `teacher_id`, `student_name`, `roll_number`, `subject_name`, `date_of_attendance`, `current_status`, `requested_status`, `reason`, `status`, `created_at`) VALUES
(1, 1, 3, 'radha', '2', 'Business Management ', 220726, 'Present', 'Present', 'a', 'Approved', '2026-07-22 09:06:47');

-- --------------------------------------------------------

--
-- Table structure for table `courses_list`
--

CREATE TABLE `courses_list` (
  `id` int(11) NOT NULL,
  `course_name` varchar(155) NOT NULL,
  `faculty_name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `courses_list`
--

INSERT INTO `courses_list` (`id`, `course_name`, `faculty_name`) VALUES
(1, 'BBA', 'FACULTY OF COMMERCE AND BUSINESS STUDIES'),
(2, 'MBA', 'FACULTY OF COMMERCE AND BUSINESS STUDIES'),
(3, 'BCOM', 'FACULTY OF COMMERCE AND BUSINESS STUDIES'),
(4, 'MCOM', 'FACULTY OF COMMERCE AND BUSINESS STUDIES');

-- --------------------------------------------------------

--
-- Table structure for table `deans`
--

CREATE TABLE `deans` (
  `id` int(11) NOT NULL,
  `Dean_name` varchar(222) NOT NULL,
  `faculty_name` varchar(222) NOT NULL,
  `number` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `deans`
--

INSERT INTO `deans` (`id`, `Dean_name`, `faculty_name`, `number`) VALUES
(2, 'PROF DR. PK AGARWAL', 'FACULTY OF COMMERCE AND BUSINESS STUDIES', 22);

-- --------------------------------------------------------

--
-- Table structure for table `faculty`
--

CREATE TABLE `faculty` (
  `id` int(11) NOT NULL,
  `faculty_name` varchar(255) NOT NULL,
  `faculty_full_name` varchar(255) NOT NULL,
  `department` varchar(122) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `faculty`
--

INSERT INTO `faculty` (`id`, `faculty_name`, `faculty_full_name`, `department`) VALUES
(1, 'FOCBS', 'FACULTY OF COMMERCE AND BUSINESS STUDIES', ''),
(8, 'FOCBS', 'FACULTY OF COMMERCE AND BUSINESS STUDIES', 'asa');

-- --------------------------------------------------------

--
-- Table structure for table `students`
--

CREATE TABLE `students` (
  `id` int(11) NOT NULL,
  `name` varchar(122) NOT NULL,
  `enrollment_number` int(11) NOT NULL,
  `roll_number` varchar(122) NOT NULL,
  `faculty` varchar(111) NOT NULL,
  `course` varchar(111) NOT NULL,
  `section` varchar(11) NOT NULL,
  `year` int(11) NOT NULL,
  `sem` int(11) NOT NULL,
  `date_of_admission` int(11) NOT NULL,
  `session` varchar(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `students`
--

INSERT INTO `students` (`id`, `name`, `enrollment_number`, `roll_number`, `faculty`, `course`, `section`, `year`, `sem`, `date_of_admission`, `session`) VALUES
(12, 'sumti', 1234, '123', 'FOCBS', 'BBA', 'A', 1, 1, 2026, '2026-27'),
(13, 'sumitaaaaa', 1234, '1277', 'FOCBS', 'BBA', 'A', 1, 1, 2026, '2025-26'),
(18, 'aarti', 111, '188', 'FOCBS', 'BBA', 'A', 1, 1, 2026, '2026-27');

-- --------------------------------------------------------

--
-- Table structure for table `subjected_student`
--

CREATE TABLE `subjected_student` (
  `id` int(11) NOT NULL,
  `student_name` varchar(122) NOT NULL,
  `subject_name` varchar(122) NOT NULL,
  `subject_code` varchar(122) NOT NULL,
  `faculty` varchar(122) NOT NULL,
  `course` varchar(122) NOT NULL,
  `year` int(11) NOT NULL,
  `semester` int(11) NOT NULL,
  `roll_number` int(11) NOT NULL,
  `session` varchar(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `subjected_student`
--

INSERT INTO `subjected_student` (`id`, `student_name`, `subject_name`, `subject_code`, `faculty`, `course`, `year`, `semester`, `roll_number`, `session`) VALUES
(17, 'sumti', 'Business care', 'bms1', 'FOCBS', 'BBA', 1, 1, 123, '2026-27'),
(18, 'sumitaaaaa', 'Business Management ', 'bms1', 'FOCBS', 'BBA', 1, 1, 1277, '2026-27'),
(25, 'aarti', 'Business Management', 'bms1', 'FOCBS', 'BBA', 1, 1, 188, '');

-- --------------------------------------------------------

--
-- Table structure for table `subjected_teacher`
--

CREATE TABLE `subjected_teacher` (
  `id` int(11) NOT NULL,
  `teacher_id` int(11) NOT NULL,
  `sub_id` int(11) NOT NULL,
  `teacher_name` varchar(255) NOT NULL,
  `subject_name` varchar(255) NOT NULL,
  `course_name` varchar(255) NOT NULL,
  `year` int(11) NOT NULL,
  `semester` int(11) NOT NULL,
  `subject_code` varchar(66) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `subjected_teacher`
--

INSERT INTO `subjected_teacher` (`id`, `teacher_id`, `sub_id`, `teacher_name`, `subject_name`, `course_name`, `year`, `semester`, `subject_code`) VALUES
(1, 3, 2, 'DR. SNEHASHISH BHARDWAJ', 'Business Management ', 'BBA', 1, 1, 'bms1'),
(3, 3, 7, 'DR. SNEHASHISH BHARDWAJ', 'Business care', 'BBA', 1, 2, 'bms1');

-- --------------------------------------------------------

--
-- Table structure for table `subjects`
--

CREATE TABLE `subjects` (
  `course_id` int(11) NOT NULL,
  `course_name` varchar(233) NOT NULL,
  `Year` int(11) NOT NULL,
  `semester` int(11) NOT NULL,
  `subject_name` varchar(255) NOT NULL,
  `dept_name` varchar(235) NOT NULL,
  `subject_code` varchar(55) NOT NULL,
  `faculty_name` varchar(55) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `subjects`
--

INSERT INTO `subjects` (`course_id`, `course_name`, `Year`, `semester`, `subject_name`, `dept_name`, `subject_code`, `faculty_name`) VALUES
(1, 'BBA', 1, 1, 'Business Management ', '', 'bms1', 'FACULTY OF COMMERCE AND BUSINESS STUDIES'),
(2, 'BBA', 1, 1, 'Business Management ', '', 'bms1', 'FACULTY OF COMMERCE AND BUSINESS STUDIES'),
(3, 'B.Sc CS', 2026, 1, 'Introduction to Programming', '', 'CS-101', 'FACULTY OF COMMERCE AND BUSINESS STUDIES'),
(4, 'B.Sc CS', 2026, 1, 'Mathematics-I', '', 'MAT-101', 'FACULTY OF COMMERCE AND BUSINESS STUDIES'),
(5, 'MBA', 1, 2, 'mba sum ', '', 'mbacd', 'FACULTY OF COMMERCE AND BUSINESS STUDIES'),
(6, 'BBA', 1, 2, 'bba 12', '', '', 'FACULTY OF COMMERCE AND BUSINESS STUDIES'),
(7, 'BBA', 1, 1, 'Business care', '', 'bms1', 'FACULTY OF COMMERCE AND BUSINESS STUDIES');

-- --------------------------------------------------------

--
-- Table structure for table `teachers`
--

CREATE TABLE `teachers` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `faculty` varchar(255) NOT NULL,
  `number` int(13) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `teachers`
--

INSERT INTO `teachers` (`id`, `name`, `faculty`, `number`) VALUES
(3, 'DR. SNEHASHISH BHARDWAJ', 'FACULTY OF COMMERCE AND BUSINESS STUDIES', 333),
(4, 'mr sumit saini', 'FACULTY OF COMMERCE AND BUSINESS STUDIES', 4),
(5, 'te1', 'FACULTY OF COMMERCE AND BUSINESS STUDIES', 4),
(6, 't2', 'FACULTY OF COMMERCE AND BUSINESS STUDIES', 5);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `attendance`
--
ALTER TABLE `attendance`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `attendance_corrections`
--
ALTER TABLE `attendance_corrections`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `courses_list`
--
ALTER TABLE `courses_list`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `deans`
--
ALTER TABLE `deans`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `faculty`
--
ALTER TABLE `faculty`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `students`
--
ALTER TABLE `students`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `subjected_student`
--
ALTER TABLE `subjected_student`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `subjected_teacher`
--
ALTER TABLE `subjected_teacher`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `subjects`
--
ALTER TABLE `subjects`
  ADD PRIMARY KEY (`course_id`);

--
-- Indexes for table `teachers`
--
ALTER TABLE `teachers`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `attendance`
--
ALTER TABLE `attendance`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT for table `attendance_corrections`
--
ALTER TABLE `attendance_corrections`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `courses_list`
--
ALTER TABLE `courses_list`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `deans`
--
ALTER TABLE `deans`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `faculty`
--
ALTER TABLE `faculty`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `students`
--
ALTER TABLE `students`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `subjected_student`
--
ALTER TABLE `subjected_student`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT for table `subjected_teacher`
--
ALTER TABLE `subjected_teacher`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `subjects`
--
ALTER TABLE `subjects`
  MODIFY `course_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `teachers`
--
ALTER TABLE `teachers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
