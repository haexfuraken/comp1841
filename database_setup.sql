CREATE DATABASE `library` 
CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE `library`;
CREATE TABLE `users` (
    `user_id` INT AUTO_INCREMENT PRIMARY KEY,
    `username` VARCHAR(50) NOT NULL UNIQUE,
    `email` VARCHAR(100) NOT NULL UNIQUE,
    `password` VARCHAR(255) NOT NULL,
    `full_name` VARCHAR(100) NOT NULL,
    `role` ENUM('student', 'staff', 'admin') DEFAULT 'student',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE `modules` (
    `module_id` INT AUTO_INCREMENT PRIMARY KEY,
    `module_code` VARCHAR(10) NOT NULL UNIQUE,
    `module_name` VARCHAR(100) NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE `questions` (
    `question_id` INT AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT NOT NULL,
    `module_id` INT NULL,
    `title` VARCHAR(200) NOT NULL,
    `content` TEXT NOT NULL,
    `image_path` VARCHAR(255) NULL,
    `is_answered` TINYINT(1) DEFAULT 0,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`user_id`) REFERENCES `users`(`user_id`) ON DELETE CASCADE,
    FOREIGN KEY (`module_id`) REFERENCES `modules`(`module_id`) ON DELETE SET NULL
);

CREATE TABLE `answers` (
    `answer_id` INT AUTO_INCREMENT PRIMARY KEY,
    `question_id` INT NOT NULL,
    `user_id` INT NOT NULL,
    `content` TEXT NOT NULL,
    `is_solution` TINYINT(1) DEFAULT 0,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`question_id`) REFERENCES `questions`(`question_id`) ON DELETE CASCADE,
    FOREIGN KEY (`user_id`) REFERENCES `users`(`user_id`) ON DELETE CASCADE
);

CREATE TABLE `votes` (
    `vote_id` INT AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT NOT NULL,
    `question_id` INT NULL,
    `answer_id` INT NULL,
    `vote_type` ENUM('upvote', 'downvote') NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`user_id`) REFERENCES `users`(`user_id`) ON DELETE CASCADE,
    FOREIGN KEY (`question_id`) REFERENCES `questions`(`question_id`) ON DELETE CASCADE,
    FOREIGN KEY (`answer_id`) REFERENCES `answers`(`answer_id`) ON DELETE CASCADE,
    UNIQUE KEY `unique_question_vote` (`user_id`, `question_id`),
    UNIQUE KEY `unique_answer_vote` (`user_id`, `answer_id`)
);

CREATE TABLE `user_sessions` (
    `session_id` VARCHAR(255) PRIMARY KEY,
    `user_id` INT NOT NULL,
    `ip_address` VARCHAR(45) NULL,
    `user_agent` VARCHAR(255) NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `expires_at` TIMESTAMP NULL,
    FOREIGN KEY (`user_id`) REFERENCES `users`(`user_id`) ON DELETE CASCADE
);

INSERT INTO `users` (`username`, `email`, `password`, `full_name`, `role`) VALUES
('admin', 'admin@qa.edu', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Admin', 'admin'),
('staff', 'staff@qa.edu', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Staff', 'staff'),
('student', 'student@qa.edu', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Student', 'student');

INSERT INTO `modules` (`module_code`, `module_name`) VALUES
('COMP1841', 'Web Programming');

INSERT INTO `questions` (`user_id`, `module_id`, `title`, `content`, `is_answered`, `created_at`) VALUES
(3, 1, 'What is the difference between GET and POST methods?', 'Can someone explain when to use GET vs POST in HTML forms? I''m confused about which one to use for my contact form.', 1, '2025-11-20 14:15:00');

INSERT INTO `answers` (`question_id`, `user_id`, `content`, `is_solution`, `created_at`) VALUES
(1, 2, 'GET is used for retrieving data and parameters are visible in the URL. POST is used for sending data to the server (like form submissions) and parameters are hidden in the request body.\n\nFor a contact form, use POST because:\n1. It''s more secure (data not in URL)\n2. Can send larger amounts of data\n3. Won''t be cached by browsers', 1, '2025-11-20 15:00:00');