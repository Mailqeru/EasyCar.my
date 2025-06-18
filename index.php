<?php
session_start();
$isLoggedIn = isset($_SESSION['user_id']);
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EasyCar.my - Find Your Perfect Used Car</title>
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


            <div class="profile-dropdown" id="userProfile" style="display: none;">
            <div class="user-profile">
            <div class="user-avatar" id="userAvatar">U</div>
            <span class="user-name" id="userName">User</span>
            </div>
    <div class="dropdown-menu">
        <a href="profile.html">My Profile</a>
        <a href="my-listings.html">My Listings</a>
        <a href="#" id="logoutBtn">Logout</a>
    </div>
</div>
        </div>
    </header>

    <!-- Hero Section -->
    <section id="home" class="hero">
        <div class="hero-container">
            <div class="hero-content">
                <h1>Find Your Perfect Used Car</h1>
                <p>Connect directly with sellers and find the best deals on quality used cars in your area.</p>
            </div>
            <div class="hero-image">
                <div class="car-illustration">
                    <div class="car-body"></div>
                </div>
            </div>
        </div>
        <div class="container">
            <div class="search-form">
                <div class="form-grid">
                    <div class="form-group">
                        <label for="make">Make</label>
                        <select id="make">
                            <option value="">Select Make</option>
                            <option value="toyota">Toyota</option>
                            <option value="honda">Honda</option>
                            <option value="ford">Ford</option>
                            <option value="chevrolet">Chevrolet</option>
                            <option value="nissan">Nissan</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="price">Price Range</label>
                        <select id="price">
                            <option value="">Any Price</option>
                            <option value="0-10000">Under $10,000</option>
                            <option value="10000-20000">$10,000 - $20,000</option>
                            <option value="20000-30000">$20,000 - $30,000</option>
                            <option value="30000+">$30,000+</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="location">Location</label>
                        <input type="text" id="location" placeholder="City or ZIP code">
                    </div>
                </div>
                <button class="btn-search" onclick="searchCars()">Search Cars</button>
            </div>
        </div>
    </section>

    <!-- Featured Cars Section -->
    <section id="browse" class="featured-section">
        <div class="container">
            <div class="section-header">
                <h2 class="section-title">Featured Cars</h2>
                <p class="section-subtitle">Browse our selection of quality used cars from private sellers</p>
            </div>
            <div class="cars-grid">
                <div class="car-card">
                    <div class="car-image">
                        <div class="car-badge badge-featured">Featured</div>
                        🚗
                    </div>
                    <div class="car-info">
                        <div class="car-header">
                            <div>
                                <h3 class="car-title">2018 Toyota Camry</h3>
                                <p class="car-subtitle">SE Sedan</p>
                            </div>
                            <div class="car-price">$15,900</div>
                        </div>
                        <div class="car-details">
                            <div class="detail-item">
                                <span class="detail-label">Mileage</span>
                                <span class="detail-value">45,230 miles</span>
                            </div>
                            <div class="detail-item">
                                <span class="detail-label">Transmission</span>
                                <span class="detail-value">Automatic</span>
                            </div>
                            <div class="detail-item">
                                <span class="detail-label">Fuel</span>
                                <span class="detail-value">Gasoline</span>
                            </div>
                            <div class="detail-item">
                                <span class="detail-label">Location</span>
                                <span class="detail-value">Portland, OR</span>
                            </div>
                        </div>
                        <div class="car-actions">
                            <button class="btn-contact" onclick="contactSeller('Toyota Camry')">Contact Seller</button>
                            <button class="btn-favorite" onclick="toggleFavorite(this)">♡</button>
                        </div>
                    </div>
                </div>

                <div class="car-card">
                    <div class="car-image">
                        🚗
                    </div>
                    <div class="car-info">
                        <div class="car-header">
                            <div>
                                <h3 class="car-title">2017 Honda Accord</h3>
                                <p class="car-subtitle">EX-L V6 Sedan</p>
                            </div>
                            <div class="car-price">$14,500</div>
                        </div>
                        <div class="car-details">
                            <div class="detail-item">
                                <span class="detail-label">Mileage</span>
                                <span class="detail-value">52,750 miles</span>
                            </div>
                            <div class="detail-item">
                                <span class="detail-label">Transmission</span>
                                <span class="detail-value">Automatic</span>
                            </div>
                            <div class="detail-item">
                                <span class="detail-label">Fuel</span>
                                <span class="detail-value">Gasoline</span>
                            </div>
                            <div class="detail-item">
                                <span class="detail-label">Location</span>
                                <span class="detail-value">Seattle, WA</span>
                            </div>
                        </div>
                        <div class="car-actions">
                            <button class="btn-contact" onclick="contactSeller('Honda Accord')">Contact Seller</button>
                            <button class="btn-favorite" onclick="toggleFavorite(this)">♡</button>
                        </div>
                    </div>
                </div>

                <div class="car-card">
                    <div class="car-image">
                        <div class="car-badge badge-new">New Listing</div>
                        🚗
                    </div>
                    <div class="car-info">
                        <div class="car-header">
                            <div>
                                <h3 class="car-title">2019 Ford Escape</h3>
                                <p class="car-subtitle">SEL SUV</p>
                            </div>
                            <div class="car-price">$17,800</div>
                        </div>
                        <div class="car-details">
                            <div class="detail-item">
                                <span class="detail-label">Mileage</span>
                                <span class="detail-value">31,200 miles</span>
                            </div>
                            <div class="detail-item">
                                <span class="detail-label">Transmission</span>
                                <span class="detail-value">Automatic</span>
                            </div>
                            <div class="detail-item">
                                <span class="detail-label">Fuel</span>
                                <span class="detail-value">Gasoline</span>
                            </div>
                            <div class="detail-item">
                                <span class="detail-label">Location</span>
                                <span class="detail-value">Denver, CO</span>
                            </div>
                        </div>
                        <div class="car-actions">
                            <button class="btn-contact" onclick="contactSeller('Ford Escape')">Contact Seller</button>
                            <button class="btn-favorite" onclick="toggleFavorite(this)">♡</button>
                        </div>
                    </div>
                </div>
            </div>
            <a href="BrowseCars.php" class="view-all-btn">View All Listings</a>
        </div>
    </section>

    <!-- How It Works Section -->
    <section id="how-it-works" class="how-it-works">
        <div class="container">
            <div class="section-header">
                <h2 class="section-title">How It Works</h2>
                <p class="section-subtitle">Simple steps to buy or sell your used car</p>
            </div>
            <div class="steps-grid">
                <div class="step-card">
                    <div class="step-icon">+</div>
                    <h3 class="step-title">Create a Listing</h3>
                    <p class="step-description">Take photos of your car, add details, and set your price. Our platform makes it easy to create an attractive listing.</p>
                </div>
                <div class="step-card">
                    <div class="step-icon">👥</div>
                    <h3 class="step-title">Connect with Buyers/Sellers</h3>
                    <p class="step-description">Our messaging system allows you to communicate directly with interested buyers or sellers to answer questions.</p>
                </div>
                <div class="step-card">
                    <div class="step-icon">✓</div>
                    <h3 class="step-title">Complete the Sale</h3>
                    <p class="step-description">Meet safely, complete paperwork, and finalize the sale. We provide guidance on the transfer process.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Testimonials Section -->
    <section class="testimonials">
        <div class="container">
            <div class="section-header">
                <h2 class="section-title">What Our Users Say</h2>
                <p class="section-subtitle">Real experiences from buyers and sellers</p>
            </div>
            <div class="testimonials-grid">
                <div class="testimonial-card">
                    <div class="testimonial-content">
                        "I sold my Honda Civic in just 3 days! The platform made it so easy to create a listing and connect with serious buyers. Highly recommended!"
                    </div>
                    <div class="testimonial-author">
                        <div class="author-avatar">M</div>
                        <div class="author-info">
                            <h4>Michael T.</h4>
                            <p>Seller</p>
                            <div class="rating">★★★★★</div>
                        </div>
                    </div>
                </div>
                <div class="testimonial-card">
                    <div class="testimonial-content">
                        "Found my dream car at a great price! The detailed listings and direct communication with the seller made the process transparent and stress-free."
                    </div>
                    <div class="testimonial-author">
                        <div class="author-avatar">S</div>
                        <div class="author-info">
                            <h4>Sarah K.</h4>
                            <p>Buyer</p>
                            <div class="rating">★★★★★</div>
                        </div>
                    </div>
                </div>
                <div class="testimonial-card">
                    <div class="testimonial-content">
                        "Got $2,000 more for my car than dealer trade-in offers! The platform connected me with buyers who appreciated my well-maintained vehicle."
                    </div>
                    <div class="testimonial-author">
                        <div class="author-avatar">J</div>
                        <div class="author-info">
                            <h4>James R.</h4>
                            <p>Seller</p>
                            <div class="rating">★★★★★</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="cta-section">
        <div class="container">
            <h2 class="cta-title">Ready to Buy or Sell Your Car?</h2>
            <p class="cta-subtitle">Join thousands of satisfied users who have successfully bought or sold their vehicles on our platform.</p>
            <div class="cta-buttons">
                <a href="sellcar.php" class="btn-cta btn-cta-primary" onclick="getStarted()">Get Started Now</a>
                <a href="HowItWorks.html" class="btn-cta btn-cta-secondary">Learn More</a>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <p>&copy; 2025 EasyCar.my. All rights reserved. | <a href="#" style="color: white;">Privacy Policy</a> | <a href="#" style="color: white;">Terms of Service</a></p>
        </div>
    </footer>

    <script>
        function searchCars() {
            const make = document.getElementById('make').value;
            const price = document.getElementById('price').value;
            const location = document.getElementById('location').value;
            
            // In a real implementation, this would redirect to search results
            alert(`Searching for ${make} cars in ${location} with price range ${price}`);
            window.location.href = `BrowseCars.php?make=${make}&price=${price}&location=${location}`;
        }

        function contactSeller(carModel) {
            alert(`Connecting you with the seller of ${carModel}`);
        }

        function toggleFavorite(button) {
            button.textContent = button.textContent === '♡' ? '♥' : '♡';
        }

        function getStarted() {
            // This would redirect to registration or listing creation
            console.log('Get started clicked');
        }
    </script>

    

    <script>
    // Check auth state
    auth.onAuthStateChanged((user) => {
        const authButtons = document.getElementById('authButtons');
        const userProfile = document.getElementById('userProfile');
        
        if (user) {
            // User is signed in
            authButtons.style.display = 'none';
            userProfile.style.display = 'block';
            
            // Get user data from Firestore
            db.collection('users').doc(user.uid).get()
                .then((doc) => {
                    if (doc.exists) {
                        const userData = doc.data();
                        document.getElementById('userName').textContent = userData.name;
                        document.getElementById('userAvatar').textContent = userData.name.charAt(0).toUpperCase();
                    }
                });
        } else {
            // User is signed out
            authButtons.style.display = 'flex';
            userProfile.style.display = 'none';
        }
    });

    // Logout functionality
    document.getElementById('logoutBtn')?.addEventListener('click', (e) => {
        e.preventDefault();
        auth.signOut()
            .then(() => {
                window.location.href = 'Home.html';
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