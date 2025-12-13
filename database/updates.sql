barbersbarbersauto_confirmation_settings

-- Payment table (fake payment system)
ALTER TABLE bookings ADD COLUMN payment_status ENUM('pending', 'paid', 'refunded') DEFAULT 'pending';
ALTER TABLE bookings ADD COLUMN payment_method VARCHAR(50) DEFAULT NULL;
ALTER TABLE bookings ADD COLUMN payment_transaction_id VARCHAR(100) DEFAULT NULL;

-- QR Code for check-in
ALTER TABLE bookings ADD COLUMN qr_code VARCHAR(255) DEFAULT NULL;
ALTER TABLE bookings ADD COLUMN check_in_time DATETIME DEFAULT NULL;

-- Auto confirmation settings
CREATE TABLE IF NOT EXISTS auto_confirmation_settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    is_enabled BOOLEAN DEFAULT FALSE,
    auto_confirm_hours INT DEFAULT 24 COMMENT 'Auto confirm bookings X hours before',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO auto_confirmation_settings (is_enabled, auto_confirm_hours) VALUES (FALSE, 24);

-- Add reply columns to reviews table for admin responses
ALTER TABLE reviews 
ADD COLUMN IF NOT EXISTS reply TEXT DEFAULT NULL,
ADD COLUMN IF NOT EXISTS reply_at TIMESTAMP NULL DEFAULT NULL;

