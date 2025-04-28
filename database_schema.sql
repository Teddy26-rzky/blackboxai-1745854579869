-- Database schema for Apartment Room Rental Website

CREATE DATABASE IF NOT EXISTS apartment_rental;
USE apartment_rental;

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama VARCHAR(100) NOT NULL,
    nomor_telepon VARCHAR(20) NOT NULL,
    alamat TEXT NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('user', 'admin') DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE rooms (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama_room VARCHAR(100) NOT NULL,
    deskripsi TEXT,
    harga DECIMAL(10,2) NOT NULL,
    status ENUM('tersedia', 'disewa') DEFAULT 'tersedia'
);

CREATE TABLE bookings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    room_id INT NOT NULL,
    tanggal_mulai DATE NOT NULL,
    tanggal_selesai DATE NOT NULL,
    status_booking ENUM('pending', 'confirmed', 'cancelled') DEFAULT 'pending',
    metode_pembayaran ENUM('COD', 'transfer') NOT NULL,
    status_pembayaran ENUM('pending', 'confirmed') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (room_id) REFERENCES rooms(id)
);

CREATE TABLE payments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    booking_id INT NOT NULL,
    jumlah DECIMAL(10,2) NOT NULL,
    metode ENUM('COD', 'transfer') NOT NULL,
    status ENUM('pending', 'confirmed') DEFAULT 'pending',
    tanggal_pembayaran TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (booking_id) REFERENCES bookings(id)
);

-- Insert initial rooms
INSERT INTO rooms (nama_room, deskripsi, harga, status) VALUES
('Room 1', 'Room 1 description', 1000000.00, 'tersedia'),
('Room 2', 'Room 2 description', 1200000.00, 'tersedia');
