<?php
// Connect to the database
$conn = new mysqli("localhost", "root", "", "cookedlocal");
// If the connection fails, stop execution and show error
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);
// Start building the base SQL query
$sql = "SELECT d.*, s.city, s.payment_methods, s.forname, s.surname
        FROM dishes d
        JOIN sellers s ON d.seller_id = s.id
        WHERE 1=1";

$params = [];
$types = "";

// Apply filters if the user submitted a search form
if ($_SERVER['REQUEST_METHOD'] === 'GET' && !empty($_GET)) {
  // Filter by keywords (dish name or description)
  if (!empty($_GET['keywords'])) {
      $sql .= " AND (d.dish_name LIKE ? OR d.description LIKE ?)";
      $kw = "%" . $_GET['keywords'] . "%";
      $params[] = $kw;
      $params[] = $kw;
      $types .= "ss";
  }
  // Filter by location (seller city)
  if (!empty($_GET['location'])) {
      $sql .= " AND s.city LIKE ?";
      $params[] = "%" . $_GET['location'] . "%";
      $types .= "s";
  }

  if (!empty($_GET['date'])) {
      $day = date('l', strtotime($_GET['date']));
      $sql .= " AND FIND_IN_SET(?, d.available_days)";
      $params[] = $day;
      $types .= "s";
  }
  // Filter by available day (based on selected date)
  if (!empty($_GET['payment']) && is_array($_GET['payment'])) {
      $placeholders = implode(" OR ", array_fill(0, count($_GET['payment']), "s.payment_methods LIKE ?"));
      $sql .= " AND ($placeholders)";
      foreach ($_GET['payment'] as $method) {
          $params[] = "%" . $method . "%";
          $types .= "s";
      }
  }
}
// Prepare the SQL statement
$stmt = $conn->prepare($sql);
if ($stmt && !empty($params)) {
    $stmt->bind_param($types, ...$params);
}
// Execute the query and get results
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Search Dishes</title>
  <style>

    body { 
      font-family: Arial, sans-serif; 
      background-color: #fff9f4; 
      margin: 0; 
    }
    
    header { 
      background-color: #fffdf9; 
      padding: 20px 40px; 
      box-shadow: 0 2px 5px rgba(0,0,0,0.05); 
      display: flex; 
      justify-content: space-between; 
      align-items: center; 
    }
    
    .logo img { 
      width: 150px; 
    }
    
    nav a { 
      text-decoration: none; 
      color: #333; 
      margin-left: 20px; 
      font-size: 1em; 
    }
    
    main { 
      padding: 30px; 
      background-color: #fedab8; 
    }

    .form-container {
      background: #ffffff;
      padding: 25px;
      border-radius: 10px;
      box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
      max-width: 800px;
      margin: 0 auto 30px auto;
    }
    .form-container h2 { 
      text-align: center; 
      margin-bottom: 20px; 
    }

    .input-group { 
      margin-bottom: 20px; 
    }

    .input-group label { 
      display: block; 
      font-weight: bold; 
      margin-bottom: 5px; 
    }

    .input-group input, .input-group select {
      width: 100%; 
      padding: 10px; 
      border: 1px solid #ccc; 
      border-radius: 5px; 
      font-size: 1em;
    }

    .payment-options label {
      margin-right: 20px;
    }
    
    .dish-item {
      padding: 15px;
      background: #fff;
      border-radius: 8px;
      margin: 15px auto;
      box-shadow: 0 1px 4px rgba(0,0,0,0.05);
      max-width: 800px;
    }

    .dish-item h3 a {
      color: #f35920;
      text-decoration: none;
    }

    .dish-item h3 a:hover {
      text-decoration: underline;
    }
    
    .dish-item p { 
      margin: 6px 0; 
    }

  </style>
</head>
<body>

<header>
  <div class="logo"><img src="Logo.png" alt="CookedLocal Logo"></div>
  <nav><a href="index.html">Home</a></nav>
</header>

<main>
  <div class="form-container">
    <!-- Search form -->
    <h2>Search Dishes</h2>
    <form method="get" action="search.php">
      <div class="input-group">
        <!-- Entry for dish -->
        <label for="keywords">Search</label>
        <input type="text" id="keywords" name="keywords" placeholder="e.g. vegan curry, lasagna...">
      </div>
      <div class="input-group">
        <!-- Location Input box -->
        <label for="location">Location</label>
        <input type="text" id="location" name="location" placeholder="e.g. London, Birmingham">
      </div>
      <div class="input-group">
        <!-- Date Input box -->
        <label for="date">Date</label>
        <input type="date" id="date" name="date">
      </div>
      <!-- Payment method selection (checkbox system) -->
      <div class="input-group payment-options">
        <label>Preferred Payment Method</label>
        <label><input type="checkbox" name="payment[]" value="paypal"> PayPal</label>
        <label><input type="checkbox" name="payment[]" value="credit-card"> Credit Card</label>
        <label><input type="checkbox" name="payment[]" value="cash"> Cash</label>
      </div>
      <!-- Search button to display all searches related to entry -->
      <div class="input-group">
        <button type="submit" style="background-color:#fe964f;color:#fff;padding:10px 20px;border:none;border-radius:5px;font-size:1em;cursor:pointer;">Search</button>
      </div>
    </form>
  </div>
  <!-- Search results -->
  <?php if ($result->num_rows > 0): ?>
    <?php while ($row = $result->fetch_assoc()): ?>
      <div class="dish-item">
        <h3>
          <a href="dish_details.php?id=<?= $row['id'] ?>">
            <?= htmlspecialchars($row['dish_name']) ?>
          </a>
        </h3>
        <!-- Dish information -->
        <p><strong>Description:</strong> <?= htmlspecialchars($row['description']) ?></p>
        <p><strong>Price:</strong> £<?= number_format($row['price'], 2) ?> – <?= (int)$row['portions'] ?> portions</p>
        <p><strong>Available:</strong> <?= htmlspecialchars($row['available_days']) ?> | <?= htmlspecialchars($row['time_start']) ?>–<?= htmlspecialchars($row['time_end']) ?></p>
        <p><strong>Allergy Info:</strong> <?= htmlspecialchars($row['allergy_info']) ?></p>
        <p><strong>Seller:</strong> <?= htmlspecialchars($row['forname'] . ' ' . $row['surname']) ?> (<?= htmlspecialchars($row['city']) ?>)</p>
        <p><strong>Payment Methods:</strong> <?= htmlspecialchars($row['payment_methods']) ?></p>
      </div>
    <?php endwhile; ?>
  <?php else: ?>
    <p style="text-align:center;max-width:800px;margin:20px auto;">No dishes found matching your criteria.</p>
  <?php endif; ?>
</main>

</body>
</html>

<?php
$stmt->close();
$conn->close();
?>
