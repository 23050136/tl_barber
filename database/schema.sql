-- Database Schema for TL Barber Booking System

CREATE DATABASE IF NOT EXISTS tl_barber CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE tl_barber;

-- Users table
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    phone VARCHAR(20),
    password VARCHAR(255) NOT NULL,
    role ENUM('customer', 'barber', 'admin') DEFAULT 'customer',
    avatar VARCHAR(255) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Services table
CREATE TABLE IF NOT EXISTS services (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    price DECIMAL(10, 2) NOT NULL,
    duration INT NOT NULL COMMENT 'Duration in minutes',
    image VARCHAR(255) DEFAULT NULL,
    is_featured BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Barbers table (providers)
CREATE TABLE IF NOT EXISTS barbers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    specialization TEXT,
    experience_years INT DEFAULT 0,
    rating DECIMAL(3, 2) DEFAULT 0.00,
    total_reviews INT DEFAULT 0,
    is_available BOOLEAN DEFAULT TRUE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Service-Barber relationship (many-to-many)
CREATE TABLE IF NOT EXISTS service_barber (
    id INT AUTO_INCREMENT PRIMARY KEY,
    service_id INT NOT NULL,
    barber_id INT NOT NULL,
    FOREIGN KEY (service_id) REFERENCES services(id) ON DELETE CASCADE,
    FOREIGN KEY (barber_id) REFERENCES barbers(id) ON DELETE CASCADE,
    UNIQUE KEY unique_service_barber (service_id, barber_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Bookings table
CREATE TABLE IF NOT EXISTS bookings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    service_id INT NOT NULL,
    barber_id INT NOT NULL,
    booking_date DATE NOT NULL,
    booking_time TIME NOT NULL,
    status ENUM('pending', 'confirmed', 'completed', 'cancelled') DEFAULT 'pending',
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (service_id) REFERENCES services(id) ON DELETE CASCADE,
    FOREIGN KEY (barber_id) REFERENCES barbers(id) ON DELETE CASCADE,
    INDEX idx_barber_date_time (barber_id, booking_date, booking_time)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Reviews table
CREATE TABLE IF NOT EXISTS reviews (
    id INT AUTO_INCREMENT PRIMARY KEY,
    booking_id INT NOT NULL,
    user_id INT NOT NULL,
    barber_id INT NOT NULL,
    service_id INT NOT NULL,
    rating INT NOT NULL CHECK (rating >= 1 AND rating <= 5),
    comment TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (booking_id) REFERENCES bookings(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (barber_id) REFERENCES barbers(id) ON DELETE CASCADE,
    FOREIGN KEY (service_id) REFERENCES services(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Notifications table
CREATE TABLE IF NOT EXISTS notifications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    booking_id INT DEFAULT NULL,
    type VARCHAR(50) NOT NULL,
    title VARCHAR(255) NOT NULL,
    message TEXT NOT NULL,
    is_read BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (booking_id) REFERENCES bookings(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Time slots configuration
CREATE TABLE IF NOT EXISTS time_slots (
    id INT AUTO_INCREMENT PRIMARY KEY,
    start_time TIME NOT NULL,
    end_time TIME NOT NULL,
    is_active BOOLEAN DEFAULT TRUE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insert default time slots (9:00 AM to 8:00 PM, 30-minute intervals)
INSERT INTO time_slots (start_time, end_time) VALUES
('09:00:00', '09:30:00'),
('09:30:00', '10:00:00'),
('10:00:00', '10:30:00'),
('10:30:00', '11:00:00'),
('11:00:00', '11:30:00'),
('11:30:00', '12:00:00'),
('12:00:00', '12:30:00'),
('12:30:00', '13:00:00'),
('13:00:00', '13:30:00'),
('13:30:00', '14:00:00'),
('14:00:00', '14:30:00'),
('14:30:00', '15:00:00'),
('15:00:00', '15:30:00'),
('15:30:00', '16:00:00'),
('16:00:00', '16:30:00'),
('16:30:00', '17:00:00'),
('17:00:00', '17:30:00'),
('17:30:00', '18:00:00'),
('18:00:00', '18:30:00'),
('18:30:00', '19:00:00'),
('19:00:00', '19:30:00'),
('19:30:00', '20:00:00');

-- Insert sample services
INSERT INTO services (name, description, price, duration, image, is_featured) VALUES
('Cắt tóc nam tiêu chuẩn', 'Dịch vụ cắt tóc nam chuyên nghiệp với kỹ thuật hiện đại, phù hợp với mọi kiểu tóc và khuôn mặt. Barber sẽ tư vấn kiểu tóc phù hợp nhất cho bạn.', 80000, 30, 'images/services/cat-toc-nam.jpg', TRUE),
('Cắt – gội – sấy cao cấp', 'Combo dịch vụ đầy đủ: cắt tóc, gội đầu với dầu gội cao cấp và sấy tạo kiểu chuyên nghiệp. Mang lại vẻ ngoài tươi mới và tự tin.', 150000, 45, 'images/services/combo-cat-goi-say.jpg', TRUE),
('Uốn – nhuộm – tạo kiểu', 'Dịch vụ làm đẹp tóc chuyên nghiệp: uốn tóc, nhuộm màu theo yêu cầu và tạo kiểu độc đáo. Sử dụng sản phẩm chất lượng cao, an toàn cho tóc.', 500000, 120, 'images/services/uon-nhuom.jpg', FALSE),
('Cạo râu', 'Dịch vụ cạo râu truyền thống với dao cạo sắc bén và kem cạo cao cấp. Bao gồm massage mặt nhẹ nhàng để thư giãn.', 60000, 20, 'images/services/cao-rau.jpg', FALSE),
('Massage đầu', 'Massage đầu và cổ chuyên nghiệp giúp giảm căng thẳng, thư giãn và cải thiện tuần hoàn máu. Dịch vụ hoàn hảo sau một ngày làm việc mệt mỏi.', 100000, 30, 'images/services/massage-dau.jpg', FALSE),
('Combo dịch vụ', 'Gói dịch vụ đầy đủ: cắt tóc, gội đầu, sấy tạo kiểu, cạo râu và massage đầu. Tiết kiệm hơn khi đặt combo.', 350000, 90, 'images/services/combo-full.jpg', TRUE);

-- Insert sample admin user (password: admin123)
INSERT INTO users (full_name, email, phone, password, role) VALUES
('Admin TL Barber', 'admin@tlbarber.com', '0123456789', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin');

-- Insert sample barber users (password: barber123)
INSERT INTO users (full_name, email, phone, password, role) VALUES
('Nguyễn Văn An', 'barber1@tlbarber.com', '0987654321', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'barber'),
('Trần Thị Bình', 'barber2@tlbarber.com', '0987654322', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'barber'),
('Lê Văn Cường', 'barber3@tlbarber.com', '0987654323', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'barber');

-- Insert barbers
INSERT INTO barbers (user_id, specialization, experience_years, rating, total_reviews, is_available) VALUES
(2, 'Cắt tóc nam, Tạo kiểu, Fade', 5, 4.8, 120, TRUE),
(3, 'Uốn nhuộm, Tạo kiểu nữ, Ombre', 7, 4.9, 200, TRUE),
(4, 'Cắt tóc nam, Cạo râu, Massage', 4, 4.7, 95, TRUE);

-- Link services to barbers
INSERT INTO service_barber (service_id, barber_id) VALUES
(1, 1), (1, 2), (1, 3), -- Cắt tóc nam
(2, 1), (2, 3), -- Cắt gội sấy
(3, 2), -- Uốn nhuộm
(4, 1), (4, 3), -- Cạo râu
(5, 1), (5, 3), -- Massage đầu
(6, 1), (6, 2), (6, 3); -- Combo

