-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: May 13, 2026 at 04:12 PM
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
-- Database: `g6_reports_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `attendance_archive`
--

CREATE TABLE `attendance_archive` (
  `id` int(11) NOT NULL,
  `g4_record_id` int(11) NOT NULL,
  `user_id` varchar(20) NOT NULL,
  `date` date NOT NULL,
  `status` enum('Present','Absent','Late','Excused') DEFAULT NULL,
  `check_in` time DEFAULT NULL,
  `received_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `attendance_data`
--

CREATE TABLE `attendance_data` (
  `id` int(11) NOT NULL,
  `g4_record_id` int(11) NOT NULL,
  `user_id` varchar(20) NOT NULL,
  `date` date NOT NULL,
  `status` enum('Present','Absent','Late','Excused') DEFAULT NULL,
  `check_in` time DEFAULT NULL,
  `received_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `reports`
--

CREATE TABLE `reports` (
  `report_id` int(11) NOT NULL,
  `report_type` varchar(20) DEFAULT NULL,
  `report_date` date DEFAULT NULL,
  `data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`data`)),
  `sent_to_admin` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `student_data`
--

CREATE TABLE `student_data` (
  `id` int(11) NOT NULL,
  `g4_profile_id` int(11) NOT NULL,
  `user_id` varchar(20) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `gender` varchar(10) DEFAULT NULL,
  `course` varchar(50) DEFAULT NULL,
  `section` varchar(10) DEFAULT NULL,
  `class_code` varchar(10) DEFAULT NULL,
  `contact` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `received_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `student_data`
--

INSERT INTO `student_data` (`id`, `g4_profile_id`, `user_id`, `name`, `gender`, `course`, `section`, `class_code`, `contact`, `email`, `received_at`) VALUES
(1, 1, 'B2017586', 'Alsado, Kean Rose M.', 'F', 'BSIT', '1A', 'A4', '09691082153', '', '2026-04-20 09:23:32'),
(2, 2, '20250249', 'Arriola, Adrian James T.', 'M', 'BSIT', '1A', 'A4', '09564038676', 'Vasel.arriola2002@gmail.com', '2026-04-20 09:23:32'),
(3, 3, '20250815', 'Barinos, John Homer N.', 'M', 'BSIT', '1A', 'A4', '06956227343', '', '2026-04-20 09:23:32'),
(4, 4, '20250279', 'Diosoy, Vincent Jay S.', 'M', 'BSIT', '1A', 'A4', '09663548543', '', '2026-04-20 09:23:32'),
(5, 5, '20250508', 'Encaja, Karl Timothy E.', 'M', 'BSIT', '1A', 'A4', '09318254576', '', '2026-04-20 09:23:32'),
(6, 7, '20250634', 'Garsula, Polo James A.', 'M', 'BSIT', '1A', 'A4', '09078743241', '', '2026-04-20 09:23:32'),
(7, 8, '20240297', 'Germo, Oya S.', 'M', 'BSIT', '1A', 'A4', '09912693704', '', '2026-04-20 09:23:32'),
(8, 9, '20250813', 'Gonzales, Chris John S.', 'F', 'BSIT', '1A', 'A4', '09121381030', '', '2026-04-20 09:23:32'),
(9, 10, 'B20240540', 'Grande, Althea Kassandra A.', 'F', 'BSIT', '1A', 'A4', '09453816399', 'althea.grande.s@southlandcollege.edu.ph', '2026-04-20 09:23:32'),
(10, 11, '20250820', 'Alfuente, Marvin Neil M.', 'M', 'BSIT', '2A', 'A30', '', '', '2026-04-20 09:23:32'),
(11, 12, '20240414', 'Arimas, John Paul T.', 'M', 'BSIT', '2A', 'A30', '09956214032', '', '2026-04-20 09:23:32'),
(12, 13, 'B20230123', 'Calalas, Loranz Ali A.', 'M', 'BSIT', '2A', 'A30', '09183219440', '', '2026-04-20 09:23:32'),
(13, 14, '20240516', 'Francia Jr., Edgar B.', 'M', 'BSIT', '2A', 'A30', '09850803845', '', '2026-04-20 09:23:32'),
(14, 15, '20250857', 'Gagatam, Denny C.', 'F', 'BSIT', '2A', 'A30', '09657923669', '', '2026-04-20 09:23:32'),
(15, 16, '20240478', 'Hsu, Cheng Huang R.', 'M', 'BSIT', '2A', 'A28', '09158763035', '', '2026-04-20 09:23:32'),
(16, 17, '20240227', 'Hulguin, John Paul E.', 'M', 'BSIT', '2A', 'A28', '09157119687', '', '2026-04-20 09:23:32'),
(17, 18, '20240415', 'Lopez, Lyzander Alexius C.', 'M', 'BSIT', '2A', 'A30', '09761597013', '', '2026-04-20 09:23:32'),
(18, 19, '20250658', 'Martir, Kent Jonil P.', 'M', 'BSIT', '2A', 'A30', '09567113247', '', '2026-04-20 09:23:32'),
(19, 20, '20240176', 'Molarto, Ivan Clint D.', 'M', 'BSIT', '2A', 'A28', '09701978001', '', '2026-04-20 09:23:32'),
(20, 21, '20240140', 'Putong, Reman Jr. F.', 'M', 'BSIT', '2A', 'A30', '09087062227', '', '2026-04-20 09:23:32'),
(21, 22, '20240147', 'Quillo, Skitch G.', 'M', 'BSIT', '2A', 'A28', '09127831714', '', '2026-04-20 09:23:32'),
(22, 23, '20240186', 'Servano, Eunice Pearl E.', 'F', 'BSIT', '2A', 'A30', '09064538148', '', '2026-04-20 09:23:32'),
(23, 24, '20220187', 'Tabacolde, May Chelle A.', 'F', 'BSIT', '2A', 'A28', '09272356140', '', '2026-04-20 09:23:32'),
(24, 25, '20240260', 'Tayco, Elderie John C.', 'M', 'BSIT', '2A', 'A30', '09673240757', '', '2026-04-20 09:23:32'),
(25, 26, '20240278', 'Zulueta, Cj J.', 'M', 'BSIT', '2A', 'A28', '09461521354', '', '2026-04-20 09:23:32'),
(26, 27, '20220003', 'Abella, Mark Allexi T.', 'M', 'BSIT', '2B', 'A37', '09777556450', '', '2026-04-20 09:23:34'),
(27, 28, '20240295', 'Aquino, Zurich Clyde O.', 'M', 'BSIT', '2B', 'A37', '09601038624', 'zurichclyde.aquino.s@southlandcollege.edu.ph', '2026-04-20 09:23:34'),
(28, 29, '20240225', 'Bagaporo, Alexa P.', 'F', 'BSIT', '2B', 'A37', '09936412673', '', '2026-04-20 09:23:34'),
(29, 30, '20240253', 'Bendol, Reynalie T.', 'F', 'BSIT', '2B', 'A37', '09673225253', '', '2026-04-20 09:23:34'),
(30, 31, '20210365', 'Bruno, Robert Dave B.', 'M', 'BSIT', '2B', 'A37', '09162199005', '', '2026-04-20 09:23:34'),
(31, 32, '20240234', 'Castillo JR., Alfred John S.', 'M', 'BSIT', '2B', 'A37', '', '', '2026-04-20 09:23:34'),
(32, 34, '20240044', 'David, Val Zendrick C.', 'M', 'BSIT', '2B', 'A37', '09070042451', '', '2026-04-20 09:23:34'),
(33, 35, '20240208', 'De Leon, Nicca A.', 'F', 'BSIT', '2B', 'A37', '09933889247', '', '2026-04-20 09:23:34'),
(34, 36, '20240166', 'Eguis, Crisly Joy G.', 'F', 'BSIT', '2B', 'A37', '09512266875', '', '2026-04-20 09:23:34'),
(35, 37, '20240296', 'Fordan, Bless Joy N.', 'F', 'BSIT', '2B', 'A37', '', '', '2026-04-20 09:23:34'),
(36, 38, '20220331', 'Guillepa, Sam C.', 'M', 'BSIT', '2B', 'A37', '09919085250', 'sam.guillepa.s@southlandcollege.edu.ph', '2026-04-20 09:23:34'),
(37, 39, '20220240', 'Jaranilla, Jardi John D.', 'M', 'BSIT', '2B', 'A37', '09127067766', '', '2026-04-20 09:23:34'),
(38, 40, '20220208', 'Lozada, John Paul R.', 'M', 'BSIT', '2B', 'A37', '09468105751', '', '2026-04-20 09:23:34'),
(39, 41, '20220186', 'Mancia, Mark D.', 'M', 'BSIT', '2B', 'A37', '09942098148', '', '2026-04-20 09:23:34'),
(40, 42, '20240142', 'Ordas, Lawrence F.', 'M', 'BSIT', '2B', 'A37', '09919066864', '', '2026-04-20 09:23:34'),
(41, 43, '20240093', 'Panila, Michaellah Luisa S.', 'F', 'BSIT', '2B', 'A37', '09667380079', 'michaelaluisa.panila.s@southlandcollege.edu.ph', '2026-04-20 09:23:34'),
(42, 44, '20210359', 'Sansano, Prince Louie G.', 'M', 'BSIT', '2B', 'A37', '09939436638', '', '2026-04-20 09:23:34'),
(43, 45, '20220174', 'Sevilleno, Julianne Grace Frances A.', 'F', 'BSIT', '2B', 'A37', '09295598035', '', '2026-04-20 09:23:34'),
(44, 46, '20220316', 'Sumugat, Christy Joyce T.', 'F', 'BSIT', '2B', 'A37', '09494577129', 'christyjoyce.sumugat.s@southlandcollege.edu.ph', '2026-04-20 09:23:34'),
(45, 47, '20240190', 'Susada, Sharah Mae C.', 'F', 'BSIT', '2B', 'A37', '09912693660', '', '2026-04-20 09:23:34'),
(46, 48, '20240315', 'De Jesus, Zenneth Braven P.', 'M', 'BSIT', '2B', 'A37', '09927265257', '', '2026-04-20 09:23:34'),
(47, 49, '20240519', 'Garduce, Rey Benedict', 'M', 'BSIT', '2B', 'A37', '', '', '2026-04-20 09:23:34'),
(48, 33, '20250699', 'Cuison, Patrick Reniel D.', 'M', 'BSIT', '3A', 'A51', '09628087554', '', '2026-04-20 09:23:34');

-- --------------------------------------------------------

--
-- Table structure for table `subjects`
--

CREATE TABLE `subjects` (
  `subject_id` int(11) NOT NULL,
  `subject_code` varchar(20) NOT NULL,
  `subject_name` varchar(100) DEFAULT NULL,
  `teacher_name` varchar(100) DEFAULT NULL,
  `course` varchar(50) DEFAULT NULL,
  `section` varchar(10) DEFAULT NULL,
  `room` varchar(20) DEFAULT 'COM LAB A',
  `schedule` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `subjects`
--

INSERT INTO `subjects` (`subject_id`, `subject_code`, `subject_name`, `teacher_name`, `course`, `section`, `room`, `schedule`) VALUES
(1, 'IT 16', 'Quantitative Methods (including Modeling & Simulation)', 'Christine Alpas', 'BSIT', '2A', 'COM LAB A', '5:00-6:30 PM Monday'),
(2, 'IT 16', 'Quantitative Methods (including Modeling & Simulation)', 'Christine Alpas', 'BSIT', '2B', 'COM LAB A', '5:00-6:30 PM Monday'),
(3, 'IT 11', 'Introduction to Human Computer Interaction', 'Andico, Loriejoy P.', 'BSIT', '1A', 'COM LAB A', '9:30-10:30 AM Monday'),
(4, 'ITC 15', 'Information Management 1', 'Andico, Loriejoy P.', 'BSIT', '2A', 'COM LAB A', '8:00-9:30 AM Monday'),
(5, 'ITC 15', 'Information Management 1', 'Andico, Loriejoy P.', 'BSIT', '2B', 'COM LAB A', '8:00-9:30 AM Monday'),
(6, 'ELECIT 103', 'Fundamentals of Database Systems', 'Tagulalac, Daniel Jr.', 'BSIT', '2A', 'COM LAB A', '1:00-2:30 PM Monday'),
(8, 'ELECIT 103', 'Fundamentals of Database Systems', 'Tagulalac, Daniel Jr.', 'BSIT', '2B', 'COM LAB A', '10:30-11:30 AM Monday'),
(9, 'IT 22', 'Information Assurance & Security 1', 'Tagulalac, Daniel Jr.', 'BSIT', '3A', 'COM LAB A', '10:30 AM-12:00 PM Monday');

-- --------------------------------------------------------

--
-- Table structure for table `subject_enrollment`
--

CREATE TABLE `subject_enrollment` (
  `user_id` varchar(20) DEFAULT NULL,
  `subject_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `subject_enrollment`
--

INSERT INTO `subject_enrollment` (`user_id`, `subject_id`) VALUES
('B2017586', 3),
('20250249', 3),
('20250815', 3),
('20250279', 3),
('20250508', 3),
('20250634', 3),
('20240297', 3),
('20250813', 3),
('B20240540', 3),
('20250820', 1),
('20240414', 1),
('B20230123', 1),
('20240516', 1),
('20250857', 1),
('20240478', 1),
('20240227', 1),
('20240415', 1),
('20250658', 1),
('20240176', 1),
('20240140', 1),
('20240147', 1),
('20240186', 1),
('20220187', 1),
('20240260', 1),
('20240278', 1),
('20250820', 4),
('20240414', 4),
('B20230123', 4),
('20240516', 4),
('20250857', 4),
('20240478', 4),
('20240227', 4),
('20240415', 4),
('20250658', 4),
('20240176', 4),
('20240140', 4),
('20240147', 4),
('20240186', 4),
('20220187', 4),
('20240260', 4),
('20240278', 4),
('20250820', 6),
('20240414', 6),
('B20230123', 6),
('20240516', 6),
('20250857', 6),
('20240478', 6),
('20240227', 6),
('20240415', 6),
('20250658', 6),
('20240176', 6),
('20240140', 6),
('20240147', 6),
('20240186', 6),
('20220187', 6),
('20240260', 6),
('20240278', 6),
('20220003', 2),
('20240295', 2),
('20240225', 2),
('20240253', 2),
('20210365', 2),
('20240234', 2),
('20240044', 2),
('20240208', 2),
('20240166', 2),
('20240296', 2),
('20220331', 2),
('20220240', 2),
('20220208', 2),
('20220186', 2),
('20240142', 2),
('20240093', 2),
('20210359', 2),
('20220174', 2),
('20220316', 2),
('20240190', 2),
('20240315', 2),
('20240519', 2),
('20220003', 5),
('20240295', 5),
('20240225', 5),
('20240253', 5),
('20210365', 5),
('20240234', 5),
('20240044', 5),
('20240208', 5),
('20240166', 5),
('20240296', 5),
('20220331', 5),
('20220240', 5),
('20220208', 5),
('20220186', 5),
('20240142', 5),
('20240093', 5),
('20210359', 5),
('20220174', 5),
('20220316', 5),
('20240190', 5),
('20240315', 5),
('20240519', 5),
('20220003', 7),
('20240295', 7),
('20240225', 7),
('20240253', 7),
('20210365', 7),
('20240234', 7),
('20240044', 7),
('20240208', 7),
('20240166', 7),
('20240296', 7),
('20220331', 7),
('20220240', 7),
('20220208', 7),
('20220186', 7),
('20240142', 7),
('20240093', 7),
('20210359', 7),
('20220174', 7),
('20220316', 7),
('20240190', 7),
('20240315', 7),
('20240519', 7),
('20220003', 8),
('20240295', 8),
('20240225', 8),
('20240253', 8),
('20210365', 8),
('20240234', 8),
('20240044', 8),
('20240208', 8),
('20240166', 8),
('20240296', 8),
('20220331', 8),
('20220240', 8),
('20220208', 8),
('20220186', 8),
('20240142', 8),
('20240093', 8),
('20210359', 8),
('20220174', 8),
('20220316', 8),
('20240190', 8),
('20240315', 8),
('20240519', 8),
('20250699', 9),
('20250699', 10);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `attendance_archive`
--
ALTER TABLE `attendance_archive`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_date` (`date`),
  ADD KEY `idx_user` (`user_id`);

--
-- Indexes for table `attendance_data`
--
ALTER TABLE `attendance_data`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_date` (`date`),
  ADD KEY `idx_user` (`user_id`);

--
-- Indexes for table `reports`
--
ALTER TABLE `reports`
  ADD PRIMARY KEY (`report_id`);

--
-- Indexes for table `student_data`
--
ALTER TABLE `student_data`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `g4_profile_id` (`g4_profile_id`),
  ADD UNIQUE KEY `user_id` (`user_id`);

--
-- Indexes for table `subjects`
--
ALTER TABLE `subjects`
  ADD PRIMARY KEY (`subject_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `attendance_archive`
--
ALTER TABLE `attendance_archive`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2048;

--
-- AUTO_INCREMENT for table `attendance_data`
--
ALTER TABLE `attendance_data`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2048;

--
-- AUTO_INCREMENT for table `reports`
--
ALTER TABLE `reports`
  MODIFY `report_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `student_data`
--
ALTER TABLE `student_data`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=49;

--
-- AUTO_INCREMENT for table `subjects`
--
ALTER TABLE `subjects`
  MODIFY `subject_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
