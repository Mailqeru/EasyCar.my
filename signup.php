<?php
$signupMessage = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Database connection
    $host = "sql206.infinityfree.com";
    $user = "if0_39046487";
    $pass = "111897Mail";
    $db   = "if0_39046487_epiz_12345678_easycar";

    $conn = new mysqli($host, $user, $pass, $db);
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Get POST data safely
    $email    = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';

    if (!empty($email) && !empty($password) && !empty($confirmPassword)) {
        if ($password !== $confirmPassword) {
            $signupMessage = "<span style='color:red;'>Passwords do not match!</span>";
        } else {
            // Check if email already exists
            $checkStmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
            $checkStmt->bind_param("s", $email);
            $checkStmt->execute();
            $checkStmt->store_result();

            if ($checkStmt->num_rows > 0) {
                $signupMessage = "<span style='color:red;'>Email is already registered!</span>";
            } else {
                $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
                $insertStmt = $conn->prepare("INSERT INTO users (email, password) VALUES (?, ?)");
                $insertStmt->bind_param("ss", $email, $hashedPassword);

                if ($insertStmt->execute()) {
                    $signupMessage = "<span style='color:green;'>Signup successful! You can now <a href='login.php'>login</a>.</span>";
                } else {
                    $signupMessage = "<span style='color:red;'>Signup failed. Please try again.</span>";
                }

                $insertStmt->close();
            }

            $checkStmt->close();
        }
    } else {
        $signupMessage = "<span style='color:red;'>Please fill in all fields!</span>";
    }

    $conn->close();
}
?>
<?php
session_start();
if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Signup - EasyCar.my</title>
    <link rel="stylesheet" href="style1.css" />
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
                    <li><a href="my_cars.php">My Cars</a></li>
                </ul>
            </nav>
            <div class="nav-buttons">
                <a href="login.php" class="btn btn-outline">Login</a>
                <a href="sellcar.php" class="btn btn-primary">Sell Your Car</a>
            </div>
        </div>
    </header>

    <!-- Signup Form -->
    <main class="how-it-works" style="padding-top: 6rem; min-height: 70vh;">
        <div class="container" style="max-width: 500px;">
            <div class="section-header">
                <h2 class="section-title">Create Account</h2>
                <p class="section-subtitle">Sign up to start using EasyCar.my</p>
                <?php if ($signupMessage): ?>
                    <p style="margin-top: 1rem;"><?php echo $signupMessage; ?></p>
                <?php endif; ?>
            </div>

            <div class="search-form" style="margin-bottom: 2rem;">
                <form method="POST" action="signup.php">
                    <div class="form-group" style="margin-bottom: 1.5rem;">
                        <label for="signupEmail">Email Address</label>
                        <input type="email" id="signupEmail" name="email" required />
                    </div>
                    <div class="form-group" style="margin-bottom: 1.5rem;">
                        <label for="signupPassword">Password</label>
                        <input type="password" id="signupPassword" name="password" required />
                    </div>
                    <div class="form-group" style="margin-bottom: 1.5rem;">
                        <label for="confirmPassword">Confirm Password</label>
                        <input type="password" id="confirmPassword" name="confirm_password" required />
                    </div>
                    <button type="submit" class="btn-search">Sign Up</button>
                </form>
            </div>

            <div style="text-align: center;">
                <p>Already have an account? <a href="login.php" style="color: #4A90E2;">Log in</a></p>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <p>&copy; 2025 EasyCar.my. All rights reserved. | <a href="#" style="color: white;">Privacy Policy</a> | <a href="#" style="color: white;">Terms of Service</a></p>
        </div>
    </footer>
</body>
</html>
