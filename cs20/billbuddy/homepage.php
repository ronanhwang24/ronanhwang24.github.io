<?php
session_start();
?>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>BillBuddy Homepage</title>
  <link rel="stylesheet" href="homepage.css">
</head>
<body>
  <div class="container">
   <h1>Hello <?php echo htmlspecialchars($_SESSION['username']); ?>!</h1>
    <nav>
      <ul>
        <li><a href="homepage.php">Home</a></li>
        <li><a href="view.php">View</a></li>
        <li><a href="add-manage.php">Add/Manage</a></li>
        <li><a href="contactus.php">Contact Us</a></li>
      </ul>
    </nav>
  </div>
</body>
</html>
