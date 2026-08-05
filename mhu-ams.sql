-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 05, 2026 at 01:20 PM
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
(5, 'BCOM (HONS)', 'FACULTY OF COMMERCE AND BUSINESS STUDIES'),
(6, 'MBA INTEGRATED', 'FACULTY OF COMMERCE AND BUSINESS STUDIES');

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
(11, 'FOS', 'FACULTY OF SCIENCES', '');

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
(1, 'AARUSHI', 'PRAVESH KUMAR', 'M2512050016', 'A-101', 'FACULTY OF COMMERCE AND BUSINESS STUIDES', 'BBA', 'A', 1, 1, '2026-07-01', '2026-2027'),
(2, 'AARYAN GUPTA', 'SUBODH KUMAR', 'M2512050017', '', 'FACULTY OF COMMERCE AND BUSINESS STUIDES', 'BBA', 'A', 1, 1, '2026-07-02', '2026-2028'),
(3, 'AAS MOHAMMAD', 'RAHEES ALAM', 'M2512050018', '', 'FACULTY OF COMMERCE AND BUSINESS STUIDES', 'BBA', 'A', 1, 1, '2026-07-03', '2026-2029'),
(4, 'AAYUSH SAINI', 'BHUPENDER', 'M2512050019', '', 'FACULTY OF COMMERCE AND BUSINESS STUIDES', 'BBA', 'A', 1, 1, '2026-07-04', '2026-2030'),
(5, 'ABDUL RAHMAN', 'MOHD TAHIR', 'M2512050020', '', 'FACULTY OF COMMERCE AND BUSINESS STUIDES', 'BBA', 'A', 1, 1, '2026-07-05', '2026-2031'),
(6, 'ABHINAV', 'VIKAS KUMAR', 'M2512050021', '', 'FACULTY OF COMMERCE AND BUSINESS STUIDES', 'BBA', 'A', 1, 2, '2026-07-06', '2026-2032'),
(7, 'ABHINAV SAINI', 'JUGENDAR SAINI', 'M2512050022', '', 'FACULTY OF COMMERCE AND BUSINESS STUIDES', 'BBA', 'A', 1, 2, '2026-07-07', '2026-2033'),
(8, 'AJAY TYAGI', 'RAKESH KUMAR', 'M2512050023', '', 'FACULTY OF COMMERCE AND BUSINESS STUIDES', 'BBA', 'A', 1, 2, '2026-07-08', '2026-2034'),
(9, 'AKSHAY', 'PRADEEP KUMAR', 'M2512050024', '', 'FACULTY OF COMMERCE AND BUSINESS STUIDES', 'BBA', 'A', 1, 2, '2026-07-09', '2026-2035'),
(10, 'AKSHAY CHOUDHARY', 'PARVESH KUMAR', 'M2512050025', '', 'FACULTY OF COMMERCE AND BUSINESS STUIDES', 'BBA', 'A', 2, 4, '2026-07-10', '2026-2036'),
(11, 'AMAN CHAUDHARY', 'RISHI PAL SINGH', 'M2512050026', '', 'FACULTY OF COMMERCE AND BUSINESS STUIDES', 'BBA', 'A', 2, 4, '2026-07-11', '2026-2037'),
(12, 'AMRIT JAIN', 'ASHISH JAIN', 'M2512050027', '', 'FACULTY OF COMMERCE AND BUSINESS STUIDES', 'BBA', 'A', 2, 4, '2026-07-12', '2026-2038'),
(13, 'ANANT SINGH', 'V K SINGH', 'M2512050298', '', 'FACULTY OF COMMERCE AND BUSINESS STUIDES', 'BBA', 'B', 2, 4, '2026-07-13', '2026-2039'),
(14, 'ANANT TYAGI', 'HARISH TYAGI', 'M2512050028', '', 'FACULTY OF COMMERCE AND BUSINESS STUIDES', 'BBA', 'B', 2, 4, '2026-07-14', '2026-2040'),
(15, 'ANIKET TYAGI', 'VINESH TYAGI', 'M2512050293', '', 'FACULTY OF COMMERCE AND BUSINESS STUIDES', 'BBA', 'B', 2, 4, '2026-07-15', '2026-2041'),
(16, 'ANKUR DWIVEDI', 'DHIRENDRA DWIVEDI', 'M2512050029', '', 'FACULTY OF COMMERCE AND BUSINESS STUIDES', 'BBA', 'B', 2, 4, '2026-07-16', '2026-2042'),
(17, 'ANSHU', 'RAVI KUMAR', 'M2512050270', '', 'FACULTY OF COMMERCE AND BUSINESS STUIDES', 'BBA', 'B', 2, 4, '2026-07-17', '2026-2043'),
(18, 'AADITYA KUMAR', 'SATISH KUMAR', 'M2512060002', '2506000002', 'FACULTY OF COMMERCE AND BUSINESS STUDIES', 'MBA', 'A', 1, 1, '2026-07-18', '2026-2044'),
(19, 'ABHIJEET PANWAR', 'MAHAKAR SINGH', 'M2512060057', '2506000059', 'FACULTY OF COMMERCE AND BUSINESS STUDIES', 'MBA', 'A', 1, 1, '2026-07-19', '2026-2045'),
(20, 'ABHINAV KUMAR', 'JAIVEER SINGH', 'M2512060003', '2506000003', 'FACULTY OF COMMERCE AND BUSINESS STUDIES', 'MBA', 'A', 1, 1, '2026-07-20', '2026-2046'),
(21, 'ABHISHEK', 'MUKESH SHARMA', 'M2512060004', '2506000004', 'FACULTY OF COMMERCE AND BUSINESS STUDIES', 'MBA', 'A', 1, 1, '2026-07-21', '2026-2047'),
(22, 'ADITI SHARMA', 'PREM CHAND JAIN', 'M2512060005', '2506000005', 'FACULTY OF COMMERCE AND BUSINESS STUDIES', 'MBA', 'A', 1, 2, '2026-07-22', '2026-2048'),
(23, 'ANANT JAIN', 'RAJKUMAR', 'M2512060049', '2506000046', 'FACULTY OF COMMERCE AND BUSINESS STUDIES', 'MBA', 'A', 1, 2, '2026-07-23', '2026-2049'),
(24, 'ANCHAL', 'PRAMOD GUPTA', 'M2512060007', '2506000007', 'FACULTY OF COMMERCE AND BUSINESS STUDIES', 'MBA', 'A', 1, 2, '2026-07-24', '2026-2050'),
(25, 'ANJALI GUPTA', 'VIPIN PAL', 'M2512060008', '2506000008', 'FACULTY OF COMMERCE AND BUSINESS STUDIES', 'MBA', 'A', 1, 2, '2026-07-25', '2026-2051'),
(26, 'ANJALI PAL', 'PARSHURAM YADAV', 'M2512060050', '2506000052', 'FACULTY OF COMMERCE AND BUSINESS STUDIES', 'MBA', 'A', 1, 2, '2026-07-26', '2026-2052'),
(27, 'ANSHIKA YADAV', 'BIPENDRA KUMAR CHOUDHARY', 'M2512060059', '2506000061', 'FACULTY OF COMMERCE AND BUSINESS STUDIES', 'MBA', 'A', 1, 2, '2026-07-27', '2026-2053'),
(28, 'ANURAG CHOUDHARY', 'PRADEEP KUMAR', 'M2512060009', '2506000009', 'FACULTY OF COMMERCE AND BUSINESS STUDIES', 'MBA', 'A', 2, 3, '2026-07-28', '2026-2054'),
(29, 'ARNIKA KAMBOJ', 'PRAVEEN KUMAR', 'M2512060010', '2506000010', 'FACULTY OF COMMERCE AND BUSINESS STUDIES', 'MBA', 'A', 2, 3, '2026-07-29', '2026-2055'),
(30, 'ARYAN SAINI', 'SANJEEV KUMAR', 'M2512060056', '2506000058', 'FACULTY OF COMMERCE AND BUSINESS STUDIES', 'MBA', 'A', 2, 3, '2026-07-30', '2026-2056'),
(31, 'ARYAV KUMAR', 'SHEHJAD', 'M2512060011', '2506000011', 'FACULTY OF COMMERCE AND BUSINESS STUDIES', 'MBA', 'A', 2, 3, '2026-07-31', '2026-2057'),
(32, 'ASIF', 'AMRISH KUMAR SAINI', 'M2512060040', '2506000012', 'FACULTY OF COMMERCE AND BUSINESS STUDIES', 'MBA', 'A', 2, 3, '2026-08-01', '2026-2058'),
(33, 'BUNISH KUMAR SAINI', 'RAMPAL', 'M2512060041', '2506000013', 'FACULTY OF COMMERCE AND BUSINESS STUDIES', 'MBA', 'A', 2, 3, '2026-08-02', '2026-2059'),
(34, 'DEEN DYAL', 'NAVEEN KUMAR', 'M2512060042', '2506000014', 'FACULTY OF COMMERCE AND BUSINESS STUDIES', 'MBA', 'A', 2, 3, '2026-08-03', '2026-2060'),
(35, 'DISHU', 'RAJESH GIRI', 'M2512060012', '2506000015', 'FACULTY OF COMMERCE AND BUSINESS STUDIES', 'MBA', 'A', 2, 3, '2026-08-04', '2026-2061'),
(36, 'HARSH GIRI', 'DEEPAK NAMDEV', 'M2512060055', '2506000056', 'FACULTY OF COMMERCE AND BUSINESS STUDIES', 'MBA', 'A', 2, 3, '2026-08-05', '2026-2062'),
(37, 'HARSH NAMDEV', 'ARUN SHARMA', 'M2512060053', '2506000055', 'FACULTY OF COMMERCE AND BUSINESS STUDIES', 'MBA', 'A', 2, 3, '2026-08-06', '2026-2063');

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
(10, 'AKSHAY CHOUDHARY', 'Indian Knowledge system', 'bba262', 'FACULTY OF COMMERCE AND BUSINESS STUDIES', 'BBA', 1, 1, '2505000010', 'M2512050025', ''),
(11, 'AJAY TYAGI', 'Business Management', 'bba261', 'FACULTY OF COMMERCE AND BUSINESS STUDIES', 'BBA', 1, 1, '2505000008', 'M2512050023', '2026-2027');

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
(1, 'MBA', 1, 1, 'Financial Accounting & Financial Management', '', 'MBA26-101T', 'FOCBS'),
(2, 'MBA', 1, 1, 'Business Environment', '', 'MBA26-102T', 'FOCBS'),
(3, 'MBA', 1, 1, 'Business Statistics', '', 'MBA26-103T', 'FOCBS'),
(4, 'MBA', 1, 1, 'Managerial Economics', '', 'MBA26-104T', 'FOCBS'),
(5, 'MBA', 1, 1, 'Management Information System', '', 'MBA26-105T', 'FOCBS'),
(6, 'MBA', 1, 1, 'Business Communication', '', 'MBA26-106T', 'FOCBS'),
(7, 'MBA', 2, 3, 'Project Management', '', 'MPMBA22-301T', 'FOCBS'),
(8, 'MBA', 2, 3, 'Business Ethics & Corporate Governance', '', 'MPMBA22-302T', 'FOCBS'),
(9, 'MBA', 2, 3, 'Strategic Management', '', 'MPMBA22-303T', 'FOCBS'),
(10, 'MBA', 2, 3, 'Specialization Group-I Elective - II (Consumer Behavior)', '', 'MPMBA22-304 M1', 'FOCBS'),
(11, 'MBA', 2, 3, 'Specialization Group-I Elective - I (Marketing of Services)', '', 'MPMBA22-304 M3', 'FOCBS'),
(12, 'MBA', 2, 3, 'Specialization Group-II Elective - I (Security Analysis & Portfolio Management )', '', 'MPMBA22-304 F1', 'FOCBS'),
(13, 'MBA', 2, 3, 'Specialization Group-II Elective - II (Financial Markets & Institutions)', '', 'MPMBA22-304 F2', 'FOCBS'),
(14, 'MBA', 2, 3, 'Specialization Group-III Elective - III (Industrial Relations &Labour Laws)', '', 'MPMBA22-304 H1', 'FOCBS'),
(15, 'MBA', 2, 3, 'Specialization Group-III Elective - III(Human Resource Planning & Development)', '', 'MPMBA22-304 H3', 'FOCBS'),
(16, 'M.Com', 1, 1, 'Corporate Financial Accounting', '', 'MPMOM22-101T', 'FOCBS'),
(17, 'M.Com', 1, 1, 'Management Principles and Practice', '', 'MPMOM22-102T', 'FOCBS'),
(18, 'M.Com', 1, 1, 'Business Environment', '', 'MPMOM22-103T', 'FOCBS'),
(19, 'M.Com', 1, 1, 'Managerial Economics', '', 'MPMOM22-104T', 'FOCBS'),
(20, 'M.Com', 1, 1, 'Constitution of India', '', 'MPMOM22-105T', 'FOCBS'),
(21, 'M.Com', 1, 1, 'Computer Applications in Business', '', 'MPMOM22-106T', 'FOCBS'),
(22, 'M.Com', 2, 3, 'Quantitative Techniques', '', 'MPMOM22-301T', 'FOCBS'),
(23, 'M.Com', 2, 3, 'Management of Financial Services', '', 'MPMOM22-302T', 'FOCBS'),
(24, 'M.Com', 2, 3, 'Income Tax Laws & Practice', '', 'MPMOM22-303T', 'FOCBS'),
(25, 'M.Com', 2, 3, 'E-Commerce', '', 'MPMOM22-304T', 'FOCBS'),
(26, 'M.Com', 2, 3, 'Group A (Human Resource Management) Industrial Relation Labour Laws', '', 'MPMOM22-305H1', 'FOCBS'),
(27, 'M.Com', 2, 3, 'Human Resource Planning & Development', '', 'MPMOM22-305H2', 'FOCBS'),
(28, 'M.Com', 2, 3, 'Group B (Financial Management) Financial Markets & Institutions', '', 'MPMOM22-305F1', 'FOCBS'),
(29, 'M.Com', 2, 3, 'Security Analysis & Portfolio Management', '', 'MPMOM22-305F2', 'FOCBS'),
(30, 'BBA', 1, 1, 'Principles & Practices of Management', '', 'BBA25-101T', 'FOCBS'),
(31, 'BBA', 1, 1, 'Financial Accounting', '', 'BBA25-102T', 'FOCBS'),
(32, 'BBA', 1, 1, 'Business Environment', '', 'BBA25-103T', 'FOCBS'),
(33, 'BBA', 1, 1, 'Indian Knowledge System', '', 'BBA25-104T', 'FOCBS'),
(34, 'BBA', 1, 1, 'Business Communication-I', '', 'BBA25-105T', 'FOCBS'),
(35, 'BBA', 1, 1, 'General English', '', 'BBA25-106T', 'FOCBS'),
(36, 'BBA', 1, 1, 'Vedic Management', '', 'BBA25-107T', 'FOCBS'),
(37, 'BBA', 2, 3, 'Cost & Management Accounting', '', 'BBA25-301', 'FOCBS'),
(38, 'BBA', 2, 3, 'Human Resource Management', '', 'BBA25-302', 'FOCBS'),
(39, 'BBA', 2, 3, 'Operations Management', '', 'BBA25-303', 'FOCBS'),
(40, 'BBA', 2, 3, 'Intellectual Property Rights', '', 'BBA25-304', 'FOCBS'),
(41, 'BBA', 2, 3, 'Fundamentals of Computer', '', 'BBA25-305', 'FOCBS'),
(42, 'BBA', 2, 3, 'Management Paradigm from Bhagwat Geeta', '', 'BBA25-306', 'FOCBS'),
(43, 'BBA', 2, 3, 'Management Information System', '', 'BBA25-307', 'FOCBS'),
(44, 'BBA', 3, 5, 'Quantitative Techniques', '', 'MUBBA22-501T', 'FOCBS'),
(45, 'BBA', 3, 5, 'Legal Aspects of Business', '', 'MUBBA22-502T', 'FOCBS'),
(46, 'BBA', 3, 5, 'DSE-1Financial Management Group Working Capital Management', '', 'MUBBA22-503F2', 'FOCBS'),
(47, 'BBA', 3, 5, 'Financial Institutions & Markets', '', 'MUBBA22-503F3', 'FOCBS'),
(48, 'BBA', 3, 5, 'DSE-2 Marketing Management Group Customers Relations Management', '', 'MUBBA22-503M1', 'FOCBS'),
(49, 'BBA', 3, 5, 'Sales & Distribution Management', '', 'MUBBA22-503M3', 'FOCBS'),
(50, 'BBA', 3, 5, 'DSE-3 Financial Management Group Industrial Relations', '', 'MUBBA22-503H1', 'FOCBS'),
(51, 'BBA', 3, 5, 'Training & Development', '', 'MUBBA22-503H2', 'FOCBS'),
(52, 'B.Com', 1, 1, 'Financial Accounting', '', 'BCOM25-101T', 'FOCBS'),
(53, 'B.Com', 1, 1, 'Business Economics', '', 'BCOM25-102T', 'FOCBS'),
(54, 'B.Com', 1, 1, 'Indian Knowledge System', '', 'BCOM25-103T', 'FOCBS'),
(55, 'B.Com', 1, 1, 'English Language', '', 'BCOM25-104T', 'FOCBS'),
(56, 'B.Com', 1, 1, 'Vedic Management', '', 'BCOM25-105T', 'FOCBS'),
(57, 'B.Com', 1, 1, 'Business Communication', '', 'BCOM25-106T', 'FOCBS'),
(58, 'B.Com', 2, 3, 'Cost Accounting', '', 'BCOM25-301T', 'FOCBS'),
(59, 'B.Com', 2, 3, 'Banking & Insurance', '', 'BCOM25-302T', 'FOCBS'),
(60, 'B.Com', 2, 3, 'Business Organisation & Management', '', 'BCOM25-303T', 'FOCBS'),
(61, 'B.Com', 2, 3, 'Intellectual Property Rights', '', 'BCOM25-304T', 'FOCBS'),
(62, 'B.Com', 2, 3, 'Management Paradigm from Bhagwat Geeta', '', 'BCOM25-305T', 'FOCBS'),
(63, 'B.Com', 2, 3, 'Computer Application in Business', '', 'BCOM25-306T', 'FOCBS'),
(64, 'B.Com', 3, 5, 'Computerized Accounting System', '', 'MUCOM22-501T', 'FOCBS'),
(65, 'B.Com', 3, 5, 'Fundamentals of Financial Management', '', 'MUCOM22-502T', 'FOCBS'),
(66, 'B.Com', 3, 5, 'Project Management', '', 'MUCOM22-503T', 'FOCBS'),
(67, 'B.Com', 3, 5, 'Entrepreneurship', '', 'MUCOM22-504T', 'FOCBS'),
(68, 'B.Com', 3, 5, 'Personality Development Through Applied Philosophy of Ramcharitra Manas', '', 'MUCOM22-505T', 'FOCBS'),
(69, 'B.Com (H)', 3, 5, 'Management Accounting', '', 'MUCOH22-501T', 'FOCBS'),
(70, 'B.Com (H)', 3, 5, 'Entrepreneurship & Small Business', '', 'MUCOH22-502T', 'FOCBS'),
(71, 'B.Com (H)', 3, 5, 'Financial Markets & Institutions', '', 'MUCOH22-503F1', 'FOCBS'),
(72, 'B.Com (H)', 3, 5, 'Working Capital Management', '', 'MUCOH22-503F2', 'FOCBS'),
(73, 'B.Com (H)', 3, 5, 'Industrial laws', '', 'MUCOH22-503H1', 'FOCBS'),
(74, 'B.Com (H)', 3, 5, 'Training & Development', '', 'MUCOH22-503H2', 'FOCBS'),
(75, 'B.Com (H)', 3, 5, 'Consumer Behavior', '', 'MUCOH22-503M1', 'FOCBS'),
(76, 'B.Com (H)', 3, 5, 'Marketing of services', '', 'MUCOH22-503M2', 'FOCBS'),
(77, 'B.Com (H)', 3, 5, 'Indian Economy', '', 'MUCOH22-503E1', 'FOCBS'),
(78, 'B.Com (H)', 3, 5, 'Economics of Regulation of Domestic & Foreign Exchange', '', 'MUCOH22-503E2', 'FOCBS'),
(79, 'MBA (Integrated)', 3, 5, 'Financial Management', '', 'MPMBAI24-501', 'FOCBS'),
(80, 'MBA (Integrated)', 3, 5, 'Marketing Management', '', 'MPMBAI24-502', 'FOCBS'),
(81, 'MBA (Integrated)', 3, 5, 'Quantitative Techniques for Managers', '', 'MPMBAI24-503', 'FOCBS'),
(82, 'MBA (Integrated)', 3, 5, 'Human Resource Management', '', 'MPMBAI24-504', 'FOCBS'),
(83, 'MBA (Integrated)', 3, 5, 'Indian Constitution', '', 'MPMBAI24-505', 'FOCBS');

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
(1, 'Dr. Snehashish Bhardwaj', 'FACULTY OF COMMERCE AND BUSINESS STUDIES', 1, ''),
(2, 'Mr. Daleep', 'FACULTY OF COMMERCE AND BUSINESS STUDIES', 2, ''),
(3, 'Mr. Gorav Yadav', 'FACULTY OF COMMERCE AND BUSINESS STUDIES', 3, ''),
(4, 'Dr. Madhu Rani', 'FACULTY OF COMMERCE AND BUSINESS STUDIES', 4, ''),
(5, 'Dr. Neeta Maheshwari', 'FACULTY OF COMMERCE AND BUSINESS STUDIES', 5, ''),
(6, 'Mr. Sachin Kumar', 'FACULTY OF COMMERCE AND BUSINESS STUDIES', 6, ''),
(7, 'Mr. Varnika Tyagi', 'FACULTY OF COMMERCE AND BUSINESS STUDIES', 7, '');

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
  ADD PRIMARY KEY (`course_id`),
  ADD KEY `course_id` (`course_id`);

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=38;

--
-- AUTO_INCREMENT for table `subjected_student`
--
ALTER TABLE `subjected_student`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `subjected_teacher`
--
ALTER TABLE `subjected_teacher`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `subjects`
--
ALTER TABLE `subjects`
  MODIFY `course_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=84;

--
-- AUTO_INCREMENT for table `teachers`
--
ALTER TABLE `teachers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
