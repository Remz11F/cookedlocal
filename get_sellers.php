<?php
header('Content-Type: application/json');
// Database conection
$conn = new mysqli("localhost", "root", "", "cookedlocal");
// Validates if the the database connection was successful
if ($conn->connect_error) {
  echo json_encode([]);
  exit;
}
// SQL query to select data from the approve_cook table
$result = $conn->query("SELECT * FROM approve_cook");
// sellers list
$sellers = [];
// Fetches each row and appends it to the seller list
while ($row = $result->fetch_assoc()) {
  $sellers[] = $row;
}

echo json_encode($sellers);
?>
