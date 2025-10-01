<?php
require 'db.php';

if (!isset($_GET['property_id']) || !isset($_GET['owner_id'])) {
    die("Invalid request.");
}
$property_id = intval($_GET['property_id']);
$owner_id = intval($_GET['owner_id']);

// Fetch premises_name (based on property_id)
$stmt = $conn->prepare("SELECT premises_name FROM housing_units WHERE id = ? AND owner_id = ?");
$stmt->bind_param("ii", $property_id, $owner_id);
$stmt->execute();
$property = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$property) {
    die("Property not found.");
}
$premises_name = $property['premises_name'];

// Fetch all unit types and counts for this premises
$stmt = $conn->prepare("SELECT type, units, price FROM housing_units 
                        WHERE owner_id = ? AND premises_name = ?");
$stmt->bind_param("is", $owner_id, $premises_name);
$stmt->execute();
$unitTypes = $stmt->get_result();
$stmt->close();

$expected = 0;
$unitDetails = [];
$totalUnits = 0;
while ($row = $unitTypes->fetch_assoc()) {
    $unitDetails[] = $row;
    $expected += $row['units'] * $row['price'];
    $totalUnits += $row['units'];
}

// Fetch tenants (each tenant has unit_type and amount_charged)
$stmt = $conn->prepare("SELECT id, full_name, unit_number, unit_type, amount_charged 
                        FROM tenants 
                        WHERE owner_id = ? AND premises_name = ?");
$stmt->bind_param("is", $owner_id, $premises_name);
$stmt->execute();
$tenants = $stmt->get_result();
$stmt->close();

$occupied = $tenants->num_rows;
$vacant = $totalUnits - $occupied;

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Bill & Occupancy</title>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.28/jspdf.plugin.autotable.min.js"></script>
<style>
    body { font-family: Arial, sans-serif; background:#f4f4f4; text-align:center; }
    header img { max-height:80px; margin:20px auto; display:block; }
    table { margin:20px auto; border-collapse: collapse; width: 95%; font-size: 12px; }
    th, td { border:1px solid #ccc; padding:6px; text-align:center; }
    th { background:#d4af37; color:#000; }
    h2 { margin-top:10px; }
    .chart-container { width:300px; height: 300px; margin:30px auto; }
    .nav-arrow { position: absolute; top: 20px; left: 20px; font-size: 1.2rem; color: #0a0a23; background: #d4af37;
                 padding: 6px 12px; border-radius: 5px; text-decoration: none; font-weight: bold; }
    .nav-arrow:hover { background: #b7950b; color: white; }
    header { position: relative; }
    tfoot td { font-weight: bold; background:#f0f0f0; }
    .btn { margin:20px; padding:10px 20px; background:#2ecc71; color:white; border:none; border-radius:5px; cursor:pointer; font-weight:bold; }
    .btn:hover { background:#27ae60; }
</style>
</head>
<body>
<header>
    <a href="ownerlogin.html" class="nav-arrow">⬅ Go Back</a>
    <img src="logo.png" alt="Logo">
</header>

<h2><?= htmlspecialchars($premises_name) ?></h2>

<!-- Summary of Units by Type -->
<table>
    <tr>
        <th>Type</th>
        <th>Units</th>
        <th>Price per Unit (KES)</th>
        <th>Expected Monthly Collection (KES)</th>
    </tr>
    <?php foreach ($unitDetails as $ud): ?>
    <tr>
        <td><?= htmlspecialchars($ud['type']) ?></td>
        <td><?= $ud['units'] ?></td>
        <td><?= number_format($ud['price']) ?></td>
        <td><?= number_format($ud['units'] * $ud['price']) ?></td>
    </tr>
    <?php endforeach; ?>
    <tr>
        <td colspan="3"><b>Total Expected</b></td>
        <td><b><?= number_format($expected) ?></b></td>
    </tr>
</table>

<!-- Occupancy Chart -->
<div class="chart-container">
    <h3>Occupancy rate for <?= htmlspecialchars($premises_name) ?></h3>
    <canvas id="occupancyChart"></canvas>
</div>
<br><br>
<!-- Billing Table -->
<h3>Billing Table for <?= htmlspecialchars($premises_name) ?></h3>
<table id="billingTable">
    <thead>
        <tr>
            <th>Occupant Name</th>
            <th>Unit No</th>
            <th>Type</th>
            <th>Amount Charged</th>
            <?php 
            $months = ["Jan","Feb","Mar","Apr","May","Jun","Jul","Aug","Sep","Oct","Nov","Dec"];
            foreach($months as $m) {
                echo "<th colspan='2'>$m</th>";
            }
            ?>
        </tr>
        <tr>
            <th></th><th></th><th></th><th></th>
            <?php foreach($months as $m) { echo "<th>Paid</th><th>Remaining</th>"; } ?>
        </tr>
    </thead>
    <tbody>
        <?php while($t = $tenants->fetch_assoc()): ?>
        <tr>
            <td><?= htmlspecialchars($t['full_name']) ?></td>
            <td><?= htmlspecialchars($t['unit_number']) ?></td>
            <td><?= htmlspecialchars($t['unit_type']) ?></td>
            <td class="charge"><?= $t['amount_charged'] ?></td>
            <?php foreach($months as $m): ?>
                <td contenteditable="true" class="paid">0</td>
                <td class="remain"><?= $t['amount_charged'] ?></td>
            <?php endforeach; ?>
        </tr>
        <?php endwhile; ?>
    </tbody>
    <tfoot>
        <tr>
            <td colspan="4">TOTAL</td>
            <?php foreach($months as $m): ?>
                <td class="paid-total">0</td>
                <td class="remain-total">0</td>
            <?php endforeach; ?>
        </tr>
    </tfoot>
</table>

<!-- Month Selector + Generate Button -->
<label for="reportMonth"><b>Select Month for Report:</b></label>
<select id="reportMonth">
    <?php foreach($months as $i=>$m): ?>
        <option value="<?= $i ?>"><?= $m ?></option>
    <?php endforeach; ?>
</select>
<button class="btn" onclick="generateReport()">Generate Report</button>

<script>
// Occupancy Chart
const ctx = document.getElementById('occupancyChart').getContext("2d");
new Chart(ctx, {
    type: 'pie',
    data: {
        labels: ['Occupied', 'Vacant'],
        datasets: [{
            data: [<?= $occupied ?>, <?= $vacant ?>],
            backgroundColor: ['#2ecc71', '#e74c3c']
        }]
    },
    options: { responsive: false, maintainAspectRatio: false }
});

// Update Remaining + Totals
function updateRemainingAndTotals() {
    const table = document.getElementById("billingTable");
    const rows = table.tBodies[0].rows;
    const footer = table.tFoot.rows[0].cells;
    const months = 12;

    for (let r of rows) {
        let charge = parseFloat(r.querySelector(".charge").innerText) || 0;
        for (let m = 0; m < months; m++) {
            let paidCell = r.cells[4 + m*2];
            let remainCell = r.cells[5 + m*2];
            let paid = parseFloat(paidCell.innerText) || 0;
            remainCell.innerText = (charge - paid >= 0) ? (charge - paid) : 0;
        }
    }

    for (let m = 0; m < months; m++) {
        let paidSum = 0, remainSum = 0;
        for (let r of rows) {
            let paid = parseFloat(r.cells[4 + m*2].innerText) || 0;
            let remain = parseFloat(r.cells[5 + m*2].innerText) || 0;
            paidSum += paid;
            remainSum += remain;
        }
        footer[4 + m*2].innerText = paidSum;
        footer[5 + m*2].innerText = remainSum;
    }
}
document.getElementById("billingTable").addEventListener("input", updateRemainingAndTotals);
updateRemainingAndTotals();

// Generate PDF Report for selected month
function generateReport() {
    const { jsPDF } = window.jspdf;
    const doc = new jsPDF('l','pt','a4');
    const monthIndex = parseInt(document.getElementById("reportMonth").value);
    const monthName = ["Jan","Feb","Mar","Apr","May","Jun","Jul","Aug","Sep","Oct","Nov","Dec"][monthIndex];

    // Logo
    const logoImg = document.querySelector("header img");
    if (logoImg) {
        const canvas = document.createElement("canvas");
        const ctx = canvas.getContext("2d");
        canvas.width = logoImg.naturalWidth;
        canvas.height = logoImg.naturalHeight;
        ctx.drawImage(logoImg, 0, 0);
        const dataURL = canvas.toDataURL("image/png");
        doc.addImage(dataURL, "PNG", 250, 20, 150, 80);
    }

    // Title
    doc.setFontSize(14);
    doc.text("<?= $premises_name ?> - Billing Report (" + monthName + ")", 400, 120, { align: "center" });

    // Build filtered table
    const table = document.getElementById("billingTable");
    const rows = [];
    for (let r of table.tBodies[0].rows) {
        let row = [
            r.cells[0].innerText, // name
            r.cells[1].innerText, // unit
            r.cells[2].innerText, // type
            r.cells[3].innerText, // charge
            r.cells[4 + monthIndex*2].innerText, // paid
            r.cells[5 + monthIndex*2].innerText  // remain
        ];
        rows.push(row);
    }
    // Footer totals
    const footer = table.tFoot.rows[0].cells;
    rows.push([
        "TOTAL", "", "", "",
        footer[4 + monthIndex*2].innerText,
        footer[5 + monthIndex*2].innerText
    ]);

    doc.autoTable({
        head: [["Occupant Name","Unit No","Type","Amount Charged","Paid","Remaining"]],
        body: rows,
        startY: 150
    });

    doc.setFontSize(12);
    doc.text("Compiled By Zedah Property Solutions for Owner ID: <?= $owner_id?>", 400, doc.lastAutoTable.finalY + 30, { align: "center" });

    doc.save("Billing_Report_"+monthName+".pdf");
}
</script>
</body>
</html>
