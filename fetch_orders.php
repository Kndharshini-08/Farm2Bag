<?php
session_start();
header("Content-Type: application/json");
include 'db_connection.php';

// Check if user is logged in
if (!isset($_SESSION['user_email'])) {
    echo json_encode(["error" => "User not logged in"]);
    exit;
}

$user_email = $_SESSION['user_email'];

// Fetch order history
$sql = "SELECT o.id, o.customer_name, o.created_at, 
               GROUP_CONCAT(oi.product_name, ' (', oi.quantity, 'x) - $', oi.price SEPARATOR ', ') AS items 
        FROM orders o 
        JOIN order_items oi ON o.id = oi.order_id 
        WHERE o.customer_email = ? 
        GROUP BY o.id 
        ORDER BY o.created_at DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $user_email);
$stmt->execute();
$result = $stmt->get_result();

$orders = [];
while ($row = $result->fetch_assoc()) {
    $orders[] = $row;
}

echo json_encode($orders);
?>