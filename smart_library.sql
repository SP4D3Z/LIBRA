-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 10, 2025 at 02:14 AM
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
-- Database: `smart_library`
--

-- --------------------------------------------------------

--
-- Table structure for table `books`
--

CREATE TABLE `books` (
  `book_id` int(11) NOT NULL,
  `isbn` varchar(20) DEFAULT NULL,
  `title` varchar(255) NOT NULL,
  `author` varchar(255) NOT NULL,
  `publisher` varchar(100) DEFAULT NULL,
  `publication_year` year(4) DEFAULT NULL,
  `edition` varchar(50) DEFAULT NULL,
  `category_id` int(11) DEFAULT NULL,
  `total_copies` int(11) NOT NULL DEFAULT 1,
  `available_copies` int(11) NOT NULL DEFAULT 1,
  `price` decimal(10,2) NOT NULL,
  `location` varchar(100) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `condition_status` enum('excellent','good','fair','poor','damaged') DEFAULT 'excellent',
  `is_archived` tinyint(1) DEFAULT 0,
  `added_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `replacement_cost` decimal(10,2) DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `books`
--

INSERT INTO `books` (`book_id`, `isbn`, `title`, `author`, `publisher`, `publication_year`, `edition`, `category_id`, `total_copies`, `available_copies`, `price`, `location`, `description`, `condition_status`, `is_archived`, `added_by`, `created_at`, `updated_at`, `replacement_cost`) VALUES
(1, '123', 'max power', 'max verstappen', 'lewis', '2016', 'special', 9, 10, 11, 26.00, 'race', 'a book about max verstappen', 'excellent', 0, 7, '2025-09-29 14:32:08', '2025-12-09 13:06:29', 0.00),
(4, '1332315115523', 'Mein Kampf', 'Adolph Zigler', 'Nick Fuentes', '2015', NULL, 5, 10, 11, 0.00, NULL, NULL, 'excellent', 0, 6, '2025-10-20 15:20:22', '2025-12-04 13:12:22', 0.00),
(5, '1234567891011', 'Mclaren\'s Downfall', 'Oscar Piastri', 'Charles Leclerc', '2025', NULL, 5, 10, 10, 23.99, NULL, NULL, 'excellent', 0, 6, '2025-10-20 15:21:53', '2025-12-04 13:12:20', 0.00),
(6, '1234567891012', 'Finding Milk', 'Ben Shapiro', 'Nick Fuentes', '2020', 'special edition', 2, 90, 90, 99.00, 'Documents', 'This shows how the blacks avoid child support', 'excellent', 0, 6, '2025-12-03 05:22:45', '2025-12-04 13:32:47', 0.00);

-- --------------------------------------------------------

--
-- Table structure for table `book_damage_reports`
--

CREATE TABLE `book_damage_reports` (
  `report_id` int(11) NOT NULL,
  `book_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `transaction_id` int(11) DEFAULT NULL,
  `damage_type` enum('minor','major','lost') NOT NULL,
  `description` text DEFAULT NULL,
  `assessed_cost` decimal(10,2) DEFAULT 0.00,
  `reported_by` int(11) NOT NULL,
  `reported_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `resolved` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `borrowing_transactions`
--

CREATE TABLE `borrowing_transactions` (
  `transaction_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `book_id` int(11) NOT NULL,
  `borrowed_date` date NOT NULL,
  `due_date` date NOT NULL,
  `returned_date` date DEFAULT NULL,
  `staff_id_borrowed` int(11) DEFAULT NULL,
  `staff_id_returned` int(11) DEFAULT NULL,
  `status` enum('active','returned','overdue','lost') DEFAULT 'active',
  `penalty_amount` decimal(10,2) DEFAULT 0.00,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `semester` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `borrowing_transactions`
--

INSERT INTO `borrowing_transactions` (`transaction_id`, `user_id`, `book_id`, `borrowed_date`, `due_date`, `returned_date`, `staff_id_borrowed`, `staff_id_returned`, `status`, `penalty_amount`, `notes`, `created_at`, `updated_at`, `semester`) VALUES
(7, 2, 1, '2025-09-29', '2025-10-29', '2025-10-04', 2, 2, 'returned', 0.00, NULL, '2025-09-29 14:43:39', '2025-10-04 12:19:36', NULL),
(8, 2, 1, '2025-10-20', '2025-11-19', '2025-10-20', 2, 2, 'returned', 0.00, NULL, '2025-10-20 06:19:36', '2025-10-20 06:19:39', NULL),
(9, 2, 1, '2025-10-20', '2025-11-19', '2025-10-20', 10, 10, 'returned', 0.00, NULL, '2025-10-20 11:04:21', '2025-10-20 11:04:26', NULL),
(10, 2, 6, '2025-12-04', '2026-01-03', '2025-12-04', 12, 12, 'returned', 0.00, NULL, '2025-12-04 13:11:04', '2025-12-04 13:11:21', NULL),
(11, 2, 1, '2025-12-04', '2026-01-03', '2025-12-04', 12, 12, 'returned', 0.00, NULL, '2025-12-04 13:11:09', '2025-12-04 13:11:22', NULL),
(12, 12, 6, '2025-12-04', '2026-01-03', '2025-12-04', 12, 12, 'returned', 0.00, NULL, '2025-12-04 13:11:31', '2025-12-04 13:12:14', NULL),
(13, 2, 1, '2025-12-04', '2026-01-03', '2025-12-04', 12, 12, 'returned', 0.00, NULL, '2025-12-04 13:11:38', '2025-12-04 13:12:13', NULL),
(14, 2, 5, '2025-12-04', '2026-01-03', '2025-12-04', 12, 12, 'returned', 0.00, NULL, '2025-12-04 13:11:42', '2025-12-04 13:12:20', NULL),
(15, 2, 4, '2025-12-04', '2026-01-03', '2025-12-04', 12, 12, 'returned', 0.00, NULL, '2025-12-04 13:11:46', '2025-12-04 13:12:21', NULL),
(16, 2, 6, '2025-12-04', '2026-01-03', '2025-12-04', 12, 6, 'returned', 0.00, NULL, '2025-12-04 13:24:03', '2025-12-04 13:32:36', NULL),
(17, 6, 6, '2025-12-04', '2026-01-03', '2025-12-04', 6, 6, 'returned', 0.00, NULL, '2025-12-04 13:32:41', '2025-12-04 13:32:46', NULL),
(18, 20, 1, '2025-12-09', '2026-01-08', '2025-12-09', 20, 20, 'returned', 0.00, NULL, '2025-12-09 13:05:30', '2025-12-09 13:06:29', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `category_id` int(11) NOT NULL,
  `category_name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`category_id`, `category_name`, `description`, `created_at`) VALUES
(1, 'Fiction', 'Novels, short stories, and other fictional works', '2025-09-28 04:57:30'),
(2, 'Non-Fiction', 'Factual books, biographies, and informational texts', '2025-09-28 04:57:30'),
(3, 'Science & Technology', 'Books related to science, technology, and engineering', '2025-09-28 04:57:30'),
(4, 'Mathematics', 'Mathematical texts and references', '2025-09-28 04:57:30'),
(5, 'History', 'Historical books and documentaries', '2025-09-28 04:57:30'),
(6, 'Literature', 'Classic and contemporary literature', '2025-09-28 04:57:30'),
(7, 'Reference', 'Dictionaries, encyclopedias, and reference materials', '2025-09-28 04:57:30'),
(8, 'Textbooks', 'Academic textbooks for various subjects', '2025-09-28 04:57:30'),
(9, 'Journals', 'Academic and professional journals', '2025-09-28 04:57:30'),
(10, 'Digital Media', 'DVDs, CDs, and other digital resources', '2025-09-28 04:57:30'),
(11, 'Science', NULL, '2025-12-09 02:15:16'),
(12, 'Technology', NULL, '2025-12-09 02:15:16'),
(13, 'Biography', NULL, '2025-12-09 02:15:16');

-- --------------------------------------------------------

--
-- Table structure for table `clearances`
--

CREATE TABLE `clearances` (
  `clearance_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `semester` varchar(20) NOT NULL,
  `academic_year` varchar(10) NOT NULL,
  `clearance_date` date DEFAULT NULL,
  `is_cleared` tinyint(1) DEFAULT 0,
  `outstanding_books` int(11) DEFAULT 0,
  `outstanding_penalties` decimal(10,2) DEFAULT 0.00,
  `staff_id_processed` int(11) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `librarians`
--

CREATE TABLE `librarians` (
  `librarian_id` int(11) NOT NULL,
  `employee_number` varchar(20) NOT NULL,
  `department` varchar(100) DEFAULT 'Library',
  `hire_date` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `librarians`
--

INSERT INTO `librarians` (`librarian_id`, `employee_number`, `department`, `hire_date`) VALUES
(7, 'LIB007', 'CCICT', '2025-09-29');

-- --------------------------------------------------------

--
-- Table structure for table `penalties`
--

CREATE TABLE `penalties` (
  `penalty_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `transaction_id` int(11) DEFAULT NULL,
  `penalty_type` enum('overdue','lost_book','damaged_book','other') NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `description` text DEFAULT NULL,
  `is_paid` tinyint(1) DEFAULT 0,
  `payment_date` date DEFAULT NULL,
  `staff_id_processed` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `reservations`
--

CREATE TABLE `reservations` (
  `reservation_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `book_id` int(11) NOT NULL,
  `reservation_date` date NOT NULL,
  `expiry_date` date NOT NULL,
  `status` enum('pending','ready','fulfilled','expired','cancelled') DEFAULT 'pending',
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `reservations`
--

INSERT INTO `reservations` (`reservation_id`, `user_id`, `book_id`, `reservation_date`, `expiry_date`, `status`, `notes`, `created_at`, `updated_at`) VALUES
(1, 2, 1, '2025-10-04', '2025-10-11', 'pending', NULL, '2025-10-04 12:19:52', '2025-10-04 12:19:52'),
(2, 14, 4, '2025-12-02', '2025-12-09', 'pending', NULL, '2025-12-02 03:16:53', '2025-12-02 03:16:53');

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(50) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`id`, `name`, `description`, `created_at`) VALUES
(1, 'student', 'Student user', '2025-11-15 13:03:29'),
(2, 'teacher', 'Teacher user', '2025-11-15 13:03:29'),
(3, 'staff', 'Staff user', '2025-11-15 13:03:29'),
(4, 'librarian', 'Librarian user', '2025-11-15 13:03:29');

-- --------------------------------------------------------

--
-- Table structure for table `staff`
--

CREATE TABLE `staff` (
  `staff_id` int(11) NOT NULL,
  `employee_number` varchar(20) NOT NULL,
  `department` varchar(100) DEFAULT NULL,
  `position` varchar(50) DEFAULT NULL,
  `hire_date` date DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `staff`
--

INSERT INTO `staff` (`staff_id`, `employee_number`, `department`, `position`, `hire_date`, `user_id`) VALUES
(6, 'STF0006', '', 'Advocator', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `students`
--

CREATE TABLE `students` (
  `student_id` int(11) NOT NULL,
  `student_number` varchar(20) NOT NULL,
  `program` varchar(100) DEFAULT NULL,
  `year_level` int(11) DEFAULT NULL,
  `semester_start_date` date DEFAULT NULL,
  `semester_end_date` date DEFAULT NULL,
  `max_books_allowed` int(11) DEFAULT 3,
  `is_cleared` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `students`
--

INSERT INTO `students` (`student_id`, `student_number`, `program`, `year_level`, `semester_start_date`, `semester_end_date`, `max_books_allowed`, `is_cleared`) VALUES
(2, 'STU0002', 'BSIT', 3, NULL, NULL, 3, 0),
(10, 'STU0010', NULL, NULL, NULL, NULL, 3, 0),
(12, 'STU0012', NULL, NULL, NULL, NULL, 3, 0);

-- --------------------------------------------------------

--
-- Table structure for table `system_logs`
--

CREATE TABLE `system_logs` (
  `log_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `action` varchar(100) NOT NULL,
  `table_affected` varchar(50) DEFAULT NULL,
  `record_id` int(11) DEFAULT NULL,
  `old_values` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`old_values`)),
  `new_values` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`new_values`)),
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `teachers`
--

CREATE TABLE `teachers` (
  `teacher_id` int(11) NOT NULL,
  `employee_number` varchar(20) NOT NULL,
  `department` varchar(100) DEFAULT NULL,
  `position` varchar(50) DEFAULT NULL,
  `semester_start_date` date DEFAULT NULL,
  `semester_end_date` date DEFAULT NULL,
  `is_cleared` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `teachers`
--

INSERT INTO `teachers` (`teacher_id`, `employee_number`, `department`, `position`, `semester_start_date`, `semester_end_date`, `is_cleared`) VALUES
(14, 'TEA0014', NULL, NULL, NULL, NULL, 0);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `first_name` varchar(50) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `user_type` enum('student','teacher','librarian','staff') NOT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_at` datetime DEFAULT NULL,
  `cleared_at` datetime DEFAULT NULL,
  `cleared_by` int(11) DEFAULT NULL,
  `last_clearance_semester` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `username`, `password_hash`, `first_name`, `last_name`, `phone`, `address`, `user_type`, `is_active`, `created_at`, `updated_at`, `deleted_at`, `cleared_at`, `cleared_by`, `last_clearance_semester`) VALUES
(2, 'spade', '$2y$10$jkMCeZ1a3WHR1Z3KaOF.YuPP2MyfVeQNmg8nzscXb5/ZJ9Tj2XmLa', 'allen', 'shelby', NULL, NULL, 'student', 0, '2025-09-28 05:03:28', '2025-12-09 04:33:00', NULL, NULL, NULL, NULL),
(6, 'kirk123', '$2y$10$07xruovzzCm0rRLkVS84g.ffxZkDaxB2ZpLMvvN53Y56ee4UwpNrK', 'charlie', 'kirk', NULL, NULL, 'staff', 1, '2025-09-28 06:22:51', '2025-09-28 06:22:51', NULL, NULL, NULL, NULL),
(7, 'admin', '$2y$10$KRWY3Xsrka0eAkQmgrtjQuAVfFKXCOxCwlouHF0R9.SkJZSBFn5ES', 'max', 'verstappen', NULL, NULL, 'librarian', 0, '2025-09-29 02:17:33', '2025-12-08 15:13:59', NULL, NULL, NULL, NULL),
(10, 'mclaren', '$2y$10$mJxxN/X8Wit3HYfbJeDxw.MA.9BEPyzguU4O1g6iTzZnHtjRwThta', 'Oscar', 'Piastri', NULL, NULL, 'student', 0, '2025-10-20 10:57:04', '2025-10-20 11:52:52', NULL, NULL, NULL, NULL),
(12, 'Checo', '$2y$10$7ahK1kD6Lhcq5Oz5yKwSCO4IP0cwAFV0s3HWuAp2riefVxKrjm0a6', 'Sergio', 'Perez', NULL, NULL, 'student', 0, '2025-11-15 12:35:36', '2025-12-08 15:14:09', NULL, NULL, NULL, NULL),
(14, 'RIP123', '$2y$10$Q2UdKUqXjsu1xrieKiHPxerpjHS1HxHQPgcXVBg4tpzRFeYAL.jNC', 'George', 'Floyyd', NULL, NULL, 'teacher', 1, '2025-12-02 03:06:35', '2025-12-02 03:06:35', NULL, NULL, NULL, NULL),
(19, 'Librarian123', '$2y$10$2UogWhfwioq5LdL/kaUlkuPKew6VMcyitErm5gE.4HdGMKdhfZ9lm', 'Albert', 'Epstein', '092901334', 'West Coast', 'librarian', 1, '2025-12-09 02:13:31', '2025-12-09 02:13:31', NULL, NULL, NULL, NULL),
(20, 'spo1heat3', '$2y$10$HbCkziHUN2jqLomF1thkdeA4VtLISmLpf69XkIQ59oCpRgiSjeRwS', 'Allen', 'Briones', '092763245', 'Cebu', 'student', 1, '2025-12-09 04:34:26', '2025-12-09 04:34:26', NULL, NULL, NULL, NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `books`
--
ALTER TABLE `books`
  ADD PRIMARY KEY (`book_id`),
  ADD UNIQUE KEY `isbn` (`isbn`),
  ADD KEY `category_id` (`category_id`),
  ADD KEY `idx_books_title` (`title`),
  ADD KEY `idx_books_author` (`author`),
  ADD KEY `idx_books_isbn` (`isbn`),
  ADD KEY `fk_books_added_by` (`added_by`);

--
-- Indexes for table `book_damage_reports`
--
ALTER TABLE `book_damage_reports`
  ADD PRIMARY KEY (`report_id`),
  ADD KEY `book_id` (`book_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `transaction_id` (`transaction_id`),
  ADD KEY `reported_by` (`reported_by`);

--
-- Indexes for table `borrowing_transactions`
--
ALTER TABLE `borrowing_transactions`
  ADD PRIMARY KEY (`transaction_id`),
  ADD KEY `book_id` (`book_id`),
  ADD KEY `idx_borrowing_user_id` (`user_id`),
  ADD KEY `idx_borrowing_status` (`status`),
  ADD KEY `idx_borrowing_due_date` (`due_date`),
  ADD KEY `borrowing_transactions_ibfk_3` (`staff_id_borrowed`),
  ADD KEY `borrowing_transactions_ibfk_4` (`staff_id_returned`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`category_id`),
  ADD UNIQUE KEY `category_name` (`category_name`);

--
-- Indexes for table `clearances`
--
ALTER TABLE `clearances`
  ADD PRIMARY KEY (`clearance_id`),
  ADD UNIQUE KEY `unique_user_semester` (`user_id`,`semester`,`academic_year`),
  ADD KEY `staff_id_processed` (`staff_id_processed`);

--
-- Indexes for table `librarians`
--
ALTER TABLE `librarians`
  ADD PRIMARY KEY (`librarian_id`),
  ADD UNIQUE KEY `employee_number` (`employee_number`);

--
-- Indexes for table `penalties`
--
ALTER TABLE `penalties`
  ADD PRIMARY KEY (`penalty_id`),
  ADD KEY `transaction_id` (`transaction_id`),
  ADD KEY `staff_id_processed` (`staff_id_processed`),
  ADD KEY `idx_penalties_user_id` (`user_id`),
  ADD KEY `idx_penalties_paid` (`is_paid`);

--
-- Indexes for table `reservations`
--
ALTER TABLE `reservations`
  ADD PRIMARY KEY (`reservation_id`),
  ADD KEY `book_id` (`book_id`),
  ADD KEY `idx_reservations_user_id` (`user_id`),
  ADD KEY `idx_reservations_status` (`status`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `staff`
--
ALTER TABLE `staff`
  ADD PRIMARY KEY (`staff_id`),
  ADD UNIQUE KEY `employee_number` (`employee_number`),
  ADD UNIQUE KEY `user_id` (`user_id`);

--
-- Indexes for table `students`
--
ALTER TABLE `students`
  ADD PRIMARY KEY (`student_id`),
  ADD UNIQUE KEY `student_number` (`student_number`);

--
-- Indexes for table `system_logs`
--
ALTER TABLE `system_logs`
  ADD PRIMARY KEY (`log_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `teachers`
--
ALTER TABLE `teachers`
  ADD PRIMARY KEY (`teacher_id`),
  ADD UNIQUE KEY `employee_number` (`employee_number`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD KEY `idx_users_type` (`user_type`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `books`
--
ALTER TABLE `books`
  MODIFY `book_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `book_damage_reports`
--
ALTER TABLE `book_damage_reports`
  MODIFY `report_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `borrowing_transactions`
--
ALTER TABLE `borrowing_transactions`
  MODIFY `transaction_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `category_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `clearances`
--
ALTER TABLE `clearances`
  MODIFY `clearance_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `penalties`
--
ALTER TABLE `penalties`
  MODIFY `penalty_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `reservations`
--
ALTER TABLE `reservations`
  MODIFY `reservation_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `system_logs`
--
ALTER TABLE `system_logs`
  MODIFY `log_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `books`
--
ALTER TABLE `books`
  ADD CONSTRAINT `books_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`category_id`),
  ADD CONSTRAINT `fk_books_added_by` FOREIGN KEY (`added_by`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `book_damage_reports`
--
ALTER TABLE `book_damage_reports`
  ADD CONSTRAINT `book_damage_reports_ibfk_1` FOREIGN KEY (`book_id`) REFERENCES `books` (`book_id`),
  ADD CONSTRAINT `book_damage_reports_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `book_damage_reports_ibfk_3` FOREIGN KEY (`transaction_id`) REFERENCES `borrowing_transactions` (`transaction_id`),
  ADD CONSTRAINT `book_damage_reports_ibfk_4` FOREIGN KEY (`reported_by`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `borrowing_transactions`
--
ALTER TABLE `borrowing_transactions`
  ADD CONSTRAINT `borrowing_transactions_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `borrowing_transactions_ibfk_2` FOREIGN KEY (`book_id`) REFERENCES `books` (`book_id`),
  ADD CONSTRAINT `borrowing_transactions_ibfk_3` FOREIGN KEY (`staff_id_borrowed`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `borrowing_transactions_ibfk_4` FOREIGN KEY (`staff_id_returned`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `clearances`
--
ALTER TABLE `clearances`
  ADD CONSTRAINT `clearances_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `clearances_ibfk_2` FOREIGN KEY (`staff_id_processed`) REFERENCES `staff` (`staff_id`);

--
-- Constraints for table `librarians`
--
ALTER TABLE `librarians`
  ADD CONSTRAINT `librarians_ibfk_1` FOREIGN KEY (`librarian_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `penalties`
--
ALTER TABLE `penalties`
  ADD CONSTRAINT `penalties_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `penalties_ibfk_2` FOREIGN KEY (`transaction_id`) REFERENCES `borrowing_transactions` (`transaction_id`),
  ADD CONSTRAINT `penalties_ibfk_3` FOREIGN KEY (`staff_id_processed`) REFERENCES `staff` (`staff_id`);

--
-- Constraints for table `reservations`
--
ALTER TABLE `reservations`
  ADD CONSTRAINT `reservations_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `reservations_ibfk_2` FOREIGN KEY (`book_id`) REFERENCES `books` (`book_id`);

--
-- Constraints for table `staff`
--
ALTER TABLE `staff`
  ADD CONSTRAINT `staff_ibfk_1` FOREIGN KEY (`staff_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `staff_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `students`
--
ALTER TABLE `students`
  ADD CONSTRAINT `students_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `system_logs`
--
ALTER TABLE `system_logs`
  ADD CONSTRAINT `system_logs_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `teachers`
--
ALTER TABLE `teachers`
  ADD CONSTRAINT `teachers_ibfk_1` FOREIGN KEY (`teacher_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
