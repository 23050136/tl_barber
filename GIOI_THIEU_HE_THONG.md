# TÀI LIỆU GIỚI THIỆU HỆ THỐNG
## TL BARBER - HỆ THỐNG ĐẶT LỊCH CẮT TÓC

---

## 1. GIỚI THIỆU VỀ HỆ THỐNG

### 1.1. Tổng quan hệ thống

**TL Barber** là hệ thống quản lý và đặt lịch dịch vụ cắt tóc, làm đẹp chuyên nghiệp được xây dựng nhằm:

- **Tối ưu hóa quy trình đặt lịch**: Khách hàng có thể đặt lịch trực tuyến 24/7 mà không cần gọi điện thoại
- **Quản lý hiệu quả**: Quản trị viên và barber có thể quản lý lịch đặt, dịch vụ, và khách hàng một cách tập trung
- **Trải nghiệm người dùng tốt**: Giao diện thân thiện, dễ sử dụng, hỗ trợ đặt lịch nhanh chóng
- **Tự động hóa**: Hệ thống tự động kiểm tra trùng lịch, xác nhận đặt lịch, và gửi thông báo

### 1.2. Đối tượng sử dụng

Hệ thống phục vụ 3 nhóm người dùng chính:

1. **Khách hàng (Customer)**
   - Đăng ký/Đăng nhập tài khoản
   - Xem danh sách dịch vụ và barber
   - Đặt lịch cắt tóc
   - Xem lịch sử đặt lịch
   - Đánh giá dịch vụ sau khi hoàn thành
   - Nhận thông báo về trạng thái đặt lịch

2. **Barber (Thợ cắt tóc)**
   - Xem lịch đặt của mình
   - Quản lý thông tin cá nhân
   - Xem đánh giá từ khách hàng

3. **Quản trị viên (Admin)**
   - Quản lý dịch vụ (thêm, sửa, xóa)
   - Quản lý barber (thêm, sửa, xóa, phân công dịch vụ)
   - Quản lý đặt lịch (xem, xác nhận, hủy)
   - Quản lý đánh giá
   - Xem báo cáo và thống kê
   - Quét QR code để check-in khách hàng

---

## 2. GIỚI THIỆU CHỨC NĂNG VÀ CÔNG NGHỆ

### 2.1. Chức năng chính của hệ thống

#### 2.1.1. Module Quản lý Người dùng (User Management)

**Chức năng:**
- Đăng ký tài khoản mới
- Đăng nhập/Đăng xuất
- Quên mật khẩu (reset password)
- Quản lý thông tin cá nhân
- Phân quyền người dùng (Customer, Barber, Admin)

**Công nghệ:**
- JWT (JSON Web Tokens) cho xác thực
- Password hashing với bcrypt
- Session management

#### 2.1.2. Module Quản lý Dịch vụ (Service Management)

**Chức năng:**
- Hiển thị danh sách dịch vụ
- Xem chi tiết dịch vụ (tên, mô tả, giá, thời lượng)
- Quản lý dịch vụ (CRUD) - Admin only
- Đánh dấu dịch vụ nổi bật
- Upload hình ảnh dịch vụ

**Công nghệ:**
- PHP file upload
- Image processing
- Database storage

#### 2.1.3. Module Quản lý Barber (Barber Management)

**Chức năng:**
- Hiển thị danh sách barber
- Xem thông tin barber (kinh nghiệm, đánh giá, chuyên môn)
- Phân công barber cho dịch vụ (many-to-many)
- Quản lý barber (CRUD) - Admin only
- Quản lý trạng thái available/unavailable

**Công nghệ:**
- Relational database (many-to-many relationship)
- Rating calculation system

#### 2.1.4. Module Đặt lịch (Booking System) ⭐ CORE MODULE

**Chức năng:**
- Chọn dịch vụ
- Chọn barber thực hiện
- Chọn ngày đặt lịch (calendar)
- Chọn khung giờ có sẵn (time slots)
- Kiểm tra trùng lịch tự động
- Xác nhận đặt lịch
- Hủy lịch (theo chính sách: trước 2 giờ)
- Xem lịch sử đặt lịch
- Quản lý trạng thái: Pending → Confirmed → Completed/Cancelled

**Công nghệ:**
- Calendar scheduling algorithm
- Time slot availability checking
- Conflict detection
- Status workflow management

#### 2.1.5. Module Đánh giá (Review System)

**Chức năng:**
- Đánh giá dịch vụ sau khi hoàn thành (1-5 sao)
- Viết comment/feedback
- Hiển thị đánh giá trên trang dịch vụ
- Tính toán rating trung bình cho barber

**Công nghệ:**
- Rating aggregation
- Comment moderation (optional)

#### 2.1.6. Module Thông báo (Notification System)

**Chức năng:**
- Thông báo đặt lịch thành công
- Thông báo xác nhận lịch
- Thông báo nhắc nhở trước giờ hẹn
- Thông báo hủy lịch
- Hiển thị số lượng thông báo chưa đọc

**Công nghệ:**
- Real-time notifications (có thể mở rộng với WebSocket)
- Email notifications (optional)

#### 2.1.7. Module Quản trị (Admin Panel)

**Chức năng:**
- Dashboard tổng quan
- Quản lý dịch vụ
- Quản lý barber
- Quản lý đặt lịch
- Quản lý đánh giá
- QR Code check-in cho khách hàng
- Cài đặt hệ thống

**Công nghệ:**
- QR Code generation
- Admin authentication
- Role-based access control

### 2.2. Công nghệ sử dụng

#### 2.2.1. Backend

| Công nghệ | Phiên bản | Mục đích |
|-----------|-----------|----------|
| **PHP** | 7.4+ | Server-side scripting, xử lý logic nghiệp vụ |
| **MySQL** | 5.7+ | Database quản lý dữ liệu |
| **Apache/Nginx** | Latest | Web server với mod_rewrite |
| **Composer** | Latest | Dependency management |

#### 2.2.2. Frontend

| Công nghệ | Phiên bản | Mục đích |
|-----------|-----------|----------|
| **HTML5** | - | Cấu trúc trang web |
| **CSS3** | - | Styling và responsive design |
| **JavaScript (Vanilla)** | ES6+ | Tương tác người dùng, AJAX calls |
| **Font Awesome** | 6.4.0 | Icons và UI elements |

#### 2.2.3. Security & Authentication

| Công nghệ | Mục đích |
|-----------|----------|
| **JWT (JSON Web Tokens)** | Xác thực người dùng, stateless authentication |
| **bcrypt** | Mã hóa mật khẩu |
| **PDO Prepared Statements** | Ngăn chặn SQL Injection |
| **htmlspecialchars()** | Ngăn chặn XSS attacks |
| **Session Management** | Quản lý phiên đăng nhập |

#### 2.2.4. Libraries & Tools

- **JWT Library**: Xử lý token authentication
- **QR Code Generator**: Tạo mã QR cho check-in
- **Calendar/Scheduling**: Quản lý lịch đặt

---

## 3. KẾ HOẠCH TRIỂN KHAI

### 3.1. Giai đoạn 1: Phân tích và Thiết kế (Week 1-2)

**Mục tiêu:**
- Phân tích yêu cầu nghiệp vụ
- Thiết kế database schema
- Thiết kế API endpoints
- Thiết kế giao diện người dùng

**Deliverables:**
- Database schema design
- API documentation
- UI/UX mockups
- Technical specification document

### 3.2. Giai đoạn 2: Phát triển Backend (Week 3-5)

**Mục tiêu:**
- Setup môi trường phát triển
- Tạo database và tables
- Phát triển API endpoints
- Implement authentication system
- Implement booking logic

**Deliverables:**
- Database setup hoàn chỉnh
- API endpoints hoạt động
- Authentication system
- Core booking functionality

### 3.3. Giai đoạn 3: Phát triển Frontend (Week 6-8)

**Mục tiêu:**
- Phát triển giao diện người dùng
- Tích hợp API với frontend
- Implement responsive design
- Testing và fix bugs

**Deliverables:**
- User interface hoàn chỉnh
- Responsive design
- Integration với backend

### 3.4. Giai đoạn 4: Phát triển Admin Panel (Week 9-10)

**Mục tiêu:**
- Phát triển giao diện quản trị
- Implement CRUD operations
- QR Code check-in system
- Reporting và statistics

**Deliverables:**
- Admin panel hoàn chỉnh
- Management features
- QR Code system

### 3.5. Giai đoạn 5: Testing và Deployment (Week 11-12)

**Mục tiêu:**
- Unit testing
- Integration testing
- User acceptance testing
- Performance optimization
- Deployment lên production

**Deliverables:**
- Test reports
- Production-ready system
- Documentation
- User manual

---

## 4. MÔ HÌNH KIẾN TRÚC HỆ THỐNG

### 4.1. Kiến trúc tổng quan

```
┌─────────────────────────────────────────────────────────────┐
│                        CLIENT LAYER                          │
│  ┌──────────────┐  ┌──────────────┐  ┌──────────────┐       │
│  │   Web App    │  │  Admin Panel │  │  Mobile Web  │       │
│  │  (Customer)  │  │              │  │  (Optional)  │       │
│  └──────────────┘  └──────────────┘  └──────────────┘       │
└─────────────────────────────────────────────────────────────┘
                            │
                            │ HTTP/HTTPS
                            │
┌─────────────────────────────────────────────────────────────┐
│                     PRESENTATION LAYER                       │
│  ┌──────────────────────────────────────────────────────┐   │
│  │              PHP Pages & Templates                    │   │
│  │  (index.php, pages/*.php, includes/*.php)            │   │
│  └──────────────────────────────────────────────────────┘   │
└─────────────────────────────────────────────────────────────┘
                            │
                            │
┌─────────────────────────────────────────────────────────────┐
│                      APPLICATION LAYER                       │
│  ┌──────────────────────────────────────────────────────┐   │
│  │                    API Endpoints                     │   │
│  │  ┌────────────┐  ┌────────────┐  ┌────────────┐    │   │
│  │  │  Auth API  │  │ Booking API│  │ Service API│    │   │
│  │  └────────────┘  └────────────┘  └────────────┘    │   │
│  └──────────────────────────────────────────────────────┘   │
│  ┌──────────────────────────────────────────────────────┐   │
│  │              Business Logic Layer                     │   │
│  │  - Authentication & Authorization                     │   │
│  │  - Booking Management                                 │   │
│  │  - Scheduling Algorithm                               │   │
│  │  - Notification Service                               │   │
│  └──────────────────────────────────────────────────────┘   │
└─────────────────────────────────────────────────────────────┘
                            │
                            │
┌─────────────────────────────────────────────────────────────┐
│                        DATA LAYER                            │
│  ┌──────────────────────────────────────────────────────┐   │
│  │                    MySQL Database                     │   │
│  │  ┌──────────┐  ┌──────────┐  ┌──────────┐          │   │
│  │  │  Users   │  │ Services │  │ Bookings │          │   │
│  │  └──────────┘  └──────────┘  └──────────┘          │   │
│  │  ┌──────────┐  ┌──────────┐  ┌──────────┐          │   │
│  │  │ Barbers  │  │ Reviews  │  │ Notifications│      │   │
│  │  └──────────┘  └──────────┘  └──────────┘          │   │
│  └──────────────────────────────────────────────────────┘   │
└─────────────────────────────────────────────────────────────┘
```

### 4.2. Mô hình Database (ERD)

```
┌─────────────┐         ┌─────────────┐         ┌─────────────┐
│    Users    │         │   Services  │         │   Barbers   │
├─────────────┤         ├─────────────┤         ├─────────────┤
│ id (PK)     │         │ id (PK)     │         │ id (PK)     │
│ full_name   │         │ name        │         │ user_id (FK)│
│ email       │         │ description │         │ rating      │
│ phone       │         │ price       │         │ experience  │
│ password    │         │ duration    │         │ is_available│
│ role        │         │ image       │         └─────────────┘
│ avatar      │         │ is_featured │                  │
└─────────────┘         └─────────────┘                  │
      │                        │                          │
      │                        │                          │
      │                  ┌─────┴──────┐                   │
      │                  │            │                   │
      │            ┌─────▼─────┐  ┌───▼──────────┐        │
      │            │service_   │  │   Bookings   │        │
      │            │barber     │  ├──────────────┤        │
      │            ├───────────┤  │ id (PK)      │        │
      │            │service_id │  │ user_id (FK) │◄───────┘
      │            │barber_id  │  │ service_id   │
      │            └───────────┘  │ barber_id    │
      │                           │ booking_date │
      │                           │ booking_time │
      │                           │ status       │
      │                           └──────────────┘
      │                                  │
      │                           ┌──────▼──────────┐
      │                           │    Reviews      │
      │                           ├─────────────────┤
      │                           │ id (PK)        │
      │                           │ booking_id (FK)│
      │                           │ user_id (FK)   │
      │                           │ barber_id (FK) │
      │                           │ rating         │
      │                           │ comment        │
      │                           └─────────────────┘
      │
      │                           ┌──────────────┐
      └───────────────────────────►│Notifications │
                                    ├──────────────┤
                                    │ id (PK)      │
                                    │ user_id (FK) │
                                    │ booking_id   │
                                    │ type         │
                                    │ title        │
                                    │ message      │
                                    │ is_read      │
                                    └──────────────┘
```

### 4.3. Luồng dữ liệu chính

#### 4.3.1. Luồng Đặt lịch

```
User → Select Service → Select Barber → Select Date → 
Check Available Slots → Confirm Booking → Save to DB → 
Generate Notification → Send Confirmation
```

#### 4.3.2. Luồng Xác thực

```
User → Login Form → Validate Credentials → Generate JWT → 
Store Token → Redirect to Dashboard → 
Use Token for API Calls
```

#### 4.3.3. Luồng Quản lý Admin

```
Admin → Login → Dashboard → Select Module → 
CRUD Operations → Update Database → 
Return Response → Update UI
```

---

## 5. PHÂN TÍCH NGHIỆP VỤ VÀ QUY TRÌNH HOẠT ĐỘNG

### 5.1. Quy trình đặt lịch (Booking Process)

#### Bước 1: Khách hàng chọn dịch vụ
- **Actor**: Customer
- **Mô tả**: Khách hàng xem danh sách dịch vụ, chọn dịch vụ muốn đặt
- **Input**: Service ID
- **Output**: Service details page

#### Bước 2: Chọn barber
- **Actor**: Customer
- **Mô tả**: Hệ thống hiển thị danh sách barber có thể thực hiện dịch vụ đã chọn
- **Input**: Service ID
- **Output**: List of available barbers

#### Bước 3: Chọn ngày
- **Actor**: Customer
- **Mô tả**: Khách hàng chọn ngày muốn đặt lịch (từ calendar)
- **Input**: Selected date
- **Output**: Available time slots for selected date

#### Bước 4: Kiểm tra khung giờ có sẵn
- **Actor**: System
- **Mô tả**: 
  - Hệ thống kiểm tra lịch đã đặt của barber trong ngày
  - Tính toán thời lượng dịch vụ
  - Loại bỏ các khung giờ đã được đặt hoặc không đủ thời gian
- **Input**: Barber ID, Date, Service duration
- **Output**: List of available time slots

#### Bước 5: Chọn khung giờ
- **Actor**: Customer
- **Mô tả**: Khách hàng chọn khung giờ phù hợp từ danh sách có sẵn
- **Input**: Selected time slot
- **Output**: Booking confirmation form

#### Bước 6: Xác nhận đặt lịch
- **Actor**: Customer
- **Mô tả**: 
  - Khách hàng xem lại thông tin đặt lịch
  - Nhập ghi chú (nếu có)
  - Xác nhận đặt lịch
- **Input**: Booking details, Notes (optional)
- **Output**: Booking confirmation

#### Bước 7: Lưu đặt lịch
- **Actor**: System
- **Mô tả**: 
  - Lưu thông tin đặt lịch vào database
  - Trạng thái ban đầu: "pending"
  - Tạo notification cho khách hàng
  - Tạo notification cho barber (optional)
- **Input**: Booking data
- **Output**: Booking ID, Success message

#### Bước 8: Xác nhận tự động hoặc thủ công
- **Actor**: System/Admin
- **Mô tả**: 
  - Hệ thống có thể tự động xác nhận (auto-confirm)
  - Hoặc Admin xác nhận thủ công
  - Cập nhật trạng thái: "pending" → "confirmed"
- **Input**: Booking ID
- **Output**: Updated booking status

### 5.2. Quy trình hủy lịch (Cancellation Process)

#### Bước 1: Khách hàng yêu cầu hủy
- **Actor**: Customer
- **Mô tả**: Khách hàng vào trang lịch sử đặt lịch, chọn lịch muốn hủy
- **Input**: Booking ID

#### Bước 2: Kiểm tra chính sách hủy
- **Actor**: System
- **Mô tả**: 
  - Tính toán thời gian từ hiện tại đến thời gian đặt lịch
  - Nếu >= 2 giờ: Cho phép hủy
  - Nếu < 2 giờ: Từ chối, yêu cầu liên hệ trực tiếp
- **Input**: Current time, Booking time
- **Output**: Allow/Deny cancellation

#### Bước 3: Xác nhận hủy
- **Actor**: Customer
- **Mô tả**: Khách hàng xác nhận hủy lịch
- **Input**: Confirmation

#### Bước 4: Cập nhật trạng thái
- **Actor**: System
- **Mô tả**: 
  - Cập nhật trạng thái: "confirmed" → "cancelled"
  - Tạo notification cho khách hàng
  - Tạo notification cho barber
  - Giải phóng time slot
- **Input**: Booking ID
- **Output**: Updated status

### 5.3. Quy trình đánh giá (Review Process)

#### Bước 1: Hoàn thành dịch vụ
- **Actor**: Barber/Admin
- **Mô tả**: Sau khi hoàn thành dịch vụ, cập nhật trạng thái: "confirmed" → "completed"
- **Input**: Booking ID

#### Bước 2: Hệ thống gửi yêu cầu đánh giá
- **Actor**: System
- **Mô tả**: Tạo notification yêu cầu khách hàng đánh giá
- **Input**: Booking ID

#### Bước 3: Khách hàng đánh giá
- **Actor**: Customer
- **Mô tả**: 
  - Khách hàng chọn số sao (1-5)
  - Viết comment (optional)
  - Gửi đánh giá
- **Input**: Rating, Comment

#### Bước 4: Lưu đánh giá
- **Actor**: System
- **Mô tả**: 
  - Lưu đánh giá vào database
  - Cập nhật rating trung bình của barber
  - Cập nhật số lượng reviews
- **Input**: Review data
- **Output**: Updated barber rating

### 5.4. Quy trình quản lý dịch vụ (Service Management)

#### Bước 1: Admin đăng nhập
- **Actor**: Admin
- **Mô tả**: Admin đăng nhập vào hệ thống

#### Bước 2: Truy cập quản lý dịch vụ
- **Actor**: Admin
- **Mô tả**: Vào trang quản lý dịch vụ

#### Bước 3: Thêm/Sửa/Xóa dịch vụ
- **Actor**: Admin
- **Mô tả**: 
  - Thêm dịch vụ mới: Nhập thông tin, upload hình ảnh
  - Sửa dịch vụ: Cập nhật thông tin
  - Xóa dịch vụ: Xóa khỏi hệ thống (có kiểm tra ràng buộc)
- **Input**: Service data
- **Output**: Updated service list

#### Bước 4: Phân công barber cho dịch vụ
- **Actor**: Admin
- **Mô tả**: Chọn barber có thể thực hiện dịch vụ (many-to-many)
- **Input**: Service ID, Barber IDs
- **Output**: Updated service-barber relationships

---

## 6. API PLANNING

### 6.1. Authentication APIs

| Endpoint | Method | Mô tả | Request | Response |
|----------|--------|-------|---------|----------|
| `/api/login.php` | POST | Đăng nhập | `{email, password}` | `{token, user}` |
| `/api/register.php` | POST | Đăng ký | `{full_name, email, phone, password}` | `{success, message}` |
| `/api/logout.php` | POST | Đăng xuất | `{token}` | `{success}` |
| `/api/forgot-password.php` | POST | Quên mật khẩu | `{email}` | `{success, message}` |

### 6.2. Service APIs

| Endpoint | Method | Mô tả | Request | Response |
|----------|--------|-------|---------|----------|
| `/api/services.php` | GET | Lấy danh sách dịch vụ | `?featured=true` | `[{id, name, price, duration, image}]` |
| `/api/service-detail.php` | GET | Chi tiết dịch vụ | `?id=1` | `{id, name, description, price, duration, barbers[]}` |
| `/api/admin/services.php` | POST | Thêm dịch vụ | `{name, description, price, duration, image}` | `{success, service_id}` |
| `/api/admin/services.php` | PUT | Sửa dịch vụ | `{id, name, description, price, duration}` | `{success}` |
| `/api/admin/services.php` | DELETE | Xóa dịch vụ | `?id=1` | `{success}` |

### 6.3. Barber APIs

| Endpoint | Method | Mô tả | Request | Response |
|----------|--------|-------|---------|----------|
| `/api/barbers.php` | GET | Lấy danh sách barber | `?service_id=1` | `[{id, name, rating, experience}]` |
| `/api/admin/barbers.php` | POST | Thêm barber | `{user_id, specialization, experience}` | `{success, barber_id}` |
| `/api/admin/barbers.php` | PUT | Sửa barber | `{id, specialization, experience}` | `{success}` |
| `/api/admin/barber-services.php` | POST | Phân công dịch vụ | `{barber_id, service_ids[]}` | `{success}` |

### 6.4. Booking APIs

| Endpoint | Method | Mô tả | Request | Response |
|----------|--------|-------|---------|----------|
| `/api/get-available-slots.php` | GET | Lấy khung giờ có sẵn | `?barber_id=1&date=2024-01-15&service_id=1` | `{available_slots: ["09:00", "10:00", ...]}` |
| `/api/booking.php` | POST | Đặt lịch | `{service_id, barber_id, date, time, notes}` | `{success, booking_id}` |
| `/api/booking-history.php` | GET | Lịch sử đặt lịch | `?user_id=1&status=pending` | `[{id, service, barber, date, time, status}]` |
| `/api/cancel-booking.php` | POST | Hủy lịch | `{booking_id}` | `{success, message}` |
| `/api/admin/bookings.php` | GET | Quản lý đặt lịch | `?status=pending` | `[{id, user, service, barber, date, time, status}]` |
| `/api/admin/confirm-booking.php` | POST | Xác nhận lịch | `{booking_id}` | `{success}` |
| `/api/auto-confirm-bookings.php` | POST | Tự động xác nhận | `{}` | `{confirmed_count}` |

### 6.5. Review APIs

| Endpoint | Method | Mô tả | Request | Response |
|----------|--------|-------|---------|----------|
| `/api/reviews.php` | GET | Lấy đánh giá | `?service_id=1&barber_id=1` | `[{id, user, rating, comment, date}]` |
| `/api/review.php` | POST | Thêm đánh giá | `{booking_id, rating, comment}` | `{success, review_id}` |
| `/api/admin/reviews.php` | GET | Quản lý đánh giá | `?barber_id=1` | `[{id, user, service, rating, comment}]` |

### 6.6. Notification APIs

| Endpoint | Method | Mô tả | Request | Response |
|----------|--------|-------|---------|----------|
| `/api/notifications.php` | GET | Lấy thông báo | `?user_id=1&unread_only=true` | `[{id, type, title, message, is_read, date}]` |
| `/api/mark-notification-read.php` | POST | Đánh dấu đã đọc | `{notification_id}` | `{success}` |

### 6.7. QR Code APIs

| Endpoint | Method | Mô tả | Request | Response |
|----------|--------|-------|---------|----------|
| `/api/generate-qr.php` | GET | Tạo QR code | `?booking_id=1` | `{qr_code_image, booking_info}` |
| `/api/admin/qr-checkin.php` | POST | Check-in bằng QR | `{qr_data}` | `{success, booking_info}` |

### 6.8. Response Format Standard

**Success Response:**
```json
{
  "success": true,
  "data": {...},
  "message": "Operation successful"
}
```

**Error Response:**
```json
{
  "success": false,
  "error": "Error message",
  "code": "ERROR_CODE"
}
```

---

## 7. THIẾT KẾ GIAO DIỆN BAN ĐẦU

### 7.1. Trang chủ (Homepage)

**Layout:**
```
┌─────────────────────────────────────────────────┐
│              HEADER / NAVIGATION                │
│  [Logo] [Trang chủ] [Dịch vụ] [Đặt lịch] [Đăng nhập] │
├─────────────────────────────────────────────────┤
│                                                 │
│              HERO BANNER                        │
│         "TL Barber - Chuyên nghiệp"             │
│         [Nút: Đặt lịch ngay]                    │
│                                                 │
├─────────────────────────────────────────────────┤
│                                                 │
│         DỊCH VỤ NỔI BẬT                         │
│  ┌──────┐  ┌──────┐  ┌──────┐  ┌──────┐        │
│  │ Cắt  │  │ Nhuộm│  │ Gội  │  │ Tạo  │        │
│  │ tóc  │  │ tóc  │  │ đầu  │  │ kiểu │        │
│  │ 150k │  │ 300k │  │ 50k  │  │ 200k │        │
│  └──────┘  └──────┘  └──────┘  └──────┘        │
│                                                 │
├─────────────────────────────────────────────────┤
│                                                 │
│         ĐÁNH GIÁ TỪ KHÁCH HÀNG                  │
│  ┌──────────────────────────────────────┐      │
│  │ ⭐⭐⭐⭐⭐ "Dịch vụ tuyệt vời!"        │      │
│  │ - Nguyễn Văn A                        │      │
│  └──────────────────────────────────────┘      │
│                                                 │
├─────────────────────────────────────────────────┤
│                    FOOTER                       │
│  Thông tin liên hệ, địa chỉ, giờ làm việc      │
└─────────────────────────────────────────────────┘
```

**Màu sắc:**
- Primary: #2c3e50 (Dark blue-gray)
- Secondary: #e74c3c (Red accent)
- Background: #ecf0f1 (Light gray)
- Text: #34495e (Dark gray)

### 7.2. Trang đặt lịch (Booking Page)

**Layout:**
```
┌─────────────────────────────────────────────────┐
│              HEADER / NAVIGATION                │
├─────────────────────────────────────────────────┤
│                                                 │
│  BƯỚC 1: CHỌN DỊCH VỤ                           │
│  ┌──────────────────────────────────────┐      │
│  │ ○ Cắt tóc nam - 150,000đ - 30 phút   │      │
│  │ ● Cắt tóc nữ - 200,000đ - 45 phút    │      │
│  │ ○ Nhuộm tóc - 300,000đ - 90 phút     │      │
│  └──────────────────────────────────────┘      │
│                                                 │
│  BƯỚC 2: CHỌN BARBER                            │
│  ┌──────────────────────────────────────┐      │
│  │ ○ Nguyễn Văn A ⭐⭐⭐⭐⭐ (5 năm)      │      │
│  │ ● Trần Thị B ⭐⭐⭐⭐ (3 năm)          │      │
│  └──────────────────────────────────────┘      │
│                                                 │
│  BƯỚC 3: CHỌN NGÀY                              │
│  ┌──────────────────────────────────────┐      │
│  │ [Calendar Widget]                     │      │
│  │   Tháng 1, 2024                      │      │
│  │   S  M  T  W  T  F  S                │      │
│  │        1  2  3  4  5                 │      │
│  │   6  7  8  9 10 11 12                │      │
│  │  13 14 15 16 17 18 19                │      │
│  └──────────────────────────────────────┘      │
│                                                 │
│  BƯỚC 4: CHỌN GIỜ                              │
│  ┌──────────────────────────────────────┐      │
│  │ Khung giờ có sẵn:                    │      │
│  │ [09:00] [10:00] [11:00] [14:00]      │      │
│  │ [15:00] [16:00] [17:00]              │      │
│  └──────────────────────────────────────┘      │
│                                                 │
│  Ghi chú (tùy chọn):                            │
│  ┌──────────────────────────────────────┐      │
│  │ [Text area]                          │      │
│  └──────────────────────────────────────┘      │
│                                                 │
│  [Nút: Xác nhận đặt lịch]                      │
│                                                 │
└─────────────────────────────────────────────────┘
```

### 7.3. Trang quản trị (Admin Dashboard)

**Layout:**
```
┌─────────────────────────────────────────────────┐
│         ADMIN HEADER                            │
│  [Logo] [Dashboard] [Dịch vụ] [Barber] [Lịch]  │
├──────────┬──────────────────────────────────────┤
│          │                                      │
│ SIDEBAR  │        MAIN CONTENT AREA             │
│          │                                      │
│ Dashboard│  ┌──────────────────────────────┐   │
│ Dịch vụ  │  │  TỔNG QUAN                   │   │
│ Barber   │  │  ┌──────┐  ┌──────┐         │   │
│ Đặt lịch │  │  │ 150  │  │  45  │         │   │
│ Đánh giá │  │  │ Đặt  │  │ Xác  │         │   │
│ Settings │  │  │ lịch │  │ nhận │         │   │
│          │  │  └──────┘  └──────┘         │   │
│          │  │                              │   │
│          │  │  [Table: Danh sách đặt lịch] │   │
│          │  └──────────────────────────────┘   │
│          │                                      │
└──────────┴──────────────────────────────────────┘
```

### 7.4. Responsive Design

**Breakpoints:**
- Desktop: >= 1024px (Full layout)
- Tablet: 768px - 1023px (Adapted layout)
- Mobile: < 768px (Stacked layout, hamburger menu)

**Mobile Features:**
- Hamburger menu
- Touch-friendly buttons
- Swipeable cards
- Optimized forms

### 7.5. UI Components

**Buttons:**
- Primary: Solid background, white text
- Secondary: Outlined, colored border
- Danger: Red background for delete/cancel actions

**Forms:**
- Input fields with labels
- Validation messages
- Loading states
- Success/Error notifications

**Cards:**
- Service cards with image, title, price
- Barber cards with avatar, rating, experience
- Booking cards with status badges

**Modals:**
- Confirmation dialogs
- Form modals for CRUD operations
- Alert messages

---

## 8. TỔNG KẾT

### 8.1. Điểm mạnh của hệ thống

1. **Tự động hóa cao**: Kiểm tra trùng lịch, xác nhận tự động, thông báo tự động
2. **Bảo mật tốt**: JWT authentication, password hashing, SQL injection prevention
3. **Giao diện thân thiện**: Responsive design, dễ sử dụng
4. **Mở rộng được**: Kiến trúc modular, dễ thêm tính năng mới
5. **Quản lý hiệu quả**: Admin panel đầy đủ, QR code check-in

### 8.2. Hướng phát triển tương lai

1. **Mobile App**: Phát triển ứng dụng di động native
2. **Payment Integration**: Tích hợp thanh toán trực tuyến
3. **Real-time Chat**: Chat trực tiếp với barber
4. **Loyalty Program**: Chương trình tích điểm, giảm giá
5. **Analytics Dashboard**: Báo cáo chi tiết, thống kê doanh thu
6. **Multi-location**: Hỗ trợ nhiều chi nhánh
7. **Email/SMS Notifications**: Thông báo qua email và SMS

---

**Tài liệu được tạo bởi:** TL Barber Development Team  
**Ngày tạo:** 2024  
**Phiên bản:** 1.0

