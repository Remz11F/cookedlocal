<?php

// Database connection
$conn = new mysqli("localhost", "root", "", "cookedlocal");
// Validates the connection of the database
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

// Gets the name, email, subject, message
$name = $_POST['name'] ?? '';
$email = $_POST['email'] ?? '';
$subject = $_POST['subject'] ?? '';
$message = $_POST['message'] ?? '';

// Checks if the fields contains information
if ($name && $email && $subject && $message) {
  $stmt = $conn->prepare("INSERT INTO inquiries (name, email, subject, message) VALUES (?, ?, ?, ?)");
  $stmt->bind_param("ssss", $name, $email, $subject, $message);

  $stmt->execute();
  $stmt->close();
  // Sends user to inquiry webpage
  header("Location: inquiry.html");
  exit();
} else {
  // Displays error message
  echo "Error";
}
$conn->close();
?>
