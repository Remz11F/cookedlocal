<?php
// Begins the session
session_start();
// Checks whether or not the user has logged in
if (!isset($_SESSION['user_id'])) {
    // Error message if user is not and exits the script
    echo "Not logged in";
    exit();
}

// Database connection (username, password and database)
$conn = new mysqli("localhost", "root", "", "cookedlocal");
// Error messafe if connection is unsucessful
if ($conn->connect_error) die("Error the connection has failed: " . $conn->connect_error);

// Gets both the seller and dishes ID
$dish_id = $_POST['id'];
$seller_id = $_SESSION['user_id'];

// SQL statment for deleting dis
$stmt = $conn->prepare("DELETE FROM dishes WHERE id = ? AND seller_id = ?");
// ii is 2 ints
$stmt->bind_param("ii", $dish_id, $seller_id);
$stmt->execute();

if ($stmt->affected_rows > 0) {
    // DIsplays that it was successfully deleted
    echo "Deleted";
} else {
    // Error message that the deletion failed
    echo "Error deleting";
}

$stmt->close();
$conn->close();
?>
