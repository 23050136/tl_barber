<?php
$page_title = 'Cài đặt hệ thống';
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/admin-auth.php';

$admin = checkAdminAuth();
$pdo = getDBConnection();
$error = '';
$success = '';

// Get settings
$stmt = $pdo->query("SELECT * FROM auto_confirmation_settings LIMIT 1");
$settings = $stmt->fetch();

if (!$settings) {
    // Create default settings
    $pdo->exec("INSERT INTO auto_confirmation_settings (is_enabled, auto_confirm_hours) VALUES (FALSE, 24)");
    $stmt = $pdo->query("SELECT * FROM auto_confirmation_settings LIMIT 1");
    $settings = $stmt->fetch();
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
                                   value="<?php echo $settings['auto_confirm_hours']; ?>">
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
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>

