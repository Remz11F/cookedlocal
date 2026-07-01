<?php
session_start();
// Database connection
$conn = new mysqli("localhost", "root", "", "cookedlocal");
// validates if the user haslogged in (seller)
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'seller') {
    echo json_encode(["error" => "Unauthorized"]);
    exit();
}
// validates the connection between the database
if ($conn->connect_error) {
    echo json_encode(["error" => "Connection failed"]);
    exit();
}
// Gets the seller ID
$seller_id = $_SESSION['user_id'];
// SQL suery to select all data from the sellers table 
$stmt = $conn->prepare("SELECT * FROM sellers WHERE id = ?");
$stmt->bind_param("i", $seller_id);
$stmt->execute();
$result = $stmt->get_result();
// Displays the selelrs data and encodes it in json
echo json_encode($result->fetch_assoc());
?>
