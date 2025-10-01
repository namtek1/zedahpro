<?php
// update_password.php
$host = "localhost";
$user = "root";
$password = "";
$database = "housing";
$conn = new mysqli($host, $user, $password, $database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirm = $_POST['confirm_password'];

    if ($password !== $confirm) {
        die("Passwords do not match.");
    }

    // Hash password before saving
    $hashed = password_hash($password, PASSWORD_BCRYPT);

    // Update landlord's password
    $stmt = $conn->prepare("UPDATE landlords SET password=? WHERE email=?");
    $stmt->bind_param("ss", $hashed, $email);

    if ($stmt->execute()) {
        // Clean up used token
        $conn->query("DELETE FROM password_resets WHERE email='$email'");
        echo "Password updated successfully. <a href='login.html'>Login</a>";
    } else {
        echo "Error updating password.";
    }
}
?>
s