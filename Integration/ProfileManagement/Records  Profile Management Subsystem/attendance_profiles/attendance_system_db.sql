-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 23, 2026 at 01:39 PM
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
-- Database: `attendance_system_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `classes`
--

CREATE TABLE `classes` (
  `class_id` int(11) NOT NULL,
  `class_code` varchar(10) DEFAULT NULL,
  `section` varchar(20) DEFAULT NULL,
  `subject_code` varchar(20) DEFAULT NULL,
  `subject_name` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `classes`
--

INSERT INTO `classes` (`class_id`, `class_code`, `section`, `subject_code`, `subject_name`) VALUES
(1, 'A4', 'BSIT 1A', 'IT 11', 'Introduction to Human Computer Interaction'),
(2, 'A37', 'BSIT 2B', 'ITC 15', 'Information Management 1'),
(3, 'A28', 'BSIT 2A', 'ELECIT 103', 'Fundamentals of Database Systems'),
(4, 'A30', 'BSIT 2A', 'IT 16', 'Quantitative Methods'),
(5, 'A51', 'BSIT 3A', 'IT 22', 'Information Assurance & Security 1');

-- --------------------------------------------------------

--
-- Table structure for table `schedules`
--

CREATE TABLE `schedules` (
  `schedule_id` int(11) NOT NULL,
  `class_code` varchar(10) DEFAULT NULL,
  `day` varchar(20) DEFAULT NULL,
  `time` varchar(50) DEFAULT NULL,
  `room` varchar(20) DEFAULT NULL,
  `instructor` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `schedules`
--

INSERT INTO `schedules` (`schedule_id`, `class_code`, `day`, `time`, `room`, `instructor`) VALUES
(7, 'A37', 'Monday', '8:00-9:30 AM', 'COM LAB A', 'Andico, LJ'),
(8, 'A4', 'Monday', '9:30-10:30 AM', 'COM LAB A', 'Andico, LJ'),
(9, 'A51', 'Monday', '10:30 AM-12:00 PM', 'COM LAB A', 'Tagulalac, D'),
(10, 'A28', 'Monday', '1:00-2:30 PM', 'COM LAB A', 'Tagulalac, D'),
(11, 'A28', 'Monday', '2:30-4:30 PM', 'COM LAB A', 'Tagulalac, D'),
(12, 'A30', 'Monday', '5:00-6:30 PM', 'COM LAB A', 'Alpas, C');

-- --------------------------------------------------------

--
-- Table structure for table `students`
--

CREATE TABLE `students` (
  `id` int(11) NOT NULL,
  `student_id` varchar(20) DEFAULT NULL,
  `full_name` varchar(100) DEFAULT NULL,
  `gender` varchar(10) DEFAULT NULL,
  `course` varchar(20) DEFAULT NULL,
  `year_level` int(11) DEFAULT NULL,
  `contact` varchar(20) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `parent_name` varchar(100) DEFAULT '',
  `parent_contact` varchar(20) DEFAULT '09056689672'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `students`
--

INSERT INTO `students` (`id`, `student_id`, `full_name`, `gender`, `course`, `year_level`, `contact`, `email`, `parent_name`, `parent_contact`) VALUES
(1, 'B2017586', 'Alsado, Kean Rose M.', 'F', 'BSIT', 1, '09691082153', '', '', '09056689672'),
(2, '20250249', 'Arriola, Adrian James T.', 'M', 'BSIT', 1, '09564038676', 'Vasel.arriola2002@gmail.com', '', '09056689672'),
(3, '20250815', 'Barinos, John Homer N.', 'M', 'BSIT', 1, '06956227343', '', '', '09056689672'),
(4, '20250279', 'Diosoy, Vincent Jay S.', 'M', 'BSIT', 1, '09663548543', '', '', '09056689672'),
(5, '20250508', 'Encaja, Karl Timothy E.', 'M', 'BSIT', 1, '09318254576', '', '', '09056689672'),
(7, '20250634', 'Garsula, Polo James A.', 'M', 'BSIT', 1, '09078743241', '', '', '09056689672'),
(8, '20240297', 'Germo, Oya S.', 'M', 'BSIT', 1, '09912693704', '', '', '09056689672'),
(9, '20250813', 'Gonzales, Chris John S.', 'F', 'BSIT', 1, '09121381030', '', '', '09056689672'),
(10, 'B20240540', 'Grande, Althea Kassandra A.', 'F', 'BSIT', 1, '09453816399', 'althea.grande.s@southlandcollege.edu.ph', '', '09056689672'),
(11, '20250820', 'Alfuente, Marvin Neil M.', 'M', 'BSIT', 2, '', '', '', '09056689672'),
(12, '20240414', 'Arimas, John Paul T.', 'M', 'BSIT', 2, '09956214032', '', '', '09056689672'),
(13, 'B20230123', 'Calalas, Loranz Ali A.', 'M', 'BSIT', 2, '09183219440', '', '', '09056689672'),
(14, '20240516', 'Francia Jr., Edgar B.', 'M', 'BSIT', 2, '09850803845', '', '', '09056689672'),
(15, '20250857', 'Gagatam, Denny C.', 'F', 'BSIT', 2, '09657923669', '', '', '09056689672'),
(16, '20240478', 'Hsu, Cheng Huang R.', 'M', 'BSIT', 2, '09158763035', '', '', '09056689672'),
(17, '20240227', 'Hulguin, John Paul E.', 'M', 'BSIT', 2, '09157119687', '', '', '09056689672'),
(18, '20240415', 'Lopez, Lyzander Alexius C.', 'M', 'BSIT', 2, '09761597013', '', '', '09056689672'),
(19, '20250658', 'Martir, Kent Jonil P.', 'M', 'BSIT', 2, '09567113247', '', '', '09056689672'),
(20, '20240176', 'Molarto, Ivan Clint D.', 'M', 'BSIT', 2, '09701978001', '', '', '09056689672'),
(21, '20240140', 'Putong, Reman Jr. F.', 'M', 'BSIT', 2, '09087062227', '', '', '09056689672'),
(22, '20240147', 'Quillo, Skitch G.', 'M', 'BSIT', 2, '09127831714', '', '', '09056689672'),
(23, '20240186', 'Servano, Eunice Pearl E.', 'F', 'BSIT', 2, '09064538148', '', '', '09056689672'),
(24, '20220187', 'Tabacolde, May Chelle A.', 'F', 'BSIT', 2, '09272356140', '', '', '09056689672'),
(25, '20240260', 'Tayco, Elderie John C.', 'M', 'BSIT', 2, '09673240757', '', '', '09056689672'),
(26, '20240278', 'Zulueta, Cj J.', 'M', 'BSIT', 2, '09461521354', '', '', '09056689672'),
(27, '20220003', 'Abella, Mark Allexi T.', 'M', 'BSIT', 2, '09777556450', '', '', '09056689672'),
(28, '20240295', 'Aquino, Zurich Clyde O.', 'M', 'BSIT', 2, '09601038624', 'zurichclyde.aquino.s@southlandcollege.edu.ph', '', '09056689672'),
(29, '20240225', 'Bagaporo, Alexa P.', 'F', 'BSIT', 2, '09936412673', '', '', '09056689672'),
(30, '20240253', 'Bendol, Reynalie T.', 'F', 'BSIT', 2, '09673225253', '', '', '09056689672'),
(31, '20210365', 'Bruno, Robert Dave B.', 'M', 'BSIT', 2, '09162199005', '', '', '09056689672'),
(32, '20240234', 'Castillo JR., Alfred John S.', 'M', 'BSIT', 2, '', '', '', '09056689672'),
(33, '20250699', 'Cuison, Patrick Reniel D.', 'M', 'BSIT', 3, '09628087554', '', '', '09056689672'),
(34, '20240044', 'David, Val Zendrick C.', 'M', 'BSIT', 2, '09070042451', '', '', '09056689672'),
(35, '20240208', 'De Leon, Nicca A.', 'F', 'BSIT', 2, '09933889247', '', '', '09056689672'),
(36, '20240166', 'Eguis, Crisly Joy G.', 'F', 'BSIT', 2, '09512266875', '', '', '09056689672'),
(37, '20240296', 'Fordan, Bless Joy N.', 'F', 'BSIT', 2, '', '', '', '09056689672'),
(38, '20220331', 'Guillepa, Sam C.', 'M', 'BSIT', 2, '09919085250', 'sam.guillepa.s@southlandcollege.edu.ph', '', '09056689672'),
(39, '20220240', 'Jaranilla, Jardi John D.', 'M', 'BSIT', 2, '09127067766', '', '', '09056689672'),
(40, '20220208', 'Lozada, John Paul R.', 'M', 'BSIT', 2, '09468105751', '', '', '09056689672'),
(41, '20220186', 'Mancia, Mark D.', 'M', 'BSIT', 2, '09942098148', '', '', '09056689672'),
(42, '20240142', 'Ordas, Lawrence F.', 'M', 'BSIT', 2, '09919066864', '', '', '09056689672'),
(43, '20240093', 'Panila, Michaellah Luisa S.', 'F', 'BSIT', 2, '09667380079', 'michaelaluisa.panila.s@southlandcollege.edu.ph', '', '09056689672'),
(44, '20210359', 'Sansano, Prince Louie G.', 'M', 'BSIT', 2, '09939436638', '', '', '09056689672'),
(45, '20220174', 'Sevilleno, Julianne Grace Frances A.', 'F', 'BSIT', 2, '09295598035', '', '', '09056689672'),
(46, '20220316', 'Sumugat, Christy Joyce T.', 'F', 'BSIT', 2, '09494577129', 'christyjoyce.sumugat.s@southlandcollege.edu.ph', '', '09056689672'),
(47, '20240190', 'Susada, Sharah Mae C.', 'F', 'BSIT', 2, '09912693660', '', '', '09056689672'),
(48, '20240315', 'De Jesus, Zenneth Braven P.', 'M', 'BSIT', 2, '09927265257', '', '', '09056689672'),
(49, '20240519', 'Garduce, Rey Benedict', 'M', 'BSIT', 2, '', '', '', '09056689672'),
(50, '20250738', 'Galan, Jan Mark S.', 'M', 'BSIT', 1, '09637570257', '', '', '09056689672'),
(51, '20250570', 'Lomocso, Adrian T.', 'M', 'BSIT', 1, '09924354591', '', '', '09056689672'),
(52, '20250080', 'Luston, Jaymar I.', 'M', 'BSIT', 1, '09948597337', '', '', '09056689672'),
(53, '20250540', 'Maquiling, Ric Joseph S.', 'M', 'BSIT', 1, '09658777110', '', '', '09056689672'),
(54, '20250534', 'Martir, Lander Keith T.', 'M', 'BSIT', 1, '09218690856', '', '', '09056689672'),
(55, '20250483', 'Mejorada, Kyle Mathew G.', 'M', 'BSIT', 1, '09150856173', '', '', '09056689672'),
(56, '20250119', 'Padriquel, Xian Daniel F.', 'M', 'BSIT', 1, '09850763067', '', '', '09056689672'),
(57, '20250576', 'Pandis, Rodeliza C.', 'F', 'BSIT', 1, '09544421427', '', '', '09056689672'),
(58, '20250633', 'Rodilla, Ken Angelo R.', 'M', 'BSIT', 1, '09383762869', '', '', '09056689672'),
(59, '20250532', 'Rufin, Jan Rodmel M.', 'M', 'BSIT', 1, '09308586700', '', '', '09056689672'),
(60, 'B20230185', 'Sebala, Jason R.', 'M', 'BSIT', 1, '09396143600', '', '', '09056689672'),
(61, '20250588', 'Sian, Lilian Paula Marie T.', 'F', 'BSIT', 1, '09167393236', '', '', '09056689672'),
(62, '20250497', 'Tamon, John Jacob B.', 'M', 'BSIT', 1, '09561665575', '', '', '09056689672'),
(63, '20250798', 'Villanueva, Junila Althea H.', 'F', 'BSIT', 1, '09052417483', '', '', '09056689672');

-- --------------------------------------------------------

--
-- Table structure for table `student_schedule`
--

CREATE TABLE `student_schedule` (
  `id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `schedule_id` int(11) NOT NULL,
  `class_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `student_schedule`
--

INSERT INTO `student_schedule` (`id`, `student_id`, `schedule_id`, `class_id`, `created_at`, `updated_at`) VALUES
(1, 1, 8, 1, '2026-04-14 15:42:37', '2026-04-14 15:42:37'),
(2, 2, 8, 1, '2026-04-14 15:42:37', '2026-04-14 15:42:37'),
(3, 3, 8, 1, '2026-04-14 15:42:37', '2026-04-14 15:42:37'),
(4, 4, 8, 1, '2026-04-14 15:42:37', '2026-04-14 15:42:37'),
(5, 5, 8, 1, '2026-04-14 15:42:37', '2026-04-14 15:42:37'),
(6, 7, 8, 1, '2026-04-14 15:42:37', '2026-04-14 15:42:37'),
(7, 8, 8, 1, '2026-04-14 15:42:37', '2026-04-14 15:42:37'),
(8, 9, 8, 1, '2026-04-14 15:42:37', '2026-04-14 15:42:37'),
(9, 10, 8, 1, '2026-04-14 15:42:37', '2026-04-14 15:42:37'),
(10, 50, 8, 1, '2026-04-14 15:42:37', '2026-04-14 15:42:37'),
(11, 51, 8, 1, '2026-04-14 15:42:37', '2026-04-14 15:42:37'),
(12, 52, 8, 1, '2026-04-14 15:42:37', '2026-04-14 15:42:37'),
(13, 53, 8, 1, '2026-04-14 15:42:37', '2026-04-14 15:42:37'),
(14, 54, 8, 1, '2026-04-14 15:42:37', '2026-04-14 15:42:37'),
(15, 55, 8, 1, '2026-04-14 15:42:37', '2026-04-14 15:42:37'),
(16, 56, 8, 1, '2026-04-14 15:42:37', '2026-04-14 15:42:37'),
(17, 57, 8, 1, '2026-04-14 15:42:37', '2026-04-14 15:42:37'),
(18, 58, 8, 1, '2026-04-14 15:42:37', '2026-04-14 15:42:37'),
(19, 59, 8, 1, '2026-04-14 15:42:37', '2026-04-14 15:42:37'),
(20, 60, 8, 1, '2026-04-14 15:42:37', '2026-04-14 15:42:37'),
(21, 61, 8, 1, '2026-04-14 15:42:37', '2026-04-14 15:42:37'),
(22, 62, 8, 1, '2026-04-14 15:42:37', '2026-04-14 15:42:37'),
(23, 63, 8, 1, '2026-04-14 15:42:37', '2026-04-14 15:42:37'),
(32, 11, 7, 2, '2026-04-14 15:42:37', '2026-04-14 15:42:37'),
(33, 12, 7, 2, '2026-04-14 15:42:37', '2026-04-14 15:42:37'),
(34, 13, 7, 2, '2026-04-14 15:42:37', '2026-04-14 15:42:37'),
(35, 14, 7, 2, '2026-04-14 15:42:37', '2026-04-14 15:42:37'),
(36, 15, 7, 2, '2026-04-14 15:42:37', '2026-04-14 15:42:37'),
(37, 16, 7, 2, '2026-04-14 15:42:37', '2026-04-14 15:42:37'),
(38, 17, 7, 2, '2026-04-14 15:42:37', '2026-04-14 15:42:37'),
(39, 18, 7, 2, '2026-04-14 15:42:37', '2026-04-14 15:42:37'),
(40, 19, 7, 2, '2026-04-14 15:42:37', '2026-04-14 15:42:37'),
(41, 20, 7, 2, '2026-04-14 15:42:37', '2026-04-14 15:42:37'),
(42, 21, 7, 2, '2026-04-14 15:42:37', '2026-04-14 15:42:37'),
(43, 22, 7, 2, '2026-04-14 15:42:37', '2026-04-14 15:42:37'),
(44, 23, 7, 2, '2026-04-14 15:42:37', '2026-04-14 15:42:37'),
(45, 24, 7, 2, '2026-04-14 15:42:37', '2026-04-14 15:42:37'),
(46, 25, 7, 2, '2026-04-14 15:42:37', '2026-04-14 15:42:37'),
(47, 26, 7, 2, '2026-04-14 15:42:37', '2026-04-14 15:42:37'),
(63, 27, 10, 3, '2026-04-14 15:42:37', '2026-04-14 15:42:37'),
(64, 28, 10, 3, '2026-04-14 15:42:37', '2026-04-14 15:42:37'),
(65, 29, 10, 3, '2026-04-14 15:42:37', '2026-04-14 15:42:37'),
(66, 30, 10, 3, '2026-04-14 15:42:37', '2026-04-14 15:42:37'),
(67, 31, 10, 3, '2026-04-14 15:42:37', '2026-04-14 15:42:37'),
(68, 32, 10, 3, '2026-04-14 15:42:37', '2026-04-14 15:42:37'),
(69, 33, 10, 3, '2026-04-14 15:42:37', '2026-04-14 15:42:37'),
(70, 34, 10, 3, '2026-04-14 15:42:37', '2026-04-14 15:42:37'),
(71, 35, 10, 3, '2026-04-14 15:42:37', '2026-04-14 15:42:37'),
(72, 36, 10, 3, '2026-04-14 15:42:37', '2026-04-14 15:42:37'),
(73, 37, 10, 3, '2026-04-14 15:42:37', '2026-04-14 15:42:37'),
(74, 38, 10, 3, '2026-04-14 15:42:37', '2026-04-14 15:42:37'),
(75, 39, 10, 3, '2026-04-14 15:42:37', '2026-04-14 15:42:37'),
(76, 40, 10, 3, '2026-04-14 15:42:37', '2026-04-14 15:42:37'),
(77, 41, 10, 3, '2026-04-14 15:42:37', '2026-04-14 15:42:37'),
(78, 42, 10, 3, '2026-04-14 15:42:37', '2026-04-14 15:42:37'),
(79, 43, 10, 3, '2026-04-14 15:42:37', '2026-04-14 15:42:37'),
(80, 44, 10, 3, '2026-04-14 15:42:37', '2026-04-14 15:42:37'),
(81, 45, 10, 3, '2026-04-14 15:42:37', '2026-04-14 15:42:37'),
(82, 46, 10, 3, '2026-04-14 15:42:37', '2026-04-14 15:42:37'),
(83, 47, 10, 3, '2026-04-14 15:42:37', '2026-04-14 15:42:37'),
(94, 8, 12, 4, '2026-04-14 15:42:37', '2026-04-14 15:42:37'),
(95, 27, 12, 4, '2026-04-14 15:42:37', '2026-04-14 15:42:37'),
(96, 28, 12, 4, '2026-04-14 15:42:37', '2026-04-14 15:42:37'),
(97, 29, 12, 4, '2026-04-14 15:42:37', '2026-04-14 15:42:37'),
(98, 30, 12, 4, '2026-04-14 15:42:37', '2026-04-14 15:42:37'),
(99, 31, 12, 4, '2026-04-14 15:42:37', '2026-04-14 15:42:37'),
(100, 32, 12, 4, '2026-04-14 15:42:37', '2026-04-14 15:42:37'),
(101, 33, 12, 4, '2026-04-14 15:42:37', '2026-04-14 15:42:37'),
(102, 34, 12, 4, '2026-04-14 15:42:37', '2026-04-14 15:42:37'),
(103, 35, 12, 4, '2026-04-14 15:42:37', '2026-04-14 15:42:37'),
(104, 36, 12, 4, '2026-04-14 15:42:37', '2026-04-14 15:42:37'),
(105, 37, 12, 4, '2026-04-14 15:42:37', '2026-04-14 15:42:37'),
(106, 38, 12, 4, '2026-04-14 15:42:37', '2026-04-14 15:42:37'),
(107, 39, 12, 4, '2026-04-14 15:42:37', '2026-04-14 15:42:37'),
(108, 40, 12, 4, '2026-04-14 15:42:37', '2026-04-14 15:42:37'),
(109, 41, 12, 4, '2026-04-14 15:42:37', '2026-04-14 15:42:37'),
(110, 42, 12, 4, '2026-04-14 15:42:37', '2026-04-14 15:42:37'),
(111, 43, 12, 4, '2026-04-14 15:42:37', '2026-04-14 15:42:37'),
(112, 44, 12, 4, '2026-04-14 15:42:37', '2026-04-14 15:42:37'),
(113, 45, 12, 4, '2026-04-14 15:42:37', '2026-04-14 15:42:37'),
(114, 46, 12, 4, '2026-04-14 15:42:37', '2026-04-14 15:42:37'),
(115, 47, 12, 4, '2026-04-14 15:42:37', '2026-04-14 15:42:37'),
(116, 48, 12, 4, '2026-04-14 15:42:37', '2026-04-14 15:42:37'),
(117, 49, 12, 4, '2026-04-14 15:42:37', '2026-04-14 15:42:37');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `classes`
--
ALTER TABLE `classes`
  ADD PRIMARY KEY (`class_id`);

--
-- Indexes for table `schedules`
--
ALTER TABLE `schedules`
  ADD PRIMARY KEY (`schedule_id`);

--
-- Indexes for table `students`
--
ALTER TABLE `students`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `student_schedule`
--
ALTER TABLE `student_schedule`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_student_schedule` (`student_id`,`schedule_id`,`class_id`),
  ADD KEY `idx_student_id` (`student_id`),
  ADD KEY `idx_schedule_id` (`schedule_id`),
  ADD KEY `idx_class_id` (`class_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `classes`
--
ALTER TABLE `classes`
  MODIFY `class_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `schedules`
--
ALTER TABLE `schedules`
  MODIFY `schedule_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `students`
--
ALTER TABLE `students`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=64;

--
-- AUTO_INCREMENT for table `student_schedule`
--
ALTER TABLE `student_schedule`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=118;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `student_schedule`
--
ALTER TABLE `student_schedule`
  ADD CONSTRAINT `fk_student_schedule_class` FOREIGN KEY (`class_id`) REFERENCES `classes` (`class_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_student_schedule_schedule` FOREIGN KEY (`schedule_id`) REFERENCES `schedules` (`schedule_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_student_schedule_student` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
