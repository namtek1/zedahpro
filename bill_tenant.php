<?php
// Database connection
$host = "localhost";
$user = "root";
$password = "";
$database = "housing";
$conn = new mysqli($host, $user, $password, $database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check tenant id
if (!isset($_GET['tenant_id'])) {
    die("Tenant not specified.");
}
$tenant_id = intval($_GET['tenant_id']);

// Fetch tenant details
$stmt = $conn->prepare("SELECT * FROM tenants WHERE id = ?");
$stmt->bind_param("i", $tenant_id);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows === 0) {
    die("Tenant not found.");
}
$tenant = $result->fetch_assoc();
$stmt->close();
$conn->close();

// Build email link
$to = $tenant['email'];
$subject = "Monthly Rent Reminder";
$body = "Dear " . $tenant['full_name'] . ",\n\n" .
        "This is a kind reminder from Zedah Realty to remit your monthly rent payment of Ksh " .
        number_format($tenant['amount_charged'], 2) .
        " for your unit " . $tenant['unit_number'] . " at " . $tenant['premises_name'] . ".\n\n" .
        "Thank you,\nZedah Realty";

$mail_link = "mailto:" . rawurlencode($to) .
             "?subject=" . rawurlencode($subject) .
             "&body=" . rawurlencode($body);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Bill Tenant - Zedah Realty</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      background: #f4f4f4;
      text-align: center;
      padding: 50px;
    }
    .card {
      background: white;
      padding: 20px;
      border-radius: 12px;
      max-width: 500px;
      margin: auto;
      box-shadow: 0px 4px 8px rgba(0,0,0,0.1);
    }
    h2 {
      color: #333;
    }
    p {
      font-size: 16px;
      margin: 10px 0;
    }
    a.button {
      display: inline-block;
      margin-top: 20px;
      padding: 12px 20px;
      background: gold;
      color: black;
      font-weight: bold;
      text-decoration: none;
      border-radius: 8px;
    }
    a.button:hover {
      background: darkorange;
      color: white;
    }
  </style>
</head>
<body>
  
  <div class="card">
    <h2>Billing Tenant</h2>
    <p><strong>Name:</strong> <?= htmlspecialchars($tenant['full_name']) ?></p>
    <p><strong>Email:</strong> <?= htmlspecialchars($tenant['email']) ?></p>
    <p><strong>Premises:</strong> <?= htmlspecialchars($tenant['premises_name']) ?></p>
    <p><strong>Unit:</strong> <?= htmlspecialchars($tenant['unit_number']) ?></p>
    <p><strong>Amount Due:</strong> Ksh <?= number_format($tenant['amount_charged'], 2) ?></p>

    <a class="button" href="<?= $mail_link ?>">Send Rent Reminder</a>
  </div>
</body>
</html>