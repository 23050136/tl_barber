<?php
$page_title = 'Cài đặt hệ thống';
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/admin-auth.php';

$admin = checkAdminAuth();
$pdo = getDBConnection();
$error = '';
$success = '';

// Check if table exists, create if not
try {
    $pdo->query("SELECT * FROM auto_confirmation_settings LIMIT 1");
    $table_exists = true;
} catch (PDOException $e) {
    $table_exists = false;
    // Create table if it doesn't exist
    try {
        $pdo->exec("
            CREATE TABLE IF NOT EXISTS auto_confirmation_settings (
                id INT AUTO_INCREMENT PRIMARY KEY,
                is_enabled BOOLEAN DEFAULT FALSE,
                auto_confirm_hours INT DEFAULT 24 COMMENT 'Auto confirm bookings X hours before',
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
        ");
        $table_exists = true;
    } catch (PDOException $e2) {
        $error = 'Không thể tạo bảng auto_confirmation_settings: ' . $e2->getMessage();
    }
}

// Get settings
$settings = null;
if ($table_exists) {
    try {
        $stmt = $pdo->query("SELECT * FROM auto_confirmation_settings LIMIT 1");
        $settings = $stmt->fetch();
        
        if (!$settings) {
            // Insert default settings
            $stmt = $pdo->prepare("INSERT INTO auto_confirmation_settings (is_enabled, auto_confirm_hours) VALUES (FALSE, ?)");
            $stmt->execute([24]);
            $stmt = $pdo->query("SELECT * FROM auto_confirmation_settings LIMIT 1");
            $settings = $stmt->fetch();
        }
    } catch (PDOException $e) {
        $error = 'Lỗi khi truy vấn cài đặt: ' . $e->getMessage();
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $is_enabled = isset($_POST['is_enabled']) ? 1 : 0;
    $auto_confirm_hours = intval($_POST['auto_confirm_hours'] ?? 24);
    
    $stmt = $pdo->prepare("
        UPDATE auto_confirmation_settings 
        SET is_enabled = ?, auto_confirm_hours = ?
        WHERE id = ?
    ");
    if ($stmt->execute([$is_enabled, $auto_confirm_hours, $settings['id']])) {
        $success = 'Cập nhật cài đặt thành công';
        $settings['is_enabled'] = $is_enabled;
        $settings['auto_confirm_hours'] = $auto_confirm_hours;
    } else {
        $error = 'Có lỗi xảy ra';
    }
}
?>

<div class="section">
    <div class="container">
        <div class="form-container" style="max-width: 600px;">
            <h2 class="text-center mb-2">
                <i class="fas fa-cog"></i> Cài đặt hệ thống
            </h2>
            
            <div style="text-align: center; margin-bottom: 1rem;">
                <a href="<?php echo BASE_URL; ?>admin/index.php" style="color: var(--primary-color); text-decoration: none;">
                    <i class="fas fa-arrow-left"></i> Quay lại dashboard
                </a>
            </div>
            
            <?php if ($error): ?>
                <div class="alert" style="background: var(--danger-color); color: white; padding: 1rem; border-radius: 5px; margin-bottom: 1rem;">
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>
            
            <?php if ($success): ?>
                <div class="alert" style="background: var(--success-color); color: white; padding: 1rem; border-radius: 5px; margin-bottom: 1rem;">
                    <?php echo htmlspecialchars($success); ?>
                </div>
            <?php endif; ?>
            
            <?php if (!$table_exists || !$settings): ?>
                <div class="alert" style="background: var(--warning-color); color: white; padding: 1rem; border-radius: 5px; margin-bottom: 1rem;">
                    <i class="fas fa-exclamation-triangle"></i> Bảng cài đặt chưa được tạo. 
                    <br><br>
                    <a href="<?php echo BASE_URL; ?>create-auto-confirmation-table.php" style="color: white; text-decoration: underline; font-weight: bold;">
                        Nhấn vào đây để tạo bảng
                    </a>
                    hoặc chạy file SQL: <code>database/create_auto_confirmation_table.sql</code>
                </div>
            <?php else: ?>
                <form method="POST" action="">
                    <div class="card" style="margin-bottom: 1.5rem;">
                        <div class="card-body">
                            <h3 style="color: var(--primary-color); margin-bottom: 1rem;">
                                <i class="fas fa-robot"></i> Tự động xác nhận lịch đặt
                            </h3>
                            <div class="form-group">
                                <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer;">
                                    <input type="checkbox" name="is_enabled" value="1" 
                                           <?php echo $settings['is_enabled'] ? 'checked' : ''; ?>>
                                    <span>Bật tự động xác nhận</span>
                                </label>
                            </div>
                            <div class="form-group">
                                <label for="auto_confirm_hours">Tự động xác nhận trước (giờ)</label>
                                <input type="number" id="auto_confirm_hours" name="auto_confirm_hours" 
                                       class="form-control" min="1" max="168"
                                       value="<?php echo htmlspecialchars($settings['auto_confirm_hours'] ?? 24); ?>">
                                <small style="color: var(--text-light);">
                                    Hệ thống sẽ tự động xác nhận lịch đặt trước X giờ so với thời gian đặt lịch
                                </small>
                            </div>
                        </div>
                    </div>
                    
                    <button type="submit" class="btn btn-primary" style="width: 100%;">
                        <i class="fas fa-save"></i> Lưu cài đặt
                    </button>
                </form>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>

