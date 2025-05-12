<?php
session_start();
header("Content-Type: application/json");

if (!isset($_SESSION['useremail'])) {
    echo json_encode(["success" => false, "message" => "User not logged in"]);
    exit();
}

$conn = new mysqli("localhost", "root", "", "farm2bag");

if ($conn->connect_error) {
    die(json_encode(["success" => false, "message" => "Database connection failed"]));
}

$email = $_SESSION['useremail'];

$stmt = $conn->prepare("SELECT fullname, email FROM users WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {
    echo json_encode(["success" => true, "fullname" => $row['fullname'], "email" => $row['email']]);
} else {
    echo json_encode(["success" => false, "message" => "User not found"]);
}

$stmt->close();
$conn->close();
?>