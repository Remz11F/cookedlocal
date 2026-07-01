<?php
session_start();
$conn = new mysqli("localhost", "root", "", "cookedlocal");
// Check if user is logged in and is buyer
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'buyer') {
    echo json_encode(["error" => "Unauthorised"]);
    exit();
}

$buyer_id = $_SESSION['user_id'];
// Handles request to fetch buyer profile
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $stmt = $conn->prepare("SELECT * FROM buyers WHERE id = ?");
    $stmt->bind_param("i", $buyer_id);
    $stmt->execute();
    echo json_encode($stmt->get_result()->fetch_assoc());
    exit();
}
// Handles request to update buyer profile
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $profile_path = "";
    // profile image upload if provided
    if (!empty($_FILES['profile']['name'])) {
        $profile_path = "uploads/" . time() . "_" . basename($_FILES['profile']['name']);
        move_uploaded_file($_FILES['profile']['tmp_name'], $profile_path);
    }
    // Fetchs data from table
    $forname = $_POST['forname'];
    $surname = $_POST['surname'];
    $email = $_POST['email'];
    $phone = $_POST['phone_number'];
    $bio = $_POST['bio'];
    $food_preference = $_POST['food_preference'];
    $hobbies = $_POST['hobbies_interests'];
    $address = $_POST['address'];
    $city = $_POST['city'];
    $postcode = $_POST['postcode'];
    $billing = $_POST['billing_address'];
    $payment_methods = isset($_POST['payment_methods']) ? implode(", ", $_POST['payment_methods']) : "";
    // SQL code for updating buyers profile
    $sql = "UPDATE buyers SET 
      forname=?, surname=?, email=?, phone=?, bio=?, food_preference=?, hobbies_interests=?, 
      address=?, city=?, postcode=?, billing_address=?, payment_methods=?";

    $params = [$forname, $surname, $email, $phone, $bio, $food_preference, $hobbies, $address, $city, $postcode, $billing, $payment_methods];
    $types = "ssssssssssss";
    // Adds the profile pic to the update
    if ($profile_path) {
      $sql .= ", profile_image=?";
      $params[] = $profile_path;
      $types .= "s";
    }
    // Specifies which to update using ID
    $sql .= " WHERE id=?";
    $params[] = $buyer_id;
    $types .= "i";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param($types, ...$params);

    if ($stmt->execute()) {
        echo "OK";
    } else {
        echo "Error: " . $stmt->error;
    }
    exit();
}
?>
