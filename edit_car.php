<?php
// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Start session
session_start();

// Check authentication
if (!isset($_SESSION['user_id'])) {
    die("You must be logged in.");
}
$user_id = $_SESSION['user_id'];

// Database connection
$host = "sql206.infinityfree.com";
$user = "if0_39046487";
$pass = "111897Mail";
$db   = "if0_39046487_epiz_12345678_easycar";

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get car ID from URL
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: my_cars.php");
    exit();
}
$car_id = (int)$_GET['id'];

// Fetch car details and verify ownership
$sql = "SELECT * FROM car_listings WHERE id = ? AND name = (SELECT name FROM users WHERE id = ?)";
$stmt = $conn->prepare($sql);
if (!$stmt) {
    die("Prepare failed: " . $conn->error);
}
$stmt->bind_param("ii", $car_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header("Location: my_cars.php");
    exit();
}
$car = $result->fetch_assoc();
$existing_photos = json_decode($car['photos'], true) ?: [];

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize and validate input
    $make = trim($_POST['make'] ?? '');
    $model = trim($_POST['model'] ?? '');
    $year = (int)($_POST['year'] ?? 0);
    $price = (float)($_POST['price'] ?? 0);
    $mileage = (int)($_POST['mileage'] ?? 0);
    $fuel = trim($_POST['fuel'] ?? '');
    $transmission = trim($_POST['transmission'] ?? '');
    $body_type = trim($_POST['body_type'] ?? '');
    $car_condition = trim($_POST['car_condition'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $location = trim($_POST['location'] ?? '');
    $errors = [];

    // Validation
    if (empty($make)) $errors[] = "Make is required";
    if (empty($model)) $errors[] = "Model is required";
    if ($year < 1900 || $year > date('Y') + 1) $errors[] = "Please enter a valid year";
    if ($price <= 0) $errors[] = "Please enter a valid price";
    if ($mileage < 0) $errors[] = "Please enter a valid mileage";
    if (empty($fuel)) $errors[] = "Fuel type is required";
    if (empty($transmission)) $errors[] = "Transmission is required";
    if (empty($car_condition)) $errors[] = "Car condition is required";
    if (empty($name)) $errors[] = "Name is required";
    if (empty($email)) $errors[] = "Email is required";
    if (empty($phone)) $errors[] = "Phone is required";
    if (empty($location)) $errors[] = "Location is required";

    // Handle photo uploads
    $photos = $existing_photos;
    if (isset($_FILES['photos']) && !empty($_FILES['photos']['name'][0])) {
        $upload_dir = 'uploads/';
        if (!file_exists($upload_dir)) {
            if (!mkdir($upload_dir, 0755, true)) {
                $errors[] = "Failed to create upload directory";
            }
        }
        $allowed_types = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
        $max_file_size = 5 * 1024 * 1024; // 5MB
        for ($i = 0; $i < count($_FILES['photos']['name']); $i++) {
            if ($_FILES['photos']['error'][$i] === UPLOAD_ERR_OK) {
                $file_type = $_FILES['photos']['type'][$i];
                $file_size = $_FILES['photos']['size'][$i];
                if (!in_array($file_type, $allowed_types)) {
                    $errors[] = "Invalid file type for " . $_FILES['photos']['name'][$i] . ". Only JPEG, PNG, and GIF are allowed.";
                    continue;
                }
                if ($file_size > $max_file_size) {
                    $errors[] = "File size too large for " . $_FILES['photos']['name'][$i] . ". Maximum 5MB allowed.";
                    continue;
                }
                $file_extension = pathinfo($_FILES['photos']['name'][$i], PATHINFO_EXTENSION);
                $new_filename = uniqid() . '.' . $file_extension;
                $upload_path = $upload_dir . $new_filename;
                if (move_uploaded_file($_FILES['photos']['tmp_name'][$i], $upload_path)) {
                    $photos[] = $upload_path;
                } else {
                    $errors[] = "Failed to upload photo: " . $_FILES['photos']['name'][$i];
                }
            } elseif ($_FILES['photos']['error'][$i] !== UPLOAD_ERR_NO_FILE) {
                $errors[] = "Upload error for " . $_FILES['photos']['name'][$i] . ": " . $_FILES['photos']['error'][$i];
            }
        }
    }

    // Handle photo removal
    if (isset($_POST['remove_photos']) && is_array($_POST['remove_photos'])) {
        foreach ($_POST['remove_photos'] as $photo_to_remove) {
            $key = array_search($photo_to_remove, $photos);
            if ($key !== false) {
                if (file_exists($photo_to_remove)) {
                    unlink($photo_to_remove);
                }
                unset($photos[$key]);
            }
        }
        $photos = array_values($photos); // Reindex array
    }

    if (empty($errors)) {
        // Update car listing
        $photos_json = json_encode(array_values($photos));
        $update_sql = "UPDATE car_listings SET 
                       make = ?, model = ?, year = ?, price = ?, mileage = ?, 
                       fuel = ?, transmission = ?, body_type = ?, car_condition = ?,
                       description = ?, name = ?, email = ?, phone = ?, 
                       location = ?, photos = ?, updated_at = NOW() 
                       WHERE id = ?";
        $update_stmt = $conn->prepare($update_sql);
        if (!$update_stmt) {
            $errors[] = "Database prepare error: " . $conn->error;
        } else {
            // Fix: Make sure all types match (s=string, i=integer, d=double)
            $bind_result = $update_stmt->bind_param(
                "ssidissssssssssi",
                $make, $model, $year, $price, $mileage,
                $fuel, $transmission, $body_type, $car_condition,
                $description, $name, $email, $phone,
                $location, $photos_json, $car_id
            );
            if (!$bind_result) {
                $errors[] = "Parameter binding failed: " . $update_stmt->error;
            } else {
                if ($update_stmt->execute()) {
                    echo '
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle"></i> Car listing updated successfully!
                    </div>
                    <script>
                        setTimeout(function() {
                            window.location.href = "car_details.php?id=' . $car_id . '";
                        }, 3000); // 3 seconds delay
                    </script>';
                    exit();
                } else {
                    $errors[] = "Update execution failed: " . $update_stmt->error;
                }
                $update_stmt->close();
            }
        }
    }
}
$stmt->close();
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Car Listing</title>
    <!-- Include FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css"> 
    <!-- Include CryptoJS for MD5 hashing -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/crypto-js/4.1.1/crypto-js.min.js"></script> 
    <style>
        /* Your existing CSS styles here */
    </style>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Car Listing - EasyCar.my</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        /* Base Styles */
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            background-color: #f5f5f5;
            color: #333;
            line-height: 1.6;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }

        /* Header Styles */
        .section-header {
            text-align: center;
            margin-bottom: 30px;
            padding-top: 20px;
        }

        .section-title {
            font-size: 2rem;
            color: #2c3e50;
            margin-bottom: 10px;
        }

        .section-subtitle {
            color: #7f8c8d;
            font-size: 1rem;
        }

        /* Alert Styles */
        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 4px;
            display: flex;
            align-items: center;
        }

        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .alert-danger {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .alert i {
            margin-right: 10px;
            font-size: 1.2rem;
        }

        .error-list {
            margin-top: 10px;
            padding-left: 20px;
        }

        /* Form Styles */
        .form-container {
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            padding: 30px;
            margin-bottom: 40px;
        }

        .form-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 20px;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group.full-width {
            grid-column: 1 / -1;
        }

        .form-label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #2c3e50;
        }

        .form-input, .form-select, .form-textarea {
            width: 100%;
            padding: 10px 15px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 1rem;
            transition: border-color 0.3s;
        }

        .form-input:focus, .form-select:focus, .form-textarea:focus {
            border-color: #3498db;
            outline: none;
        }

        .form-textarea {
            min-height: 120px;
            resize: vertical;
        }

        /* Button Styles */
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 10px 20px;
            border-radius: 4px;
            font-size: 1rem;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s;
            text-decoration: none;
            border: none;
        }

        .btn i {
            margin-right: 8px;
        }

        .btn-primary {
            background-color: #3498db;
            color: white;
        }

        .btn-primary:hover {
            background-color: #2980b9;
        }

        .btn-secondary {
            background-color: #95a5a6;
            color: white;
        }

        .btn-secondary:hover {
            background-color: #7f8c8d;
        }

        .form-actions {
            display: flex;
            justify-content: space-between;
            margin-top: 30px;
        }

        /* Photo Section Styles */
        .photo-section {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #eee;
        }

        .photo-section h3 {
            margin-bottom: 15px;
            color: #2c3e50;
        }

        .existing-photos {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
            gap: 15px;
            margin-bottom: 20px;
        }

        .photo-item {
            position: relative;
            height: 150px;
            border-radius: 4px;
            overflow: hidden;
            transition: all 0.3s;
        }

        .photo-item img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .photo-remove {
            position: absolute;
            top: 5px;
            right: 5px;
            width: 25px;
            height: 25px;
            background-color: rgba(220, 53, 69, 0.8);
            color: white;
            border: none;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.3s;
        }

        .photo-remove:hover {
            background-color: #dc3545;
        }

        /* File Input Styles */
        .file-input-wrapper {
            margin-top: 15px;
        }

        .file-input {
            width: 0.1px;
            height: 0.1px;
            opacity: 0;
            overflow: hidden;
            position: absolute;
            z-index: -1;
        }

        .file-input-label {
            display: inline-flex;
            align-items: center;
            padding: 10px 20px;
            background-color: #f8f9fa;
            border: 1px dashed #95a5a6;
            border-radius: 4px;
            color: #7f8c8d;
            cursor: pointer;
            transition: all 0.3s;
        }

        .file-input-label:hover {
            background-color: #e9ecef;
            border-color: #7f8c8d;
        }

        /* Responsive Adjustments */
        @media (max-width: 768px) {
            .form-grid {
                grid-template-columns: 1fr;
            }
            
            .existing-photos {
                grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
            }
            
            .form-actions {
                flex-direction: column;
                gap: 10px;
            }
            
            .btn {
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <!-- Header (keep your existing header code) -->

    <main class="edit-section">
        <div class="container">
            <div class="section-header">
                <h1 class="section-title">Edit Car Listing</h1>
                <p class="section-subtitle">Update your car listing details</p>
            </div>

            <?php if (isset($success_msg)): ?>
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i> <?= htmlspecialchars($success_msg) ?>
                </div>
            <?php endif; ?>

            <?php if (!empty($errors)): ?>
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-triangle"></i>
                    <strong>Please fix the following errors:</strong>
                    <ul class="error-list">
                        <?php foreach ($errors as $error): ?>
                            <li><?= htmlspecialchars($error) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <div class="form-container">
                <form method="POST" enctype="multipart/form-data">
                    <div class="form-grid">
                        <!-- Basic Car Info -->
                        <div class="form-group">
                            <label for="make" class="form-label">Make *</label>
                            <input type="text" id="make" name="make" class="form-input" 
                                   value="<?= htmlspecialchars($car['make']) ?>" required>
                        </div>

                        <div class="form-group">
                            <label for="model" class="form-label">Model *</label>
                            <input type="text" id="model" name="model" class="form-input" 
                                   value="<?= htmlspecialchars($car['model']) ?>" required>
                        </div>

                        <div class="form-group">
                            <label for="year" class="form-label">Year *</label>
                            <input type="number" id="year" name="year" class="form-input" 
                                   min="1900" max="<?= date('Y') + 1 ?>" 
                                   value="<?= $car['year'] ?>" required>
                        </div>

                        <div class="form-group">
                            <label for="price" class="form-label">Price (RM) *</label>
                            <input type="number" id="price" name="price" class="form-input" 
                                   min="0" step="0.01" value="<?= $car['price'] ?>" required>
                        </div>

                        <div class="form-group">
                            <label for="mileage" class="form-label">Mileage (km) *</label>
                            <input type="number" id="mileage" name="mileage" class="form-input" 
                                   min="0" value="<?= $car['mileage'] ?>" required>
                        </div>

                        <!-- Car Specifications -->
                        <div class="form-group">
                            <label for="fuel" class="form-label">Fuel Type *</label>
                            <select id="fuel" name="fuel" class="form-select" required>
                                <option value="">Select Fuel Type</option>
                                <option value="gasoline" <?= $car['fuel'] === 'gasoline' ? 'selected' : '' ?>>Gasoline</option>
                                <option value="diesel" <?= $car['fuel'] === 'diesel' ? 'selected' : '' ?>>Diesel</option>
                                <option value="hybrid" <?= $car['fuel'] === 'hybrid' ? 'selected' : '' ?>>Hybrid</option>
                                <option value="electric" <?= $car['fuel'] === 'electric' ? 'selected' : '' ?>>Electric</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="transmission" class="form-label">Transmission *</label>
                            <select id="transmission" name="transmission" class="form-select" required>
                                <option value="">Select Transmission</option>
                                <option value="manual" <?= $car['transmission'] === 'manual' ? 'selected' : '' ?>>Manual</option>
                                <option value="automatic" <?= $car['transmission'] === 'automatic' ? 'selected' : '' ?>>Automatic</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="body_type" class="form-label">Body Type</label>
                            <select id="body_type" name="body_type" class="form-select">
                                <option value="">Select Body Type</option>
                                <option value="Sedan" <?= $car['body_type'] === 'Sedan' ? 'selected' : '' ?>>Sedan</option>
                                <option value="Hatchback" <?= $car['body_type'] === 'Hatchback' ? 'selected' : '' ?>>Hatchback</option>
                                <option value="SUV" <?= $car['body_type'] === 'SUV' ? 'selected' : '' ?>>SUV</option>
                                <option value="MPV" <?= $car['body_type'] === 'MPV' ? 'selected' : '' ?>>MPV</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="car_condition" class="form-label">Car Condition *</label>
                            <select id="car_condition" name="car_condition" class="form-select" required>
                                <option value="">Select Condition</option>
                                <option value="excellent" <?= $car['car_condition'] === 'excellent' ? 'selected' : '' ?>>Excellent</option>
                                <option value="good" <?= $car['car_condition'] === 'good' ? 'selected' : '' ?>>Good</option>
                                <option value="fair" <?= $car['car_condition'] === 'fair' ? 'selected' : '' ?>>Fair</option>
                            </select>
                        </div>

                        <!-- Contact Information -->
                        <div class="form-group">
                            <label for="name" class="form-label">Your Name *</label>
                            <input type="text" id="name" name="name" class="form-input" 
                                   value="<?= htmlspecialchars($car['name']) ?>" required>
                        </div>

                        <div class="form-group">
                            <label for="email" class="form-label">Email *</label>
                            <input type="email" id="email" name="email" class="form-input" 
                                   value="<?= htmlspecialchars($car['email']) ?>" required>
                        </div>

                        <div class="form-group">
                            <label for="phone" class="form-label">Phone *</label>
                            <input type="tel" id="phone" name="phone" class="form-input" 
                                   value="<?= htmlspecialchars($car['phone']) ?>" required>
                        </div>

                        <div class="form-group">
                            <label for="location" class="form-label">Location *</label>
                            <input type="text" id="location" name="location" class="form-input" 
                                   value="<?= htmlspecialchars($car['location']) ?>" required>
                        </div>
                    </div>

                    <!-- Description -->
                    <div class="form-group full-width">
                        <label for="description" class="form-label">Description</label>
                        <textarea id="description" name="description" class="form-textarea" 
                                  placeholder="Describe your car's condition, features, and any additional information..."><?= htmlspecialchars($car['description']) ?></textarea>
                    </div>

                    <!-- Photos Section -->
                    <div class="photo-section">
                        <h3>Car Photos</h3>
                        
                        <?php if (!empty($existing_photos)): ?>
                            <div class="existing-photos">
                                <?php foreach ($existing_photos as $photo): ?>
                                    <div class="photo-item">
                                        <img src="<?= htmlspecialchars($photo) ?>" alt="Car photo">
                                        <button type="button" class="photo-remove" onclick="removePhoto('<?= htmlspecialchars($photo) ?>')">
                                            <i class="fas fa-times"></i>
                                        </button>
                                        <input type="checkbox" name="remove_photos[]" value="<?= htmlspecialchars($photo) ?>" 
                                               id="remove_<?= md5($photo) ?>" style="display: none;">
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>

                        <div class="file-input-wrapper">
                            <input type="file" id="photos" name="photos[]" class="file-input" 
                                   multiple accept="image/*">
                            <label for="photos" class="file-input-label">
                                <i class="fas fa-camera"></i> Add More Photos
                            </label>
                        </div>
                        <p style="margin-top: 0.5rem; color: #666; font-size: 0.9rem;">
                            Maximum 5MB per photo. Supported formats: JPEG, PNG, GIF
                        </p>
                    </div>

                    <!-- Form Actions -->
                    <div class="form-actions">
                        <a href="my_cars.php" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Cancel
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Update Listing
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </main>

    <script>
        function removePhoto(photoPath) {
            // Create a unique ID for the checkbox using MD5 hash
            const checkboxId = 'remove_' + CryptoJS.MD5(photoPath).toString();
            const checkbox = document.getElementById(checkboxId);
            
            if (checkbox) {
                // Mark the photo for removal
                checkbox.checked = true;
                const photoItem = checkbox.closest('.photo-item');
                
                // Visual feedback
                photoItem.style.opacity = '0.5';
                photoItem.style.position = 'relative';
                
                // Create removal overlay
                const overlay = document.createElement('div');
                overlay.style.cssText = `
                    position: absolute;
                    top: 0;
                    left: 0;
                    right: 0;
                    bottom: 0;
                    background: rgba(220, 53, 69, 0.8);
                    color: white;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    font-weight: bold;
                    font-size: 0.8rem;
                `;
                overlay.textContent = 'Will be removed';
                photoItem.appendChild(overlay);
                
                // Change the remove button to an undo button
                const button = photoItem.querySelector('.photo-remove');
                button.innerHTML = '<i class="fas fa-undo"></i>';
                button.onclick = function() { undoRemovePhoto(photoPath); };
            }
        }

        function undoRemovePhoto(photoPath) {
            const checkboxId = 'remove_' + CryptoJS.MD5(photoPath).toString();
            const checkbox = document.getElementById(checkboxId);
            
            if (checkbox) {
                // Unmark the photo for removal
                checkbox.checked = false;
                const photoItem = checkbox.closest('.photo-item');
                
                // Restore visual appearance
                photoItem.style.opacity = '1';
                
                // Remove the overlay
                const overlay = photoItem.querySelector('div[style*="background: rgba(220, 53, 69, 0.8)"]');
                if (overlay) overlay.remove();
                
                // Change the button back to remove
                const button = photoItem.querySelector('.photo-remove');
                button.innerHTML = '<i class="fas fa-times"></i>';
                button.onclick = function() { removePhoto(photoPath); };
            }
        }

        // Update file input label when files are selected
        document.getElementById('photos').addEventListener('change', function(e) {
            const files = e.target.files;
            const label = document.querySelector('.file-input-label');
            
            if (files.length > 0) {
                // Update label with number of selected files
                const fileText = files.length === 1 ? '1 photo selected' : `${files.length} photos selected`;
                label.innerHTML = `<i class="fas fa-camera"></i> ${fileText}`;
                
                // Validate file sizes and types
                const maxSize = 5 * 1024 * 1024; // 5MB
                const allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
                
                for (let i = 0; i < files.length; i++) {
                    if (files[i].size > maxSize) {
                        alert(`File "${files[i].name}" is too large. Maximum size is 5MB.`);
                        this.value = '';
                        label.innerHTML = '<i class="fas fa-camera"></i> Add More Photos';
                        return;
                    }
                    
                    if (!allowedTypes.includes(files[i].type)) {
                        alert(`File "${files[i].name}" is not a supported image type. Please upload JPEG, PNG, or GIF.`);
                        this.value = '';
                        label.innerHTML = '<i class="fas fa-camera"></i> Add More Photos';
                        return;
                    }
                }
            } else {
                // Reset label if no files selected
                label.innerHTML = '<i class="fas fa-camera"></i> Add More Photos';
            }
        });

        // Form validation before submission
        document.querySelector('form').addEventListener('submit', function(e) {
            const requiredFields = document.querySelectorAll('[required]');
            let isValid = true;
            
            requiredFields.forEach(field => {
                if (!field.value.trim()) {
                    field.style.borderColor = '#dc3545';
                    isValid = false;
                } else {
                    field.style.borderColor = '#ddd';
                }
            });
            
            if (!isValid) {
                e.preventDefault();
                alert('Please fill in all required fields marked with *.');
            }
        });

        // Highlight required fields when they're empty
        document.querySelectorAll('[required]').forEach(field => {
            field.addEventListener('blur', function() {
                if (!this.value.trim()) {
                    this.style.borderColor = '#dc3545';
                } else {
                    this.style.borderColor = '#ddd';
                }
            });
        });
    </script>
    <style>
    #chat-widget {
        position: fixed;
        bottom: 20px;
        right: 20px;
        z-index: 9999;
        width: 350px;
        height: 450px;
        background-color: white;
        border-radius: 10px;
        box-shadow: 0 0 10px rgba(0,0,0,0.2);
        display: flex;
        flex-direction: column;
        overflow: hidden;
        font-family: Arial, sans-serif;
    }

    #chat-header {
        background-color: #007bff;
        color: white;
        padding: 15px;
        font-weight: bold;
        text-align: center;
        display: flex;
        justify-content: space-between;
        align-items: center;
        cursor: pointer;
    }

    #chat-header .close-btn {
        font-size: 18px;
        cursor: pointer;
        margin-left: 10px;
        color: white;
    }

    #chat-body {
        flex-grow: 1;
        padding: 10px;
        overflow-y: auto;
        background-color: #f9f9f9;
    }

    .message {
        margin-bottom: 10px;
        max-width: 80%;
    }

    .user-message {
        align-self: flex-end;
        background-color: #dc3545;
        color: white;
        padding: 10px;
        border-radius: 10px;
        float: right;
        clear: both;
    }

    .bot-message {
        align-self: flex-start;
        background-color: #e9ecef;
        padding: 10px;
        border-radius: 10px;
        float: left;
        clear: both;
    }

    #chat-input-container {
        display: flex;
        border-top: 1px solid #ccc;
    }

    #chat-input {
        flex-grow: 1;
        padding: 10px;
        border: none;
        outline: none;
        font-size: 14px;
    }

    #chat-send {
        padding: 10px 15px;
        background-color: #007bff;
        color: white;
        border: none;
        cursor: pointer;
        font-size: 16px;
    }

    #chat-send:hover {
        background-color: #0056b3;
    }

    .clearfix {
        clear: both;
    }
</style>

<div id="chat-widget" style="display: none;">
    <div id="chat-header">
        Car Assistant 💬
        <span class="close-btn" title="Close Chat">×</span>
    </div>
    <div id="chat-body"></div>
    <div id="chat-input-container">
        <input type="text" id="chat-input" placeholder="Ask something about used cars..." />
        <button id="chat-send">➤</button>
    </div>
</div>

<button id="chat-toggle-btn" style="
    position: fixed;
    bottom: 20px;
    right: 20px;
    z-index: 9998;
    background-color: #007bff;
    color: white;
    border: none;
    border-radius: 50%;
    width: 60px;
    height: 60px;
    font-size: 24px;
    box-shadow: 0 0 10px rgba(0,0,0,0.2);
    cursor: pointer;
">
    💬
</button>

<script>
document.addEventListener("DOMContentLoaded", function () {
    const toggleBtn = document.getElementById("chat-toggle-btn");
    const chatWidget = document.getElementById("chat-widget");
    const chatHeaderCloseBtn = chatWidget.querySelector(".close-btn");
    const chatBody = document.getElementById("chat-body");
    const chatInput = document.getElementById("chat-input");
    const chatSend = document.getElementById("chat-send");

    function toggleChat(show) {
        if (show) {
            chatWidget.style.display = "flex";
            toggleBtn.innerText = "✕";
        } else {
            chatWidget.style.display = "none";
            toggleBtn.innerText = "💬";
        }
    }

    toggleBtn.addEventListener("click", function () {
        const isHidden = chatWidget.style.display === "none";
        toggleChat(isHidden);
    });

    chatHeaderCloseBtn.addEventListener("click", function () {
        toggleChat(false);
    });

    function appendMessage(text, className) {
        const msgDiv = document.createElement("div");
        msgDiv.className = "message " + className;
        msgDiv.innerHTML = text;
        chatBody.appendChild(msgDiv);
        chatBody.scrollTop = chatBody.scrollHeight;
    }

    async function sendMessage() {
        const message = chatInput.value.trim();
        if (!message) return;

        appendMessage(message, "user-message");
        chatInput.value = "";

        try {
            const response = await fetch("https://openrouter.ai/api/v1/chat/completions",  {
                method: "POST",
                headers: {
                    "Authorization": "Bearer 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",
                    "Content-Type": "application/json",
                    "HTTP-Referer": "https://easycar.my-board.org/", 
                    "X-Title": "Used Car Bot"
                },
                body: JSON.stringify({
                    model: "mistralai/mistral-7b-instruct:free",
                    messages: [
                        { role: "system", content: "You are a helpful assistant that gives advice on buying and selling used cars in Malaysia." },
                        { role: "user", content: message }
                    ]
                })
            });

            const data = await response.json();

            if (!data.choices || !data.choices[0]?.message?.content) {
                throw new Error("Invalid or empty response from AI");
            }

            const reply = data.choices[0].message.content;
            appendMessage(reply, "bot-message");

        } catch (error) {
            console.error(error);
            appendMessage("Error: Could not get response from AI.", "bot-message");
        }
    }

    chatSend.addEventListener("click", sendMessage);

    chatInput.addEventListener("keypress", function (e) {
        if (e.key === "Enter") {
            sendMessage();
        }
    });
});
</script>
</body>
</html>