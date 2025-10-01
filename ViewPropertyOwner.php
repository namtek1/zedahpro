<?php
// --- DB CONNECTION ---
$host = "localhost";
$user = "root";
$password = "";
$database = "housing";

$conn = new mysqli($host, $user, $password, $database);
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

// --- HANDLE DROP REQUEST ---
if (isset($_GET['drop_id'])) {
  $drop_id = intval($_GET['drop_id']);
  $conn->query("DELETE FROM landlords WHERE id = $drop_id");
  header("Location: ViewPropertyOwner.php");
  exit();
}

// --- FETCH PROPERTY OWNERS ---
$result = $conn->query("SELECT * FROM landlords ORDER BY id ASC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>View Property Owners</title>
  <style>
    body {
      margin: 0;
      font-family: 'Segoe UI', sans-serif;
      background-color: #0b0c2a;
      color: gold;
      display: flex;
      flex-direction: column;
      align-items: center;
    }
    header img { width: 120px; margin: 20px auto; display: block; }
    .container { width: 95%; max-width: 1200px; background-color: #000; border: 1px solid gold; padding: 20px; border-radius: 8px; box-shadow: 0 0 10px gold; }
    table { width: 100%; border-collapse: collapse; margin-top: 20px; background-color: #0b0c2a; table-layout: auto; }
    th, td { border: 1px solid gold; padding: 10px; text-align: center; }
    th { background-color: #1a1a1a; }
    th.actions-col, td.actions-col { min-width: 300px; } 
    a, button { padding: 6px 12px; background-color: gold; color: #000; border: none; border-radius: 4px; cursor: pointer; text-decoration: none; margin: 2px; display: inline-block; }
    a:hover, button:hover { background-color: #ffcc00; }
    footer { margin-top: 40px; padding: 20px; text-align: center; color: gold; background-color: #000; width: 100%; }
    .nav { width: 100%; display: flex; justify-content: space-between; padding: 10px 30px; }
    .nav a { font-size: 14px; background-color: white; border: 2px solid gold; }
  </style>
</head>
<body>
  <header>
    <img src="logo.png" alt="Zedah Logo" />
  </header>

  <div class="nav">
    <a href="ownerlogin.html">⬅ Back</a>
    <a href="ownerlogin.html">➡ Next</a>
  </div>

  <div class="container">
    <h2 style="text-align:center;">Zedah Property Solutions</h2>
    <table>
      <tr>
        <th>#</th>
        <th>First Name</th>
        <th>Last Name</th>
        <th>Username</th>
        <th>Email</th>
        <th>Plan</th>
        <th>Billing</th>
        <th>Price (KES)</th>
        <th class="actions-col">Actions</th>
      </tr>
      <?php if ($result->num_rows > 0): ?>
        <?php while ($row = $result->fetch_assoc()): ?>
          <tr>
            <td><?= $row['id'] ?></td>
            <td><?= htmlspecialchars($row['first_name']) ?></td>
            <td><?= htmlspecialchars($row['second_name']) ?></td>
            <td><?= htmlspecialchars($row['username']) ?></td>
            <td><?= htmlspecialchars($row['email']) ?></td>
            <td><?= htmlspecialchars($row['plan']) ?></td>
            <td><?= htmlspecialchars($row['billing']) ?></td>
            <td><?= number_format($row['price']) ?></td>
            <td class="actions-col">
              <a href="viewproperties1.php?owner_id=<?= $row['id'] ?>">Properties</a>
              <a href="bill_owner.php?owner_id=<?= $row['id'] ?>">Bill Owner</a>
              <a href="add_tenant.php?owner_id=<?= $row['id'] ?>">Add Tenant</a>
              <a href="?drop_id=<?= $row['id'] ?>" onclick="return confirm('Are you sure you want to remove this owner?')">Drop</a>
            </td>
          </tr>
        <?php endwhile; ?>
      <?php else: ?>
        <tr><td colspan="9">No property owners found.</td></tr>
      <?php endif; ?>
    </table>
  </div>

  <footer>
    &copy; <?= date("Y") ?> RATEK ZPM. All Rights Reserved.
  </footer>
</body>
</html>
