<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
session_start();
// DB connect
$conn = new mysqli("localhost", "root", "", "your_database_name");
// Validates if user is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'seller') {
    header("Location: login.html");
    exit();
}
// Validates for connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$seller_id = $_SESSION['user_id'];
$uploadDir = "uploads/";
// Validates directory exists
if (!is_dir($uploadDir)) {
    mkdir($uploadDir);
}
// Profile image path
$profileImagePath = '';
// Validates if image was uploaded
if (!empty($_FILES['profile']['name'])) {
    $profileImagePath = $uploadDir . time() . '_' . basename($_FILES['profile']['name']);
    move_uploaded_file($_FILES['profile']['tmp_name'], $profileImagePath);
}
// Certificate path
$certificatePath = '';
// Validates if certificate was uploaded
if (!empty($_FILES['certificate']['name'])) {
    $certificatePath = $uploadDir . time() . '_' . basename($_FILES['certificate']['name']);
    move_uploaded_file($_FILES['certificate']['tmp_name'], $certificatePath);
}
// Get data
$forname = $_POST['forname'];
$surname = $_POST['surname'];
$email = $_POST['email'];
$phone = $_POST['phone-number'];
$bio = $_POST['bio'];
cuisine = $_POST['cuisine'];
$address = $_POST['address'];
$city = $_POST['city'];
$postcode = $_POST['postcode'];
$payment_methods = isset($_POST['payment_methods']) ? implode(", ", $_POST['payment_methods']) : "";
// SQL Update details
$sql = "UPDATE sellers SET 
        forname=?, surname=?, email=?, phone=?, bio=?, 
        cuisine=?, address=?, city=?, postcode=?, payment_methods=?";

$params = [$forname, $surname, $email, $phone, $bio, $cuisine, $address, $city, $postcode, $payment_methods];
$types = "ssssssssss";

if ($profileImagePath) {
    $sql .= ", profile_image=?";
    $params[] = $profileImagePath;
    $types .= "s";
}
if ($certificatePath) {
    $sql .= ", certificate_image=?";
    $params[] = $certificatePath;
    $types .= "s";
}

$sql .= " WHERE id=?";
$params[] = $seller_id;
$types .= "i";

$stmt = $conn->prepare($sql);
if (!$stmt) {
    die("SQL error: " . $conn->error);
}

$stmt->bind_param($types, ...$params);

if ($stmt->execute()) {
    // sends user back to seller dashboard
    header("Location: seller_dashboard.html");
    exit();
} else {
    // Error message
    echo "Error: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>