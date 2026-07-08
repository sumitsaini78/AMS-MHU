-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 08, 2026 at 01:54 PM
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
(10, 'Sup-Admin', 'super@admin', 10),
(11, 'super-admin', 'super@admin', 11);

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
  `attendance_status` varchar(12) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `attendance`
--

INSERT INTO `attendance` (`id`, `student_name`, `roll_number`, `subject_name`, `subject_code`, `course`, `year`, `semester`, `date_of_attendence`, `attendance_status`) VALUES
(7, 'a', '0', ' Principles and Practice of Management', 'mpmba', 'BBA', 4, 3, 2026, 'Present'),
(8, 'sumit saini', '121212', ' Principles and Practice of Management', 'mpmba', 'BBA', 4, 3, 2026, 'Present'),
(9, 'a', '0', ' Principles and Practice of Management', 'mpmba', 'BBA', 4, 3, 2026, 'Present'),
(10, 'sumit saini', '121212', ' Principles and Practice of Management', 'mpmba', 'BBA', 4, 3, 2026, 'Absent'),
(11, 'a', '0', ' Principles and Practice of Management', 'mpmba', 'BBA', 4, 4, 2026, 'Absent'),
(12, 'sumit saini', '121212', ' Principles and Practice of Management', 'mpmba', 'BBA', 4, 4, 2026, 'Present'),
(13, 'a', '0', ' Principles and Practice of Management', 'kdskjdfs', 'BBA', 4, 3, 2026, 'Absent'),
(14, 'sumit saini', '121212', ' Principles and Practice of Management', 'kdskjdfs', 'BBA', 4, 3, 2026, 'Present'),
(15, 'a', '0', ' Principles and Practice of Management', 'kdskjdfs', 'BBA', 4, 3, 80726, 'Absent'),
(16, 'sumit saini', '121212', ' Principles and Practice of Management', 'kdskjdfs', 'BBA', 4, 3, 80726, 'Absent');

-- --------------------------------------------------------

--
-- Table structure for table `courses`
--

CREATE TABLE `courses` (
  `course_id` int(11) NOT NULL,
  `course_name` varchar(233) NOT NULL,
  `Year` int(11) NOT NULL,
  `semester` int(11) NOT NULL,
  `subject_name` varchar(255) NOT NULL,
  `dept_name` varchar(235) NOT NULL,
  `subject_code` varchar(55) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `courses`
--

INSERT INTO `courses` (`course_id`, `course_name`, `Year`, `semester`, `subject_name`, `dept_name`, `subject_code`) VALUES
(38, 'BBA', 1, 1, ' Principles and Practice of Management', 'FOCBS', 'mubba22-504'),
(39, 'BBA', 1, 1, ' Financial Accountin', 'FOCBS', 'mpmba33-601'),
(40, 'bba', 1, 2, 'kaka', 'FOE', 'mpbba11-211');

-- --------------------------------------------------------

--
-- Table structure for table `deans`
--

CREATE TABLE `deans` (
  `id` int(11) NOT NULL,
  `Dean_name` varchar(222) NOT NULL,
  `Dept_name` varchar(222) NOT NULL,
  `number` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `deans`
--

INSERT INTO `deans` (`id`, `Dean_name`, `Dept_name`, `number`) VALUES
(2, 'Prof. P.K. Agarwal', 'focbs', 22),
(18, 'Prof. P.K. Agarwal', 'focbs', 0),
(19, 'dr. seema tomar', 'FOPS', 0),
(20, 'test', 'FOE', 0);

-- --------------------------------------------------------

--
-- Table structure for table `departments`
--

CREATE TABLE `departments` (
  `id` int(11) NOT NULL,
  `dep_name` varchar(255) NOT NULL,
  `full_name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `departments`
--

INSERT INTO `departments` (`id`, `dep_name`, `full_name`) VALUES
(4, 'FOCBS', 'Faculty of Commerce and Business Studies'),
(5, 'FOECS', 'Faculty of Engineering and Computing Sciences'),
(6, 'FOPS', 'Faculty of Pharmaceutical Sciences'),
(7, 'FOS', 'Faculty of Science'),
(8, 'FOLS', 'Faculty of Legal Studies'),
(9, 'FOE', 'Faculty of Education'),
(10, 'FOAH', 'Faculty of Arts and Humanities'),
(11, 'FOA', 'Faculty of Agriculture'),
(12, 'FOPAHS', 'Faculty of Paramedical and Allied Health Sciences');

-- --------------------------------------------------------

--
-- Table structure for table `students`
--

CREATE TABLE `students` (
  `id` int(11) NOT NULL,
  `name` varchar(122) NOT NULL,
  `roll_number` varchar(122) NOT NULL,
  `faculty` varchar(111) NOT NULL,
  `course` varchar(111) NOT NULL,
  `year` int(11) NOT NULL,
  `sem` int(11) NOT NULL,
  `subject_code` varchar(244) NOT NULL,
  `subject_name` varchar(234) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `students`
--

INSERT INTO `students` (`id`, `name`, `roll_number`, `faculty`, `course`, `year`, `sem`, `subject_code`, `subject_name`) VALUES
(1, 'a', 'a', 'FOECS', 'BBA', 2, 5, 'ad', 'as'),
(2, 'sumit saini', '121212', 'FOCBS', 'bba', 2, 6, 'mba', ''),
(3, 'sumit saini', '121212', 'FOCBS', 'bba', 2, 6, 'mba', '');

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
(12, 3, 38, 'dr. snehashish bhardwaj', ' Principles and Practice of Management', '', 1, 8, 'mpmba'),
(13, 3, 38, 'dr. snehashish bhardwaj', ' Principles and Practice of Management', '', 1, 8, 'kdskjdfs');

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
(3, 'dr. snehashish bhardwaj', 'FOCBS', 333),
(5, 'dr. brajkishore bharti', 'FOCBS', 555);

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
-- Indexes for table `courses`
--
ALTER TABLE `courses`
  ADD PRIMARY KEY (`course_id`);

--
-- Indexes for table `deans`
--
ALTER TABLE `deans`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `departments`
--
ALTER TABLE `departments`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `students`
--
ALTER TABLE `students`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `subjected_teacher`
--
ALTER TABLE `subjected_teacher`
  ADD PRIMARY KEY (`id`);

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `courses`
--
ALTER TABLE `courses`
  MODIFY `course_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=41;

--
-- AUTO_INCREMENT for table `deans`
--
ALTER TABLE `deans`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `departments`
--
ALTER TABLE `departments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `students`
--
ALTER TABLE `students`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `subjected_teacher`
--
ALTER TABLE `subjected_teacher`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `teachers`
--
ALTER TABLE `teachers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
