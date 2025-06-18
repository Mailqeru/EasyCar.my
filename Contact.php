<?php
$feedback = "";

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Database connection
    $host = "sql206.infinityfree.com"; // Replace with your actual DB host
    $user = "if0_39046487";
    $pass = "111897Mail";
    $dbname = "if0_39046487_epiz_12345678_easycar";

    $conn = new mysqli($host, $user, $pass, $dbname);

    if ($conn->connect_error) {
        $feedback = "Connection failed: " . $conn->connect_error;
    } else {
        $name    = $conn->real_escape_string($_POST['name']);
        $email   = $conn->real_escape_string($_POST['email']);
        $subject = $conn->real_escape_string($_POST['subject']);
        $message = $conn->real_escape_string($_POST['message']);

        $sql = "INSERT INTO contact_messages (name, email, subject, message)
                VALUES ('$name', '$email', '$subject', '$message')";

        if ($conn->query($sql) === TRUE) {
            $feedback = "✅ Your message has been sent successfully!";
        } else {
            $feedback = "❌ Error: " . $conn->error;
        }

        $conn->close();
    }
}
?>

<?php include 'session_check.php';
$isLoggedIn = isset($_SESSION['user_id']); ?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us - EasyCar.my</title>
    <link rel="stylesheet" href="style1.css">
</head>
<body>
<header class="header">
    <div class="nav-container">
        <div class="logo">EasyCar.my</div>
        <nav>
            <ul class="nav-links">
                <li><a href="index.php">Home</a></li>
                <li><a href="BrowseCars.php">Browse Cars</a></li>
                <li><a href="HowItWorks.php">How It Works</a></li>
                <li><a href="About.php">About</a></li>
                <li><a href="Contact.php" class="active">Contact</a></li>
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

    </div>
</header>

<main class="how-it-works" style="padding-top: 6rem;">
    <div class="container">
        <div class="section-header">
            <h2 class="section-title">Contact EasyCar.my</h2>
            <p class="section-subtitle">We're here to help with any questions or concerns</p>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 3rem; margin-bottom: 4rem;">
            <div>
                <h3 style="margin-bottom: 1.5rem; color: #4A90E2;">Get in Touch</h3>
                <div style="margin-bottom: 2rem;">
                    <h4>Customer Support</h4>
                    <p>Email: support@easycar.my</p>
                    <p>Phone: +1 (555) 123-4567</p>
                    <p>Hours: Monday-Friday, 9am-6pm</p>
                </div>
                <div style="margin-bottom: 2rem;">
                    <h4>Headquarters</h4>
                    <p>123 Automotive Way</p>
                    <p>Kuala Lumpur, Malaysia</p>
                </div>
                <div>
                    <h4>Follow Us</h4>
                    <div style="display: flex; gap: 1rem;">
                        <a href="#" style="color: #4A90E2;">Facebook</a>
                        <a href="#" style="color: #4A90E2;">Twitter</a>
                        <a href="#" style="color: #4A90E2;">Instagram</a>
                    </div>
                </div>
            </div>

            <div>
                <h3 style="margin-bottom: 1.5rem; color: #4A90E2;">Send Us a Message</h3>

                <?php if (!empty($feedback)): ?>
                    <div style="background: #e9f6ff; padding: 1rem; margin-bottom: 1.5rem; border-left: 4px solid #4A90E2;">
                        <?= $feedback ?>
                    </div>
                <?php endif; ?>

                <form style="display: grid; gap: 1.5rem;" method="POST" action="Contact.php">
                    <div class="form-group">
                        <label for="name">Your Name</label>
                        <input type="text" id="name" name="name" required>
                    </div>
                    <div class="form-group">
                        <label for="email">Email Address</label>
                        <input type="email" id="email" name="email" required>
                    </div>
                    <div class="form-group">
                        <label for="subject">Subject</label>
                        <select id="subject" name="subject">
                            <option value="general">General Inquiry</option>
                            <option value="support">Support Request</option>
                            <option value="feedback">Feedback</option>
                            <option value="business">Business Inquiry</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="message">Message</label>
                        <textarea id="message" name="message" rows="5" required></textarea>
                    </div>
                    <button type="submit" class="btn-search">Send Message</button>
                </form>
            </div>
        </div>
    </div>
</main>

<footer class="footer">
    <div class="container">
        <p>&copy; 2025 EasyCar.my. All rights reserved. | <a href="#" style="color: white;">Privacy Policy</a> | <a href="#" style="color: white;">Terms of Service</a></p>
    </div>
</footer>
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
