-- =====================================================
-- SOLARYA TRAVEL - DATABASE SCHEMA
-- Sistema Prenotazione Catamarani Premium
-- Versione: 1.0
-- Database: MySQL 8.0+
-- =====================================================

-- Configurazione iniziale
SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;
SET SQL_MODE = 'STRICT_TRANS_TABLES,NO_ENGINE_SUBSTITUTION';

-- =====================================================
-- TABELLA: users
-- Utenti del sistema (clienti e admin)
-- =====================================================
DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `uuid` CHAR(36) NOT NULL,
    `name` VARCHAR(255) NOT NULL,
    `email` VARCHAR(255) NOT NULL,
    `email_verified_at` TIMESTAMP NULL,
    `password` VARCHAR(255) NOT NULL,
    `phone` VARCHAR(50) NULL,
    `role` ENUM('customer', 'admin', 'super_admin') DEFAULT 'customer',
    `locale` VARCHAR(10) DEFAULT 'it',
    `avatar_url` VARCHAR(500) NULL,
    `marketing_consent` BOOLEAN DEFAULT FALSE,
    `last_login_at` TIMESTAMP NULL,
    `remember_token` VARCHAR(100) NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    `deleted_at` TIMESTAMP NULL,
    
    UNIQUE KEY `uk_users_uuid` (`uuid`),
    UNIQUE KEY `uk_users_email` (`email`),
    INDEX `idx_users_role` (`role`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABELLA: password_reset_tokens
-- Token per reset password
-- =====================================================
DROP TABLE IF EXISTS `password_reset_tokens`;
CREATE TABLE `password_reset_tokens` (
    `email` VARCHAR(255) PRIMARY KEY,
    `token` VARCHAR(255) NOT NULL,
    `created_at` TIMESTAMP NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABELLA: sessions
-- Sessioni utente
-- =====================================================
DROP TABLE IF EXISTS `sessions`;
CREATE TABLE `sessions` (
    `id` VARCHAR(255) PRIMARY KEY,
    `user_id` BIGINT UNSIGNED NULL,
    `ip_address` VARCHAR(45) NULL,
    `user_agent` TEXT NULL,
    `payload` LONGTEXT NOT NULL,
    `last_activity` INT NOT NULL,
    INDEX `idx_sessions_user` (`user_id`),
    INDEX `idx_sessions_activity` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABELLA: catamarans
-- Flotta di catamarani
-- =====================================================
DROP TABLE IF EXISTS `catamarans`;
CREATE TABLE `catamarans` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `uuid` CHAR(36) NOT NULL,
    `name` VARCHAR(255) NOT NULL,
    `slug` VARCHAR(255) NOT NULL,
    `description` TEXT NULL,
    `description_short` VARCHAR(500) NULL,
    `capacity` INT UNSIGNED NOT NULL DEFAULT 12,
    `length_meters` DECIMAL(5,2) NULL,
    `features` JSON NULL COMMENT '["wifi", "bar", "sundeck", ...]',
    `base_price_half_day` DECIMAL(10,2) NOT NULL,
    `base_price_full_day` DECIMAL(10,2) NOT NULL,
    `exclusive_price_half_day` DECIMAL(10,2) NOT NULL,
    `exclusive_price_full_day` DECIMAL(10,2) NOT NULL,
    `price_per_person_half_day` DECIMAL(10,2) NOT NULL,
    `price_per_person_full_day` DECIMAL(10,2) NOT NULL,
    `is_active` BOOLEAN DEFAULT TRUE,
    `sort_order` INT DEFAULT 0,
    `meta_title` VARCHAR(255) NULL,
    `meta_description` VARCHAR(500) NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    `deleted_at` TIMESTAMP NULL,
    
    UNIQUE KEY `uk_catamarans_uuid` (`uuid`),
    UNIQUE KEY `uk_catamarans_slug` (`slug`),
    INDEX `idx_catamarans_active` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABELLA: catamaran_images
-- Immagini dei catamarani
-- =====================================================
DROP TABLE IF EXISTS `catamaran_images`;
CREATE TABLE `catamaran_images` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `catamaran_id` BIGINT UNSIGNED NOT NULL,
    `image_path` VARCHAR(500) NOT NULL,
    `image_alt` VARCHAR(255) NULL,
    `is_primary` BOOLEAN DEFAULT FALSE,
    `sort_order` INT DEFAULT 0,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (`catamaran_id`) REFERENCES `catamarans`(`id`) ON DELETE CASCADE,
    INDEX `idx_catamaran_images_catamaran` (`catamaran_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABELLA: time_slots
-- Fasce orarie predefinite
-- =====================================================
DROP TABLE IF EXISTS `time_slots`;
CREATE TABLE `time_slots` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(100) NOT NULL,
    `slug` VARCHAR(100) NOT NULL,
    `start_time` TIME NOT NULL,
    `end_time` TIME NOT NULL,
    `slot_type` ENUM('half_day', 'full_day') NOT NULL,
    `price_modifier` DECIMAL(5,2) DEFAULT 1.00 COMMENT 'Moltiplicatore prezzo',
    `is_active` BOOLEAN DEFAULT TRUE,
    `sort_order` INT DEFAULT 0,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    UNIQUE KEY `uk_time_slots_slug` (`slug`),
    INDEX `idx_time_slots_type` (`slot_type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABELLA: availability
-- Disponibilità per catamarano/giorno/slot
-- =====================================================
DROP TABLE IF EXISTS `availability`;
CREATE TABLE `availability` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `catamaran_id` BIGINT UNSIGNED NOT NULL,
    `date` DATE NOT NULL,
    `time_slot_id` BIGINT UNSIGNED NULL COMMENT 'NULL = giornata intera',
    `status` ENUM('available', 'partially_booked', 'fully_booked', 'blocked') DEFAULT 'available',
    `seats_available` INT UNSIGNED NOT NULL,
    `seats_booked` INT UNSIGNED DEFAULT 0,
    `is_exclusive_booked` BOOLEAN DEFAULT FALSE,
    `block_reason` VARCHAR(255) NULL,
    `custom_price` DECIMAL(10,2) NULL COMMENT 'Override prezzo per questa data',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (`catamaran_id`) REFERENCES `catamarans`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`time_slot_id`) REFERENCES `time_slots`(`id`) ON DELETE SET NULL,
    
    UNIQUE KEY `uk_availability` (`catamaran_id`, `date`, `time_slot_id`),
    INDEX `idx_availability_date` (`date`),
    INDEX `idx_availability_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABELLA: blocked_dates
-- Date bloccate (manutenzione, eventi, etc.)
-- =====================================================
DROP TABLE IF EXISTS `blocked_dates`;
CREATE TABLE `blocked_dates` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `catamaran_id` BIGINT UNSIGNED NULL COMMENT 'NULL = tutte le barche',
    `start_date` DATE NOT NULL,
    `end_date` DATE NOT NULL,
    `time_slot_id` BIGINT UNSIGNED NULL COMMENT 'NULL = tutto il giorno',
    `reason` VARCHAR(255) NULL,
    `blocked_by` BIGINT UNSIGNED NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (`catamaran_id`) REFERENCES `catamarans`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`time_slot_id`) REFERENCES `time_slots`(`id`) ON DELETE SET NULL,
    FOREIGN KEY (`blocked_by`) REFERENCES `users`(`id`),
    
    INDEX `idx_blocked_dates_range` (`start_date`, `end_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABELLA: addons
-- Servizi aggiuntivi
-- =====================================================
DROP TABLE IF EXISTS `addons`;
CREATE TABLE `addons` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `uuid` CHAR(36) NOT NULL,
    `name` VARCHAR(255) NOT NULL,
    `slug` VARCHAR(255) NOT NULL,
    `description` TEXT NULL,
    `price` DECIMAL(10,2) NOT NULL,
    `price_type` ENUM('per_booking', 'per_person', 'per_day') NOT NULL DEFAULT 'per_booking',
    `max_quantity` INT UNSIGNED NULL,
    `is_active` BOOLEAN DEFAULT TRUE,
    `requires_advance_booking` BOOLEAN DEFAULT FALSE,
    `advance_hours` INT UNSIGNED DEFAULT 24,
    `image_path` VARCHAR(500) NULL,
    `sort_order` INT DEFAULT 0,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    `deleted_at` TIMESTAMP NULL,
    
    UNIQUE KEY `uk_addons_uuid` (`uuid`),
    UNIQUE KEY `uk_addons_slug` (`slug`),
    INDEX `idx_addons_active` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABELLA: discount_codes
-- Codici sconto
-- =====================================================
DROP TABLE IF EXISTS `discount_codes`;
CREATE TABLE `discount_codes` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `code` VARCHAR(50) NOT NULL,
    `description` VARCHAR(255) NULL,
    `discount_type` ENUM('percentage', 'fixed') NOT NULL,
    `discount_value` DECIMAL(10,2) NOT NULL,
    `min_amount` DECIMAL(10,2) NULL,
    `max_discount` DECIMAL(10,2) NULL,
    `usage_limit` INT UNSIGNED NULL,
    `usage_count` INT UNSIGNED DEFAULT 0,
    `user_limit` INT UNSIGNED DEFAULT 1 COMMENT 'Usi per utente',
    `valid_from` TIMESTAMP NULL,
    `valid_until` TIMESTAMP NULL,
    `is_active` BOOLEAN DEFAULT TRUE,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    UNIQUE KEY `uk_discount_codes_code` (`code`),
    INDEX `idx_discount_codes_active` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABELLA: bookings
-- Prenotazioni principali
-- =====================================================
DROP TABLE IF EXISTS `bookings`;
CREATE TABLE `bookings` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `uuid` CHAR(36) NOT NULL,
    `booking_number` VARCHAR(20) NOT NULL COMMENT 'SLY-2026-00001',
    `user_id` BIGINT UNSIGNED NOT NULL,
    `catamaran_id` BIGINT UNSIGNED NOT NULL,
    
    -- Tipologia prenotazione
    `booking_type` ENUM('seats', 'exclusive') NOT NULL DEFAULT 'seats',
    `duration_type` ENUM('half_day', 'full_day', 'multi_day') NOT NULL,
    
    -- Date e orari
    `start_date` DATE NOT NULL,
    `end_date` DATE NOT NULL,
    `time_slot_id` BIGINT UNSIGNED NULL COMMENT 'Per half_day',
    
    -- Posti
    `seats_booked` INT UNSIGNED NOT NULL DEFAULT 1,
    
    -- Stato prenotazione
    `status` ENUM(
        'pending',
        'confirmed',
        'checked_in',
        'completed',
        'cancelled',
        'refunded',
        'no_show'
    ) NOT NULL DEFAULT 'pending',
    
    -- Prezzi
    `base_amount` DECIMAL(10,2) NOT NULL,
    `addons_amount` DECIMAL(10,2) DEFAULT 0.00,
    `discount_amount` DECIMAL(10,2) DEFAULT 0.00,
    `tax_amount` DECIMAL(10,2) DEFAULT 0.00,
    `total_amount` DECIMAL(10,2) NOT NULL,
    `currency` VARCHAR(3) DEFAULT 'EUR',
    
    -- Codice sconto
    `discount_code_id` BIGINT UNSIGNED NULL,
    
    -- QR Code per check-in
    `qr_code` VARCHAR(100) NOT NULL,
    `qr_code_url` VARCHAR(500) NULL,
    
    -- Check-in
    `checked_in_at` TIMESTAMP NULL,
    `checked_in_by` BIGINT UNSIGNED NULL,
    
    -- Note
    `customer_notes` TEXT NULL,
    `admin_notes` TEXT NULL,
    
    -- Metadati
    `ip_address` VARCHAR(45) NULL,
    `user_agent` VARCHAR(500) NULL,
    `source` VARCHAR(50) DEFAULT 'website' COMMENT 'website, api, admin',
    
    -- Timestamps
    `confirmed_at` TIMESTAMP NULL,
    `cancelled_at` TIMESTAMP NULL,
    `completed_at` TIMESTAMP NULL,
    `expires_at` TIMESTAMP NULL COMMENT 'Scadenza pending',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    `deleted_at` TIMESTAMP NULL,
    
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`),
    FOREIGN KEY (`catamaran_id`) REFERENCES `catamarans`(`id`),
    FOREIGN KEY (`time_slot_id`) REFERENCES `time_slots`(`id`) ON DELETE SET NULL,
    FOREIGN KEY (`checked_in_by`) REFERENCES `users`(`id`) ON DELETE SET NULL,
    FOREIGN KEY (`discount_code_id`) REFERENCES `discount_codes`(`id`) ON DELETE SET NULL,
    
    UNIQUE KEY `uk_bookings_uuid` (`uuid`),
    UNIQUE KEY `uk_bookings_number` (`booking_number`),
    UNIQUE KEY `uk_bookings_qr` (`qr_code`),
    INDEX `idx_bookings_user` (`user_id`),
    INDEX `idx_bookings_catamaran` (`catamaran_id`),
    INDEX `idx_bookings_dates` (`start_date`, `end_date`),
    INDEX `idx_bookings_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABELLA: booking_seats
-- Dettaglio posti per prenotazione
-- =====================================================
DROP TABLE IF EXISTS `booking_seats`;
CREATE TABLE `booking_seats` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `booking_id` BIGINT UNSIGNED NOT NULL,
    `guest_name` VARCHAR(255) NULL,
    `guest_email` VARCHAR(255) NULL,
    `is_primary` BOOLEAN DEFAULT FALSE COMMENT 'Intestatario',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (`booking_id`) REFERENCES `bookings`(`id`) ON DELETE CASCADE,
    INDEX `idx_booking_seats_booking` (`booking_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABELLA: booking_addons
-- Addon associati alla prenotazione
-- =====================================================
DROP TABLE IF EXISTS `booking_addons`;
CREATE TABLE `booking_addons` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `booking_id` BIGINT UNSIGNED NOT NULL,
    `addon_id` BIGINT UNSIGNED NOT NULL,
    `quantity` INT UNSIGNED DEFAULT 1,
    `unit_price` DECIMAL(10,2) NOT NULL,
    `total_price` DECIMAL(10,2) NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (`booking_id`) REFERENCES `bookings`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`addon_id`) REFERENCES `addons`(`id`),
    INDEX `idx_booking_addons_booking` (`booking_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABELLA: payments
-- Pagamenti
-- =====================================================
DROP TABLE IF EXISTS `payments`;
CREATE TABLE `payments` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `uuid` CHAR(36) NOT NULL,
    `booking_id` BIGINT UNSIGNED NOT NULL,
    
    -- Gateway
    `gateway` ENUM('stripe', 'paypal') NOT NULL,
    `gateway_payment_id` VARCHAR(255) NULL COMMENT 'Stripe PaymentIntent ID',
    `gateway_transaction_id` VARCHAR(255) NULL,
    `gateway_customer_id` VARCHAR(255) NULL,
    
    -- Importi
    `amount` DECIMAL(10,2) NOT NULL,
    `currency` VARCHAR(3) DEFAULT 'EUR',
    `fee_amount` DECIMAL(10,2) DEFAULT 0.00 COMMENT 'Commissioni gateway',
    `net_amount` DECIMAL(10,2) NULL,
    
    -- Stato
    `status` ENUM(
        'pending',
        'processing',
        'succeeded',
        'failed',
        'cancelled',
        'refunded',
        'partially_refunded'
    ) NOT NULL DEFAULT 'pending',
    
    -- Dettagli carta (mascherati)
    `card_brand` VARCHAR(20) NULL,
    `card_last_four` VARCHAR(4) NULL,
    
    -- Metadati
    `gateway_response` JSON NULL,
    `failure_reason` VARCHAR(500) NULL,
    `refund_reason` VARCHAR(500) NULL,
    `refunded_amount` DECIMAL(10,2) DEFAULT 0.00,
    `refunded_at` TIMESTAMP NULL,
    
    -- Idempotency
    `idempotency_key` VARCHAR(100) NULL,
    
    -- Timestamps
    `paid_at` TIMESTAMP NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (`booking_id`) REFERENCES `bookings`(`id`),
    
    UNIQUE KEY `uk_payments_uuid` (`uuid`),
    UNIQUE KEY `uk_payments_idempotency` (`idempotency_key`),
    INDEX `idx_payments_booking` (`booking_id`),
    INDEX `idx_payments_gateway` (`gateway_payment_id`),
    INDEX `idx_payments_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABELLA: payment_webhooks
-- Log webhook pagamenti
-- =====================================================
DROP TABLE IF EXISTS `payment_webhooks`;
CREATE TABLE `payment_webhooks` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `gateway` ENUM('stripe', 'paypal') NOT NULL,
    `event_type` VARCHAR(100) NOT NULL,
    `event_id` VARCHAR(255) NOT NULL,
    `payload` JSON NOT NULL,
    `processed` BOOLEAN DEFAULT FALSE,
    `processed_at` TIMESTAMP NULL,
    `error_message` TEXT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    UNIQUE KEY `uk_webhook_event` (`gateway`, `event_id`),
    INDEX `idx_webhooks_processed` (`processed`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABELLA: check_ins
-- Registro check-in
-- =====================================================
DROP TABLE IF EXISTS `check_ins`;
CREATE TABLE `check_ins` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `booking_id` BIGINT UNSIGNED NOT NULL,
    `checked_in_by` BIGINT UNSIGNED NOT NULL,
    `check_in_method` ENUM('qr_scan', 'manual', 'app') DEFAULT 'qr_scan',
    `device_info` VARCHAR(255) NULL,
    `location_lat` DECIMAL(10,8) NULL,
    `location_lng` DECIMAL(11,8) NULL,
    `notes` TEXT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (`booking_id`) REFERENCES `bookings`(`id`),
    FOREIGN KEY (`checked_in_by`) REFERENCES `users`(`id`),
    INDEX `idx_check_ins_booking` (`booking_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABELLA: reviews
-- Recensioni (Fase 2)
-- =====================================================
DROP TABLE IF EXISTS `reviews`;
CREATE TABLE `reviews` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `booking_id` BIGINT UNSIGNED NOT NULL,
    `user_id` BIGINT UNSIGNED NOT NULL,
    `rating` TINYINT UNSIGNED NOT NULL,
    `title` VARCHAR(255) NULL,
    `comment` TEXT NULL,
    `is_verified` BOOLEAN DEFAULT FALSE,
    `is_published` BOOLEAN DEFAULT FALSE,
    `published_at` TIMESTAMP NULL,
    `admin_response` TEXT NULL,
    `responded_at` TIMESTAMP NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (`booking_id`) REFERENCES `bookings`(`id`),
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`),
    
    UNIQUE KEY `uk_review_booking` (`booking_id`),
    INDEX `idx_reviews_user` (`user_id`),
    INDEX `idx_reviews_published` (`is_published`),
    
    CONSTRAINT `chk_rating` CHECK (`rating` BETWEEN 1 AND 5)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABELLA: email_logs
-- Log email inviate
-- =====================================================
DROP TABLE IF EXISTS `email_logs`;
CREATE TABLE `email_logs` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `user_id` BIGINT UNSIGNED NULL,
    `booking_id` BIGINT UNSIGNED NULL,
    `email_type` VARCHAR(100) NOT NULL,
    `recipient_email` VARCHAR(255) NOT NULL,
    `subject` VARCHAR(255) NOT NULL,
    `status` ENUM('queued', 'sent', 'delivered', 'bounced', 'failed') DEFAULT 'queued',
    `provider_message_id` VARCHAR(255) NULL,
    `error_message` TEXT NULL,
    `sent_at` TIMESTAMP NULL,
    `opened_at` TIMESTAMP NULL,
    `clicked_at` TIMESTAMP NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE SET NULL,
    FOREIGN KEY (`booking_id`) REFERENCES `bookings`(`id`) ON DELETE SET NULL,
    INDEX `idx_email_logs_type` (`email_type`),
    INDEX `idx_email_logs_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABELLA: settings
-- Configurazioni sistema
-- =====================================================
DROP TABLE IF EXISTS `settings`;
CREATE TABLE `settings` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `group` VARCHAR(100) NOT NULL,
    `key` VARCHAR(100) NOT NULL,
    `value` TEXT NULL,
    `type` ENUM('string', 'integer', 'boolean', 'json', 'array') DEFAULT 'string',
    `is_public` BOOLEAN DEFAULT FALSE,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    UNIQUE KEY `uk_setting` (`group`, `key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABELLA: activity_log
-- Audit trail
-- =====================================================
DROP TABLE IF EXISTS `activity_log`;
CREATE TABLE `activity_log` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `log_name` VARCHAR(100) DEFAULT 'default',
    `description` TEXT NOT NULL,
    `subject_type` VARCHAR(255) NULL,
    `subject_id` BIGINT UNSIGNED NULL,
    `causer_type` VARCHAR(255) NULL,
    `causer_id` BIGINT UNSIGNED NULL,
    `properties` JSON NULL,
    `batch_uuid` CHAR(36) NULL,
    `event` VARCHAR(100) NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    INDEX `idx_activity_subject` (`subject_type`, `subject_id`),
    INDEX `idx_activity_causer` (`causer_type`, `causer_id`),
    INDEX `idx_activity_log_name` (`log_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABELLA: cache
-- Cache Laravel
-- =====================================================
DROP TABLE IF EXISTS `cache`;
CREATE TABLE `cache` (
    `key` VARCHAR(255) PRIMARY KEY,
    `value` MEDIUMTEXT NOT NULL,
    `expiration` INT NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `cache_locks`;
CREATE TABLE `cache_locks` (
    `key` VARCHAR(255) PRIMARY KEY,
    `owner` VARCHAR(255) NOT NULL,
    `expiration` INT NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABELLA: jobs
-- Queue Jobs Laravel
-- =====================================================
DROP TABLE IF EXISTS `jobs`;
CREATE TABLE `jobs` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `queue` VARCHAR(255) NOT NULL,
    `payload` LONGTEXT NOT NULL,
    `attempts` TINYINT UNSIGNED NOT NULL,
    `reserved_at` INT UNSIGNED NULL,
    `available_at` INT UNSIGNED NOT NULL,
    `created_at` INT UNSIGNED NOT NULL,
    INDEX `idx_jobs_queue` (`queue`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `job_batches`;
CREATE TABLE `job_batches` (
    `id` VARCHAR(255) PRIMARY KEY,
    `name` VARCHAR(255) NOT NULL,
    `total_jobs` INT NOT NULL,
    `pending_jobs` INT NOT NULL,
    `failed_jobs` INT NOT NULL,
    `failed_job_ids` LONGTEXT NOT NULL,
    `options` MEDIUMTEXT NULL,
    `cancelled_at` INT NULL,
    `created_at` INT NOT NULL,
    `finished_at` INT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `failed_jobs`;
CREATE TABLE `failed_jobs` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `uuid` VARCHAR(255) NOT NULL,
    `connection` TEXT NOT NULL,
    `queue` TEXT NOT NULL,
    `payload` LONGTEXT NOT NULL,
    `exception` LONGTEXT NOT NULL,
    `failed_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY `uk_failed_jobs_uuid` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- DATI INIZIALI
-- =====================================================

-- Time Slots predefiniti
INSERT INTO `time_slots` (`name`, `slug`, `start_time`, `end_time`, `slot_type`, `sort_order`) VALUES
('Mattina', 'morning', '09:00:00', '13:00:00', 'half_day', 1),
('Pomeriggio', 'afternoon', '14:30:00', '18:30:00', 'half_day', 2),
('Giornata Intera', 'full-day', '09:00:00', '18:30:00', 'full_day', 3);

-- Impostazioni base
INSERT INTO `settings` (`group`, `key`, `value`, `type`, `is_public`) VALUES
('booking', 'advance_booking_hours', '24', 'integer', TRUE),
('booking', 'max_booking_days_ahead', '180', 'integer', TRUE),
('booking', 'pending_expiry_minutes', '30', 'integer', FALSE),
('booking', 'allow_same_day_booking', 'true', 'boolean', TRUE),
('payment', 'tax_rate', '0.22', 'string', FALSE),
('payment', 'default_currency', 'EUR', 'string', TRUE),
('notification', 'review_request_delay_hours', '24', 'integer', FALSE),
('notification', 'reminder_hours_before', '24', 'integer', FALSE),
('company', 'name', 'Solarya Travel', 'string', TRUE),
('company', 'email', 'info@solaryatravel.com', 'string', TRUE),
('company', 'phone', '+39 000 0000000', 'string', TRUE);

-- Addon di esempio
INSERT INTO `addons` (`uuid`, `name`, `slug`, `description`, `price`, `price_type`, `sort_order`) VALUES
(UUID(), 'Pranzo a bordo', 'pranzo-bordo', 'Pranzo gourmet con prodotti locali, bevande incluse', 35.00, 'per_person', 1),
(UUID(), 'Aperitivo al tramonto', 'aperitivo-tramonto', 'Aperitivo con prosecco e stuzzichini durante il tramonto', 25.00, 'per_person', 2),
(UUID(), 'Attrezzatura Snorkeling', 'snorkeling', 'Maschera, pinne e boccaglio per esplorare i fondali', 15.00, 'per_person', 3),
(UUID(), 'Fotografo professionista', 'fotografo', 'Servizio fotografico professionale durante l\'escursione', 150.00, 'per_booking', 4);

-- Catamarani di esempio
INSERT INTO `catamarans` (`uuid`, `name`, `slug`, `description`, `description_short`, `capacity`, `length_meters`, `features`, 
    `base_price_half_day`, `base_price_full_day`, `exclusive_price_half_day`, `exclusive_price_full_day`,
    `price_per_person_half_day`, `price_per_person_full_day`, `sort_order`) VALUES
(UUID(), 'Solarya One', 'solarya-one', 
    'Il nostro catamarano ammiraglia, perfetto per gruppi che cercano comfort e eleganza. Dotato di ampio solarium, bar interno e tutte le comodità per una giornata indimenticabile in mare.',
    'Catamarano di lusso per 12 persone con solarium e bar',
    12, 15.00, '["solarium", "bar", "wifi", "doccia", "sound_system", "paddleboard"]',
    450.00, 800.00, 900.00, 1600.00, 45.00, 80.00, 1),
    
(UUID(), 'Solarya Two', 'solarya-two', 
    'Catamarano sportivo e dinamico, ideale per chi ama le avventure in mare. Equipaggiato per snorkeling e sport acquatici.',
    'Catamarano sportivo per 10 persone',
    10, 12.00, '["snorkeling", "kayak", "wifi", "doccia", "sound_system"]',
    380.00, 700.00, 760.00, 1400.00, 40.00, 75.00, 2),
    
(UUID(), 'Solarya Three', 'solarya-three', 
    'Il più intimo dei nostri catamarani, perfetto per piccoli gruppi o coppie che cercano privacy ed esclusività.',
    'Catamarano intimo per 8 persone',
    8, 10.00, '["solarium", "wifi", "doccia", "sound_system"]',
    320.00, 580.00, 640.00, 1160.00, 42.00, 78.00, 3);

SET FOREIGN_KEY_CHECKS = 1;

-- =====================================================
-- FINE SCHEMA
-- =====================================================
