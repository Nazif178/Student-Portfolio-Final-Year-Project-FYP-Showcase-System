-- Import this file using HeidiSQL.

CREATE DATABASE IF NOT EXISTS student_project_db;
USE student_project_db;

CREATE TABLE IF NOT EXISTS users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('Admin', 'Student') NOT NULL DEFAULT 'Student',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS assignments (
    assignment_id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(150) NOT NULL,
    description TEXT NOT NULL,
    created_by INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_assignments_creator
      FOREIGN KEY (created_by) REFERENCES users(user_id)
      ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS submissions (
    submission_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    assignment_id INT NOT NULL,
    file_name VARCHAR(255) NOT NULL,
    file_path VARCHAR(255) NOT NULL,
    submitted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_submissions_user
      FOREIGN KEY (user_id) REFERENCES users(user_id)
      ON UPDATE CASCADE ON DELETE CASCADE,
    CONSTRAINT fk_submissions_assignment
      FOREIGN KEY (assignment_id) REFERENCES assignments(assignment_id)
      ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB;

-- Default admin account (login: admin@admin.com / password: 1234)
-- The password below is a bcrypt hash of "1234".
INSERT IGNORE INTO users (full_name, email, password, role)
VALUES ('Administrator', 'admin@admin.com',
        '$2b$12$AyC1bnVSI0maHaYqrKVA.uRoLxosMf940D95val7pkKm8P61xYxMi', 'Admin');
