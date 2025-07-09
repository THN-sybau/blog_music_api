<?php
require_once '../db.php';
header('Content-Type: application/json');

$user_id = $_POST['user_id'] ?? null;

if (!$user_id) {
    echo json_encode(['status' => false, 'message' => 'Thiếu user_id']);
    exit;
}

$stmt = $conn->prepare("DELETE FROM users WHERE user_id = ?");
$stmt->bind_param("i", $user_id);

if ($stmt->execute()) {
    echo json_encode(['status' => true, 'message' => 'Đã xóa người dùng']);
} else {
    echo json_encode(['status' => false, 'message' => 'Không thể xóa']);
}
?>
