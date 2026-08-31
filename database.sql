CREATE DATABASE IF NOT EXISTS codeblue_db;
USE codeblue_db;

CREATE TABLE IF NOT EXISTS rooms (
    id INT AUTO_INCREMENT PRIMARY KEY,
    ip_address VARCHAR(45) NOT NULL UNIQUE,
    room_name VARCHAR(100) NOT NULL,
    floor VARCHAR(50) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS code_blue_calls (
    id INT AUTO_INCREMENT PRIMARY KEY,
    room_id INT NOT NULL,
    call_time DATETIME NOT NULL,
    response_time DATETIME NULL,
    status ENUM('pending', 'responded') DEFAULT 'pending',
    FOREIGN KEY (room_id) REFERENCES rooms(id) ON DELETE CASCADE
);

-- Insert dummy data for testing (optional)
-- INSERT INTO rooms (ip_address, room_name, floor) VALUES ('127.0.0.1', 'IGD', '1');
-- INSERT INTO rooms (ip_address, room_name, floor) VALUES ('::1', 'ICU', '2');
