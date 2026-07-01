<?php
// Connection to database cookedlocal
$conn = new mysqli("localhost", "root", "", "cookedlocal");
if ($conn->connect_error) {
  // Error if database connection fails
  http_response_code(500);
  echo json_encode(["error" => "Database connection failed"]);
  exit;
}
// SQL to get dishes from table
$result = $conn->query("SELECT * FROM approval_dishes ORDER BY created_at DESC");
// Normal empty list to store dishes
$dishes = [];
// gets each row and puts it into the dishes list
while ($row = $result->fetch_assoc()) {
  $dishes[] = $row;
}

header('Content-Type: application/json');
echo json_encode($dishes);

$conn->close();
?>
