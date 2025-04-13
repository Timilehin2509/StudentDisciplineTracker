-- Create Database
CREATE DATABASE IF NOT EXISTS disciplinary_system;
USE disciplinary_system;

-- Users Table (For Admin & Staff)
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'staff') NOT NULL,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Default Users
INSERT INTO users (username, password, role, name, email) 
VALUES 
('admin', 'admin123', 'admin', 'Administrator', 'admin@example.com'),
('staff01', 'password123', 'staff', 'Sample Staff', 'staff@example.com');

-- Students Table
CREATE TABLE IF NOT EXISTS students (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_number VARCHAR(20) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    class VARCHAR(50) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Default Students
INSERT INTO students (student_number, password, name, email, class) 
VALUES 
('BU123456', 'password123', 'John Doe', 'john@example.com', 'Class A'),
('BU789012', 'password123', 'Jane Smith', 'jane@example.com', 'Class B'),
('BU345678', 'password123', 'Bob Wilson', 'bob@example.com', 'Class C');

-- Incidents Table
CREATE TABLE IF NOT EXISTS incidents (
    id INT AUTO_INCREMENT PRIMARY KEY,
    type ENUM('Academic Dishonesty', 'Attendance Issues', 'Behavioral Problems', 
              'Dress Code Violation', 'Property Damage', 'Physical Altercation', 
              'Verbal Misconduct', 'Other') NOT NULL,
    description TEXT NOT NULL,
    date_of_incidence DATE NOT NULL,
    date_reported DATE NOT NULL,
    status ENUM('Open', 'Investigate', 'Closed') NOT NULL DEFAULT 'Open',
    supporting_documents TEXT,
    reporter_id INT NOT NULL,
    updated_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (reporter_id) REFERENCES users(id),
    FOREIGN KEY (updated_by) REFERENCES users(id)
);

-- Incident_Students Table
CREATE TABLE IF NOT EXISTS incident_students (
    id INT AUTO_INCREMENT PRIMARY KEY,
    incident_id INT NOT NULL,
    student_id INT NOT NULL,
    punishment ENUM('No Punishment', 'Suspension', 'Expulsion', 'Community Service'),
    details TEXT,
    FOREIGN KEY (incident_id) REFERENCES incidents(id) ON DELETE CASCADE,
    FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE
);
