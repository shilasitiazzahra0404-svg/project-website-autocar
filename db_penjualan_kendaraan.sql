-- =========================================================
-- DATABASE SISTEM INFORMASI PENJUALAN KENDARAAN
-- Sesuai dengan source code: penjualan_kendaraan.zip
-- Database: db_penjualan_kendaraan
-- =========================================================

CREATE DATABASE IF NOT EXISTS db_penjualan_kendaraan
CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE db_penjualan_kendaraan;

SET FOREIGN_KEY_CHECKS = 0;

DROP TABLE IF EXISTS angsuran;
DROP TABLE IF EXISTS kredit;
DROP TABLE IF EXISTS history_penjualan;
DROP TABLE IF EXISTS insentif;
DROP TABLE IF EXISTS penjualan;
DROP TABLE IF EXISTS kendaraan;
DROP TABLE IF EXISTS tipe_kendaraan;
DROP TABLE IF EXISTS merek;
DROP TABLE IF EXISTS customer;
DROP TABLE IF EXISTS sales;
DROP TABLE IF EXISTS users;

SET FOREIGN_KEY_CHECKS = 1;

-- =========================================================
-- 1. USERS
-- Login hanya untuk ADMIN dan SALES.
-- Customer tidak login sesuai README/source website.
-- =========================================================
CREATE TABLE users (
    id_user INT AUTO_INCREMENT PRIMARY KEY,
    nama VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin','sales') NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

INSERT INTO users (nama, email, password, role) VALUES
('Administrator', 'admin@gmail.com', 'admin123', 'admin'),
('Sales AutoCar', 'sales@gmail.com', 'sales123', 'sales');

-- =========================================================
-- 2. SALES
-- Terhubung ke users melalui id_user.
-- =========================================================
CREATE TABLE sales (
    id_sales INT AUTO_INCREMENT PRIMARY KEY,
    id_user INT NOT NULL UNIQUE,
    nama VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    no_hp VARCHAR(20) DEFAULT NULL,
    tanggal_bergabung DATE DEFAULT NULL,
    status ENUM('Aktif','Tidak Aktif') NOT NULL DEFAULT 'Aktif',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_sales_user
        FOREIGN KEY (id_user) REFERENCES users(id_user)
        ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB;

INSERT INTO sales
(id_user, nama, email, no_hp, tanggal_bergabung, status)
VALUES
(2, 'Sales AutoCar', 'sales@gmail.com', '081234567890', '2026-01-10', 'Aktif');

-- =========================================================
-- 3. CUSTOMER
-- Customer tidak mempunyai akun/login.
-- =========================================================
CREATE TABLE customer (
    id_customer INT AUTO_INCREMENT PRIMARY KEY,
    nama VARCHAR(100) NOT NULL,
    email VARCHAR(100) DEFAULT NULL,
    no_hp VARCHAR(20) DEFAULT NULL,
    alamat TEXT DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

INSERT INTO customer (nama, email, no_hp, alamat) VALUES
('Andi Saputra', 'andi@gmail.com', '081234567801', 'Tangerang'),
('Budi Santoso', 'budi@gmail.com', '081234567802', 'Jakarta Selatan'),
('Citra Lestari', 'citra@gmail.com', '081234567803', 'Depok');

-- =========================================================
-- 4. MEREK
-- =========================================================
CREATE TABLE merek (
    id_merek INT AUTO_INCREMENT PRIMARY KEY,
    nama_merek VARCHAR(100) NOT NULL UNIQUE
) ENGINE=InnoDB;

INSERT INTO merek (nama_merek) VALUES
('Toyota'),
('Honda'),
('Mitsubishi'),
('Nissan');

-- =========================================================
-- 5. TIPE KENDARAAN
-- =========================================================
CREATE TABLE tipe_kendaraan (
    id_tipe INT AUTO_INCREMENT PRIMARY KEY,
    nama_tipe VARCHAR(100) NOT NULL,
    id_merek INT DEFAULT NULL,
    CONSTRAINT fk_tipe_merek
        FOREIGN KEY (id_merek) REFERENCES merek(id_merek)
        ON UPDATE CASCADE ON DELETE SET NULL
) ENGINE=InnoDB;

INSERT INTO tipe_kendaraan (nama_tipe, id_merek) VALUES
('Avanza', 1),
('Calya', 1),
('Fortuner', 1),
('Innova', 1),
('Raize', 1),
('Rush', 1),
('Veloz', 1),
('Yaris', 1),
('Brio', 2),
('BR-V', 2),
('City', 2),
('Civic', 2),
('HR-V', 2),
('WR-V', 2),
('Eclipse Cross', 3),
('Outlander', 3),
('Pajero', 3),
('Triton', 3),
('Xpander', 3),
('Xforce', 3);

-- =========================================================
-- 6. KENDARAAN
-- =========================================================
CREATE TABLE kendaraan (
    id_kendaraan INT AUTO_INCREMENT PRIMARY KEY,
    kode_unit VARCHAR(30) NOT NULL UNIQUE,
    id_merek INT NOT NULL,
    id_tipe INT NOT NULL,
    tahun YEAR NOT NULL,
    warna VARCHAR(50) DEFAULT NULL,
    harga DECIMAL(15,2) NOT NULL DEFAULT 0,
    foto VARCHAR(255) DEFAULT NULL,
    status ENUM('TERSEDIA','DIPROSES','TERJUAL') NOT NULL DEFAULT 'TERSEDIA',
    deskripsi TEXT DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_kendaraan_merek
        FOREIGN KEY (id_merek) REFERENCES merek(id_merek)
        ON UPDATE CASCADE ON DELETE RESTRICT,
    CONSTRAINT fk_kendaraan_tipe
        FOREIGN KEY (id_tipe) REFERENCES tipe_kendaraan(id_tipe)
        ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB;

INSERT INTO kendaraan
(kode_unit, id_merek, id_tipe, tahun, warna, harga, foto, status, deskripsi)
VALUES
('TYT-001', 1, 1, 2026, 'Putih', 250000000, 'avanza.jpg', 'TERSEDIA', 'Toyota Avanza untuk kebutuhan keluarga.'),
('TYT-002', 1, 2, 2026, 'Silver', 185000000, 'calya.jpg', 'TERSEDIA', 'Toyota Calya dengan desain praktis dan nyaman.'),
('TYT-003', 1, 3, 2026, 'Hitam', 610000000, 'fortuner.jpg', 'TERSEDIA', 'Toyota Fortuner SUV untuk perjalanan keluarga.'),
('TYT-004', 1, 4, 2026, 'Putih', 425000000, 'innova.jpg', 'TERSEDIA', 'Toyota Innova dengan kabin luas.'),
('TYT-005', 1, 5, 2026, 'Merah', 245000000, 'raize.jpg', 'TERSEDIA', 'Toyota Raize compact SUV.'),
('TYT-006', 1, 6, 2026, 'Putih', 285000000, 'rush.jpg', 'TERSEDIA', 'Toyota Rush SUV keluarga.'),
('TYT-007', 1, 7, 2026, 'Hitam', 305000000, 'veloz.jpg', 'TERSEDIA', 'Toyota Veloz MPV keluarga.'),
('TYT-008', 1, 8, 2026, 'Merah', 325000000, 'yaris.jpg', 'TERSEDIA', 'Toyota Yaris hatchback.'),
('HND-001', 2, 9, 2026, 'Putih', 175000000, 'brio.jpg', 'TERSEDIA', 'Honda Brio city car.'),
('HND-002', 2, 10, 2026, 'Silver', 310000000, 'brv.jpg', 'TERSEDIA', 'Honda BR-V SUV keluarga.'),
('HND-003', 2, 11, 2026, 'Putih', 360000000, 'city.jpg', 'TERSEDIA', 'Honda City sedan.'),
('HND-004', 2, 12, 2026, 'Hitam', 620000000, 'civic.jpg', 'TERSEDIA', 'Honda Civic sedan.'),
('HND-005', 2, 13, 2026, 'Abu-abu', 420000000, 'hrv.jpg', 'TERSEDIA', 'Honda HR-V SUV.'),
('HND-006', 2, 14, 2026, 'Putih', 280000000, 'wrv.jpg', 'TERSEDIA', 'Honda WR-V compact SUV.'),
('MTS-001', 3, 15, 2026, 'Putih', 525000000, 'eclipse-cross.jpg', 'TERSEDIA', 'Mitsubishi Eclipse Cross.'),
('MTS-002', 3, 16, 2026, 'Hitam', 620000000, 'outlander.jpg', 'TERSEDIA', 'Mitsubishi Outlander.'),
('MTS-003', 3, 17, 2026, 'Hitam', 720000000, 'pajero.jpg', 'TERSEDIA', 'Mitsubishi Pajero Sport.'),
('MTS-004', 3, 18, 2026, 'Putih', 475000000, 'triton.jpg', 'TERSEDIA', 'Mitsubishi Triton.'),
('MTS-005', 3, 19, 2026, 'Silver', 330000000, 'xpander.jpg', 'TERSEDIA', 'Mitsubishi Xpander MPV.'),
('MTS-006', 3, 20, 2026, 'Merah', 390000000, 'xforce.jpg', 'TERSEDIA', 'Mitsubishi Xforce SUV.'),
('NSS-001', 4, 20, 2026, 'Putih', 395000000, 'xforce.jpg', 'TERSEDIA', 'Unit contoh untuk pengujian sistem.');

-- =========================================================
-- 7. PENJUALAN
-- Status digunakan langsung oleh source:
-- Menunggu Verifikasi / Selesai
-- =========================================================
CREATE TABLE penjualan (
    id_penjualan INT AUTO_INCREMENT PRIMARY KEY,
    no_transaksi VARCHAR(50) NOT NULL UNIQUE,
    id_customer INT NOT NULL,
    id_sales INT NOT NULL,
    id_kendaraan INT NOT NULL,
    harga DECIMAL(15,2) NOT NULL DEFAULT 0,
    tanggal DATE NOT NULL,
    metode_pembayaran ENUM('Cash','Transfer','Kredit') NOT NULL,
    status ENUM('Menunggu Verifikasi','Selesai','Ditolak') NOT NULL DEFAULT 'Menunggu Verifikasi',
    dp DECIMAL(15,2) NOT NULL DEFAULT 0,
    tenor INT NOT NULL DEFAULT 0,
    bunga_tahunan DECIMAL(5,2) NOT NULL DEFAULT 0,
    pokok_pinjaman DECIMAL(15,2) NOT NULL DEFAULT 0,
    total_bunga DECIMAL(15,2) NOT NULL DEFAULT 0,
    total_kredit DECIMAL(15,2) NOT NULL DEFAULT 0,
    angsuran_per_bulan DECIMAL(15,2) NOT NULL DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_penjualan_customer
        FOREIGN KEY (id_customer) REFERENCES customer(id_customer)
        ON UPDATE CASCADE ON DELETE RESTRICT,
    CONSTRAINT fk_penjualan_sales
        FOREIGN KEY (id_sales) REFERENCES sales(id_sales)
        ON UPDATE CASCADE ON DELETE RESTRICT,
    CONSTRAINT fk_penjualan_kendaraan
        FOREIGN KEY (id_kendaraan) REFERENCES kendaraan(id_kendaraan)
        ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB;

-- Contoh transaksi selesai/cash
INSERT INTO penjualan
(no_transaksi,id_customer,id_sales,id_kendaraan,harga,tanggal,metode_pembayaran,status,
dp,tenor,bunga_tahunan,pokok_pinjaman,total_bunga,total_kredit,angsuran_per_bulan)
VALUES
('TRX-202609010001',1,1,1,250000000,'2026-09-01','Cash','Selesai',
0,0,0,0,0,0,0);

-- Contoh transaksi menunggu verifikasi/kredit
INSERT INTO penjualan
(no_transaksi,id_customer,id_sales,id_kendaraan,harga,tanggal,metode_pembayaran,status,
dp,tenor,bunga_tahunan,pokok_pinjaman,total_bunga,total_kredit,angsuran_per_bulan)
VALUES
('TRX-202609260001',2,1,3,610000000,'2026-09-26','Kredit','Menunggu Verifikasi',
100000000,36,8,510000000,122400000,632400000,17566666.67);

-- Sesuaikan status kendaraan dengan transaksi contoh
UPDATE kendaraan SET status='TERJUAL' WHERE id_kendaraan=1;
UPDATE kendaraan SET status='DIPROSES' WHERE id_kendaraan=3;

-- =========================================================
-- 8. HISTORY PENJUALAN
-- Dibuat saat transaksi diverifikasi oleh admin.
-- =========================================================
CREATE TABLE history_penjualan (
    id_history INT AUTO_INCREMENT PRIMARY KEY,
    id_penjualan INT NOT NULL,
    tanggal_history TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_history_penjualan
        FOREIGN KEY (id_penjualan) REFERENCES penjualan(id_penjualan)
        ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB;

INSERT INTO history_penjualan (id_penjualan)
SELECT id_penjualan
FROM penjualan
WHERE status='Selesai';

-- =========================================================
-- 9. INSENTIF SALES
-- Source website menambah Rp500.000 setiap transaksi selesai.
-- =========================================================
CREATE TABLE insentif (
    id_insentif INT AUTO_INCREMENT PRIMARY KEY,
    id_sales INT NOT NULL,
    periode VARCHAR(20) NOT NULL,
    jumlah_penjualan INT NOT NULL DEFAULT 0,
    jumlah_insentif DECIMAL(15,2) NOT NULL DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_insentif_sales
        FOREIGN KEY (id_sales) REFERENCES sales(id_sales)
        ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB;

INSERT INTO insentif
(id_sales, periode, jumlah_penjualan, jumlah_insentif)
VALUES
(1, 'September 2026', 1, 500000);

-- =========================================================
-- 10. KREDIT
-- Dibuat saat admin memverifikasi transaksi Kredit.
-- =========================================================
CREATE TABLE kredit (
    id_kredit INT AUTO_INCREMENT PRIMARY KEY,
    id_penjualan INT NOT NULL UNIQUE,
    harga_kendaraan DECIMAL(15,2) NOT NULL DEFAULT 0,
    dp DECIMAL(15,2) NOT NULL DEFAULT 0,
    pokok_pinjaman DECIMAL(15,2) NOT NULL DEFAULT 0,
    tenor INT NOT NULL DEFAULT 0,
    bunga_tahunan DECIMAL(5,2) NOT NULL DEFAULT 0,
    total_bunga DECIMAL(15,2) NOT NULL DEFAULT 0,
    total_kredit DECIMAL(15,2) NOT NULL DEFAULT 0,
    angsuran_per_bulan DECIMAL(15,2) NOT NULL DEFAULT 0,
    tanggal_mulai DATE DEFAULT NULL,
    status_kredit ENUM('Aktif','Lunas') NOT NULL DEFAULT 'Aktif',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_kredit_penjualan
        FOREIGN KEY (id_penjualan) REFERENCES penjualan(id_penjualan)
        ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB;

-- =========================================================
-- 11. ANGSURAN
-- Satu baris untuk setiap periode angsuran.
-- =========================================================
CREATE TABLE angsuran (
    id_angsuran INT AUTO_INCREMENT PRIMARY KEY,
    id_kredit INT NOT NULL,
    angsuran_ke INT NOT NULL,
    jatuh_tempo DATE NOT NULL,
    jumlah_angsuran DECIMAL(15,2) NOT NULL DEFAULT 0,
    jumlah_bayar DECIMAL(15,2) NOT NULL DEFAULT 0,
    tanggal_bayar DATE DEFAULT NULL,
    status ENUM('Belum Bayar','Lunas') NOT NULL DEFAULT 'Belum Bayar',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_angsuran_kredit
        FOREIGN KEY (id_kredit) REFERENCES kredit(id_kredit)
        ON UPDATE CASCADE ON DELETE CASCADE,
    UNIQUE KEY uq_angsuran_kredit_ke (id_kredit, angsuran_ke)
) ENGINE=InnoDB;

-- =========================================================
-- CONTOH KREDIT + ANGSURAN
-- Agar menu kredit/angsuran dapat langsung diuji.
-- Transaksi kedua dibuat sebagai contoh kredit aktif.
-- =========================================================
INSERT INTO kredit
(id_penjualan,harga_kendaraan,dp,pokok_pinjaman,tenor,bunga_tahunan,
total_bunga,total_kredit,angsuran_per_bulan,tanggal_mulai,status_kredit)
VALUES
(2,610000000,100000000,510000000,36,8,
122400000,632400000,17566666.67,'2026-09-26','Aktif');

INSERT INTO angsuran
(id_kredit,angsuran_ke,jatuh_tempo,jumlah_angsuran,jumlah_bayar,status)
VALUES
(1,1,'2026-10-26',17566666.67,0,'Belum Bayar'),
(1,2,'2026-11-26',17566666.67,0,'Belum Bayar'),
(1,3,'2026-12-26',17566666.67,0,'Belum Bayar'),
(1,4,'2027-01-26',17566666.67,0,'Belum Bayar'),
(1,5,'2027-02-26',17566666.67,0,'Belum Bayar'),
(1,6,'2027-03-26',17566666.67,0,'Belum Bayar'),
(1,7,'2027-04-26',17566666.67,0,'Belum Bayar'),
(1,8,'2027-05-26',17566666.67,0,'Belum Bayar'),
(1,9,'2027-06-26',17566666.67,0,'Belum Bayar'),
(1,10,'2027-07-26',17566666.67,0,'Belum Bayar'),
(1,11,'2027-08-26',17566666.67,0,'Belum Bayar'),
(1,12,'2027-09-26',17566666.67,0,'Belum Bayar'),
(1,13,'2027-10-26',17566666.67,0,'Belum Bayar'),
(1,14,'2027-11-26',17566666.67,0,'Belum Bayar'),
(1,15,'2027-12-26',17566666.67,0,'Belum Bayar'),
(1,16,'2028-01-26',17566666.67,0,'Belum Bayar'),
(1,17,'2028-02-26',17566666.67,0,'Belum Bayar'),
(1,18,'2028-03-26',17566666.67,0,'Belum Bayar'),
(1,19,'2028-04-26',17566666.67,0,'Belum Bayar'),
(1,20,'2028-05-26',17566666.67,0,'Belum Bayar'),
(1,21,'2028-06-26',17566666.67,0,'Belum Bayar'),
(1,22,'2028-07-26',17566666.67,0,'Belum Bayar'),
(1,23,'2028-08-26',17566666.67,0,'Belum Bayar'),
(1,24,'2028-09-26',17566666.67,0,'Belum Bayar'),
(1,25,'2028-10-26',17566666.67,0,'Belum Bayar'),
(1,26,'2028-11-26',17566666.67,0,'Belum Bayar'),
(1,27,'2028-12-26',17566666.67,0,'Belum Bayar'),
(1,28,'2029-01-26',17566666.67,0,'Belum Bayar'),
(1,29,'2029-02-26',17566666.67,0,'Belum Bayar'),
(1,30,'2029-03-26',17566666.67,0,'Belum Bayar'),
(1,31,'2029-04-26',17566666.67,0,'Belum Bayar'),
(1,32,'2029-05-26',17566666.67,0,'Belum Bayar'),
(1,33,'2029-06-26',17566666.67,0,'Belum Bayar'),
(1,34,'2029-07-26',17566666.67,0,'Belum Bayar'),
(1,35,'2029-08-26',17566666.67,0,'Belum Bayar'),
(1,36,'2029-09-26',17566666.67,0,'Belum Bayar');

-- =========================================================
-- INDEX TAMBAHAN UNTUK QUERY WEBSITE
-- =========================================================
CREATE INDEX idx_kendaraan_status ON kendaraan(status);
CREATE INDEX idx_penjualan_status ON penjualan(status);
CREATE INDEX idx_penjualan_tanggal ON penjualan(tanggal);
CREATE INDEX idx_penjualan_sales ON penjualan(id_sales);
CREATE INDEX idx_penjualan_customer ON penjualan(id_customer);
CREATE INDEX idx_kredit_status ON kredit(status_kredit);
CREATE INDEX idx_angsuran_status ON angsuran(status);
CREATE INDEX idx_insentif_sales ON insentif(id_sales);

-- =========================================================
-- SELESAI
-- =========================================================
