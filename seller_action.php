<?php
// Enable error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json');

// DB connection
$conn = new mysqli("localhost", "root", "", "cookedlocal");
if ($conn->connect_error) {
  echo json_encode(["success" => false, "error" => "DB connection failed"]);
  exit;
}

// Read request
$data = json_decode(file_get_contents("php://input"), true);
$id = isset($data['id']) ? (int)$data['id'] : 0;
$action = $data['action'] ?? '';

if (!$id || !in_array($action, ['approve', 'suspend'])) {
  echo json_encode(["success" => false, "error" => "Invalid request"]);
  exit;
}

// Fetch cook record from approve_cook
$get = $conn->prepare("SELECT * FROM approve_cook WHERE id = ?");
$get->bind_param("i", $id);
$get->execute();
$result = $get->get_result();

if ($result->num_rows === 0) {
  echo json_encode(["success" => false, "error" => "Cook not found"]);
  exit;
}

$cook = $result->fetch_assoc();

if ($action === 'approve') {
  // SQL code to insert into sellers table
  $insert = $conn->prepare("INSERT INTO sellers 
    (id, forname, surname, email, password, phone, bio, cuisine, address, city, postcode, payment_methods, profile_image, certificate_image) 
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

  $insert->bind_param(
    "isssssssssssss",
    $cook['id'],
    $cook['forname'],
    $cook['surname'],
    $cook['email'],
    $cook['password'],
    $cook['phone'],
    $cook['bio'],
    $cook['cuisine'],
    $cook['address'],
    $cook['city'],
    $cook['postcode'],
    $cook['payment_methods'],
    $cook['profile_image'],
    $cook['certificate_image']
  );
  // Validates if adding was successful or an error occured
  if (!$insert->execute()) {
    echo json_encode(["success" => false, "error" => "Failed to add: " . $conn->error]);
    exit;
  }
  $insert->close();

  // Delete related dishes 
  $conn->query("DELETE FROM dishes WHERE seller_id = $id");

  // SQL query deletes from approve_cook table
  $delete = $conn->prepare("DELETE FROM approve_cook WHERE id = ?");
  $delete->bind_param("i", $id);
  $delete->execute();

  if ($delete->affected_rows > 0) {
    // Cook was approved and added to table
    echo json_encode(["success" => true, "message" => "Cook approved"]);
  } else {
    // Error due to deletion not being successful
    echo json_encode(["success" => false, "error" => "Error delete failed: " . $conn->error]);
  }
  $delete->close();
}
// if the button suspend is clicked delete seller
if ($action === 'suspend') {
  $conn->query("DELETE FROM dishes WHERE seller_id = $id");

  $delete = $conn->prepare("DELETE FROM approve_cook WHERE id = ?");
  $delete->bind_param("i", $id);
  $delete->execute();

  if ($delete->affected_rows > 0) {
    // Cook was successfully removed
    echo json_encode(["success" => true, "message" => "Cook was removed"]);
  } else {
    // Error with deletion
    echo json_encode(["success" => false, "error" => "Deletetion failed: " . $conn->error]);
  }

  $delete->close();
}

$conn->close();
?>
