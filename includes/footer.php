    </main>
    <footer class="main-footer">
        <div class="container">
            <div class="footer-content">
                <div class="footer-section">
                    <h3><i class="fas fa-cut"></i> <?php echo SITE_NAME; ?></h3>
                    <p>Dịch vụ cắt tóc và làm đẹp chuyên nghiệp, mang đến cho bạn vẻ ngoài tự tin và phong cách độc đáo.</p>
                    <div class="social-links">
                        <a href="#"><i class="fab fa-facebook"></i></a>
                        <a href="#"><i class="fab fa-instagram"></i></a>
                        <a href="#"><i class="fab fa-tiktok"></i></a>
                        <a href="#"><i class="fab fa-youtube"></i></a>
                    </div>
                </div>
                <div class="footer-section">
                    <h4>Dịch vụ</h4>
                    <ul>
                        <li><a href="<?php echo BASE_URL; ?>pages/services.php">Cắt tóc nam</a></li>
                        <li><a href="<?php echo BASE_URL; ?>pages/services.php">Uốn nhuộm</a></li>
                        <li><a href="<?php echo BASE_URL; ?>pages/services.php">Cạo râu</a></li>
                        <li><a href="<?php echo BASE_URL; ?>pages/services.php">Massage đầu</a></li>
                    </ul>
                </div>
                <div class="footer-section">
                    <h4>Thông tin</h4>
                    <ul>
                        <li><a href="#">Về chúng tôi</a></li>
                        <li><a href="#">Đội ngũ barber</a></li>
                        <li><a href="#">Chính sách hủy lịch</a></li>
                        <li><a href="#">Liên hệ</a></li>
                    </ul>
                </div>
                <div class="footer-section">
                    <h4>Liên hệ</h4>
                    <ul class="contact-info">
                        <li><i class="fas fa-map-marker-alt"></i> 123 Đường ABC, Quận XYZ, TP.HCM</li>
                        <li><i class="fas fa-phone"></i> 0123 456 789</li>
                        <li><i class="fas fa-envelope"></i> info@tlbarber.com</li>
                        <li><i class="fas fa-clock"></i> 9:00 - 20:00 (T2 - CN)</li>
                    </ul>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; <?php echo date('Y'); ?> <?php echo SITE_NAME; ?>. Tất cả quyền được bảo lưu.</p>
            </div>
        </div>
    </footer>
    
    <div id="notification-toast" class="notification-toast"></div>
    
    <script src="<?php echo ASSETS_PATH; ?>js/main.js"></script>
</body>
</html>

