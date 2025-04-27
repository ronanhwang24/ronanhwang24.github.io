<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Redirect if not logged in
if (!isset($_SESSION["username"]) || !isset($_SESSION["user_id"])) {
  header("Location: mainLogin.php");
  exit();
}

// Database connection
$servername = "localhost";
$db_username = "uqmfivnexadws";
$db_password = "bradisdabest";
$dbname = "db03qmxhguzp08";

$conn = new mysqli($servername, $db_username, $db_password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$user_id = $_SESSION["user_id"]; // Get user ID from session

// Add bill
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["add_bill"])) {
    $date = $_POST["date"];
    $amount = floatval($_POST["amount"]);
    $location = trim($_POST["location"]);
    $company = trim($_POST["company"]);
    $description = trim($_POST["description"]);
    $subscription = isset($_POST["subscription"]) ? 1 : 0;

    if ($date && $amount && $location && $company && $description) {
        $stmt = $conn->prepare("INSERT INTO user_bills (user_id, date, amount, location, company, description, subscription) VALUES (?, ?, ?, ?, ?, ?, ?)");
        if (!$stmt) {
            die("Prepare failed: " . $conn->error);
        }
        $stmt->bind_param("isdsssi", $user_id, $date, $amount, $location, $company, $description, $subscription);
        if (!$stmt->execute()) {
            die("Execute failed: " . $stmt->error);
        }
        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Add/Manage Bills</title>
  <link rel="stylesheet" href="add-manage.css" />
  <style>
    body { font-family: sans-serif; padding: 20px; }
    .container { max-width: 800px; margin: auto; }
    table { width: 100%; border-collapse: collapse; margin-top: 20px; }
    th, td { padding: 10px; border: 1px solid #ddd; text-align: left; }
    th { background-color: #f4f4f4; }
    form input, form textarea { display: block; margin-bottom: 10px; padding: 8px; width: 100%; }
    form button { padding: 10px 15px; }
    label {
  display: inline-flex;
  align-items: center;
  gap: 6px; 
}
  </style>
</head>
<body>
  <div class="container">
    <h1>Hello <?php echo htmlspecialchars($_SESSION['username']); ?>!</h1>

    <nav>
      <ul>
        <li><a href="upcoming.php">Upcoming</a></li>
        <li><a href="#view">View</a></li>
        <li><a href="add-manage.php">Add/Manage</a></li>
        <li><a href="contactus.php">Contact Us</a></li>
      </ul>
    </nav>

    <h2>Add a New Bill</h2>
    <form method="POST" action="add-manage.php">
      <input type="date" name="date" required />
      <input type="number" step="0.01" name="amount" placeholder="Amount" required />
      <input type="text" name="location" placeholder="Location" maxlength="50" required />
      <input type="text" name="company" placeholder="Company" maxlength="50" required />
      <textarea name="description" placeholder="Description" required></textarea>
      <label><input type="checkbox" name="subscription" value="1" /> Subscription?</label>
      <br>
      <button type="submit" name="add_bill">Add Bill</button>
    </form>

    <h2>Your Bills</h2>
    <table>
      <thead>
        <tr>
          <th>Date</th>
          <th>Amount</th>
          <th>Company</th>
          <th>Subscription</th>
        </tr>
      </thead>
      <tbody>
      <?php
      $stmt = $conn->prepare("SELECT date, amount, company, subscription FROM user_bills WHERE user_id = ?");
      if ($stmt) {
          $stmt->bind_param("i", $user_id);
          $stmt->execute();
          $result = $stmt->get_result();

          if ($result->num_rows === 0): ?>
              <tr><td colspan="4">You have no outgoing payments.</td></tr>
          <?php else:
              while ($bill = $result->fetch_assoc()): ?>
                  <tr>
                      <td><?= htmlspecialchars($bill["date"]) ?></td>
                      <td>$<?= number_format($bill["amount"], 2) ?></td>
                      <td><?= htmlspecialchars($bill["company"]) ?></td>
                      <td><?= $bill["subscription"] ? "Yes" : "No" ?></td>
                  </tr>
          <?php endwhile;
          endif;
          $stmt->close();
      }
      $conn->close();
      ?>
      </tbody>
    </table>
  </div>
</body>
</html>
