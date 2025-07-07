<?php
header('Content-Type: application/json');
require_once '../db.php';

// Debug: Ghi log dữ liệu nhận được
file_put_contents('order_debug.log', print_r($_POST, true), FILE_APPEND);

// Lấy dữ liệu từ POST
$user_id   = isset($_POST['user_id']) ? intval($_POST['user_id']) : null;
$album_id  = isset($_POST['album_id']) ? intval($_POST['album_id']) : null;
$name      = isset($_POST['name']) ? trim($_POST['name']) : null;
$address   = isset($_POST['address']) ? trim($_POST['address']) : null;
$phone     = isset($_POST['phone']) ? trim($_POST['phone']) : null;
$quantity  = isset($_POST['quantity']) ? intval($_POST['quantity']) : 1;
$order_date = date('Y-m-d H:i:s');
$status    = 'pending';

// Kiểm tra dữ liệu bắt buộc
if (!$user_id || !$album_id || !$name || !$address || !$phone) {
    echo json_encode([
        'success' => false,
        'status' => false,
        'message' => 'Thiếu thông tin bắt buộc.'
    ]);
    exit;
}

// Thêm đơn hàng vào database
$sql = "INSERT INTO orders (user_id, album_id, name, address, phone, quantity, order_date, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
if ($stmt) {
    $stmt->bind_param('iisssiss', $user_id, $album_id, $name, $address, $phone, $quantity, $order_date, $status);
    if ($stmt->execute()) {
        echo json_encode([
            'success' => true,
            'status' => true,
            'message' => 'Đặt hàng thành công!'
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'status' => false,
            'message' => 'Lỗi khi lưu đơn hàng: ' . $stmt->error
        ]);
    }
    $stmt->close();
} else {
    echo json_encode([
        'success' => false,
        'status' => false,
        'message' => 'Lỗi truy vấn: ' . $conn->error
    ]);
}
$conn->close();
