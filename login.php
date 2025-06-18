<?php
// Start session
session_start();

// Initialize variables
$loginMessage = "";

// Handle form submission
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

    $email    = $_POST['email'] ;
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT id, password FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows == 1) {
        $stmt->bind_result($id, $hashedPassword);
        $stmt->fetch();

        if (password_verify($password, $hashedPassword)) {
            $_SESSION['user_id'] = $id;
            header("Location: index.php");
            exit();
        } else {
            $loginMessage = "Incorrect password!";
        }
    } else {
        $loginMessage = "Email not found!";
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
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - EasyCar.my</title>
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
                    <li><a href="my_cars.php">My Cars</a></li>
                </ul>
            </nav>
            <div class="nav-buttons">
                <a href="login.php" class="btn btn-outline active">Login</a>
                <a href="sellcar.php" class="btn btn-primary">Sell Your Car</a>
            </div>
        </div>
    </header>

    <main class="how-it-works" style="padding-top: 6rem; min-height: 70vh;">
        <div class="container" style="max-width: 500px;">
            <div class="section-header">
                <h2 class="section-title">Welcome Back</h2>
                <p class="section-subtitle">Sign in to your EasyCar.my account</p>
            </div>

            <?php if (!empty($loginMessage)): ?>
                <div style="color: red; margin-bottom: 1rem;"><?php echo htmlspecialchars($loginMessage); ?></div>
            <?php endif; ?>

            <div class="search-form" style="margin-bottom: 2rem;">
                <form method="POST" action="login.php">
                    <div class="form-group" style="margin-bottom: 1.5rem;">
                        <label for="loginEmail">Email Address</label>
                        <input type="email" id="loginEmail" name="email" required>
                    </div>
                    <div class="form-group" style="margin-bottom: 1.5rem;">
                        <label for="loginPassword">Password</label>
                        <input type="password" id="loginPassword" name="password" required>
                    </div>
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
                        <div>
                            <input type="checkbox" id="remember">
                            <label for="remember">Remember me</label>
                        </div>
                        <a href="#" style="color: #4A90E2;">Forgot password?</a>
                    </div>
                    <button type="submit" class="btn-search">Sign In</button>
                </form>
            </div>

            <div style="text-align: center;">
                <p>Don't have an account? <a href="signup.php" style="color: #4A90E2;">Sign up</a></p>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <p>&copy; 2025 EasyCar.my. All rights reserved. | 
            <a href="#" style="color: white;">Privacy Policy</a> | 
            <a href="#" style="color: white;">Terms of Service</a></p>
        </div>
    </footer>
</body>
</html>
