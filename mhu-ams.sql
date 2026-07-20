-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 20, 2026 at 10:56 AM
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
(1, 'super-admin', 'super@admin', 1),
(10, 'Sup-Admin', 'super@admin', 10);

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
  `teacher_name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `attendance`
--

INSERT INTO `attendance` (`id`, `student_name`, `roll_number`, `subject_name`, `subject_code`, `course`, `year`, `semester`, `date_of_attendence`, `attendance_status`, `teacher_name`) VALUES
(23, 'Aarav Sharma', '26CSE001', ' Principles and Practice of Management', 'kdskjdfs', 'BBA', 4, 4, 130726, 'Present', ''),
(24, 'Ananya Iyer', '25ECE024', ' Principles and Practice of Management', 'kdskjdfs', 'BBA', 4, 4, 130726, 'Present', ''),
(25, 'Rohan Verma', '24MECH015', ' Principles and Practice of Management', 'kdskjdfs', 'BBA', 4, 4, 130726, 'Present', ''),
(26, 'Diya Nair', '23CSE042', ' Principles and Practice of Management', 'kdskjdfs', 'BBA', 4, 4, 130726, 'Present', ''),
(27, 'Ishaan Gupta', '26BBA009', ' Principles and Practice of Management', 'kdskjdfs', 'BBA', 4, 4, 130726, 'Present', ''),
(28, 'Meera Joshi', '25MBA054', ' Principles and Practice of Management', 'kdskjdfs', 'BBA', 4, 4, 130726, 'Absent', ''),
(29, 'Aditya Rao', '26BCA012', ' Principles and Practice of Management', 'kdskjdfs', 'BBA', 4, 4, 130726, 'Absent', ''),
(30, 'Kavya Patel', '24MCA031', ' Principles and Practice of Management', 'kdskjdfs', 'BBA', 4, 4, 130726, 'Absent', ''),
(31, 'Vivaan Saxena', '25LAW004', ' Principles and Practice of Management', 'kdskjdfs', 'BBA', 4, 4, 130726, 'Absent', ''),
(32, 'Sanya Mirza', '23LAW048', ' Principles and Practice of Management', 'kdskjdfs', 'BBA', 4, 4, 130726, 'Absent', ''),
(33, 'Aarav Sharma', '26CSE001', 'sub1', 'mpmba', 'BBA', 3, 4, 130726, 'Absent', ''),
(34, 'Ananya Iyer', '25ECE024', 'sub1', 'mpmba', 'BBA', 3, 4, 130726, 'Absent', ''),
(35, 'Rohan Verma', '24MECH015', 'sub1', 'mpmba', 'BBA', 3, 4, 130726, 'Absent', ''),
(36, 'Diya Nair', '23CSE042', 'sub1', 'mpmba', 'BBA', 3, 4, 130726, 'Present', ''),
(37, 'Ishaan Gupta', '26BBA009', 'sub1', 'mpmba', 'BBA', 3, 4, 130726, 'Present', ''),
(38, 'Meera Joshi', '25MBA054', 'sub1', 'mpmba', 'BBA', 3, 4, 120726, 'Present', ''),
(39, 'Aditya Rao', '26BCA012', 'sub1', 'mpmba', 'BBA', 3, 4, 130726, 'Present', ''),
(40, 'Kavya Patel', '24MCA031', 'sub1', 'mpmba', 'BBA', 3, 4, 130726, 'Present', ''),
(41, 'Vivaan Saxena', '25LAW004', 'sub1', 'mpmba', 'BBA', 3, 4, 130726, 'Present', ''),
(42, 'Sanya Mirza', '23LAW048', 'sub1', 'mpmba', 'BBA', 3, 4, 130726, 'Present', ''),
(43, 'Aarav Sharma', '26CSE001', ' Principles and Practice of Management', 'kdskjdfs', 'BBA', 4, 4, 130726, 'Present', ''),
(44, 'Ananya Iyer', '25ECE024', ' Principles and Practice of Management', 'kdskjdfs', 'BBA', 4, 4, 130726, 'Present', ''),
(45, 'Rohan Verma', '24MECH015', ' Principles and Practice of Management', 'kdskjdfs', 'BBA', 4, 4, 130726, 'Present', ''),
(46, 'Diya Nair', '23CSE042', ' Principles and Practice of Management', 'kdskjdfs', 'BBA', 4, 4, 130726, 'Absent', ''),
(47, 'Ishaan Gupta', '26BBA009', ' Principles and Practice of Management', 'kdskjdfs', 'BBA', 4, 4, 130726, 'Present', ''),
(48, 'Meera Joshi', '25MBA054', ' Principles and Practice of Management', 'kdskjdfs', 'BBA', 4, 4, 130726, 'Absent', ''),
(49, 'Aditya Rao', '26BCA012', ' Principles and Practice of Management', 'kdskjdfs', 'BBA', 4, 4, 130726, 'Present', ''),
(50, 'Kavya Patel', '24MCA031', ' Principles and Practice of Management', 'kdskjdfs', 'BBA', 4, 4, 130726, 'Present', ''),
(51, 'Vivaan Saxena', '25LAW004', ' Principles and Practice of Management', 'kdskjdfs', 'BBA', 4, 4, 130726, 'Present', ''),
(52, 'Sanya Mirza', '23LAW048', ' Principles and Practice of Management', 'kdskjdfs', 'BBA', 4, 4, 130726, 'Present', ''),
(53, 'Aarav Sharma', '26CSE001', ' Principles and Practice of Management', 'kdskjdfs', 'BBA', 4, 3, 140726, 'Absent', ''),
(54, 'Ananya Iyer', '25ECE024', ' Principles and Practice of Management', 'kdskjdfs', 'BBA', 4, 3, 140726, 'Absent', ''),
(55, 'Rohan Verma', '24MECH015', ' Principles and Practice of Management', 'kdskjdfs', 'BBA', 4, 3, 140726, 'Present', ''),
(56, 'Diya Nair', '23CSE042', ' Principles and Practice of Management', 'kdskjdfs', 'BBA', 4, 3, 140726, 'Present', ''),
(57, 'Ishaan Gupta', '26BBA009', ' Principles and Practice of Management', 'kdskjdfs', 'BBA', 4, 3, 140726, 'Present', ''),
(58, 'Meera Joshi', '25MBA054', ' Principles and Practice of Management', 'kdskjdfs', 'BBA', 4, 3, 140726, 'Absent', ''),
(59, 'Aditya Rao', '26BCA012', ' Principles and Practice of Management', 'kdskjdfs', 'BBA', 4, 3, 140726, 'Absent', ''),
(60, 'Kavya Patel', '24MCA031', ' Principles and Practice of Management', 'kdskjdfs', 'BBA', 4, 3, 140726, 'Absent', ''),
(61, 'Vivaan Saxena', '25LAW004', ' Principles and Practice of Management', 'kdskjdfs', 'BBA', 4, 3, 140726, 'Absent', ''),
(62, 'Sanya Mirza', '23LAW048', ' Principles and Practice of Management', 'kdskjdfs', 'BBA', 4, 3, 140726, 'Absent', ''),
(63, 'Aarav Sharma', '26CSE001', 'sub1', 'kdskjdfs', 'BBA', 4, 3, 140726, 'Absent', ''),
(64, 'Ananya Iyer', '25ECE024', 'sub1', 'kdskjdfs', 'BBA', 4, 3, 140726, 'Present', ''),
(65, 'Rohan Verma', '24MECH015', 'sub1', 'kdskjdfs', 'BBA', 4, 3, 140726, 'Present', ''),
(66, 'Diya Nair', '23CSE042', 'sub1', 'kdskjdfs', 'BBA', 4, 3, 140726, 'Present', ''),
(67, 'Ishaan Gupta', '26BBA009', 'sub1', 'kdskjdfs', 'BBA', 4, 3, 140726, 'Absent', ''),
(68, 'Meera Joshi', '25MBA054', 'sub1', 'kdskjdfs', 'BBA', 4, 3, 140726, 'Absent', ''),
(69, 'Aditya Rao', '26BCA012', 'sub1', 'kdskjdfs', 'BBA', 4, 3, 140726, 'Present', ''),
(70, 'Kavya Patel', '24MCA031', 'sub1', 'kdskjdfs', 'BBA', 4, 3, 140726, 'Present', ''),
(71, 'Vivaan Saxena', '25LAW004', 'sub1', 'kdskjdfs', 'BBA', 4, 3, 140726, 'Absent', ''),
(72, 'Sanya Mirza', '23LAW048', 'sub1', 'kdskjdfs', 'BBA', 4, 3, 140726, 'Absent', ''),
(73, 'Aarav Sharma', '26CSE001', ' Principles and Practice of Management', 'mpmba', 'BBA', 3, 2, 140726, 'Present', ''),
(74, 'Ananya Iyer', '25ECE024', ' Principles and Practice of Management', 'mpmba', 'BBA', 3, 2, 140726, 'Absent', ''),
(75, 'Rohan Verma', '24MECH015', ' Principles and Practice of Management', 'mpmba', 'BBA', 3, 2, 140726, 'Absent', ''),
(76, 'Diya Nair', '23CSE042', ' Principles and Practice of Management', 'mpmba', 'BBA', 3, 2, 140726, 'Present', ''),
(77, 'Ishaan Gupta', '26BBA009', ' Principles and Practice of Management', 'mpmba', 'BBA', 3, 2, 140726, 'Absent', ''),
(78, 'Meera Joshi', '25MBA054', ' Principles and Practice of Management', 'mpmba', 'BBA', 3, 2, 140726, 'Absent', ''),
(79, 'Aditya Rao', '26BCA012', ' Principles and Practice of Management', 'mpmba', 'BBA', 3, 2, 140726, 'Present', ''),
(80, 'Kavya Patel', '24MCA031', ' Principles and Practice of Management', 'mpmba', 'BBA', 3, 2, 140726, 'Absent', ''),
(81, 'Vivaan Saxena', '25LAW004', ' Principles and Practice of Management', 'mpmba', 'BBA', 3, 2, 140726, 'Present', ''),
(82, 'Sanya Mirza', '23LAW048', ' Principles and Practice of Management', 'mpmba', 'BBA', 3, 2, 140726, 'Absent', ''),
(83, 'Aarav Sharma', '26CSE001', ' Principles and Practice of Management', 'mpmba', 'BBA', 4, 4, 140726, 'Present', ''),
(84, 'Ananya Iyer', '25ECE024', ' Principles and Practice of Management', 'mpmba', 'BBA', 4, 4, 140726, 'Present', ''),
(85, 'Rohan Verma', '24MECH015', ' Principles and Practice of Management', 'mpmba', 'BBA', 4, 4, 140726, 'Present', ''),
(86, 'Diya Nair', '23CSE042', ' Principles and Practice of Management', 'mpmba', 'BBA', 4, 4, 140726, 'Present', ''),
(87, 'Ishaan Gupta', '26BBA009', ' Principles and Practice of Management', 'mpmba', 'BBA', 4, 4, 140726, 'Present', ''),
(88, 'Meera Joshi', '25MBA054', ' Principles and Practice of Management', 'mpmba', 'BBA', 4, 4, 140726, 'Present', ''),
(89, 'Aditya Rao', '26BCA012', ' Principles and Practice of Management', 'mpmba', 'mca', 4, 4, 140726, 'Present', ''),
(90, 'Kavya Patel', '24MCA031', ' Principles and Practice of Management', 'mpmba', 'mcom', 4, 4, 140726, 'Present', ''),
(91, 'Vivaan Saxena', '25LAW004', ' Principles and Practice of Management', 'mpmba', 'mba', 4, 4, 140726, 'Present', ''),
(92, 'Sanya Mirza', '23LAW048', ' Principles and Practice of Management', 'mpmba', 'BBA', 4, 4, 140726, 'Present', ''),
(93, 'Aarav Sharma', '26CSE001', ' Principles and Practice of Management', 'mpmba', 'bcom', 4, 4, 140726, 'Absent', ''),
(94, 'Ananya Iyer', '25ECE024', ' Principles and Practice of Management', 'mpmba', 'BBA', 4, 4, 140726, 'Absent', ''),
(95, 'Rohan Verma', '24MECH015', ' Principles and Practice of Management', 'mpmba', 'BBA', 4, 4, 140726, 'Absent', ''),
(96, 'Diya Nair', '23CSE042', ' Principles and Practice of Management', 'mpmba', 'BBA', 4, 4, 140726, 'Present', ''),
(97, 'Ishaan Gupta', '26BBA009', ' Principles and Practice of Management', 'mpmba', 'BBA', 4, 4, 140726, 'Absent', ''),
(98, 'Meera Joshi', '25MBA054', ' Principles and Practice of Management', 'mpmba', 'BBA', 4, 4, 140726, 'Present', ''),
(99, 'Aditya Rao', '26BCA012', ' Principles and Practice of Management', 'mpmba', 'BBA', 4, 4, 140726, 'Present', ''),
(100, 'Kavya Patel', '24MCA031', ' Principles and Practice of Management', 'mpmba', 'BBA', 4, 4, 140726, 'Absent', ''),
(101, 'Vivaan Saxena', '25LAW004', ' Principles and Practice of Management', 'mpmba', 'BBA', 4, 4, 140726, 'Absent', ''),
(102, 'Sanya Mirza', '23LAW048', ' Principles and Practice of Management', 'mpmba', 'BBA', 4, 4, 140726, 'Absent', ''),
(103, 'Aarav Sharma', '26CSE001', 'fa', 'kdskjdfs', 'BBA', 3, 2, 140726, 'Absent', ''),
(104, 'Ananya Iyer', '25ECE024', 'fa', 'kdskjdfs', 'BBA', 3, 2, 140726, 'Absent', ''),
(105, 'Rohan Verma', '24MECH015', 'fa', 'kdskjdfs', 'BBA', 3, 2, 140726, 'Absent', ''),
(106, 'Diya Nair', '23CSE042', 'fa', 'kdskjdfs', 'BBA', 3, 2, 140726, 'Present', ''),
(107, 'Ishaan Gupta', '26BBA009', 'fa', 'kdskjdfs', 'BBA', 3, 2, 140726, 'Absent', ''),
(108, 'Meera Joshi', '25MBA054', 'fa', 'kdskjdfs', 'BBA', 3, 2, 140726, 'Absent', ''),
(109, 'Aditya Rao', '26BCA012', 'fa', 'kdskjdfs', 'BBA', 3, 2, 140726, 'Absent', ''),
(110, 'Kavya Patel', '24MCA031', 'fa', 'kdskjdfs', 'BBA', 3, 2, 140726, 'Absent', ''),
(111, 'Vivaan Saxena', '25LAW004', 'fa', 'kdskjdfs', 'BBA', 3, 2, 140726, 'Absent', ''),
(112, 'Sanya Mirza', '23LAW048', 'fa', 'kdskjdfs', 'BBA', 3, 2, 140726, 'Absent', ''),
(113, 'Aarav Sharma', '26CSE001', ' Principles and Practice of Management', 'mpmba', 'BBA', 3, 4, 140726, 'Present', ''),
(114, 'Ananya Iyer', '25ECE024', ' Principles and Practice of Management', 'mpmba', 'BBA', 3, 4, 140726, 'Absent', ''),
(115, 'Rohan Verma', '24MECH015', ' Principles and Practice of Management', 'mpmba', 'BBA', 3, 4, 140726, 'Absent', ''),
(116, 'Diya Nair', '23CSE042', ' Principles and Practice of Management', 'mpmba', 'BBA', 3, 4, 140726, 'Present', ''),
(117, 'Ishaan Gupta', '26BBA009', ' Principles and Practice of Management', 'mpmba', 'BBA', 3, 4, 140726, 'Absent', ''),
(118, 'Meera Joshi', '25MBA054', ' Principles and Practice of Management', 'mpmba', 'BBA', 3, 4, 140726, 'Absent', ''),
(119, 'Aditya Rao', '26BCA012', ' Principles and Practice of Management', 'mpmba', 'BBA', 3, 4, 140726, 'Absent', ''),
(120, 'Kavya Patel', '24MCA031', ' Principles and Practice of Management', 'mpmba', 'BBA', 3, 4, 140726, 'Absent', ''),
(121, 'Vivaan Saxena', '25LAW004', ' Principles and Practice of Management', 'mpmba', 'BBA', 3, 4, 140726, 'Absent', ''),
(122, 'Sanya Mirza', '23LAW048', ' Principles and Practice of Management', 'mpmba', 'BBA', 3, 4, 140726, 'Absent', ''),
(123, 'Aarav Sharma', '26CSE001', ' Principles and Practice of Management', 'mpmba', 'BBA', 3, 4, 140726, 'Absent', ''),
(124, 'Ananya Iyer', '25ECE024', ' Principles and Practice of Management', 'mpmba', 'BBA', 3, 4, 140726, 'Present', ''),
(125, 'Rohan Verma', '24MECH015', ' Principles and Practice of Management', 'mpmba', 'BBA', 3, 4, 140726, 'Absent', ''),
(126, 'Diya Nair', '23CSE042', ' Principles and Practice of Management', 'mpmba', 'BBA', 3, 4, 140726, 'Present', ''),
(127, 'Ishaan Gupta', '26BBA009', ' Principles and Practice of Management', 'mpmba', 'BBA', 3, 4, 140726, 'Absent', ''),
(128, 'Meera Joshi', '25MBA054', ' Principles and Practice of Management', 'mpmba', 'BBA', 3, 4, 140726, 'Absent', ''),
(129, 'Aditya Rao', '26BCA012', ' Principles and Practice of Management', 'mpmba', 'BBA', 3, 4, 120726, 'Absent', ''),
(130, 'Kavya Patel', '24MCA031', ' Principles and Practice of Management', 'mpmba', 'BBA', 3, 4, 140726, 'Absent', ''),
(131, 'Vivaan Saxena', '25LAW004', ' Principles and Practice of Management', 'mpmba', 'BBA', 3, 4, 140726, 'Absent', ''),
(132, 'Sanya Mirza', '23LAW048', ' Principles and Practice of Management', 'mpmba', 'BBA', 3, 4, 140726, 'Absent', ''),
(133, 'Aarav Sharma', '26CSE001', 'fa', 'mpmba', 'BBA', 4, 1, 140726, 'Present', ''),
(134, 'Ananya Iyer', '25ECE024', 'fa', 'mpmba', 'BBA', 4, 1, 140726, 'Present', ''),
(135, 'Rohan Verma', '24MECH015', 'fa', 'mpmba', 'BBA', 4, 1, 140726, 'Present', ''),
(136, 'Diya Nair', '23CSE042', 'fa', 'mpmba', 'BBA', 4, 1, 140726, 'Present', ''),
(137, 'Ishaan Gupta', '26BBA009', 'fa', 'mpmba', 'BBA', 4, 1, 140726, 'Present', ''),
(138, 'Meera Joshi', '25MBA054', 'fa', 'mpmba', 'BBA', 4, 1, 140726, 'Present', ''),
(139, 'Aditya Rao', '26BCA012', 'fa', 'mpmba', 'BBA', 4, 1, 140726, 'Present', ''),
(140, 'Kavya Patel', '24MCA031', 'fa', 'mpmba', 'BBA', 4, 1, 140726, 'Present', ''),
(141, 'Vivaan Saxena', '25LAW004', 'fa', 'mpmba', 'BBA', 4, 1, 140726, 'Present', ''),
(142, 'Sanya Mirza', '23LAW048', 'fa', 'mpmba', 'BBA', 4, 1, 140726, 'Present', ''),
(143, 'Aarav Sharma', '26CSE001', ' Principles and Practice of Management', 'mpmba', '', 1, 8, 150726, 'Absent', ''),
(144, 'Ananya Iyer', '25ECE024', ' Principles and Practice of Management', 'mpmba', '', 1, 8, 150726, 'Absent', ''),
(145, 'Rohan Verma', '24MECH015', ' Principles and Practice of Management', 'mpmba', '', 1, 8, 150726, 'Absent', ''),
(146, 'Diya Nair', '23CSE042', ' Principles and Practice of Management', 'mpmba', '', 1, 8, 150726, 'Present', ''),
(147, 'Ishaan Gupta', '26BBA009', ' Principles and Practice of Management', 'mpmba', '', 1, 8, 150726, 'Present', ''),
(148, 'Meera Joshi', '25MBA054', ' Principles and Practice of Management', 'mpmba', '', 1, 8, 150726, 'Absent', ''),
(149, 'Aditya Rao', '26BCA012', ' Principles and Practice of Management', 'mpmba', '', 1, 8, 150726, 'Absent', ''),
(150, 'Kavya Patel', '24MCA031', ' Principles and Practice of Management', 'mpmba', '', 1, 8, 150726, 'Absent', ''),
(151, 'Vivaan Saxena', '25LAW004', ' Principles and Practice of Management', 'mpmba', '', 1, 8, 150726, 'Absent', ''),
(152, 'Sanya Mirza', '23LAW048', ' Principles and Practice of Management', 'mpmba', '', 1, 8, 150726, 'Absent', ''),
(153, 'Aarav Sharma', '26CSE001', 'sub1', 'N/A', '', 2, 7, 150726, 'Present', ''),
(154, 'Ananya Iyer', '25ECE024', 'sub1', 'N/A', '', 2, 7, 150726, 'Absent', ''),
(155, 'Rohan Verma', '24MECH015', 'sub1', 'N/A', '', 2, 7, 150726, 'Absent', ''),
(156, 'Diya Nair', '23CSE042', 'sub1', 'N/A', '', 2, 7, 150726, 'Absent', ''),
(157, 'Ishaan Gupta', '26BBA009', 'sub1', 'N/A', '', 2, 7, 150726, 'Absent', ''),
(158, 'Meera Joshi', '25MBA054', 'sub1', 'N/A', '', 2, 7, 150726, 'Absent', ''),
(159, 'Aditya Rao', '26BCA012', 'sub1', 'N/A', '', 2, 7, 150726, 'Absent', ''),
(160, 'Kavya Patel', '24MCA031', 'sub1', 'N/A', '', 2, 7, 150726, 'Absent', ''),
(161, 'Vivaan Saxena', '25LAW004', 'sub1', 'N/A', '', 2, 7, 150726, 'Present', ''),
(162, 'Sanya Mirza', '23LAW048', 'sub1', 'N/A', '', 2, 7, 150726, 'Absent', ''),
(163, 'Aarav Sharma', '26CSE001', ' Principles and Practice of Management', 'mpmba', '', 1, 8, 150726, 'Present', ''),
(164, 'Ananya Iyer', '25ECE024', ' Principles and Practice of Management', 'mpmba', '', 1, 8, 150726, 'Present', ''),
(165, 'Rohan Verma', '24MECH015', ' Principles and Practice of Management', 'mpmba', '', 1, 8, 150726, 'Present', ''),
(166, 'Diya Nair', '23CSE042', ' Principles and Practice of Management', 'mpmba', '', 1, 8, 150726, 'Absent', ''),
(167, 'Ishaan Gupta', '26BBA009', ' Principles and Practice of Management', 'mpmba', '', 1, 8, 150726, 'Present', ''),
(168, 'Meera Joshi', '25MBA054', ' Principles and Practice of Management', 'mpmba', '', 1, 8, 150726, 'Absent', ''),
(169, 'Aditya Rao', '26BCA012', ' Principles and Practice of Management', 'mpmba', '', 1, 8, 150726, 'Absent', ''),
(170, 'Kavya Patel', '24MCA031', ' Principles and Practice of Management', 'mpmba', '', 1, 8, 150726, 'Absent', ''),
(171, 'Vivaan Saxena', '25LAW004', ' Principles and Practice of Management', 'mpmba', '', 1, 8, 150726, 'Absent', ''),
(172, 'Sanya Mirza', '23LAW048', ' Principles and Practice of Management', 'mpmba', '', 1, 8, 150726, 'Absent', ''),
(173, 'Aarav Sharma', '26CSE001', ' Principles and Practice of Management', 'mpmba', '', 1, 8, 150726, 'Absent', ''),
(174, 'Ananya Iyer', '25ECE024', ' Principles and Practice of Management', 'mpmba', '', 1, 8, 150726, 'Absent', ''),
(175, 'Rohan Verma', '24MECH015', ' Principles and Practice of Management', 'mpmba', '', 1, 8, 150726, 'Absent', ''),
(176, 'Diya Nair', '23CSE042', ' Principles and Practice of Management', 'mpmba', '', 1, 8, 150726, 'Present', ''),
(177, 'Ishaan Gupta', '26BBA009', ' Principles and Practice of Management', 'mpmba', '', 1, 8, 150726, 'Present', ''),
(178, 'Meera Joshi', '25MBA054', ' Principles and Practice of Management', 'mpmba', '', 1, 8, 150726, 'Present', ''),
(179, 'Aditya Rao', '26BCA012', ' Principles and Practice of Management', 'mpmba', '', 1, 8, 150726, 'Absent', ''),
(180, 'Kavya Patel', '24MCA031', ' Principles and Practice of Management', 'mpmba', '', 1, 8, 150726, 'Absent', ''),
(181, 'Vivaan Saxena', '25LAW004', ' Principles and Practice of Management', 'mpmba', '', 1, 8, 150726, 'Absent', ''),
(182, 'Sanya Mirza', '23LAW048', ' Principles and Practice of Management', 'mpmba', '', 1, 8, 150726, 'Absent', ''),
(183, ' ABHIJEET PANWAR ', '2506000002', ' Principles and Practice of Management', 'mpmba', '', 1, 8, 150726, 'Present', ''),
(184, ' ABHINAV KUMAR ', '2506000059', ' Principles and Practice of Management', 'mpmba', '', 1, 8, 150726, 'Present', ''),
(185, ' ABHISHEK ', '2506000003', ' Principles and Practice of Management', 'mpmba', '', 1, 8, 150726, 'Present', ''),
(186, ' ADITI SHARMA ', '2506000004', ' Principles and Practice of Management', 'mpmba', '', 1, 8, 150726, 'Absent', ''),
(187, ' ANANT JAIN ', '2506000005', ' Principles and Practice of Management', 'mpmba', '', 1, 8, 150726, 'Absent', ''),
(188, ' ANCHAL ', '2506000046', ' Principles and Practice of Management', 'mpmba', '', 1, 8, 150726, 'Absent', ''),
(189, ' ANJALI GUPTA ', '2506000007', ' Principles and Practice of Management', 'mpmba', '', 1, 8, 150726, 'Absent', ''),
(190, ' ANJALI PAL ', '2506000008', ' Principles and Practice of Management', 'mpmba', '', 1, 8, 150726, 'Absent', ''),
(191, ' ANSHIKA YADAV ', '2506000052', ' Principles and Practice of Management', 'mpmba', '', 1, 8, 150726, 'Absent', ''),
(192, ' ANURAG CHOUDHARY ', '2506000061', ' Principles and Practice of Management', 'mpmba', '', 1, 8, 150726, 'Absent', ''),
(193, ' ARNIKA KAMBOJ ', '2506000009', ' Principles and Practice of Management', 'mpmba', '', 1, 8, 150726, 'Absent', ''),
(194, ' ARYAN SAINI ', '2506000010', ' Principles and Practice of Management', 'mpmba', '', 1, 8, 150726, 'Absent', ''),
(195, ' ARYAV KUMAR ', '2506000058', ' Principles and Practice of Management', 'mpmba', '', 1, 8, 150726, 'Absent', ''),
(196, ' ASIF ', '2506000011', ' Principles and Practice of Management', 'mpmba', '', 1, 8, 150726, 'Absent', ''),
(197, ' BUNISH KUMAR SAINI ', '2506000012', ' Principles and Practice of Management', 'mpmba', '', 1, 8, 150726, 'Absent', ''),
(198, ' DEEN DYAL ', '2506000013', ' Principles and Practice of Management', 'mpmba', '', 1, 8, 150726, 'Absent', ''),
(199, ' DISHU ', '2506000014', ' Principles and Practice of Management', 'mpmba', '', 1, 8, 150726, 'Absent', ''),
(200, ' HARSH GIRI ', '2506000015', ' Principles and Practice of Management', 'mpmba', '', 1, 8, 150726, 'Absent', ''),
(201, ' HARSH NAMDEV ', '2506000056', ' Principles and Practice of Management', 'mpmba', '', 1, 8, 150726, 'Absent', ''),
(202, ' HARSH SHARMA ', '2506000055', ' Principles and Practice of Management', 'mpmba', '', 1, 8, 150726, 'Absent', ''),
(203, ' HARSH SHARMA ', '2506000047', ' Principles and Practice of Management', 'mpmba', '', 1, 8, 150726, 'Absent', ''),
(204, ' HIMANI SHARMA ', '2506000016', ' Principles and Practice of Management', 'mpmba', '', 1, 8, 150726, 'Absent', ''),
(205, ' HRITIK ', '2506000065', ' Principles and Practice of Management', 'mpmba', '', 1, 8, 150726, 'Absent', ''),
(206, ' JAISHREE ', '2506000017', ' Principles and Practice of Management', 'mpmba', '', 1, 8, 150726, 'Absent', ''),
(207, ' KANNU DEVI ', '2506000018', ' Principles and Practice of Management', 'mpmba', '', 1, 8, 150726, 'Absent', ''),
(208, ' KAPIL SAINI ', '2506000019', ' Principles and Practice of Management', 'mpmba', '', 1, 8, 150726, 'Absent', ''),
(209, ' KHUSHI KUMARI ', '2506000020', ' Principles and Practice of Management', 'mpmba', '', 1, 8, 150726, 'Absent', ''),
(210, ' KM ANCHAL SAINI ', '2506000021', ' Principles and Practice of Management', 'mpmba', '', 1, 8, 150726, 'Absent', ''),
(211, ' KM ANJALI ', '2506000006', ' Principles and Practice of Management', 'mpmba', '', 1, 8, 150726, 'Absent', ''),
(212, ' KM PREETI ', '2506000022', ' Principles and Practice of Management', 'mpmba', '', 1, 8, 150726, 'Absent', ''),
(213, ' MANSAVI ', '2506000023', ' Principles and Practice of Management', 'mpmba', '', 1, 8, 150726, 'Absent', ''),
(214, ' NEERAJ KUMAR ', '2506000024', ' Principles and Practice of Management', 'mpmba', '', 1, 8, 150726, 'Absent', ''),
(215, ' NEHA KASHYAP ', '2506000025', ' Principles and Practice of Management', 'mpmba', '', 1, 8, 150726, 'Absent', ''),
(216, ' NIKHIL ', '2506000060', ' Principles and Practice of Management', 'mpmba', '', 1, 8, 150726, 'Absent', ''),
(217, ' NISHANT ', '2506000026', ' Principles and Practice of Management', 'mpmba', '', 1, 8, 150726, 'Absent', ''),
(218, ' NISHU SHARMA ', '2506000027', ' Principles and Practice of Management', 'mpmba', '', 1, 8, 150726, 'Absent', ''),
(219, ' NITIN CHAUDHARY ', '2506000053', ' Principles and Practice of Management', 'mpmba', '', 1, 8, 150726, 'Absent', ''),
(220, ' PRACHI ', '2506000028', ' Principles and Practice of Management', 'mpmba', '', 1, 8, 150726, 'Absent', ''),
(221, ' PRAJJWAL SHARMA ', '2506000048', ' Principles and Practice of Management', 'mpmba', '', 1, 8, 150726, 'Absent', ''),
(222, ' PRAKHAR GOEL ', '2506000029', ' Principles and Practice of Management', 'mpmba', '', 1, 8, 150726, 'Absent', ''),
(223, ' RADHIKA ', '2506000030', ' Principles and Practice of Management', 'mpmba', '', 1, 8, 150726, 'Absent', ''),
(224, ' RISHABH BIJALWAN', '2506000062', ' Principles and Practice of Management', 'mpmba', '', 1, 8, 150726, 'Absent', ''),
(225, ' RIYA TYAGI ', '2506000063', ' Principles and Practice of Management', 'mpmba', '', 1, 8, 150726, 'Absent', ''),
(226, ' SACHIN KUMAR ', '2506000031', ' Principles and Practice of Management', 'mpmba', '', 1, 8, 150726, 'Absent', ''),
(227, ' SAGAR GHOTNA ', '2506000032', ' Principles and Practice of Management', 'mpmba', '', 1, 8, 150726, 'Absent', ''),
(228, ' SAKSHI ', '2506000033', ' Principles and Practice of Management', 'mpmba', '', 1, 8, 150726, 'Absent', ''),
(229, ' SALONI ', '2506000034', ' Principles and Practice of Management', 'mpmba', '', 1, 8, 150726, 'Absent', ''),
(230, ' SHIKHA ', '2506000035', ' Principles and Practice of Management', 'mpmba', '', 1, 8, 150726, 'Absent', ''),
(231, ' SHIVAM ', '2506000036', ' Principles and Practice of Management', 'mpmba', '', 1, 8, 150726, 'Absent', ''),
(232, ' SHIVANK TRIPATHI ', '2506000037', ' Principles and Practice of Management', 'mpmba', '', 1, 8, 150726, 'Absent', ''),
(233, ' SUJAL KUMAR ', '2506000038', ' Principles and Practice of Management', 'mpmba', '', 1, 8, 150726, 'Absent', ''),
(234, ' SUWECHA SINGH ', '2506000054', ' Principles and Practice of Management', 'mpmba', '', 1, 8, 150726, 'Absent', ''),
(235, ' TANISHKA TYAGI ', '2506000039', ' Principles and Practice of Management', 'mpmba', '', 1, 8, 150726, 'Absent', ''),
(236, ' TANU SAHRAWAT ', '2506000057', ' Principles and Practice of Management', 'mpmba', '', 1, 8, 150726, 'Absent', ''),
(237, ' TARUN TOMAR ', '2506000040', ' Principles and Practice of Management', 'mpmba', '', 1, 8, 150726, 'Absent', ''),
(238, ' UJJAWAL SHARMA ', '2506000041', ' Principles and Practice of Management', 'mpmba', '', 1, 8, 150726, 'Absent', ''),
(239, ' VANSHIKA PAL ', '2506000042', ' Principles and Practice of Management', 'mpmba', '', 1, 8, 150726, 'Absent', ''),
(240, ' VARTIKA CHOUDHARY ', '2506000043', ' Principles and Practice of Management', 'mpmba', '', 1, 8, 150726, 'Absent', ''),
(241, ' VEDANSHI ', '2506000044', ' Principles and Practice of Management', 'mpmba', '', 1, 8, 150726, 'Absent', ''),
(242, ' YASH TYAGI ', '2506000064', ' Principles and Practice of Management', 'mpmba', '', 1, 8, 150726, 'Absent', ''),
(243, ' YATIN KUMAR ', '2506000045', ' Principles and Practice of Management', 'mpmba', '', 1, 8, 150726, 'Absent', ''),
(244, 'asaq', '2123', ' Principles and Practice of Management', 'mpmba', '', 1, 8, 150726, 'Absent', ''),
(245, 'Aarav Sharma', '26CSE001', ' Principles and Practice of Management', 'mpmba', '', 1, 8, 150726, 'Absent', ''),
(246, 'Ananya Iyer', '25ECE024', ' Principles and Practice of Management', 'mpmba', '', 1, 8, 150726, 'Absent', ''),
(247, 'Rohan Verma', '24MECH015', ' Principles and Practice of Management', 'mpmba', '', 1, 8, 150726, 'Absent', ''),
(248, 'Diya Nair', '23CSE042', ' Principles and Practice of Management', 'mpmba', '', 1, 8, 150726, 'Absent', ''),
(249, 'Ishaan Gupta', '26BBA009', ' Principles and Practice of Management', 'mpmba', '', 1, 8, 150726, 'Absent', ''),
(250, 'Meera Joshi', '25MBA054', ' Principles and Practice of Management', 'mpmba', '', 1, 8, 150726, 'Absent', ''),
(251, 'Aditya Rao', '26BCA012', ' Principles and Practice of Management', 'mpmba', '', 1, 8, 150726, 'Absent', ''),
(252, 'Kavya Patel', '24MCA031', ' Principles and Practice of Management', 'mpmba', '', 1, 8, 150726, 'Absent', ''),
(253, 'Vivaan Saxena', '25LAW004', ' Principles and Practice of Management', 'mpmba', '', 1, 8, 150726, 'Absent', ''),
(254, 'Sanya Mirza', '23LAW048', ' Principles and Practice of Management', 'mpmba', '', 1, 8, 150726, 'Absent', ''),
(255, ' ABHIJEET PANWAR ', '2506000002', 'sub1', 'N/A', '', 2, 7, 150726, 'Absent', ''),
(256, ' ABHINAV KUMAR ', '2506000059', 'sub1', 'N/A', '', 2, 7, 150726, 'Absent', ''),
(257, ' ABHISHEK ', '2506000003', 'sub1', 'N/A', '', 2, 7, 150726, 'Absent', ''),
(258, ' ADITI SHARMA ', '2506000004', 'sub1', 'N/A', '', 2, 7, 150726, 'Absent', ''),
(259, ' ANANT JAIN ', '2506000005', 'sub1', 'N/A', '', 2, 7, 150726, 'Absent', ''),
(260, ' ANCHAL ', '2506000046', 'sub1', 'N/A', '', 2, 7, 150726, 'Absent', ''),
(261, ' ANJALI GUPTA ', '2506000007', 'sub1', 'N/A', '', 2, 7, 150726, 'Present', ''),
(262, ' ANJALI PAL ', '2506000008', 'sub1', 'N/A', '', 2, 7, 150726, 'Absent', ''),
(263, ' ANSHIKA YADAV ', '2506000052', 'sub1', 'N/A', '', 2, 7, 150726, 'Present', ''),
(264, ' ANURAG CHOUDHARY ', '2506000061', 'sub1', 'N/A', '', 2, 7, 150726, 'Present', ''),
(265, ' ARNIKA KAMBOJ ', '2506000009', 'sub1', 'N/A', '', 2, 7, 150726, 'Present', ''),
(266, ' ARYAN SAINI ', '2506000010', 'sub1', 'N/A', '', 2, 7, 150726, 'Absent', ''),
(267, ' ARYAV KUMAR ', '2506000058', 'sub1', 'N/A', '', 2, 7, 150726, 'Absent', ''),
(268, ' ASIF ', '2506000011', 'sub1', 'N/A', '', 2, 7, 150726, 'Absent', ''),
(269, ' BUNISH KUMAR SAINI ', '2506000012', 'sub1', 'N/A', '', 2, 7, 150726, 'Absent', ''),
(270, ' DEEN DYAL ', '2506000013', 'sub1', 'N/A', '', 2, 7, 150726, 'Absent', ''),
(271, ' DISHU ', '2506000014', 'sub1', 'N/A', '', 2, 7, 150726, 'Absent', ''),
(272, ' HARSH GIRI ', '2506000015', 'sub1', 'N/A', '', 2, 7, 150726, 'Absent', ''),
(273, ' HARSH NAMDEV ', '2506000056', 'sub1', 'N/A', '', 2, 7, 150726, 'Absent', ''),
(274, ' HARSH SHARMA ', '2506000055', 'sub1', 'N/A', '', 2, 7, 150726, 'Absent', ''),
(275, ' HARSH SHARMA ', '2506000047', 'sub1', 'N/A', '', 2, 7, 150726, 'Absent', ''),
(276, ' HIMANI SHARMA ', '2506000016', 'sub1', 'N/A', '', 2, 7, 150726, 'Absent', ''),
(277, ' HRITIK ', '2506000065', 'sub1', 'N/A', '', 2, 7, 150726, 'Absent', ''),
(278, ' JAISHREE ', '2506000017', 'sub1', 'N/A', '', 2, 7, 150726, 'Absent', ''),
(279, ' KANNU DEVI ', '2506000018', 'sub1', 'N/A', '', 2, 7, 150726, 'Absent', ''),
(280, ' KAPIL SAINI ', '2506000019', 'sub1', 'N/A', '', 2, 7, 150726, 'Absent', ''),
(281, ' KHUSHI KUMARI ', '2506000020', 'sub1', 'N/A', '', 2, 7, 150726, 'Absent', ''),
(282, ' KM ANCHAL SAINI ', '2506000021', 'sub1', 'N/A', '', 2, 7, 150726, 'Absent', ''),
(283, ' KM ANJALI ', '2506000006', 'sub1', 'N/A', '', 2, 7, 150726, 'Absent', ''),
(284, ' KM PREETI ', '2506000022', 'sub1', 'N/A', '', 2, 7, 150726, 'Absent', ''),
(285, ' MANSAVI ', '2506000023', 'sub1', 'N/A', '', 2, 7, 150726, 'Absent', ''),
(286, ' NEERAJ KUMAR ', '2506000024', 'sub1', 'N/A', '', 2, 7, 150726, 'Absent', ''),
(287, ' NEHA KASHYAP ', '2506000025', 'sub1', 'N/A', '', 2, 7, 150726, 'Absent', ''),
(288, ' NIKHIL ', '2506000060', 'sub1', 'N/A', '', 2, 7, 150726, 'Absent', ''),
(289, ' NISHANT ', '2506000026', 'sub1', 'N/A', '', 2, 7, 150726, 'Absent', ''),
(290, ' NISHU SHARMA ', '2506000027', 'sub1', 'N/A', '', 2, 7, 150726, 'Absent', ''),
(291, ' NITIN CHAUDHARY ', '2506000053', 'sub1', 'N/A', '', 2, 7, 150726, 'Absent', ''),
(292, ' PRACHI ', '2506000028', 'sub1', 'N/A', '', 2, 7, 150726, 'Absent', ''),
(293, ' PRAJJWAL SHARMA ', '2506000048', 'sub1', 'N/A', '', 2, 7, 150726, 'Absent', ''),
(294, ' PRAKHAR GOEL ', '2506000029', 'sub1', 'N/A', '', 2, 7, 150726, 'Absent', ''),
(295, ' RADHIKA ', '2506000030', 'sub1', 'N/A', '', 2, 7, 150726, 'Absent', ''),
(296, ' RISHABH BIJALWAN', '2506000062', 'sub1', 'N/A', '', 2, 7, 150726, 'Absent', ''),
(297, ' RIYA TYAGI ', '2506000063', 'sub1', 'N/A', '', 2, 7, 150726, 'Absent', ''),
(298, ' SACHIN KUMAR ', '2506000031', 'sub1', 'N/A', '', 2, 7, 150726, 'Absent', ''),
(299, ' SAGAR GHOTNA ', '2506000032', 'sub1', 'N/A', '', 2, 7, 150726, 'Absent', ''),
(300, ' SAKSHI ', '2506000033', 'sub1', 'N/A', '', 2, 7, 150726, 'Absent', ''),
(301, ' SALONI ', '2506000034', 'sub1', 'N/A', '', 2, 7, 150726, 'Absent', ''),
(302, ' SHIKHA ', '2506000035', 'sub1', 'N/A', '', 2, 7, 150726, 'Absent', ''),
(303, ' SHIVAM ', '2506000036', 'sub1', 'N/A', '', 2, 7, 150726, 'Absent', ''),
(304, ' SHIVANK TRIPATHI ', '2506000037', 'sub1', 'N/A', '', 2, 7, 150726, 'Absent', ''),
(305, ' SUJAL KUMAR ', '2506000038', 'sub1', 'N/A', '', 2, 7, 150726, 'Absent', ''),
(306, ' SUWECHA SINGH ', '2506000054', 'sub1', 'N/A', '', 2, 7, 150726, 'Absent', ''),
(307, ' TANISHKA TYAGI ', '2506000039', 'sub1', 'N/A', '', 2, 7, 150726, 'Absent', ''),
(308, ' TANU SAHRAWAT ', '2506000057', 'sub1', 'N/A', '', 2, 7, 150726, 'Absent', ''),
(309, ' TARUN TOMAR ', '2506000040', 'sub1', 'N/A', '', 2, 7, 150726, 'Absent', ''),
(310, ' UJJAWAL SHARMA ', '2506000041', 'sub1', 'N/A', '', 2, 7, 150726, 'Absent', ''),
(311, ' VANSHIKA PAL ', '2506000042', 'sub1', 'N/A', '', 2, 7, 150726, 'Absent', ''),
(312, ' VARTIKA CHOUDHARY ', '2506000043', 'sub1', 'N/A', '', 2, 7, 150726, 'Absent', ''),
(313, ' VEDANSHI ', '2506000044', 'sub1', 'N/A', '', 2, 7, 150726, 'Absent', ''),
(314, ' YASH TYAGI ', '2506000064', 'sub1', 'N/A', '', 2, 7, 150726, 'Absent', ''),
(315, ' YATIN KUMAR ', '2506000045', 'sub1', 'N/A', '', 2, 7, 150726, 'Absent', ''),
(316, 'asaq', '2123', 'sub1', 'N/A', '', 2, 7, 150726, 'Absent', ''),
(317, 'Aarav Sharma', '26CSE001', 'sub1', 'N/A', '', 2, 7, 150726, 'Absent', ''),
(318, 'Ananya Iyer', '25ECE024', 'sub1', 'N/A', '', 2, 7, 150726, 'Absent', ''),
(319, 'Rohan Verma', '24MECH015', 'sub1', 'N/A', '', 2, 7, 150726, 'Absent', ''),
(320, 'Diya Nair', '23CSE042', 'sub1', 'N/A', '', 2, 7, 150726, 'Absent', ''),
(321, 'Ishaan Gupta', '26BBA009', 'sub1', 'N/A', '', 2, 7, 150726, 'Absent', ''),
(322, 'Meera Joshi', '25MBA054', 'sub1', 'N/A', '', 2, 7, 150726, 'Absent', ''),
(323, 'Aditya Rao', '26BCA012', 'sub1', 'N/A', '', 2, 7, 150726, 'Absent', ''),
(324, 'Kavya Patel', '24MCA031', 'sub1', 'N/A', '', 2, 7, 150726, 'Absent', ''),
(325, 'Vivaan Saxena', '25LAW004', 'sub1', 'N/A', '', 2, 7, 150726, 'Absent', ''),
(326, 'Sanya Mirza', '23LAW048', 'sub1', 'N/A', '', 2, 7, 150726, 'Absent', ''),
(327, ' ANANT JAIN ', '2506000005', 'Principles and Practice of Management', 'mpmba', 'N/A', 1, 8, 180726, 'Present', ''),
(328, 'Aarav Sharma', '26CSE001', 'Principles and Practice of Management', 'mpmba', 'N/A', 1, 8, 180726, 'Absent', ''),
(329, ' ANANT JAIN ', '2506000005', 'Principles and Practice of Management', 'mpmba', 'N/A', 1, 8, 180726, 'Present', ''),
(330, 'Aarav Sharma', '26CSE001', 'Principles and Practice of Management', 'mpmba', 'N/A', 1, 8, 180726, 'Present', ''),
(331, ' ANANT JAIN ', '2506000005', 'Principles and Practice of Management', 'mpmba', 'N/A', 1, 8, 180726, 'Present', ''),
(332, 'Aarav Sharma', '26CSE001', 'Principles and Practice of Management', 'mpmba', 'N/A', 1, 8, 180726, 'Absent', ''),
(333, ' ANANT JAIN ', '2506000005', 'Principles and Practice of Management', 'mpmba', 'N/A', 1, 8, 180726, 'Present', ''),
(334, 'Aarav Sharma', '26CSE001', 'Principles and Practice of Management', 'mpmba', 'N/A', 1, 8, 180726, 'Present', ''),
(335, ' ANANT JAIN ', '2506000005', 'Principles and Practice of Management', 'mpmba', 'bba', 1, 8, 200726, 'Present', ''),
(336, 'Aarav Sharma', '26CSE001', 'Principles and Practice of Management', 'mpmba', 'N/A', 1, 8, 200726, 'Present', ''),
(337, ' ANANT JAIN ', '2506000005', 'Principles and Practice of Management', 'mpmba', 'N/A', 1, 8, 200726, 'Present', ''),
(338, 'Aarav Sharma', '26CSE001', 'Principles and Practice of Management', 'mpmba', 'N/A', 1, 8, 200726, 'Present', ''),
(339, ' ARYAN SAINI ', '2506000010', 'sub1', 'N/A', 'N/A', 2, 7, 200726, 'Present', '');

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
(1, 56, 3, 'Diya Nair', '23CSE042', ' Principles and Practice of Management', 140726, 'Present', 'Present', 'i did mistakenly', 'Approved', '2026-07-15 05:04:03'),
(2, 56, 3, 'Diya Nair', '23CSE042', ' Principles and Practice of Management', 140726, 'Present', 'Present', 'asasas', 'Approved', '2026-07-15 05:14:46');

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
(40, 'bba', 1, 2, 'kaka', 'FOE', 'mpbba11-211'),
(41, 'BBA', 1, 6, 'web technmoglies', 'FOPS', ''),
(42, 'BBA', 1, 6, 'web technmoglies', 'FOPS', ''),
(43, 'BBA', 1, 6, 'web technmoglies', 'FOPS', ''),
(44, 'BBA', 1, 6, 'web technmoglies', 'FOPS', ''),
(45, 'BBA', 2, 8, 'sub1', 'FOCBS', ''),
(46, 'BBA', 2, 7, 'sub2', 'FOPS', '');

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
(0, 'BBA', 'FOCBS'),
(2, 'BCA', 'Faculty of Computer Science & Information Technology'),
(3, 'MCA', 'Faculty of Computer Science & Information Technology'),
(4, 'B.Sc. CS', 'Faculty of Computer Science & Information Technology'),
(5, 'B.Sc. IT', 'Faculty of Computer Science & Information Technology'),
(6, 'M.Sc. CS', 'Faculty of Computer Science & Information Technology'),
(7, 'M.Sc. IT', 'Faculty of Computer Science & Information Technology'),
(8, 'Ph.D. CS', 'Faculty of Computer Science & Information Technology'),
(9, 'Ph.D. IT', 'Faculty of Computer Science & Information Technology'),
(10, 'BBA', 'Faculty of Commerce & Business Studies'),
(11, 'MBA', 'Faculty of Commerce & Business Studies'),
(12, 'B.Com.', 'Faculty of Commerce & Business Studies'),
(13, 'M.Com.', 'Faculty of Commerce & Business Studies'),
(14, 'Ph.D. Management', 'Faculty of Commerce & Business Studies'),
(15, 'Ph.D. Commerce', 'Faculty of Commerce & Business Studies'),
(16, 'BA', 'Faculty of Arts, Humanities & Social Sciences'),
(17, 'MA Economics', 'Faculty of Arts, Humanities & Social Sciences'),
(18, 'MA English', 'Faculty of Arts, Humanities & Social Sciences'),
(19, 'MA Political Science', 'Faculty of Arts, Humanities & Social Sciences'),
(20, 'Ph.D. English', 'Faculty of Arts, Humanities & Social Sciences'),
(21, 'Ph.D. Sociology', 'Faculty of Arts, Humanities & Social Sciences'),
(22, 'Ph.D. Economics', 'Faculty of Arts, Humanities & Social Sciences'),
(23, 'LLB', 'Faculty of Legal Studies'),
(24, 'LLM', 'Faculty of Legal Studies'),
(25, 'BA LLB Integrated', 'Faculty of Legal Studies'),
(26, 'BBA LLB Integrated', 'Faculty of Legal Studies'),
(27, 'B.Com. LLB Integrated', 'Faculty of Legal Studies'),
(28, 'Diploma in Agriculture', 'Faculty of Agriculture'),
(29, 'B.Sc. Ag. Hons.', 'Faculty of Agriculture'),
(30, 'M.Sc. Ag. Agronomy', 'Faculty of Agriculture'),
(31, 'D.Pharm.', 'Faculty of Pharmaceutical Sciences'),
(32, 'B.Pharm.', 'Faculty of Pharmaceutical Sciences'),
(33, 'B.Sc. Nursing', 'Faculty of Nursing'),
(34, 'BPT', 'Faculty of Paramedical & Allied Health Sciences'),
(35, 'BMLT', 'Faculty of Paramedical & Allied Health Sciences'),
(36, 'BMRAIT', 'Faculty of Paramedical & Allied Health Sciences'),
(37, 'M.Sc. MLT', 'Faculty of Paramedical & Allied Health Sciences'),
(38, 'MPT', 'Faculty of Paramedical & Allied Health Sciences'),
(39, 'B.Ed.', 'Faculty of Education'),
(40, 'M.Ed.', 'Faculty of Education'),
(41, 'Diploma in Mechanical Engineering', 'Faculty of Engineering & Technology'),
(42, 'Diploma in Civil Engineering', 'Faculty of Engineering & Technology'),
(43, 'Diploma in Electrical Engineering', 'Faculty of Engineering & Technology'),
(44, 'Diploma in Computer Science Engineering', 'Faculty of Engineering & Technology'),
(45, 'B.Sc. PCM', 'Faculty of Sciences'),
(46, 'B.Sc. ZBC', 'Faculty of Sciences'),
(47, 'M.Sc. Chemistry', 'Faculty of Sciences'),
(48, 'M.Sc. Botany', 'Faculty of Sciences'),
(49, 'M.Sc. Microbiology', 'Faculty of Sciences'),
(50, 'M.Sc. Mathematics', 'Faculty of Sciences');

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
(2, 'Prof. P.K. Agarwal', 'Faculty of Commerce & Business Studies', 22),
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
(4, 'FOCBS', 'Faculty of Commerce and Business Studies', ''),
(5, 'FOECS', 'Faculty of Engineering and Computing Sciences', ''),
(6, 'FOPS', 'Faculty of Pharmaceutical Sciences', ''),
(7, 'FOS', 'Faculty of Science', ''),
(8, 'FOLS', 'Faculty of Legal Studies', ''),
(9, 'FOE', 'Faculty of Education', ''),
(10, 'FOAH', 'Faculty of Arts and Humanities', ''),
(11, 'FOA', 'Faculty of Agriculture', ''),
(12, 'FOPAHS', 'Faculty of Paramedical and Allied Health Sciences', ''),
(14, 'mca', 'bca', ''),
(15, 'asas', 'asas', ''),
(17, 'test faculty', 'asas', ''),
(18, 'test faculty', 'asas', '');

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
  `year` int(11) NOT NULL,
  `sem` int(11) NOT NULL,
  `date_of_admission` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `students`
--

INSERT INTO `students` (`id`, `name`, `enrollment_number`, `roll_number`, `faculty`, `course`, `year`, `sem`, `date_of_admission`) VALUES
(80, ' ABHIJEET PANWAR ', 2147483647, '2506000002', 'FOCBS', 'MBA', 1, 1, 0),
(81, ' ABHINAV KUMAR ', 2147483647, '2506000059', 'FOCBS', 'MBA', 1, 2, 0),
(82, ' ABHISHEK ', 2147483647, '2506000003', 'FOCBS', 'MBA', 1, 2, 0),
(83, ' ADITI SHARMA ', 2147483647, '2506000004', 'FOCBS', 'MBA', 1, 2, 0),
(84, ' ANANT JAIN ', 2147483647, '2506000005', 'FOCBS', 'BBA', 1, 2, 0),
(85, ' ANCHAL ', 2147483647, '2506000046', 'FOCBS', 'BBA', 1, 2, 0),
(86, ' ANJALI GUPTA ', 2147483647, '2506000007', 'FOCBS', 'BBA', 1, 2, 0),
(87, ' ANJALI PAL ', 2147483647, '2506000008', 'FOCBS', 'BBA', 1, 2, 0),
(88, ' ANSHIKA YADAV ', 2147483647, '2506000052', 'FOCBS', 'BBA', 1, 2, 0),
(89, ' ANURAG CHOUDHARY ', 2147483647, '2506000061', 'FOCBS', 'BBA', 1, 2, 0),
(90, ' ARNIKA KAMBOJ ', 2147483647, '2506000009', 'FOCBS', 'BBA', 0, 2, 0),
(91, ' ARYAN SAINI ', 2147483647, '2506000010', 'FOCBS', 'BBA', 1, 2, 0),
(92, ' ARYAV KUMAR ', 2147483647, '2506000058', 'FOCBS', 'BBA', 1, 2, 0),
(93, ' ASIF ', 2147483647, '2506000011', 'FOCBS', 'BBA', 1, 2, 0),
(94, ' BUNISH KUMAR SAINI ', 2147483647, '2506000012', 'FOCBS', 'BBA', 1, 2, 0),
(95, ' DEEN DYAL ', 2147483647, '2506000013', 'FOCBS', 'BBA', 1, 2, 0),
(96, ' DISHU ', 2147483647, '2506000014', 'FOCBS', 'BBA', 1, 0, 0),
(97, ' HARSH GIRI ', 2147483647, '2506000015', 'FOCBS', 'BBA', 1, 2, 0),
(98, ' HARSH NAMDEV ', 2147483647, '2506000056', 'FOCBS', 'BBA', 1, 1, 0),
(99, ' HARSH SHARMA ', 2147483647, '2506000055', 'FOCBS', 'BBA', 1, 2, 0),
(100, ' HARSH SHARMA ', 2147483647, '2506000047', 'FOCBS', 'BBA', 1, 1, 0),
(101, ' HIMANI SHARMA ', 2147483647, '2506000016', 'FOCBS', 'BBA', 1, 1, 0),
(102, ' HRITIK ', 2147483647, '2506000065', 'FOCBS', 'BBA', 1, 1, 0),
(103, ' JAISHREE ', 2147483647, '2506000017', 'FOCBS', 'BBA', 1, 2, 0),
(104, ' KANNU DEVI ', 2147483647, '2506000018', 'FOCBS', 'BBA', 1, 2, 0),
(105, ' KAPIL SAINI ', 2147483647, '2506000019', 'FOCBS', 'BBA', 1, 1, 0),
(106, ' KHUSHI KUMARI ', 2147483647, '2506000020', 'FOCBS', 'BBA', 1, 1, 0),
(107, ' KM ANCHAL SAINI ', 2147483647, '2506000021', 'FOCBS', 'BBA', 1, 2, 0),
(108, ' KM ANJALI ', 2147483647, '2506000006', 'FOCBS', 'BBA', 1, 2, 0),
(109, ' KM PREETI ', 2147483647, '2506000022', 'FOCBS', 'BBA', 1, 2, 0),
(110, ' MANSAVI ', 2147483647, '2506000023', 'FOCBS', 'BBA', 1, 2, 0),
(111, ' NEERAJ KUMAR ', 2147483647, '2506000024', 'FOCBS', 'BBA', 1, 2, 0),
(112, ' NEHA KASHYAP ', 2147483647, '2506000025', 'FOCBS', 'BBA', 1, 2, 0),
(113, ' NIKHIL ', 2147483647, '2506000060', 'FOCBS', 'BBA', 1, 2, 0),
(114, ' NISHANT ', 2147483647, '2506000026', 'FOCBS', 'BBA', 1, 1, 0),
(115, ' NISHU SHARMA ', 2147483647, '2506000027', 'FOCBS', 'BBA', 1, 1, 0),
(116, ' NITIN CHAUDHARY ', 2147483647, '2506000053', 'FOCBS', 'BBA', 1, 1, 0),
(117, ' PRACHI ', 2147483647, '2506000028', 'FOCBS', 'BCOM', 1, 1, 0),
(118, ' PRAJJWAL SHARMA ', 2147483647, '2506000048', 'FOCBS', 'BCOM', 1, 2, 0),
(119, ' PRAKHAR GOEL ', 2147483647, '2506000029', 'FOCBS', 'BCOM', 1, 2, 0),
(120, ' RADHIKA ', 2147483647, '2506000030', 'FOCBS', 'BCOM', 1, 2, 0),
(121, ' RISHABH BIJALWAN', 2147483647, '2506000062', 'FOCBS', 'BCOM', 1, 2, 0),
(122, ' RIYA TYAGI ', 2147483647, '2506000063', 'FOCBS', 'BCOM', 1, 2, 0),
(123, ' SACHIN KUMAR ', 2147483647, '2506000031', 'FOCBS', 'BCOM', 1, 2, 0),
(124, ' SAGAR GHOTNA ', 2147483647, '2506000032', 'FOCBS', 'BCOM', 1, 2, 0),
(125, ' SAKSHI ', 2147483647, '2506000033', 'FOCBS', 'BCOM', 1, 2, 0),
(126, ' SALONI ', 2147483647, '2506000034', 'FOCBS', 'BCOM', 1, 2, 0),
(127, ' SHIKHA ', 2147483647, '2506000035', 'FOCBS', 'BCOM', 1, 2, 0),
(128, ' SHIVAM ', 2147483647, '2506000036', 'FOCBS', 'BCOM', 1, 2, 0),
(129, ' SHIVANK TRIPATHI ', 2147483647, '2506000037', 'FOCBS', 'BCOM', 1, 2, 0),
(130, ' SUJAL KUMAR ', 2147483647, '2506000038', 'FOCBS', 'BCOM', 1, 2, 0),
(131, ' SUWECHA SINGH ', 2147483647, '2506000054', 'FOCBS', 'BCOM', 1, 2, 0),
(132, ' TANISHKA TYAGI ', 2147483647, '2506000039', 'FOCBS', 'BCOM', 1, 2, 0),
(133, ' TANU SAHRAWAT ', 2147483647, '2506000057', 'FOCBS', 'BCOM', 1, 2, 0),
(134, ' TARUN TOMAR ', 2147483647, '2506000040', 'FOCBS', 'BCOM', 1, 2, 0),
(135, ' UJJAWAL SHARMA ', 2147483647, '2506000041', 'FOCBS', 'BCOM', 1, 2, 0),
(136, ' VANSHIKA PAL ', 2147483647, '2506000042', 'FOCBS', 'BCOM', 1, 2, 0),
(137, ' VARTIKA CHOUDHARY ', 2147483647, '2506000043', 'FOCBS', 'BCOM', 1, 2, 0),
(138, ' VEDANSHI ', 2147483647, '2506000044', 'FOCBS', 'BCOM', 1, 2, 0),
(139, ' YASH TYAGI ', 2147483647, '2506000064', 'FOCBS', 'BCOM', 1, 2, 0),
(140, ' YATIN KUMAR ', 2147483647, '2506000045', 'FOCBS', 'BCOM', 1, 2, 0),
(141, 'asaq', 231, '2123', 'FOA', 'BBA', 2, 2, 0),
(142, 'Aarav Sharma', 0, '26CSE001', 'Faculty of Engineering', 'B.Tech CSE', 1, 1, 0),
(143, 'Ananya Iyer', 0, '25ECE024', 'Faculty of Engineering', 'B.Tech ECE', 2, 3, 0),
(144, 'Rohan Verma', 0, '24MECH015', 'Faculty of Engineering', 'B.Tech ME', 3, 5, 0),
(145, 'Diya Nair', 0, '23CSE042', 'Faculty of Engineering', 'B.Tech CSE', 4, 7, 0),
(146, 'Ishaan Gupta', 0, '26BBA009', 'Faculty of Management', 'BBA', 1, 1, 0),
(147, 'Meera Joshi', 0, '25MBA054', 'Faculty of Management', 'MBA', 2, 3, 0),
(148, 'Aditya Rao', 0, '26BCA012', 'Faculty of Computer Apps', 'BCA', 1, 1, 0),
(149, 'Kavya Patel', 0, '24MCA031', 'Faculty of Computer Apps', 'MCA', 3, 5, 0),
(150, 'Vivaan Saxena', 0, '25LAW004', 'Faculty of Law', 'BA LLB', 2, 3, 0),
(151, 'Sanya Mirza', 0, '23LAW048', 'Faculty of Law', 'BA LLB', 4, 7, 0),
(152, 'a', 1, '1', 'FOCBS', 'MBA', 1, 2, 0),
(153, 'a', 1, '1', 'FOCBS', 'MBA', 1, 2, 0),
(154, 'John Doe', 12345678, 'A-101', 'Computer Science', 'B.Tech', 2026, 4, 0),
(155, 'sumit ', 564564564, '5656456', 'klklk', 'bbb', 2222, 2, 0);

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
  `roll_number` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `subjected_student`
--

INSERT INTO `subjected_student` (`id`, `student_name`, `subject_name`, `subject_code`, `faculty`, `course`, `year`, `semester`, `roll_number`) VALUES
(5, 'Aarav Sharma', 'Principles and Practice of Management', 'mpmba', 'Faculty of Engineering', 'B.Tech CSE', 1, 1, 0),
(6, ' ANANT JAIN ', 'Principles and Practice of Management', 'mpmba', 'FOCBS', 'BBA', 1, 2, 0),
(7, ' ARYAN SAINI ', 'sub1', '', 'FOCBS', 'BBA', 1, 2, 0),
(8, ' BUNISH KUMAR SAINI ', 'sub1', '', 'FOCBS', 'BBA', 1, 2, 0);

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
(12, 3, 38, 'dr. snehashish bhardwaj', 'Principles and Practice of Management', '', 1, 8, 'mpmba'),
(13, 3, 38, 'dr. snehashish bhardwaj', 'Principles and Practice of Management', '', 1, 8, 'kdskjdfs'),
(19, 3, 39, 'dr. snehashish bhardwaj', 'fa', '', 3, 6, ''),
(20, 3, 45, 'dr. snehashish bhardwaj', 'sub1', '', 2, 7, ''),
(24, 3, 39, 'dr. snehashish bhardwaj', ' Financial Accountin', 'B.Com.', 3, 9, 'mpmba33-601'),
(25, 5, 49, 'dr. brajkishore bharti', 'adddddd', '', 3, 5, '');

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
  `subject_code` varchar(55) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `subjects`
--

INSERT INTO `subjects` (`course_id`, `course_name`, `Year`, `semester`, `subject_name`, `dept_name`, `subject_code`) VALUES
(38, 'BBA', 1, 1, ' Principles and Practice of Management', 'FOCBS', 'mubba22-504'),
(39, 'BBA', 1, 1, ' Financial Accountin', 'FOCBS', 'mpmba33-601'),
(40, 'bba', 1, 2, 'kaka', 'FOE', 'mpbba11-211'),
(41, 'BBA', 1, 6, 'web technmoglies', 'FOPS', ''),
(42, 'BBA', 1, 6, 'web technmoglies', 'FOPS', ''),
(43, 'BBA', 1, 6, 'web technmoglies', 'FOPS', ''),
(44, 'BBA', 1, 6, 'web technmoglies', 'FOPS', ''),
(45, 'BBA', 2, 8, 'sub1', 'FOCBS', ''),
(46, 'BBA', 2, 7, 'sub2', 'FOPS', ''),
(47, 'BBA', 2, 5, 'aasasas', 'FOECS', ''),
(48, 'BBA', 2, 5, 'aasasas', 'FOECS', ''),
(49, 'MBA', 1, 3, 'adddddd', 'FOCBS', '');

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
-- Indexes for table `attendance_corrections`
--
ALTER TABLE `attendance_corrections`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `courses`
--
ALTER TABLE `courses`
  ADD PRIMARY KEY (`course_id`);

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
-- Indexes for table `departments`
--
ALTER TABLE `departments`
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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=340;

--
-- AUTO_INCREMENT for table `attendance_corrections`
--
ALTER TABLE `attendance_corrections`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `courses`
--
ALTER TABLE `courses`
  MODIFY `course_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=47;

--
-- AUTO_INCREMENT for table `courses_list`
--
ALTER TABLE `courses_list`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=51;

--
-- AUTO_INCREMENT for table `deans`
--
ALTER TABLE `deans`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `departments`
--
ALTER TABLE `departments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `faculty`
--
ALTER TABLE `faculty`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `students`
--
ALTER TABLE `students`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=156;

--
-- AUTO_INCREMENT for table `subjected_student`
--
ALTER TABLE `subjected_student`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `subjected_teacher`
--
ALTER TABLE `subjected_teacher`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT for table `subjects`
--
ALTER TABLE `subjects`
  MODIFY `course_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=50;

--
-- AUTO_INCREMENT for table `teachers`
--
ALTER TABLE `teachers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
