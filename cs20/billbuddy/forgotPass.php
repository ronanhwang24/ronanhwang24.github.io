<?php
// forgot_password.php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $servername = "localhost";
    $username = "uqmfivnexadws";
    $password = "bradisdabest";
    $dbname = "db03qmxhguzp08";

    $conn = new mysqli($servername, $username, $password, $dbname);
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Get the username entered by the user
    $entered_username = trim($_POST["username"]);

    // Query to get the user's email and password (hashed)
    $stmt = $conn->prepare("SELECT email, password FROM users WHERE username = ?");
    $stmt->bind_param("s", $entered_username);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows === 1) {
        $stmt->bind_result($email, $userPassword);
        $stmt->fetch();

        // Email the user their password (here using the email retrieved)
        $subject = "Your BillBuddy Password";
        $message = "Hello $entered_username,\n\nYour password is: $userPassword\n\nPlease don't share your password with anyone.";
        $headers = "From: support@BillBuddy.com";

        if (mail($email, $subject, $message, $headers)) {
            echo "An email with your password has been sent to $email.";
        } else {
            echo "Failed to send the email. Please try again.";
        }
    } else {
        echo "Username not found.";
    }

    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Forgot Password</title>
  <link rel="stylesheet" href="style.css" />
</head>
<body>
  <div class="forgot-password-container">
    <h1>Forgot Your Password?</h1>
    <form method="POST" novalidate>
      <input type="text" name="username" placeholder="Enter your username" required />
      <button type="submit">Send Password</button>
    </form>
    <div class="footer">
      <a href="mainLogin.php">Back to Login</a>
    </div>
  </div>
</body>
</html>