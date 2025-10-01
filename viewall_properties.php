<?php 
require 'db.php';

// Fetch unique counties
$counties = [];
$result = $conn->query("SELECT DISTINCT county FROM housing_units ORDER BY county ASC");
while ($row = $result->fetch_assoc()) {
    $counties[] = $row['county'];
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>All Properties</title>
<style>
    body { font-family: Arial, sans-serif; background: #0a0a23; margin: 0; padding: 0; color: white; }
    header { background: #d4af37; color: #0a0a23; padding: 1rem; text-align: center; font-size: 1.5rem; font-weight: bold; position: relative; }
    .nav-arrow { position: absolute; top: 50%; transform: translateY(-50%); font-size: 1.5rem; color: #0a0a23; text-decoration: none; font-weight: bold; }
    .nav-left { left: 10px; }
    .nav-right { right: 10px; }
    header img { display: block; margin: 0 auto; max-height: 60px; }
    .filter-bar { background: white; padding: 1rem; display: flex; gap: 1rem; justify-content: center; border-bottom: 3px solid #d4af37; }
    .filter-bar select, .filter-bar input { padding: 0.5rem; font-size: 1rem; border: 2px solid #d4af37; border-radius: 5px; background: #0a0a23; color: white; }
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
    <a href="ownerlogin.html" class="nav-arrow nav-left">⬅Back</a>
    <img src="logo.png" alt="Logo">
    <a href="index.html" class="nav-arrow nav-right">Home➡</a>
</header>

<div class="filter-bar">
    <!-- County Filter -->
    <select id="countyFilter">
        <option value="">All Counties</option>
        <?php foreach ($counties as $county): ?>
            <option value="<?= htmlspecialchars($county) ?>"><?= htmlspecialchars($county) ?></option>
        <?php endforeach; ?>
    </select>

    <!-- Location Filter (dynamic based on county) -->
    <select id="locationFilter">
        <option value="">All Locations</option>
    </select>

    <!-- Price Range Filter -->
    <input type="number" id="minPrice" placeholder="Min Price">
    <input type="number" id="maxPrice" placeholder="Max Price">
    <button onclick="loadProperties()">Apply</button>
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
    const minPrice = document.getElementById("minPrice");
    const maxPrice = document.getElementById("maxPrice");
    const propertiesTableBody = document.querySelector("#propertiesTable tbody");

    // Load locations dynamically based on county
    countyFilter.addEventListener("change", () => {
        const county = countyFilter.value;
        if (!county) {
            locationFilter.innerHTML = `<option value="">All Locations</option>`;
            loadProperties();
            return;
        }
        fetch(`get_locations.php?county=${encodeURIComponent(county)}`)
            .then(res => res.json())
            .then(data => {
                locationFilter.innerHTML = `<option value="">All Locations</option>`;
                data.forEach(loc => {
                    locationFilter.innerHTML += `<option value="${loc}">${loc}</option>`;
                });
                loadProperties();
            })
            .catch(err => console.error("Error loading locations:", err));
    });

    function loadProperties() {
        const county = countyFilter.value;
        const location = locationFilter.value;
        const min = minPrice.value;
        const max = maxPrice.value;

        fetch(`fetchall_properties.php?county=${encodeURIComponent(county)}&location=${encodeURIComponent(location)}&min=${min}&max=${max}`)
            .then(res => res.json())
            .then(data => {
                propertiesTableBody.innerHTML = "";
                if (data.length === 0) {
                    propertiesTableBody.innerHTML = `<tr><td colspan="8" style="color:white;background:#0a0a23;">No properties found.</td></tr>`;
                    return;
                }

                // ✅ Group by owner_id
                const grouped = {};
                data.forEach(prop => {
                    if (!grouped[prop.owner_id]) grouped[prop.owner_id] = [];
                    grouped[prop.owner_id].push(prop);
                });

                // Render each group
                Object.keys(grouped).forEach(owner_id => {
                    // Add group header row
                    propertiesTableBody.innerHTML += `
                        <tr style="background:#222;color:gold;font-weight:bold;">
                            <td colspan="8">Owner ID: ${owner_id}</td>
                        </tr>
                    `;

                    // Add properties under this owner
                    grouped[owner_id].forEach(prop => {
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
                            </tr>
                        `;
                        propertiesTableBody.innerHTML += row;
                    });
                });
            })
            .catch(err => {
                console.error("Error fetching properties:", err);
                propertiesTableBody.innerHTML = `<tr><td colspan="8" style="color:white;background:#0a0a23;">Error loading properties.</td></tr>`;
            });
    }

    window.deleteProperty = function(id) {
        if (!confirm("Are you sure you want to delete this property?")) return;
        fetch(`delete_property.php?id=${id}`, { method: 'GET' })
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

    locationFilter.addEventListener("change", loadProperties);
    minPrice.addEventListener("input", loadProperties);
    maxPrice.addEventListener("input", loadProperties);

    loadProperties();
});
</script>

</body>
</html>
