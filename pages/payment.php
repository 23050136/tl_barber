<?php
$page_title = 'Thanh toán';
require_once __DIR__ . '/../includes/header.php';

if (!$current_user) {
    redirect(BASE_URL . 'pages/login.php');
}

$booking_id = $_GET['booking_id'] ?? 0;

if (!$booking_id) {
    redirect(BASE_URL . 'pages/booking-history.php');
}

$pdo = getDBConnection();

// Get booking details
$stmt = $pdo->prepare("
    SELECT b.*, s.name as service_name, s.price
    FROM bookings b
    JOIN services s ON b.service_id = s.id
    WHERE b.id = ? AND b.user_id = ?
");
$stmt->execute([$booking_id, $current_user['id']]);
$booking = $stmt->fetch();

if (!$booking) {
    redirect(BASE_URL . 'pages/booking-history.php');
}

$has_payment_columns = true;
try {
    $pdo->query("SELECT payment_status, payment_method, payment_transaction_id FROM bookings LIMIT 1");
} catch (PDOException $e) {
    $has_payment_columns = false;
}

$error = '';
$success = '';

// Handle payment
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $payment_method = sanitize($_POST['payment_method'] ?? '');
    
    if (empty($payment_method)) {
        $error = 'Vui lòng chọn phương thức thanh toán';
    } else {
        // Generate fake transaction ID
        $transaction_id = 'TXN' . time() . rand(1000, 9999);
        
        if ($has_payment_columns) {
            $stmt = $pdo->prepare("
                UPDATE bookings 
                SET payment_status = 'paid', 
                    payment_method = ?, 
                    payment_transaction_id = ?
                WHERE id = ?
            ");
            $update_ok = $stmt->execute([$payment_method, $transaction_id, $booking_id]);
        } else {
            // Fallback nếu chưa có cột thanh toán: chỉ cập nhật trạng thái thành confirmed
            $stmt = $pdo->prepare("UPDATE bookings SET status = 'confirmed' WHERE id = ?");
            $update_ok = $stmt->execute([$booking_id]);
        }
        
        if ($update_ok) {
            // Create notification
            $stmt = $pdo->prepare("
                INSERT INTO notifications (user_id, booking_id, type, title, message)
                VALUES (?, ?, 'payment_success', 'Thanh toán thành công', ?)
            ");
            $message = "Bạn đã thanh toán thành công cho lịch đặt. Mã giao dịch: $transaction_id";
            $stmt->execute([$current_user['id'], $booking_id, $message]);
            
            $success = 'Thanh toán thành công!';
            echo "<script>setTimeout(function() { window.location.href = '" . BASE_URL . "pages/booking-history.php'; }, 2000);</script>";
        } else {
            $error = 'Có lỗi xảy ra';
        }
    }
}
?>

<section class="section">
    <div class="container">
        <div class="form-container" style="max-width: 600px;">
            <h2 class="text-center mb-2">
                <i class="fas fa-credit-card"></i> Thanh toán
            </h2>
            
            <div class="card" style="margin-bottom: 2rem; background: var(--bg-light);">
                <div class="card-body">
                    <h3 style="color: var(--primary-color); margin-bottom: 1rem;">
                        <?php echo htmlspecialchars($booking['service_name']); ?>
                    </h3>
                    <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem;">
                        <span>Giá dịch vụ:</span>
                        <strong><?php echo formatPrice($booking['price']); ?></strong>
                    </div>
                    <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem;">
                        <span>Ngày đặt:</span>
                        <strong><?php echo formatDate($booking['booking_date']); ?></strong>
                    </div>
                    <div style="display: flex; justify-content: space-between; margin-top: 1rem; padding-top: 1rem; border-top: 2px solid var(--primary-color);">
                        <span style="font-size: 1.2rem;">Tổng cộng:</span>
                        <strong style="font-size: 1.5rem; color: var(--primary-color);">
                            <?php echo formatPrice($booking['price']); ?>
                        </strong>
                    </div>
                </div>
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
            
            <?php $payment_status = $booking['payment_status'] ?? 'unpaid'; ?>
            <?php if ($payment_status === 'paid'): ?>
                <div class="alert" style="background: var(--success-color); color: white; padding: 1rem; border-radius: 5px; margin-bottom: 1rem;">
                    <i class="fas fa-check-circle"></i> Đã thanh toán
                    <?php if (!empty($booking['payment_transaction_id'] ?? '')): ?>
                        <br><small>Mã giao dịch: <?php echo htmlspecialchars($booking['payment_transaction_id']); ?></small>
                    <?php endif; ?>
                </div>
            <?php else: ?>
                <form method="POST" action="">
                    <div class="form-group">
                        <label>Phương thức thanh toán *</label>
                        <div style="display: grid; gap: 1rem; margin-top: 0.5rem;">
                            <label style="display: flex; align-items: center; gap: 1rem; padding: 1rem; border: 2px solid var(--border-color); border-radius: 5px; cursor: pointer; transition: all 0.3s;">
                                <input type="radio" name="payment_method" value="cash" required>
                                <div>
                                    <strong><i class="fas fa-money-bill-wave"></i> Thanh toán tại cửa hàng</strong>
                                    <p style="margin: 0; color: var(--text-light); font-size: 0.9rem;">Thanh toán bằng tiền mặt khi đến</p>
                                </div>
                            </label>
                            <label style="display: flex; align-items: center; gap: 1rem; padding: 1rem; border: 2px solid var(--border-color); border-radius: 5px; cursor: pointer; transition: all 0.3s;">
                                <input type="radio" name="payment_method" value="bank_transfer" required>
                                <div>
                                    <strong><i class="fas fa-university"></i> Chuyển khoản ngân hàng</strong>
                                    <p style="margin: 0; color: var(--text-light); font-size: 0.9rem;">Chuyển khoản qua ngân hàng (FAKE)</p>
                                </div>
                            </label>
                            <label style="display: flex; align-items: center; gap: 1rem; padding: 1rem; border: 2px solid var(--border-color); border-radius: 5px; cursor: pointer; transition: all 0.3s;">
                                <input type="radio" name="payment_method" value="momo" required>
                                <div>
                                    <strong><i class="fas fa-mobile-alt"></i> Ví điện tử MoMo</strong>
                                    <p style="margin: 0; color: var(--text-light); font-size: 0.9rem;">Thanh toán qua MoMo (FAKE)</p>
                                </div>
                            </label>
                        </div>
                    </div>
                    
                    <div class="alert" style="background: var(--warning-color); color: var(--text-dark); padding: 1rem; border-radius: 5px; margin-bottom: 1rem;">
                        <i class="fas fa-info-circle"></i> <strong>Lưu ý:</strong> Đây là hệ thống thanh toán giả lập (FAKE) để demo. Không có giao dịch thực tế nào được thực hiện.
                    </div>
                    
                    <button type="submit" class="btn btn-primary" style="width: 100%;">
                        <i class="fas fa-check"></i> Xác nhận thanh toán
                    </button>
                </form>
            <?php endif; ?>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>

