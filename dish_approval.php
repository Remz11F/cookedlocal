<?php
// Enable error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json');

// Connection to DB
$conn = new mysqli("localhost", "root", "", "cookedlocal");
if ($conn->connect_error) {
  echo json_encode(["success" => false, "error" => "DB connection failed"]);
  exit;
}

// Reads request data
$data = json_decode(file_get_contents("php://input"), true);
$id = isset($data['id']) ? (int)$data['id'] : 0;
$action = $data['action'] ?? '';

if (!$id || !in_array($action, ['approve', 'suspend'])) {
  echo json_encode(["success" => false, "error" => "Invalid request"]);
  exit;
}

// Gets all dish from approval_dishes table 
$get = $conn->prepare("SELECT * FROM approval_dishes WHERE id = ?");
$get->bind_param("i", $id);
$get->execute();
$result = $get->get_result();

if ($result->num_rows === 0) {
  echo json_encode(["success" => false, "error" => "Dish not found"]);
  exit;
}

$dish = $result->fetch_assoc();

if ($action === 'approve') {
  // Inserts into dishes
  $insert = $conn->prepare("INSERT INTO dishes 
    (seller_id, dish_name, price, portions, allergy_info, description, image_path, available_days, time_start, time_end, created_at)
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

  $insert->bind_param(
    "isdisssssss",
    $dish['seller_id'],
    $dish['dish_name'],
    $dish['price'],
    $dish['portions'],
    $dish['allergy_info'],
    $dish['description'],
    $dish['image_path'],
    $dish['available_days'],
    $dish['time_start'],
    $dish['time_end'],
    $dish['created_at']
  );
  // If fails to insert dish
  if (!$insert->execute()) {
    echo json_encode(["success" => false, "error" => "Insert dishes failed: " . $conn->error]);
    exit;
  }

  $insert->close();

  // Delete from approval_dishes table
  $delete = $conn->prepare("DELETE FROM approval_dishes WHERE id = ?");
  $delete->bind_param("i", $id);
  $delete->execute();
  // validation of dish added
  if ($delete->affected_rows > 0) {
    echo json_encode(["success" => true, "message" => "Dish approved"]);
  } else {
    echo json_encode(["success" => false, "error" => "Failed delete"]);
  }

  $delete->close();
}

if ($action === 'suspend') {
  // Deletes from approval_dishes
  $delete = $conn->prepare("DELETE FROM approval_dishes WHERE id = ?");
  $delete->bind_param("i", $id);
  $delete->execute();
  // Verification if deletion was successful
  if ($delete->affected_rows > 0) {
    echo json_encode(["success" => true, "message" => "Dish removed"]);
  } else {
    echo json_encode(["success" => false, "error" => "Delete failed"]);
  }

  $delete->close();
}

$conn->close();
?>
