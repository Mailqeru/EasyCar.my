<?php
// Include session check
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

// Get car ID from URL
$car_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Fetch car details
$car = [];
$photos = [];
if ($car_id > 0) {
    $sql = "SELECT * FROM car_listings WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $car_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $car = $result->fetch_assoc();
    
    if ($car) {
        // Decode photos JSON
        $photos = json_decode($car['photos'] ?? '[]', true) ?: [];
    }
}

// Function to format price
function formatPrice($price) {
    return 'RM ' . number_format($price, 0);
}

// Function to format mileage
function formatMileage($mileage) {
    return number_format($mileage) . ' KM';
}

// Function to safely output HTML-escaped strings
function safeOutput(...$values) {
    foreach ($values as $value) {
        if (!empty($value)) {
            return htmlspecialchars($value);
        }
    }
    return 'Not specified';
}

// Close connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= safeOutput($car['make'], 'Car Details') ?> - EasyCar.my</title>
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
            padding: 1rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .container {
            max-width: 900px;
            margin: 2rem auto;
            background: #fff;
            padding: 2rem;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        }

        .car-title {
            font-size: 1.8rem;
            margin-bottom: 0.5rem;
        }

        .price {
            font-size: 1.4rem;
            color: #007bff;
            margin-bottom: 1.5rem;
            font-weight: bold;
        }

        .gallery {
            display: flex;
            gap: 1rem;
            overflow-x: auto;
            margin-bottom: 2rem;
            padding-bottom: 1rem;
        }

        .gallery img {
            height: 200px;
            border-radius: 8px;
            object-fit: cover;
            cursor: pointer;
            transition: transform 0.3s;
        }

        .gallery img:hover {
            transform: scale(1.03);
        }

        .details-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 1rem;
            margin-bottom: 2rem;
        }

        .detail-card {
            background: #f8f9fa;
            padding: 1rem;
            border-radius: 8px;
        }

        .detail-card h4 {
            margin-bottom: 0.5rem;
            color: #495057;
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .detail-card p {
            font-size: 1.1rem;
            font-weight: 500;
        }

        .description {
            margin: 2rem 0;
            padding: 1.5rem;
            background: #f8f9fa;
            border-radius: 8px;
            line-height: 1.7;
        }

        .description h3 {
            margin-bottom: 1rem;
            color: #343a40;
        }

        .seller-info {
            margin-top: 2rem;
            padding: 1.5rem;
            background: #e9ecef;
            border-radius: 8px;
        }

        .seller-info h3 {
            margin-bottom: 1rem;
            color: #343a40;
        }

        .contact-details {
            margin-top: 1rem;
            padding: 1rem;
            background: white;
            border-radius: 6px;
        }

        .contact-row {
            display: flex;
            align-items: center;
            margin-bottom: 0.8rem;
        }

        .contact-row i {
            width: 30px;
            color: #007bff;
        }

        .login-prompt {
            color: #dc3545;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .back-link {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            margin-top: 1.5rem;
            color: #007bff;
            text-decoration: none;
            font-weight: 500;
            padding: 0.5rem 1rem;
            border: 1px solid #007bff;
            border-radius: 6px;
            transition: all 0.3s;
        }

        .back-link:hover {
            background: #007bff;
            color: white;
            text-decoration: none;
        }

        @media (max-width: 768px) {
            .container {
                padding: 1rem;
                margin: 1rem;
            }
            
            .details-grid {
                grid-template-columns: 1fr;
            }
            
            .gallery img {
                height: 150px;
            }
        }
    </style>
</head>
<body>

    <div class="header">
        <h2>EasyCar.my</h2>
        <a href="BrowseCars.php" class="back-link"><i class="fa fa-arrow-left"></i> Back to Listings</a>
    </div>

    <div class="container">
        <?php if ($car): ?>
            <h1 class="car-title"><?= safeOutput($car['year']) . ' ' . safeOutput($car['make']) . ' ' . safeOutput($car['model']) ?></h1>
            <div class="price"><?= formatPrice($car['price']) ?></div>

            <?php if (!empty($photos)): ?>
                <div class="gallery">
                    <?php foreach ($photos as $photo): ?>
                        <img src="<?= safeOutput($photo) ?>" alt="Car photo">
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="gallery">
                    <img src="https://via.placeholder.com/600x400?text=No+Image+Available" alt="No car image">
                </div>
            <?php endif; ?>

            <div class="details-grid">
                <div class="detail-card">
                    <h4>Year</h4>
                    <p><?= safeOutput($car['year']) ?></p>
                </div>
                <div class="detail-card">
                    <h4>Mileage</h4>
                    <p><?= formatMileage($car['mileage']) ?></p>
                </div>
                <div class="detail-card">
                    <h4>Transmission</h4>
                    <p><?= safeOutput($car['transmission']) ?></p>
                </div>
                <div class="detail-card">
                    <h4>Fuel Type</h4>
                    <p><?= safeOutput($car['fuel']) ?></p>
                </div>
                <div class="detail-card">
    <h4>Condition</h4>
    <p><?= safeOutput($car['car_condition'] ?? null, 'Not specified') ?></p>
</div>
                <div class="detail-card">
                    <h4>Location</h4>
                    <p><?= safeOutput($car['location']) ?></p>
                </div>
            </div>

            <?php if (!empty($car['description'])): ?>
                <div class="description">
                    <h3>Description</h3>
                    <p><?= nl2br(safeOutput($car['description'])) ?></p>
                </div>
            <?php endif; ?>

            <div class="seller-info">
                <h3>Seller Information</h3>
                <p><strong><?= safeOutput($car['name'], 'Private Seller') ?></strong></p>
                
                <?php if ($isLoggedIn): ?>
                    <div class="contact-details">
                        <?php if (!empty($car['email'])): ?>
                            <div class="contact-row">
                                <i class="fas fa-envelope"></i>
                                <span><?= safeOutput($car['email']) ?></span>
                            </div>
                        <?php endif; ?>
                        
                        <?php if (!empty($car['phone'])): ?>
                            <div class="contact-row">
                                <i class="fas fa-phone"></i>
                                <span><?= safeOutput($car['phone']) ?></span>
                            </div>
                        <?php endif; ?>
                        
                        <?php if (empty($car['email']) && empty($car['phone'])): ?>
                            <p>No contact information provided</p>
                        <?php endif; ?>
                    </div>
                <?php else: ?>
                    <div class="login-prompt">
                        <i class="fas fa-lock"></i> Please <a href="login.php">login</a> to view contact details
                    </div>
                <?php endif; ?>
            </div>

        <?php else: ?>
            <div style="text-align: center; padding: 3rem 0;">
                <i class="fas fa-car" style="font-size: 3rem; color: #ddd; margin-bottom: 1rem;"></i>
                <h3>Car Not Found</h3>
                <p>The car you're looking for doesn't exist or has been removed.</p>
                <a href="BrowseCars.php" class="back-link" style="margin: 1rem auto;"><i class="fa fa-arrow-left"></i> Back to Car Listings</a>
            </div>
        <?php endif; ?>
    </div>

    <script>
        // Simple image gallery functionality
        document.querySelectorAll('.gallery img').forEach(img => {
            img.addEventListener('click', function() {
                // You could enhance this to show a larger view/modal
                console.log('Image clicked:', this.src);
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