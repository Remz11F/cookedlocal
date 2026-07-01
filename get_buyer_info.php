<?php
session_start();
// Validation to confirm whether or not user is valid to access the resources
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'buyer') {
    // Error message
    echo json_encode(["error" => "Unauthorized"]);
    exit();
}
// Database connection
$conn = new mysqli("localhost", "root", "", "cookedlocal");
if ($conn->connect_error) {
    // Error message if connection is not successful
    echo json_encode(["error" => "Database connection failed"]);
    exit();
}

// Gets buyer ID
$buyer_id = $_SESSION['user_id'];
// SQL statment to select buyers info
$stmt = $conn->prepare("SELECT * FROM buyers WHERE id = ?");
$stmt->bind_param("i", $buyer_id);
// executes SQL
$stmt->execute();
// Gets result
$result = $stmt->get_result();
echo json_encode($result->fetch_assoc());
?>
