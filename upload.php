<?php
session_start(); // Ensure sessions are active

// ========================
// CHECK LOGIN
// ========================
if (!isset($_SESSION['owner_id'])) {
    die("Error: You must be logged in as a landlord to upload properties.");
}
$owner_id = $_SESSION['owner_id'];

// ========================
// CONFIGURATION
// ========================
$uploadFolder = "uploads/"; // Relative path
$targetDir = __DIR__ . "/" . $uploadFolder; // Absolute path

if (!is_dir($targetDir)) {
    mkdir($targetDir, 0777, true);
}

// ========================
// DATABASE CONNECTION
// ========================
$conn = new mysqli("localhost", "root", "", "housing");
if ($conn->connect_error) {
    die("Database Connection Failed: " . $conn->connect_error);
}

// ========================
// INSERT PROPERTY
// ========================
$stmt = $conn->prepare("INSERT INTO housing_units (owner_id, county, location, premises_name, type, units, price) VALUES (?, ?, ?, ?, ?, ?, ?)");
$stmt->bind_param(
    "issssii",
    $owner_id,
    $_POST['county'],
    $_POST['location'],
    $_POST['premises_name'],
    $_POST['type'],
    $_POST['units'],
    $_POST['price']
);

if (!$stmt->execute()) {
    die("Property Insert Failed: " . $stmt->error);
}

$property_id = $stmt->insert_id;
$stmt->close();

// ========================
// HANDLE MULTIPLE IMAGES
// ========================
foreach ($_FILES['images']['tmp_name'] as $key => $tmp_name) {
    if ($_FILES['images']['error'][$key] === UPLOAD_ERR_OK) {
        $filename = time() . "_" . basename($_FILES['images']['name'][$key]);
        $targetFile = $targetDir . $filename;
        $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

        if (in_array($imageFileType, $allowed)) {
            if (move_uploaded_file($tmp_name, $targetFile)) {
                $imagePathForDB = $uploadFolder . $filename;
                $imgStmt = $conn->prepare("INSERT INTO property_images (property_id, image_path) VALUES (?, ?)");
                $imgStmt->bind_param("is", $property_id, $imagePathForDB);
                $imgStmt->execute();
                $imgStmt->close();
            }
        }
    }
}

// ========================
// SUCCESS + REDIRECT view_properties.php?owner_id={$owner_id}
// ========================
echo "<script>
    alert('Property uploaded successfully!');
    window.location.href = 'upload.html';
</script>";

$conn->close();
?>
