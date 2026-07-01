<?php
// Validates the user 
session_start();
if (!isset($_SESSION['user_id'])) {
    // Error message
    echo "Not logged in";
    exit();
}

// Database connection
$conn = new mysqli("localhost", "root", "", "cookedlocal");
// Validates whether the connection was successful
if ($conn->connect_error) {
    // Error message
    die("Connection failed: " . $conn->connect_error);
}
// Gets seller ID
$seller_id = $_SESSION['user_id'];

// Gets data (name, price, portions, allergy, description, days, start, end)
$name = $_POST['name'] ?? '';
$price = isset($_POST['price']) ? floatval($_POST['price']) : 0.00;
$portions = $_POST['portions'] ?? '';
$allergy = $_POST['allergy'] ?? '';
$description = $_POST['description'] ?? '';
$days = isset($_POST['days']) ? implode(",", $_POST['days']) : '';
$start = $_POST['time_start'] ?? '';
$end = $_POST['time_end'] ?? '';
 
// Image upload
// Image path way
$image_path = '';
if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
    $image_path = 'uploads/' . time() . '_' . basename($_FILES['image']['name']);
    // Creats file to directory
    move_uploaded_file($_FILES['image']['tmp_name'], $image_path);
}

// SQL query insert data into the approval_dishes table
$stmt = $conn->prepare("INSERT INTO approval_dishes 
  (seller_id, dish_name, price, portions, allergy_info, description, image_path, available_days, time_start, time_end)
  VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

// Validates if statement was successful
if (!$stmt) {
    die("SQL error: " . $conn->error);
}


$stmt->bind_param("isdissssss",
    $seller_id,
    $name,
    $price,
    $portions,
    $allergy,
    $description,
    $image_path,
    $days,
    $start,
    $end
);


if ($stmt->execute()) {
    // Redirects the user to manage_dishes page
    header("Location: manage_dishes.html");
    exit();
} else {
    // Displays an error message
    echo "Error: Insert Failed: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>
