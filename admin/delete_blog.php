<?php
require_once "../db.php";
header('Content-Type: application/json');

$type = $_POST['type']; // 'post' or 'review'
$id_or_title = trim($_POST['id_or_title'] ?? '');

if (empty($type) || empty($id_or_title)) {
    echo json_encode(["status" => false, "message" => "Thiếu tham số"]);
    exit;
}

$is_id = is_numeric($id_or_title);
$id = null;

if ($type == "post") {
    if ($is_id) {
        $id = intval($id_or_title);
    } else {
        $title = $conn->real_escape_string($id_or_title);
        $query = "SELECT id FROM posts WHERE title = '$title' LIMIT 1";
        $result = $conn->query($query);
        if ($result && $result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $id = intval($row['id']);
        } else {
            echo json_encode(["status" => false, "message" => "Không tìm thấy bài viết"]);
            exit;
        }
    }

    $conn->query("DELETE FROM post_detail WHERE post_id = $id");
    $conn->query("DELETE FROM posts WHERE id = $id");
    echo json_encode(["status" => true, "message" => "Đã xóa bài viết"]);
}
else if ($type == "review") {
    if ($is_id) {
        $id = intval($id_or_title);
    } else {
        $title = $conn->real_escape_string($id_or_title);
        $query = "SELECT id FROM reviews WHERE album_title = '$title' LIMIT 1";
        $result = $conn->query($query);
        if ($result && $result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $id = intval($row['id']);
        } else {
            echo json_encode(["status" => false, "message" => "Không tìm thấy review"]);
            exit;
        }
    }

    $conn->query("DELETE FROM review_detail WHERE review_id = $id");
    $conn->query("DELETE FROM reviews WHERE id = $id");
    echo json_encode(["status" => true, "message" => "Đã xóa review"]);
}
else {
    echo json_encode(["status" => false, "message" => "Loại không hợp lệ"]);
}
?>
