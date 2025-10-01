<?php
require 'db.php';

// Validate inputs
if (!isset($_GET['owner_id']) || !is_numeric($_GET['owner_id'])) {
    die(json_encode([]));
}
$owner_id = intval($_GET['owner_id']);
$county   = isset($_GET['county']) ? trim($_GET['county']) : '';
$location = isset($_GET['location']) ? trim($_GET['location']) : '';

// Base query: only fetch this owner's properties
$query  = "SELECT * FROM housing_units WHERE owner_id = ?";
$params = [$owner_id];
$types  = "i";

if (!empty($county)) {
    $query .= " AND county = ?";
    $params[] = $county;
    $types   .= "s";
}
if (!empty($location)) {
    $query .= " AND location = ?";
    $params[] = $location;
    $types   .= "s";
}

$stmt = $conn->prepare($query);
$stmt->bind_param($types, ...$params);
$stmt->execute();
$result = $stmt->get_result();

$properties = [];
while ($row = $result->fetch_assoc()) {
    // fetch images for this property from property_images table
    $imgStmt = $conn->prepare("SELECT image_path FROM property_images WHERE property_id = ?");
    $imgStmt->bind_param("i", $row['id']);
    $imgStmt->execute();
    $imgRes = $imgStmt->get_result();

    $images = [];
    while ($imgRow = $imgRes->fetch_assoc()) {
        $images[] = $imgRow['image_path'];
    }
    $imgStmt->close();

    // Build structured property response
    $properties[] = [
        "id"            => $row['id'],
        "premises_name" => $row['premises_name'],
        "county"        => $row['county'],
        "location"      => $row['location'],
        "type"          => $row['type'],
        "units"         => $row['units'],
        "price"         => $row['price'],
        "images"        => $images
    ];
}

$stmt->close();
$conn->close();

header('Content-Type: application/json');
echo json_encode($properties);
