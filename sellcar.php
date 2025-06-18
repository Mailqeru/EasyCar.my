<?php
// Show errors (for debugging, turn off in production)
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Connection settings
$host = "sql206.infinityfree.com";
$user = "if0_39046487";
$pass = "111897Mail";
$db   = "if0_39046487_epiz_12345678_easycar";

// Connect to DB
$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle form submission
$message = "";
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $make        = $_POST['make'];
    $model       = $_POST['model'];
    $year        = $_POST['year'];
    $mileage     = $_POST['mileage'];
    $transmission= $_POST['transmission'];
    $fuel        = $_POST['fuel'];
    $condition   = $_POST['condition'];
    $price       = $_POST['price'];
    $description = $_POST['description'];
    $name        = $_POST['name'];
    $email       = $_POST['email'];
    $phone       = $_POST['phone'];
    $location    = $_POST['location'];
    
    // Handle photo uploads
    $uploadedPhotos = [];
    if (isset($_FILES['photos']) && !empty($_FILES['photos']['name'][0])) {
        $uploadDir = 'uploads/cars/';
        
        // Create upload directory if it doesn't exist
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        
        $allowedTypes = ['image/jpeg', 'image/png', 'image/jpg'];
        $maxFileSize = 5 * 1024 * 1024; // 5MB
        
        for ($i = 0; $i < count($_FILES['photos']['name']); $i++) {
            if ($_FILES['photos']['error'][$i] === UPLOAD_ERR_OK) {
                $fileName = $_FILES['photos']['name'][$i];
                $fileType = $_FILES['photos']['type'][$i];
                $fileSize = $_FILES['photos']['size'][$i];
                $tempName = $_FILES['photos']['tmp_name'][$i];
                
                // Validate file type
                if (!in_array($fileType, $allowedTypes)) {
                    $message = "❌ Error: Only JPG, JPEG, and PNG files are allowed.";
                    break;
                }
                
                // Validate file size
                if ($fileSize > $maxFileSize) {
                    $message = "❌ Error: File size should not exceed 5MB.";
                    break;
                }
                
                // Generate unique filename
                $fileExtension = pathinfo($fileName, PATHINFO_EXTENSION);
                $uniqueName = uniqid() . '_' . time() . '.' . $fileExtension;
                $targetPath = $uploadDir . $uniqueName;
                
                // Move uploaded file
                if (move_uploaded_file($tempName, $targetPath)) {
                    $uploadedPhotos[] = $targetPath;
                } else {
                    $message = "❌ Error uploading file: " . $fileName;
                    break;
                }
            }
        }
    }
    
    // Convert photos array to JSON string
    $photosJson = json_encode($uploadedPhotos);
    
    // Insert into database only if no upload errors
    if (empty($message)) {
        $sql = "INSERT INTO car_listings (make, model, year, mileage, transmission, fuel, car_condition, price, description, name, email, phone, location, photos)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            $message = "Error preparing statement: " . $conn->error;
        } else {
            $stmt->bind_param("ssiisssdssssss", $make, $model, $year, $mileage, $transmission, $fuel, $condition, $price, $description, $name, $email, $phone, $location, $photosJson);

            if ($stmt->execute()) {
                $message = "✅ Your car listing has been submitted successfully!";
            } else {
                $message = "❌ Error: " . $stmt->error;
            }
            $stmt->close();
        }
    }
}

// Generate year options
$currentYear = date('Y');
$years = range($currentYear, 1980);
?>

<?php include 'session_check.php';
$isLoggedIn = isset($_SESSION['user_id']); ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sell Your Car - EasyCar.my</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f8f9fa;
            color: #333;
            line-height: 1.6;
        }

        .header {
            background: white;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            padding: 1rem 0;
            margin-bottom: 2rem;
        }

        .nav {
            max-width: 1200px;
            margin: 0 auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0 2rem;
        }

        .logo {
            display: flex;
            align-items: center;
            font-size: 1.5rem;
            font-weight: bold;
            color: #4A90E2;
        }

        .logo i {
            margin-right: 0.5rem;
        }

        .nav-links {
            display: flex;
            list-style: none;
            gap: 2rem;
        }

        .nav-links a {
            text-decoration: none;
            color: #666;
            font-weight: 500;
        }

        .nav-buttons {
            display: flex;
            gap: 1rem;
        }

        .btn {
            padding: 0.5rem 1.5rem;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: 500;
            text-decoration: none;
            display: inline-block;
            transition: all 0.3s ease;
        }

        .btn-outline {
            border: 1px solid #4A90E2;
            color: #4A90E2;
            background: white;
        }

        .btn-primary {
            background: #4A90E2;
            color: white;
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(74, 144, 226, 0.3);
        }

        .container {
            max-width: 800px;
            margin: 0 auto;
            padding: 0 2rem;
        }

        .page-header {
            text-align: center;
            margin-bottom: 3rem;
        }

        .page-header h1 {
            font-size: 2.5rem;
            color: #333;
            margin-bottom: 1rem;
        }

        .page-header p {
            font-size: 1.1rem;
            color: #666;
        }

        .form-container {
            background: white;
            border-radius: 12px;
            padding: 2.5rem;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
        }

        .form-section {
            margin-bottom: 2.5rem;
        }

        .form-section h3 {
            color: #4A90E2;
            font-size: 1.2rem;
            margin-bottom: 1.5rem;
            padding-bottom: 0.5rem;
            border-bottom: 2px solid #e9ecef;
        }

        .form-row {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            margin-bottom: 1.5rem;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 600;
            color: #333;
        }

        .form-control {
            width: 100%;
            padding: 0.75rem;
            border: 2px solid #e9ecef;
            border-radius: 8px;
            font-size: 1rem;
            transition: border-color 0.3s ease;
        }

        .form-control:focus {
            outline: none;
            border-color: #4A90E2;
            box-shadow: 0 0 0 3px rgba(74, 144, 226, 0.1);
        }

        textarea.form-control {
            resize: vertical;
            min-height: 120px;
        }

        .photo-upload {
            border: 2px dashed #4A90E2;
            border-radius: 12px;
            padding: 2rem;
            text-align: center;
            background: #f8f9ff;
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .photo-upload:hover {
            background: #f0f4ff;
            border-color: #3d7bc6;
        }

        .photo-upload.dragover {
            background: #e6f0ff;
            border-color: #2d6cb6;
        }

        .photo-upload i {
            font-size: 3rem;
            color: #4A90E2;
            margin-bottom: 1rem;
        }

        .photo-upload-text {
            color: #666;
            margin-bottom: 1rem;
        }

        .photo-upload input[type="file"] {
            display: none;
        }

        .photo-preview {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
            gap: 1rem;
            margin-top: 1rem;
        }

        .photo-item {
            position: relative;
            border-radius: 8px;
            overflow: hidden;
            background: #f8f9fa;
        }

        .photo-item img {
            width: 100%;
            height: 120px;
            object-fit: cover;
        }

        .photo-remove {
            position: absolute;
            top: 5px;
            right: 5px;
            background: rgba(255, 0, 0, 0.8);
            color: white;
            border: none;
            border-radius: 50%;
            width: 25px;
            height: 25px;
            cursor: pointer;
            font-size: 12px;
        }

        .submit-btn {
            background: linear-gradient(135deg, #4A90E2, #357ABD);
            color: white;
            padding: 1rem 3rem;
            border: none;
            border-radius: 8px;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            width: 100%;
            transition: all 0.3s ease;
        }

        .submit-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(74, 144, 226, 0.4);
        }

        .alert {
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 2rem;
            font-weight: 500;
        }

        .alert-success {
            background: #d4edda;
            color: #155724;
            border-left: 4px solid #28a745;
        }

        .alert-error {
            background: #f8d7da;
            color: #721c24;
            border-left: 4px solid #dc3545;
        }

        @media (max-width: 768px) {
            .nav {
                flex-direction: column;
                gap: 1rem;
            }

            .nav-links {
                display: none;
            }

            .container {
                padding: 0 1rem;
            }

            .form-container {
                padding: 1.5rem;
            }

            .page-header h1 {
                font-size: 2rem;
            }

            .form-row {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <header class="header">
        <nav class="nav">
            <div class="logo">
                <i class="fas fa-car"></i>
                EasyCar.my
            </div>
            <ul class="nav-links">
                <li><a href="index.php">Home</a></li>
                <li><a href="BrowseCars.php">Browse Cars</a></li>
                <li><a href="HowItWorks.php">How It Works</a></li>
                <li><a href="About.php">About</a></li>
                <li><a href="Contact.php">Contact</a></li>
                <li><a href="my_cars.php" >My Cars</a></li>
            </ul>
            <div class="nav-buttons">
    <?php if ($isLoggedIn): ?>
        <a href="logout.php" class="btn btn-outline">Logout</a>
    <?php else: ?>
        <a href="login.php" class="btn btn-outline active">Login</a>
    <?php endif; ?>
    <a href="sellcar.php" class="btn btn-primary">Sell Your Car</a>
</div>

        </nav>
    </header>

    <div class="container">
        <div class="page-header">
            <h1>Sell Your Car with EasyCar.my</h1>
            <p>Reach thousands of potential buyers and get the best price for your vehicle</p>
        </div>

        <?php if ($message): ?>
            <div class="alert <?= str_starts_with($message, '✅') ? 'alert-success' : 'alert-error' ?>">
                <?= $message ?>
            </div>
        <?php endif; ?>

        <div class="form-container">
            <form method="POST" enctype="multipart/form-data">
                <div class="form-section">
                    <h3><i class="fas fa-car"></i> Vehicle Information</h3>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="make">Make</label>
                            <select name="make" id="make" class="form-control" required>
                                <option value="">Select Make</option>
                                <option value="Toyota">Toyota</option>
                                <option value="Honda">Honda</option>
                                <option value="Nissan">Nissan</option>
                                <option value="Mazda">Mazda</option>
                                <option value="Mitsubishi">Mitsubishi</option>
                                <option value="Subaru">Subaru</option>
                                <option value="Suzuki">Suzuki</option>
                                <option value="Daihatsu">Daihatsu</option>
                                <option value="Perodua">Perodua</option>
                                <option value="Proton">Proton</option>
                                <option value="BMW">BMW</option>
                                <option value="Mercedes-Benz">Mercedes-Benz</option>
                                <option value="Audi">Audi</option>
                                <option value="Volkswagen">Volkswagen</option>
                                <option value="Ford">Ford</option>
                                <option value="Chevrolet">Chevrolet</option>
                                <option value="Hyundai">Hyundai</option>
                                <option value="Kia">Kia</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="model">Model</label>
                            <input type="text" name="model" id="model" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label for="year">Year</label>
                            <select name="year" id="year" class="form-control" required>
                                <option value="">Select Year</option>
                                <?php foreach ($years as $year): ?>
                                    <option value="<?= $year ?>"><?= $year ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="mileage">Mileage (KM)</label>
                        <input type="number" name="mileage" id="mileage" class="form-control" required>
                    </div>
                </div>

                <div class="form-section">
                    <h3><i class="fas fa-cogs"></i> Vehicle Details</h3>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="transmission">Transmission</label>
                            <select name="transmission" id="transmission" class="form-control" required>
                                <option value="">Select</option>
                                <option value="Automatic">Automatic</option>
                                <option value="Manual">Manual</option>
                                <option value="CVT">CVT</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="fuel">Fuel Type</label>
                            <select name="fuel" id="fuel" class="form-control" required>
                                <option value="">Select</option>
                                <option value="Gasoline">Gasoline</option>
                                <option value="Diesel">Diesel</option>
                                <option value="Hybrid">Hybrid</option>
                                <option value="Electric">Electric</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="condition">Condition</label>
                            <select name="condition" id="condition" class="form-control" required>
                                <option value="">Select</option>
                                <option value="Excellent">Excellent</option>
                                <option value="Good">Good</option>
                                <option value="Fair">Fair</option>
                                <option value="Poor">Poor</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="price">Asking Price (RM)</label>
                        <input type="number" name="price" id="price" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="description">Description</label>
                        <textarea name="description" id="description" class="form-control" rows="5" 
                                  placeholder="Describe your car's condition, features, service history, etc." required></textarea>
                    </div>
                </div>

                <div class="form-section">
                    <h3><i class="fas fa-camera"></i> Photos</h3>
                    <div class="photo-upload" onclick="document.getElementById('photos').click()">
                        <i class="fas fa-cloud-upload-alt"></i>
                        <div class="photo-upload-text">
                            <strong>+ Add Photo</strong><br>
                            Drop photos here or click to browse<br>
                            <small>Maximum 5MB per photo, JPG/PNG only</small>
                        </div>
                        <input type="file" name="photos[]" id="photos" multiple accept="image/*">
                    </div>
                    <div class="photo-preview" id="photoPreview"></div>
                </div>

                <div class="form-section">
                    <h3><i class="fas fa-user"></i> Contact Information</h3>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="name">Your Name</label>
                            <input type="text" name="name" id="name" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="email" name="email" id="email" class="form-control" required>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="phone">Phone Number</label>
                            <input type="tel" name="phone" id="phone" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label for="location">Location</label>
                            <input type="text" name="location" id="location" class="form-control" 
                                   placeholder="City, State" required>
                        </div>
                    </div>
                </div>

                <button type="submit" class="submit-btn">
                    <i class="fas fa-paper-plane"></i> Create Listing
                </button>
            </form>
        </div>
    </div>

    <script>
        // Photo upload functionality
        const photoInput = document.getElementById('photos');
        const photoPreview = document.getElementById('photoPreview');
        const photoUpload = document.querySelector('.photo-upload');
        let selectedFiles = [];

        photoInput.addEventListener('change', handleFiles);

        // Drag and drop functionality
        photoUpload.addEventListener('dragover', (e) => {
            e.preventDefault();
            photoUpload.classList.add('dragover');
        });

        photoUpload.addEventListener('dragleave', () => {
            photoUpload.classList.remove('dragover');
        });

        photoUpload.addEventListener('drop', (e) => {
            e.preventDefault();
            photoUpload.classList.remove('dragover');
            const files = Array.from(e.dataTransfer.files);
            handleFileSelection(files);
        });

        function handleFiles(e) {
            const files = Array.from(e.target.files);
            handleFileSelection(files);
        }

        function handleFileSelection(files) {
            // Limit to 10 photos
            const remainingSlots = 10 - selectedFiles.length;
            const filesToAdd = files.slice(0, remainingSlots);
            
            filesToAdd.forEach((file, index) => {
                if (file.type.startsWith('image/')) {
                    selectedFiles.push(file);
                    displayPhoto(file, selectedFiles.length - 1);
                }
            });

            updateFileInput();
        }

        function displayPhoto(file, index) {
            const reader = new FileReader();
            reader.onload = (e) => {
                const photoItem = document.createElement('div');
                photoItem.className = 'photo-item';
                photoItem.innerHTML = `
                    <img src="${e.target.result}" alt="Photo ${index + 1}">
                    <button type="button" class="photo-remove" onclick="removePhoto(${index})">
                        <i class="fas fa-times"></i>
                    </button>
                `;
                photoPreview.appendChild(photoItem);
            };
            reader.readAsDataURL(file);
        }

        function removePhoto(index) {
            selectedFiles.splice(index, 1);
            updatePhotoPreview();
            updateFileInput();
        }

        function updatePhotoPreview() {
            photoPreview.innerHTML = '';
            selectedFiles.forEach((file, index) => {
                displayPhoto(file, index);
            });
        }

        function updateFileInput() {
            // Create new FileList
            const dt = new DataTransfer();
            selectedFiles.forEach(file => dt.items.add(file));
            photoInput.files = dt.files;
        }

        // Form validation
        document.querySelector('form').addEventListener('submit', function(e) {
            const price = document.getElementById('price').value;
            if (price && price < 0) {
                e.preventDefault();
                alert('Price cannot be negative');
                return false;
            }

            const year = document.getElementById('year').value;
            const currentYear = new Date().getFullYear();
            if (year && (year < 1980 || year > currentYear)) {
                e.preventDefault();
                alert('Please enter a valid year');
                return false;
            }
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
            const response = await fetch("https://api.groq.com/openai/v1/chat/completions",  {
                method: "POST",
                headers: {
                    "Authorization": "Bearer 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",
                    "Content-Type": "application/json"
                },
                body: JSON.stringify({
                    model: "llama3-8b-8192", // or "mixtral-8x7b-32768"
                    messages: [
                        { role: "system", content: "You are a helpful assistant that gives advice on buying and selling used cars in Malaysia." },
                        { role: "user", content: message }
                    ],
                    temperature: 0.7,
                    max_tokens: 512
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