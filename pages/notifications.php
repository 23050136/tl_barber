<?php
$page_title = 'Thông báo';
require_once __DIR__ . '/../includes/header.php';

if (!$current_user) {
    redirect(BASE_URL . 'pages/login.php');
}

$pdo = getDBConnection();

// Mark all as read if requested
if (isset($_GET['mark_all_read'])) {
    $stmt = $pdo->prepare("UPDATE notifications SET is_read = 1 WHERE user_id = ?");
    $stmt->execute([$current_user['id']]);
    redirect(BASE_URL . 'pages/notifications.php');
}

// Mark single notification as read
if (isset($_GET['mark_read'])) {
    $notif_id = $_GET['mark_read'];
    $stmt = $pdo->prepare("UPDATE notifications SET is_read = 1 WHERE id = ? AND user_id = ?");
    $stmt->execute([$notif_id, $current_user['id']]);
    redirect(BASE_URL . 'pages/notifications.php');
}

// Get notifications
$stmt = $pdo->prepare("
    SELECT * FROM notifications 
    WHERE user_id = ? 
    ORDER BY created_at DESC
    LIMIT 50
");
$stmt->execute([$current_user['id']]);
$notifications = $stmt->fetchAll();

// Count unread
$stmt = $pdo->prepare("SELECT COUNT(*) as count FROM notifications WHERE user_id = ? AND is_read = 0");
$stmt->execute([$current_user['id']]);
$unread_count = $stmt->fetch()['count'];
?>

<section class="section">
    <div class="container">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
            <h2 class="section-title" style="margin: 0;">Thông báo</h2>
            <?php if ($unread_count > 0): ?>
                <a href="<?php echo BASE_URL; ?>pages/notifications.php?mark_all_read=1" class="btn btn-outline">
                    <i class="fas fa-check-double"></i> Đánh dấu tất cả đã đọc
                </a>
            <?php endif; ?>
        </div>
        
        <?php if (empty($notifications)): ?>
            <div class="card" style="text-align: center; padding: 3rem;">
                <i class="fas fa-bell-slash" style="font-size: 4rem; color: var(--text-light); margin-bottom: 1rem;"></i>
                <h3 style="color: var(--text-light);">Chưa có thông báo nào</h3>
            </div>
        <?php else: ?>
            <div class="notification-list">
                <?php foreach ($notifications as $notif): ?>
                    <div class="notification-item <?php echo $notif['is_read'] ? '' : 'unread'; ?>">
                        <div style="display: flex; justify-content: space-between; align-items: start;">
                            <div style="flex: 1;">
                                <h3 style="margin-bottom: 0.5rem; color: var(--primary-color);">
                                    <?php echo htmlspecialchars($notif['title']); ?>
                                </h3>
                                <p style="color: var(--text-light); margin-bottom: 0.5rem;">
                                    <?php echo htmlspecialchars($notif['message']); ?>
                                </p>
                                <small style="color: var(--text-light);">
                                    <i class="fas fa-clock"></i> <?php echo formatDateTime($notif['created_at']); ?>
                                </small>
                            </div>
                            <?php if (!$notif['is_read']): ?>
                                <a href="<?php echo BASE_URL; ?>pages/notifications.php?mark_read=<?php echo $notif['id']; ?>" 
                                   style="margin-left: 1rem; color: var(--accent-color); text-decoration: none;"
                                   title="Đánh dấu đã đọc">
                                    <i class="fas fa-circle" style="font-size: 0.5rem;"></i>
                                </a>
                            <?php endif; ?>
                        </div>
                        <?php if ($notif['booking_id']): ?>
                            <div style="margin-top: 1rem;">
                                <a href="<?php echo BASE_URL; ?>pages/booking-history.php" 
                                   class="btn btn-outline" style="font-size: 0.9rem; padding: 5px 15px;">
                                    <i class="fas fa-eye"></i> Xem chi tiết
                                </a>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>

