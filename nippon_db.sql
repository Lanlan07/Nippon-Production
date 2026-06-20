-- =============================================
-- NIPPON PRODUCTION - DATABASE SCHEMA
-- Import file ini lewat phpMyAdmin (XAMPP)
-- =============================================

CREATE DATABASE IF NOT EXISTS nippon_db;
USE nippon_db;

-- =============================================
-- TABLE: users
-- =============================================
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin','customer','crew') NOT NULL DEFAULT 'customer',
    avatar VARCHAR(10),
    institution VARCHAR(150) DEFAULT '-',
    phone VARCHAR(30) DEFAULT '-',
    position VARCHAR(50) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO users (name, email, password, role, avatar, institution, phone, position) VALUES
('Administrator', 'admin@nipponproduction.com', 'admin123', 'admin', 'A', '-', '-', NULL),
('Andi Malik', 'andi.malik@pupr.go.id', 'password123', 'customer', 'AM', 'Kementerian PUPR', '081234567890', NULL),
('Dewi Sartika', 'dewi.sartika@ptpn.co.id', 'password123', 'customer', 'DS', 'PT Perkebunan Nusantara', '081298765432', NULL),
('Rina Wijaya', 'rina@binawedding.com', 'password123', 'customer', 'RW', 'Bina Wedding Organizer', '081355577788', NULL),
('Ahmad Fauzi', 'ahmad.fauzi@nipponproduction.com', 'password123', 'crew', 'AF', '-', '-', 'Cameraman');

-- =============================================
-- TABLE: events (orders)
-- =============================================
CREATE TABLE events (
    id INT AUTO_INCREMENT PRIMARY KEY,
    customer VARCHAR(100) NOT NULL,
    event_name VARCHAR(150) NOT NULL,
    type VARCHAR(50),
    date DATE,
    location VARCHAR(100),
    status VARCHAR(50) DEFAULT 'Menunggu Persetujuan',
    kamera INT DEFAULT 0,
    gimbal INT DEFAULT 0,
    drone INT DEFAULT 0,
    led INT DEFAULT 0,
    operator INT DEFAULT 0,
    durasi INT DEFAULT 0,
    estimated BIGINT DEFAULT 0,
    dp_paid BIGINT DEFAULT 0,
    remaining BIGINT DEFAULT 0,
    payment_status VARCHAR(50) DEFAULT 'Belum Bayar',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO events (customer, event_name, type, date, location, status, kamera, gimbal, drone, led, operator, durasi, estimated, dp_paid, remaining, payment_status) VALUES
('Andi Malik', 'HUT TNI ke-79', 'Pemerintahan', '2025-10-05', 'Makassar', 'Selesai', 4, 2, 1, 12, 6, 12, 45000000, 45000000, 0, 'Lunas'),
('Dewi Sartika', 'Gathering Tahunan PTPN', 'BUMN', '2025-09-20', 'Makassar', 'Selesai', 3, 1, 0, 0, 4, 8, 32000000, 32000000, 0, 'Lunas'),
('Rina Wijaya', 'Pernikahan Andi & Rina', 'Wedding', '2025-11-15', 'Makassar', 'DP Dibayar', 3, 1, 1, 0, 3, 10, 18000000, 5000000, 13000000, 'DP Dibayar'),
('Budi Santoso', 'Seminar Nasional UNHAS', 'Seminar', '2025-08-25', 'Makassar', 'Selesai', 2, 0, 0, 8, 3, 6, 25000000, 25000000, 0, 'Lunas'),
('Siti Rahayu', 'Peluncuran Produk Telkom', 'BUMN', '2025-12-10', 'Jakarta', 'Menunggu Persetujuan', 4, 2, 1, 15, 7, 10, 55000000, 0, 55000000, 'Belum Bayar'),
('Irwan Setiawan', 'Festival Ramadhan Makassar', 'Festival', '2025-04-15', 'Makassar', 'Selesai', 5, 2, 2, 20, 8, 12, 38000000, 38000000, 0, 'Lunas'),
('Maya Permata', 'Seminar Energi PLN', 'Seminar', '2025-11-05', 'Jakarta', 'Menunggu Pembayaran', 3, 1, 0, 10, 4, 8, 42000000, 0, 42000000, 'Belum Bayar'),
('Fajar Nugroho', 'Rapat Koordinasi Nasional', 'Pemerintahan', '2025-12-15', 'Jakarta', 'Menunggu Persetujuan', 4, 2, 1, 12, 6, 10, 65000000, 0, 65000000, 'Belum Bayar'),
('Andi Malik', 'Peresmian Bandara', 'Pemerintahan', '2026-01-10', 'Makassar', 'Selesai', 3, 1, 2, 0, 5, 8, 50000000, 50000000, 0, 'Lunas');

-- =============================================
-- TABLE: crew
-- =============================================
CREATE TABLE crew (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    role VARCHAR(50),
    status VARCHAR(20) DEFAULT 'Aktif',
    salary_per_event BIGINT DEFAULT 0
);

INSERT INTO crew (name, role, status, salary_per_event) VALUES
('Ahmad Fauzi', 'Cameraman', 'Aktif', 1500000),
('Budi Prasetyo', 'Cameraman', 'Aktif', 1500000),
('Chandra Wijaya', 'Photographer', 'Aktif', 1200000),
('Dewi Lestari', 'Photographer', 'Aktif', 1200000),
('Eko Saputra', 'Videographer', 'Aktif', 1300000),
('Gunawan Putra', 'Drone Pilot', 'Aktif', 1400000);

-- =============================================
-- TABLE: assignments
-- =============================================
CREATE TABLE assignments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    crew_id INT NOT NULL,
    event_id INT NOT NULL,
    date DATE,
    salary BIGINT DEFAULT 0,
    status VARCHAR(20) DEFAULT 'Scheduled',
    FOREIGN KEY (crew_id) REFERENCES crew(id) ON DELETE CASCADE,
    FOREIGN KEY (event_id) REFERENCES events(id) ON DELETE CASCADE
);

-- =============================================
-- TABLE: attendance
-- =============================================
CREATE TABLE attendance (
    id INT AUTO_INCREMENT PRIMARY KEY,
    crew_id INT NOT NULL,
    event_id INT NOT NULL,
    date DATE,
    checkin VARCHAR(10) DEFAULT '-',
    checkout VARCHAR(10) DEFAULT '-',
    status VARCHAR(20) DEFAULT 'Scheduled',
    FOREIGN KEY (crew_id) REFERENCES crew(id) ON DELETE CASCADE,
    FOREIGN KEY (event_id) REFERENCES events(id) ON DELETE CASCADE
);

INSERT INTO attendance (crew_id, event_id, date, checkin, checkout, status) VALUES
(1, 1, '2025-10-05', '06:15', '18:00', 'Hadir'),
(1, 6, '2025-04-15', '14:00', '22:00', 'Hadir');

-- =============================================
-- TABLE: inventory
-- =============================================
CREATE TABLE inventory (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    type VARCHAR(50),
    status VARCHAR(20) DEFAULT 'Tersedia',
    price BIGINT DEFAULT 0
);

INSERT INTO inventory (name, type, status, price) VALUES
('Sony FX6', 'Kamera', 'Tersedia', 85000000),
('Sony A7S III', 'Kamera', 'Digunakan', 45000000),
('Canon EOS C70', 'Kamera', 'Tersedia', 65000000),
('DJI RS 3 Pro', 'Gimbal', 'Tersedia', 18000000),
('DJI Mavic 3 Pro', 'Drone', 'Tersedia', 35000000),
('DJI Mavic 3 Classic', 'Drone', 'Digunakan', 25000000),
('LED Indoor P2.5', 'LED', 'Tersedia', 45000000),
('ATEM Mini Pro', 'Switcher', 'Tersedia', 15000000);

-- =============================================
-- TABLE: payments
-- =============================================
CREATE TABLE payments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    event_id INT NOT NULL,
    event_name VARCHAR(150),
    customer VARCHAR(100),
    amount BIGINT DEFAULT 0,
    type VARCHAR(20),
    date DATE,
    status VARCHAR(30) DEFAULT 'Diverifikasi',
    FOREIGN KEY (event_id) REFERENCES events(id) ON DELETE CASCADE
);

-- =============================================
-- TABLE: contacts (pesan dari form kontak)
-- =============================================
CREATE TABLE contacts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100),
    email VARCHAR(150),
    message TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
