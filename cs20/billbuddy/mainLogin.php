<?php
session_start();

$servername = "localhost";
$username = "uqmfivnexadws";
$password = "bradisdabest";
$dbname = "db03qmxhguzp08";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $entered_username = trim($_POST["username"]);
    $entered_password = $_POST["password"];

    $stmt = $conn->prepare("SELECT password FROM users WHERE username = ?");
    $stmt->bind_param("s", $entered_username);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows === 1) {
        $stmt->bind_result($userpassword);
        $stmt->fetch();

        if ($entered_password == $userpassword) {
            // Pass is correct, login the user
            $_SESSION["username"] = $entered_username;
            header("Location: dashboard.php"); // Redirect after login
            exit(); // Ensure no further code is executed
        } else {
            echo "<p>Incorrect password.</p>";
        }
    } else {
        echo "<p>User not found.</p>";
    }

    $stmt->close();
}

$conn->close();
?>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Login Page</title>
  <link rel="stylesheet" href="style.css" />
</head>
<body>
<div class="container login-container">
    <h1>BillBuddy</h1>
    <form method="POST" action="mainLogin.php" id="loginForm" novalidate>
      <input type="text" name="username" id="username" placeholder="Username" required />
      <input type="password" name="password" id="password" placeholder="Password" required />
      <button type="submit">Login</button>
    </form>
    <div class="footer">
      Don't have an account? <a href="makeAcc.php">Click here to subscribe.</a><br>
      <a href="forgotPass.php">Forgot your password?</a>
    </div>
  </div>

  <!-- JavaScript for form validation -->
  <script>
    document.addEventListener("DOMContentLoaded", () => {
      const form = document.getElementById("loginForm");

      form.addEventListener("submit", function (e) {
        const username = document.getElementById("username").value.trim();
        const password = document.getElementById("password").value;

        let errors = [];

        if (username === "") {
          errors.push("Username is required.");
        }

        if (password === "") {
          errors.push("Password is required.");
        }

        if (errors.length > 0) {
          e.preventDefault();
          alert(errors.join("\n"));
        }
      });
    });
  </script>
</body>
</html>