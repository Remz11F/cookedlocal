<?php
// Trys to coonect to the database
$conn = new mysqli("localhost", "root", "", "cookedlocal");

session_start();
// Identifies if the user is not logged into
if (!isset($_SESSION['user_id'])) {
    // Error message
    echo json_encode(["error" => "Not logged in"]);
    exit();
}
// Validates the connection with the database
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);
// Gets the users id
$seller_id = $_SESSION['user_id'];
// Retrieves dishes from the seller
$result = $conn->query("SELECT * FROM dishes WHERE seller_id = $seller_id");
// List to store dishes
$dishes = [];
// Loops through dishes and adds to dish list
while ($row = $result->fetch_assoc()) {
    $row['days'] = explode(",", $row['available_days']);
    $dishes[] = $row;
}
 
header('Content-Type: application/json');
// Output dishes in json
echo json_encode($dishes);
?>
          