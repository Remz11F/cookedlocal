<?php
session_start();

// Database connection
$conn = new mysqli("localhost", "root", "", "cookedlocal");
// Validates connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
// Gets email, password and role
$email = $_POST['email'] ?? '';
$password = $_POST['password'] ?? '';
$role = $_POST['role'] ?? '';

// This is a built in admin logging for testing purposes
if ($role === 'admin') {
    // if it matches with credentials then is redirects user to admin pannel
    if ($email === 'admin@gmail.com' && $password === 'password') {
        $_SESSION['role'] = 'admin';
        header("Location: admin.html");
        exit();
    } else {
        // Else error message
        header("Location: login.html?error=" . urlencode("Invalid admin credentials"));
        exit();
    }
}

// This line checks against bothe buyer and seller table
$table = ($role === 'buyer') ? 'buyers' : 'sellers';
// SQL code which selects users ID, Email and Password within chosen table
$stmt = $conn->prepare("SELECT id, password FROM $table WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result && $result->num_rows > 0) {
    $user = $result->fetch_assoc();
    // Checks if password is correct
    if (password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['role'] = $role;
        header("Location: " . $role . "_dashboard.html");
        exit();
    }
}

header("Location: login.html?error=" . urlencode("Invalid credentials"));
exit();
