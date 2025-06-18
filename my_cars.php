<?php
// Start session and check authentication
 include 'session_check.php';
$isLoggedIn = isset($_SESSION['user_id']); 

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

// Handle delete request
if (isset($_GET['delete'])) {
    $car_id = (int)$_GET['delete'];
    
    // Verify the car belongs to the current user before deleting
    // New query (using email)
$check_sql = "SELECT id FROM car_listings WHERE id = ? AND email = (SELECT email FROM users WHERE id = ?)";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bind_param("ii", $car_id, $user_id);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();
    
    if ($check_result->num_rows > 0) {
        $delete_sql = "DELETE FROM car_listings WHERE id = ?";
        $delete_stmt = $conn->prepare($delete_sql);
        $delete_stmt->bind_param("i", $car_id);
        $delete_stmt->execute();
        
        if ($delete_stmt->affected_rows > 0) {
            $success_msg = "Car listing deleted successfully!";
        } else {
            $error_msg = "Error deleting car listing.";
        }
    } else {
        $error_msg = "You don't have permission to delete this listing.";
    }
}

// Fetch user's car listings
$sql = "SELECT id, make, model, year, price, photos FROM car_listings 
        WHERE email = (SELECT email FROM users WHERE id = ?) 
        ORDER BY created_at DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$cars = $result->fetch_all(MYSQLI_ASSOC);

// Function to get first photo
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

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Car Listings - EasyCar.my</title>
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

        .btn-danger {
            background: #dc3545;
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

        .my-cars-section {
            padding-top: 8rem;
            padding-bottom: 4rem;
        }

        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
        }

        .section-title {
            font-size: 2rem;
            color: #333;
        }

        .add-car-btn {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }

        .cars-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 2rem;
        }

        .car-card {
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
        }

        .car-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 30px rgba(0,0,0,0.15);
        }

        .car-image {
            width: 100%;
            height: 200px;
            object-fit: cover;
            background: #f8f9fa;
        }

        .car-info {
            padding: 1.5rem;
        }

        .car-title {
            font-size: 1.2rem;
            font-weight: bold;
            color: #333;
            margin-bottom: 0.5rem;
        }

        .car-price {
            font-size: 1.3rem;
            font-weight: bold;
            color: #4A90E2;
            margin-bottom: 1rem;
        }

        .car-actions {
            display: flex;
            gap: 1rem;
            margin-top: 1.5rem;
        }

        .no-cars {
            text-align: center;
            padding: 4rem 2rem;
            color: #666;
            grid-column: 1 / -1;
        }

        .no-cars i {
            font-size: 4rem;
            color: #ddd;
            margin-bottom: 1rem;
        }

        .alert {
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 2rem;
        }

        .alert-success {
            background: #d4edda;
            color: #155724;
        }

        .alert-danger {
            background: #f8d7da;
            color: #721c24;
        }

        @media (max-width: 768px) {
            .nav-container {
                flex-direction: column;
                gap: 1rem;
            }

            .nav-links {
                display: none;
            }

            .section-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 1rem;
            }

            .cars-grid {
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
                    <li><a href="BrowseCars.php">Browse Cars</a></li>
                    <li><a href="HowItWorks.php">How It Works</a></li>
                    <li><a href="About.php">About</a></li>
                    <li><a href="Contact.php">Contact</a></li>
                    <li><a href="my_cars.php" class="active">My Cars</a></li>
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

    <main class="my-cars-section">
        <div class="container">
            <div class="section-header">
                <h1 class="section-title">My Car Listings</h1>
                <a href="sellcar.php" class="btn btn-primary add-car-btn">
                    <i class="fas fa-plus"></i> Add New Car
                </a>
            </div>

            <?php if (isset($success_msg)): ?>
                <div class="alert alert-success">
                    <?= $success_msg ?>
                </div>
            <?php endif; ?>

            <?php if (isset($error_msg)): ?>
                <div class="alert alert-danger">
                    <?= $error_msg ?>
                </div>
            <?php endif; ?>

            <div class="cars-grid">
                <?php if (empty($cars)): ?>
                    <div class="no-cars">
                        <i class="fas fa-car"></i>
                        <h3>No Cars Listed</h3>
                        <p>You haven't listed any cars for sale yet.</p>
                        <a href="sellcar.php" class="btn btn-primary" style="margin-top: 1rem;">
                            <i class="fas fa-plus"></i> List Your First Car
                        </a>
                    </div>
                <?php else: ?>
                    <?php foreach ($cars as $car): ?>
                        <div class="car-card">
                            <img src="<?= getFirstPhoto($car['photos']) ?>" 
                                 alt="<?= htmlspecialchars($car['make'] . ' ' . $car['model']) ?>" 
                                 class="car-image">
                            <div class="car-info">
                                <div class="car-title"><?= htmlspecialchars($car['year'] . ' ' . $car['make'] . ' ' . $car['model']) ?></div>
                                <div class="car-price"><?= formatPrice($car['price']) ?></div>
                                <div class="car-actions">
                                    <a href="car_details.php?id=<?= $car['id'] ?>" class="btn btn-outline">
                                        <i class="fas fa-eye"></i> View
                                    </a>
                                    <a href="edit_car.php?id=<?= $car['id'] ?>" class="btn btn-outline">
                                        <i class="fas fa-edit"></i> Edit
                                    </a>
                                    <a href="my_cars.php?delete=<?= $car['id'] ?>" 
                                       class="btn btn-danger"
                                       onclick="return confirm('Are you sure you want to delete this listing?');">
                                        <i class="fas fa-trash"></i> Delete
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </main>

    <script>
        // Confirm before deleting
        document.querySelectorAll('.btn-danger').forEach(btn => {
            btn.addEventListener('click', function(e) {
                if (!confirm('Are you sure you want to delete this listing?')) {
                    e.preventDefault();
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