<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

if (!isset($_SESSION["user_id"])) {
    header("Location: mainLogin.php");
    exit();
}

$servername = "localhost";
$db_username = "uqmfivnexadws";
$db_password = "bradisdabest";
$dbname = "db03qmxhguzp08";
$conn = new mysqli($servername, $db_username, $db_password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$user_id = $_SESSION["user_id"];

$form_submitted = false; // <- Track if bills are submitted

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["submit"])) {
    if (isset($_POST['selected_bills'])) {
        $selected_bills = $_POST['selected_bills'];

        $stmt = $conn->prepare("DELETE FROM user_bills WHERE bill_id = ? AND user_id = ?");
        foreach ($selected_bills as $bill_id) {
            $stmt->bind_param("ii", $bill_id, $user_id);
            $stmt->execute();
        }
        $stmt->close();
    }

    $form_submitted = true; // <- Mark that submission happened
}

// Get user's bills
$stmt = $conn->prepare("SELECT bill_id, date, amount, location, company, description, subscription FROM user_bills WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>BillBuddy - Manage Your Bills</title>
  <link rel="stylesheet" href="upcoming.css">
  <style>
    /* Fireworks overlay if triggered */
    #fireworks-background {
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background: url('fireworks.gif') center center no-repeat;
      background-size: cover;
      z-index: 1000;
      pointer-events: none;
      animation: fadeOut 5s forwards;
    }

    /* Optional: Fade it out after 5s */
    @keyframes fadeOut {
      0% { opacity: 1; }
      80% { opacity: 1; }
      100% { opacity: 0; }
    }
  </style>
</head>
<body>

  <?php if ($form_submitted): ?>
    <div id="fireworks-background"></div>
  <?php endif; ?>

  <div class="container">
    <h1>Hello <?php echo htmlspecialchars($_SESSION['username']); ?>!</h1>
    <nav>
      <ul>
        <li><a href="upcoming.php">Upcoming</a></li>
        <li><a href="view.php">View</a></li>
        <li><a href="add-manage.php">Add/Manage</a></li>
        <li><a href="contactus.php">Contact Us</a></li>
      </ul>
    </nav>
    <div id="upcoming-payments">
      <h2>Here are your upcoming payments:</h2>
      <form method="POST" id="payments-form">
        <div id="payments-list">
          <?php
          while ($bill = $result->fetch_assoc()) {
              echo "<div class='payment-item' id='payment-" . $bill['bill_id'] . "'>";
              echo "<p>Date: " . htmlspecialchars($bill['date']) . "</p>";
              echo "<p>Amount: $" . number_format($bill['amount'], 2) . "</p>";
              echo "<p>Location: " . htmlspecialchars($bill['location']) . "</p>";
              echo "<p>Company: " . htmlspecialchars($bill['company']) . "</p>";
              echo "<p>Description: " . htmlspecialchars($bill['description']) . "</p>";
              echo "<p>Subscription: " . ($bill['subscription'] ? 'Yes' : 'No') . "</p>";
              echo "<label for='bill-" . $bill['bill_id'] . "'>Select to Mark as Paid: </label>";
              echo "<input type='checkbox' name='selected_bills[]' value='" . $bill['bill_id'] . "' id='bill-" . $bill['bill_id'] . "' />";
              echo "</div><br>";
          }
          ?>
        </div>
        <button type="submit" id="yes-button" name="submit">Submit Paid Bills!</button>
      </form>
    </div>
  </div>

</body>
</html>

<?php
$stmt->close();
$conn->close();
?>