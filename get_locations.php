<?php
require 'db.php';

$county = isset($_GET['county']) ? $_GET['county'] : '';

$locations = [];
if ($county) {
    $stmt = $conn->prepare("SELECT DISTINCT location FROM housing_units WHERE county = ? ORDER BY location ASC");
    $stmt->bind_param("s", $county);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $locations[] = $row['location'];
    }
    $stmt->close();
}

$conn->close();

header('Content-Type: application/json');
echo json_encode($locations);
