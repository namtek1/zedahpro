<?php
require 'db.php';

$county   = isset($_GET['county']) ? trim($_GET['county']) : '';
$location = isset($_GET['location']) ? trim($_GET['location']) : '';
$type     = isset($_GET['type']) ? trim($_GET['type']) : '';
$minPrice = isset($_GET['minPrice']) && is_numeric($_GET['minPrice']) ? intval($_GET['minPrice']) : 0;
$maxPrice = isset($_GET['maxPrice']) && is_numeric($_GET['maxPrice']) ? intval($_GET['maxPrice']) : 0;

$query  = "SELECT * FROM housing_units WHERE 1=1";
$params = [];
$types  = "";

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
if (!empty($type)) {
    $query .= " AND type = ?";
    $params[] = $type;
    $types   .= "s";
}
if ($minPrice > 0) {
    $query .= " AND price >= ?";
    $params[] = $minPrice;
    $types   .= "i";
}
if ($maxPrice > 0) {
    $query .= " AND price <= ?";
    $params[] = $maxPrice;
    $types   .= "i";
}

$stmt = $conn->prepare($query);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();

$properties = [];
while ($row = $result->fetch_assoc()) {
    // fetch images for this property
    $imgStmt = $conn->prepare("SELECT image_path FROM property_images WHERE property_id = ?");
    $imgStmt->bind_param("i", $row['id']);
    $imgStmt->execute();
    $imgRes = $imgStmt->get_result();

    $images = [];
    while ($imgRow = $imgRes->fetch_assoc()) {
        $images[] = $imgRow['image_path'];
    }
    $imgStmt->close();

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
