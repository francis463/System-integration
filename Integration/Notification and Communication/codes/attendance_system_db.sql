-- phpMyAdmin SQL Dump
-- Attendance System Database
-- Fixed: Added message_history table + full weekly schedules

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+08:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

-- --------------------------------------------------------
-- Database: `attendance_system_db`
-- --------------------------------------------------------

-- --------------------------------------------------------
-- Table: attendance_logs
-- --------------------------------------------------------

CREATE TABLE IF NOT EXISTS `attendance_logs` (
  `log_id` int(11) NOT NULL AUTO_INCREMENT,
  `student_id` varchar(20) DEFAULT NULL,
  `full_name` varchar(100) DEFAULT NULL,
  `class_code` varchar(10) DEFAULT NULL,
  `scanned_by` enum('teacher','student') DEFAULT 'student',
  `status` varchar(20) DEFAULT 'present',
  `attendance_status` enum('on_time','late') DEFAULT 'on_time',
  `scan_time` timestamp NOT NULL DEFAULT current_timestamp(),
  `notification_sent` tinyint(1) DEFAULT 0,
  PRIMARY KEY (`log_id`),
  KEY `idx_student_id` (`student_id`),
  KEY `idx_scan_time` (`scan_time`),
  KEY `idx_class_code` (`class_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------
-- Table: classes
-- --------------------------------------------------------

CREATE TABLE IF NOT EXISTS `classes` (
  `class_id` int(11) NOT NULL AUTO_INCREMENT,
  `class_code` varchar(10) DEFAULT NULL,
  `section` varchar(20) DEFAULT NULL,
  `subject_code` varchar(20) DEFAULT NULL,
  `subject_name` varchar(100) DEFAULT NULL,
  `time_in` time DEFAULT NULL,
  `grace_period_minutes` int(11) DEFAULT 15,
  PRIMARY KEY (`class_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT IGNORE INTO `classes` (`class_id`, `class_code`, `section`, `subject_code`, `subject_name`, `time_in`, `grace_period_minutes`) VALUES
(1, 'A4',  'BSIT 1A', 'IT 11',       'Introduction to Human Computer Interaction', '09:30:00', 15),
(2, 'A37', 'BSIT 2B', 'ITC 15',      'Information Management 1',                  '08:00:00', 15),
(3, 'A28', 'BSIT 2A', 'ELECIT 103',  'Fundamentals of Database Systems',          '13:00:00', 15),
(4, 'A30', 'BSIT 2A', 'IT 16',       'Quantitative Methods',                      '17:00:00', 15),
(5, 'A51', 'BSIT 3A', 'IT 22',       'Information Assurance & Security 1',        '10:30:00', 15);

-- --------------------------------------------------------
-- Table: schedules
-- --------------------------------------------------------

CREATE TABLE IF NOT EXISTS `schedules` (
  `schedule_id` int(11) NOT NULL AUTO_INCREMENT,
  `class_code` varchar(10) DEFAULT NULL,
  `day` varchar(20) DEFAULT NULL,
  `time` varchar(50) DEFAULT NULL,
  `room` varchar(20) DEFAULT NULL,
  `instructor` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`schedule_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT IGNORE INTO `schedules` (`schedule_id`, `class_code`, `day`, `time`, `room`, `instructor`) VALUES
-- Monday
(1,  'A37', 'Monday',    '8:00-9:30 AM',      'COM LAB A', 'Andico, LJ'),
(2,  'A4',  'Monday',    '9:30-10:30 AM',     'COM LAB A', 'Andico, LJ'),
(3,  'A51', 'Monday',    '10:30 AM-12:00 PM', 'COM LAB A', 'Tagulalac, D'),
(4,  'A28', 'Monday',    '1:00-2:30 PM',      'COM LAB A', 'Tagulalac, D'),
(5,  'A30', 'Monday',    '5:00-6:30 PM',      'COM LAB A', 'Alpas, C'),
-- Tuesday
(6,  'A37', 'Tuesday',   '8:00-9:30 AM',      'COM LAB A', 'Andico, LJ'),
(7,  'A4',  'Tuesday',   '9:30-10:30 AM',     'COM LAB A', 'Andico, LJ'),
(8,  'A51', 'Tuesday',   '10:30 AM-12:00 PM', 'COM LAB A', 'Tagulalac, D'),
(9,  'A28', 'Tuesday',   '1:00-2:30 PM',      'COM LAB A', 'Tagulalac, D'),
(10, 'A30', 'Tuesday',   '5:00-6:30 PM',      'COM LAB A', 'Alpas, C'),
-- Wednesday
(11, 'A37', 'Wednesday', '8:00-9:30 AM',      'COM LAB A', 'Andico, LJ'),
(12, 'A4',  'Wednesday', '9:30-10:30 AM',     'COM LAB A', 'Andico, LJ'),
(13, 'A51', 'Wednesday', '10:30 AM-12:00 PM', 'COM LAB A', 'Tagulalac, D'),
(14, 'A28', 'Wednesday', '1:00-2:30 PM',      'COM LAB A', 'Tagulalac, D'),
(15, 'A30', 'Wednesday', '5:00-6:30 PM',      'COM LAB A', 'Alpas, C'),
-- Thursday
(16, 'A37', 'Thursday',  '8:00-9:30 AM',      'COM LAB A', 'Andico, LJ'),
(17, 'A4',  'Thursday',  '9:30-10:30 AM',     'COM LAB A', 'Andico, LJ'),
(18, 'A51', 'Thursday',  '10:30 AM-12:00 PM', 'COM LAB A', 'Tagulalac, D'),
(19, 'A28', 'Thursday',  '1:00-2:30 PM',      'COM LAB A', 'Tagulalac, D'),
(20, 'A30', 'Thursday',  '5:00-6:30 PM',      'COM LAB A', 'Alpas, C'),
-- Friday
(21, 'A37', 'Friday',    '8:00-9:30 AM',      'COM LAB A', 'Andico, LJ'),
(22, 'A4',  'Friday',    '9:30-10:30 AM',     'COM LAB A', 'Andico, LJ'),
(23, 'A51', 'Friday',    '10:30 AM-12:00 PM', 'COM LAB A', 'Tagulalac, D'),
(24, 'A28', 'Friday',    '1:00-2:30 PM',      'COM LAB A', 'Tagulalac, D'),
(25, 'A30', 'Friday',    '5:00-6:30 PM',      'COM LAB A', 'Alpas, C');

-- --------------------------------------------------------
-- Table: parents
-- --------------------------------------------------------

CREATE TABLE IF NOT EXISTS `parents` (
  `parent_id` int(11) NOT NULL AUTO_INCREMENT,
  `student_id` varchar(20) DEFAULT NULL,
  `parent_name` varchar(100) NOT NULL,
  `contact_number` varchar(20) NOT NULL,
  `relationship` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`parent_id`),
  KEY `student_id` (`student_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT IGNORE INTO `parents` (`parent_id`, `student_id`, `parent_name`, `contact_number`, `relationship`) VALUES
(1,  'B2017586',  'Mr. Alsado',    '09691082153', 'Father'),
(2,  '20250249',  'Mrs. Arriola',  '09564038676', 'Mother'),
(3,  '20250815',  'Mr. Barinos',   '06956227343', 'Father'),
(4,  '20250279',  'Mrs. Diosoy',   '09663548543', 'Mother'),
(5,  '20250508',  'Mr. Encaja',    '09318254576', 'Father'),
(6,  '20250634',  'Mrs. Garsula',  '09078743241', 'Mother'),
(7,  '20240297',  'Mr. Germo',     '09912693704', 'Father'),
(8,  '20250813',  'Mrs. Gonzales', '09121381030', 'Mother'),
(9,  'B20240540', 'Mrs. Grande',   '09453816399', 'Mother'),
(10, '20230049',  'Mr. Manlapao',  '09942855227', 'Father'),
(11, '20230157',  'Mrs. Momo',     '09129347325', 'Mother');

-- --------------------------------------------------------
-- Table: students
-- --------------------------------------------------------

CREATE TABLE IF NOT EXISTS `students` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `student_id` varchar(20) DEFAULT NULL,
  `full_name` varchar(100) DEFAULT NULL,
  `gender` varchar(10) DEFAULT NULL,
  `course` varchar(20) DEFAULT NULL,
  `year_level` int(11) DEFAULT NULL,
  `contact` varchar(20) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `student_id` (`student_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT IGNORE INTO `students` (`id`, `student_id`, `full_name`, `gender`, `course`, `year_level`, `contact`, `email`) VALUES
(1,  'B2017586',  'Alsado, Kean Rose M.',              'F',    'BSIT', 1, '09691082153', ''),
(2,  '20250249',  'Arriola, Adrian James T.',           'M',    'BSIT', 1, '09564038676', 'Vasel.arriola2002@gmail.com'),
(3,  '20250815',  'Barinos, John Homer N.',             'M',    'BSIT', 1, '06956227343', ''),
(4,  '20250279',  'Diosoy, Vincent Jay S.',             'M',    'BSIT', 1, '09663548543', ''),
(5,  '20250508',  'Encaja, Karl Timothy E.',            'M',    'BSIT', 1, '09318254576', ''),
(7,  '20250634',  'Garsula, Polo James A.',             'M',    'BSIT', 1, '09078743241', ''),
(8,  '20240297',  'Germo, Oya S.',                     'M',    'BSIT', 1, '09912693704', ''),
(9,  '20250813',  'Gonzales, Chris John S.',            'F',    'BSIT', 1, '09121381030', ''),
(10, 'B20240540', 'Grande, Althea Kassandra A.',        'F',    'BSIT', 1, '09453816399', 'althea.grande.s@southlandcollege.edu.ph'),
(11, '20250820',  'Alfuente, Marvin Neil M.',           'M',    'BSIT', 2, '', ''),
(12, '20240414',  'Arimas, John Paul T.',               'M',    'BSIT', 2, '09956214032', ''),
(13, 'B20230123', 'Calalas, Loranz Ali A.',             'M',    'BSIT', 2, '09183219440', ''),
(14, '20240516',  'Francia Jr., Edgar B.',              'M',    'BSIT', 2, '09850803845', ''),
(15, '20250857',  'Gagatam, Denny C.',                  'F',    'BSIT', 2, '09657923669', ''),
(16, '20240478',  'Hsu, Cheng Huang R.',                'M',    'BSIT', 2, '09158763035', ''),
(17, '20240227',  'Hulguin, John Paul E.',              'M',    'BSIT', 2, '09157119687', ''),
(18, '20240415',  'Lopez, Lyzander Alexius C.',         'M',    'BSIT', 2, '09761597013', ''),
(19, '20250658',  'Martir, Kent Jonil P.',              'M',    'BSIT', 2, '09567113247', ''),
(20, '20240176',  'Molarto, Ivan Clint D.',             'M',    'BSIT', 2, '09701978001', ''),
(21, '20240140',  'Putong, Reman Jr. F.',               'M',    'BSIT', 2, '09087062227', ''),
(22, '20240147',  'Quillo, Skitch G.',                  'M',    'BSIT', 2, '09127831714', ''),
(23, '20240186',  'Servano, Eunice Pearl E.',           'F',    'BSIT', 2, '09064538148', ''),
(24, '20220187',  'Tabacolde, May Chelle A.',           'F',    'BSIT', 2, '09272356140', ''),
(25, '20240260',  'Tayco, Elderie John C.',             'M',    'BSIT', 2, '09673240757', ''),
(26, '20240278',  'Zulueta, Cj J.',                    'M',    'BSIT', 2, '09461521354', ''),
(27, '20220003',  'Abella, Mark Allexi T.',             'M',    'BSIT', 2, '09777556450', ''),
(28, '20240295',  'Aquino, Zurich Clyde O.',            'M',    'BSIT', 2, '09601038624', 'zurichclyde.aquino.s@southlandcollege.edu.ph'),
(29, '20240225',  'Bagaporo, Alexa P.',                 'F',    'BSIT', 2, '09936412673', ''),
(30, '20240253',  'Bendol, Reynalie T.',                'F',    'BSIT', 2, '09673225253', ''),
(31, '20210365',  'Bruno, Robert Dave B.',              'M',    'BSIT', 2, '09162199005', ''),
(32, '20240234',  'Castillo JR., Alfred John S.',       'M',    'BSIT', 2, '', ''),
(33, '20250699',  'Cuison, Patrick Reniel D.',          'M',    'BSIT', 3, '09628087554', ''),
(34, '20240044',  'David, Val Zendrick C.',             'M',    'BSIT', 2, '09070042451', ''),
(35, '20240208',  'De Leon, Nicca A.',                  'F',    'BSIT', 2, '09933889247', ''),
(36, '20240166',  'Eguis, Crisly Joy G.',               'F',    'BSIT', 2, '09512266875', ''),
(37, '20240296',  'Fordan, Bless Joy N.',               'F',    'BSIT', 2, '', ''),
(38, '20220331',  'Guillepa, Sam C.',                   'M',    'BSIT', 2, '09919085250', 'sam.guillepa.s@southlandcollege.edu.ph'),
(39, '20220240',  'Jaranilla, Jardi John D.',           'M',    'BSIT', 2, '09127067766', ''),
(40, '20220208',  'Lozada, John Paul R.',               'M',    'BSIT', 2, '09468105751', ''),
(41, '20220186',  'Mancia, Mark D.',                    'M',    'BSIT', 2, '09942098148', ''),
(42, '20240142',  'Ordas, Lawrence F.',                 'M',    'BSIT', 2, '09919066864', ''),
(43, '20240093',  'Panila, Michaellah Luisa S.',        'F',    'BSIT', 2, '09667380079', 'michaelaluisa.panila.s@southlandcollege.edu.ph'),
(44, '20210359',  'Sansano, Prince Louie G.',           'M',    'BSIT', 2, '09939436638', ''),
(45, '20220174',  'Sevilleno, Julianne Grace Frances A.','F',   'BSIT', 2, '09295598035', ''),
(46, '20220316',  'Sumugat, Christy Joyce T.',          'F',    'BSIT', 2, '09494577129', 'christyjoyce.sumugat.s@southlandcollege.edu.ph'),
(47, '20240190',  'Susada, Sharah Mae C.',              'F',    'BSIT', 2, '09912693660', ''),
(48, '20240315',  'De Jesus, Zenneth Braven P.',        'M',    'BSIT', 2, '09927265257', ''),
(49, '20240519',  'Garduce, Rey Benedict',              'M',    'BSIT', 2, '', ''),
(50, '20230049',  'Carl Manlapao',                      'Male', 'BSIT', 3, '09942855227', NULL),
(51, '20230157',  'John Lloyd Momo',                    'Male', 'BSIT', 3, '09129347325', NULL);

-- --------------------------------------------------------
-- Table: message_history  ← WAS MISSING — FIXED
-- --------------------------------------------------------

CREATE TABLE IF NOT EXISTS `message_history` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `student_id` varchar(20) DEFAULT NULL,
  `student_name` varchar(100) DEFAULT NULL,
  `parent_name` varchar(100) DEFAULT NULL,
  `parent_contact` varchar(20) DEFAULT NULL,
  `message_body` text DEFAULT NULL,
  `attendance_status` enum('on_time','late') DEFAULT 'on_time',
  `scan_time` datetime DEFAULT NULL,
  `status` varchar(20) DEFAULT 'failed',
  `api_response` text DEFAULT NULL,
  `attendance_log_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_student_id` (`student_id`),
  KEY `idx_scan_time` (`scan_time`),
  KEY `idx_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
