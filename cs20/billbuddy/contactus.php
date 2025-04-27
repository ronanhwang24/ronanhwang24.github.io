<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Check if the user is logged in
if (!isset($_SESSION["user_id"])) {
    header("Location: mainLogin.php");
    exit();
}

// Setup variables for confirmation
$form_submitted = false;
$submission_error = '';

// Database connection
$servername = "localhost";
$db_username = "uqmfivnexadws";
$db_password = "bradisdabest";
$dbname = "db03qmxhguzp08";

$conn = new mysqli($servername, $db_username, $db_password, $dbname);
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

$user_id = $_SESSION["user_id"];
$username = $_SESSION["username"];

// Fetch the user's email from the database
$stmt = $conn->prepare("SELECT email FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($email);
$stmt->fetch();
$stmt->close();

// Process form if submitted
if ($_SERVER["REQUEST_METHOD"] === "POST") {
  $issue = trim($_POST["issue"]);
  $message = trim($_POST["message"]);

  if (empty($issue) || empty($message)) {
      $submission_error = "Please fill out all fields.";
  } else {
      // Prepare the email
      $to = $email; // Send to the user's email
      $subject = "BillBuddy Support - Message Received";
      $body = "Hello $username,\n\n";
      $body .= "Thank you for reaching out to us regarding: \"$issue\".\n\n";
      $body .= "We have received your message and our team will work to resolve the issue as quickly as possible.\n\n";
      $body .= "Your Message:\n\"$message\"\n\n";
      $body .= "Thank you for using BillBuddy!\n\n- The BillBuddy Team";

      $headers = "From: support@billbuddy.com\r\n";
      $headers .= "Reply-To: support@billbuddy.com\r\n";
      $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";

      // Send the email
      if (mail($to, $subject, $body, $headers)) {
          $form_submitted = true;
      } else {
          $submission_error = "There was an error sending your message. Please try again.";
      }
  }
}

$conn->close();
?>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Contact Us - BillBuddy</title>
  <link rel="stylesheet" href="homepage.css" />
  <style>
    form {
      display: flex;
      flex-direction: column;
      gap: 1rem;
      margin-top: 1.5rem;
    }

    select, textarea {
      padding: 0.75rem;
      border: 1px solid #ccc;
      border-radius: 8px;
      font-size: 1rem;
      width: 100%;
      box-sizing: border-box;
    }

    button {
      background-color: #2e7d32;
      color: white;
      padding: 0.75rem;
      border: none;
      border-radius: 8px;
      font-size: 1rem;
      cursor: pointer;
    }

    button:hover {
      background-color: #27642a;
    }

    .confirmation {
      background-color: #d4edda;
      color: #155724;
      padding: 1rem;
      border: 1px solid #c3e6cb;
      border-radius: 8px;
      margin-top: 1rem;
    }

    .error {
      background-color: #f8d7da;
      color: #721c24;
      padding: 1rem;
      border: 1px solid #f5c6cb;
      border-radius: 8px;
      margin-top: 1rem;
    }
  </style>
</head>
<body>
  <div class="container">
    <h1>Contact Us</h1>
    <nav>
      <ul>
        <li><a href="homepage.php">Home</a></li>
        <li><a href="view.php">View</a></li>
        <li><a href="add-manage.php">Add/Manage</a></li>
        <li><a href="contactus.php">Contact Us</a></li>
      </ul>
    </nav>

    <?php if ($form_submitted): ?>
      <div class="confirmation">
        <h2>Thank you for contacting us!</h2>
        <p>We have received your message and will get back to you shortly.</p>
      </div>
    <?php else: ?>

      <?php if (!empty($submission_error)): ?>
        <div class="error"><?php echo htmlspecialchars($submission_error); ?></div>
      <?php endif; ?>

      <form method="POST" action="">
        <p><strong>Name:</strong> <?php echo htmlspecialchars($username); ?></p>
        <p><strong>Email:</strong> <?php echo htmlspecialchars($email); ?></p>

        <label for="issue">Select Issue:</label>
        <select name="issue" id="issue" required>
          <option value="">-- Please Choose an Option --</option>
          <option value="Billing Problem">Billing Problem</option>
          <option value="Technical Issue">Technical Issue</option>
          <option value="Feature Request">Feature Request</option>
          <option value="Account Help">Account Help</option>
          <option value="Other">Other</option>
        </select>

        <textarea name="message" rows="5" placeholder="Type your message here..." required></textarea>

        <button type="submit">Send Message</button>
      </form>

    <?php endif; ?>
  </div>
</body>
</html>