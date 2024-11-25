-- Create the database
CREATE DATABASE IF NOT EXISTS movies_db;

-- Use the created database
USE movies_db;

-- Create the movies table
CREATE TABLE IF NOT EXISTS movies (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    category VARCHAR(100) NOT NULL
);
