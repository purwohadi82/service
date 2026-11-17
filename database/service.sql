-- ============================================================
-- Database: service (CodeIgniter 4 - Monitoring Service Kendaraan)
-- MySQL 5.7 / 8.x
-- Kredensial: root / (kosong)
-- ============================================================

CREATE DATABASE IF NOT EXISTS `service`
  DEFAULT CHARACTER SET utf8mb4
  DEFAULT COLLATE utf8mb4_general_ci;
USE `service`;

-- ------------------------------------------------------------
-- 1. Pengguna (login & daftar)
-- ------------------------------------------------------------
DROP TABLE IF EXISTS `notifikasi`;
DROP TABLE IF EXISTS `service_item`;
DROP TABLE IF EXISTS `service`;
DROP TABLE IF EXISTS `bengkel`;
DROP TABLE IF EXISTS `sparepart`;
DROP TABLE IF EXISTS `kendaraan`;
DROP TABLE IF EXISTS `users`;

CREATE TABLE `users` (
  `id`         INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `username`   VARCHAR(50)  NOT NULL,
  `password`   VARCHAR(255) NOT NULL,
  `nama`       VARCHAR(100) NOT NULL,
  `email`      VARCHAR(100) DEFAULT NULL,
  `no_hp`      VARCHAR(20)  DEFAULT NULL,
  `role`       ENUM('admin','user') NOT NULL DEFAULT 'user',
  `created_at` DATETIME     DEFAULT NULL,
  `updated_at` DATETIME     DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_users_username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ------------------------------------------------------------
-- 2. Kendaraan (roda 2 / roda 4)
-- ------------------------------------------------------------
CREATE TABLE `kendaraan` (
  `id`         INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id`    INT UNSIGNED NOT NULL,
  `jenis`      ENUM('roda2','roda4') NOT NULL DEFAULT 'roda2',
  `merk`       VARCHAR(50)  NOT NULL,
  `tipe`       VARCHAR(80)  DEFAULT NULL,
  `no_pol`     VARCHAR(20)  DEFAULT NULL,
  `tahun`      SMALLINT     DEFAULT NULL,
  `warna`      VARCHAR(30)  DEFAULT NULL,
  `foto`       VARCHAR(255) DEFAULT NULL,
  `created_at` DATETIME     DEFAULT NULL,
  `updated_at` DATETIME     DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_kendaraan_user` (`user_id`),
  CONSTRAINT `fk_kendaraan_user` FOREIGN KEY (`user_id`)
    REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ------------------------------------------------------------
-- 3. Spare part (kode, nama, harga, foto)
-- ------------------------------------------------------------
CREATE TABLE `sparepart` (
  `id`         INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `kode`       VARCHAR(30)  NOT NULL,
  `nama`       VARCHAR(120) NOT NULL,
  `harga`      DECIMAL(15,2) NOT NULL DEFAULT 0,
  `satuan`     VARCHAR(20)  DEFAULT 'pcs',
  `stok`       INT          NOT NULL DEFAULT 0,
  `foto`       VARCHAR(255) DEFAULT NULL,
  `created_at` DATETIME     DEFAULT NULL,
  `updated_at` DATETIME     DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_sparepart_kode` (`kode`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ------------------------------------------------------------
-- 4. Bengkel kendaraan
-- ------------------------------------------------------------
CREATE TABLE `bengkel` (
  `id`         INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `nama`       VARCHAR(120) NOT NULL,
  `alamat`     VARCHAR(255) DEFAULT NULL,
  `telepon`    VARCHAR(30)  DEFAULT NULL,
  `jam_buka`   VARCHAR(60)  DEFAULT NULL,
  `layanan`    VARCHAR(255) DEFAULT NULL,
  `foto`       VARCHAR(255) DEFAULT NULL,
  `created_at` DATETIME     DEFAULT NULL,
  `updated_at` DATETIME     DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ------------------------------------------------------------
-- 5. Data service kendaraan (input service)
-- ------------------------------------------------------------
CREATE TABLE `service` (
  `id`                  INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id`             INT UNSIGNED NOT NULL,
  `kendaraan_id`        INT UNSIGNED NOT NULL,
  `bengkel_id`          INT UNSIGNED DEFAULT NULL,
  `tanggal`             DATE NOT NULL,
  `odometer`            INT DEFAULT NULL,
  `keluhan`             TEXT,
  `teknisi`             VARCHAR(100) DEFAULT NULL,
  `total_biaya`         DECIMAL(15,2) NOT NULL DEFAULT 0,
  `biaya_jasa`          DECIMAL(15,2) NOT NULL DEFAULT 0,
  `status`              ENUM('dijadwalkan','diproses','selesai') NOT NULL DEFAULT 'selesai',
  `catatan`             TEXT,
  `next_service_date`   DATE DEFAULT NULL,
  `next_service_odometer` INT DEFAULT NULL,
  `created_at`          DATETIME DEFAULT NULL,
  `updated_at`          DATETIME DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_service_user` (`user_id`),
  KEY `idx_service_kendaraan` (`kendaraan_id`),
  KEY `idx_service_bengkel` (`bengkel_id`),
  CONSTRAINT `fk_service_user` FOREIGN KEY (`user_id`)
    REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_service_kendaraan` FOREIGN KEY (`kendaraan_id`)
    REFERENCES `kendaraan` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_service_bengkel` FOREIGN KEY (`bengkel_id`)
    REFERENCES `bengkel` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ------------------------------------------------------------
-- 6. Rincian spare part per service
-- ------------------------------------------------------------
CREATE TABLE `service_item` (
  `id`            INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `service_id`    INT UNSIGNED NOT NULL,
  `sparepart_id`  INT UNSIGNED DEFAULT NULL,
  `nama`          VARCHAR(120) NOT NULL,
  `qty`           INT NOT NULL DEFAULT 1,
  `harga_satuan`  DECIMAL(15,2) NOT NULL DEFAULT 0,
  `subtotal`      DECIMAL(15,2) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `idx_item_service` (`service_id`),
  CONSTRAINT `fk_item_service` FOREIGN KEY (`service_id`)
    REFERENCES `service` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_item_sparepart` FOREIGN KEY (`sparepart_id`)
    REFERENCES `sparepart` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ------------------------------------------------------------
-- 7. Setting notifikasi service berikutnya (per kendaraan)
-- ------------------------------------------------------------
CREATE TABLE `notifikasi` (
  `id`                INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id`           INT UNSIGNED NOT NULL,
  `kendaraan_id`      INT UNSIGNED NOT NULL,
  `aktif`             TINYINT(1) NOT NULL DEFAULT 1,
  `sebelum_hari`      INT NOT NULL DEFAULT 7,
  `interval_hari`     INT NOT NULL DEFAULT 180,
  `jam_pengingat`     TIME NOT NULL DEFAULT '08:00:00',
  `kanal`             ENUM('app','email','wa') NOT NULL DEFAULT 'app',
  `tujuan`            VARCHAR(100) DEFAULT NULL,
  `created_at`        DATETIME DEFAULT NULL,
  `updated_at`        DATETIME DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_notifikasi_kendaraan` (`kendaraan_id`),
  KEY `idx_notifikasi_user` (`user_id`),
  CONSTRAINT `fk_notif_user` FOREIGN KEY (`user_id`)
    REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_notif_kendaraan` FOREIGN KEY (`kendaraan_id`)
    REFERENCES `kendaraan` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ============================================================
-- DATA AWAL
-- ============================================================

-- Akun: admin / admin123  dan  bengkel / bengkel123
INSERT INTO `users` (`id`,`username`,`password`,`nama`,`email`,`no_hp`,`role`,`created_at`) VALUES
(1,'admin','$2y$10$aRmns7MPHfbzC.zF18bV6OnG7OazHNOkztJR6heNKfK5gupX8kq9C','Administrator','admin@service.local','081234567890','admin',NOW()),
(2,'bengkel','$2y$10$Q5ucz2XqCr9dRMdy.N8B0OyYyEanW3Sa84O6tj.Z14Ea8./FrTeCa','Bengkel Saya','bengkel@service.local','089876543210','user',NOW());

-- Akun bawaan: admin / admin123   dan   bengkel / bengkel123

INSERT INTO `bengkel` (`id`,`nama`,`alamat`,`telepon`,`jam_buka`,`layanan`,`created_at`) VALUES
(1,'Bengkel Jaya Motor','Jl. Merdeka No. 12, Jakarta','021-5551234','Senin - Sabtu (08.00 - 17.00)','Servis rutin, ganti oli, ban, rem, cvt',NOW()),
(2,'Bengkel Auto Prima','Jl. Sudirman No. 45, Bekasi','021-8899001','Senin - Minggu (09.00 - 20.00)','Servis besar, body repair, kelistrikan, AC',NOW());

INSERT INTO `sparepart` (`id`,`kode`,`nama`,`harga`,`satuan`,`stok`,`created_at`) VALUES
(1,'SP-0001','Oli Mesin 10W-40 1L',65000,'liter',50,NOW()),
(2,'SP-0002','Filter Oli',35000,'pcs',40,NOW()),
(3,'SP-0003','Busi Iridium',85000,'pcs',30,NOW()),
(4,'SP-0004','Kampas Rem Depan',150000,'pcs',25,NOW()),
(5,'SP-0005','Ban Depan 80/90-14',220000,'pcs',15,NOW()),
(6,'SP-0006','Aki Kering 12V',450000,'pcs',10,NOW()),
(7,'SP-0007','Filter Udara',55000,'pcs',20,NOW()),
(8,'SP-0008','V-Belt CVT',175000,'pcs',18,NOW());

INSERT INTO `kendaraan` (`id`,`user_id`,`jenis`,`merk`,`tipe`,`no_pol`,`tahun`,`warna`,`created_at`) VALUES
(1,1,'roda2','Honda','Vario 160','B 1234 ABC',2023,'Putih',NOW()),
(2,1,'roda4','Toyota','Avanza 1.3 G','B 5678 XYZ',2021,'Silver',NOW());
