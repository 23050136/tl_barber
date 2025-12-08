<?php
$page_title = 'Quản lý đặt lịch';
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/admin-auth.php';

$admin = checkAdminAuth();
$pdo = getDBConnection();
$error = '';
$success = '';

// Handle approve/reject
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $booking_id = intval($_POST['booking_id'] ?? 0);
    $action = $_POST['action'] ?? '';
    
    if ($booking_id && $action) {
        if ($action === 'approve') {
            $stmt = $pdo->prepare("UPDATE bookings SET status = 'confirmed' WHERE id = ?");
            if ($stmt->execute([$booking_id])) {
<<<<<<< HEAD
                // Generate QR code
                $qr_code = 'BOOKING-' . $booking_id . '-' . time();
                $stmt = $pdo->prepare("UPDATE bookings SET qr_code = ? WHERE id = ?");
                $stmt->execute([$qr_code, $booking_id]);
=======
                // Generate QR code if column exists
                try {
                    $pdo->query("SELECT qr_code FROM bookings LIMIT 1");
                    $qr_code = 'BOOKING-' . $booking_id . '-' . time();
                    $stmt = $pdo->prepare("UPDATE bookings SET qr_code = ? WHERE id = ?");
                    $stmt->execute([$qr_code, $booking_id]);
                } catch (PDOException $e) {
                    // Column doesn't exist, skip QR generation
                }
>>>>>>> e906b55 (update code)
                
                // Create notification
                $stmt = $pdo->prepare("SELECT user_id FROM bookings WHERE id = ?");
                $stmt->execute([$booking_id]);
                $booking = $stmt->fetch();
                
                $stmt = $pdo->prepare("
                    INSERT INTO notifications (user_id, booking_id, type, title, message)
                    VALUES (?, ?, 'booking_confirmed', 'Lịch đặt đã được xác nhận', ?)
                ");
                $message = "Lịch đặt của bạn đã được xác nhận. Vui lòng đến đúng giờ!";
                $stmt->execute([$booking['user_id'], $booking_id, $message]);
                
                $success = 'Xác nhận lịch đặt thành công';
            } else {
                $error = 'Có lỗi xảy ra';
            }
        } elseif ($action === 'reject') {
            $stmt = $pdo->prepare("UPDATE bookings SET status = 'cancelled' WHERE id = ?");
            if ($stmt->execute([$booking_id])) {
                // Create notification
                $stmt = $pdo->prepare("SELECT user_id FROM bookings WHERE id = ?");
                $stmt->execute([$booking_id]);
                $booking = $stmt->fetch();
                
                $stmt = $pdo->prepare("
                    INSERT INTO notifications (user_id, booking_id, type, title, message)
                    VALUES (?, ?, 'booking_rejected', 'Lịch đặt bị từ chối', ?)
                ");
                $message = "Rất tiếc, lịch đặt của bạn đã bị từ chối. Vui lòng đặt lịch khác.";
                $stmt->execute([$booking['user_id'], $booking_id, $message]);
                
                $success = 'Từ chối lịch đặt thành công';
            } else {
                $error = 'Có lỗi xảy ra';
            }
        }
    }
}

// Filter by date
$filter_date = $_GET['date'] ?? '';
$where_clause = "1=1";
$params = [];
if ($filter_date) {
    $where_clause = "b.booking_date = ?";
    $params[] = $filter_date;
}

// Get bookings
$stmt = $pdo->prepare("
    SELECT b.*, s.name as service_name, s.price,
           u.full_name as customer_name, u.email as customer_email, u.phone as customer_phone,
           u2.full_name as barber_name
    FROM bookings b
    JOIN services s ON b.service_id = s.id
    JOIN users u ON b.user_id = u.id
    JOIN barbers bar ON b.barber_id = bar.id
    JOIN users u2 ON bar.user_id = u2.id
    WHERE $where_clause
    ORDER BY b.booking_date DESC, b.booking_time DESC
");
$stmt->execute($params);
$bookings = $stmt->fetchAll();

// Generate QR codes for confirmed bookings that don't have one
// First check if qr_code column exists
try {
    $pdo->query("SELECT qr_code FROM bookings LIMIT 1");
    $qr_column_exists = true;
} catch (PDOException $e) {
    $qr_column_exists = false;
}

if ($qr_column_exists) {
    foreach ($bookings as $booking) {
        if ($booking['status'] === 'confirmed' && empty($booking['qr_code'] ?? '')) {
            $qr_code = 'BOOKING-' . $booking['id'] . '-' . time();
            $stmt = $pdo->prepare("UPDATE bookings SET qr_code = ? WHERE id = ?");
            $stmt->execute([$qr_code, $booking['id']]);
        }
    }
}
?>

<div class="section">
    <div class="container">
        <h1 style="color: var(--primary-color); margin-bottom: 2rem;">
            <i class="fas fa-calendar"></i> Quản lý đặt lịch
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
        
        <!-- Filter -->
        <div class="card" style="margin-bottom: 2rem;">
            <div class="card-body">
                <form method="GET" action="" style="display: flex; gap: 1rem; align-items: end;">
                    <div class="form-group" style="flex: 1; margin: 0;">
                        <label for="date">Lọc theo ngày</label>
                        <input type="date" id="date" name="date" class="form-control"
                               value="<?php echo htmlspecialchars($filter_date); ?>">
                    </div>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-filter"></i> Lọc
                    </button>
                    <?php if ($filter_date): ?>
                        <a href="<?php echo BASE_URL; ?>admin/bookings.php" class="btn btn-outline">
                            <i class="fas fa-times"></i> Xóa lọc
                        </a>
                    <?php endif; ?>
                </form>
            </div>
        </div>
        
        <!-- Bookings List -->
        <div class="booking-list">
            <?php if (empty($bookings)): ?>
                <div class="card" style="text-align: center; padding: 3rem;">
                    <i class="fas fa-calendar-times" style="font-size: 4rem; color: var(--text-light); margin-bottom: 1rem;"></i>
                    <h3 style="color: var(--text-light);">Chưa có đặt lịch nào</h3>
                </div>
            <?php else: ?>
                <?php foreach ($bookings as $booking): ?>
                    <div class="booking-item">
                        <div class="booking-info" style="flex: 1;">
                            <h3 style="color: var(--primary-color); margin-bottom: 0.5rem;">
                                <?php echo htmlspecialchars($booking['service_name']); ?>
                            </h3>
                            <p style="margin-bottom: 0.25rem;">
                                <i class="fas fa-user"></i> Khách: <strong><?php echo htmlspecialchars($booking['customer_name']); ?></strong>
                                (<?php echo htmlspecialchars($booking['customer_email']); ?>)
                            </p>
                            <?php if ($booking['customer_phone']): ?>
                                <p style="margin-bottom: 0.25rem;">
                                    <i class="fas fa-phone"></i> <?php echo htmlspecialchars($booking['customer_phone']); ?>
                                </p>
                            <?php endif; ?>
                            <p style="margin-bottom: 0.25rem;">
                                <i class="fas fa-user-tie"></i> Barber: <strong><?php echo htmlspecialchars($booking['barber_name']); ?></strong>
                            </p>
                            <p style="margin-bottom: 0.25rem;">
                                <i class="fas fa-calendar"></i> <?php echo formatDate($booking['booking_date']); ?> 
                                lúc <?php echo date('H:i', strtotime($booking['booking_time'])); ?>
                            </p>
                            <p style="margin-bottom: 0.25rem;">
                                <i class="fas fa-money-bill-wave"></i> Giá: <strong><?php echo formatPrice($booking['price']); ?></strong>
                            </p>
                            <?php if ($booking['notes']): ?>
                                <p style="color: var(--text-light); font-size: 0.9rem; margin-top: 0.5rem;">
                                    <i class="fas fa-sticky-note"></i> <?php echo htmlspecialchars($booking['notes']); ?>
                                </p>
                            <?php endif; ?>
                            <p style="color: var(--text-light); font-size: 0.85rem; margin-top: 0.5rem;">
                                Đặt lúc: <?php echo formatDateTime($booking['created_at']); ?>
                            </p>
                        </div>
                        <div style="display: flex; flex-direction: column; gap: 0.5rem; align-items: flex-end;">
                            <span class="booking-status status-<?php echo $booking['status']; ?>">
                                <?php
                                $status_text = [
                                    'pending' => 'Chờ xác nhận',
                                    'confirmed' => 'Đã xác nhận',
                                    'completed' => 'Hoàn thành',
                                    'cancelled' => 'Đã hủy'
                                ];
                                echo $status_text[$booking['status']];
                                ?>
                            </span>
                            
                            <?php if ($booking['status'] === 'pending'): ?>
                                <form method="POST" style="display: inline;">
                                    <input type="hidden" name="booking_id" value="<?php echo $booking['id']; ?>">
                                    <input type="hidden" name="action" value="approve">
                                    <button type="submit" class="btn btn-success" style="font-size: 0.9rem; padding: 8px 15px;">
                                        <i class="fas fa-check"></i> Duyệt
                                    </button>
                                </form>
                                <form method="POST" style="display: inline;">
                                    <input type="hidden" name="booking_id" value="<?php echo $booking['id']; ?>">
                                    <input type="hidden" name="action" value="reject">
                                    <button type="submit" class="btn btn-danger" style="font-size: 0.9rem; padding: 8px 15px;"
                                            onclick="return confirm('Bạn có chắc muốn từ chối lịch đặt này?');">
                                        <i class="fas fa-times"></i> Từ chối
                                    </button>
                                </form>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>

