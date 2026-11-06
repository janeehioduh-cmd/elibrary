-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 06, 2026 at 04:43 PM
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
-- Database: `elibrary`
--

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `id` int(11) NOT NULL,
  `name` varchar(150) NOT NULL,
  `email` varchar(150) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`id`, `name`, `email`, `password`) VALUES
(1, 'jane ehi oduh', 'janeehioduh@gmail.com', '$2y$12$OiIrHTefDqqsPpv8TeyiZe5x9wRheHAyOEKfBvZO4rR/KcQzrqJey');

-- --------------------------------------------------------

--
-- Table structure for table `books`
--

CREATE TABLE `books` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `author` varchar(150) NOT NULL,
  `category_id` int(11) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `is_available` tinyint(1) DEFAULT 1,
  `cover_image` varchar(255) DEFAULT NULL,
  `coverimage` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `books`
--

INSERT INTO `books` (`id`, `title`, `author`, `category_id`, `description`, `is_available`, `cover_image`, `coverimage`) VALUES
(1, 'History of English Literature', 'Dr. Rajesh Singh', 4, 'A comprehensive guide to the evolution of English literature from the Age of Chaucer to the modern era. It covers the major periods including the Elizabethan Age, the Augustan Age, and the Romantic Age, along with key writers, their works, and the social and cultural influences that shaped them. A useful resource for students, teachers, and lovers of English literature.', 1, 'uploads/books/1cdb6a4c37a91e508a1c68ccc26dc3ff.jpg', NULL),
(2, 'Introduction to Political Science', 'Prof. Dr. Muhammad Sarwar', 4, 'A beginner-friendly introduction to the basic concepts of political science. It explains the state, government, democracy, political systems, constitutions, and the roles of citizens and institutions in a clear and simple way. Perfect for students who are just starting to study politics, law, and public administration.', 1, 'uploads/books/aac95a3c439234a19cc042e9ff901117.jpg', NULL),
(3, 'Introduction to General Chemistry (Second Edition)', 'Jerome K. Holmes', 4, 'A clear introduction to the fundamentals of general chemistry. It covers atoms, elements, compounds, chemical reactions, the periodic table, bonding, acids and bases, and basic laboratory concepts in a simple, student-friendly way. Ideal for beginners, high school and first-year university students who want a solid foundation in chemistry.', 1, 'uploads/books/c6395e2d7e2b4589f7ff1d65688a6451.jpg', NULL),
(4, 'Introductory Principles in Physics (Second Edition)', 'John D. Mays', 4, 'A simple and clear introduction to the core principles of physics. It covers motion, forces, energy, electricity, magnetism, waves, light, and modern physics, with practical examples and experiments to help students understand how the physical world works. Ideal for high school and first-year students who are new to physics.', 1, 'uploads/books/ce5bbd0d92d57322004fdcc906de2da3.jpg', NULL),
(5, 'A History of Political Theory', 'George H. Sabine and Thomas L. Thorson', 4, 'A classic textbook that traces the development of political thought from ancient Greece to the modern era. It explores the ideas of major thinkers like Plato, Aristotle, Machiavelli, Hobbes, Locke, Rousseau, and Marx, and explains how their theories shaped government, democracy, and society. An essential read for students of political science, history, law, and philosophy.', 1, 'uploads/books/e84c4c73fe9ecf20fa61ba9b2ec8ce58.jpg', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `book_images`
--

CREATE TABLE `book_images` (
  `id` int(11) NOT NULL,
  `book_id` int(11) NOT NULL,
  `image_path` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `book_images`
--

INSERT INTO `book_images` (`id`, `book_id`, `image_path`, `created_at`) VALUES
(2, 1, 'uploads/books/8b446517506760e930dd86b013d588ca.jpg', '2026-11-06 06:02:17'),
(3, 1, 'uploads/books/083be83186f88d89966e65777e825d56.jpg', '2026-11-06 06:02:17'),
(4, 1, 'uploads/books/5fcabb04aff53e9312c34bdc094741a7.jpg', '2026-11-06 06:02:17'),
(5, 1, 'uploads/books/2cd98c272df00850e1d9ab5f45f64e3e.jpg', '2026-11-06 06:02:17');

-- --------------------------------------------------------

--
-- Table structure for table `borrowings`
--

CREATE TABLE `borrowings` (
  `id` int(11) NOT NULL,
  `book_id` int(11) NOT NULL,
  `member_id` int(11) NOT NULL,
  `borrow_date` date DEFAULT NULL,
  `due_date` date DEFAULT NULL,
  `return_date` date DEFAULT NULL,
  `preferred_pickup_date` date DEFAULT NULL,
  `terms_agreed` tinyint(1) DEFAULT 0,
  `status` enum('pending','borrowed','returned','rejected') DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `borrowings`
--

INSERT INTO `borrowings` (`id`, `book_id`, `member_id`, `borrow_date`, `due_date`, `return_date`, `preferred_pickup_date`, `terms_agreed`, `status`) VALUES
(1, 1, 2, '2026-11-04', '2026-11-03', '2026-11-04', '2026-11-04', 1, 'returned'),
(2, 1, 2, '2026-11-04', '2026-11-18', '2026-11-04', '2026-11-04', 1, 'returned'),
(3, 1, 2, NULL, NULL, NULL, '2026-11-04', 1, 'rejected'),
(4, 1, 2, NULL, NULL, NULL, '2026-11-05', 1, 'rejected'),
(5, 1, 2, '2026-11-05', '2026-11-19', '2026-11-05', '2026-11-05', 1, 'returned'),
(6, 1, 2, '2026-11-05', '2026-11-19', '2026-11-05', '2026-11-05', 1, 'returned'),
(7, 5, 2, '2026-11-05', '2026-11-19', '2026-11-06', '2026-11-05', 1, 'returned'),
(8, 1, 1, NULL, NULL, NULL, '2026-11-05', 1, 'pending'),
(9, 1, 1, NULL, NULL, NULL, '2026-11-05', 1, 'pending'),
(10, 1, 1, '2026-11-06', '2026-11-20', '2026-11-06', '2026-11-06', 1, 'returned'),
(11, 2, 1, '2026-11-06', '2026-11-20', '2026-11-06', '2026-11-06', 1, 'returned'),
(12, 4, 1, '2026-11-06', '2026-11-20', '2026-11-06', '2026-11-06', 1, 'returned'),
(13, 2, 1, NULL, NULL, NULL, '2026-11-06', 1, 'rejected'),
(14, 3, 1, '2026-11-06', '2026-11-20', '2026-11-06', '2026-11-06', 1, 'returned'),
(15, 4, 1, '2026-11-06', '2026-11-20', '2026-11-06', '2026-11-06', 1, 'returned'),
(16, 2, 1, NULL, NULL, NULL, '2026-11-06', 1, 'pending');

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `name`) VALUES
(1, 'Technology'),
(2, 'Science'),
(3, 'Business'),
(4, 'Education'),
(7, 'Health and Wellness');

-- --------------------------------------------------------

--
-- Table structure for table `contacts`
--

CREATE TABLE `contacts` (
  `id` int(11) NOT NULL,
  `name` varchar(150) NOT NULL,
  `email` varchar(150) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `message` text NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `contacts`
--

INSERT INTO `contacts` (`id`, `name`, `email`, `phone`, `message`, `created_at`) VALUES
(1, 'jane ehi oduh', 'janeehioduh@gmail.com', '08039268873', 'hello', '2026-11-04 20:55:52');

-- --------------------------------------------------------

--
-- Table structure for table `fines`
--

CREATE TABLE `fines` (
  `id` int(11) NOT NULL,
  `borrowing_id` int(11) NOT NULL,
  `member_id` int(11) NOT NULL,
  `amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `status` enum('unpaid','paid') NOT NULL DEFAULT 'unpaid',
  `paid_at` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `fines`
--

INSERT INTO `fines` (`id`, `borrowing_id`, `member_id`, `amount`, `status`, `paid_at`, `created_at`) VALUES
(1, 10, 1, 1500.00, 'paid', '2026-11-06 06:06:36', '2026-11-06 05:05:13');

-- --------------------------------------------------------

--
-- Table structure for table `members`
--

CREATE TABLE `members` (
  `id` int(11) NOT NULL,
  `name` varchar(150) NOT NULL,
  `email` varchar(150) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `password` varchar(255) NOT NULL,
  `date_joined` date DEFAULT curdate(),
  `status` enum('active','suspended') DEFAULT 'active',
  `suspension_reason` text DEFAULT NULL,
  `profile_image` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `members`
--

INSERT INTO `members` (`id`, `name`, `email`, `phone`, `password`, `date_joined`, `status`, `suspension_reason`, `profile_image`) VALUES
(1, 'Janeehioduh', 'janeehioduh@gmail.com', '12333', '$2y$12$hKaFKzOXrqIv2Gg.ODMuwevLZYy3GkV8SzyWt/1YpYlS8eUnIUSd6', '2026-11-04', 'active', NULL, 'uploads/profiles/9b3ed04eb7592088f11b5922a463972e.jpg'),
(2, 'Janeehioduh', 'janeoduh6@gmail.com', '08039268873', '$2y$12$zxt8E25a3HMoWLNcdpvFTufACwTJOxBCOqJzqw3zd4xCnlxqxsGMO', '2026-11-04', 'active', NULL, 'uploads/profiles/e2071b959956cde37c877deac0f52ecb.jpg'),
(3, 'Jane zipporah', 'janezipporah@gmail.com', '08039268873', '$2y$12$fy/dED6B4grqSCKlwcO6ju52cmOyROmXFykWgC2OFPSbvZVQECbIy', '2026-11-05', 'active', NULL, '5680475c418de397fddbde4d03212e92.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `testimonials`
--

CREATE TABLE `testimonials` (
  `id` int(11) NOT NULL,
  `member_id` int(11) NOT NULL,
  `message` text NOT NULL,
  `status` enum('pending','approved','rejected') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `testimonials`
--

INSERT INTO `testimonials` (`id`, `member_id`, `message`, `status`, `created_at`) VALUES
(1, 1, 'This eLibrary has completely changed how I read and study. I can search for any book, borrow it in seconds, and read from anywhere — no more waiting in long queues. It\'s simple, fast, and has everything I need in one place. I highly recommend it to every student and book lover!', 'approved', '2026-11-06 09:29:36'),
(2, 2, 'Finding and borrowing books has never been this easy. I love the eLibrary!', 'rejected', '2026-11-06 15:21:26'),
(3, 2, 'Finding and borrowing books has never been this easy. I love the eLibrary!', 'approved', '2026-11-06 15:22:44');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `books`
--
ALTER TABLE `books`
  ADD PRIMARY KEY (`id`),
  ADD KEY `category_id` (`category_id`);

--
-- Indexes for table `book_images`
--
ALTER TABLE `book_images`
  ADD PRIMARY KEY (`id`),
  ADD KEY `book_id` (`book_id`);

--
-- Indexes for table `borrowings`
--
ALTER TABLE `borrowings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `book_id` (`book_id`),
  ADD KEY `member_id` (`member_id`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `contacts`
--
ALTER TABLE `contacts`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `fines`
--
ALTER TABLE `fines`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `borrowing_id` (`borrowing_id`);

--
-- Indexes for table `members`
--
ALTER TABLE `members`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `testimonials`
--
ALTER TABLE `testimonials`
  ADD PRIMARY KEY (`id`),
  ADD KEY `member_id` (`member_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `books`
--
ALTER TABLE `books`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `book_images`
--
ALTER TABLE `book_images`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `borrowings`
--
ALTER TABLE `borrowings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `contacts`
--
ALTER TABLE `contacts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `fines`
--
ALTER TABLE `fines`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `members`
--
ALTER TABLE `members`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `testimonials`
--
ALTER TABLE `testimonials`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `books`
--
ALTER TABLE `books`
  ADD CONSTRAINT `books_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `borrowings`
--
ALTER TABLE `borrowings`
  ADD CONSTRAINT `borrowings_ibfk_1` FOREIGN KEY (`book_id`) REFERENCES `books` (`id`),
  ADD CONSTRAINT `borrowings_ibfk_2` FOREIGN KEY (`member_id`) REFERENCES `members` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
