<?php
session_start();
require 'db.php'; // contains $conn

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    // Prepare and execute query
    $stmt = $conn->prepare("SELECT id AS owner_id, first_name, password FROM landlords WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        // Verify password
        if (password_verify($password, $row['password'])) {
            // Store landlord info in session
            $_SESSION['owner_id'] = $row['owner_id']; // now matches upload.php expectation
            $_SESSION['landlord_name'] = $row['first_name'];

            echo "<script>
                alert('Login successful. Welcome, " . addslashes($row['first_name']) . "!');
                window.location.href = 'dashboard.php';
            </script>";
        } else {
            echo "<script>
                alert('Invalid email or password.');
                window.location.href = 'login.html';
            </script>";
        }
    } else {
        echo "<script>
            alert('Invalid email or password.');
            window.location.href = 'login.html';
        </script>";
    }

    $stmt->close();
    $conn->close();
}
?>
