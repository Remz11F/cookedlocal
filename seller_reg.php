<?php
// Database config
$host = "localhost";
$user = "root";
$password = "";
$database = "cookedlocal"; // Replace with your actual database name

$conn = new mysqli($host, $user, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// File upload paths
$uploadDir = "uploads/";
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}

// Save profile picture
$profileImagePath = "";
if (isset($_FILES["profile"]) && $_FILES["profile"]["error"] === 0) {
    $profileImagePath = $uploadDir . basename($_FILES["profile"]["name"]);
    move_uploaded_file($_FILES["profile"]["tmp_name"], $profileImagePath);
}

// Save certificate
$certificatePath = "";
if (isset($_FILES["certificate"]) && $_FILES["certificate"]["error"] === 0) {
    $certificatePath = $uploadDir . basename($_FILES["certificate"]["name"]);
    move_uploaded_file($_FILES["certificate"]["tmp_name"], $certificatePath);
}

// Get form values
$forname = $_POST["forname"];
$surname = $_POST["surname"];
$email = $_POST["email"];
$password = password_hash($_POST["password"], PASSWORD_BCRYPT);
$phone = $_POST["phone-number"];
$bio = $_POST["bio"];
$cuisine = $_POST["cuisine"];
$address = $_POST["address"];
$city = $_POST["city"];
$postcode = $_POST["postcode"];
$payment_methods = isset($_POST["payment_methods"]) ? implode(", ", $_POST["payment_methods"]) : "";

// Insert into database
$sql = "INSERT INTO approve_cook (
    forname, surname, email, password, phone, bio, cuisine,
    address, city, postcode, payment_methods,
    profile_image, certificate_image
) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

$stmt = $conn->prepare($sql);
$stmt->bind_param(
    "sssssssssssss",
    $forname, $surname, $email, $password, $phone, $bio, $cuisine,
    $address, $city, $postcode, $payment_methods,
    $profileImagePath, $certificatePath
);

if ($stmt->execute()) {
    // If true send user to login page
    header("Location: login.html");
    exit();
} else {
    // Display error if not
    echo "Error: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>
