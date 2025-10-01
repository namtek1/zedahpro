<?php
$host = "localhost";
$user = "root";
$password = "";
$database = "housing";
$conn = new mysqli($host, $user, $password, $database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $type = $_POST['type'];
    $county = $_POST['county'];
    $location = $_POST['location'];
    $price = $_POST['price'];

    $uploaded_images = [];
    if (!empty($_FILES['images']['name'][0])) {
        foreach ($_FILES['images']['name'] as $key => $name) {
            $targetDir = "uploads/";
            if (!is_dir($targetDir)) {
                mkdir($targetDir, 0777, true);
            }
            $fileName = time() . "_" . basename($name);
            $targetFilePath = $targetDir . $fileName;
            if (move_uploaded_file($_FILES["images"]["tmp_name"][$key], $targetFilePath)) {
                $uploaded_images[] = $fileName;
            }
        }
    }

    $images_str = implode(",", $uploaded_images);
    $stmt = $conn->prepare("INSERT INTO featured_units (type, county, location, price, images) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssds", $type, $county, $location, $price, $images_str);
    $stmt->execute();
    $stmt->close();

    echo "<script>alert('Featured unit uploaded successfully!'); window.location='upload_featured.php';</script>";
}
$conn->close();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Featured Unit - Sale | Rental</title>
    <style>
        body, html {
            margin: 0;
            padding: 0;
            height: 100%;
            overflow: hidden;
            font-family: sans-serif;
            color: gold;
        }
        canvas {
            position: fixed;
            top: 0; left: 0;
            width: 100%;
            height: 100%;
            z-index: -1;
            background: black;
        }
        header {
            position: absolute;
            top: 15px;
            width: 100%;
            text-align: center;
            color: gold;
            font-size: 24px;
            font-weight: bold;
        }
        .back-arrow {
            position: absolute;
            top: 15px;
            left: 20px;
            font-size: 28px;
            text-decoration: none;
            color: gold;
        }
        footer {
            position: absolute;
            bottom: 10px;
            width: 100%;
            text-align: center;
            color: white;
            font-size: 14px;
        }
        form {
            background: rgba(255,255,255,0.1);
            padding: 25px;
            border-radius: 15px;
            text-align: center;
            position: relative;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 320px;
        }
        input, select {
            margin: 8px;
            padding: 10px;
            border-radius: 8px;
            border: none;
            width: 90%;
            text-align: center;
        }
        button {
            background: gold;
            border: none;
            padding: 10px 25px;
            border-radius: 10px;
            cursor: pointer;
            font-weight: bold;
        }
        button:hover { background: white; color: black; }
    </style>
</head>
<body>
    <!-- Particle background -->
    <canvas id="particles"></canvas>

    <!-- Header with logo -->
    <header>
        <span><img src="logo.png" alt="mylogo" width="100px" height="100px"></span>
    </header>

    <!-- Back arrow -->
    <a href="ownerlogin.html" class="back-arrow">&#8592;</a>

    <!-- Upload form -->
    <form method="post" enctype="multipart/form-data">
        <h2>Upload Featured Unit</h2>
        <input type="text" name="type" placeholder="Housing Type ..Villa, Bungalow etc" required><br>
        <input type="text" name="county" placeholder="County" required><br>
        <input type="text" name="location" placeholder=" Specific Location" required><br>
        <input type="number" name="price" placeholder="Price Per Month (Ksh)" required><br>
        <input type="file" name="images[]" multiple required><br>
        <button type="submit">Upload</button>
    </form>

    <!-- Footer -->
    <footer>
        &copy; 2025 RATEK ZPS. All Rights Reserved.
    </footer>

    <!-- Particle animation script -->
    <script>
        const canvas = document.getElementById("particles");
        const ctx = canvas.getContext("2d");
        canvas.width = window.innerWidth;
        canvas.height = window.innerHeight;

        let particlesArray = [];
        const numParticles = 80;

        class Particle {
            constructor() {
                this.x = Math.random() * canvas.width;
                this.y = Math.random() * canvas.height;
                this.size = Math.random() * 3 + 1;
                this.speedX = Math.random() * 1 - 0.5;
                this.speedY = Math.random() * 1 - 0.5;
                this.color = ["gold", "white", "darkblue"][Math.floor(Math.random() * 3)];
            }
            update() {
                this.x += this.speedX;
                this.y += this.speedY;

                if (this.x < 0 || this.x > canvas.width) this.speedX *= -1;
                if (this.y < 0 || this.y > canvas.height) this.speedY *= -1;
            }
            draw() {
                ctx.fillStyle = this.color;
                ctx.beginPath();
                ctx.arc(this.x, this.y, this.size, 0, Math.PI * 2);
                ctx.fill();
            }
        }

        function init() {
            particlesArray = [];
            for (let i = 0; i < numParticles; i++) {
                particlesArray.push(new Particle());
            }
        }

        function animate() {
            ctx.clearRect(0, 0, canvas.width, canvas.height);
            particlesArray.forEach(p => {
                p.update();
                p.draw();
            });
            requestAnimationFrame(animate);
        }

        window.addEventListener("resize", () => {
            canvas.width = window.innerWidth;
            canvas.height = window.innerHeight;
            init();
        });

        init();
        animate();
    </script>
</body>
</html>
