<?php
$host = "ftpupload.net";
$user = "if0_39046487";
$pass = "111897Mail";
$db   = "epiz_12345678_easycar";

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$email    = $_POST['email'];
$password = $_POST['password'];

$stmt = $conn->prepare("SELECT id, password FROM users WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows == 1) {
    $stmt->bind_result($id, $hashedPassword);
    $stmt->fetch();
    
    if (password_verify($password, $hashedPassword)) {
        echo "success";
    } else {
        echo "Incorrect password!";
    }
} else {
    echo "Email not found!";
}

$conn->close();
?>
