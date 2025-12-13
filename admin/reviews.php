<?php
$page_title = 'Quản lý đánh giá';
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/admin-auth.php';

$admin = checkAdminAuth();
$pdo = getDBConnection();
$error = '';
$success = '';

// Check if reply columns exist
try {
    $pdo->query("SELECT reply, reply_at FROM reviews LIMIT 1");
    $reply_columns_exist = true;
} catch (PDOException $e) {
    $reply_columns_exist = false;
    $error = '⚠️ Vui lòng cập nhật database để sử dụng tính năng phản hồi đánh giá. <a href="' . BASE_URL . 'add_reply_columns.php" style="color: white; text-decoration: underline; font-weight: bold;">Nhấn vào đây để cập nhật</a>';
}

// Handle reply
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['reply_review']) && $reply_columns_exist) {
    $review_id = intval($_POST['review_id'] ?? 0);
    $reply = sanitize($_POST['reply'] ?? '');
    
    if ($review_id && !empty($reply)) {
        $stmt = $pdo->prepare("
            UPDATE reviews 
            SET reply = ?, reply_at = NOW() 
            WHERE id = ?
        ");
        
        if ($stmt->execute([$reply, $review_id])) {
            // Get review info to send notification
            $stmt = $pdo->prepare("SELECT user_id, booking_id FROM reviews WHERE id = ?");
            $stmt->execute([$review_id]);
            $review = $stmt->fetch();
            
            if ($review) {
                // Create notification for user
                $stmt = $pdo->prepare("
                    INSERT INTO notifications (user_id, booking_id, type, title, message)
                    VALUES (?, ?, 'review_reply', 'Shop TL đã trả lời bình luận của bạn', ?)
                ");
                $message = "Shop TL đã trả lời bình luận của bạn. Hãy xem phản hồi trong lịch sử đặt lịch.";
                $stmt->execute([$review['user_id'], $review['booking_id'], $message]);
            }
            
            $success = 'Trả lời đánh giá thành công! Thông báo đã được gửi đến khách hàng.';
        } else {
            $error = 'Có lỗi xảy ra khi trả lời';
        }
    } else {
        $error = 'Vui lòng nhập nội dung trả lời';
    }
}

// Handle delete reply
if (isset($_GET['delete_reply']) && $reply_columns_exist) {
    $id = intval($_GET['delete_reply']);
    $stmt = $pdo->prepare("UPDATE reviews SET reply = NULL, reply_at = NULL WHERE id = ?");
    if ($stmt->execute([$id])) {
        $success = 'Xóa phản hồi thành công';
    } else {
        $error = 'Có lỗi xảy ra khi xóa phản hồi';
    }
}

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

// Get all reviews with reply info
if ($reply_columns_exist) {
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
} else {
    // Fallback query without reply columns
    $stmt = $pdo->query("
        SELECT r.*, u.full_name as customer_name, s.name as service_name,
               u2.full_name as barber_name,
               NULL as reply, NULL as reply_at
        FROM reviews r
        JOIN users u ON r.user_id = u.id
        JOIN services s ON r.service_id = s.id
        JOIN barbers bar ON r.barber_id = bar.id
        JOIN users u2 ON bar.user_id = u2.id
        ORDER BY r.created_at DESC
    ");
}
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
                        
                        <!-- Admin Reply Section -->
                        <?php if ($reply_columns_exist && !empty($review['reply'])): ?>
                            <div style="margin-top: 1rem; padding: 1rem; background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%); color: white; border-radius: 8px;">
                                <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.5rem;">
                                    <i class="fas fa-store"></i>
                                    <strong>Phản hồi của bạn:</strong>
                                    <span style="font-size: 0.85rem; opacity: 0.9; margin-left: auto;">
                                        <?php echo formatDateTime($review['reply_at']); ?>
                                    </span>
                                </div>
                                <p style="margin: 0; line-height: 1.6;">
                                    <?php echo nl2br(htmlspecialchars($review['reply'])); ?>
                                </p>
                                <div style="margin-top: 0.5rem;">
                                    <a href="<?php echo BASE_URL; ?>admin/reviews.php?delete_reply=<?php echo $review['id']; ?>" 
                                       class="btn" style="background: rgba(255,255,255,0.2); color: white; font-size: 0.85rem; padding: 5px 15px; border: none;"
                                       onclick="return confirm('Bạn có chắc muốn xóa phản hồi này?');">
                                        <i class="fas fa-trash"></i> Xóa phản hồi
                                    </a>
                                </div>
                            </div>
                        <?php elseif ($reply_columns_exist): ?>
                            <!-- Reply Form -->
                            <div style="margin-top: 1rem; padding: 1rem; background: var(--bg-light); border-radius: 8px;">
                                <form method="POST" action="" style="margin: 0;">
                                    <input type="hidden" name="review_id" value="<?php echo $review['id']; ?>">
                                    <div class="form-group" style="margin-bottom: 0.5rem;">
                                        <label style="font-weight: bold; margin-bottom: 0.5rem; display: block;">
                                            <i class="fas fa-reply"></i> Trả lời đánh giá:
                                        </label>
                                        <textarea name="reply" class="form-control" rows="3" required
                                                  placeholder="Nhập phản hồi của bạn..."></textarea>
                                    </div>
                                    <button type="submit" name="reply_review" class="btn btn-primary" style="font-size: 0.9rem; padding: 5px 15px;">
                                        <i class="fas fa-paper-plane"></i> Gửi phản hồi
                                    </button>
                                </form>
                            </div>
                        <?php endif; ?>
                        
                        <div style="margin-top: 1rem; display: flex; gap: 0.5rem;">
                            <a href="<?php echo BASE_URL; ?>admin/reviews.php?delete=<?php echo $review['id']; ?>" 
                               class="btn btn-danger" style="font-size: 0.9rem; padding: 5px 15px;"
                               onclick="return confirm('Bạn có chắc muốn xóa đánh giá này?');">
                                <i class="fas fa-trash"></i> Xóa đánh giá
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>

