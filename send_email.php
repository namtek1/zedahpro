<?php
require 'db.php';

// Get JSON input
$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data['sender_email']) || !isset($data['property_id'])) {
    echo json_encode(["success" => false, "message" => "Invalid request"]);
    exit;
}

$senderEmail = htmlspecialchars($data['sender_email']);
$propertyId = intval($data['property_id']);

// Fetch property details
$stmt = $conn->prepare("SELECT county, location, type, price FROM housing_units WHERE id = ?");
$stmt->bind_param("i", $propertyId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode(["success" => false, "message" => "Property not found"]);
    exit;
}

$property = $result->fetch_assoc();
$conn->close();

// Build email subject & body
$to = "zedahrealty@gmail.com";
$subject = "Property Inquiry (ID: $propertyId)";
$body = "Hello Zedah Realty,%0D%0A%0D%0A"
      . "I am interested in the following property:%0D%0A"
      . "County: " . $property['county'] . "%0D%0A"
      . "Location: " . $property['location'] . "%0D%0A"
      . "Type: " . $property['type'] . "%0D%0A"
      . "Price: KES " . $property['price'] . "%0D%0A%0D%0A"
      . "Please contact me at: " . $senderEmail . "%0D%0A%0D%0A"
      . "Regards,%0D%0A"
      . $senderEmail;

// Encode properly for mailto
$mailtoLink = "mailto:$to?subject=" . rawurlencode($subject) . "&body=" . $body;

echo json_encode([
    "success" => true,
    "mailto" => $mailtoLink
]);
?>
