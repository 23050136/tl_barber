<?php
$page_title = 'Quản lý đánh giá';
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/admin-auth.php';

$admin = checkAdminAuth();
$pdo = getDBConnection();
$error = '';
$success = '';

// Handle delete
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    
    // Get review info to update barber rating
    $stmt = $pdo->prepare("SELECT barber_id FROM reviews WHERE id = ?");
    $stmt->execute([$id]);
    $review = $stmt->fetch();
    
    if ($review) {
        $barber_id = $review['barber_id'];
        
        // Delete review
        $stmt = $pdo->prepare("DELETE FROM reviews WHERE id = ?");
        if ($stmt->execute([$id])) {
            // Recalculate barber rating
            $stmt = $pdo->prepare("
                UPDATE barbers 
                SET rating = COALESCE((SELECT AVG(rating) FROM reviews WHERE barber_id = ?), 0),
                    total_reviews = (SELECT COUNT(*) FROM reviews WHERE barber_id = ?)
                WHERE id = ?
            ");
            $stmt->execute([$barber_id, $barber_id, $barber_id]);
            
            $success = 'Xóa đánh giá thành công';
        } else {
            $error = 'Có lỗi xảy ra khi xóa';
        }
    } else {
        $error = 'Không tìm thấy đánh giá';
    }
}

// Get all reviews
$stmt = $pdo->query("
    SELECT r.*, u.full_name as customer_name, s.name as service_name,
           u2.full_name as barber_name
    FROM reviews r
    JOIN users u ON r.user_id = u.id
    JOIN services s ON r.service_id = s.id
    JOIN barbers bar ON r.barber_id = bar.id
    JOIN users u2 ON bar.user_id = u2.id
    ORDER BY r.created_at DESC
");
$reviews = $stmt->fetchAll();
?>

<div class="section">
    <div class="container">
        <h1 style="color: var(--primary-color); margin-bottom: 2rem;">
            <i class="fas fa-star"></i> Quản lý đánh giá
        </h1>
        
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
        
        <div class="reviews-grid">
            <?php if (empty($reviews)): ?>
                <div class="card" style="text-align: center; padding: 3rem; grid-column: 1 / -1;">
                    <i class="fas fa-star" style="font-size: 4rem; color: var(--text-light); margin-bottom: 1rem;"></i>
                    <h3 style="color: var(--text-light);">Chưa có đánh giá nào</h3>
                </div>
            <?php else: ?>
                <?php foreach ($reviews as $review): ?>
                    <div class="review-card">
                        <div class="review-header">
                            <div class="review-avatar">
                                <?php echo strtoupper(substr($review['customer_name'], 0, 1)); ?>
                            </div>
                            <div style="flex: 1;">
                                <strong><?php echo htmlspecialchars($review['customer_name']); ?></strong>
                                <div class="review-rating">
                                    <?php
                                    for ($i = 1; $i <= 5; $i++) {
                                        if ($i <= $review['rating']) {
                                            echo '<i class="fas fa-star"></i>';
                                        } else {
                                            echo '<i class="far fa-star"></i>';
                                        }
                                    }
                                    ?>
                                </div>
                            </div>
                        </div>
                        <p class="review-text">"<?php echo htmlspecialchars($review['comment']); ?>"</p>
                        <div style="margin-top: 1rem; padding-top: 1rem; border-top: 1px solid var(--border-color);">
                            <p style="color: var(--text-light); font-size: 0.9rem; margin-bottom: 0.25rem;">
                                <i class="fas fa-cut"></i> Dịch vụ: <?php echo htmlspecialchars($review['service_name']); ?>
                            </p>
                            <p style="color: var(--text-light); font-size: 0.9rem; margin-bottom: 0.25rem;">
                                <i class="fas fa-user-tie"></i> Barber: <?php echo htmlspecialchars($review['barber_name']); ?>
                            </p>
                            <p style="color: var(--text-light); font-size: 0.85rem;">
                                <i class="fas fa-clock"></i> <?php echo formatDateTime($review['created_at']); ?>
                            </p>
                        </div>
                        <div style="margin-top: 1rem;">
                            <a href="<?php echo BASE_URL; ?>admin/reviews.php?delete=<?php echo $review['id']; ?>" 
                               class="btn btn-danger" style="font-size: 0.9rem; padding: 5px 15px;"
                               onclick="return confirm('Bạn có chắc muốn xóa đánh giá này?');">
                                <i class="fas fa-trash"></i> Xóa
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>

