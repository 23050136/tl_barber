# TL Barber - Hệ thống đặt lịch cắt tóc

Website đặt lịch dịch vụ cắt tóc và làm đẹp chuyên nghiệp.

## Tính năng chính

### Trang người dùng (USER)

1. **Trang chủ (Home)**
   - Banner giới thiệu TL Barber
   - Danh sách dịch vụ nổi bật
   - Feedback/Review nổi bật
   - Nút "Đặt lịch ngay"

2. **Trang danh sách dịch vụ (Services)**
   - Hiển thị tất cả dịch vụ đang cung cấp
   - Thông tin: Tên, Giá, Thời lượng, Hình minh họa
   - Nút "Đặt lịch"

3. **Trang chi tiết dịch vụ**
   - Mô tả chi tiết
   - Giá và thời lượng
   - Danh sách barber có thể thực hiện
   - Đánh giá của khách hàng
   - Nút "Đặt lịch ngay"

4. **Đăng ký – Đăng nhập (Auth – JWT)**
   - Đăng ký tài khoản
   - Đăng nhập
   - Quên mật khẩu
   - Sửa thông tin cá nhân

5. **Trang đặt lịch (Booking)** ⭐ QUAN TRỌNG NHẤT
   - Chọn dịch vụ
   - Chọn barber thực hiện
   - Chọn ngày
   - Chọn khung giờ có sẵn (calendar scheduling)
   - Xác nhận thông tin
   - Kiểm tra trùng lịch tự động

6. **Trang lịch sử đặt lịch (Booking History)**
   - Hiển thị lịch đã đặt
   - Trạng thái: Pending / Confirmed / Completed / Cancelled
   - Nút hủy lịch (theo policy)
   - Nút đánh giá sau khi hoàn thành

7. **Thông báo (Notifications)**
   - Popup "Đặt lịch thành công"
   - Notification trên web
   - Email xác nhận (optional)

## Cài đặt

### Yêu cầu hệ thống
- PHP >= 7.4
- MySQL >= 5.7
- Apache/Nginx với mod_rewrite
- Composer

### Các bước cài đặt

1. **Clone repository hoặc copy files vào thư mục web**

2. **Cài đặt dependencies:**
```bash
composer install
```

3. **Tạo database:**
   - Import file `database/schema.sql` vào MySQL
   - Hoặc chạy các câu lệnh SQL trong file

4. **Cấu hình database:**
   - Mở file `config/database.php`
   - Cập nhật thông tin kết nối database:
     - DB_HOST
     - DB_USER
     - DB_PASS
     - DB_NAME

5. **Cấu hình BASE_URL:**
   - Mở file `config/config.php`
   - Cập nhật `BASE_URL` theo đường dẫn của bạn

6. **Tạo thư mục images (nếu cần):**
```bash
mkdir -p images/services
```

## Tài khoản mặc định

### Admin
- Email: `admin@tlbarber.com`
- Password: `admin123`

### Barber (mẫu)
- Email: `barber1@tlbarber.com`
- Password: `barber123`

## Cấu trúc thư mục

```
PTUDMNM_TL/
├── api/              # API endpoints
├── assets/
│   ├── css/          # Stylesheets
│   └── js/           # JavaScript files
├── config/           # Configuration files
├── database/         # Database schema
├── includes/         # Header, footer, navigation
├── pages/            # Main pages
└── index.php         # Homepage
```

## Công nghệ sử dụng

- **Backend:** PHP 7.4+
- **Database:** MySQL
- **Authentication:** JWT (JSON Web Tokens)
- **Frontend:** HTML5, CSS3, JavaScript (Vanilla)
- **Icons:** Font Awesome 6.4.0

## Tính năng bảo mật

- JWT authentication
- Password hashing (bcrypt)
- SQL injection prevention (PDO prepared statements)
- XSS protection (htmlspecialchars)
- CSRF protection (có thể thêm)

## Chính sách hủy lịch

- Khách hàng chỉ có thể hủy lịch trước 2 giờ so với thời gian đặt lịch
- Sau 2 giờ, cần liên hệ trực tiếp để hủy

## License

MIT License

## Tác giả

TL Barber Team

