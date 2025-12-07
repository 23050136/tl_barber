<?php
$page_title = 'Quản lý dịch vụ';
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/admin-auth.php';

$admin = checkAdminAuth();
$pdo = getDBConnection();
$error = '';
$success = '';

// Handle delete
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $stmt = $pdo->prepare("DELETE FROM services WHERE id = ?");
    if ($stmt->execute([$id])) {
        $success = 'Xóa dịch vụ thành công';
    } else {
        $error = 'Có lỗi xảy ra khi xóa';
    }
}

// Get all services
$stmt = $pdo->query("SELECT * FROM services ORDER BY is_featured DESC, name ASC");
$services = $stmt->fetchAll();
?>

<div class="section">
    <div class="container">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
            <h1 style="color: var(--primary-color);">
                <i class="fas fa-cut"></i> Quản lý dịch vụ
            </h1>
            <a href="<?php echo BASE_URL; ?>admin/service-edit.php" class="btn btn-primary">
                <i class="fas fa-plus"></i> Thêm dịch vụ mới
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
        
        <div class="card">
            <div class="card-body">
                <table style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr style="background: var(--bg-light);">
                            <th style="padding: 1rem; text-align: left; border-bottom: 2px solid var(--border-color);">ID</th>
                            <th style="padding: 1rem; text-align: left; border-bottom: 2px solid var(--border-color);">Tên dịch vụ</th>
                            <th style="padding: 1rem; text-align: left; border-bottom: 2px solid var(--border-color);">Giá</th>
                            <th style="padding: 1rem; text-align: left; border-bottom: 2px solid var(--border-color);">Thời lượng</th>
                            <th style="padding: 1rem; text-align: left; border-bottom: 2px solid var(--border-color);">Nổi bật</th>
                            <th style="padding: 1rem; text-align: left; border-bottom: 2px solid var(--border-color);">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($services)): ?>
                            <tr>
                                <td colspan="6" style="padding: 2rem; text-align: center; color: var(--text-light);">
                                    Chưa có dịch vụ nào
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($services as $service): ?>
                                <tr style="border-bottom: 1px solid var(--border-color);">
                                    <td style="padding: 1rem;"><?php echo $service['id']; ?></td>
                                    <td style="padding: 1rem;">
                                        <strong><?php echo htmlspecialchars($service['name']); ?></strong>
                                    </td>
                                    <td style="padding: 1rem;"><?php echo formatPrice($service['price']); ?></td>
                                    <td style="padding: 1rem;"><?php echo $service['duration']; ?> phút</td>
                                    <td style="padding: 1rem;">
                                        <?php if ($service['is_featured']): ?>
                                            <span style="color: var(--secondary-color);">
                                                <i class="fas fa-star"></i> Có
                                            </span>
                                        <?php else: ?>
                                            <span style="color: var(--text-light);">Không</span>
                                        <?php endif; ?>
                                    </td>
                                    <td style="padding: 1rem;">
                                        <div style="display: flex; gap: 0.5rem;">
                                            <a href="<?php echo BASE_URL; ?>admin/service-edit.php?id=<?php echo $service['id']; ?>" 
                                               class="btn btn-outline" style="font-size: 0.9rem; padding: 5px 15px;">
                                                <i class="fas fa-edit"></i> Sửa
                                            </a>
                                            <a href="<?php echo BASE_URL; ?>admin/services.php?delete=<?php echo $service['id']; ?>" 
                                               class="btn btn-danger" style="font-size: 0.9rem; padding: 5px 15px;"
                                               onclick="return confirm('Bạn có chắc muốn xóa dịch vụ này?');">
                                                <i class="fas fa-trash"></i> Xóa
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>

