<?php
$page_title = 'Quản lý barber';
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/admin-auth.php';

$admin = checkAdminAuth();
$pdo = getDBConnection();
$error = '';
$success = '';

// Handle delete
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $stmt = $pdo->prepare("DELETE FROM barbers WHERE id = ?");
    if ($stmt->execute([$id])) {
        $success = 'Xóa barber thành công';
    } else {
        $error = 'Có lỗi xảy ra khi xóa';
    }
}

// Get all barbers
$stmt = $pdo->query("
    SELECT b.*, u.full_name, u.email, u.phone
    FROM barbers b
    JOIN users u ON b.user_id = u.id
    ORDER BY b.rating DESC
");
$barbers = $stmt->fetchAll();
?>

<div class="section">
    <div class="container">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
            <h1 style="color: var(--primary-color);">
                <i class="fas fa-user-tie"></i> Quản lý barber
            </h1>
            <a href="<?php echo BASE_URL; ?>admin/barber-edit.php" class="btn btn-primary">
                <i class="fas fa-plus"></i> Thêm barber mới
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
        
        <div class="services-grid">
            <?php foreach ($barbers as $barber): ?>
                <div class="card">
                    <div class="card-body">
                        <div style="display: flex; align-items: center; gap: 1rem; margin-bottom: 1rem;">
                            <div class="review-avatar" style="width: 60px; height: 60px; font-size: 1.5rem;">
                                <?php echo strtoupper(substr($barber['full_name'], 0, 1)); ?>
                            </div>
                            <div style="flex: 1;">
                                <h3 style="margin: 0; color: var(--primary-color);"><?php echo htmlspecialchars($barber['full_name']); ?></h3>
                                <p style="margin: 0.25rem 0; color: var(--text-light); font-size: 0.9rem;">
                                    <i class="fas fa-envelope"></i> <?php echo htmlspecialchars($barber['email']); ?>
                                </p>
                                <?php if ($barber['phone']): ?>
                                    <p style="margin: 0.25rem 0; color: var(--text-light); font-size: 0.9rem;">
                                        <i class="fas fa-phone"></i> <?php echo htmlspecialchars($barber['phone']); ?>
                                    </p>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <div style="margin-bottom: 1rem;">
                            <div class="review-rating" style="margin-bottom: 0.5rem;">
                                <?php
                                $rating = round($barber['rating'], 1);
                                for ($i = 1; $i <= 5; $i++) {
                                    if ($i <= floor($rating)) {
                                        echo '<i class="fas fa-star"></i>';
                                    } else {
                                        echo '<i class="far fa-star"></i>';
                                    }
                                }
                                ?>
                                <span style="margin-left: 0.5rem;"><?php echo $rating; ?> (<?php echo $barber['total_reviews']; ?> đánh giá)</span>
                            </div>
                            <?php if ($barber['specialization']): ?>
                                <p style="color: var(--text-light); font-size: 0.9rem; margin-bottom: 0.25rem;">
                                    <i class="fas fa-star"></i> <?php echo htmlspecialchars($barber['specialization']); ?>
                                </p>
                            <?php endif; ?>
                            <p style="color: var(--text-light); font-size: 0.9rem;">
                                <i class="fas fa-briefcase"></i> <?php echo $barber['experience_years']; ?> năm kinh nghiệm
                            </p>
                            <p style="color: var(--text-light); font-size: 0.9rem;">
                                Trạng thái: 
                                <?php if ($barber['is_available']): ?>
                                    <span style="color: var(--success-color);">Đang hoạt động</span>
                                <?php else: ?>
                                    <span style="color: var(--danger-color);">Tạm nghỉ</span>
                                <?php endif; ?>
                            </p>
                        </div>
                        
                        <div style="display: flex; gap: 0.5rem; margin-top: 1rem;">
                            <a href="<?php echo BASE_URL; ?>admin/barber-edit.php?id=<?php echo $barber['id']; ?>" 
                               class="btn btn-outline" style="flex: 1; font-size: 0.9rem; padding: 8px;">
                                <i class="fas fa-edit"></i> Sửa
                            </a>
                            <a href="<?php echo BASE_URL; ?>admin/barber-services.php?barber_id=<?php echo $barber['id']; ?>" 
                               class="btn btn-secondary" style="flex: 1; font-size: 0.9rem; padding: 8px;">
                                <i class="fas fa-cut"></i> Dịch vụ
                            </a>
                            <a href="<?php echo BASE_URL; ?>admin/barbers.php?delete=<?php echo $barber['id']; ?>" 
                               class="btn btn-danger" style="font-size: 0.9rem; padding: 8px;"
                               onclick="return confirm('Bạn có chắc muốn xóa barber này?');">
                                <i class="fas fa-trash"></i>
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>

