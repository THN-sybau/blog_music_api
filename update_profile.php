<?php
require_once "db.php";
header('Content-Type: application/json');

ini_set('display_errors', 1);
error_reporting(E_ALL);

$user_id = $_POST['user_id'] ?? '';
$name    = $_POST['name'] ?? '';
$email   = $_POST['email'] ?? '';

if (!$user_id || !$name || !$email) {
    echo json_encode(["status" => false, "message" => "Thiếu dữ liệu"]);
    exit;
}

// Kiểm tra trùng email (trừ chính người dùng hiện tại)
$check = $conn->prepare("SELECT user_id FROM users WHERE email = ? AND user_id != ?");
$check->bind_param("si", $email, $user_id);
$check->execute();
$result = $check->get_result();
if ($result->num_rows > 0) {
    echo json_encode(["status" => false, "message" => "Email đã tồn tại"]);
    exit;
}

// Cập nhật hồ sơ
$stmt = $conn->prepare("UPDATE users SET name = ?, email = ? WHERE user_id = ?");
$stmt->bind_param("ssi", $name, $email, $user_id);
$success = $stmt->execute();

if ($success) {
    echo json_encode(["status" => true, "message" => "Cập nhật thành công"]);
} else {
    echo json_encode(["status" => false, "message" => "Lỗi khi cập nhật"]);
}
