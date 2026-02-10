-- Database: arsip_surat
-- Membuat database
CREATE DATABASE IF NOT EXISTS arsip_surat CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE arsip_surat;

-- Membuat tabel surat
CREATE TABLE IF NOT EXISTS surat (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nomor_surat VARCHAR(100) NOT NULL,
    nama_surat VARCHAR(255) NOT NULL,
    tanggal_surat DATE NOT NULL,
    nama_file VARCHAR(255) NOT NULL,
    tipe_file VARCHAR(10) NOT NULL,
    ukuran_file INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_nama_surat (nama_surat),
    INDEX idx_tanggal_surat (tanggal_surat)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


