<?php
session_start();
include 'db_connection.php';

if (!isset($_SESSION['email'])) {
    header("Location: login.html");
    exit();
}

$email = $_SESSION['email'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $age = $_POST['age'];
    $height = $_POST['height'];
    $weight = $_POST['weight'];
    $disease = $_POST['disease'];

    // Calculate BMI
    $bmi = ($height > 0) ? ($weight / (($height / 100) * ($height / 100))) : 0;

    // Update user details
    $sql = "UPDATE users SET age=?, height=?, weight=?, bmi=?, disease=? WHERE email=?";
    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        die("Query preparation failed: " . $conn->error);
    }

    $stmt->bind_param("iidsss", $age, $height, $weight, $bmi, $disease, $email);

    if ($stmt->execute()) {
        echo "<script>alert('Profile updated successfully!'); window.location='profile.php';</script>";
    } else {
        echo "Error updating record: " . $conn->error;
    }

    $stmt->close();
    $conn->close();
}
?>