<?php include 'session_check.php';
$isLoggedIn = isset($_SESSION['user_id']); ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>How It Works - EasyCar.my</title>
    <link rel="stylesheet" href="style1.css">
</head>
<body>
    <!-- Header -->
    <header class="header">
        <div class="nav-container">
            <div class="logo">EasyCar.my</div>
            <nav>
                <ul class="nav-links">
                    <li><a href="index.php">Home</a></li>
                    <li><a href="BrowseCars.php">Browse Cars</a></li>
                    <li><a href="HowItWorks.php" class="active">How It Works</a></li>
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

        </div>
    </header>

    <main class="how-it-works" style="padding-top: 6rem;">
        <div class="container">
            <div class="section-header">
                <h2 class="section-title">How EasyCar.my Works</h2>
                <p class="section-subtitle">A simple, transparent process for buying and selling used cars</p>
            </div>

            <div class="steps-grid">
                <div class="step-card">
                    <div class="step-icon">1</div>
                    <h3 class="step-title">For Buyers</h3>
                    <p class="step-description">
                        <strong>1. Search:</strong> Use our filters to find cars that match your needs.<br>
                        <strong>2. Connect:</strong> Message sellers directly through our platform.<br>
                        <strong>3. Inspect:</strong> Arrange to see the car in person.<br>
                        <strong>4. Purchase:</strong> Complete the transaction with our guidance.
                    </p>
                </div>
                <div class="step-card">
                    <div class="step-icon">2</div>
                    <h3 class="step-title">For Sellers</h3>
                    <p class="step-description">
                        <strong>1. List:</strong> Create a detailed listing with photos and information.<br>
                        <strong>2. Respond:</strong> Answer buyer questions through our messaging system.<br>
                        <strong>3. Show:</strong> Arrange viewings with serious buyers.<br>
                        <strong>4. Sell:</strong> Complete the sale with our support.
                    </p>
                </div>
                <div class="step-card">
                    <div class="step-icon">✓</div>
                    <h3 class="step-title">Safety & Support</h3>
                    <p class="step-description">
                        We provide secure messaging, transaction guidance, and safety tips for both buyers and sellers. Our team is available to answer any questions and help ensure a smooth process for all parties involved.
                    </p>
                </div>
            </div>

            <div class="cta-section" style="margin-top: 4rem; border-radius: 16px;">
                <h2 class="cta-title">Ready to Get Started?</h2>
                <p class="cta-subtitle">Join thousands of satisfied users who have successfully bought or sold their vehicles on our platform.</p>
                <div class="cta-buttons">
                    <a href="BrowseCars.php" class="btn-cta btn-cta-primary">Browse Cars</a>
                    <a href="sellcar.php" class="btn-cta btn-cta-secondary">Sell Your Car</a>
                </div>
            </div>
        </div>
    </main>

    <!-- Footer -->
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