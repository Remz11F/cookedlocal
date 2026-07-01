<?php
session_start();
// Connect to the MySQL database
$conn = new mysqli("localhost", "root", "", "cookedlocal");
// Check if the user is logged in and has the role of 'buyer'
if ($conn->connect_error) {
    echo json_encode(["error" => "DB connection failed"]);
    exit();
}

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'seller') {
    echo json_encode(["error" => "Unauthorized"]);
    exit();
}

$seller_id = $_SESSION['user_id'];
// Handles request to fetch seller profile
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $stmt = $conn->prepare("SELECT * FROM sellers WHERE id = ?");
    $stmt->bind_param("i", $seller_id);
    $stmt->execute();
    $result = $stmt->get_result();
    echo json_encode($result->fetch_assoc());
    exit();
}
// Handles request to update seller profile
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $profile_path = "";
    // profile image upload if provided
    if (!empty($_FILES['profile']['name'])) {
        $profile_path = "uploads/" . time() . "_" . basename($_FILES['profile']['name']);
        move_uploaded_file($_FILES['profile']['tmp_name'], $profile_path);
    }

    // Certificate path
    $certificate_path = "";
    if (!empty($_FILES['certificate']['name'])) {
        $certificate_path = "uploads/" . time() . "_" . basename($_FILES['certificate']['name']);
        move_uploaded_file($_FILES['certificate']['tmp_name'], $certificate_path);
    }
    // Collect form data
    $forname = $_POST['forname'];
    $surname = $_POST['surname'];
    $email = $_POST['email'];
    $phone = $_POST['phone-number'];
    $bio = $_POST['bio'];
    $cuisine = $_POST['cuisine'];
    $address = $_POST['address'];
    $city = $_POST['city'];
    $postcode = $_POST['postcode'];
    $payment_methods = isset($_POST['payment_methods']) ? implode(", ", $_POST['payment_methods']) : "";
    // SQL query to upddate
    $sql = "UPDATE sellers SET 
        forname = ?, surname = ?, email = ?, phone = ?, bio = ?, cuisine = ?, 
        address = ?, city = ?, postcode = ?, payment_methods = ?";

    $params = [$forname, $surname, $email, $phone, $bio, $cuisine, $address, $city, $postcode, $payment_methods];
    $types = "ssssssssss";
    // Adds profile path to update
    if ($profile_path) {
        $sql .= ", profile_image = ?";
        $params[] = $profile_path;
        $types .= "s";
    }
    // Adds certificate path to update
    if ($certificate_path) {
        $sql .= ", certificate_image = ?";
        $params[] = $certificate_path;
        $types .= "s";
    }

    $sql .= " WHERE id = ?";
    $params[] = $seller_id;
    $types .= "i";
    // Validates if perp failed which would display and error
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        echo "SQL Error: " . $conn->error;
        exit();
    }

    $stmt->bind_param($types, ...$params);
    if ($stmt->execute()) {
        echo json_encode(["success" => true, "redirect" => "seller_dashboard.html"]);
        exit();
    } else {
        echo json_encode(["success" => false, "error" => "Update failed: " . $stmt->error]);
        exit();

    }
    exit();
}
?>
