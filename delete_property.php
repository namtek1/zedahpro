<?php
require 'db.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id']) || 
    !isset($_GET['owner_id']) || !is_numeric($_GET['owner_id'])) {
    die(json_encode(["status" => "error", "message" => "Invalid request."]));
}

$id = intval($_GET['id']);
$owner_id = intval($_GET['owner_id']);

// Delete only if property belongs to this owner
$stmt = $conn->prepare("DELETE FROM housing_units WHERE id = ? AND owner_id = ?");
$stmt->bind_param("ii", $id, $owner_id);
$stmt->execute();

if ($stmt->affected_rows > 0) {
    $response = ["status" => "success", "message" => "Property deleted successfully."];
} else {
    $response = ["status" => "error", "message" => "Property not found or not yours."];
}

$stmt->close();
$conn->close();

header('Content-Type: application/json');
echo json_encode($response);
?>
