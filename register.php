<?php
require 'db.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $firstname = trim($_POST["firstname"]);
    $lastname = trim($_POST["lastname"]);
    $username = trim($_POST["username"]);
    $email = trim($_POST["email"]);
    $password = trim($_POST["password"]);
    $plan = $_POST["plan"] ?? null;
    $billing = $_POST["billing"] ?? null;
    $price = $_POST["price"] ?? null;


    // Validate minimum password length
    if (strlen($password) < 5) {
        die("<script>alert('Password must be at least 5 characters.'); window.history.back();</script>");
    }

    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

   $stmt = $conn->prepare("INSERT INTO landlords 
  (first_name, second_name, username, email, password, plan, billing, price) 
  VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
   $stmt->bind_param("sssssssi", $firstname, $lastname, $username, $email, $hashedPassword, $plan, $billing, $price);


    if ($stmt->execute()) {
        echo "<script>
            alert('Registration successful.');
            window.location.href = 'login.html';
        </script>";
    } else {
        echo "<script>
            alert('Error: " . addslashes($stmt->error) . "');
            window.history.back();
        </script>";
    }

    $stmt->close();
    $conn->close();
}
?>
