<?php include 'session_check.php';
$isLoggedIn = isset($_SESSION['user_id']); ?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us - EasyCar.my</title>
    <link rel="stylesheet" href="style1.css">
    <style>
        /* Additional inline styles for better presentation */
        .team-member-img {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            object-fit: cover;
            margin: 0 auto 1rem;
            display: block;
            border: 3px solid #4A90E2;
        }
    </style>
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
                    <li><a href="HowItWorks.php">How It Works</a></li>
                    <li><a href="About.php" class="active">About</a></li>
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
                <h1 class="section-title">About EasyCar.my</h1>
                <p class="section-subtitle">Connecting buyers and sellers for better car deals</p>
            </div>

            <div style="max-width: 800px; margin: 0 auto 3rem;">
                <p style="margin-bottom: 1.5rem; line-height: 1.7;">
                    EasyCar.my was founded in 2025 with a simple mission: to make buying and selling used cars easier, safer, and more transparent for everyone. We saw that the traditional process was filled with hassles and uncertainty, so we built a platform that connects buyers and sellers directly while providing the tools and support needed for a smooth transaction.
                </p>
                
                <h2 style="margin-bottom: 1rem; color: #4A90E2;">Our Values</h2>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem; margin-bottom: 2rem;">
                    <div>
                        <h3 style="margin-bottom: 0.5rem;">Transparency</h3>
                        <p>We believe in open communication and full disclosure between buyers and sellers.</p>
                    </div>
                    <div>
                        <h3 style="margin-bottom: 0.5rem;">Trust</h3>
                        <p>Our platform is designed to build trust through verified profiles and secure messaging.</p>
                    </div>
                    <div>
                        <h3 style="margin-bottom: 0.5rem;">Simplicity</h3>
                        <p>We've streamlined the process to make it as straightforward as possible.</p>
                    </div>
                    <div>
                        <h3 style="margin-bottom: 0.5rem;">Value</h3>
                        <p>By cutting out middlemen, we help both buyers and sellers get better deals.</p>
                    </div>
                </div>

                <h2 style="margin-bottom: 1rem; color: #4A90E2;">The Team</h2>
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 2rem; margin-bottom: 2rem;">
                    <div style="text-align: center;">
                        <img src="photo_2025-05-02_21-20-07 (1).jpg" alt="Ismail - Founder & CEO" class="team-member-img">
                        <h3>Ismail</h3>
                        <p>Founder & CEO</p>
                    </div>
                    <div style="text-align: center;">
                        <img src="WhatsApp Image 2025-05-06 at 15.15.48.jpeg" alt="Zakwan Zuhairie - Head of Product" class="team-member-img">
                        <h3>Zakwan Zuhairie</h3>
                        <p>Head of Product</p>
                    </div>
                    <div style="text-align: center;">
                        <img src="WhatsApp Image 2025-05-06 at 11.05.43.jpeg" alt="Harresh - Customer Support" class="team-member-img">
                        <h3>Harresh</h3>
                        <p>Customer Support</p>
                    </div>
                </div>
            </div>

            <div class="cta-section" style="background: #4A90E2; color: white; padding: 3rem; text-align: center; border-radius: 16px;">
                <h2 class="cta-title">Join Our Community</h2>
                <p class="cta-subtitle">Become part of the growing EasyCar.my community today</p>
                <div class="cta-buttons" style="display: flex; gap: 1rem; justify-content: center; margin-top: 1.5rem;">
                    <a href="BrowseCars.php" class="btn-cta btn-cta-primary" style="padding: 0.75rem 1.5rem; background: white; color: #4A90E2; border-radius: 8px; text-decoration: none; font-weight: bold;">Browse Cars</a>
                    <a href="sellcar.php" class="btn-cta btn-cta-secondary" style="padding: 0.75rem 1.5rem; background: transparent; color: white; border: 2px solid white; border-radius: 8px; text-decoration: none; font-weight: bold;">Sell Your Car</a>
                </div>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="footer" style="background: #333; color: white; padding: 2rem 0; margin-top: 4rem;">
        <div class="container" style="max-width: 1200px; margin: 0 auto; text-align: center;">
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