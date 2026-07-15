-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 15, 2026 at 10:19 AM
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
(182, 'Sanya Mirza', '23LAW048', ' Principles and Practice of Management', 'mpmba', '', 1, 8, 150726, 'Absent', '');

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
(0, 'BBA', 'FOCBS');

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
  `enrollment_number` int(11) NOT NULL,
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

INSERT INTO `students` (`id`, `name`, `enrollment_number`, `roll_number`, `faculty`, `course`, `year`, `sem`, `subject_code`, `subject_name`) VALUES
(70, 'Aarav Sharma', 0, '26CSE001', 'Faculty of Engineering', 'B.Tech CSE', 1, 1, '', ''),
(71, 'Ananya Iyer', 0, '25ECE024', 'Faculty of Engineering', 'B.Tech ECE', 2, 3, '', ''),
(72, 'Rohan Verma', 0, '24MECH015', 'Faculty of Engineering', 'B.Tech ME', 3, 5, '', ''),
(73, 'Diya Nair', 0, '23CSE042', 'Faculty of Engineering', 'B.Tech CSE', 4, 7, '', ''),
(74, 'Ishaan Gupta', 0, '26BBA009', 'Faculty of Management', 'BBA', 1, 1, '', ''),
(75, 'Meera Joshi', 0, '25MBA054', 'Faculty of Management', 'MBA', 2, 3, '', ''),
(76, 'Aditya Rao', 0, '26BCA012', 'Faculty of Computer Apps', 'BCA', 1, 1, '', ''),
(77, 'Kavya Patel', 0, '24MCA031', 'Faculty of Computer Apps', 'MCA', 3, 5, '', ''),
(78, 'Vivaan Saxena', 0, '25LAW004', 'Faculty of Law', 'BA LLB', 2, 3, '', ''),
(79, 'Sanya Mirza', 0, '23LAW048', 'Faculty of Law', 'BA LLB', 4, 7, '', '');

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
  `semester` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `subjected_student`
--

INSERT INTO `subjected_student` (`id`, `student_name`, `subject_name`, `subject_code`, `faculty`, `course`, `year`, `semester`) VALUES
(1, 'Aditya Rao', ' Principles and Practice of Management', 'mpmba', 'Faculty of Computer Apps', 'BCA', 0, 0),
(2, 'Sanya Mirza', 'fa', '', 'Faculty of Law', 'BA LLB', 0, 0);

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
(13, 3, 38, 'dr. snehashish bhardwaj', ' Principles and Practice of Management', '', 1, 8, 'kdskjdfs'),
(19, 3, 39, 'dr. snehashish bhardwaj', 'fa', '', 3, 6, ''),
(20, 3, 45, 'dr. snehashish bhardwaj', 'sub1', '', 2, 7, '');

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=183;

--
-- AUTO_INCREMENT for table `attendance_corrections`
--
ALTER TABLE `attendance_corrections`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `courses`
--
ALTER TABLE `courses`
  MODIFY `course_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=47;

--
-- AUTO_INCREMENT for table `deans`
--
ALTER TABLE `deans`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `departments`
--
ALTER TABLE `departments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `students`
--
ALTER TABLE `students`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=80;

--
-- AUTO_INCREMENT for table `subjected_student`
--
ALTER TABLE `subjected_student`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `subjected_teacher`
--
ALTER TABLE `subjected_teacher`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `teachers`
--
ALTER TABLE `teachers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
