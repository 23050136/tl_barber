<?php
$page_title = 'Quản lý Thống kê';
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/admin-auth.php';

$admin = checkAdminAuth();
$pdo = getDBConnection();
$error = '';
$success = '';

// Check if statistics table exists
try {
    $pdo->query("SELECT * FROM statistics LIMIT 1");
    $table_exists = true;
} catch (PDOException $e) {
    $table_exists = false;
    $error = '⚠️ Bảng statistics chưa được tạo. Vui lòng chạy file SQL: <code>database/create_statistics_table.sql</code>';
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $table_exists) {
    if (isset($_POST['update_statistics'])) {
        // Update all statistics
        $stats = $_POST['stats'] ?? [];
        
        foreach ($stats as $stat_id => $stat_data) {
            $stat_key = sanitize($stat_data['key'] ?? '');
            $stat_value = sanitize($stat_data['value'] ?? '');
            $stat_label = sanitize($stat_data['label'] ?? '');
            $stat_description = sanitize($stat_data['description'] ?? '');
            $display_order = intval($stat_data['order'] ?? 0);
            $is_active = isset($stat_data['is_active']) ? 1 : 0;
            
            $stmt = $pdo->prepare("
                UPDATE statistics 
                SET stat_value = ?, stat_label = ?, description = ?, display_order = ?, is_active = ?
                WHERE id = ?
            ");
            $stmt->execute([$stat_value, $stat_label, $stat_description, $display_order, $is_active, $stat_id]);
        }
        
        $success = 'Cập nhật thống kê thành công!';
    }
}

// Get all statistics
$statistics = [];
if ($table_exists) {
    $stmt = $pdo->query("SELECT * FROM statistics ORDER BY display_order ASC, id ASC");
    $statistics = $stmt->fetchAll();
}
?>

<div class="section">
    <div class="container">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
            <h2 class="section-title" style="margin: 0;">
                <i class="fas fa-chart-line"></i> Quản lý Thống kê / Thành tựu
            </h2>
            <a href="<?php echo BASE_URL; ?>admin/index.php" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Quay lại
            </a>
        </div>
        
        <?php if ($error): ?>
            <div class="alert" style="background: var(--danger-color); color: white; padding: 1rem; border-radius: 5px; margin-bottom: 1rem;">
                <?php echo $error; ?>
            </div>
        <?php endif; ?>
        
        <?php if ($success): ?>
            <div class="alert" style="background: var(--success-color); color: white; padding: 1rem; border-radius: 5px; margin-bottom: 1rem;">
                <i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($success); ?>
            </div>
        <?php endif; ?>
        
        <?php if ($table_exists): ?>
            <div class="card">
                <div class="card-body">
                    <p style="color: var(--text-light); margin-bottom: 2rem;">
                        <i class="fas fa-info-circle"></i> Quản lý các số liệu thống kê hiển thị trên trang "Về chúng tôi". 
                        Các giá trị này sẽ được hiển thị thay cho số liệu tự động từ database.
                    </p>
                    
                    <form method="POST" action="">
                        <div style="display: grid; gap: 1.5rem;">
                            <?php foreach ($statistics as $index => $stat): ?>
                                <div style="border: 2px solid var(--border-color); border-radius: 10px; padding: 1.5rem; background: var(--bg-light);">
                                    <div style="display: grid; grid-template-columns: 1fr 1fr 1fr 80px; gap: 1rem; align-items: start;">
                                        <!-- Key -->
                                        <div>
                                            <label style="display: block; margin-bottom: 0.5rem; font-weight: 600; color: var(--primary-color);">
                                                Key (Khóa)
                                            </label>
                                            <input type="text" 
                                                   name="stats[<?php echo $stat['id']; ?>][key]" 
                                                   value="<?php echo htmlspecialchars($stat['stat_key']); ?>" 
                                                   class="form-control" 
                                                   readonly
                                                   style="background: #e9ecef; cursor: not-allowed;">
                                        </div>
                                        
                                        <!-- Value -->
                                        <div>
                                            <label style="display: block; margin-bottom: 0.5rem; font-weight: 600; color: var(--primary-color);">
                                                Giá trị hiển thị <span style="color: red;">*</span>
                                            </label>
                                            <input type="text" 
                                                   name="stats[<?php echo $stat['id']; ?>][value]" 
                                                   value="<?php echo htmlspecialchars($stat['stat_value']); ?>" 
                                                   class="form-control" 
                                                   required
                                                   placeholder="VD: 1500+, 4.8, 5000+">
                                            <small style="color: var(--text-light); font-size: 0.85rem;">
                                                Số liệu sẽ hiển thị (có thể thêm ký tự như +, /, etc.)
                                            </small>
                                        </div>
                                        
                                        <!-- Label -->
                                        <div>
                                            <label style="display: block; margin-bottom: 0.5rem; font-weight: 600; color: var(--primary-color);">
                                                Nhãn hiển thị <span style="color: red;">*</span>
                                            </label>
                                            <input type="text" 
                                                   name="stats[<?php echo $stat['id']; ?>][label]" 
                                                   value="<?php echo htmlspecialchars($stat['stat_label']); ?>" 
                                                   class="form-control" 
                                                   required
                                                   placeholder="VD: Khách hàng tin tưởng">
                                        </div>
                                        
                                        <!-- Active Checkbox -->
                                        <div style="text-align: center; padding-top: 1.8rem;">
                                            <label style="display: flex; flex-direction: column; align-items: center; gap: 0.5rem; cursor: pointer;">
                                                <input type="checkbox" 
                                                       name="stats[<?php echo $stat['id']; ?>][is_active]" 
                                                       value="1"
                                                       <?php echo $stat['is_active'] ? 'checked' : ''; ?>>
                                                <small style="color: var(--text-light); font-size: 0.85rem;">Hiển thị</small>
                                            </label>
                                        </div>
                                    </div>
                                    
                                    <!-- Description -->
                                    <div style="margin-top: 1rem;">
                                        <label style="display: block; margin-bottom: 0.5rem; font-weight: 600; color: var(--text-dark);">
                                            Mô tả (tùy chọn)
                                        </label>
                                        <textarea name="stats[<?php echo $stat['id']; ?>][description]" 
                                                  class="form-control" 
                                                  rows="2"
                                                  placeholder="Mô tả về số liệu này..."><?php echo htmlspecialchars($stat['description'] ?? ''); ?></textarea>
                                    </div>
                                    
                                    <!-- Order (hidden) -->
                                    <input type="hidden" name="stats[<?php echo $stat['id']; ?>][order]" value="<?php echo $stat['display_order']; ?>">
                                </div>
                            <?php endforeach; ?>
                        </div>
                        
                        <div style="margin-top: 2rem; text-align: right;">
                            <button type="submit" name="update_statistics" class="btn btn-primary" style="padding: 12px 40px; font-size: 1.1rem;">
                                <i class="fas fa-save"></i> Lưu tất cả thay đổi
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            
            <!-- Preview Section -->
            <div class="card" style="margin-top: 2rem; background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);">
                <div class="card-body">
                    <h3 style="color: var(--primary-color); margin-bottom: 1rem;">
                        <i class="fas fa-eye"></i> Xem trước (Preview)
                    </h3>
                    <div class="services-grid" style="grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1.5rem;">
                        <?php foreach ($statistics as $stat): ?>
                            <?php if ($stat['is_active']): ?>
                                <div style="background: white; padding: 1.5rem; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); text-align: center;">
                                    <div style="font-size: 2.5rem; color: var(--primary-color); font-weight: 700; margin-bottom: 0.5rem;">
                                        <?php echo htmlspecialchars($stat['stat_value']); ?>
                                    </div>
                                    <div style="color: var(--text-light); font-size: 0.95rem;">
                                        <?php echo htmlspecialchars($stat['stat_label']); ?>
                                    </div>
                                </div>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>

