<!DOCTYPE html>
<html>
<head>
    <title>All Tenants</title>
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
            position: absolute;
            top: 20px;
            width: 100%;
            display: flex;
            justify-content: space-between;
            padding: 0 20px;
        }
        .nav-arrows a {
            color: gold;
            font-size: 24px;
            text-decoration: none;
            background: #000428;
            padding: 8px 14px;
            border-radius: 50%;
            border: 2px solid gold;
            transition: 0.3s;
        }
        .nav-arrows a:hover { background: gold; color: #000428; }
        h2 { margin-top: 80px; }
        table {
            margin: 20px auto;
            border-collapse: collapse;
            background: rgba(0,0,0,0.7);
            min-width: 95%;
        }
        table th, table td {
            border: 1px solid gold;
            padding: 10px 15px;
        }
        table th {
            background: gold;
            color: #000428;
        }
        table tr:hover {
            background: blue;
            color: #fff;
            cursor: pointer;
        }
        table a {
            color: gold;
            text-decoration: none;
            font-weight: bold;
        }
        table a:hover { text-decoration: underline; }
        .filter-form {
            margin: 20px auto;
            text-align: center;
            background: rgba(0,0,0,0.6);
            padding: 15px;
            border-radius: 8px;
            display: inline-block;
            border: 2px solid gold;
        }
        .filter-form select, .filter-form button {
            margin: 8px;
            padding: 8px 12px;
            border-radius: 5px;
            border: none;
        }
        .filter-form button {
            background: gold;
            color: #000428;
            font-weight: bold;
            cursor: pointer;
        }
    </style>
</head>
<body>
<?php
$host = "localhost";
$user = "root";
$password = "";
$database = "housing";
$conn = new mysqli($host, $user, $password, $database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// ===============================
// Handle tenant deletion
// ===============================
if (isset($_GET['drop_tenant'])) {
    $drop_id = intval($_GET['drop_tenant']);
    $conn->query("DELETE FROM tenants WHERE id = $drop_id");
    header("Location: viewall_tenant.php");
    exit();
}

// ===============================
// Fetch unique filters
// ===============================
$counties = $conn->query("SELECT DISTINCT county FROM tenants ORDER BY county ASC");

$locations = [];
if (!empty($_GET['county'])) {
    $county = $conn->real_escape_string($_GET['county']);
    $locations = $conn->query("SELECT DISTINCT location FROM tenants WHERE county = '$county' ORDER BY location ASC");
}

$unit_types = $conn->query("SELECT DISTINCT unit_type FROM tenants ORDER BY unit_type ASC");

// ===============================
// Apply filter if set
// ===============================
$where = [];
if (!empty($_GET['county'])) {
    $county = $conn->real_escape_string($_GET['county']);
    $where[] = "county = '$county'";
}
if (!empty($_GET['location'])) {
    $location = $conn->real_escape_string($_GET['location']);
    $where[] = "location = '$location'";
}
if (!empty($_GET['unit_type'])) {
    $unit_type = $conn->real_escape_string($_GET['unit_type']);
    $where[] = "unit_type = '$unit_type'";
}

$whereSQL = count($where) ? "WHERE " . implode(" AND ", $where) : "";

// ===============================
// Fetch tenants
// ===============================
$tenants = $conn->query("SELECT * FROM tenants $whereSQL ORDER BY id DESC");
?>

    <div class="nav-arrows">
        <a href="ownerlogin.html">&#8592; Back</a>
        <a href="index.html">&#8594; Home</a>
    </div>

    <div class="logo">
        <img src="logo.png" alt="Logo" width="100">
    </div>

    <h2>All Registered Tenants</h2>

    <!-- Filter Form -->
    <form method="get" class="filter-form">
        <select name="county" onchange="this.form.submit()">
            <option value="">-- Select County --</option>
            <?php while ($c = $counties->fetch_assoc()): ?>
                <option value="<?= $c['county'] ?>" <?= (isset($_GET['county']) && $_GET['county']==$c['county'])?'selected':'' ?>>
                    <?= $c['county'] ?>
                </option>
            <?php endwhile; ?>
        </select>
        <select name="location">
            <option value="">-- Select Location --</option>
            <?php if (!empty($locations) && $locations instanceof mysqli_result): ?>
                <?php while ($l = $locations->fetch_assoc()): ?>
                    <option value="<?= $l['location'] ?>" <?= (isset($_GET['location']) && $_GET['location']==$l['location'])?'selected':'' ?>>
                        <?= $l['location'] ?>
                    </option>
                <?php endwhile; ?>
            <?php endif; ?>
        </select>
        <select name="unit_type">
            <option value="">-- Select Premise (Unit Type) --</option>
            <?php while ($u = $unit_types->fetch_assoc()): ?>
                <option value="<?= $u['unit_type'] ?>" <?= (isset($_GET['unit_type']) && $_GET['unit_type']==$u['unit_type'])?'selected':'' ?>>
                    <?= $u['unit_type'] ?>
                </option>
            <?php endwhile; ?>
        </select>
        <button type="submit">Filter</button>
        <a href="viewall_tenant.php" style="margin-left:10px;color:gold;">Reset</a>
    </form>
  
    <!-- Button to send email -->
<form method="get" action="email_all_tenants.php" class="filter-form">
    <select name="premises_name" required>
        <option value="">-- Select Premise --</option>
        <?php
        $premises = $conn->query("SELECT DISTINCT premises_name FROM tenants ORDER BY premises_name ASC");
        while ($p = $premises->fetch_assoc()):
        ?>
            <option value="<?= $p['premises_name'] ?>"><?= $p['premises_name'] ?></option>
        <?php endwhile; ?>
    </select>
    <button type="submit">Send Email to All Tenants</button>
</form>

    
    <!-- Tenant Table -->
    <table>
        <tr>
            <th>ID</th>
            <th>Owner ID</th>
            <th>County</th>
            <th>Location</th>
            <th>Premises Name</th>
            <th>Unit Number</th>
            <th>Full Name</th>
            <th>Age</th>
            <th>Email</th>
            <th>Phone</th>
            <th>Alt Phone</th>
            <th>Unit Type</th>
            <th>Amount Charged</th>
            <th>Actions</th>
        </tr>
        
        <?php if ($tenants->num_rows > 0): ?>
            <?php while ($t = $tenants->fetch_assoc()): ?>
            <tr>
                <td><?= $t['id'] ?></td>
                <td><?= $t['owner_id'] ?></td>
                <td><?= $t['county'] ?></td>
                <td><?= $t['location'] ?></td>
                <td><?= $t['premises_name'] ?></td>
                <td><?= $t['unit_number'] ?></td>
                <td><?= $t['full_name'] ?></td>
                <td><?= $t['age'] ?></td>
                <td><?= $t['email'] ?></td>
                <td><?= $t['phone'] ?></td>
                <td><?= $t['alt_phone'] ?></td>
                <td><?= $t['unit_type'] ?></td>
                <td><?= number_format($t['amount_charged'], 2) ?></td>
                <td>
                    <a href="viewall_tenant.php?drop_tenant=<?= $t['id'] ?>" onclick="return confirm('Remove tenant?')">Drop</a> | 
                    <a href="bill_tenant.php?tenant_id=<?= $t['id'] ?>">Bill</a>
                </td>
            </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr><td colspan="14">No tenants found.</td></tr>
        <?php endif; ?>
    </table>
</body>
</html>
