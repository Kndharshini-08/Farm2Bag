<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

header("Content-Type: application/json");

// Database Connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "farm2bag";

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die(json_encode(["status" => "error", "message" => "Database connection failed", "error" => $conn->connect_error]));
}

// Get JSON input
$data = json_decode(file_get_contents("php://input"), true);

// Debugging: Log received data
file_put_contents("debug_log.txt", print_r($data, true));

if (!$data || !isset($data['name'], $data['email'], $data['cart'])) {
    echo json_encode(["status" => "error", "message" => "Invalid input"]);
    exit;
}

// Extract data
$name = $conn->real_escape_string($data['name']);
$email = $conn->real_escape_string($data['email']);
$cart = $data['cart']; // Cart contains an array of items

if (empty($cart)) {
    echo json_encode(["status" => "error", "message" => "Cart is empty"]);
    exit;
}

// Insert into orders table
$order_sql = "INSERT INTO orders (customer_name, customer_email) VALUES (?, ?)";
$stmt = $conn->prepare($order_sql);

if (!$stmt) {
    die(json_encode(["status" => "error", "message" => "Prepare statement failed (orders)", "error" => $conn->error]));
}

$stmt->bind_param("ss", $name, $email);
if (!$stmt->execute()) {
    die(json_encode(["status" => "error", "message" => "Order placement failed", "error" => $stmt->error]));
}

$order_id = $stmt->insert_id; // Get the last inserted order ID
$stmt->close();

// Insert items into order_items table
$item_sql = "INSERT INTO order_items (order_id, product_name, price, quantity) VALUES (?, ?, ?, ?)";
$stmt = $conn->prepare($item_sql);

if (!$stmt) {
    die(json_encode(["status" => "error", "message" => "Prepare statement failed (order_items)", "error" => $conn->error]));
}

foreach ($cart as $item) {
    if (!isset($item['name'], $item['price'], $item['quantity'])) {
        echo json_encode(["status" => "error", "message" => "Missing item data", "item" => $item]);
        exit;
    }

    $product_name = $conn->real_escape_string($item['name']); // 🔹 Fix: Use 'name' instead of 'product_name'
    $price = floatval($item['price']);
    $quantity = intval($item['quantity']);

    $stmt->bind_param("isdi", $order_id, $product_name, $price, $quantity);

    if (!$stmt->execute()) {
        echo json_encode(["status" => "error", "message" => "Insert into order_items failed", "error" => $stmt->error]);
        exit;
    }
}

$stmt->close();
$conn->close();

echo json_encode(["status" => "success", "message" => "Order placed successfully!", "order_id" => $order_id]);
?>