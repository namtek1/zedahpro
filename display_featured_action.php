<?php
$host = "localhost";
$user = "root";
$password = "";
$database = "housing";
$conn = new mysqli($host, $user, $password, $database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle delete
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $conn->query("DELETE FROM featured_units WHERE id=$id");
    header("Location: ".$_SERVER['PHP_SELF']);
    exit;
}

// Build filters
$whereClauses = [];
if ($_SERVER["REQUEST_METHOD"] == "GET") {
    if (!empty($_GET['type'])) {
        $type = $conn->real_escape_string($_GET['type']);
        $whereClauses[] = "type LIKE '%$type%'";
    }
    if (!empty($_GET['county'])) {
        $county = $conn->real_escape_string($_GET['county']);
        $whereClauses[] = "county LIKE '%$county%'";
    }
    if (!empty($_GET['location'])) {
        $location = $conn->real_escape_string($_GET['location']);
        $whereClauses[] = "location LIKE '%$location%'";
    }
    if (!empty($_GET['price'])) {
        $price = $conn->real_escape_string($_GET['price']);
        $whereClauses[] = "price <= '$price'";
    }
}

$where = "";
if (count($whereClauses) > 0) {
    $where = "WHERE " . implode(" AND ", $whereClauses);
}

$result = $conn->query("SELECT * FROM featured_units $where ORDER BY id DESC LIMIT 30"); 
?>
<!DOCTYPE html>
<html>
<head>
    <title>Featured Units</title>
    <style>
        body {
            margin: 0;
            font-family: sans-serif;
            color: white;
            text-align: center;
            position: relative;
            overflow-x: hidden;
        }

        #particles {
            position: fixed;
            top: 0; left: 0;
            width: 100%; height: 100%;
            background: black;
            z-index: -1;
        }

        .logo { 
            margin: 15px; 
            font-size: 28px; 
            font-weight: bold; 
            color: gold; 
        }

        .back { 
            position: absolute; 
            top: 20px; 
            left: 20px; 
            font-size: 24px; 
            color: gold; 
            text-decoration: none; 
        }

        .filter {
            margin: 20px auto;
        }
        .filter input {
            padding: 10px;
            border-radius: 8px;
            border: none;
            width: 200px;
            margin: 5px;
        }
        .filter button {
            padding: 10px 20px;
            border: none;
            background: gold;
            color: black;
            font-weight: bold;
            border-radius: 8px;
            cursor: pointer;
        }

        table {
            border-collapse: collapse;
            margin: auto;
            width: 95%;
            background: rgba(255,255,255,0.05);
        }
        th, td {
            border: 1px solid gold;
            padding: 10px;
            text-align: center;
        }
        th {
            background: darkblue;
            color: gold;
        }
        td {
            color: white;
        }

        .slideshow { 
            width: 200px; 
            height: 120px; 
            display: flex; 
            justify-content: center; 
            align-items: center; 
            position: relative; 
            overflow: hidden; 
            background: black;
        }

        .slideshow img {
            max-width: 100%;
            max-height: 100%;
            position: absolute; 
            opacity: 0; 
            transition: opacity 1s ease-in-out;
        }
        .slideshow img.active { opacity: 1; }

        .delete-btn {
            display: inline-block;
            background: red;
            color: white;
            padding: 8px 15px;
            margin: 5px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: bold;
        }
        .delete-btn:hover { background: darkred; }

        footer {
            margin-top: 40px;
            padding: 15px;
            color: gold;
        }
    </style>
</head>
<body>
    <canvas id="particles"></canvas>
    <a href="ownerlogin.html" class="back">&#8592; Back</a>
    <div class="logo">Featured Sales | Rentals</div>

    <!-- Filters -->
    <form class="filter" method="get">
        <input type="text" name="type" placeholder="Filter by type" value="<?php echo isset($_GET['type']) ? htmlspecialchars($_GET['type']) : ''; ?>">
        <input type="text" name="county" placeholder="Filter by county" value="<?php echo isset($_GET['county']) ? htmlspecialchars($_GET['county']) : ''; ?>">
        <input type="text" name="location" placeholder="Filter by location" value="<?php echo isset($_GET['location']) ? htmlspecialchars($_GET['location']) : ''; ?>">
        <input type="number" name="price" placeholder="Max price (Ksh)" value="<?php echo isset($_GET['price']) ? htmlspecialchars($_GET['price']) : ''; ?>">
        <button type="submit">Search</button>
    </form>

    <!-- Tabular Display -->
    <table>
        <tr>
            <th>Images</th>
            <th>Type</th>
            <th>County</th>
            <th>Location</th>
            <th>Price</th>
            <th>Action</th>
        </tr>
        <?php while($row = $result->fetch_assoc()): ?>
        <tr>
            <td>
                <div class="slideshow" id="slideshow-<?php echo $row['id']; ?>">
                    <?php 
                    $images = explode(",", $row['images']);
                    foreach($images as $i => $img): ?>
                        <img src="uploads/<?php echo $img; ?>" class="<?php echo $i==0 ? 'active' : ''; ?>">
                    <?php endforeach; ?>
                </div>
            </td>
            <td><?php echo $row['type']; ?></td>
            <td><?php echo $row['county']; ?></td>
            <td><?php echo $row['location']; ?></td>
            <td>Ksh <?php echo number_format($row['price']); ?></td>
            <td>
                <a class="delete-btn" href="?delete=<?php echo $row['id']; ?>" onclick="return confirm('Are you sure you want to delete this property?');">Delete</a>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>

    <footer>&copy; 2025 RATEK ZPS. All Rights Reserved.</footer>

    <script>
        // Slideshows
        document.querySelectorAll(".slideshow").forEach(slideshow => {
            let images = slideshow.querySelectorAll("img");
            let index = 0;
            setInterval(() => {
                if(images.length > 1){
                    images[index].classList.remove("active");
                    index = (index + 1) % images.length;
                    images[index].classList.add("active");
                }
            }, 2000);
        });

        // Particle background animation
        const canvas = document.getElementById('particles');
        const ctx = canvas.getContext('2d');
        let particles = [];

        function resize() {
            canvas.width = window.innerWidth;
            canvas.height = window.innerHeight;
        }
        window.addEventListener("resize", resize);
        resize();

        function createParticles() {
            particles = [];
            for (let i = 0; i < 80; i++) {
                particles.push({
                    x: Math.random() * canvas.width,
                    y: Math.random() * canvas.height,
                    dx: (Math.random() - 0.5) * 1,
                    dy: (Math.random() - 0.5) * 1,
                    radius: Math.random() * 2 + 1
                });
            }
        }

        function drawParticles() {
            ctx.clearRect(0, 0, canvas.width, canvas.height);
            ctx.fillStyle = "gold";
            particles.forEach(p => {
                ctx.beginPath();
                ctx.arc(p.x, p.y, p.radius, 0, Math.PI * 2);
                ctx.fill();
                p.x += p.dx;
                p.y += p.dy;
                if (p.x < 0 || p.x > canvas.width) p.dx *= -1;
                if (p.y < 0 || p.y > canvas.height) p.dy *= -1;
            });
            requestAnimationFrame(drawParticles);
        }

        createParticles();
        drawParticles();
    </script>
</body>
</html>
<?php $conn->close(); ?>
