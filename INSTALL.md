# Hướng dẫn cài đặt TL Barber

## Bước 1: Cài đặt Composer và Dependencies

```bash
composer install
```

Nếu chưa có Composer, tải tại: https://getcomposer.org/

## Bước 2: Tạo Database

1. Mở phpMyAdmin hoặc MySQL client
2. Tạo database mới (hoặc sử dụng database có sẵn)
3. Import file `database/schema.sql`

Hoặc chạy lệnh:
```bash
mysql -u root -p < database/schema.sql
```

## Bước 3: Cấu hình Database

Mở file `config/database.php` và cập nhật:

```php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');        // Thay đổi nếu cần
define('DB_PASS', '');            // Thay đổi nếu cần
define('DB_NAME', 'tl_barber');
```

## Bước 4: Cấu hình BASE_URL

Mở file `config/config.php` và cập nhật:

```php
define('BASE_URL', 'http://localhost/PTUDMNM_TL/');
```

Thay đổi theo đường dẫn thực tế của bạn.

## Bước 5: Tạo thư mục images (nếu chưa có)

```bash
mkdir -p images/services
```

## Bước 6: Kiểm tra

1. Mở trình duyệt và truy cập: `http://localhost/PTUDMNM_TL/`
2. Đăng ký tài khoản mới hoặc đăng nhập với:
   - Email: `admin@tlbarber.com`
   - Password: `admin123`

## Lưu ý

- Đảm bảo PHP >= 7.4
- Đảm bảo MySQL đang chạy
- Đảm bảo Apache/Nginx có mod_rewrite enabled
- Nếu gặp lỗi JWT, chạy lại `composer install`

## Troubleshooting

### Lỗi "Class 'Firebase\JWT\JWT' not found"
```bash
composer install
```

### Lỗi kết nối database
- Kiểm tra MySQL đang chạy
- Kiểm tra thông tin trong `config/database.php`

### Lỗi 404 trên các trang
- Kiểm tra `.htaccess` có được load không
- Kiểm tra `mod_rewrite` đã được bật chưa

