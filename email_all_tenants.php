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

// Get selected premise
if (!isset($_GET['premises_name']) || empty($_GET['premises_name'])) {
    die("Premise not selected.");
}

$premises_name = $conn->real_escape_string($_GET['premises_name']);

// Fetch tenant emails from the selected premise
$result = $conn->query("SELECT email FROM tenants WHERE premises_name = '$premises_name' AND email IS NOT NULL AND email != ''");

$emails = [];
while ($row = $result->fetch_assoc()) {
    $emails[] = $row['email'];
}

if (empty($emails)) {
    die("No tenants with email addresses found for this premise.");
}

// Convert emails into comma-separated string
$emailList = implode(",", $emails);

// Email subject and body
$subject = "Message to tenants at $premises_name";
$body = "Type your message here...\n\nRegards,\nZedah Property Solutions";

// Build mailto link (bcc keeps recipients hidden)
$mailto = "mailto:zedahrealty@gmail.com"
        . "?bcc=" . rawurlencode($emailList)
        . "&subject=" . rawurlencode($subject)
        . "&body=" . rawurlencode($body);

// Redirect to email client
header("Location: $mailto");
exit();
?>
