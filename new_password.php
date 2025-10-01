<?php
// new_password.php
$host = "localhost";
$user = "root";
$password = "";
$database = "housing";
$conn = new mysqli($host, $user, $password, $database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$token = $_GET['token'] ?? '';

if (!$token) {
    die("Invalid request.");
}

// Verify token
$stmt = $conn->prepare("SELECT email, expires_at FROM password_resets WHERE token=?");
$stmt->bind_param("s", $token);
$stmt->execute();
$result = $stmt->get_result();
$reset = $result->fetch_assoc();

if (!$reset || strtotime($reset['expires_at']) < time()) {
    die("Reset link is invalid or expired.");
}

$email = $reset['email'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Create New Password</title>
  <style>
    body { font-family: Arial; background: #f2f2f2; display: flex; justify-content: center; align-items: center; height: 100vh; }
    .form-container { background: #fff; padding: 30px; border-radius: 10px; box-shadow: 0 0 15px rgba(0,0,0,0.3); }
    input { width: 100%; padding: 12px; margin: 10px 0; border-radius: 6px; border: 1px solid #ccc; }
    button { width: 100%; padding: 12px; background: black; color: gold; border: none; border-radius: 6px; cursor: pointer; }
  </style>
</head>
<body>
  <div class="form-container">
    <h2>Set New Password</h2>
    <form action="update_password.php" method="POST">
      <input type="hidden" name="email" value="<?php echo htmlspecialchars($email); ?>">
      <input type="password" name="password" placeholder="Enter new password" required>
      <input type="password" name="confirm_password" placeholder="Confirm new password" required>
      <button type="submit">Update Password</button>
    </form>
  </div>
</body>
</html>
