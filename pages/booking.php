<?php
$page_title = 'Đặt lịch';
require_once __DIR__ . '/../includes/header.php';

if (!$current_user) {
    redirect(BASE_URL . 'pages/login.php');
}

$pdo = getDBConnection();
$error = '';
$success = '';

// Get selected service if provided
$selected_service_id = $_GET['service_id'] ?? 0;

// Get all services
$stmt = $pdo->query("SELECT * FROM services ORDER BY name");
$services = $stmt->fetchAll();

// Get all barbers
$stmt = $pdo->query("
    SELECT b.*, u.full_name
    FROM barbers b
    JOIN users u ON b.user_id = u.id
    WHERE b.is_available = 1
    ORDER BY b.rating DESC
");
$barbers = $stmt->fetchAll();

// Get time slots
$stmt = $pdo->query("SELECT start_time, end_time FROM time_slots WHERE is_active = 1 ORDER BY start_time");
$time_slots = $stmt->fetchAll();

// Handle booking submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $service_id = $_POST['service_id'] ?? 0;
    $barber_id = $_POST['barber_id'] ?? 0;
    $booking_date = $_POST['booking_date'] ?? '';
    $booking_time = $_POST['booking_time'] ?? '';
    $notes = sanitize($_POST['notes'] ?? '');
    
    if (empty($service_id) || empty($barber_id) || empty($booking_date) || empty($booking_time)) {
        $error = 'Vui lòng điền đầy đủ thông tin';
    } else {
        // Check if slot is available
        $stmt = $pdo->prepare("
            SELECT id FROM bookings 
            WHERE barber_id = ? 
            AND booking_date = ? 
            AND booking_time = ? 
            AND status IN ('pending', 'confirmed')
        ");
        $stmt->execute([$barber_id, $booking_date, $booking_time]);
        
        if ($stmt->fetch()) {
            $error = 'Khung giờ này đã được đặt. Vui lòng chọn khung giờ khác.';
        } else {
            // Create booking
            $stmt = $pdo->prepare("
                INSERT INTO bookings (user_id, service_id, barber_id, booking_date, booking_time, notes, status)
                VALUES (?, ?, ?, ?, ?, ?, 'pending')
            ");
            
            if ($stmt->execute([$current_user['id'], $service_id, $barber_id, $booking_date, $booking_time, $notes])) {
                $booking_id = $pdo->lastInsertId();
                
                // Create notification
                $service_stmt = $pdo->prepare("SELECT name FROM services WHERE id = ?");
                $service_stmt->execute([$service_id]);
                $service_name = $service_stmt->fetch()['name'];
                
                $stmt = $pdo->prepare("
                    INSERT INTO notifications (user_id, booking_id, type, title, message)
                    VALUES (?, ?, 'booking_created', 'Đặt lịch thành công', ?)
                ");
                $message = "Bạn đã đặt lịch dịch vụ \"{$service_name}\" thành công. Chúng tôi sẽ xác nhận sớm nhất có thể.";
                $stmt->execute([$current_user['id'], $booking_id, $message]);
                
                $success = 'Đặt lịch thành công!';
                echo "<script>setTimeout(function() { window.location.href = '" . BASE_URL . "pages/booking-history.php'; }, 2000);</script>";
            } else {
                $error = 'Có lỗi xảy ra, vui lòng thử lại';
            }
        }
    }
}
?>

<section class="section">
    <div class="container">
        <h2 class="section-title">Đặt lịch dịch vụ</h2>
        
        <?php if ($error): ?>
            <div class="alert" style="background: var(--danger-color); color: white; padding: 1rem; border-radius: 5px; margin-bottom: 1rem; max-width: 800px; margin-left: auto; margin-right: auto;">
                <i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>
        
        <?php if ($success): ?>
            <div class="alert" style="background: var(--success-color); color: white; padding: 1rem; border-radius: 5px; margin-bottom: 1rem; max-width: 800px; margin-left: auto; margin-right: auto;">
                <i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($success); ?>
            </div>
        <?php endif; ?>
        
        <form method="POST" action="" id="booking-form" style="max-width: 800px; margin: 0 auto;">
            <!-- Step 1: Select Service -->
            <div class="card" style="margin-bottom: 2rem;">
                <div class="card-body">
                    <h3 style="color: var(--primary-color); margin-bottom: 1rem;">
                        <span class="step-number" style="display: inline-block; width: 30px; height: 30px; background: var(--primary-color); color: white; border-radius: 50%; text-align: center; line-height: 30px; margin-right: 10px;">1</span>
                        Chọn dịch vụ
                    </h3>
                    <div class="form-group">
                        <label for="service_id"><i class="fas fa-cut"></i> Dịch vụ *</label>
                        <select id="service_id" name="service_id" class="form-control" required onchange="updateBarbers()">
                            <option value="">-- Chọn dịch vụ --</option>
                            <?php foreach ($services as $service): ?>
                                <option value="<?php echo $service['id']; ?>" 
                                        <?php echo ($selected_service_id == $service['id']) ? 'selected' : ''; ?>
                                        data-duration="<?php echo $service['duration']; ?>">
                                    <?php echo htmlspecialchars($service['name']); ?> - <?php echo formatPrice($service['price']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
            </div>
            
            <!-- Step 2: Select Barber -->
            <div class="card" style="margin-bottom: 2rem;">
                <div class="card-body">
                    <h3 style="color: var(--primary-color); margin-bottom: 1rem;">
                        <span class="step-number" style="display: inline-block; width: 30px; height: 30px; background: var(--primary-color); color: white; border-radius: 50%; text-align: center; line-height: 30px; margin-right: 10px;">2</span>
                        Chọn barber
                    </h3>
                    <div class="form-group">
                        <label for="barber_id"><i class="fas fa-user-tie"></i> Barber *</label>
                        <select id="barber_id" name="barber_id" class="form-control" required onchange="updateAvailableSlots()">
                            <option value="">-- Chọn barber --</option>
                            <?php foreach ($barbers as $barber): ?>
                                <option value="<?php echo $barber['id']; ?>">
                                    <?php echo htmlspecialchars($barber['full_name']); ?> 
                                    (<?php echo round($barber['rating'], 1); ?>⭐ - <?php echo $barber['total_reviews']; ?> đánh giá)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
            </div>
            
            <!-- Step 3: Select Date -->
            <div class="card" style="margin-bottom: 2rem;">
                <div class="card-body">
                    <h3 style="color: var(--primary-color); margin-bottom: 1rem;">
                        <span class="step-number" style="display: inline-block; width: 30px; height: 30px; background: var(--primary-color); color: white; border-radius: 50%; text-align: center; line-height: 30px; margin-right: 10px;">3</span>
                        Chọn ngày
                    </h3>
                    <div class="form-group">
                        <label for="booking_date"><i class="fas fa-calendar"></i> Ngày đặt lịch *</label>
                        <input type="date" id="booking_date" name="booking_date" class="form-control" required 
                               min="<?php echo date('Y-m-d'); ?>" 
                               onchange="updateAvailableSlots()">
                    </div>
                </div>
            </div>
            
            <!-- Step 4: Select Time -->
            <div class="card" style="margin-bottom: 2rem;">
                <div class="card-body">
                    <h3 style="color: var(--primary-color); margin-bottom: 1rem;">
                        <span class="step-number" style="display: inline-block; width: 30px; height: 30px; background: var(--primary-color); color: white; border-radius: 50%; text-align: center; line-height: 30px; margin-right: 10px;">4</span>
                        Chọn khung giờ
                    </h3>
                    <div id="time-slots-container">
                        <p style="color: var(--text-light); text-align: center; padding: 2rem;">
                            Vui lòng chọn barber và ngày để xem khung giờ có sẵn
                        </p>
                    </div>
                    <input type="hidden" id="selected_time" name="booking_time" required>
                </div>
            </div>
            
            <!-- Step 5: Notes -->
            <div class="card" style="margin-bottom: 2rem;">
                <div class="card-body">
                    <h3 style="color: var(--primary-color); margin-bottom: 1rem;">
                        <span class="step-number" style="display: inline-block; width: 30px; height: 30px; background: var(--primary-color); color: white; border-radius: 50%; text-align: center; line-height: 30px; margin-right: 10px;">5</span>
                        Ghi chú (tùy chọn)
                    </h3>
                    <div class="form-group">
                        <label for="notes"><i class="fas fa-sticky-note"></i> Ghi chú</label>
                        <textarea id="notes" name="notes" class="form-control" rows="3" 
                                  placeholder="Ví dụ: Cắt kiểu fade, để lại phần trên dài..."></textarea>
                    </div>
                </div>
            </div>
            
            <div class="text-center">
                <button type="submit" class="btn btn-primary" style="font-size: 1.1rem; padding: 15px 40px;">
                    <i class="fas fa-calendar-check"></i> Xác nhận đặt lịch
                </button>
            </div>
        </form>
    </div>
</section>

<script>
// Generate time slots HTML
const timeSlotsData = <?php echo json_encode($time_slots); ?>;

function generateTimeSlots(availableSlots = [], bookedSlots = []) {
    const container = document.getElementById('time-slots-container');
    const selectedTimeInput = document.getElementById('selected_time');
    
    if (!timeSlotsData || timeSlotsData.length === 0) {
        container.innerHTML = '<p style="color: var(--text-light); text-align: center; padding: 2rem;">Không có khung giờ nào</p>';
        return;
    }
    
    let html = '<div class="time-slots-grid">';
    
    timeSlotsData.forEach(slot => {
        const startTime = slot.start_time.substring(0, 5); // HH:MM format
        const endTime = slot.end_time.substring(0, 5);
        const isBooked = bookedSlots.includes(startTime);
        const isAvailable = availableSlots.length === 0 || availableSlots.includes(startTime);
        
        html += `<div class="time-slot ${isBooked ? 'disabled' : ''} ${isAvailable && !isBooked ? '' : 'disabled'}" 
                     data-time="${startTime}"
                     onclick="selectTimeSlot(this)">
                    ${startTime} - ${endTime}
                </div>`;
    });
    
    html += '</div>';
    container.innerHTML = html;
    
    // Reset selected time
    selectedTimeInput.value = '';
}

function updateAvailableSlots() {
    const dateInput = document.getElementById('booking_date');
    const barberSelect = document.getElementById('barber_id');
    
    if (!dateInput.value || !barberSelect.value) {
        generateTimeSlots();
        return;
    }
    
    fetch(`<?php echo BASE_URL; ?>api/get-available-slots.php?date=${dateInput.value}&barber_id=${barberSelect.value}`)
        .then(response => response.json())
        .then(data => {
            generateTimeSlots(data.available_slots || [], data.booked_slots || []);
        })
        .catch(error => {
            console.error('Error:', error);
            generateTimeSlots();
        });
}

function selectTimeSlot(element) {
    if (element.classList.contains('disabled')) return;
    
    document.querySelectorAll('.time-slot').forEach(slot => {
        slot.classList.remove('selected');
    });
    
    element.classList.add('selected');
    document.getElementById('selected_time').value = element.dataset.time;
}

// Initialize
generateTimeSlots();

// Update slots when date or barber changes
document.getElementById('booking_date')?.addEventListener('change', updateAvailableSlots);
document.getElementById('barber_id')?.addEventListener('change', updateAvailableSlots);
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>

