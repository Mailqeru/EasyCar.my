<?php
// Include session check first
include 'session_check.php';
$isLoggedIn = isset($_SESSION['user_id']);

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

// Get filter parameters
$make = isset($_GET['make']) ? $_GET['make'] : '';
$model = isset($_GET['model']) ? $_GET['model'] : '';
$price_range = isset($_GET['price']) ? $_GET['price'] : '';
$year_range = isset($_GET['year']) ? $_GET['year'] : '';
$mileage_range = isset($_GET['mileage']) ? $_GET['mileage'] : '';
$location = isset($_GET['location']) ? $_GET['location'] : '';
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$cars_per_page = 12;
$offset = ($page - 1) * $cars_per_page;

// Build WHERE clause based on filters
$where_conditions = [];
$params = [];
$param_types = '';

if (!empty($make)) {
    $where_conditions[] = "make = ?";
    $params[] = $make;
    $param_types .= 's';
}

if (!empty($model)) {
    $where_conditions[] = "model LIKE ?";
    $params[] = "%$model%";
    $param_types .= 's';
}

if (!empty($price_range)) {
    switch ($price_range) {
        case '0-10000':
            $where_conditions[] = "price <= 10000";
            break;
        case '10000-20000':
            $where_conditions[] = "price BETWEEN 10000 AND 20000";
            break;
        case '20000-30000':
            $where_conditions[] = "price BETWEEN 20000 AND 30000";
            break;
        case '30000+':
            $where_conditions[] = "price >= 30000";
            break;
    }
}

if (!empty($year_range)) {
    switch ($year_range) {
        case '2020+':
            $where_conditions[] = "year >= 2020";
            break;
        case '2015-2019':
            $where_conditions[] = "year BETWEEN 2015 AND 2019";
            break;
        case '2010-2014':
            $where_conditions[] = "year BETWEEN 2010 AND 2014";
            break;
        case '2005-2009':
            $where_conditions[] = "year BETWEEN 2005 AND 2009";
            break;
    }
}

if (!empty($mileage_range)) {
    switch ($mileage_range) {
        case '0-50000':
            $where_conditions[] = "mileage <= 50000";
            break;
        case '50000-100000':
            $where_conditions[] = "mileage BETWEEN 50000 AND 100000";
            break;
        case '100000+':
            $where_conditions[] = "mileage >= 100000";
            break;
    }
}

if (!empty($location)) {
    $where_conditions[] = "location LIKE ?";
    $params[] = "%$location%";
    $param_types .= 's';
}

// Build the SQL query
$where_clause = '';
if (!empty($where_conditions)) {
    $where_clause = 'WHERE ' . implode(' AND ', $where_conditions);
}

// Get total count for pagination
$count_sql = "SELECT COUNT(*) as total FROM car_listings $where_clause";
$count_stmt = $conn->prepare($count_sql);
if (!empty($params)) {
    $count_stmt->bind_param($param_types, ...$params);
}
$count_stmt->execute();
$total_cars = $count_stmt->get_result()->fetch_assoc()['total'];
$total_pages = ceil($total_cars / $cars_per_page);

// Get cars with pagination
$sql = "SELECT * FROM car_listings $where_clause ORDER BY id DESC LIMIT $cars_per_page OFFSET $offset";
$stmt = $conn->prepare($sql);
if (!empty($params)) {
    $stmt->bind_param($param_types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();
$cars = $result->fetch_all(MYSQLI_ASSOC);

// Function to get first photo from JSON
function getFirstPhoto($photos_json) {
    if (empty($photos_json)) return 'https://via.placeholder.com/300x200?text=No+Image';
    
    $photos = json_decode($photos_json, true);
    if (is_array($photos) && !empty($photos)) {
        return $photos[0];
    }
    return 'https://via.placeholder.com/300x200?text=No+Image';
}

// Function to format price
function formatPrice($price) {
    return 'RM ' . number_format($price, 0);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Browse Cars - EasyCar.my</title>
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
            position: fixed;
            top: 0;
            width: 100%;
            z-index: 1000;
        }

        .nav-container {
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

        .nav-links {
            display: flex;
            list-style: none;
            gap: 2rem;
        }

        .nav-links a {
            text-decoration: none;
            color: #666;
            font-weight: 500;
            transition: color 0.3s ease;
        }

        .nav-links a:hover,
        .nav-links a.active {
            color: #4A90E2;
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
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 2rem;
        }

        .featured-section {
            padding-top: 8rem;
            padding-bottom: 4rem;
        }

        .section-header {
            text-align: center;
            margin-bottom: 3rem;
        }

        .section-title {
            font-size: 2.5rem;
            color: #333;
            margin-bottom: 1rem;
        }

        .section-subtitle {
            font-size: 1.1rem;
            color: #666;
        }

        .search-form {
            background: white;
            padding: 2rem;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
            margin-bottom: 3rem;
        }

        .form-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 600;
            color: #333;
        }

        .form-group input,
        .form-group select {
            width: 100%;
            padding: 0.75rem;
            border: 2px solid #e9ecef;
            border-radius: 8px;
            font-size: 1rem;
            transition: border-color 0.3s ease;
        }

        .form-group input:focus,
        .form-group select:focus {
            outline: none;
            border-color: #4A90E2;
            box-shadow: 0 0 0 3px rgba(74, 144, 226, 0.1);
        }

        .btn-search {
            background: linear-gradient(135deg, #4A90E2, #357ABD);
            color: white;
            padding: 1rem 2rem;
            border: none;
            border-radius: 8px;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            width: 100%;
        }

        .btn-search:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(74, 144, 226, 0.4);
        }

        .cars-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
            gap: 2rem;
            margin-bottom: 3rem;
        }

        .car-card {
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .car-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 30px rgba(0,0,0,0.15);
        }

        .car-image {
            width: 100%;
            height: 220px;
            object-fit: cover;
            background: #f8f9fa;
        }

        .car-info {
            padding: 1.5rem;
        }

        .car-title {
            font-size: 1.3rem;
            font-weight: bold;
            color: #333;
            margin-bottom: 0.5rem;
        }

        .car-price {
            font-size: 1.5rem;
            font-weight: bold;
            color: #4A90E2;
            margin-bottom: 1rem;
        }

        .car-details {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 0.5rem;
            font-size: 0.9rem;
            color: #666;
        }

        .car-detail {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .car-detail i {
            color: #4A90E2;
            width: 16px;
        }

        .pagination {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 1rem;
            margin-top: 3rem;
        }

        .pagination a {
            padding: 0.75rem 1rem;
            text-decoration: none;
            color: #4A90E2;
            border: 1px solid #4A90E2;
            border-radius: 5px;
            transition: all 0.3s ease;
        }

        .pagination a:hover,
        .pagination a.active {
            background: #4A90E2;
            color: white;
        }

        .no-results {
            text-align: center;
            padding: 4rem 2rem;
            color: #666;
        }

        .no-results i {
            font-size: 4rem;
            color: #ddd;
            margin-bottom: 1rem;
        }

        .results-info {
            margin-bottom: 2rem;
            color: #666;
            font-size: 1rem;
        }

        .footer {
            background: #333;
            color: white;
            text-align: center;
            padding: 2rem 0;
            margin-top: 4rem;
        }

        @media (max-width: 768px) {
            .nav-container {
                flex-direction: column;
                gap: 1rem;
            }

            .nav-links {
                display: none;
            }

            .section-title {
                font-size: 2rem;
            }

            .form-grid {
                grid-template-columns: 1fr;
            }

            .cars-grid {
                grid-template-columns: 1fr;
            }

            .car-details {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <!-- Header -->
    <header class="header">
        <div class="nav-container">
            <div class="logo">
                <i class="fas fa-car" style="margin-right: 0.5rem;"></i>
                EasyCar.my
            </div>
            <nav>
                <ul class="nav-links">
                    <li><a href="index.php">Home</a></li>
                    <li><a href="BrowseCars.php" class="active">Browse Cars</a></li>
                    <li><a href="HowItWorks.php">How It Works</a></li>
                    <li><a href="About.php">About</a></li>
                    <li><a href="Contact.php">Contact</a></li>
                    <li><a href="my_cars.php" >My Cars</a></li>
                </ul>
            </nav>
            <div class="nav-buttons">
            <?php if ($isLoggedIn): ?>
                <a href="logout.php" class="btn btn-outline">Logout</a>
                 <?php else: ?>
                <a href="login.php" class="btn btn-outline active">Login</a>
            <?php endif; ?>
            <a href="sellcar.php" class="btn btn-primary">Sell Your Car</a>
            </div>

        
    </header>

    <main class="featured-section">
        <div class="container">
            <div class="section-header">
                <h2 class="section-title">Browse All Cars</h2>
                <p class="section-subtitle">Find your perfect used car from our extensive listings</p>
            </div>
            
            <!-- Search Form -->
            <form class="search-form" method="GET">
                <div class="form-grid">
                    <div class="form-group">
                        <label for="make">Make</label>
                        <select id="make" name="make">
                            <option value="">All Makes</option>
                            <option value="Toyota" <?= $make === 'Toyota' ? 'selected' : '' ?>>Toyota</option>
                            <option value="Honda" <?= $make === 'Honda' ? 'selected' : '' ?>>Honda</option>
                            <option value="Nissan" <?= $make === 'Nissan' ? 'selected' : '' ?>>Nissan</option>
                            <option value="Mazda" <?= $make === 'Mazda' ? 'selected' : '' ?>>Mazda</option>
                            <option value="Mitsubishi" <?= $make === 'Mitsubishi' ? 'selected' : '' ?>>Mitsubishi</option>
                            <option value="Perodua" <?= $make === 'Perodua' ? 'selected' : '' ?>>Perodua</option>
                            <option value="Proton" <?= $make === 'Proton' ? 'selected' : '' ?>>Proton</option>
                            <option value="BMW" <?= $make === 'BMW' ? 'selected' : '' ?>>BMW</option>
                            <option value="Mercedes-Benz" <?= $make === 'Mercedes-Benz' ? 'selected' : '' ?>>Mercedes-Benz</option>
                            <option value="Ford" <?= $make === 'Ford' ? 'selected' : '' ?>>Ford</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="model">Model</label>
                        <input type="text" id="model" name="model" placeholder="Any model" value="<?= htmlspecialchars($model) ?>">
                    </div>
                    <div class="form-group">
                        <label for="price">Price Range</label>
                        <select id="price" name="price">
                            <option value="">Any Price</option>
                            <option value="0-10000" <?= $price_range === '0-10000' ? 'selected' : '' ?>>Under RM 10,000</option>
                            <option value="10000-20000" <?= $price_range === '10000-20000' ? 'selected' : '' ?>>RM 10,000 - RM 20,000</option>
                            <option value="20000-30000" <?= $price_range === '20000-30000' ? 'selected' : '' ?>>RM 20,000 - RM 30,000</option>
                            <option value="30000+" <?= $price_range === '30000+' ? 'selected' : '' ?>>RM 30,000+</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="year">Year</label>
                        <select id="year" name="year">
                            <option value="">Any Year</option>
                            <option value="2020+" <?= $year_range === '2020+' ? 'selected' : '' ?>>2020 or newer</option>
                            <option value="2015-2019" <?= $year_range === '2015-2019' ? 'selected' : '' ?>>2015-2019</option>
                            <option value="2010-2014" <?= $year_range === '2010-2014' ? 'selected' : '' ?>>2010-2014</option>
                            <option value="2005-2009" <?= $year_range === '2005-2009' ? 'selected' : '' ?>>2005-2009</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="mileage">Mileage</label>
                        <select id="mileage" name="mileage">
                            <option value="">Any Mileage</option>
                            <option value="0-50000" <?= $mileage_range === '0-50000' ? 'selected' : '' ?>>Under 50,000 KM</option>
                            <option value="50000-100000" <?= $mileage_range === '50000-100000' ? 'selected' : '' ?>>50,000 - 100,000 KM</option>
                            <option value="100000+" <?= $mileage_range === '100000+' ? 'selected' : '' ?>>Over 100,000 KM</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="location">Location</label>
                        <input type="text" id="location" name="location" placeholder="City or State" value="<?= htmlspecialchars($location) ?>">
                    </div>
                </div>
                <button type="submit" class="btn-search">
                    <i class="fas fa-search"></i> Filter Cars
                </button>
            </form>

            <!-- Results Info -->
            <div class="results-info">
                <strong><?= $total_cars ?></strong> cars found
                <?php if (!empty(array_filter([$make, $model, $price_range, $year_range, $mileage_range, $location]))): ?>
                    with your filters
                <?php endif; ?>
            </div>

            <!-- Cars Grid -->
            <div class="cars-grid">
                <?php if (empty($cars)): ?>
                    <div class="no-results" style="grid-column: 1 / -1;">
                        <i class="fas fa-car"></i>
                        <h3>No cars found</h3>
                        <p>Try adjusting your search filters to find more results.</p>
                    </div>
                <?php else: ?>
                    <?php foreach ($cars as $car): ?>
                        <div class="car-card" onclick="viewCarDetails(<?= $car['id'] ?>)">
                            <img src="<?= getFirstPhoto($car['photos']) ?>" alt="<?= htmlspecialchars($car['make'] . ' ' . $car['model']) ?>" class="car-image">
                            <div class="car-info">
                                <div class="car-title"><?= htmlspecialchars($car['year'] . ' ' . $car['make'] . ' ' . $car['model']) ?></div>
                                <div class="car-price"><?= formatPrice($car['price']) ?></div>
                                <div class="car-details">
                                    <div class="car-detail">
                                        <i class="fas fa-calendar"></i>
                                        <span><?= $car['year'] ?></span>
                                    </div>
                                    <div class="car-detail">
                                        <i class="fas fa-tachometer-alt"></i>
                                        <span><?= number_format($car['mileage']) ?> KM</span>
                                    </div>
                                    <div class="car-detail">
                                        <i class="fas fa-cog"></i>
                                        <span><?= $car['transmission'] ?></span>
                                    </div>
                                    <div class="car-detail">
                                        <i class="fas fa-gas-pump"></i>
                                        <span><?= $car['fuel'] ?></span>
                                    </div>
                                    <div class="car-detail">
                                        <i class="fas fa-map-marker-alt"></i>
                                        <span><?= htmlspecialchars($car['location']) ?></span>
                                    </div>
                                    <div class="car-detail">
                                        <i class="fas fa-star"></i>
                                        <span><?= $car['car_condition'] ?></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
            
            <!-- Pagination -->
            <?php if ($total_pages > 1): ?>
                <div class="pagination">
                    <?php if ($page > 1): ?>
                        <a href="?<?= http_build_query(array_merge($_GET, ['page' => $page - 1])) ?>">
                            <i class="fas fa-chevron-left"></i> Previous
                        </a>
                    <?php endif; ?>
                    
                    <?php for ($i = max(1, $page - 2); $i <= min($total_pages, $page + 2); $i++): ?>
                        <a href="?<?= http_build_query(array_merge($_GET, ['page' => $i])) ?>" 
                           class="<?= $i === $page ? 'active' : '' ?>"><?= $i ?></a>
                    <?php endfor; ?>
                    
                    <?php if ($page < $total_pages): ?>
                        <a href="?<?= http_build_query(array_merge($_GET, ['page' => $page + 1])) ?>">
                            Next <i class="fas fa-chevron-right"></i>
                        </a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
    </main>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <p>&copy; 2025 EasyCar.my. All rights reserved. | 
               <a href="#" style="color: white;">Privacy Policy</a> | 
               <a href="#" style="color: white;">Terms of Service</a>
            </p>
        </div>
    </footer>

    <script>
        function viewCarDetails(carId) {
            // Redirect to car details page
            window.location.href = 'car_details.php?id=' + carId;
        }

        // Auto-submit form when filters change (optional)
        document.querySelectorAll('select').forEach(select => {
            select.addEventListener('change', function() {
                // Uncomment the next line to auto-submit on filter change
                // this.form.submit();
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