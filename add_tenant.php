<?php
$host = "localhost";
$user = "root";
$password = "";
$database = "housing";
$conn = new mysqli($host, $user, $password, $database);
if ($conn->connect_error) { die("Connection failed: " . $conn->connect_error); }

if (!isset($_GET['owner_id'])) {
    die("Owner ID missing");
}

$owner_id = intval($_GET['owner_id']);

// Handle tenant deletion
if (isset($_GET['drop_tenant'])) {
    $drop_id = intval($_GET['drop_tenant']);
    $conn->query("DELETE FROM tenants WHERE id = $drop_id");
    header("Location: add_tenant.php?owner_id=$owner_id");
    exit();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $county = $_POST['county'];
    $location = $_POST['location'];
    $premises_name = $_POST['premises_name'];
    $name = $_POST['full_name'];
    $age = intval($_POST['age']);
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $alt_phone = $_POST['alt_phone'];
    $unit_type = $_POST['unit_type'];
    $unit_number = $_POST['unit_number'];
    $amount_charged = floatval($_POST['amount_charged']);

    $stmt = $conn->prepare("INSERT INTO tenants 
        (owner_id, county, location, premises_name, full_name, age, email, phone, alt_phone, unit_type, unit_number, amount_charged) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("issssisssssd", 
        $owner_id, $county, $location, $premises_name, $name, $age, $email, $phone, $alt_phone, $unit_type, $unit_number, $amount_charged);
    $stmt->execute();
    $stmt->close();
}

// Fetch tenants
$tenants = $conn->query("SELECT * FROM tenants WHERE owner_id = $owner_id ORDER BY id DESC");
?>
<!DOCTYPE html>
<html>
<head>
    <title>Add Tenant</title>
    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            color: white;
            text-align: center;
            background: linear-gradient(270deg, #000428, #000428, #ffd700);
            background-size: 600% 600%;
            animation: backgroundShift 10s ease infinite;
        }
        @keyframes backgroundShift {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }
        .logo { margin-top: 20px; }
        .nav-arrows {
            position: absolute; top: 20px; width: 100%;
            display: flex; justify-content: space-between; padding: 0 20px;
        }
        .nav-arrows a {
            color: gold; font-size: 24px; text-decoration: none;
            background: #000428; padding: 8px 14px; border-radius: 50%;
            border: 2px solid gold; transition: 0.3s;
        }
        .nav-arrows a:hover { background: gold; color: #000428; }
        form {
            background: rgba(0,0,0,0.6); display: inline-block; padding: 20px;
            border-radius: 10px; border: 2px solid gold; margin-top: 20px;
        }
        form input, form select, form button {
            margin: 8px 0; padding: 10px; width: 250px;
            border: none; border-radius: 5px;
        }
        form input { background: white; color: #000; }
        form button {
            background: gold; color: #000428; font-weight: bold; cursor: pointer;
        }
        table {
            margin: 20px auto; border-collapse: collapse; background: rgba(0,0,0,0.7);
        }
        table th, table td { border: 1px solid gold; padding: 10px 15px; }
        table th { background: gold; color: #000428; }
        table tr:hover { background: blue; color: #000428; cursor: pointer; }
        table a { color: gold; text-decoration: none; font-weight: bold; }
        table a:hover { text-decoration: underline; }
    </style>
</head>
<body>
    <div class="nav-arrows">
        <a href="index.html">&#8592; Back</a>
        <a href="index.html">&#8594; Home</a>
    </div>

    <div class="logo">
        <img src="logo.png" alt="Logo" width="100">
    </div>

    <h2> Proceed to Add Tenants For This Owner!<br><br> 
        <b>ONLY THOSE BELONGING TO HIS OR HER HOUSING UNITS!!!</b>
    </h2>

    <form method="post">
        <input type="text" name="county" placeholder="County" required><br>
        <input type="text" name="location" placeholder="Specific Location" required><br>
        <input type="text" name="premises_name" placeholder="Premises Name" required><br>
        <input type="text" name="full_name" placeholder="Full Name" required><br>
        <input type="number" name="age" placeholder="Age" required><br>
        <input type="email" name="email" placeholder="Email Address" required><br>
        <input type="text" name="phone" placeholder="Phone" required><br>
        <input type="text" name="alt_phone" placeholder="Alternative Phone" required><br>
        <input type="text" name="unit_type" placeholder="Housing Unit Type" required><br>
        <input type="text" name="unit_number" placeholder="Unit Number" required><br>
        <input type="number" step="0.01" name="amount_charged" placeholder="Amount Charged" required><br>
        <button type="submit">ADD Tenant</button>
    </form>

    <h3>Tenant Listing For This Property Owner</h3>
    <table>
        <tr>
            <th>County</th>
            <th>Location</th>
            <th>Premises Name</th>
            <th>Full Name</th>
            <th>Age</th>
            <th>Email</th>
            <th>Phone</th>
            <th>Alt Phone</th>
            <th>Unit Type</th>
            <th>Unit Number</th>
            <th>Amount Charged</th>
            <th>Actions</th>
        </tr>
        <?php while ($t = $tenants->fetch_assoc()): ?>
        <tr>
            <td><?= $t['county'] ?></td>
            <td><?= $t['location'] ?></td>
            <td><?= $t['premises_name'] ?></td>
            <td><?= $t['full_name'] ?></td>
            <td><?= $t['age'] ?></td>
            <td><?= $t['email'] ?></td>
            <td><?= $t['phone'] ?></td>
            <td><?= $t['alt_phone'] ?></td>
            <td><?= $t['unit_type'] ?></td>
            <td><?= $t['unit_number'] ?></td>
            <td><?= number_format($t['amount_charged'], 2) ?></td>
            <td>
                <a href="add_tenant.php?owner_id=<?= $owner_id ?>&drop_tenant=<?= $t['id'] ?>" onclick="return confirm('Remove tenant?')">Drop</a> | 
                <a href="bill_tenant.php?tenant_id=<?= $t['id'] ?>">Bill</a>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>
</body>
</html>
