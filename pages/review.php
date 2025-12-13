<?php
$page_title = 'Đánh giá dịch vụ';
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
// Cho phép đánh giá ngay sau khi admin đã duyệt lịch (confirmed) hoặc đã hoàn thành (completed)
$stmt = $pdo->prepare("
    SELECT b.*, s.name as service_name, s.id as service_id, s.duration,
           bar.id as barber_id
    FROM bookings b
    JOIN services s ON b.service_id = s.id
    JOIN barbers bar ON b.barber_id = bar.id
    WHERE b.id = ? AND b.user_id = ?
          AND b.status IN ('confirmed', 'completed')
");
$stmt->execute([$booking_id, $current_user['id']]);
$booking = $stmt->fetch();

if (!$booking) {
    redirect(BASE_URL . 'pages/booking-history.php');
}

// Check if already reviewed
$stmt = $pdo->prepare("SELECT id FROM reviews WHERE booking_id = ?");
$stmt->execute([$booking_id]);
if ($stmt->fetch()) {
    redirect(BASE_URL . 'pages/booking-history.php');
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $rating = intval($_POST['rating'] ?? 0);
    $comment = sanitize($_POST['comment'] ?? '');
    
    if ($rating < 1 || $rating > 5) {
        $error = 'Vui lòng chọn điểm đánh giá';
    } elseif (empty($comment)) {
        $error = 'Vui lòng nhập nhận xét';
    } else {
        $stmt = $pdo->prepare("
            INSERT INTO reviews (booking_id, user_id, barber_id, service_id, rating, comment)
            VALUES (?, ?, ?, ?, ?, ?)
        ");
        
        if ($stmt->execute([$booking_id, $current_user['id'], $booking['barber_id'], $booking['service_id'], $rating, $comment])) {
            // Update barber rating
            $stmt = $pdo->prepare("
                UPDATE barbers 
                SET rating = (SELECT AVG(rating) FROM reviews WHERE barber_id = ?),
                    total_reviews = (SELECT COUNT(*) FROM reviews WHERE barber_id = ?)
                WHERE id = ?
            ");
            $stmt->execute([$booking['barber_id'], $booking['barber_id'], $booking['barber_id']]);
            
            $success = 'Cảm ơn bạn đã đánh giá!';
            echo "<script>setTimeout(function() { window.location.href = '" . BASE_URL . "pages/booking-history.php'; }, 2000);</script>";
        } else {
            $error = 'Có lỗi xảy ra, vui lòng thử lại';
        }
    }
}
?>

<section class="section">
    <div class="container">
        <div class="form-container" style="max-width: 600px;">
            <h2 class="text-center mb-2"><i class="fas fa-star"></i> Đánh giá dịch vụ</h2>
            
            <div class="card" style="margin-bottom: 2rem; background: var(--bg-light);">
                <div class="card-body">
                    <h3 style="color: var(--primary-color); margin-bottom: 1rem;">
                        <?php echo htmlspecialchars($booking['service_name']); ?>
                    </h3>
                    <p style="color: var(--text-light);">
                        <i class="fas fa-calendar"></i> <?php echo formatDate($booking['booking_date']); ?> 
                        lúc <?php echo date('H:i', strtotime($booking['booking_time'])); ?>
                    </p>
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
            
            <form method="POST" action="">
                <div class="form-group">
                    <label>Đánh giá *</label>
                    <div style="display: flex; gap: 0.5rem; justify-content: center; margin: 1rem 0;">
                        <?php for ($i = 1; $i <= 5; $i++): ?>
                            <label style="cursor: pointer; font-size: 2rem; color: var(--text-light);">
                                <input type="radio" name="rating" value="<?php echo $i; ?>" required style="display: none;" 
                                       onchange="setRatingStars(<?php echo $i; ?>)">
                                <i class="far fa-star rating-star" id="star-<?php echo $i; ?>"></i>
                            </label>
                        <?php endfor; ?>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="comment"><i class="fas fa-comment"></i> Nhận xét *</label>
                    <textarea id="comment" name="comment" class="form-control" rows="5" required
                              placeholder="Chia sẻ trải nghiệm của bạn về dịch vụ..."><?php echo htmlspecialchars($_POST['comment'] ?? ''); ?></textarea>
                </div>
                
                <button type="submit" class="btn btn-primary" style="width: 100%;">
                    <i class="fas fa-paper-plane"></i> Gửi đánh giá
                </button>
            </form>
        </div>
    </div>
</section>

<script>
function setRatingStars(rating) {
    for (let i = 1; i <= 5; i++) {
        const star = document.getElementById('star-' + i);
        if (i <= rating) {
            star.classList.remove('far');
            star.classList.add('fas');
            star.style.color = 'var(--secondary-color)';
        } else {
            star.classList.remove('fas');
            star.classList.add('far');
            star.style.color = 'var(--text-light)';
        }
    }
}

// Handle click on stars
document.querySelectorAll('.rating-star').forEach((star, index) => {
    star.addEventListener('click', function() {
        const rating = index + 1;
        document.querySelector(`input[value="${rating}"]`).checked = true;
        setRatingStars(rating);
    });
});
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>

