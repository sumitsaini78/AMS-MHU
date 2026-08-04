-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 04, 2026 at 01:08 PM
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
(1, 'Super-Admin(MHU-AMS)', '', 1);

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
(4, 'MCOM', 'FACULTY OF COMMERCE AND BUSINESS STUDIES'),
(7, 'BCOM (HONS)', 'FACULTY OF COMMERCE AND BUSINESS STUDIES');

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
(2, 'PROF DR. PK AGARWAL', 'FACULTY OF COMMERCE AND BUSINESS STUDIES', 22),
(6, 'td', 'FPAHS', 43);

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
(1, 'FOA', 'FACULTY OF AGRICULTURE', ''),
(2, 'FAHSS', 'FACULTY OF ARTS, HUMANITIES & SOCIAL SCIENCES', ''),
(3, 'FOCBS', 'FACULTY OF COMMERCE AND BUSINESS STUDIES', ''),
(4, 'FCSIT', 'FACULTY OF COMPUTER SCIENCE AND IT', ''),
(5, 'FOE', 'FACULTY OF EDUCATION', ''),
(6, 'FOET', 'FACULTY OF ENGINEERING AND TECHNOLOGY', ''),
(7, 'FOLS', 'FACULTY OF LEGAL STUDIES', ''),
(8, 'FON', 'FACULTY OF NURSING', ''),
(9, 'FPAHS', 'FACULTY OF PARAMEDICAL AND ALLIED HEALTH SCIENCES', ''),
(10, 'FOPS', 'FACULTY OF PHARMACEUTICAL SCIENCES', ''),
(11, 'FOS', 'FACULTY OF SCIENCES', ''),
(12, 'tf', 'tf', 'tdf'),
(13, 'a', 'a', ''),
(14, 'FOCBS', 'FACULTY OF COMMERCE AND BUSINESS STUDIES', 'r d'),
(15, 'test facx', 'fofffs', '');

-- --------------------------------------------------------

--
-- Table structure for table `students`
--

CREATE TABLE `students` (
  `id` int(11) NOT NULL,
  `name` varchar(122) NOT NULL,
  `father_name` varchar(77) NOT NULL,
  `enrollment_number` varchar(50) DEFAULT NULL,
  `roll_number` varchar(122) DEFAULT NULL,
  `faculty` varchar(111) NOT NULL,
  `course` varchar(111) NOT NULL,
  `section` varchar(11) NOT NULL,
  `year` int(11) NOT NULL,
  `sem` int(11) NOT NULL,
  `date_of_admission` date NOT NULL,
  `session` varchar(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `students`
--

INSERT INTO `students` (`id`, `name`, `father_name`, `enrollment_number`, `roll_number`, `faculty`, `course`, `section`, `year`, `sem`, `date_of_admission`, `session`) VALUES
(1, 'AARUSHI', 'PRAVESH KUMAR', 'M2512050016', '2505000001', 'FACULTY OF COMMERCE AND BUSINESS STUDIES', 'BBA', 'A', 1, 1, '2026-07-01', '2026-2027'),
(2, 'AARYAN GUPTA', 'SUBODH KUMAR', 'M2512050017', '2505000002', 'FACULTY OF COMMERCE AND BUSINESS STUDIES', 'BBA', 'A', 1, 1, '0000-00-00', '2026-2027'),
(3, 'AAS MOHAMMAD', 'RAHEES ALAM', 'M2512050018', '2505000003', 'FACULTY OF COMMERCE AND BUSINESS STUDIES', 'BBA', 'A', 1, 1, '0000-00-00', '2026-2027'),
(4, 'AAYUSH SAINI', 'BHUPENDER', 'M2512050019', '2505000004', 'FACULTY OF COMMERCE AND BUSINESS STUDIES', 'BBA', 'A', 1, 1, '0000-00-00', '2026-2027'),
(5, 'ABDUL RAHMAN', 'MOHD TAHIR', 'M2512050020', '2505000005', 'FACULTY OF COMMERCE AND BUSINESS STUDIES', 'BBA', 'A', 1, 1, '0000-00-00', '2026-2027'),
(6, 'ABHINAV', 'VIKAS KUMAR', 'M2512050021', '2505000006', 'FACULTY OF COMMERCE AND BUSINESS STUDIES', 'BBA', 'A', 1, 1, '0000-00-00', '2026-2027'),
(7, 'ABHINAV SAINI', 'JUGENDAR SAINI', 'M2512050022', '2505000007', 'FACULTY OF COMMERCE AND BUSINESS STUDIES', 'BBA', 'A', 1, 1, '0000-00-00', '2026-2027'),
(8, 'AJAY TYAGI', 'RAKESH KUMAR', 'M2512050023', '2505000008', 'FACULTY OF COMMERCE AND BUSINESS STUDIES', 'BBA', 'A', 1, 1, '0000-00-00', '2026-2027'),
(9, 'AKSHAY', 'PRADEEP KUMAR', 'M2512050024', '2505000009', 'FACULTY OF COMMERCE AND BUSINESS STUDIES', 'BBA', 'B', 1, 1, '0000-00-00', '2026-2027'),
(10, 'AKSHAY CHOUDHARY', 'PARVESH KUMAR', 'M2512050025', '2505000010', 'FACULTY OF COMMERCE AND BUSINESS STUDIES', 'BBA', 'B', 1, 1, '0000-00-00', '2026-2027'),
(11, 'SAVAN SAINI', 'RAJEEV KUMAR SAINI', 'M2512030136', '2503190001', 'FACULTY OF COMMERCE AND BUSINESS STUDIES', 'BCOM', 'A', 1, 1, '2026-07-01', '2026-2027'),
(12, 'AANIK', 'MOHD SAYEED', 'M2512030137', '2503190002', 'FACULTY OF COMMERCE AND BUSINESS STUDIES', 'BCOM', 'A', 1, 1, '0000-00-00', '2026-2027'),
(13, 'AARAV SANGAM', 'PARVESH KUMAR', 'M2512030138', '2503190003', 'FACULTY OF COMMERCE AND BUSINESS STUDIES', 'BCOM', 'A', 1, 1, '0000-00-00', '2026-2027'),
(14, 'AARTI SAINI', 'PANKAJ KUMAR', 'M2512030139', '2503190004', 'FACULTY OF COMMERCE AND BUSINESS STUDIES', 'BCOM', 'A', 1, 1, '0000-00-00', '2026-2027'),
(15, 'AASHISH KARNWAL', 'NARESH KUMAR', 'M2512030140', '2503190005', 'FACULTY OF COMMERCE AND BUSINESS STUDIES', 'BCOM', 'A', 1, 1, '0000-00-00', '2026-2027'),
(16, 'AAYAN', 'SAMIM', 'M2512030141', '2503190006', 'FACULTY OF COMMERCE AND BUSINESS STUDIES', 'BCOM', 'A', 1, 1, '0000-00-00', '2026-2027'),
(17, 'AAYUSH CHOUDHARY', 'SHINOD', 'M2512030142', '2503190007', 'FACULTY OF COMMERCE AND BUSINESS STUDIES', 'BCOM', 'A', 1, 1, '0000-00-00', '2026-2027'),
(18, 'AAYUSHI CHAUDHARY', 'AJAB SINGH', 'M2512030143', '2503190008', 'FACULTY OF COMMERCE AND BUSINESS STUDIES', 'BCOM', 'A', 1, 1, '0000-00-00', '2026-2027'),
(19, 'ABDUL RAHMAN', 'MUKAMMIL', 'M2512030278', '2503190143', 'FACULTY OF COMMERCE AND BUSINESS STUDIES', 'BCOM', 'B', 1, 1, '0000-00-00', '2026-2027'),
(20, 'ABDUSSAMAD', 'SHAHNAWAJ', 'M2512030144', '2503190009', 'FACULTY OF COMMERCE AND BUSINESS STUDIES', 'BCOM', 'B', 1, 1, '0000-00-00', '2026-2027');

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
  `roll_number` varchar(50) DEFAULT NULL,
  `enrollment_number` varchar(50) DEFAULT NULL,
  `session` varchar(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `subjected_student`
--

INSERT INTO `subjected_student` (`id`, `student_name`, `subject_name`, `subject_code`, `faculty`, `course`, `year`, `semester`, `roll_number`, `enrollment_number`, `session`) VALUES
(1, 'AARUSHI', 'Business communication', '', 'FACULTY OF COMMERCE AND BUSINESS STUDIES', 'BBA', 1, 1, '2505000001', 'M2512050016', '2026-2027'),
(2, 'AARYAN GUPTA', 'Business communication', '', 'FACULTY OF COMMERCE AND BUSINESS STUDIES', 'BBA', 1, 1, '2505000002', 'M2512050017', ''),
(3, 'AAS MOHAMMAD', 'Business communication', '', 'FACULTY OF COMMERCE AND BUSINESS STUDIES', 'BBA', 1, 1, '2505000003', 'M2512050018', ''),
(4, 'AAYUSH SAINI', 'Business communication', '', 'FACULTY OF COMMERCE AND BUSINESS STUDIES', 'BBA', 1, 1, '2505000004', 'M2512050019', ''),
(5, 'ABDUL RAHMAN', 'Business communication', '', 'FACULTY OF COMMERCE AND BUSINESS STUDIES', 'BBA', 1, 1, '2505000005', 'M2512050020', ''),
(6, 'ABHINAV', 'Business communication', '', 'FACULTY OF COMMERCE AND BUSINESS STUDIES', 'BBA', 1, 1, '2505000006', 'M2512050021', ''),
(7, 'ABHINAV SAINI', 'Indian Knowledge system', 'bba262', 'FACULTY OF COMMERCE AND BUSINESS STUDIES', 'BBA', 1, 1, '2505000007', 'M2512050022', ''),
(8, 'AJAY TYAGI', 'Indian Knowledge system', 'bba262', 'FACULTY OF COMMERCE AND BUSINESS STUDIES', 'BBA', 1, 1, '2505000008', 'M2512050023', ''),
(9, 'AKSHAY', 'Indian Knowledge system', 'bba262', 'FACULTY OF COMMERCE AND BUSINESS STUDIES', 'BBA', 1, 1, '2505000009', 'M2512050024', ''),
(10, 'AKSHAY CHOUDHARY', 'Indian Knowledge system', 'bba262', 'FACULTY OF COMMERCE AND BUSINESS STUDIES', 'BBA', 1, 1, '2505000010', 'M2512050025', '');

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
(7, 1, 7, 'DR. Snehashish Bhardwaj', 'Business communication', 'BCOM', 1, 1, 'bcom261'),
(8, 1, 8, 'DR. Snehashish Bhardwaj', 'Indian Knowledge system', 'BBA', 1, 1, 'bba262'),
(9, 1, 9, 'DR. Snehashish Bhardwaj', 'Business Management', 'BBA', 1, 2, 'bba261'),
(10, 2, 8, 'DR. Gorav yadav', 'Indian Knowledge system', 'BBA', 1, 1, 'bba262');

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
(7, 'BCOM', 1, 1, 'Business communication', '', 'bcom261', 'FACULTY OF COMMERCE AND BUSINESS STUDIES'),
(8, 'BBA', 1, 1, 'Indian Knowledge system', '', 'bba262', 'FACULTY OF COMMERCE AND BUSINESS STUDIES'),
(9, 'BBA', 1, 2, 'Business Management', '', 'bba261', 'FACULTY OF COMMERCE AND BUSINESS STUDIES'),
(10, 'BCOM', 1, 2, 'Business Mayhmatics', '', 'bcom262', 'FACULTY OF COMMERCE AND BUSINESS STUDIES');

-- --------------------------------------------------------

--
-- Table structure for table `teachers`
--

CREATE TABLE `teachers` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `faculty` varchar(255) NOT NULL,
  `number` int(13) NOT NULL,
  `designation` varchar(66) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `teachers`
--

INSERT INTO `teachers` (`id`, `name`, `faculty`, `number`, `designation`) VALUES
(1, 'DR. Snehashish Bhardwaj', 'FACULTY OF COMMERCE AND BUSINESS STUDIES', 1, 'ASSISTANT PROFESSOR'),
(2, 'DR. Gorav yadav', 'FACULTY OF COMMERCE AND BUSINESS STUDIES', 2, 'ASSISTANT PROFESSOR'),
(3, 'MR. DALEP', 'FACULTY OF COMMERCE AND BUSINESS STUDIES', 3, 'ASSISTANT PROFESSOR'),
(4, 'DR. MADHU RANI', 'FACULTY OF COMMERCE AND BUSINESS STUDIES', 4, 'ASSISTANT PROFESSOR');

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
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `enrollment_number` (`enrollment_number`);

--
-- Indexes for table `subjected_student`
--
ALTER TABLE `subjected_student`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_student_subject` (`roll_number`,`subject_name`,`semester`);

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `attendance_corrections`
--
ALTER TABLE `attendance_corrections`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `courses_list`
--
ALTER TABLE `courses_list`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `deans`
--
ALTER TABLE `deans`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `faculty`
--
ALTER TABLE `faculty`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `students`
--
ALTER TABLE `students`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `subjected_student`
--
ALTER TABLE `subjected_student`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `subjected_teacher`
--
ALTER TABLE `subjected_teacher`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `subjects`
--
ALTER TABLE `subjects`
  MODIFY `course_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `teachers`
--
ALTER TABLE `teachers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=48;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
