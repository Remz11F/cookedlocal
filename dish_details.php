<?php
// Connect to the database
$conn = new mysqli("localhost", "root", "", "cookedlocal");
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

// Validate and sanitize the dish ID from the URL
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if (!$id) { echo "Invalid dish ID."; exit; }

// Fetch the dish details from the database
$dish = $conn->query("SELECT * FROM dishes WHERE id = $id")->fetch_assoc();
if (!$dish) { echo "Dish not found."; exit; }

// Handle form submission for posting a review
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $review = trim($_POST['review']);
  $rating = isset($_POST['rating']) ? (int)$_POST['rating'] : 0;

  // Validate the review and rating
  if ($review !== '' && $rating >= 1 && $rating <= 5) {
    $stmt = $conn->prepare("INSERT INTO reviews (dish_id, content, rating) VALUES (?, ?, ?)");
    $stmt->bind_param("isi", $id, $review, $rating);
    $stmt->execute();
    $stmt->close();
  }
}

// Fetch all reviews for the dish, newest first
$reviews = $conn->query("SELECT content, rating, created_at FROM reviews WHERE dish_id = $id ORDER BY created_at DESC");
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title><?= htmlspecialchars($dish['dish_name']) ?> - CookedLocal</title>
  <style>
    /* Body of website */
    body {
      margin: 0;
      font-family: Arial, sans-serif;
      background-color: #fff9f4;
      color: #000;
    }
    /* Header of Website (how it will be) */
    header {
      background-color: #fffdf9;
      padding: 20px 40px;
      display: flex;
      justify-content: space-between;
      align-items: center;
      box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
    }
    /* Format of Logo */
    .logo img {
      width: 150px;
      height: auto;
    }
    /* Navigate links */
    nav a {
      text-decoration: none;
      color: #333;
      margin-left: 20px;
      font-size: 1em;
    }
    /* The Main Content */
    main {
      padding: 40px 20px;
      background-color: #fedab8;
      display: flex;
      justify-content: center;
    }
    /* Content */
    .content {
      max-width: 800px;
      background: #fff;
      padding: 30px;
      border-radius: 10px;
      box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
    }

    .content img {
      width: 100%;
      max-height: 300px;
      object-fit: cover;
      border-radius: 10px;
    }
    .content h2 {
      color: #f35920;
      margin-top: 20px;
    }
    .content p {
      margin: 10px 0;
    }
    /* Button Designs */
    .btns {
      margin-top: 20px;
    }
    .btns form {
      display: inline-block;
    }
    .btns button {
      padding: 10px 20px;
      background-color: #fe964f;
      color: white;
      border: none;
      border-radius: 5px;
      cursor: pointer;
    }
    .btns button:hover {
      background-color: #f35920;
    }
    .review-section {
      margin-top: 40px;
    }
    .review-section h3 {
      margin-bottom: 10px;
    }
    .review-section textarea,
    .review-section select {
      width: 100%;
      padding: 10px;
      font-size: 1em;
      margin-top: 10px;
      margin-bottom: 15px;
      border-radius: 5px;
      border: 1px solid #ccc;
    }
    .review {
      background-color: #fffdf9;
      padding: 15px;
      border-radius: 8px;
      margin-bottom: 15px;
      box-shadow: 0 2px 6px rgba(0, 0, 0, 0.05);
    }
    .review small {
      color: #666;
      display: block;
      margin-top: 5px;
    }
    .back-link {
      display: inline-block;
      margin-bottom: 20px;
      background-color: #fe964f;
      color: #fff;
      padding: 10px 20px;
      border-radius: 6px;
      text-decoration: none;
    }
    .back-link:hover {
      background-color: #f35920;
    }
  </style>
</head>
<body>

<header>
  <div class="logo">
    <img src="Logo.png" alt="CookedLocal Logo" />
  </div>
  <nav>
    <a href="index.html">Home</a>
  </nav>
</header>

<main>
  <div class="content">
   
    <a href="search.php" class="back-link">← Back to Search</a>

    <!-- Dishes image and details taken from database -->
    <img src="<?= htmlspecialchars($dish['image_path']) ?>" alt="Dish Image">
    <h2><?= htmlspecialchars($dish['dish_name']) ?></h2>
    <p><strong>Price:</strong> £<?= number_format($dish['price'], 2) ?></p>
    <p><strong>Portions:</strong> <?= $dish['portions'] ?></p>
    <p><strong>Allergy Info:</strong> <?= htmlspecialchars($dish['allergy_info']) ?></p>
    <p><strong>Description:</strong><br><?= nl2br(htmlspecialchars($dish['description'])) ?></p>
    <p><strong>Availability:</strong> <?= htmlspecialchars($dish['available_days']) ?> 
      (<?= htmlspecialchars($dish['time_start']) ?> - <?= htmlspecialchars($dish['time_end']) ?>)</p>

    <!-- Checkout button -->
    <div class="btns">
      <form method="GET" action="checkout.php">
        <input type="hidden" name="dish_id" value="<?= $dish['id'] ?>">
        <button type="submit">Checkout</button>
      </form>
    </div>

    <!-- Review form section -->
    <div class="review-section">
      <h3>Leave a Review</h3>
      <form method="POST">
        <textarea name="review" rows="4" required placeholder="Write your review..."></textarea>
        <select name="rating" required>
          <option value="">Select a rating</option>
          <?php for ($i = 1; $i <= 5; $i++): ?>
            <option value="<?= $i ?>"><?= $i ?> Star<?= $i > 1 ? 's' : '' ?></option>
          <?php endfor; ?>
        </select>
        <button type="submit">Submit Review</button>
      </form>

      <!-- Display existing reviews -->
      <h3>Reviews</h3>
      <?php if ($reviews->num_rows > 0): ?>
        <?php while ($r = $reviews->fetch_assoc()): ?>
          <div class="review">
            <p><?= nl2br(htmlspecialchars($r['content'])) ?></p>
            <p><strong>Rating:</strong> <?= str_repeat("⭐", $r['rating']) ?> (<?= $r['rating'] ?>/5)</p>
            <small><?= $r['created_at'] ?></small>
          </div>
        <?php endwhile; ?>
      <?php else: ?>
        <p>No reviews yet.</p>
      <?php endif; ?>
    </div>
  </div>
</main>

</body>
</html>
