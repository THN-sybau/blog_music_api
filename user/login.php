<?php
include '../db.php';
header("Content-Type: application/json; charset=UTF-8");

$data = json_decode(file_get_contents("php://input"), true);

$email = strtolower(trim($data['email'] ?? ''));
$password = trim($data['password'] ?? '');

if (empty($email) || empty($password)) {
    echo json_encode([
        "status" => false,
        "message" => "Thiếu email hoặc mật khẩu"
    ]);
    exit;
}


$sql = "SELECT * FROM users WHERE email=? AND password=?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $email, $password);
$stmt->execute();
$result = $stmt->get_result();


if ($row = $result->fetch_assoc()) {
    echo json_encode([
        "status" => true,
        "message" => "Login success",
        "user_id" => $row['user_id'],
        "name" => $row['role'] === 'admin' ? $row['name'] . " - Quản trị viên" : $row['name'],
        "email" => $row['email'],
        "role" => $row['role']
    ]);
} else {
    echo json_encode([
        "status" => false,
        "message" => "Sai email hoặc mật khẩu"
    ]);
}

$stmt->close();
$conn->close();
