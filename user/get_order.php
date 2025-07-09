<?php
require '../db.php'; // File kết nối DB

header("Content-Type: application/json");

$user_id = isset($_GET['user_id']) ? intval($_GET['user_id']) : 0;

$sql = ($user_id > 0)
    ? "SELECT o.*, r.album_title, u.name, u.role 
       FROM orders o 
       JOIN reviews r ON o.album_id = r.id 
       JOIN users u ON o.user_id = u.user_id 
       WHERE o.user_id = ?"
    : "SELECT o.*, r.album_title, u.name, u.role 
       FROM orders o 
       JOIN reviews r ON o.album_id = r.id 
       JOIN users u ON o.user_id = u.user_id";

$stmt = $conn->prepare($sql);

if ($user_id > 0) {
    $stmt->bind_param("i", $user_id);
}

$stmt->execute();
$result = $stmt->get_result();

$orders = [];

while ($row = $result->fetch_assoc()) {
    $orders[] = $row;
}

echo json_encode([
    "status" => true,
    "data" => $orders
], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
