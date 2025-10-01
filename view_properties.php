<?php 
require 'db.php';

// Ensure owner_id is provided
if (!isset($_GET['owner_id']) || !is_numeric($_GET['owner_id'])) {
    die("Owner ID is required.");
}
$owner_id = intval($_GET['owner_id']);

// Fetch unique counties for this owner
$counties = [];
$stmt = $conn->prepare("SELECT DISTINCT county FROM housing_units WHERE owner_id = ? ORDER BY county ASC");
$stmt->bind_param("i", $owner_id);
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $counties[] = $row['county'];
}
$stmt->close();

// Fetch unique locations for this owner
$locations = [];
$stmt = $conn->prepare("SELECT DISTINCT location FROM housing_units WHERE owner_id = ? ORDER BY location ASC");
$stmt->bind_param("i", $owner_id);
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $locations[] = $row['location'];
}
$stmt->close();

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Owner Properties</title>
<style>
    body { font-family: Arial, sans-serif; background: #0a0a23; margin: 0; padding: 0; color: white; }
    header { background: #d4af37; color: #0a0a23; padding: 1rem; text-align: center; font-size: 1.5rem; font-weight: bold; position: relative; }
    .nav-arrow { position: absolute; top: 50%; transform: translateY(-50%); font-size: 1.5rem; color: #0a0a23; text-decoration: none; font-weight: bold; }
    .nav-left { left: 10px; }
    .nav-right { right: 10px; }
    header img { display: block; margin: 0 auto; max-height: 60px; }
    .filter-bar { background: white; padding: 1rem; display: flex; gap: 1rem; justify-content: center; border-bottom: 3px solid #d4af37; }
    .filter-bar select { padding: 0.5rem; font-size: 1rem; border: 2px solid #d4af37; border-radius: 5px; background: #0a0a23; color: white; }
    table { width: 100%; border-collapse: collapse; margin-top: 1rem; background: white; color: black; }
    th, td { padding: 10px; border: 1px solid #ccc; text-align: center; vertical-align: middle; }
    th { background: #d4af37; color: #0a0a23; }
    .images-container { display: flex; gap: 5px; justify-content: center; flex-wrap: wrap; }
    .images-container img { max-width: 100px; border-radius: 4px; }
    .delete-btn { background: red; padding: 5px 10px; cursor: pointer; color: white; border: none; border-radius: 4px; font-weight: bold; }
    .delete-btn:hover { background: darkred; }
    footer { text-align: center; padding: 1rem; background: #000; color: gold; margin-top: 2rem; }
</style>
</head>
<body>

<header>
    <a href="login.html".html?owner_id=<?= $owner_id ?>" class="nav-arrow nav-left">⬅Go Back</a>
    <img src="logo.png" alt="Logo">
    <a href="index.html" class="nav-arrow nav-right">Go Home➡</a>
</header>

<div class="filter-bar">
    <select id="countyFilter">
        <option value="">All Counties</option>
        <?php foreach ($counties as $county): ?>
            <option value="<?= htmlspecialchars($county) ?>"><?= htmlspecialchars($county) ?></option>
        <?php endforeach; ?>
    </select>

    <select id="locationFilter">
        <option value="">All Locations</option>
        <?php foreach ($locations as $location): ?>
            <option value="<?= htmlspecialchars($location) ?>"><?= htmlspecialchars($location) ?></option>
        <?php endforeach; ?>
    </select>
</div>

<table id="propertiesTable">
    <thead>
        <tr>
            <th>Images</th>
            <th>Premises</th>
            <th>County</th>
            <th>Location</th>
            <th>Type</th>
            <th>Units</th>
            <th>Price (KES)</th>
            <th>Action</th>
            <th>Bill and Occupancy</th>
        </tr>
    </thead>
    <tbody></tbody>
</table>

<footer>
    &copy; <?= date("Y") ?> RATEK ZPM. All Rights Reserved.
</footer>

<script>
document.addEventListener("DOMContentLoaded", () => {
    const countyFilter = document.getElementById("countyFilter");
    const locationFilter = document.getElementById("locationFilter");
    const propertiesTableBody = document.querySelector("#propertiesTable tbody");
    const ownerId = <?= json_encode($owner_id) ?>;

    function loadProperties() {
        const county = countyFilter.value;
        const location = locationFilter.value;

        fetch(`fetch_properties.php?owner_id=${ownerId}&county=${encodeURIComponent(county)}&location=${encodeURIComponent(location)}`)
            .then(res => res.json())
            .then(data => {
                propertiesTableBody.innerHTML = "";
                if (data.length === 0) {
                    propertiesTableBody.innerHTML = `<tr><td colspan="8" style="color:white;background:#0a0a23;">No properties found.</td></tr>`;
                    return;
                }
                data.forEach(prop => {
                    let imgsHTML = "";
                    if (prop.images && prop.images.length > 0) {
                        imgsHTML = `<div class="images-container">` + 
                            prop.images.map(img => `<img src="${img}" alt="Property Image">`).join("") + 
                            `</div>`;
                    } else {
                        imgsHTML = `<img src="no-image.jpg" alt="No Image">`;
                    }

                    const row = `
                        <tr>
                            <td>${imgsHTML}</td>
                            <td>${prop.premises_name}</td>
                            <td>${prop.county}</td>
                            <td>${prop.location}</td>
                            <td>${prop.type}</td>
                            <td>${prop.units}</td>
                            <td>${prop.price}</td>
                            <td><button class="delete-btn" onclick="deleteProperty(${prop.id})">Delete</button></td>
                            <td><button class="delete-btn" style="background:green;" 
                                onclick="window.location.href='bilo1.php?property_id=${prop.id}&owner_id=${ownerId}'">BillO</button></td>
                        </tr>
                    `;
                    propertiesTableBody.innerHTML += row;
                });
            })
            .catch(err => {
                console.error("Error fetching properties:", err);
                propertiesTableBody.innerHTML = `<tr><td colspan="8" style="color:white;background:#0a0a23;">Error loading properties.</td></tr>`;
            });
    }

    window.deleteProperty = function(id) {
        if (!confirm("Are you sure you want to delete this property?")) return;
        fetch(`delete_property.php?id=${id}&owner_id=${ownerId}`, { method: 'GET' })
            .then(res => res.json())
            .then(response => {
                alert(response.message);
                loadProperties();
            })
            .catch(err => {
                console.error("Error deleting property:", err);
                alert("Error deleting property.");
            });
    };

    countyFilter.addEventListener("change", loadProperties);
    locationFilter.addEventListener("change", loadProperties);

    loadProperties();
});
</script>
</body>
</html>
