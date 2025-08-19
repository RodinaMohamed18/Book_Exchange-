-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 19, 2025 at 08:29 PM
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
-- Database: `book_exchangedb`
--

-- --------------------------------------------------------

--
-- Table structure for table `books`
--

CREATE TABLE `books` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `author` varchar(255) NOT NULL,
  `genre` varchar(100) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `cover_image` varchar(255) DEFAULT NULL,
  `condition` varchar(50) DEFAULT NULL,
  `availability` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `books`
--

INSERT INTO `books` (`id`, `user_id`, `title`, `author`, `genre`, `description`, `cover_image`, `condition`, `availability`, `created_at`) VALUES
(1, 1, 'To Kill a Mockingbird', 'Harper Lee', 'Classic', 'A timeless novel about justice and race.', 'To Kill a Mockingbird.jpg\r\n', 'Good', 1, '2025-08-16 18:10:15'),
(2, 2, 'The Great Gatsby', 'F. Scott Fitzgerald', 'Classic', 'A story of the Jazz Age and the American Dream.', 'The Great Gatsby.jpeg', 'Like New', 1, '2025-08-16 18:10:15'),
(3, 2, 'The Catcher in the Rye', 'J.D. Salinger', 'Classic', 'A coming-of-age novel.', 'The Catcher in the Rye.jpeg', 'Fair', 1, '2025-08-16 18:10:15'),
(4, 4, 'Harry Potter and the Philosopher\'s Stone', 'J.K. Rowling', 'Fantasy', 'The first Harry Potter book.', 'Harry Potter and the Philosopher\'s Stone.jpeg', 'Like New', 1, '2025-08-16 18:10:15'),
(5, 5, '1984', 'George Orwell', 'Dystopian', 'A chilling depiction of totalitarianism.', '1984.jpeg', 'Good', 1, '2025-08-16 18:10:15'),
(6, 6, 'The Hobbit', 'J.R.R. Tolkien', 'Fantasy', 'Bilbo\'s adventure through Middle-earth.', 'The Hobbit_ The Desolation of Smaug (2013).jpeg', 'Like New', 1, '2025-08-16 18:10:15'),
(7, 7, 'Pride and Prejudice', 'Jane Austen', 'Romance', 'A love story with social commentary.', '\'Pride and Prejudice Austen Book Cover\' Poster, picture, metal print, paint by Marie K _ Displate.jpeg', 'Fair', 1, '2025-08-16 18:10:15'),
(8, 8, 'The Da Vinci Code', 'Dan Brown', 'Thriller', 'A fast-paced mystery thriller.', 'The Da Vinci Code - Dan Brown.jpeg', 'Good', 1, '2025-08-16 18:10:15'),
(9, 9, 'The Alchemist', 'Paulo Coelho', 'Philosophical', 'A novel about dreams and destiny.', 'The Alchemist cover design by Jim Tierney; art direction by Michele Wetherbee and Laura Beers (HarperOne).jpeg', 'Like New', 1, '2025-08-16 18:10:15'),
(10, 10, 'Moby-Dick', 'Herman Melville', 'Adventure', 'The quest for the great white whale.', 'Moby Dick by Herman Melville_ The Original Classic Hardcover - A Riveting Exploration of One Man’s Obsession and Nature’s Unforgiving Power.jpeg', 'Poor', 1, '2025-08-16 18:10:15'),
(11, 1, 'Little Women', 'Louisa May Alcott', 'Classic', 'The story of four sisters.', 'Such a beautiful story!.jpeg', 'Good', 1, '2025-08-16 18:10:15'),
(12, 3, 'Brave New World', 'Aldous Huxley', 'Dystopian', 'A futuristic world of control.', 'aldous huxley - brave new world, 1932.jpeg', 'Fair', 1, '2025-08-16 18:10:15'),
(13, 5, 'The Lord of the Rings', 'J.R.R. Tolkien', 'Fantasy', 'Epic high fantasy trilogy.', 'The Lord of the Rings.jpeg', 'Like New', 1, '2025-08-16 18:10:15'),
(14, 7, 'The Fault in Our Stars', 'John Green', 'Romance', 'A heartbreaking love story.', 'The Fault in Our Stars.jpeg', 'Good', 1, '2025-08-16 18:10:15'),
(15, 9, 'Crime and Punishment', 'Fyodor Dostoevsky', 'Classic', 'A psychological novel about guilt.', 'crime and punishment by dostoevsky.jpeg', 'Good', 1, '2025-08-16 18:10:15'),
(26, 31, 'in cold blood', 'Truman Capote', 'comedy', 'the new book', 'book_68a4ba41968bd6.89915937.jpeg', NULL, 0, '2025-08-19 17:54:09');

-- --------------------------------------------------------

--
-- Table structure for table `exchanges`
--

CREATE TABLE `exchanges` (
  `id` int(11) NOT NULL,
  `book_id` int(11) NOT NULL,
  `requester_id` int(11) NOT NULL,
  `status` enum('pending','approved','rejected','completed') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `owner_id` int(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `exchanges`
--

INSERT INTO `exchanges` (`id`, `book_id`, `requester_id`, `status`, `created_at`, `owner_id`) VALUES
(1, 1, 2, 'pending', '2025-08-16 18:10:15', 1),
(2, 2, 1, 'approved', '2025-08-16 18:10:15', 2),
(3, 4, 3, 'completed', '2025-08-16 18:10:15', 3),
(4, 5, 6, 'pending', '2025-08-16 18:10:15', 4),
(5, 7, 8, 'approved', '2025-08-16 18:10:15', 5),
(6, 9, 10, 'rejected', '2025-08-16 18:10:15', 6),
(7, 12, 4, 'completed', '2025-08-16 18:10:15', 7),
(8, 14, 9, 'pending', '2025-08-16 18:10:15', 8),
(26, 26, 32, 'completed', '2025-08-19 17:56:04', 31);

-- --------------------------------------------------------

--
-- Table structure for table `messages`
--

CREATE TABLE `messages` (
  `id` int(11) NOT NULL,
  `sender_id` int(11) NOT NULL,
  `receiver_id` int(11) NOT NULL,
  `book_id` int(11) DEFAULT NULL,
  `message` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `is_read` tinyint(1) DEFAULT 0,
  `read_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `messages`
--

INSERT INTO `messages` (`id`, `sender_id`, `receiver_id`, `book_id`, `message`, `created_at`, `is_read`, `read_at`) VALUES
(1, 1, 2, 2, 'Hi Bob, can I borrow \"The Great Gatsby\"?', '2025-08-16 18:10:15', 1, '2025-08-16 18:10:15'),
(2, 2, 1, 2, 'Sure Alice, I’ll approve your request!', '2025-08-16 18:10:15', 1, '2025-08-16 18:10:15'),
(3, 3, 4, 4, 'David, I enjoyed the book. Thanks!', '2025-08-16 18:10:15', 0, NULL),
(4, 6, 5, 5, 'Emily, I’d like to borrow \"1984\".', '2025-08-16 18:10:15', 1, '2025-08-16 18:10:15'),
(5, 8, 7, 7, 'Grace, your book \"Pride and Prejudice\" looks interesting.', '2025-08-16 18:10:15', 0, NULL),
(6, 9, 10, 14, 'Jackson, is \"The Fault in Our Stars\" available?', '2025-08-16 18:10:15', 1, '2025-08-16 18:10:15'),
(19, 32, 31, NULL, 'i need this book for me', '2025-08-19 17:56:13', 1, '2025-08-19 17:56:55'),
(20, 31, 32, NULL, 'it\'s yours now', '2025-08-19 17:56:55', 0, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `message` varchar(255) NOT NULL,
  `link` varchar(255) DEFAULT '#',
  `is_read` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`id`, `user_id`, `message`, `link`, `is_read`, `created_at`) VALUES
(1, 1, 'Your exchange request for \"The Great Gatsby\" has been approved.', '/exchanges/2', 0, '2025-08-16 21:10:15'),
(2, 2, 'Alice requested your book \"To Kill a Mockingbird\".', '/exchanges/1', 1, '2025-08-16 21:10:15'),
(3, 4, 'Your exchange with Carol has been completed successfully.', '/exchanges/3', 0, '2025-08-16 21:10:15'),
(4, 6, 'Your request for \"1984\" is pending.', '/exchanges/5', 0, '2025-08-16 21:10:15'),
(5, 9, 'Your request for \"The Fault in Our Stars\" is still pending.', '/exchanges/14', 0, '2025-08-16 21:10:15'),
(34, 31, 'user2 has requested your book: \'in cold blood\'.', 'my-books.php', 1, '2025-08-19 20:56:04'),
(35, 31, 'You have a new message from user2', 'conversation.php?partner_id=32', 1, '2025-08-19 20:56:13'),
(36, 32, 'You have a new message from user1', 'conversation.php?partner_id=31', 1, '2025-08-19 20:56:55'),
(37, 32, 'Your request for \'in cold blood\' has been approved.', 'my-books.php', 1, '2025-08-19 20:57:02'),
(38, 31, 'The exchange for \'in cold blood\' with user2 has been marked as complete.', 'profile.php?id=31', 1, '2025-08-19 20:57:34');

-- --------------------------------------------------------

--
-- Table structure for table `ratings`
--

CREATE TABLE `ratings` (
  `id` int(11) NOT NULL,
  `exchange_id` int(11) NOT NULL,
  `reviewer_id` int(11) NOT NULL,
  `reviewed_user_id` int(11) NOT NULL,
  `rating` tinyint(1) NOT NULL CHECK (`rating` >= 1 and `rating` <= 5),
  `review` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `ratings`
--

INSERT INTO `ratings` (`id`, `exchange_id`, `reviewer_id`, `reviewed_user_id`, `rating`, `review`, `created_at`) VALUES
(1, 2, 1, 2, 5, 'Great exchange! Smooth and friendly.', '2025-08-16 18:10:15'),
(2, 3, 3, 4, 4, 'Book was in good condition.', '2025-08-16 18:10:15'),
(3, 3, 4, 3, 5, 'Excellent communication and fast exchange!', '2025-08-16 18:10:15'),
(4, 5, 8, 7, 3, 'Book condition was fair, but delivery was late.', '2025-08-16 18:10:15'),
(5, 7, 4, 3, 4, 'Good experience overall.', '2025-08-16 18:10:15'),
(9, 26, 32, 31, 5, 'that was awesome', '2025-08-19 17:57:52');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `full_name` varchar(100) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `profile_pic` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `is_admin` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password_hash`, `full_name`, `address`, `phone`, `profile_pic`, `created_at`, `is_admin`) VALUES
(1, 'alice123', 'alice@example.com', 'hashed_pw_1', 'Alice Johnson', '123 Elm St, New York, USA', '+1-202-555-0147', 'alice.jpg', '2025-08-16 18:08:22', 0),
(2, 'bob_the_reader', 'bob@example.com', 'hashed_pw_2', 'Bob Smith', '456 Oak Ave, Chicago, USA', '+1-202-555-0178', 'bob.png', '2025-08-16 18:08:22', 0),
(3, 'carol_admin', 'carol@example.com', 'hashed_pw_3', 'Carol White', '789 Pine Rd, Los Angeles, USA', '+1-202-555-0199', 'carol.png', '2025-08-16 18:08:22', 1),
(4, 'david89', 'david@example.com', 'hashed_pw_4', 'David Brown', '22 River St, Boston, USA', '+1-202-555-0135', 'david.jpg', '2025-08-16 18:08:22', 0),
(5, 'emily_books', 'emily@example.com', 'hashed_pw_5', 'Emily Davis', '10 Maple St, Denver, USA', '+1-202-555-0112', 'emily.png', '2025-08-16 18:08:22', 0),
(6, 'frankie', 'frank@example.com', 'hashed_pw_6', 'Frank Miller', '77 Hill Rd, Austin, USA', '+1-202-555-0188', 'frank.jpg', '2025-08-16 18:08:22', 0),
(7, 'grace_lit', 'grace@example.com', 'hashed_pw_7', 'Grace Wilson', '99 Lake St, Miami, USA', '+1-202-555-0144', 'grace.png', '2025-08-16 18:08:22', 0),
(8, 'harry_potterfan', 'harry@example.com', 'hashed_pw_8', 'Harry Green', '8 Castle St, Orlando, USA', '+1-202-555-0155', 'harry.png', '2025-08-16 18:08:22', 0),
(9, 'isabella', 'isabella@example.com', 'hashed_pw_9', 'Isabella Brown', '55 Rose St, Dallas, USA', '+1-202-555-0166', 'isabella.jpg', '2025-08-16 18:08:22', 0),
(10, 'jackson', 'jackson@example.com', 'hashed_pw_10', 'Jackson Lee', '12 Main St, Seattle, USA', '+1-202-555-0177', 'jackson.jpg', '2025-08-16 18:08:22', 0),
(31, 'user1', 'user1@gmail.com', '$2y$10$AOTj.ySaKpiXWc6MihHTluERqGugFrEZ4SwB1zZN7QIlzwMIWGpuW', 'user 1', 'alex', '54546', 'user_68a4ba6fae2077.14899874.jpeg', '2025-08-19 17:53:36', 0),
(32, 'user2', 'user2@gmail.com', '$2y$10$4cNIUXJrdxlmQVOM7Qwq7e4zSHxb78v8CEY75Ub6FfPjI4OROop.6', 'user2', 'alex', '7986', 'user_68a4ba9b3926f1.57550849.jpeg', '2025-08-19 17:55:17', 0);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `books`
--
ALTER TABLE `books`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `exchanges`
--
ALTER TABLE `exchanges`
  ADD PRIMARY KEY (`id`),
  ADD KEY `book_id` (`book_id`),
  ADD KEY `requester_id` (`requester_id`);

--
-- Indexes for table `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `book_id` (`book_id`),
  ADD KEY `idx_pair` (`sender_id`,`receiver_id`,`created_at`),
  ADD KEY `idx_receiver` (`receiver_id`,`is_read`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `ratings`
--
ALTER TABLE `ratings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `exchange_id` (`exchange_id`),
  ADD KEY `reviewer_id` (`reviewer_id`),
  ADD KEY `reviewed_user_id` (`reviewed_user_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `books`
--
ALTER TABLE `books`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT for table `exchanges`
--
ALTER TABLE `exchanges`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT for table `messages`
--
ALTER TABLE `messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=39;

--
-- AUTO_INCREMENT for table `ratings`
--
ALTER TABLE `ratings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `books`
--
ALTER TABLE `books`
  ADD CONSTRAINT `books_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `exchanges`
--
ALTER TABLE `exchanges`
  ADD CONSTRAINT `exchanges_ibfk_1` FOREIGN KEY (`book_id`) REFERENCES `books` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `exchanges_ibfk_2` FOREIGN KEY (`requester_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `messages`
--
ALTER TABLE `messages`
  ADD CONSTRAINT `messages_ibfk_1` FOREIGN KEY (`sender_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `messages_ibfk_2` FOREIGN KEY (`receiver_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `messages_ibfk_3` FOREIGN KEY (`book_id`) REFERENCES `books` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `notifications_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `ratings`
--
ALTER TABLE `ratings`
  ADD CONSTRAINT `ratings_ibfk_1` FOREIGN KEY (`exchange_id`) REFERENCES `exchanges` (`id`),
  ADD CONSTRAINT `ratings_ibfk_2` FOREIGN KEY (`reviewer_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `ratings_ibfk_3` FOREIGN KEY (`reviewed_user_id`) REFERENCES `users` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
