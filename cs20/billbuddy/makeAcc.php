<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Sign-up Page</title>
  <link rel="stylesheet" href="style.css" />
  <script defer src="validate.js"></script>
</head>
<body>
<?php
$servername = "localhost";
$username = "uqmfivnexadws";
$password = "bradisdabest";
$dbname = "db03qmxhguzp08";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// PHP form validation and insertion
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST["email"]);
    $username = trim($_POST["username"]);
    $userpassword = $_POST["password"];
    $repassword = $_POST["repassword"];

    $errors = [];

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format.";
    }
    if (strlen($username) < 3) {
        $errors[] = "Username must be at least 3 characters.";
    }
    if ($userpassword !== $repassword) {
        $errors[] = "Passwords do not match.";
    }
    if (strlen($password) < 6) {
        $errors[] = "Password must be at least 6 characters.";
    }

    if (empty($errors)) {
        $stmt = $conn->prepare("INSERT INTO users (email, username, password) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $email, $username, $userpassword);
        if ($stmt->execute()) {
            echo "<p>Sign-up successful!</p>";
            $subject = "Welcome to BillBuddy!";
            $message = "Thanks for signing up with BillBuddy.\nWe're glad you're here!";
            $headers = "From: support@BillBuddy.com";
            mail($email,$subject,$message,$headers);
        } else {
            echo "<p>Error: " . $stmt->error . "</p>";
        }
        $stmt->close();
    } else {
        foreach ($errors as $error) {
            echo "<p>$error</p>";
        }
    }
}
?>
  <div class="container signup-container">
    <h1>BillBuddy</h1>
    <form method="POST" id="signupForm" novalidate>
      <input type="text" name="email" id="email" placeholder="Email" required />
      <input type="text" name="username" id="username" placeholder="Username" required />
      <input type="password" name="password" id="password" placeholder="Password" required />
      <input type="password" name="repassword" id="repassword" placeholder="Re-enter Password" required />
      <button type="submit">Sign-up</button>
    </form>
    <div class="footer">
      Already have an account? <a href="mainLogin.php">Click here to login.</a>
    </div>
  </div>
   <!-- JavaScript validation-->
   <script>
    document.addEventListener("DOMContentLoaded", () => {
      const form = document.getElementById("signupForm");

      form.addEventListener("submit", function (e) {
        const email = document.getElementById("email").value.trim();
        const username = document.getElementById("username").value.trim();
        const password = document.getElementById("password").value;
        const repassword = document.getElementById("repassword").value;

        let errors = [];
        //checks for correct format
        if (!email.includes("@")) {
          errors.push("Please enter a valid email.");
        }

        if (username.length < 3) {
          errors.push("Username must be at least 3 characters long.");
        }

        if (password.length < 6) {
          errors.push("Password must be at least 6 characters long.");
        }

        if (password !== repassword) {
          errors.push("Passwords do not match.");
        }

        if (errors.length > 0) {
          e.preventDefault(); // stop form submission
          alert(errors.join("\\n"));
        }
      });
    });
  </script>
</body>
</html>