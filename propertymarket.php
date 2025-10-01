<?php
require 'db.php';

// Fetch unique counties
$counties = [];
$countyResult = $conn->query("SELECT DISTINCT county FROM housing_units ORDER BY county ASC");
while ($row = $countyResult->fetch_assoc()) {
    $counties[] = $row['county'];
}

// Fetch unique locations
$locations = [];
$locationResult = $conn->query("SELECT DISTINCT location FROM housing_units ORDER BY location ASC");
while ($row = $locationResult->fetch_assoc()) {
    $locations[] = $row['location'];
}

// Fetch unique house types
$types = [];
$typeResult = $conn->query("SELECT DISTINCT type FROM housing_units ORDER BY type ASC");
while ($row = $typeResult->fetch_assoc()) {
    $types[] = $row['type'];
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Property Market</title>
<style>
    body { font-family: Arial, sans-serif; background: #0a0a23; margin: 0; padding: 0; color: white; }
    header { background: #d4af37; color: #0a0a23; padding: 1rem; text-align: center; font-size: 1.5rem; font-weight: bold; position: relative; }

    /* Navigation arrows */
    .nav-arrow {
        position: absolute; top: 50%; transform: translateY(-50%);
        font-size: 28px; font-weight: bold; text-decoration: none;
        color: #0a0a23; background: rgba(255, 215, 0, 0.7);
        padding: 8px 12px; border-radius: 50%; transition: 0.3s;
    }
    .nav-arrow:hover { background: gold; transform: scale(1.1); color: black; }
    .left-arrow { left: 15px; } .right-arrow { right: 15px; }

    .filter-bar { background: white; padding: 1rem; display: flex; gap: 1rem; flex-wrap: wrap; justify-content: center; border-bottom: 3px solid #d4af37; }
    .filter-bar select, .filter-bar input { padding: 0.5rem; font-size: 1rem; border: 2px solid #d4af37; border-radius: 5px; background: #0a0a23; color: white; }

    .properties-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); gap: 1rem; padding: 1rem; }
    .property-card { background: white; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 5px rgba(0,0,0,0.3); color: #0a0a23; display: flex; flex-direction: column; }
    .property-card img { width: 100%; height: 220px; object-fit: cover; border-bottom: 3px solid #d4af37; }

    .property-card .details { padding: 0.8rem; }
    .property-card .details p { margin: 0.4rem 0; color: #333; }

    .email-btn { margin: 0.6rem; padding: 0.6rem; background: #d4af37; color: #0a0a23; font-weight: bold; text-align: center; border-radius: 5px; cursor: pointer; transition: 0.3s; }
    .email-btn:hover { background: #b38e2e; color: white; }
</style>
</head>
<body>

<header>
    <a href="index.html" class="nav-arrow left-arrow">&#8592;</a>
    Available Properties
    <a href="plans.html" class="nav-arrow right-arrow">&#8594;</a>
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

    <select id="typeFilter">
        <option value="">All Types</option>
        <?php foreach ($types as $type): ?>
            <option value="<?= htmlspecialchars($type) ?>"><?= htmlspecialchars($type) ?></option>
        <?php endforeach; ?>
    </select>

    <input type="number" id="minPrice" placeholder="Min Price">
    <input type="number" id="maxPrice" placeholder="Max Price">
    <button onclick="loadProperties()">Filter</button>
</div>

<section class="properties-grid" id="properties"></section>

<script>
document.addEventListener("DOMContentLoaded", () => {
    const countyFilter = document.getElementById("countyFilter");
    const locationFilter = document.getElementById("locationFilter");
    const typeFilter = document.getElementById("typeFilter");
    const minPrice = document.getElementById("minPrice");
    const maxPrice = document.getElementById("maxPrice");
    const propertiesSection = document.getElementById("properties");

    function loadProperties() {
        const county = countyFilter.value;
        const location = locationFilter.value;
        const type = typeFilter.value;
        const minP = minPrice.value;
        const maxP = maxPrice.value;

        fetch(`fetch_property.php?county=${encodeURIComponent(county)}&location=${encodeURIComponent(location)}&type=${encodeURIComponent(type)}&minPrice=${minP}&maxPrice=${maxP}`)
            .then(res => res.json())
            .then(data => {
                propertiesSection.innerHTML = "";
                if (!data || data.length === 0) {
                    propertiesSection.innerHTML = "<p style='color:white;'>No properties found.</p>";
                    return;
                }
                data.forEach(prop => {
                    const imgSrc = (prop.images && prop.images.length > 0) ? prop.images[0] : "no-image.jpg";
                    const card = `
                        <div class="property-card">
                            <img src="${imgSrc}" alt="Property Image">
                            <div class="details">
                                <p><b>${prop.county}</b> - ${prop.location}</p>
                                <p>Type: ${prop.type}</p>
                                <p>Price: KES ${prop.price}</p>
                            </div>
                            <div class="email-btn" onclick="sendInquiry('${prop.id}')">Inquire Now</div>
                        </div>
                    `;
                    propertiesSection.innerHTML += card;
                });
            })
            .catch(err => {
                console.error("Error fetching properties:", err);
                propertiesSection.innerHTML = "<p style='color:white;'>Error loading properties.</p>";
            });
    }

    window.sendInquiry = function(propertyId) {
        const senderEmail = prompt("Enter your email address:");
        if (!senderEmail) return alert("Email address is required.");
        
        fetch('send_email.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ sender_email: senderEmail, property_id: propertyId })
})
.then(res => res.json())
.then(response => {
    if (response.success) {
        window.location.href = response.mailto; // open default email app
    } else {
        alert(response.message);
    }
})
.catch(err => {
    console.error("Error preparing email:", err);
    alert("Error preparing email.");
});

        

    };

    // 🔹 Auto reload when filters change
    countyFilter.addEventListener("change", loadProperties);
    locationFilter.addEventListener("change", loadProperties);
    typeFilter.addEventListener("change", loadProperties);
    minPrice.addEventListener("input", loadProperties);
    maxPrice.addEventListener("input", loadProperties);

    // Load initial properties
    loadProperties();
});
</script>

</body>
</html>
