<?php
$page_title = 'Về chúng tôi';
require_once __DIR__ . '/../includes/header.php';

$pdo = getDBConnection();

$stmt = $pdo->query("
    SELECT b.*, u.full_name, u.email, u.phone
    FROM barbers b
    JOIN users u ON b.user_id = u.id
    WHERE b.is_available = 1
    ORDER BY b.id DESC
    LIMIT 6
");
$barbers = $stmt->fetchAll();


// Get statistics
$stmt = $pdo->query("SELECT COUNT(*) as count FROM bookings WHERE status = 'completed'");
$total_bookings = $stmt->fetch()['count'];

$stmt = $pdo->query("SELECT COUNT(*) as count FROM users WHERE role = 'customer'");
$total_customers = $stmt->fetch()['count'];

$stmt = $pdo->query("SELECT AVG(rating) as avg_rating FROM reviews");
$avg_rating = round($stmt->fetch()['avg_rating'], 1);
?>

<!-- Hero Section -->
<section class="hero" style="background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-light) 100%);">
    <div class="container">
        <h1 style="color: white; font-size: 3rem; margin-bottom: 1rem;">
            <i class="fas fa-cut"></i> Về TL Barber
        </h1>
        <p style="color: rgba(255, 255, 255, 0.9); font-size: 1.25rem; max-width: 800px;">
            Hơn 10 năm kinh nghiệm trong ngành cắt tóc và làm đẹp, chúng tôi tự hào mang đến cho khách hàng những dịch vụ chuyên nghiệp và phong cách độc đáo
        </p>
    </div>
</section>

<!-- About Section -->
<section class="section">
    <div class="container">
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 4rem; align-items: center; margin-bottom: 4rem;">
            <div>
                <h2 class="section-title" style="text-align: left; margin-bottom: 1.5rem;">
                    Câu chuyện của chúng tôi
                </h2>
                <p style="margin-bottom: 1rem; color: var(--text-light); line-height: 1.8;">
                    TL Barber được thành lập với tầm nhìn trở thành địa chỉ hàng đầu về dịch vụ cắt tóc và làm đẹp nam giới tại Việt Nam. Với hơn 10 năm kinh nghiệm, chúng tôi đã phục vụ hàng nghìn khách hàng và nhận được sự tin tưởng, yêu mến từ cộng đồng.
                </p>
                <p style="margin-bottom: 1rem; color: var(--text-light); line-height: 1.8;">
                    Chúng tôi không chỉ đơn thuần là một tiệm cắt tóc, mà còn là nơi khách hàng có thể thư giãn, tận hưởng không gian sang trọng và nhận được sự chăm sóc tận tâm từ đội ngũ barber chuyên nghiệp.
                </p>
                <p style="color: var(--text-light); line-height: 1.8;">
                    Mỗi kiểu tóc, mỗi dịch vụ đều được thực hiện với sự tỉ mỉ, chuyên nghiệp và đam mê nghề nghiệp. Chúng tôi cam kết mang đến cho bạn trải nghiệm tuyệt vời nhất.
                </p>
            </div>
            <div style="text-align: center;">
                <div style="background: var(--primary-color); color: white; padding: 3rem; border-radius: 10px; box-shadow: var(--shadow-lg);">
                    <i class="fas fa-award" style="font-size: 4rem; color: var(--secondary-color); margin-bottom: 1rem;"></i>
                    <h3 style="font-size: 2rem; margin-bottom: 0.5rem;">Hơn 10 năm</h3>
                    <p style="font-size: 1.1rem; opacity: 0.9;">Kinh nghiệm phục vụ</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Statistics Section -->
<section class="section" style="background: var(--bg-white);">
    <div class="container">
        <h2 class="section-title">Thành tựu của chúng tôi</h2>
        <div class="services-grid" style="grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); margin-top: 2rem;">
            <div class="card" style="text-align: center; padding: 2rem;">
                <i class="fas fa-users" style="font-size: 3rem; color: var(--primary-color); margin-bottom: 1rem;"></i>
                <h3 style="font-size: 2.5rem; color: var(--primary-color); margin-bottom: 0.5rem;">
                    <?php echo number_format($total_customers); ?>+
                </h3>
                <p style="color: var(--text-light);">Khách hàng tin tưởng</p>
            </div>
            
            <div class="card" style="text-align: center; padding: 2rem;">
                <i class="fas fa-calendar-check" style="font-size: 3rem; color: var(--primary-color); margin-bottom: 1rem;"></i>
                <h3 style="font-size: 2.5rem; color: var(--primary-color); margin-bottom: 0.5rem;">
                    <?php echo number_format($total_bookings); ?>+
                </h3>
                <p style="color: var(--text-light);">Lượt đặt lịch</p>
            </div>
            
            <div class="card" style="text-align: center; padding: 2rem;">
                <i class="fas fa-star" style="font-size: 3rem; color: var(--secondary-color); margin-bottom: 1rem;"></i>
                <h3 style="font-size: 2.5rem; color: var(--primary-color); margin-bottom: 0.5rem;">
                    <?php echo $avg_rating; ?>/5
                </h3>
                <p style="color: var(--text-light);">Đánh giá trung bình</p>
            </div>
            
            <div class="card" style="text-align: center; padding: 2rem;">
                <i class="fas fa-user-tie" style="font-size: 3rem; color: var(--primary-color); margin-bottom: 1rem;"></i>
                <h3 style="font-size: 2.5rem; color: var(--primary-color); margin-bottom: 0.5rem;">
                    <?php echo count($barbers); ?>+
                </h3>
                <p style="color: var(--text-light);">Barber chuyên nghiệp</p>
            </div>
        </div>
    </div>
</section>

<!-- Values Section -->
<section class="section">
    <div class="container">
        <h2 class="section-title">Giá trị cốt lõi</h2>
        <div class="services-grid" style="grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); margin-top: 2rem;">
            <div class="card">
                <div class="card-body">
                    <div style="display: flex; align-items: start; gap: 1.5rem;">
                        <div style="background: var(--primary-color); color: white; width: 60px; height: 60px; border-radius: 50%; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                            <i class="fas fa-heart" style="font-size: 1.5rem;"></i>
                        </div>
                        <div>
                            <h3 style="color: var(--primary-color); margin-bottom: 0.5rem;">Tận tâm</h3>
                            <p style="color: var(--text-light); line-height: 1.8;">
                                Mỗi khách hàng đều được chăm sóc với sự tận tâm và chu đáo. Chúng tôi lắng nghe nhu cầu của bạn để mang đến dịch vụ phù hợp nhất.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="card">
                <div class="card-body">
                    <div style="display: flex; align-items: start; gap: 1.5rem;">
                        <div style="background: var(--primary-color); color: white; width: 60px; height: 60px; border-radius: 50%; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                            <i class="fas fa-gem" style="font-size: 1.5rem;"></i>
                        </div>
                        <div>
                            <h3 style="color: var(--primary-color); margin-bottom: 0.5rem;">Chất lượng</h3>
                            <p style="color: var(--text-light); line-height: 1.8;">
                                Sử dụng các sản phẩm cao cấp và kỹ thuật chuyên nghiệp để đảm bảo chất lượng dịch vụ tốt nhất cho khách hàng.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="card">
                <div class="card-body">
                    <div style="display: flex; align-items: start; gap: 1.5rem;">
                        <div style="background: var(--primary-color); color: white; width: 60px; height: 60px; border-radius: 50%; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                            <i class="fas fa-lightbulb" style="font-size: 1.5rem;"></i>
                        </div>
                        <div>
                            <h3 style="color: var(--primary-color); margin-bottom: 0.5rem;">Sáng tạo</h3>
                            <p style="color: var(--text-light); line-height: 1.8;">
                                Luôn cập nhật xu hướng mới nhất và sáng tạo những kiểu tóc độc đáo, phù hợp với từng cá nhân.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="card">
                <div class="card-body">
                    <div style="display: flex; align-items: start; gap: 1.5rem;">
                        <div style="background: var(--primary-color); color: white; width: 60px; height: 60px; border-radius: 50%; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                            <i class="fas fa-handshake" style="font-size: 1.5rem;"></i>
                        </div>
                        <div>
                            <h3 style="color: var(--primary-color); margin-bottom: 0.5rem;">Uy tín</h3>
                            <p style="color: var(--text-light); line-height: 1.8;">
                                Xây dựng niềm tin với khách hàng thông qua sự minh bạch, trung thực và cam kết chất lượng dịch vụ.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Team Section -->
<?php if (!empty($barbers)): ?>
<section id="team" class="section" style="background: var(--bg-white);">
    <div class="container">
        <h2 class="section-title">Đội ngũ Barber</h2>
        <p class="text-center" style="color: var(--text-light); margin-bottom: 2rem; max-width: 600px; margin-left: auto; margin-right: auto;">
            Đội ngũ barber chuyên nghiệp, giàu kinh nghiệm và đam mê với nghề
        </p>
        <div class="services-grid" style="grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));">
            <?php foreach ($barbers as $barber): ?>
                <div class="card" style="text-align: center;">
                    <div class="card-body">
                        <div style="width: 120px; height: 120px; border-radius: 50%; background: linear-gradient(135deg, var(--primary-color), var(--primary-light)); margin: 0 auto 1rem; display: flex; align-items: center; justify-content: center; color: white; font-size: 3rem; font-weight: bold;">
                            <?php echo strtoupper(substr($barber['full_name'], 0, 1)); ?>
                        </div>
                        <h3 style="color: var(--primary-color); margin-bottom: 0.5rem;">
                            <?php echo htmlspecialchars($barber['full_name']); ?>
                        </h3>
                        <p style="color: var(--text-light); margin-bottom: 0.5rem;">
                            <i class="fas fa-briefcase"></i> Barber chuyên nghiệp
                        </p>
                        <?php if ($barber['phone']): ?>
                            <p style="color: var(--text-light); font-size: 0.9rem;">
                                <i class="fas fa-phone"></i> <?php echo htmlspecialchars($barber['phone']); ?>
                            </p>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- Contact Info Section -->
<section id="contact" class="section">
    <div class="container">
        <h2 class="section-title">Thông tin liên hệ</h2>
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 2rem; margin-top: 2rem;">
            <div class="card">
                <div class="card-body">
                    <div style="display: flex; align-items: start; gap: 1.5rem;">
                        <div style="background: var(--primary-color); color: white; width: 50px; height: 50px; border-radius: 10px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                            <i class="fas fa-map-marker-alt"></i>
                        </div>
                        <div>
                            <h3 style="color: var(--primary-color); margin-bottom: 0.5rem;">Địa chỉ</h3>
                            <p style="color: var(--text-light); line-height: 1.8;">
                                123 Đường ABC, Phường XYZ<br>
                                Quận 1, TP. Hồ Chí Minh
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="card">
                <div class="card-body">
                    <div style="display: flex; align-items: start; gap: 1.5rem;">
                        <div style="background: var(--primary-color); color: white; width: 50px; height: 50px; border-radius: 10px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                            <i class="fas fa-phone"></i>
                        </div>
                        <div>
                            <h3 style="color: var(--primary-color); margin-bottom: 0.5rem;">Điện thoại</h3>
                            <p style="color: var(--text-light); line-height: 1.8;">
                                <a href="tel:0123456789" style="color: var(--primary-color); text-decoration: none;">
                                    0123 456 789
                                </a><br>
                                <a href="tel:0987654321" style="color: var(--primary-color); text-decoration: none;">
                                    0987 654 321
                                </a>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="card">
                <div class="card-body">
                    <div style="display: flex; align-items: start; gap: 1.5rem;">
                        <div style="background: var(--primary-color); color: white; width: 50px; height: 50px; border-radius: 10px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                            <i class="fas fa-clock"></i>
                        </div>
                        <div>
                            <h3 style="color: var(--primary-color); margin-bottom: 0.5rem;">Giờ làm việc</h3>
                            <p style="color: var(--text-light); line-height: 1.8;">
                                Thứ 2 - Thứ 6: 9:00 - 20:00<br>
                                Thứ 7 - Chủ nhật: 8:00 - 21:00
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="card">
                <div class="card-body">
                    <div style="display: flex; align-items: start; gap: 1.5rem;">
                        <div style="background: var(--primary-color); color: white; width: 50px; height: 50px; border-radius: 10px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                            <i class="fas fa-envelope"></i>
                        </div>
                        <div>
                            <h3 style="color: var(--primary-color); margin-bottom: 0.5rem;">Email</h3>
                            <p style="color: var(--text-light); line-height: 1.8;">
                                <a href="mailto:info@tlbarber.com" style="color: var(--primary-color); text-decoration: none;">
                                    info@tlbarber.com
                                </a><br>
                                <a href="mailto:support@tlbarber.com" style="color: var(--primary-color); text-decoration: none;">
                                    support@tlbarber.com
                                </a>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="section" style="background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-light) 100%); color: white;">
    <div class="container text-center">
        <h2 style="font-size: 2.5rem; margin-bottom: 1rem;">Sẵn sàng trải nghiệm dịch vụ?</h2>
        <p style="font-size: 1.25rem; margin-bottom: 2rem; opacity: 0.9;">
            Đặt lịch ngay hôm nay để nhận được dịch vụ tốt nhất từ đội ngũ chuyên nghiệp
        </p>
        <div style="display: flex; gap: 1rem; justify-content: center; flex-wrap: wrap;">
            <a href="<?php echo BASE_URL; ?>pages/booking.php" class="btn btn-primary" style="font-size: 1.1rem; padding: 15px 40px; background: var(--secondary-color); color: var(--primary-color);">
                <i class="fas fa-calendar-check"></i> Đặt lịch ngay
            </a>
            <a href="<?php echo BASE_URL; ?>pages/services.php" class="btn btn-outline" style="font-size: 1.1rem; padding: 15px 40px; border: 2px solid white; color: white; background: transparent;">
                <i class="fas fa-cut"></i> Xem dịch vụ
            </a>
        </div>
    </div>
</section>
<?php echo round($barber['rating'] ?? 0); ?>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>

