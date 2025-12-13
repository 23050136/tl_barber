<?php
$page_title = 'Thêm/Sửa dịch vụ';
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/admin-auth.php';

$admin = checkAdminAuth();
$pdo = getDBConnection();
$error = '';
$success = '';

$service_id = $_GET['id'] ?? 0;
$service = null;

if ($service_id) {
    $stmt = $pdo->prepare("SELECT * FROM services WHERE id = ?");
    $stmt->execute([$service_id]);
    $service = $stmt->fetch();
    if (!$service) {
        redirect(BASE_URL . 'admin/services.php');
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = sanitize($_POST['name'] ?? '');
    $description = sanitize($_POST['description'] ?? '');
    $price = floatval($_POST['price'] ?? 0);
    $duration = intval($_POST['duration'] ?? 0);
    $is_featured = isset($_POST['is_featured']) ? 1 : 0;
    
    // Handle image upload or URL
    $old_image = $service['image'] ?? '';
    $image_result = handleImageUpload($_FILES['image_file'] ?? [], $old_image);
    
    if (is_array($image_result) && isset($image_result['error'])) {
        $error = $image_result['error'];
    } else {
        $image = $image_result;
        
        if (empty($name) || $price <= 0 || $duration <= 0) {
            $error = 'Vui lòng điền đầy đủ thông tin';
        } else {
            if ($service_id && $service) {
                // Update
                $stmt = $pdo->prepare("
                    UPDATE services 
                    SET name = ?, description = ?, price = ?, duration = ?, is_featured = ?, image = ?
                    WHERE id = ?
                ");
                if ($stmt->execute([$name, $description, $price, $duration, $is_featured, $image, $service_id])) {
                    $success = 'Cập nhật dịch vụ thành công';
                    $service = $pdo->prepare("SELECT * FROM services WHERE id = ?");
                    $service->execute([$service_id]);
                    $service = $service->fetch();
                } else {
                    $error = 'Có lỗi xảy ra';
                }
            } else {
                // Insert
                $stmt = $pdo->prepare("
                    INSERT INTO services (name, description, price, duration, is_featured, image)
                    VALUES (?, ?, ?, ?, ?, ?)
                ");
                if ($stmt->execute([$name, $description, $price, $duration, $is_featured, $image])) {
                    $success = 'Thêm dịch vụ thành công';
                    redirect(BASE_URL . 'admin/services.php');
                } else {
                    $error = 'Có lỗi xảy ra';
                }
            }
        }
    }
}
?>

<div class="section">
    <div class="container">
        <div class="form-container" style="max-width: 700px;">
            <h2 class="text-center mb-2">
                <i class="fas fa-<?php echo $service ? 'edit' : 'plus'; ?>"></i> 
                <?php echo $service ? 'Sửa dịch vụ' : 'Thêm dịch vụ mới'; ?>
            </h2>
            
            <div style="text-align: center; margin-bottom: 1rem;">
                <a href="<?php echo BASE_URL; ?>admin/services.php" style="color: var(--primary-color); text-decoration: none;">
                    <i class="fas fa-arrow-left"></i> Quay lại danh sách
                </a>
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
            
            <form method="POST" action="" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="name"><i class="fas fa-tag"></i> Tên dịch vụ *</label>
                    <input type="text" id="name" name="name" class="form-control" required
                           value="<?php echo htmlspecialchars($service['name'] ?? $_POST['name'] ?? ''); ?>">
                </div>
                
                <div class="form-group">
                    <label for="description"><i class="fas fa-align-left"></i> Mô tả</label>
                    <textarea id="description" name="description" class="form-control" rows="4"><?php echo htmlspecialchars($service['description'] ?? $_POST['description'] ?? ''); ?></textarea>
                </div>
                
                <div class="form-group">
                    <label><i class="fas fa-image"></i> Ảnh dịch vụ</label>
                    
                    <!-- Current image preview -->
                    <?php if (!empty($service['image'])): ?>
                        <div style="margin-bottom: 1rem; padding: 1rem; background: var(--bg-light); border-radius: 5px;">
                            <p style="margin-bottom: 0.5rem; font-weight: bold;">Ảnh hiện tại:</p>
                            <img src="<?php echo getImageUrl($service['image']); ?>" 
                                 alt="Preview" 
                                 style="max-width: 200px; max-height: 200px; object-fit: cover; border-radius: 5px; border: 2px solid var(--border-color);">
                        </div>
                    <?php endif; ?>
                    
                    <!-- Upload from computer -->
                    <div style="margin-bottom: 1rem;">
                        <label for="image_file" style="display: block; margin-bottom: 0.5rem; font-weight: bold;">
                            <i class="fas fa-upload"></i> Tải ảnh lên từ máy tính
                        </label>
                        <input type="file" 
                               id="image_file" 
                               name="image_file" 
                               accept="image/jpeg,image/jpg,image/png,image/gif,image/webp"
                               class="form-control"
                               onchange="previewImage(this)">
                        <small style="color: var(--text-light); display: block; margin-top: 0.5rem;">
                            Chấp nhận: JPG, PNG, GIF, WEBP (tối đa 5MB)
                        </small>
                        <div id="image_preview" style="margin-top: 1rem; display: none;">
                            <img id="preview_img" src="" alt="Preview" style="max-width: 200px; max-height: 200px; object-fit: cover; border-radius: 5px; border: 2px solid var(--border-color);">
                        </div>
                    </div>
                    
                    <!-- OR separator -->
                    <div style="text-align: center; margin: 1rem 0; position: relative;">
                        <hr style="border: none; border-top: 1px solid var(--border-color);">
                        <span style="background: white; padding: 0 1rem; color: var(--text-light); position: relative; top: -10px;">HOẶC</span>
                    </div>
                    
                    <!-- Enter URL from Google or other sources -->
                    <div>
                        <label for="image_url" style="display: block; margin-bottom: 0.5rem; font-weight: bold;">
                            <i class="fas fa-link"></i> Nhập link ảnh từ Google hoặc nguồn khác
                        </label>
                        <input type="url" 
                               id="image_url" 
                               name="image_url" 
                               class="form-control"
                               placeholder="https://example.com/image.jpg hoặc đường dẫn tương đối"
                               value="<?php echo (!empty($service['image']) && filter_var($service['image'], FILTER_VALIDATE_URL)) ? htmlspecialchars($service['image']) : ''; ?>">
                        <small style="color: var(--text-light); display: block; margin-top: 0.5rem;">
                            Dán link ảnh từ Google Images hoặc bất kỳ nguồn nào. Hệ thống sẽ ưu tiên file upload nếu có.
                        </small>
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                    <div class="form-group">
                        <label for="price"><i class="fas fa-money-bill-wave"></i> Giá (VNĐ) *</label>
                        <input type="number" id="price" name="price" class="form-control" required min="0" step="1000"
                               value="<?php echo $service['price'] ?? $_POST['price'] ?? ''; ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="duration"><i class="fas fa-clock"></i> Thời lượng (phút) *</label>
                        <input type="number" id="duration" name="duration" class="form-control" required min="1"
                               value="<?php echo $service['duration'] ?? $_POST['duration'] ?? ''; ?>">
                    </div>
                </div>
                
                <div class="form-group">
                    <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer;">
                        <input type="checkbox" name="is_featured" value="1" 
                               <?php echo ($service['is_featured'] ?? false) ? 'checked' : ''; ?>>
                        <span>Đánh dấu là dịch vụ nổi bật</span>
                    </label>
                </div>
                
                <button type="submit" class="btn btn-primary" style="width: 100%;">
                    <i class="fas fa-save"></i> <?php echo $service ? 'Cập nhật' : 'Thêm mới'; ?>
                </button>
            </form>
        </div>
    </div>
</div>

<script>
function previewImage(input) {
    const preview = document.getElementById('image_preview');
    const previewImg = document.getElementById('preview_img');
    
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        
        reader.onload = function(e) {
            previewImg.src = e.target.result;
            preview.style.display = 'block';
        }
        
        reader.readAsDataURL(input.files[0]);
    } else {
        preview.style.display = 'none';
    }
}
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>

