-- MySQL dump adapted for "The Scent" E-commerce Platform
-- Target: MariaDB 11.7 / MySQL 8.0
-- Reflects decisions from technical_design_specification_document.md

SET NAMES utf8mb4;
SET TIME_ZONE='+00:00';
SET FOREIGN_KEY_CHECKS=0; -- Disable for table creation order
SET SQL_MODE='NO_AUTO_VALUE_ON_ZERO';

--
-- Database: `the_scent` (or your chosen DB name from .env)
--
-- CREATE DATABASE IF NOT EXISTS `the_scent_manatee` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
-- USE `the_scent_manatee`;

-- CREATE USER 'manatee_user'@'localhost' IDENTIFIED BY 'ScentPassword123';
-- GRANT ALL PRIVILEGES ON the_scent_manatee.* TO 'manatee_user'@'localhost';
-- FLUSH PRIVILEGES;

--
-- Table structure for table `users`
--
DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(191) NOT NULL,
  `email` VARCHAR(191) NOT NULL UNIQUE,
  `email_verified_at` TIMESTAMP NULL DEFAULT NULL,
  `password` VARCHAR(191) NOT NULL,
  `remember_token` VARCHAR(100) NULL DEFAULT NULL,
  `role` ENUM('user','admin', 'staff') NOT NULL DEFAULT 'user',
  `status` ENUM('active','inactive','locked') NOT NULL DEFAULT 'active' COMMENT 'User account status',
  `newsletter_subscribed` BOOLEAN NOT NULL DEFAULT FALSE COMMENT 'Flag indicating newsletter subscription',
  `reset_token` VARCHAR(191) NULL DEFAULT NULL COMMENT 'Secure token for password reset requests',
  `reset_token_expires_at` TIMESTAMP NULL DEFAULT NULL COMMENT 'Expiry timestamp for the password reset token',
  `default_shipping_address_id` BIGINT UNSIGNED NULL DEFAULT NULL,
  `default_billing_address_id` BIGINT UNSIGNED NULL DEFAULT NULL,
  `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX `idx_users_status` (`status`),
  INDEX `idx_users_reset_token` (`reset_token`)
  -- Foreign keys for default addresses will be added after `addresses` table is defined
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Table structure for table `addresses`
--
DROP TABLE IF EXISTS `addresses`;
CREATE TABLE `addresses` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `user_id` BIGINT UNSIGNED NULL DEFAULT NULL,
  `first_name` VARCHAR(191) NULL DEFAULT NULL,
  `last_name` VARCHAR(191) NULL DEFAULT NULL,
  `company` VARCHAR(191) NULL DEFAULT NULL,
  `address_line1` VARCHAR(191) NOT NULL,
  `address_line2` VARCHAR(191) NULL DEFAULT NULL,
  `city` VARCHAR(100) NOT NULL,
  `state` VARCHAR(100) NULL DEFAULT NULL COMMENT 'State / Province / Region',
  `postal_code` VARCHAR(20) NOT NULL,
  `country_code` CHAR(2) NOT NULL COMMENT 'ISO 3166-1 alpha-2 country code',
  `phone` VARCHAR(30) NULL DEFAULT NULL,
  `type` ENUM('billing', 'shipping', 'general') NULL DEFAULT 'general',
  `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT `fk_addresses_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Add foreign keys to `users` for default addresses
--
ALTER TABLE `users`
  ADD CONSTRAINT `fk_users_default_shipping_address` FOREIGN KEY (`default_shipping_address_id`) REFERENCES `addresses` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_users_default_billing_address` FOREIGN KEY (`default_billing_address_id`) REFERENCES `addresses` (`id`) ON DELETE SET NULL;

--
-- Table structure for table `categories`
--
DROP TABLE IF EXISTS `categories`;
CREATE TABLE `categories` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(100) NOT NULL,
  `slug` VARCHAR(120) NOT NULL UNIQUE,
  `description` TEXT NULL DEFAULT NULL,
  `parent_id` BIGINT UNSIGNED NULL DEFAULT NULL,
  `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT `fk_categories_parent` FOREIGN KEY (`parent_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Table structure for table `products`
--
DROP TABLE IF EXISTS `products`;
CREATE TABLE `products` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(150) NOT NULL,
  `slug` VARCHAR(170) NOT NULL UNIQUE,
  `description` TEXT NULL DEFAULT NULL,
  `short_description` TEXT NULL DEFAULT NULL COMMENT 'Brief description for listings/previews',
  `image` VARCHAR(191) NULL DEFAULT NULL COMMENT 'Path to main image',
  `gallery_images` JSON NULL DEFAULT NULL COMMENT 'JSON array of additional image paths',
  `price_minor_amount` INTEGER UNSIGNED NOT NULL COMMENT 'Price in minor units (e.g., cents)',
  `currency_code` VARCHAR(3) NOT NULL DEFAULT 'USD',
  `benefits` JSON NULL DEFAULT NULL COMMENT 'Product benefits, stored as JSON array of strings',
  `ingredients` TEXT NULL DEFAULT NULL COMMENT 'List of key ingredients',
  `usage_instructions` TEXT NULL DEFAULT NULL COMMENT 'How to use the product',
  `category_id` BIGINT UNSIGNED NULL DEFAULT NULL,
  `is_featured` BOOLEAN NOT NULL DEFAULT FALSE,
  `is_active` BOOLEAN NOT NULL DEFAULT TRUE,
  `stock_quantity` INTEGER NOT NULL DEFAULT 0,
  `low_stock_threshold` INTEGER UNSIGNED NOT NULL DEFAULT 20,
  `reorder_point` INTEGER UNSIGNED NOT NULL DEFAULT 30,
  `backorder_allowed` BOOLEAN NOT NULL DEFAULT FALSE COMMENT 'Allow purchase when stock_quantity <= 0',
  `highlight_text` VARCHAR(50) NULL DEFAULT NULL,
  `size` VARCHAR(50) NULL DEFAULT NULL COMMENT 'e.g., 10ml, 100g',
  `scent_profile` VARCHAR(191) NULL DEFAULT NULL COMMENT 'Simple text description of scent',
  `origin` VARCHAR(100) NULL DEFAULT NULL COMMENT 'Country or region of origin',
  `sku` VARCHAR(100) NULL DEFAULT NULL UNIQUE COMMENT 'Stock Keeping Unit',
  `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT `fk_products_category` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL,
  INDEX `idx_products_is_active_is_featured` (`is_active`, `is_featured`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Table structure for table `product_attributes`
--
DROP TABLE IF EXISTS `product_attributes`;
CREATE TABLE `product_attributes` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `product_id` BIGINT UNSIGNED NOT NULL,
  `scent_type` ENUM('floral','woody','citrus','oriental','fresh', 'herbal', 'spicy', 'sweet') NULL DEFAULT NULL,
  `mood_effect` ENUM('calming','energizing','focusing','balancing', 'uplifting', 'grounding') NULL DEFAULT NULL,
  `intensity_level` ENUM('light','medium','strong') NULL DEFAULT NULL,
  `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT `fk_product_attributes_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Table structure for table `inventory_movements`
--
DROP TABLE IF EXISTS `inventory_movements`;
CREATE TABLE `inventory_movements` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `product_id` BIGINT UNSIGNED NOT NULL,
  `quantity_change` INTEGER NOT NULL COMMENT 'Positive for stock in, negative for stock out',
  `type` ENUM('sale','restock','return','adjustment','initial') NOT NULL,
  `reference_id` BIGINT UNSIGNED NULL DEFAULT NULL COMMENT 'e.g., order_id for sale, purchase_order_id for restock',
  `notes` TEXT NULL DEFAULT NULL,
  `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT `fk_inventory_movements_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  INDEX `idx_inventory_movements_product_date` (`product_id`,`created_at`),
  INDEX `idx_inventory_movements_type` (`type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Table structure for table `coupons`
--
DROP TABLE IF EXISTS `coupons`;
CREATE TABLE `coupons` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `code` VARCHAR(50) NOT NULL UNIQUE,
  `description` VARCHAR(191) NULL DEFAULT NULL,
  `type` ENUM('percentage', 'fixed_amount') NOT NULL,
  `value_minor_amount` INTEGER UNSIGNED NOT NULL COMMENT 'For fixed_amount type, in minor units. For percentage, it is the percentage value (e.g., 10 for 10%)',
  `currency_code` VARCHAR(3) NULL DEFAULT NULL COMMENT 'Applicable for fixed_amount type. If NULL, applies to any currency.',
  `max_uses` INTEGER UNSIGNED NULL DEFAULT NULL COMMENT 'Total number of times this coupon can be used',
  `uses_count` INTEGER UNSIGNED NOT NULL DEFAULT 0,
  `max_uses_per_user` INTEGER UNSIGNED NULL DEFAULT NULL COMMENT 'Max uses per user',
  `min_purchase_minor_amount` INTEGER UNSIGNED NULL DEFAULT NULL COMMENT 'Minimum purchase amount in minor units for coupon to be valid',
  `min_purchase_currency_code` VARCHAR(3) NULL DEFAULT NULL,
  `valid_from` TIMESTAMP NULL DEFAULT NULL,
  `valid_to` TIMESTAMP NULL DEFAULT NULL,
  `is_active` BOOLEAN NOT NULL DEFAULT TRUE,
  `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX `idx_coupons_is_active_valid_to` (`is_active`, `valid_to`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Table structure for table `orders`
--
DROP TABLE IF EXISTS `orders`;
CREATE TABLE `orders` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `order_number` VARCHAR(32) NOT NULL UNIQUE COMMENT 'User-friendly order identifier',
  `user_id` BIGINT UNSIGNED NULL DEFAULT NULL,
  `guest_email` VARCHAR(191) NULL DEFAULT NULL,
  `shipping_address_id` BIGINT UNSIGNED NULL DEFAULT NULL,
  `billing_address_id` BIGINT UNSIGNED NULL DEFAULT NULL,
  `status` ENUM('pending_payment','paid','processing','shipped','delivered','cancelled','refunded','disputed','payment_failed','completed') NOT NULL DEFAULT 'pending_payment',
  `payment_status` VARCHAR(50) NOT NULL DEFAULT 'pending',
  `payment_intent_id` VARCHAR(191) NULL DEFAULT NULL COMMENT 'e.g., Stripe PaymentIntent ID',
  `payment_gateway` VARCHAR(50) NULL DEFAULT NULL,
  `subtotal_minor_amount` INTEGER UNSIGNED NOT NULL DEFAULT 0,
  `discount_minor_amount` INTEGER UNSIGNED NOT NULL DEFAULT 0,
  `coupon_id` BIGINT UNSIGNED NULL DEFAULT NULL,
  `coupon_code_applied` VARCHAR(50) NULL DEFAULT NULL,
  `shipping_cost_minor_amount` INTEGER UNSIGNED NOT NULL DEFAULT 0,
  `tax_minor_amount` INTEGER UNSIGNED NOT NULL DEFAULT 0,
  `total_minor_amount` INTEGER UNSIGNED NOT NULL DEFAULT 0,
  `currency_code` VARCHAR(3) NOT NULL DEFAULT 'USD',
  `order_notes` TEXT NULL DEFAULT NULL,
  `paid_at` TIMESTAMP NULL DEFAULT NULL,
  `dispute_id` VARCHAR(191) NULL DEFAULT NULL,
  `disputed_at` TIMESTAMP NULL DEFAULT NULL,
  `refund_id` VARCHAR(191) NULL DEFAULT NULL,
  `refunded_at` TIMESTAMP NULL DEFAULT NULL,
  `tracking_number` VARCHAR(100) NULL DEFAULT NULL,
  `carrier` VARCHAR(100) NULL DEFAULT NULL,
  `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT `fk_orders_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_orders_shipping_address` FOREIGN KEY (`shipping_address_id`) REFERENCES `addresses` (`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_orders_billing_address` FOREIGN KEY (`billing_address_id`) REFERENCES `addresses` (`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_orders_coupon` FOREIGN KEY (`coupon_id`) REFERENCES `coupons` (`id`) ON DELETE SET NULL,
  INDEX `idx_orders_payment_intent_id` (`payment_intent_id`),
  INDEX `idx_orders_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Table structure for table `order_items`
--
DROP TABLE IF EXISTS `order_items`;
CREATE TABLE `order_items` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `order_id` BIGINT UNSIGNED NOT NULL,
  `product_id` BIGINT UNSIGNED NOT NULL,
  `product_name_snapshot` VARCHAR(150) NOT NULL COMMENT 'Product name at time of order',
  `product_sku_snapshot` VARCHAR(100) NULL DEFAULT NULL COMMENT 'Product SKU at time of order',
  `product_options_snapshot` JSON NULL DEFAULT NULL COMMENT 'e.g., size, color at time of order',
  `quantity` INTEGER UNSIGNED NOT NULL,
  `unit_price_minor_amount` INTEGER UNSIGNED NOT NULL COMMENT 'Price per unit in minor units at time of order',
  `total_minor_amount` INTEGER UNSIGNED NOT NULL COMMENT 'Total price for this item (quantity * unit_price)',
  `currency_code` VARCHAR(3) NOT NULL DEFAULT 'USD',
  `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT `fk_order_items_order` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_order_items_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE RESTRICT -- RESTRICT to prevent product deletion if in orders
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Table structure for table `cart_items`
--
DROP TABLE IF EXISTS `cart_items`;
CREATE TABLE `cart_items` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `user_id` BIGINT UNSIGNED NULL DEFAULT NULL COMMENT 'Null for guest carts identified by session_id',
  `session_id` VARCHAR(191) NULL DEFAULT NULL COMMENT 'Used for guest carts',
  `product_id` BIGINT UNSIGNED NOT NULL,
  `quantity` INTEGER UNSIGNED NOT NULL,
  `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT `fk_cart_items_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_cart_items_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  UNIQUE KEY `uq_cart_item_user_product` (`user_id`, `product_id`),
  UNIQUE KEY `uq_cart_item_session_product` (`session_id`, `product_id`),
  INDEX `idx_cart_items_session_id` (`session_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Table structure for table `audit_log`
--
DROP TABLE IF EXISTS `audit_log`;
CREATE TABLE `audit_log` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `user_id` BIGINT UNSIGNED NULL DEFAULT NULL,
  `action` VARCHAR(100) NOT NULL,
  `auditable_type` VARCHAR(191) NULL DEFAULT NULL,
  `auditable_id` BIGINT UNSIGNED NULL DEFAULT NULL,
  `old_values` JSON NULL DEFAULT NULL,
  `new_values` JSON NULL DEFAULT NULL,
  `ip_address` VARCHAR(45) NULL DEFAULT NULL,
  `user_agent` VARCHAR(191) NULL DEFAULT NULL,
  `details` TEXT NULL DEFAULT NULL,
  `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT `fk_audit_log_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  INDEX `idx_audit_log_auditable` (`auditable_type`, `auditable_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Table structure for table `email_log`
--
DROP TABLE IF EXISTS `email_log`;
CREATE TABLE `email_log` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `user_id` BIGINT UNSIGNED NULL DEFAULT NULL,
  `to_email` VARCHAR(191) NOT NULL,
  `subject` VARCHAR(191) NULL DEFAULT NULL,
  `email_type` VARCHAR(50) NULL DEFAULT NULL COMMENT 'e.g., welcome, password_reset, order_confirmation',
  `status` ENUM('sent','failed','pending') NOT NULL DEFAULT 'pending',
  `mailer_error` TEXT NULL DEFAULT NULL,
  `sent_at` TIMESTAMP NULL DEFAULT NULL,
  `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT `fk_email_log_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  INDEX `idx_email_log_to_email_status` (`to_email`,`status`),
  INDEX `idx_email_log_email_type` (`email_type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Table structure for table `newsletter_subscribers`
--
DROP TABLE IF EXISTS `newsletter_subscribers`;
CREATE TABLE `newsletter_subscribers` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `email` VARCHAR(191) NOT NULL UNIQUE,
  `subscribed_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  `unsubscribed_at` TIMESTAMP NULL DEFAULT NULL,
  `token` VARCHAR(64) NULL DEFAULT NULL UNIQUE COMMENT 'For unsubscribe link',
  `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Table structure for table `quiz_results`
--
DROP TABLE IF EXISTS `quiz_results`;
CREATE TABLE `quiz_results` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `user_id` BIGINT UNSIGNED NULL DEFAULT NULL,
  `email` VARCHAR(191) NULL DEFAULT NULL,
  `answers` JSON NOT NULL,
  `recommendations` JSON NULL DEFAULT NULL COMMENT 'JSON array of recommended product_ids or SKUs',
  `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT `fk_quiz_results_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  INDEX `idx_quiz_results_user_timestamp` (`user_id`,`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Table structure for table `tax_rates`
--
DROP TABLE IF EXISTS `tax_rates`;
CREATE TABLE `tax_rates` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(100) NOT NULL COMMENT 'e.g., "US-CA Sales Tax", "EU VAT"',
  `country_code` CHAR(2) NOT NULL COMMENT 'ISO 3166-1 alpha-2 country code',
  `state_code` VARCHAR(10) NULL DEFAULT NULL COMMENT 'ISO 3166-2 state/province code (if applicable)',
  `postal_code_pattern` VARCHAR(50) NULL DEFAULT NULL COMMENT 'Regex pattern for postal codes, or specific code',
  `city` VARCHAR(100) NULL DEFAULT NULL,
  `rate_percentage` DECIMAL(10,4) NOT NULL COMMENT 'Tax rate percentage (e.g., 7.25 for 7.25%)',
  `is_compound` BOOLEAN NOT NULL DEFAULT FALSE COMMENT 'Is this tax compounded on other taxes?',
  `priority` INTEGER UNSIGNED NOT NULL DEFAULT 1 COMMENT 'Order in which taxes are applied',
  `is_active` BOOLEAN NOT NULL DEFAULT TRUE,
  `start_date` DATE NULL DEFAULT NULL,
  `end_date` DATE NULL DEFAULT NULL,
  `created_by_user_id` BIGINT UNSIGNED NULL DEFAULT NULL,
  `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT `fk_tax_rates_created_by_user` FOREIGN KEY (`created_by_user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  UNIQUE KEY `uq_tax_rates_country_state_postal_city` (`country_code`, `state_code`, `postal_code_pattern`, `city`), -- Ensure uniqueness for a given region definition
  INDEX `idx_tax_rates_country_code` (`country_code`),
  INDEX `idx_tax_rates_is_active` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Stores tax rates for different regions';

--
-- Table structure for table `tax_rate_history`
--
DROP TABLE IF EXISTS `tax_rate_history`;
CREATE TABLE `tax_rate_history` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `tax_rate_id` BIGINT UNSIGNED NOT NULL,
  `old_rate_percentage` DECIMAL(10,4) NULL DEFAULT NULL,
  `new_rate_percentage` DECIMAL(10,4) NOT NULL,
  `changed_by_user_id` BIGINT UNSIGNED NULL DEFAULT NULL,
  `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Timestamp of when the change was made',
  CONSTRAINT `fk_tax_rate_history_tax_rate` FOREIGN KEY (`tax_rate_id`) REFERENCES `tax_rates` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_tax_rate_history_changed_by_user` FOREIGN KEY (`changed_by_user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  INDEX `idx_tax_rate_history_tax_rate_id` (`tax_rate_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Tracks changes to tax rates';

-- Laravel Standard Tables (Password Resets, Failed Jobs, Sessions (if DB driver), Cache (if DB driver))
-- Ensure these are present or generated by Laravel's migrations if using those features.

--
-- Table structure for table `password_reset_tokens`
--
DROP TABLE IF EXISTS `password_reset_tokens`;
CREATE TABLE `password_reset_tokens` (
  `email` VARCHAR(191) NOT NULL PRIMARY KEY,
  `token` VARCHAR(191) NOT NULL,
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  INDEX `idx_password_reset_tokens_email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Table structure for table `failed_jobs`
--
DROP TABLE IF EXISTS `failed_jobs`;
CREATE TABLE `failed_jobs` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `uuid` VARCHAR(191) NOT NULL UNIQUE,
  `connection` TEXT NOT NULL,
  `queue` TEXT NOT NULL,
  `payload` LONGTEXT NOT NULL,
  `exception` LONGTEXT NOT NULL,
  `failed_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Table structure for table `sessions` (if using database session driver)
--
DROP TABLE IF EXISTS `sessions`;
CREATE TABLE `sessions` (
    `id` VARCHAR(191) NOT NULL PRIMARY KEY,
    `user_id` BIGINT UNSIGNED NULL DEFAULT NULL,
    `ip_address` VARCHAR(45) NULL DEFAULT NULL,
    `user_agent` TEXT NULL DEFAULT NULL,
    `payload` LONGTEXT NOT NULL,
    `last_activity` INTEGER NOT NULL,
    INDEX `sessions_user_id_index` (`user_id`),
    INDEX `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Table structure for table `cache` (if using database cache driver)
--
DROP TABLE IF EXISTS `cache`;
CREATE TABLE `cache` (
  `key` VARCHAR(191) NOT NULL PRIMARY KEY,
  `value` MEDIUMTEXT NOT NULL,
  `expiration` INTEGER NOT NULL,
  INDEX `idx_cache_key` (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `cache_locks`;
CREATE TABLE `cache_locks` (
  `key` VARCHAR(191) NOT NULL PRIMARY KEY,
  `owner` VARCHAR(191) NOT NULL,
  `expiration` INTEGER NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


SET FOREIGN_KEY_CHECKS=1; -- Re-enable foreign key checks

-- End of schema dump
