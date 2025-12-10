<?php
$page_title = 'Địa chỉ TL Barber';
require_once __DIR__ . '/../includes/header.php';
?>

<div class="section">
    <div class="container location-wrapper">
        <div class="section-title">Địa chỉ TL Barber</div>

        <div class="location-grid">
            <div class="card location-info">
                <div class="card-body">
                    <h2 class="location-heading"><i class="fas fa-map-marker-alt"></i> Thông tin tiệm</h2>
                    <ul class="location-list">
                        <li>
                            <span class="location-label"><i class="fas fa-store"></i> Tên tiệm:</span>
                            <span class="location-value">TL Barber</span>
                        </li>
                        <li>
                            <span class="location-label"><i class="fas fa-location-dot"></i> Địa chỉ:</span>
                            <span class="location-value">504 Đại Lộ Bình Dương, Hiệp Thành, Thủ Dầu Một, Bình Dương</span>
                        </li>
                        <li>
                            <span class="location-label"><i class="fas fa-clock"></i> Thời gian hoạt động:</span>
                            <span class="location-value">Thứ 2 – Chủ nhật (8:00 – 20:00)</span>
                        </li>
                        <li>
                            <span class="location-label"><i class="fas fa-phone"></i> Số điện thoại:</span>
                            <span class="location-value"><a href="tel:0398556089">0398556089</a></span>
                        </li>
                        <li>
                            <span class="location-label"><i class="fas fa-envelope"></i> Email:</span>
                            <span class="location-value"><a href="mailto:tlbarber@gmail.com">tlbarber@gmail.com</a></span>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="card location-map">
                <div class="card-body">
                    <h2 class="location-heading"><i class="fas fa-map"></i> Bản đồ</h2>
                    <div class="map-embed">
                        <iframe
                            src="https://www.google.com/maps?q=504%20%C4%90%E1%BA%A1i%20L%E1%BB%99%20B%C3%ACnh%20D%C6%B0%C6%A1ng%2C%20Hi%E1%BB%87p%20Th%C3%A0nh%2C%20Th%E1%BB%A7%20D%E1%BA%A7u%20M%E1%BB%99t%2C%20B%C3%ACnh%20D%C6%B0%C6%A1ng&output=embed"
                            allowfullscreen=""
                            loading="lazy"
                            referrerpolicy="no-referrer-when-downgrade"></iframe>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>


