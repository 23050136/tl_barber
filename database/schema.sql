-- Bảng lưu trữ báo cáo tội phạm
CREATE TABLE IF NOT EXISTS crime_reports (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL COMMENT 'Họ và tên người báo cáo',
    phone VARCHAR(20) NOT NULL COMMENT 'Số điện thoại',
    email VARCHAR(255) NULL COMMENT 'Email (không bắt buộc)',
    incident_type VARCHAR(100) NOT NULL COMMENT 'Loại sự cố/tội phạm',
    location TEXT NOT NULL COMMENT 'Địa điểm xảy ra',
    incident_date DATE NOT NULL COMMENT 'Ngày xảy ra',
    incident_time TIME NOT NULL COMMENT 'Thời gian xảy ra',
    description TEXT NOT NULL COMMENT 'Mô tả chi tiết sự cố',
    evidence_file VARCHAR(500) NULL COMMENT 'Đường dẫn file đính kèm',
    priority ENUM('low', 'medium', 'high', 'urgent') DEFAULT 'medium' COMMENT 'Mức độ ưu tiên',
    status ENUM('pending', 'reviewing', 'investigating', 'resolved', 'closed') DEFAULT 'pending' COMMENT 'Trạng thái xử lý',
    user_id INT NULL COMMENT 'ID người dùng (khi có hệ thống user)',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT 'Thời gian tạo báo cáo',
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Thời gian cập nhật cuối',
    notes TEXT NULL COMMENT 'Ghi chú của cơ quan chức năng',
    assigned_officer VARCHAR(255) NULL COMMENT 'Cán bộ được phân công',
    
    INDEX idx_incident_type (incident_type),
    INDEX idx_incident_date (incident_date),
    INDEX idx_status (status),
    INDEX idx_priority (priority),
    INDEX idx_location (location(100)),
    INDEX idx_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Bảng lưu trữ báo cáo tội phạm';