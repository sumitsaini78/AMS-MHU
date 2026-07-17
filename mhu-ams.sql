-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 16, 2026 at 05:00 PM
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
(25, 'Rohan Verma', '24MECH015', ' Principles and Practice of Management', 'kdskjdfs', 'BBA', 4, 4, 50726, 'Present', ''),
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
(183, 'Aarav Sharma', '26CSE001', ' Principles and Practice of Management', 'mpmba', '', 1, 8, 150726, 'Absent', ''),
(184, 'Ananya Iyer', '25ECE024', ' Principles and Practice of Management', 'mpmba', '', 1, 8, 150726, 'Absent', ''),
(185, 'Rohan Verma', '24MECH015', ' Principles and Practice of Management', 'mpmba', '', 1, 8, 150726, 'Present', ''),
(186, 'Diya Nair', '23CSE042', ' Principles and Practice of Management', 'mpmba', '', 1, 8, 150726, 'Present', ''),
(187, 'Ishaan Gupta', '26BBA009', ' Principles and Practice of Management', 'mpmba', '', 1, 8, 150726, 'Absent', ''),
(188, 'Meera Joshi', '25MBA054', ' Principles and Practice of Management', 'mpmba', '', 1, 8, 150726, 'Absent', ''),
(189, 'Aditya Rao', '26BCA012', ' Principles and Practice of Management', 'mpmba', '', 1, 8, 150726, 'Present', ''),
(190, 'Kavya Patel', '24MCA031', ' Principles and Practice of Management', 'mpmba', '', 1, 8, 150726, 'Present', ''),
(191, 'Vivaan Saxena', '25LAW004', ' Principles and Practice of Management', 'mpmba', '', 1, 8, 150726, 'Present', ''),
(192, 'Sanya Mirza', '23LAW048', ' Principles and Practice of Management', 'mpmba', '', 1, 8, 150726, 'Present', ''),
(193, 'Aarav Sharma', '26CSE001', ' Principles and Practice of Management', 'mpmba', '', 1, 8, 150726, 'Present', ''),
(194, 'Ananya Iyer', '25ECE024', ' Principles and Practice of Management', 'mpmba', '', 1, 8, 150726, 'Absent', ''),
(195, 'Rohan Verma', '24MECH015', ' Principles and Practice of Management', 'mpmba', '', 1, 8, 150726, 'Absent', ''),
(196, 'Diya Nair', '23CSE042', ' Principles and Practice of Management', 'mpmba', '', 1, 8, 150726, 'Absent', ''),
(197, 'Ishaan Gupta', '26BBA009', ' Principles and Practice of Management', 'mpmba', '', 1, 8, 150726, 'Absent', ''),
(198, 'Meera Joshi', '25MBA054', ' Principles and Practice of Management', 'mpmba', '', 1, 8, 150726, 'Absent', ''),
(199, 'Aditya Rao', '26BCA012', ' Principles and Practice of Management', 'mpmba', '', 1, 8, 150726, 'Absent', ''),
(200, 'Kavya Patel', '24MCA031', ' Principles and Practice of Management', 'mpmba', '', 1, 8, 150726, 'Absent', ''),
(201, 'Vivaan Saxena', '25LAW004', ' Principles and Practice of Management', 'mpmba', '', 1, 8, 150726, 'Absent', ''),
(202, 'Sanya Mirza', '23LAW048', ' Principles and Practice of Management', 'mpmba', '', 1, 8, 150726, 'Absent', ''),
(203, 'Aarav Sharma', '26CSE001', ' Financial Accountin', 'mpmba33-601', 'BBA', 3, 7, 160726, 'Absent', ''),
(204, 'Ananya Iyer', '25ECE024', ' Financial Accountin', 'mpmba33-601', 'BBA', 3, 7, 160726, 'Present', ''),
(205, 'Rohan Verma', '24MECH015', ' Financial Accountin', 'mpmba33-601', 'BBA', 3, 7, 160726, 'Present', ''),
(206, 'Diya Nair', '23CSE042', ' Financial Accountin', 'mpmba33-601', 'BBA', 3, 7, 160726, 'Absent', ''),
(207, 'Ishaan Gupta', '26BBA009', ' Financial Accountin', 'mpmba33-601', 'BBA', 3, 7, 160726, 'Present', ''),
(208, 'Meera Joshi', '25MBA054', ' Financial Accountin', 'mpmba33-601', 'BBA', 3, 7, 160726, 'Present', ''),
(209, 'Aditya Rao', '26BCA012', ' Financial Accountin', 'mpmba33-601', 'BBA', 3, 7, 160726, 'Present', ''),
(210, 'Kavya Patel', '24MCA031', ' Financial Accountin', 'mpmba33-601', 'BBA', 3, 7, 160726, 'Absent', ''),
(211, 'Vivaan Saxena', '25LAW004', ' Financial Accountin', 'mpmba33-601', 'BBA', 3, 7, 160726, 'Absent', ''),
(212, 'Sanya Mirza', '23LAW048', ' Financial Accountin', 'mpmba33-601', 'BBA', 3, 7, 160726, 'Absent', ''),
(213, 'Aarav Sharma', '26CSE001', ' Principles and Practice of Management', 'mpmba', '', 1, 8, 160726, 'Present', ''),
(214, 'Ananya Iyer', '25ECE024', ' Principles and Practice of Management', 'mpmba', '', 1, 8, 160726, 'Present', ''),
(215, 'Rohan Verma', '24MECH015', ' Principles and Practice of Management', 'mpmba', '', 1, 8, 160726, 'Absent', ''),
(216, 'Diya Nair', '23CSE042', ' Principles and Practice of Management', 'mpmba', '', 1, 8, 160726, 'Absent', ''),
(217, 'Ishaan Gupta', '26BBA009', ' Principles and Practice of Management', 'mpmba', '', 1, 8, 160726, 'Absent', ''),
(218, 'Meera Joshi', '25MBA054', ' Principles and Practice of Management', 'mpmba', '', 1, 8, 160726, 'Absent', ''),
(219, 'Aditya Rao', '26BCA012', ' Principles and Practice of Management', 'mpmba', '', 1, 8, 160726, 'Absent', ''),
(220, 'Kavya Patel', '24MCA031', ' Principles and Practice of Management', 'mpmba', '', 1, 8, 160726, 'Absent', ''),
(221, 'Vivaan Saxena', '25LAW004', ' Principles and Practice of Management', 'mpmba', '', 1, 8, 160726, 'Absent', ''),
(222, 'Sanya Mirza', '23LAW048', ' Principles and Practice of Management', 'mpmba', '', 1, 8, 60726, 'Present', ''),
(223, 'Aditya Rao', '1', ' Financial Accountin', 'mpmba33-601', 'BBA', 3, 7, 160726, 'Absent', ''),
(224, 'Sanya Mirza', '2', ' Financial Accountin', 'mpmba33-601', 'BBA', 3, 7, 160726, 'Present', ''),
(225, 'Rohan Verma', '24MECH015', ' Principles and Practice of Management', 'kdskjdfs', 'BBA', 4, 4, 60726, 'Present', ''),
(226, 'Aditya Rao', '1', ' Principles and Practice of Management', 'mpmba', '', 1, 8, 160726, 'Present', ''),
(227, 'Sanya Mirza', '2', ' Principles and Practice of Management', 'mpmba', '', 1, 8, 160726, 'Present', ''),
(228, 'Aditya Rao', '26BCA012', ' Principles and Practice of Management', 'mpmba', 'N/A', 1, 8, 160726, 'Present', ''),
(229, 'Aditya Rao', '26BCA012', ' Principles and Practice of Management', 'mpmba', 'N/A', 1, 8, 160726, 'Present', ''),
(230, 'Aditya Rao', '26BCA012', ' Principles and Practice of Management', 'mpmba', 'N/A', 1, 8, 160726, 'Present', ''),
(231, 'Sanya Mirza', '23LAW048', 'fa', 'N/A', 'MBA', 3, 6, 160726, 'Present', '');

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
(2, 56, 3, 'Diya Nair', '23CSE042', ' Principles and Practice of Management', 140726, 'Present', 'Present', 'asasas', 'Approved', '2026-07-15 05:14:46'),
(3, 206, 3, 'Diya Nair', '23CSE042', ' Financial Accountin', 160726, 'Present', 'Absent', 'mistaklelnlynfgbjkbjh', 'Approved', '2026-07-16 06:24:36');

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
(20, 'test', 'FOE', 0),
(21, 'testdean', 'Faculty of Commerce and Business Studies', 0);

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
(79, 'Sanya Mirza', 0, '23LAW048', 'Faculty of Law', 'BA LLB', 4, 7, '', ''),
(80, 'as', 111, '11', 'test faculty', 'Ph.D. IT', 12, 1, '', '');

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
(19, 3, 39, 'dr. snehashish bhardwaj', 'fa', 'MBA', 3, 6, ''),
(20, 3, 45, 'dr. snehashish bhardwaj', 'sub1', 'MBA', 2, 7, ''),
(21, 3, 39, 'dr. snehashish bhardwaj', ' Financial Accountin', 'BBA', 3, 7, 'mpmba33-601'),
(22, 7, 39, 'DR.Chiku Saini', ' Financial Accountin', 'BBA', 2, 6, 'mpmba33-601'),
(23, 5, 39, 'dr. brajkishore bharti', ' Financial Accountin', 'MBA', 2, 6, 'mpmba33-601');

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
(5, 'dr. brajkishore bharti', 'FOCBS', 555),
(7, 'DR.Chiku Saini', 'FOCBS', 0),
(8, 'asasas', '', 0),
(9, 'asasa', '', 0),
(10, 'asasas', '', 0),
(11, 'wsedw', '', 0),
(12, 'wsedw', '', 0),
(13, 'wsedw', '', 0),
(14, 'dstg', 'FOA', 0),
(15, 'asasas', 'test faculty', 0);

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=232;

--
-- AUTO_INCREMENT for table `attendance_corrections`
--
ALTER TABLE `attendance_corrections`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

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
-- AUTO_INCREMENT for table `faculty`
--
ALTER TABLE `faculty`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `students`
--
ALTER TABLE `students`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=81;

--
-- AUTO_INCREMENT for table `subjected_student`
--
ALTER TABLE `subjected_student`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `subjected_teacher`
--
ALTER TABLE `subjected_teacher`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

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
