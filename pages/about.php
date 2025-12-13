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


// Get statistics from statistics table (admin-managed) or fallback to database calculation
$stats_data = [];
try {
    $stmt = $pdo->query("SELECT stat_key, stat_value, stat_label FROM statistics WHERE is_active = 1 ORDER BY display_order ASC, id ASC");
    $stats_rows = $stmt->fetchAll();
    
    foreach ($stats_rows as $row) {
        $stats_data[$row['stat_key']] = [
            'value' => $row['stat_value'],
            'label' => $row['stat_label']
        ];
    }
} catch (PDOException $e) {
    // Table doesn't exist yet, use fallback calculation
    $stats_data = [];
}

// Use statistics from table if available, otherwise calculate from database
$total_customers = $stats_data['total_customers']['value'] ?? null;
$total_bookings = $stats_data['total_bookings']['value'] ?? null;
$avg_rating = $stats_data['avg_rating']['value'] ?? null;
$total_barbers = $stats_data['total_barbers']['value'] ?? null;

// Fallback to database calculation if statistics table doesn't have data
if ($total_customers === null) {
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM users WHERE role = 'customer'");
    $total_customers = number_format($stmt->fetch()['count']) . '+';
}
if ($total_bookings === null) {
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM bookings WHERE status = 'completed'");
    $total_bookings = number_format($stmt->fetch()['count']) . '+';
}
if ($avg_rating === null) {
    $stmt = $pdo->query("SELECT AVG(rating) as avg_rating FROM reviews");
    $avg_row = $stmt->fetch();
    $avg_rating = $avg_row && $avg_row['avg_rating'] !== null ? round((float)$avg_row['avg_rating'], 1) : '4.8';
    $avg_rating = $avg_rating . '/5';
}
if ($total_barbers === null) {
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM barbers WHERE is_available = 1");
    $total_barbers = number_format($stmt->fetch()['count']) . '+';
}

// Get labels
$customer_label = $stats_data['total_customers']['label'] ?? 'Khách hàng tin tưởng';
$booking_label = $stats_data['total_bookings']['label'] ?? 'Lượt đặt lịch';
$rating_label = $stats_data['avg_rating']['label'] ?? 'Đánh giá trung bình';
$barber_label = $stats_data['total_barbers']['label'] ?? 'Barber chuyên nghiệp';
?>

<!-- Hero Section -->
<section class="hero-about" style="background: linear-gradient(135deg, #0a1929 0%, #1a365d 50%, #2d4a7c 100%); position: relative; overflow: hidden; padding: 120px 0 100px;">
    <!-- Decorative Bat Icons -->
    <div style="position: absolute; top: 50px; right: 10%; font-size: 3rem; opacity: 0.15; animation: float 6s ease-in-out infinite;">
        <i class="fas fa-dove"></i>
    </div>
    <div style="position: absolute; top: 150px; right: 5%; font-size: 2.5rem; opacity: 0.1; animation: float 8s ease-in-out infinite 1s;">
        <i class="fas fa-feather-alt"></i>
    </div>
    <div style="position: absolute; bottom: 80px; left: 8%; font-size: 2.8rem; opacity: 0.12; animation: float 7s ease-in-out infinite 2s;">
        <i class="fas fa-dove"></i>
    </div>
    
    <div class="container" style="position: relative; z-index: 2;">
        <div style="max-width: 900px; text-align: left; padding-left: 40px;">
            <div style="display: flex; align-items: center; gap: 20px; margin-bottom: 30px;">
                <div style="width: 5px; height: 80px; background: linear-gradient(180deg, var(--secondary-color), #ffed4e); border-radius: 3px;"></div>
                <h1 style="color: white; font-size: 4rem; font-weight: 700; margin: 0; line-height: 1.2; text-shadow: 2px 2px 10px rgba(0,0,0,0.3);">
                    <i class="fas fa-cut" style="color: var(--secondary-color); margin-right: 15px;"></i>
                    Về TL Barber
                </h1>
            </div>
            <p style="color: rgba(255, 255, 255, 0.95); font-size: 1.4rem; line-height: 1.8; margin: 0; max-width: 750px; font-weight: 300; text-shadow: 1px 1px 5px rgba(0,0,0,0.2);">
                Hơn 10 năm kinh nghiệm trong ngành cắt tóc và làm đẹp, chúng tôi tự hào mang đến cho khách hàng những dịch vụ chuyên nghiệp và phong cách độc đáo
            </p>
            <div style="margin-top: 40px; display: flex; gap: 15px; align-items: center;">
                <div style="display: flex; gap: 10px;">
                    <div style="width: 12px; height: 12px; background: var(--secondary-color); border-radius: 50%; animation: pulse 2s infinite;"></div>
                    <div style="width: 12px; height: 12px; background: var(--secondary-color); border-radius: 50%; animation: pulse 2s infinite 0.3s;"></div>
                    <div style="width: 12px; height: 12px; background: var(--secondary-color); border-radius: 50%; animation: pulse 2s infinite 0.6s;"></div>
                </div>
                <span style="color: rgba(255, 255, 255, 0.8); font-size: 0.95rem; margin-left: 10px;">
                    <i class="fas fa-star" style="color: var(--secondary-color);"></i> Đẳng cấp và chuyên nghiệp
                </span>
            </div>
        </div>
    </div>
    
    <style>
        @keyframes float {
            0%, 100% { transform: translateY(0px) rotate(0deg); }
            50% { transform: translateY(-20px) rotate(5deg); }
        }
        @keyframes pulse {
            0%, 100% { opacity: 1; transform: scale(1); }
            50% { opacity: 0.5; transform: scale(0.8); }
        }
    </style>
</section>

<!-- About Section -->
<section class="section" style="padding: 100px 0; background: linear-gradient(to bottom, #ffffff 0%, #f8f9fa 100%);">
    <div class="container">
        <div style="display: grid; grid-template-columns: 1.2fr 0.8fr; gap: 6rem; align-items: start; margin-bottom: 4rem; position: relative;">
            <!-- Decorative Elements -->
            <div style="position: absolute; top: -30px; left: -20px; font-size: 4rem; opacity: 0.08; z-index: 0;">
                <i class="fas fa-feather-alt"></i>
            </div>
            
            <div style="position: relative; z-index: 1;">
                <div style="display: flex; align-items: center; gap: 15px; margin-bottom: 35px;">
                    <div style="width: 4px; height: 60px; background: linear-gradient(180deg, var(--primary-color), var(--primary-light)); border-radius: 2px;"></div>
                    <h2 class="section-title" style="text-align: left; margin: 0; font-size: 2.8rem; font-weight: 700; color: var(--primary-color);">
                        Câu chuyện của chúng tôi
                    </h2>
                    <div style="font-size: 2rem; color: var(--secondary-color); opacity: 0.6;">
                        <i class="fas fa-dove"></i>
                    </div>
                </div>
                
                <div style="margin-left: 19px;">
                    <p style="margin-bottom: 25px; color: var(--text-dark); line-height: 2; font-size: 1.1rem; font-weight: 300;">
                        TL Barber được thành lập với tầm nhìn trở thành địa chỉ hàng đầu về dịch vụ cắt tóc và làm đẹp nam giới tại Việt Nam. Với hơn 10 năm kinh nghiệm, chúng tôi đã phục vụ hàng nghìn khách hàng và nhận được sự tin tưởng, yêu mến từ cộng đồng.
                    </p>
                    <p style="margin-bottom: 25px; color: var(--text-dark); line-height: 2; font-size: 1.1rem; font-weight: 300;">
                        Chúng tôi không chỉ đơn thuần là một tiệm cắt tóc, mà còn là nơi khách hàng có thể thư giãn, tận hưởng không gian sang trọng và nhận được sự chăm sóc tận tâm từ đội ngũ barber chuyên nghiệp.
                    </p>
                    <p style="color: var(--text-dark); line-height: 2; font-size: 1.1rem; font-weight: 300; position: relative; padding-left: 30px;">
                        <span style="position: absolute; left: 0; top: 0; font-size: 3rem; color: var(--secondary-color); opacity: 0.3; font-family: serif; line-height: 1;">"</span>
                        Mỗi kiểu tóc, mỗi dịch vụ đều được thực hiện với sự tỉ mỉ, chuyên nghiệp và đam mê nghề nghiệp. Chúng tôi cam kết mang đến cho bạn trải nghiệm tuyệt vời nhất.
                    </p>
                </div>
            </div>
            
            <div style="position: relative; z-index: 1;">
                <div style="background: linear-gradient(135deg, var(--primary-color) 0%, #1a365d 100%); color: white; padding: 50px 40px; border-radius: 20px; box-shadow: 0 20px 60px rgba(0, 31, 63, 0.3); position: relative; overflow: hidden;">
                    <!-- Decorative Pattern -->
                    <div style="position: absolute; top: -50px; right: -50px; width: 200px; height: 200px; background: var(--secondary-color); opacity: 0.1; border-radius: 50%;"></div>
                    <div style="position: absolute; bottom: -30px; left: -30px; width: 150px; height: 150px; background: var(--secondary-color); opacity: 0.1; border-radius: 50%;"></div>
                    
                    <div style="position: relative; z-index: 2; text-align: center;">
                        <div style="margin-bottom: 25px;">
                            <div style="width: 80px; height: 80px; background: rgba(255, 215, 0, 0.2); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 20px; border: 3px solid var(--secondary-color);">
                                <i class="fas fa-award" style="font-size: 3.5rem; color: var(--secondary-color);"></i>
                            </div>
                        </div>
                        <h3 style="font-size: 2.5rem; margin-bottom: 10px; font-weight: 700;">Hơn 10 năm</h3>
                        <p style="font-size: 1.2rem; opacity: 0.9; font-weight: 300;">Kinh nghiệm phục vụ</p>
                        <div style="margin-top: 30px; display: flex; justify-content: center; gap: 5px;">
                            <i class="fas fa-star" style="color: var(--secondary-color);"></i>
                            <i class="fas fa-star" style="color: var(--secondary-color);"></i>
                            <i class="fas fa-star" style="color: var(--secondary-color);"></i>
                            <i class="fas fa-star" style="color: var(--secondary-color);"></i>
                            <i class="fas fa-star" style="color: var(--secondary-color);"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Statistics Section -->
<section class="section" style="background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 50%, #f8f9fa 100%); padding: 100px 0; position: relative; overflow: hidden;">
    <!-- Floating Decorative Icons -->
    <div style="position: absolute; top: 50px; left: 5%; font-size: 3rem; opacity: 0.08; animation: float 8s ease-in-out infinite;">
        <i class="fas fa-dove"></i>
    </div>
    <div style="position: absolute; top: 200px; right: 8%; font-size: 2.5rem; opacity: 0.08; animation: float 10s ease-in-out infinite 2s;">
        <i class="fas fa-feather-alt"></i>
    </div>
    
    <div class="container" style="position: relative; z-index: 2;">
        <div style="text-align: left; margin-bottom: 60px; padding-left: 40px;">
            <h2 class="section-title" style="text-align: left; font-size: 2.8rem; font-weight: 700; color: var(--primary-color); margin-bottom: 15px;">
                Thành tựu của chúng tôi
            </h2>
            <div style="width: 100px; height: 4px; background: linear-gradient(90deg, var(--primary-color), var(--secondary-color)); border-radius: 2px; margin-top: 10px;"></div>
        </div>
        
        <div class="services-grid" style="grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 30px; margin-top: 2rem;">
            <div class="card" style="text-align: left; padding: 40px 35px; border: none; box-shadow: 0 10px 40px rgba(0, 31, 63, 0.1); transition: all 0.4s; background: white; position: relative; overflow: hidden;">
                <div style="position: absolute; top: -20px; right: -20px; font-size: 4rem; opacity: 0.05;">
                    <i class="fas fa-dove"></i>
                </div>
                <div style="width: 70px; height: 70px; background: linear-gradient(135deg, var(--primary-color), var(--primary-light)); border-radius: 15px; display: flex; align-items: center; justify-content: center; margin-bottom: 25px; box-shadow: 0 5px 15px rgba(0, 31, 63, 0.2);">
                    <i class="fas fa-users" style="font-size: 2rem; color: white;"></i>
                </div>
                <h3 style="font-size: 3rem; color: var(--primary-color); margin-bottom: 10px; font-weight: 700;">
                    <?php echo $total_customers; ?>
                </h3>
                <p style="color: var(--text-dark); font-size: 1.1rem; font-weight: 500; margin-bottom: 5px;"><?php echo $customer_label; ?></p>
                <p style="color: var(--text-light); font-size: 0.95rem; margin: 0;">Được yêu mến và tin cậy</p>
            </div>
            
            <div class="card" style="text-align: left; padding: 40px 35px; border: none; box-shadow: 0 10px 40px rgba(0, 31, 63, 0.1); transition: all 0.4s; background: white; position: relative; overflow: hidden;">
                <div style="position: absolute; top: -20px; right: -20px; font-size: 4rem; opacity: 0.05;">
                    <i class="fas fa-feather-alt"></i>
                </div>
                <div style="width: 70px; height: 70px; background: linear-gradient(135deg, var(--primary-color), var(--primary-light)); border-radius: 15px; display: flex; align-items: center; justify-content: center; margin-bottom: 25px; box-shadow: 0 5px 15px rgba(0, 31, 63, 0.2);">
                    <i class="fas fa-calendar-check" style="font-size: 2rem; color: white;"></i>
                </div>
                <h3 style="font-size: 3rem; color: var(--primary-color); margin-bottom: 10px; font-weight: 700;">
                    <?php echo $total_bookings; ?>
                </h3>
                <p style="color: var(--text-dark); font-size: 1.1rem; font-weight: 500; margin-bottom: 5px;"><?php echo $booking_label; ?></p>
                <p style="color: var(--text-light); font-size: 0.95rem; margin: 0;">Phục vụ chuyên nghiệp</p>
            </div>
            
            <div class="card" style="text-align: left; padding: 40px 35px; border: none; box-shadow: 0 10px 40px rgba(255, 215, 0, 0.15); transition: all 0.4s; background: linear-gradient(135deg, #fff9e6 0%, #ffffff 100%); position: relative; overflow: hidden;">
                <div style="position: absolute; top: -20px; right: -20px; font-size: 4rem; opacity: 0.08; color: var(--secondary-color);">
                    <i class="fas fa-dove"></i>
                </div>
                <div style="width: 70px; height: 70px; background: linear-gradient(135deg, var(--secondary-color), #ffed4e); border-radius: 15px; display: flex; align-items: center; justify-content: center; margin-bottom: 25px; box-shadow: 0 5px 15px rgba(255, 215, 0, 0.3);">
                    <i class="fas fa-star" style="font-size: 2rem; color: var(--primary-color);"></i>
                </div>
                <h3 style="font-size: 3rem; color: var(--primary-color); margin-bottom: 10px; font-weight: 700;">
                    <?php echo $avg_rating; ?>
                </h3>
                <p style="color: var(--text-dark); font-size: 1.1rem; font-weight: 500; margin-bottom: 5px;"><?php echo $rating_label; ?></p>
                <div style="display: flex; gap: 3px; margin-top: 5px;">
                    <?php for($i = 0; $i < 5; $i++): ?>
                        <i class="fas fa-star" style="color: var(--secondary-color); font-size: 0.9rem;"></i>
                    <?php endfor; ?>
                </div>
            </div>
            
            <div class="card" style="text-align: left; padding: 40px 35px; border: none; box-shadow: 0 10px 40px rgba(0, 31, 63, 0.1); transition: all 0.4s; background: white; position: relative; overflow: hidden;">
                <div style="position: absolute; top: -20px; right: -20px; font-size: 4rem; opacity: 0.05;">
                    <i class="fas fa-feather-alt"></i>
                </div>
                <div style="width: 70px; height: 70px; background: linear-gradient(135deg, var(--primary-color), var(--primary-light)); border-radius: 15px; display: flex; align-items: center; justify-content: center; margin-bottom: 25px; box-shadow: 0 5px 15px rgba(0, 31, 63, 0.2);">
                    <i class="fas fa-user-tie" style="font-size: 2rem; color: white;"></i>
                </div>
                <h3 style="font-size: 3rem; color: var(--primary-color); margin-bottom: 10px; font-weight: 700;">
                    <?php echo $total_barbers; ?>
                </h3>
                <p style="color: var(--text-dark); font-size: 1.1rem; font-weight: 500; margin-bottom: 5px;"><?php echo $barber_label; ?></p>
                <p style="color: var(--text-light); font-size: 0.95rem; margin: 0;">Đội ngũ tận tâm</p>
            </div>
        </div>
    </div>
</section>

<!-- Values Section -->
<section class="section" style="padding: 100px 0; background: white; position: relative;">
    <div class="container">
        <div style="text-align: left; margin-bottom: 60px; padding-left: 40px;">
            <h2 class="section-title" style="text-align: left; font-size: 2.8rem; font-weight: 700; color: var(--primary-color); margin-bottom: 15px;">
                Giá trị cốt lõi
            </h2>
            <div style="width: 100px; height: 4px; background: linear-gradient(90deg, var(--primary-color), var(--secondary-color)); border-radius: 2px; margin-top: 10px;"></div>
            <p style="color: var(--text-light); font-size: 1.1rem; margin-top: 20px; max-width: 600px;">Những giá trị đã tạo nên thương hiệu TL Barber</p>
        </div>
        
        <div class="services-grid" style="grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 30px; margin-top: 2rem;">
            <div class="card" style="border: none; box-shadow: 0 8px 30px rgba(0, 31, 63, 0.08); transition: all 0.4s; background: white; position: relative; overflow: hidden; padding: 0;">
                <div style="position: absolute; top: 20px; right: 20px; font-size: 3rem; opacity: 0.06;">
                    <i class="fas fa-dove"></i>
                </div>
                <div class="card-body" style="padding: 40px 35px;">
                    <div style="display: flex; align-items: start; gap: 25px;">
                        <div style="background: linear-gradient(135deg, var(--primary-color), var(--primary-light)); color: white; width: 80px; height: 80px; border-radius: 18px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; box-shadow: 0 8px 20px rgba(0, 31, 63, 0.25);">
                            <i class="fas fa-heart" style="font-size: 2rem;"></i>
                        </div>
                        <div style="flex: 1;">
                            <h3 style="color: var(--primary-color); margin-bottom: 15px; font-size: 1.5rem; font-weight: 700;">Tận tâm</h3>
                            <p style="color: var(--text-dark); line-height: 1.9; font-size: 1rem; margin: 0;">
                                Mỗi khách hàng đều được chăm sóc với sự tận tâm và chu đáo. Chúng tôi lắng nghe nhu cầu của bạn để mang đến dịch vụ phù hợp nhất.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="card" style="border: none; box-shadow: 0 8px 30px rgba(0, 31, 63, 0.08); transition: all 0.4s; background: white; position: relative; overflow: hidden; padding: 0;">
                <div style="position: absolute; top: 20px; right: 20px; font-size: 3rem; opacity: 0.06;">
                    <i class="fas fa-feather-alt"></i>
                </div>
                <div class="card-body" style="padding: 40px 35px;">
                    <div style="display: flex; align-items: start; gap: 25px;">
                        <div style="background: linear-gradient(135deg, var(--primary-color), var(--primary-light)); color: white; width: 80px; height: 80px; border-radius: 18px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; box-shadow: 0 8px 20px rgba(0, 31, 63, 0.25);">
                            <i class="fas fa-gem" style="font-size: 2rem;"></i>
                        </div>
                        <div style="flex: 1;">
                            <h3 style="color: var(--primary-color); margin-bottom: 15px; font-size: 1.5rem; font-weight: 700;">Chất lượng</h3>
                            <p style="color: var(--text-dark); line-height: 1.9; font-size: 1rem; margin: 0;">
                                Sử dụng các sản phẩm cao cấp và kỹ thuật chuyên nghiệp để đảm bảo chất lượng dịch vụ tốt nhất cho khách hàng.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="card" style="border: none; box-shadow: 0 8px 30px rgba(0, 31, 63, 0.08); transition: all 0.4s; background: white; position: relative; overflow: hidden; padding: 0;">
                <div style="position: absolute; top: 20px; right: 20px; font-size: 3rem; opacity: 0.06;">
                    <i class="fas fa-dove"></i>
                </div>
                <div class="card-body" style="padding: 40px 35px;">
                    <div style="display: flex; align-items: start; gap: 25px;">
                        <div style="background: linear-gradient(135deg, var(--secondary-color), #ffed4e); color: var(--primary-color); width: 80px; height: 80px; border-radius: 18px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; box-shadow: 0 8px 20px rgba(255, 215, 0, 0.3);">
                            <i class="fas fa-lightbulb" style="font-size: 2rem;"></i>
                        </div>
                        <div style="flex: 1;">
                            <h3 style="color: var(--primary-color); margin-bottom: 15px; font-size: 1.5rem; font-weight: 700;">Sáng tạo</h3>
                            <p style="color: var(--text-dark); line-height: 1.9; font-size: 1rem; margin: 0;">
                                Luôn cập nhật xu hướng mới nhất và sáng tạo những kiểu tóc độc đáo, phù hợp với từng cá nhân.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="card" style="border: none; box-shadow: 0 8px 30px rgba(0, 31, 63, 0.08); transition: all 0.4s; background: white; position: relative; overflow: hidden; padding: 0;">
                <div style="position: absolute; top: 20px; right: 20px; font-size: 3rem; opacity: 0.06;">
                    <i class="fas fa-feather-alt"></i>
                </div>
                <div class="card-body" style="padding: 40px 35px;">
                    <div style="display: flex; align-items: start; gap: 25px;">
                        <div style="background: linear-gradient(135deg, var(--primary-color), var(--primary-light)); color: white; width: 80px; height: 80px; border-radius: 18px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; box-shadow: 0 8px 20px rgba(0, 31, 63, 0.25);">
                            <i class="fas fa-handshake" style="font-size: 2rem;"></i>
                        </div>
                        <div style="flex: 1;">
                            <h3 style="color: var(--primary-color); margin-bottom: 15px; font-size: 1.5rem; font-weight: 700;">Uy tín</h3>
                            <p style="color: var(--text-dark); line-height: 1.9; font-size: 1rem; margin: 0;">
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
<section id="team" class="section" style="padding: 100px 0; background: white; position: relative; overflow: hidden;">
    <!-- Decorative Icons -->
    <div style="position: absolute; top: 80px; right: 5%; font-size: 3rem; opacity: 0.06; animation: float 9s ease-in-out infinite;">
        <i class="fas fa-dove"></i>
    </div>
    
    <div class="container" style="position: relative; z-index: 2;">
        <div style="text-align: left; margin-bottom: 60px; padding-left: 40px;">
            <h2 class="section-title" style="text-align: left; font-size: 2.8rem; font-weight: 700; color: var(--primary-color); margin-bottom: 15px;">
                Đội ngũ Barber
            </h2>
            <div style="width: 100px; height: 4px; background: linear-gradient(90deg, var(--primary-color), var(--secondary-color)); border-radius: 2px; margin-top: 10px;"></div>
            <p style="color: var(--text-light); font-size: 1.1rem; margin-top: 20px; max-width: 600px;">
                Đội ngũ barber chuyên nghiệp, giàu kinh nghiệm và đam mê với nghề
            </p>
        </div>
        
        <div class="services-grid" style="grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 30px;">
            <?php foreach ($barbers as $barber): ?>
                <div class="card" style="text-align: center; border: none; box-shadow: 0 10px 40px rgba(0, 31, 63, 0.1); transition: all 0.4s; background: white; position: relative; overflow: hidden; padding: 0;">
                    <div style="position: absolute; top: 20px; right: 20px; font-size: 2.5rem; opacity: 0.05;">
                        <i class="fas fa-feather-alt"></i>
                    </div>
                    <div class="card-body" style="padding: 50px 35px 40px;">
                        <div style="width: 140px; height: 140px; border-radius: 50%; background: linear-gradient(135deg, var(--primary-color), var(--primary-light)); margin: 0 auto 25px; display: flex; align-items: center; justify-content: center; color: white; font-size: 3.5rem; font-weight: bold; box-shadow: 0 10px 30px rgba(0, 31, 63, 0.25); border: 5px solid rgba(255, 255, 255, 0.9);">
                            <?php echo strtoupper(substr($barber['full_name'], 0, 1)); ?>
                        </div>
                        <h3 style="color: var(--primary-color); margin-bottom: 12px; font-size: 1.5rem; font-weight: 700;">
                            <?php echo htmlspecialchars($barber['full_name']); ?>
                        </h3>
                        <p style="color: var(--text-dark); margin-bottom: 12px; font-size: 1rem; font-weight: 500;">
                            <i class="fas fa-briefcase" style="color: var(--secondary-color); margin-right: 8px;"></i> Barber chuyên nghiệp
                        </p>
                        <?php if ($barber['phone']): ?>
                            <p style="color: var(--text-light); font-size: 0.95rem; margin: 0;">
                                <i class="fas fa-phone" style="margin-right: 8px;"></i> <?php echo htmlspecialchars($barber['phone']); ?>
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
<section id="contact" class="section" style="padding: 100px 0; background: linear-gradient(to bottom, #f8f9fa 0%, #ffffff 100%); position: relative;">
    <!-- Decorative Icons -->
    <div style="position: absolute; top: 100px; left: 3%; font-size: 3rem; opacity: 0.06; animation: float 9s ease-in-out infinite;">
        <i class="fas fa-dove"></i>
    </div>
    
    <div class="container" style="position: relative; z-index: 2;">
        <div style="text-align: left; margin-bottom: 60px; padding-left: 40px;">
            <h2 class="section-title" style="text-align: left; font-size: 2.8rem; font-weight: 700; color: var(--primary-color); margin-bottom: 15px;">
                Thông tin liên hệ
            </h2>
            <div style="width: 100px; height: 4px; background: linear-gradient(90deg, var(--primary-color), var(--secondary-color)); border-radius: 2px; margin-top: 10px;"></div>
        </div>
        
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 30px; margin-top: 2rem;">
            <div class="card" style="border: none; box-shadow: 0 10px 40px rgba(0, 31, 63, 0.1); transition: all 0.4s; background: white; position: relative; overflow: hidden; padding: 0;">
                <div style="position: absolute; top: 15px; right: 15px; font-size: 2.5rem; opacity: 0.05;">
                    <i class="fas fa-feather-alt"></i>
                </div>
                <div class="card-body" style="padding: 40px 35px;">
                    <div style="display: flex; align-items: start; gap: 25px;">
                        <div style="background: linear-gradient(135deg, var(--primary-color), var(--primary-light)); color: white; width: 70px; height: 70px; border-radius: 16px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; box-shadow: 0 8px 20px rgba(0, 31, 63, 0.25);">
                            <i class="fas fa-map-marker-alt" style="font-size: 1.8rem;"></i>
                        </div>
                        <div style="flex: 1;">
                            <h3 style="color: var(--primary-color); margin-bottom: 15px; font-size: 1.4rem; font-weight: 700;">Địa chỉ</h3>
                            <p style="color: var(--text-dark); line-height: 1.9; font-size: 1rem; margin: 0;">
                                504 Đại Lộ Bình Dương, Hiệp Thành<br>
                                Thủ Dầu Một, Bình Dương
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="card" style="border: none; box-shadow: 0 10px 40px rgba(0, 31, 63, 0.1); transition: all 0.4s; background: white; position: relative; overflow: hidden; padding: 0;">
                <div style="position: absolute; top: 15px; right: 15px; font-size: 2.5rem; opacity: 0.05;">
                    <i class="fas fa-dove"></i>
                </div>
                <div class="card-body" style="padding: 40px 35px;">
                    <div style="display: flex; align-items: start; gap: 25px;">
                        <div style="background: linear-gradient(135deg, var(--primary-color), var(--primary-light)); color: white; width: 70px; height: 70px; border-radius: 16px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; box-shadow: 0 8px 20px rgba(0, 31, 63, 0.25);">
                            <i class="fas fa-phone" style="font-size: 1.8rem;"></i>
                        </div>
                        <div style="flex: 1;">
                            <h3 style="color: var(--primary-color); margin-bottom: 15px; font-size: 1.4rem; font-weight: 700;">Điện thoại</h3>
                            <p style="color: var(--text-dark); line-height: 1.9; font-size: 1rem; margin: 0;">
                                <a href="tel:0398556089" style="color: var(--primary-color); text-decoration: none; font-weight: 600; transition: color 0.3s;" onmouseover="this.style.color='var(--primary-light)'" onmouseout="this.style.color='var(--primary-color)'">
                                    <i class="fas fa-phone-alt" style="margin-right: 8px;"></i>0398556089
                                </a>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="card" style="border: none; box-shadow: 0 10px 40px rgba(0, 31, 63, 0.1); transition: all 0.4s; background: white; position: relative; overflow: hidden; padding: 0;">
                <div style="position: absolute; top: 15px; right: 15px; font-size: 2.5rem; opacity: 0.05;">
                    <i class="fas fa-feather-alt"></i>
                </div>
                <div class="card-body" style="padding: 40px 35px;">
                    <div style="display: flex; align-items: start; gap: 25px;">
                        <div style="background: linear-gradient(135deg, var(--secondary-color), #ffed4e); color: var(--primary-color); width: 70px; height: 70px; border-radius: 16px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; box-shadow: 0 8px 20px rgba(255, 215, 0, 0.3);">
                            <i class="fas fa-clock" style="font-size: 1.8rem;"></i>
                        </div>
                        <div style="flex: 1;">
                            <h3 style="color: var(--primary-color); margin-bottom: 15px; font-size: 1.4rem; font-weight: 700;">Giờ làm việc</h3>
                            <p style="color: var(--text-dark); line-height: 1.9; font-size: 1rem; margin: 0;">
                                <strong>Thứ 2 - Thứ 6:</strong> 9:00 - 20:00<br>
                                <strong>Thứ 7 - Chủ nhật:</strong> 8:00 - 21:00
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="card" style="border: none; box-shadow: 0 10px 40px rgba(0, 31, 63, 0.1); transition: all 0.4s; background: white; position: relative; overflow: hidden; padding: 0;">
                <div style="position: absolute; top: 15px; right: 15px; font-size: 2.5rem; opacity: 0.05;">
                    <i class="fas fa-dove"></i>
                </div>
                <div class="card-body" style="padding: 40px 35px;">
                    <div style="display: flex; align-items: start; gap: 25px;">
                        <div style="background: linear-gradient(135deg, var(--primary-color), var(--primary-light)); color: white; width: 70px; height: 70px; border-radius: 16px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; box-shadow: 0 8px 20px rgba(0, 31, 63, 0.25);">
                            <i class="fas fa-envelope" style="font-size: 1.8rem;"></i>
                        </div>
                        <div style="flex: 1;">
                            <h3 style="color: var(--primary-color); margin-bottom: 15px; font-size: 1.4rem; font-weight: 700;">Email</h3>
                            <p style="color: var(--text-dark); line-height: 1.9; font-size: 1rem; margin: 0;">
                                <a href="mailto:info@tlbarber.com" style="color: var(--primary-color); text-decoration: none; font-weight: 500; transition: color 0.3s; display: block; margin-bottom: 5px;" onmouseover="this.style.color='var(--primary-light)'" onmouseout="this.style.color='var(--primary-color)'">
                                    info@tlbarber.com
                                </a>
                                <a href="mailto:support@tlbarber.com" style="color: var(--primary-color); text-decoration: none; font-weight: 500; transition: color 0.3s;" onmouseover="this.style.color='var(--primary-light)'" onmouseout="this.style.color='var(--primary-color)'">
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
<section class="section" style="background: linear-gradient(135deg, #0a1929 0%, #1a365d 50%, #2d4a7c 100%); color: white; padding: 120px 0; position: relative; overflow: hidden;">
    <!-- Decorative Bat Icons -->
    <div style="position: absolute; top: 50px; left: 10%; font-size: 3.5rem; opacity: 0.12; animation: float 8s ease-in-out infinite;">
        <i class="fas fa-dove"></i>
    </div>
    <div style="position: absolute; bottom: 60px; right: 8%; font-size: 3rem; opacity: 0.1; animation: float 10s ease-in-out infinite 2s;">
        <i class="fas fa-feather-alt"></i>
    </div>
    
    <div class="container" style="position: relative; z-index: 2; text-align: left; padding-left: 40px; max-width: 800px;">
        <div style="display: flex; align-items: center; gap: 20px; margin-bottom: 30px;">
            <div style="width: 5px; height: 80px; background: linear-gradient(180deg, var(--secondary-color), #ffed4e); border-radius: 3px;"></div>
            <h2 style="font-size: 3.5rem; font-weight: 700; margin: 0; line-height: 1.2; text-shadow: 2px 2px 10px rgba(0,0,0,0.3);">
                Sẵn sàng trải nghiệm dịch vụ?
            </h2>
        </div>
        <p style="font-size: 1.3rem; margin-bottom: 45px; opacity: 0.95; line-height: 1.8; font-weight: 300; padding-left: 25px;">
            Đặt lịch ngay hôm nay để nhận được dịch vụ tốt nhất từ đội ngũ chuyên nghiệp
        </p>
        <div style="display: flex; gap: 20px; flex-wrap: wrap; padding-left: 25px;">
            <a href="<?php echo BASE_URL; ?>pages/booking.php" class="btn btn-primary" style="font-size: 1.15rem; padding: 18px 45px; background: var(--secondary-color); color: var(--primary-color); font-weight: 700; box-shadow: 0 8px 25px rgba(255, 215, 0, 0.4); border-radius: 8px; transition: all 0.3s;" onmouseover="this.style.transform='translateY(-3px)'; this.style.boxShadow='0 12px 35px rgba(255, 215, 0, 0.5)'" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 8px 25px rgba(255, 215, 0, 0.4)'">
                <i class="fas fa-calendar-check"></i> Đặt lịch ngay
            </a>
            <a href="<?php echo BASE_URL; ?>pages/services.php" class="btn btn-outline" style="font-size: 1.15rem; padding: 18px 45px; border: 3px solid white; color: white; background: transparent; font-weight: 700; border-radius: 8px; transition: all 0.3s;" onmouseover="this.style.background='rgba(255,255,255,0.1)'; this.style.transform='translateY(-3px)'" onmouseout="this.style.background='transparent'; this.style.transform='translateY(0)'">
                <i class="fas fa-cut"></i> Xem dịch vụ
            </a>
        </div>
    </div>
</section>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>

