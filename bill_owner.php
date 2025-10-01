<?php
// --- DB CONNECTION ---
$host = "localhost";
$user = "root";
$password = "";
$database = "housing";

$conn = new mysqli($host, $user, $password, $database);
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

// --- VALIDATE INPUT ---
if (!isset($_GET['owner_id']) || !ctype_digit($_GET['owner_id'])) {
  die("Invalid request: missing or bad owner_id.");
}
$owner_id = (int) $_GET['owner_id'];

// --- FETCH OWNER ROW ---
$stmt = $conn->prepare("SELECT first_name, second_name, email, plan, billing, price FROM landlords WHERE id = ?");
$stmt->bind_param("i", $owner_id);
$stmt->execute();
$res = $stmt->get_result();

if ($res->num_rows === 0) {
  $stmt->close();
  $conn->close();
  die("Owner not found.");
}

$row = $res->fetch_assoc();
$stmt->close();
$conn->close();

// --- BUILD EMAIL ---
$firstName = $row['first_name'];
$fullName  = trim($row['first_name'] . ' ' . $row['second_name']);
$email     = $row['email'];
$planName  = $row['plan'];              // e.g., Starter, Professional, Enterprise
$billing   = strtolower($row['billing']); // "monthly" or "yearly"
$amount    = number_format((float)$row['price']); // KES formatted

$subject = ($billing === 'yearly') ? "Yearly Billing Reminder" : "Monthly Billing Reminder";

$bodyText = "Dear {$firstName},\r\n\r\n"
          . "This is a friendly reminder to remit your " . ucfirst($billing) . " subscription for the {$planName} plan.\r\n"
          . "Amount Due: KES {$amount}\r\n"
          . "Billing Cycle: " . ucfirst($billing) . "\r\n\r\n"
          . "Please complete the remittance at your earliest convenience.\r\n\r\n"
          . "Regards,\r\n"
          . "Zedah Realty\r\n"
          . "zedahrealty@gmail.com";

$mailto = "mailto:" . rawurlencode($email)
        . "?subject=" . rawurlencode($subject)
        . "&body=" . rawurlencode($bodyText);

// Note: The 'From' address is controlled by the user's default mail account.
// To send from zedarealty@gmail.com, make sure that account is selected in the mail client.
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Billing Reminder</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <style>
    body { font-family: Segoe UI, sans-serif; background:#0b0c2a; color: gold; margin:0; display:flex; align-items:center; justify-content:center; height:100vh; }
    .card { background:#000; border:1px solid gold; padding:24px; border-radius:10px; text-align:center; box-shadow:0 0 10px gold; width: min(520px, 92vw); }
    a.btn { display:inline-block; margin-top:14px; background:gold; color:#000; padding:10px 16px; border-radius:6px; text-decoration:none; }
    a.btn:hover { background:#ffcc00; }
    p { margin: 6px 0; }
    .muted { color:#ddd; font-size: 14px; }
    code { color:#fff; }
  </style>
</head>
<body>
  <div class="card">
    <h2>Preparing Email…</h2>
    <p>To: <strong><?php echo htmlspecialchars($fullName); ?></strong> (<?php echo htmlspecialchars($email); ?>)</p>
    <p>Plan: <strong><?php echo htmlspecialchars($planName); ?></strong> — <?php echo ucfirst(htmlspecialchars($billing)); ?> (KES <?php echo $amount; ?>)</p>
    <a class="btn" id="openMail" href="<?php echo $mailto; ?>">Open Email App</a>
    <p class="muted">If your email app doesn’t open automatically, click the button above.</p>
  </div>

  <script>
    // Try to auto-open the default email client
    (function(){
      var link = document.getElementById('openMail');
      // small delay so the page renders first
      setTimeout(function(){ window.location.href = link.href; }, 250);
    })();
  </script>
</body>
</html>
