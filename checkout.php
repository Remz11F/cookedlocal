<?php

$conn = new mysqli("localhost", "root", "", "cookedlocal");

// Stop execution if database connection fails
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

// Get the dish ID from the URL, cast it safely to an integer
$dish_id = isset($_GET['dish_id']) ? (int)$_GET['dish_id'] : 0;

// Fetch the dish information from the database
$dish = $conn->query("SELECT * FROM dishes WHERE id = $dish_id")->fetch_assoc();

// If the dish is not found, display an error and exit
if (!$dish) {
  echo "Dish not found.";
  exit;
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  // Collect user-submitted form data
  $buyer_name = $_POST['name'];
  $email = $_POST['email'];
  $address = $_POST['address'];
  $phone = $_POST['phone'];
  $quantity = (int)$_POST['quantity']; 
  $price = $dish['price']; 
  $total = $quantity * $price; // Calculate total cost

  // Insert the order into the database 
  $stmt = $conn->prepare("INSERT INTO orders (dish_id, buyer_name, email, address, phone, quantity, total_price) VALUES (?, ?, ?, ?, ?, ?, ?)");
  $stmt->bind_param("issssii", $dish_id, $buyer_name, $email, $address, $phone, $quantity, $total);

  if ($stmt->execute()) {
    // If successful, redirect the user to the search page
    header("Location: search.html");
    exit;
  } else {
    // Show error if something goes wrong
    echo "Error: " . $stmt->error;
  }

  $stmt->close(); // Close the statement
}

$conn->close(); // Close the database connection
?>

<!DOCTYPE html>
<html>
<head>
  <title>Checkout - <?= htmlspecialchars($dish['dish_name']) ?></title>
  <style>
    /* Body of website */
    body { 
      font-family: Arial, sans-serif; 
      background: #fff9f4; 
      padding: 30px; 
    }
    /* Container that holds the form */
    .container {
      max-width: 650px;
      background: #fff;
      padding: 30px;
      margin: auto;
      border-radius: 10px;
      box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }
    /* Main Header */
    h2 { 
      color: #f35920; 
      margin-bottom: 20px; 
    }
    /* Style of the Label */
    label { 
      display: block; 
      margin: 10px 0 5px; 
      font-weight: bold; 
    }
    /* Inputs fields and text areas */
    input, textarea {
      width: 100%; 
      padding: 10px;
      border: 1px solid #ccc; 
      border-radius: 5px;
      font-size: 1em;
    }
    /* Creating columns */
    .row {
      display: flex;
      gap: 20px;
    }

    .row .half { 
      width: 50%; 
    }
    /* Button design */
    button {
      margin-top: 20px;
      padding: 12px 20px;
      background: #fe964f;
      border: none;
      color: white;
      font-weight: bold;
      border-radius: 5px;
      cursor: pointer;
    }
    /* Colour of button hovering */
    button:hover {
      background: #f35920;
    }

    .back-link {
      display: inline-block;
      margin-top: 20px;
      text-decoration: none;
      color: #f35920;
      font-weight: bold;
    }
  </style>
</head>
<body>
  <div class="container">
    <h2>Checkout: <?= htmlspecialchars($dish['dish_name']) ?></h2>

    <!-- Checkout Form -->
    <form method="POST">
      <!-- Name input -->
      <label>First Name</label>
      <input type="text" name="name" required>
      <!-- Email input -->
      <label>Email</label>
      <input type="email" name="email" required>
      <!-- Phone number input -->
      <label>Phone Number</label>
      <input type="text" name="phone" required>
      <!-- Address input -->
      <label>Delivery Address</label>
      <textarea name="address" required></textarea>
      <!-- Total quantity input -->
      <label>Quantity</label>
      <input type="number" name="quantity" value="1" min="1" max="<?= $dish['portions'] ?>" required>
      <hr style="margin: 30px 0;">

      <!-- Payment section  -->
      <h3 style="color: #e25d3b;">Details</h3>
      <!-- Card Name input -->
      <label>Cardholder</label>
      <input type="text" name="card_name" required>
      <!-- Card num input -->
      <label>Card Number</label>
      <input type="text" name="card_number" maxlength="19" placeholder="XXXX XXXX XXXX XXXX" required>

      <div class="row">
        <div class="half">
          <!-- Input of expiry date -->
          <label>Expiry Date</label>
          <input type="text" name="expiry" placeholder="MM/YY" required>
        </div>
        <!-- 3 number code of card input -->
        <div class="half">
          <label>CVV</label>
          <input type="text" name="cvv" maxlength="4" required>
        </div>
      </div>

      <button type="submit">Confirm</button>
    </form>

    <!-- Back to dish details page -->
    <a class="back-link" href="dish_details.php?id=<?= $dish['id'] ?>">← Back to Dish</a>
  </div>
</body>
</html>
