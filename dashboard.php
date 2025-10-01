<!Doctype HTML>
<html>
<head>
  <title>Landlord Dashboard</title>
  <style>
    body {
      background: #000428;
      color: gold;
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      height: 100vh;
      font-family: sans-serif;
      margin: 0;
      overflow: hidden;
      position: relative;
    }

    .animated-bg {
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      z-index: -1;
      overflow: hidden;
    }

    .circle {
      position: absolute;
      border-radius: 50%;
      opacity: 0.6;
      animation: float 15s infinite linear;
    }

    .circle.gold { background: gold; width: 100px; height: 100px; }
    .circle.white { background: white; width: 80px; height: 80px; }
    .circle.blue { background: dodgerblue; width: 120px; height: 120px; }

    @keyframes float {
      0% {
        transform: translateY(100vh) translateX(0) scale(1);
        opacity: 0.6;
      }
      50% { opacity: 1; }
      100% {
        transform: translateY(-20vh) translateX(40vw) scale(1.2);
        opacity: 0;
      }
    }

    .flex {
      display: flex;
      flex-direction: column;
      gap: 20px;
    }

    a {
      padding: 12px 20px;
      background: gold;
      color: black;
      text-decoration: none;
      border-radius: 10px;
      text-align: center;
      font-weight: bold;
    }

    .nav-arrows {
      position: absolute;
      top: 20px;
      width: 100%;
      display: flex;
      justify-content: space-between;
      padding: 0 20px;
    }

    .nav-arrows a {
      background: transparent;
      color: gold;
      font-size: 2rem;
      text-decoration: none;
      font-weight: bold;
    }

    .nav-arrows a:hover { color: white; }
  </style>
</head>
<body>
<?php
  session_start();
  
  if (!isset($_SESSION['owner_id'])) {
      die("Unauthorized access. Please login first.");
  }
  $owner_id = intval($_SESSION['owner_id']);
?>
  
  <div class="animated-bg">
    <div class="circle gold" style="left: 10%; animation-delay: 0s;"></div>
    <div class="circle white" style="left: 40%; animation-delay: 5s;"></div>
    <div class="circle blue" style="left: 70%; animation-delay: 10s;"></div>
    <div class="circle gold" style="left: 85%; animation-delay: 7s;"></div>
    <div class="circle white" style="left: 20%; animation-delay: 3s;"></div>
  </div>

  <div class="nav-arrows">
    <a href="index.html">&#8592;</a>
    <a href="login.html">&#8594;</a>
  </div>

  <h1>Welcome! <br> Proceed to:</h1>
  <div class="flex">
    <a href="upload.html">Upload Housing Properties</a>
    <a href="view_properties.php?owner_id=<?php echo $owner_id; ?>">View Housing Properties</a>
    <a href="add_tenant.php?owner_id=<?php echo $owner_id; ?>">Add Tenants</a>
    <a href="mailto:zedahrealty@gmail.com">Email Property Manager</a>
    <a href="login.html" style="background:#f00;color:#fff;">Logout</a>
  </div>
</body>
</html>
