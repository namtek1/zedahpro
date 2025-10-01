<?php 
require 'db.php';

$county   = isset($_GET['county']) ? $_GET['county'] : '';
$location = isset($_GET['location']) ? $_GET['location'] : '';
$minPrice = isset($_GET['min']) && is_numeric($_GET['min']) ? (float)$_GET['min'] : '';
$maxPrice = isset($_GET['max']) && is_numeric($_GET['max']) ? (float)$_GET['max'] : '';

$query = "SELECT * FROM housing_units WHERE 1=1";
$params = [];
$types = "";

// county filter
if (!empty($county)) {
    $query .= " AND county = ?";
    $params[] = $county;
    $types .= "s";
}

// location filter
if (!empty($location)) {
    $query .= " AND location = ?";
    $params[] = $location;
    $types .= "s";
}

// min price filter
if ($minPrice !== '') {
    $query .= " AND price >= ?";
    $params[] = $minPrice;
    $types .= "d"; // double for price
}

// max price filter
if ($maxPrice !== '') {
    $query .= " AND price <= ?";
    $params[] = $maxPrice;
    $types .= "d";
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
        "id" => $row['id'],
        "owner_id" => $row['owner_id'],   // ✅ include owner_id
        "premises_name" => $row['premises_name'],
        "county" => $row['county'],
        "location" => $row['location'],
        "type" => $row['type'],
        "units" => $row['units'],
        "price" => $row['price'],
        "images" => $images
    ];
}

$stmt->close();
$conn->close();

header('Content-Type: application/json');
echo json_encode($properties);
