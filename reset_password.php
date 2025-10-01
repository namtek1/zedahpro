<?php
// reset_password.php
$host = "localhost";
$user = "root";
$password = "";
$database = "housing";
$conn = new mysqli($host, $user, $password, $database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $conn->real_escape_string($_POST['email']);

    // Check if email exists in landlords table
    $check = $conn->query("SELECT * FROM landlords WHERE email='$email'");
    if ($check->num_rows > 0) {
        // Generate token & expiry (1 hour validity)
        $token = bin2hex(random_bytes(50));
        $expires_at = date("Y-m-d H:i:s", strtotime("+1 hour"));

        // Store reset token
        $conn->query("INSERT INTO password_resets (email, token, expires_at) 
                      VALUES ('$email', '$token', '$expires_at')");

        // Reset link
        $reset_link = "http://localhost/ZEDAH/new_password.php?token=$token";

        // Email content
        $subject = "Password Reset Request";
        $message = "Click the link below to reset your password:\n\n$reset_link\n\nIf you didn't request this, ignore this email.";
        $headers = "From: zedahrealty@gmail.com";

        // Send email
        if (mail($email, $subject, $message, $headers)) {
            echo "A password reset link has been sent to your email.";
        } else {
            echo "Error sending reset email.";
        }
    } else {
        echo "No account found with that email.";
    }
}
?>
