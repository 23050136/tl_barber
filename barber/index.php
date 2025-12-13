<?php
$page_title = 'Barber Dashboard';
require_once __DIR__ . '/../includes/header.php';

if (!$current_user) {
    redirect(BASE_URL . 'pages/login.php');
}

if ($current_user['role'] !== 'barber') {
    redirect(BASE_URL . 'index.php');
}

$pdo = getDBConnection();

// Get barber info
$stmt = $pdo->prepare("
    SELECT b.*, u.full_name, u.email, u.phone
    FROM barbers b
    JOIN users u ON b.user_id = u.id
    WHERE b.user_id = ?
");
$stmt->execute([$current_user['id']]);
$barber = $stmt->fetch();

if (!$barber) {
    redirect(BASE_URL . 'index.php');
}

// Get filter parameters
$filter_date = $_GET['date'] ?? date('Y-m-d');
$filter_status = $_GET['status'] ?? 'all';

// Build query for bookings
$where_conditions = ["b.barber_id = ?"];
$params = [$barber['id']];

if ($filter_date) {
    $where_conditions[] = "b.booking_date = ?";
    $params[] = $filter_date;
}

if ($filter_status !== 'all') {
    $where_conditions[] = "b.status = ?";
    $params[] = $filter_status;
}

$where_clause = implode(' AND ', $where_conditions);

// Get bookings
$stmt = $pdo->prepare("
    SELECT b.*, s.name as service_name, s.price, s.duration,
           u.full_name as customer_name, u.phone as customer_phone,
           u.email as customer_email
    FROM bookings b
    JOIN services s ON b.service_id = s.id
    JOIN users u ON b.user_id = u.id
    WHERE $where_clause
    ORDER BY b.booking_date ASC, b.booking_time ASC
");
$stmt->execute($params);
$bookings = $stmt->fetchAll();

// Get statistics
$stmt = $pdo->prepare("
    SELECT 
        COUNT(*) as total_bookings,
        SUM(CASE WHEN b.status = 'pending' THEN 1 ELSE 0 END) as pending_count,
        SUM(CASE WHEN b.status = 'confirmed' THEN 1 ELSE 0 END) as confirmed_count,
        SUM(CASE WHEN b.status = 'completed' THEN 1 ELSE 0 END) as completed_count,
        SUM(CASE WHEN b.booking_date = CURDATE() THEN 1 ELSE 0 END) as today_count
    FROM bookings b
    WHERE b.barber_id = ?
");
$stmt->execute([$barber['id']]);
$stats = $stmt->fetch();
?>

<div class="section">
    <div class="container">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
            <div>
                <h2 class="section-title" style="margin: 0;">
                    <i class="fas fa-calendar-alt"></i> Lịch làm việc của tôi
                </h2>
                <p style="color: var(--text-light); margin-top: 0.5rem;">
                    Xin chào, <strong><?php echo htmlspecialchars($barber['full_name']); ?></strong>
                </p>
            </div>
            <div style="text-align: right;">
                <p style="margin: 0; color: var(--text-light);">
                    <i class="fas fa-star" style="color: var(--secondary-color);"></i> 
                    Đánh giá: <?php echo number_format($barber['rating'], 1); ?>/5.0
                </p>
                <p style="margin: 0.25rem 0 0 0; color: var(--text-light);">
                    Kinh nghiệm: <?php echo $barber['experience_years']; ?> năm
                </p>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="services-grid" style="grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); margin-bottom: 2rem; gap: 1rem;">
            <div class="card" style="background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-light) 100%); color: white;">
                <div class="card-body" style="text-align: center;">
                    <h3 style="font-size: 2.5rem; margin: 0;"><?php echo $stats['total_bookings']; ?></h3>
                    <p style="margin: 0.5rem 0 0 0; opacity: 0.9;">Tổng lịch hẹn</p>
                </div>
            </div>
            
            <div class="card" style="background: linear-gradient(135deg, var(--warning-color) 0%, #ff9800 100%); color: white;">
                <div class="card-body" style="text-align: center;">
                    <h3 style="font-size: 2.5rem; margin: 0;"><?php echo $stats['pending_count']; ?></h3>
                    <p style="margin: 0.5rem 0 0 0; opacity: 0.9;">Chờ xác nhận</p>
                </div>
            </div>
            
            <div class="card" style="background: linear-gradient(135deg, var(--accent-color) 0%, #0056b3 100%); color: white;">
                <div class="card-body" style="text-align: center;">
                    <h3 style="font-size: 2.5rem; margin: 0;"><?php echo $stats['confirmed_count']; ?></h3>
                    <p style="margin: 0.5rem 0 0 0; opacity: 0.9;">Đã xác nhận</p>
                </div>
            </div>
            
            <div class="card" style="background: linear-gradient(135deg, var(--success-color) 0%, #218838 100%); color: white;">
                <div class="card-body" style="text-align: center;">
                    <h3 style="font-size: 2.5rem; margin: 0;"><?php echo $stats['today_count']; ?></h3>
                    <p style="margin: 0.5rem 0 0 0; opacity: 0.9;">Hôm nay</p>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div class="card" style="margin-bottom: 2rem;">
            <div class="card-body">
                <form method="GET" action="" style="display: grid; grid-template-columns: 1fr 1fr auto; gap: 1rem; align-items: end;">
                    <div class="form-group" style="margin: 0;">
                        <label for="date">Chọn ngày</label>
                        <input type="date" id="date" name="date" class="form-control" 
                               value="<?php echo htmlspecialchars($filter_date); ?>">
                    </div>
                    
                    <div class="form-group" style="margin: 0;">
                        <label for="status">Trạng thái</label>
                        <select id="status" name="status" class="form-control">
                            <option value="all" <?php echo $filter_status === 'all' ? 'selected' : ''; ?>>Tất cả</option>
                            <option value="pending" <?php echo $filter_status === 'pending' ? 'selected' : ''; ?>>Chờ xác nhận</option>
                            <option value="confirmed" <?php echo $filter_status === 'confirmed' ? 'selected' : ''; ?>>Đã xác nhận</option>
                            <option value="completed" <?php echo $filter_status === 'completed' ? 'selected' : ''; ?>>Hoàn thành</option>
                            <option value="cancelled" <?php echo $filter_status === 'cancelled' ? 'selected' : ''; ?>>Đã hủy</option>
                        </select>
                    </div>
                    
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-search"></i> Lọc
                    </button>
                </form>
            </div>
        </div>

        <!-- Bookings List -->
        <div class="card">
            <div class="card-body">
                <h3 style="color: var(--primary-color); margin-bottom: 1.5rem;">
                    <i class="fas fa-list"></i> Danh sách lịch hẹn
                    <?php if ($filter_date): ?>
                        - Ngày <?php echo formatDate($filter_date); ?>
                    <?php endif; ?>
                </h3>

                <?php if (empty($bookings)): ?>
                    <div style="text-align: center; padding: 3rem; color: var(--text-light);">
                        <i class="fas fa-calendar-times" style="font-size: 4rem; margin-bottom: 1rem; opacity: 0.3;"></i>
                        <p style="font-size: 1.1rem;">Không có lịch hẹn nào</p>
                    </div>
                <?php else: ?>
                    <div class="booking-list">
                        <?php foreach ($bookings as $booking): ?>
                            <div class="booking-item" style="border: 1px solid var(--border-color); border-radius: 8px; padding: 1.5rem; margin-bottom: 1rem; background: white;">
                                <div style="display: grid; grid-template-columns: 1fr auto; gap: 1rem; align-items: start;">
                                    <div>
                                        <div style="display: flex; align-items: center; gap: 1rem; margin-bottom: 0.75rem;">
                                            <h4 style="margin: 0; color: var(--primary-color);">
                                                <i class="fas fa-clock"></i> 
                                                <?php echo formatDateTime($booking['booking_date'] . ' ' . $booking['booking_time']); ?>
                                            </h4>
                                            <span class="badge" style="
                                                background: <?php 
                                                    echo $booking['status'] === 'confirmed' ? 'var(--success-color)' : 
                                                         ($booking['status'] === 'pending' ? 'var(--warning-color)' : 
                                                         ($booking['status'] === 'completed' ? 'var(--accent-color)' : 'var(--danger-color)')); 
                                                ?>; 
                                                color: white; 
                                                padding: 4px 12px; 
                                                border-radius: 12px; 
                                                font-size: 0.85rem;
                                                text-transform: capitalize;
                                            ">
                                                <?php 
                                                    $status_text = [
                                                        'pending' => 'Chờ xác nhận',
                                                        'confirmed' => 'Đã xác nhận',
                                                        'completed' => 'Hoàn thành',
                                                        'cancelled' => 'Đã hủy'
                                                    ];
                                                    echo $status_text[$booking['status']] ?? $booking['status'];
                                                ?>
                                            </span>
                                        </div>

                                        <div style="margin-bottom: 0.5rem;">
                                            <strong><i class="fas fa-cut"></i> Dịch vụ:</strong> 
                                            <?php echo htmlspecialchars($booking['service_name']); ?>
                                        </div>

                                        <div style="margin-bottom: 0.5rem;">
                                            <strong><i class="fas fa-user"></i> Khách hàng:</strong> 
                                            <?php echo htmlspecialchars($booking['customer_name']); ?>
                                        </div>

                                        <?php if ($booking['customer_phone']): ?>
                                            <div style="margin-bottom: 0.5rem;">
                                                <strong><i class="fas fa-phone"></i> Điện thoại:</strong> 
                                                <a href="tel:<?php echo htmlspecialchars($booking['customer_phone']); ?>">
                                                    <?php echo htmlspecialchars($booking['customer_phone']); ?>
                                                </a>
                                            </div>
                                        <?php endif; ?>

                                        <div style="margin-bottom: 0.5rem;">
                                            <strong><i class="fas fa-dollar-sign"></i> Giá:</strong> 
                                            <?php echo formatPrice($booking['price']); ?>
                                        </div>

                                        <div>
                                            <strong><i class="fas fa-hourglass-half"></i> Thời lượng:</strong> 
                                            <?php echo $booking['duration']; ?> phút
                                        </div>

                                        <?php if ($booking['notes']): ?>
                                            <div style="margin-top: 0.75rem; padding: 0.75rem; background: var(--bg-light); border-radius: 5px;">
                                                <strong><i class="fas fa-sticky-note"></i> Ghi chú:</strong>
                                                <p style="margin: 0.25rem 0 0 0; color: var(--text-dark);">
                                                    <?php echo htmlspecialchars($booking['notes']); ?>
                                                </p>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>

