<?php
// Connection to the SQL database
$conn = new mysqli("localhost", "root", "", "cookedlocal");
// Validates the connection
if ($conn->connect_error) {
    // Error message if connection fails
    die("Error Failed Connection: " . $conn->connect_error);
}

// File upload path
$uploadDir = "uploads/";
if (!is_dir($uploadDir)) {
    // Directory has full permissions
    mkdir($uploadDir, 0777, true);
}

// stores the path the profiles images
$profileImagePath = "";
// Handles the profiles images
if (isset($_FILES["profile"]) && $_FILES["profile"]["error"] === 0) {
    $profileImagePath = $uploadDir . basename($_FILES["profile"]["name"]);
    // Move the uploaded file from temp location to defined path way
    move_uploaded_file($_FILES["profile"]["tmp_name"], $profileImagePath);
}

// Gets the data sent through the POST method
$forname = $_POST["forname"];
$surname = $_POST["surname"];
$email = $_POST["email"];
$password = password_hash($_POST["password"], PASSWORD_BCRYPT);
$phone = $_POST["phone_number"];
$bio = $_POST["bio"];
$food_preference = $_POST["food_preference"];
$hobbies_interests = $_POST["hobbies_interests"];
$address = $_POST["address"];
$city = $_POST["city"];
$postcode = $_POST["postcode"];
$billing_address = $_POST["billing_address"];
$payment_methods = isset($_POST["payment_methods"]) ? implode(", ", $_POST["payment_methods"]) : "";

// Inserts the following data into the database 
$sql = "INSERT INTO buyers (forname, surname, email, password, phone, bio, food_preference, hobbies_interests, address, city, postcode, billing_address, payment_methods, profile_image
) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

// SQL statement for execution
$stmt = $conn->prepare($sql);
$stmt->bind_param(
    "ssssssssssssss",
    $forname, $surname, $email, $password, $phone,
    $bio, $food_preference, $hobbies_interests,
    $address, $city, $postcode, $billing_address,
    $payment_methods, $profileImagePath
);

if ($stmt->execute()) {
    // Redirects user to login page once user submits data
    header("Location: login.html");
    exit();
} else {
    // If not successfull the system will display and error message
    echo "Error: " . $stmt->error;
}
// Closes both the statnment and the connection 
$stmt->close();
$conn->close();
?>
