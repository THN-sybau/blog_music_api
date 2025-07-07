<?php
header("Content-Type: application/json");
require_once("../db.php");

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['type'])) {
    $type = $_GET['type'];
    if ($type === 'post') {
        $sql = "SELECT id FROM posts ORDER BY id DESC";
    } elseif ($type === 'review') {
        $sql = "SELECT id FROM reviews ORDER BY id DESC";
    } else {
        http_response_code(400);
        echo json_encode(["status" => false, "message" => "Loại blog không hợp lệ"]);
        exit;
    }

    $result = $conn->query($sql);
    $ids = [];

    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $ids[] = (int)$row['id'];
        }
    }

    echo json_encode($ids);
    $conn->close();
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(["status" => false, "message" => "Chỉ chấp nhận phương thức PUT"]);
    exit;
}

$input = file_get_contents("php://input");
$data = json_decode($input, true);

if (!$data) {
    http_response_code(400);
    echo json_encode(["status" => false, "message" => "Dữ liệu không hợp lệ"]);
    exit;
}

$type = $data['type'] ?? '';
$id = intval($data['id'] ?? 0);

if ($type === 'post') {
    // posts
    $title = $data['title'];
    $author = $data['author'];
    $image = $data['image_cover'];

    $stmt1 = $conn->prepare("UPDATE posts SET title=?, author=?, image_cover=? WHERE id=?");
    $stmt1->bind_param("sssi", $title, $author, $image, $id);

    // post_details
    $subtitle = $data['subtitle'];
    $intro = $data['introduction'];
    $content = $data['main_content'];
    $conclusion = $data['conclusion'];
    $tags = $data['tags'];

    $stmt2 = $conn->prepare("UPDATE post_detail SET subtitle=?, introduction=?, main_content=?, conclusion=?, tags=? WHERE post_id=?");
    $stmt2->bind_param("sssssi", $subtitle, $intro, $content, $conclusion, $tags, $id);

} elseif ($type === 'review') {
    // reviews
    $album_title = $data['album_title'];
    $artist = $data['artist'];
    $genre = $data['genre'];
    $rating = $data['rating'];
    $reviewer = $data['reviewer'];
    $release_date = $data['release_date'];
    $image = $data['image_cover'];

    $stmt1 = $conn->prepare("UPDATE reviews SET album_title=?, artist=?, genre=?, rating=?, reviewer=?, release_date=?, image_cover=? WHERE id=?");
    $stmt1->bind_param("sssdsssi", $album_title, $artist, $genre, $rating, $reviewer, $release_date, $image, $id);

    // review_details
    $subtitle = $data['subtitle'];
    $summary = $data['summary'];
    $tracklist = $data['tracklist'];
    $content = $data['main_content'];
    $score = $data['score'];
    $conclusion = $data['conclusion'];
    $tags = $data['tags'];

    $stmt2 = $conn->prepare("UPDATE review_detail SET subtitle=?, summary=?, tracklist=?, main_content=?, score=?, conclusion=?, tags=? WHERE review_id=?");
    $stmt2->bind_param("ssssdssi", $subtitle, $summary, $tracklist, $content, $score, $conclusion, $tags, $id);

} else {
    http_response_code(400);
    echo json_encode(["status" => false, "message" => "Loại blog không hợp lệ"]);
    exit;
}

// Thực hiện 2 lệnh cập nhật
$success1 = $stmt1->execute();
$success2 = $stmt2->execute();

if ($success1 && $success2) {
    echo json_encode(["status" => true, "message" => "Cập nhật thành công"], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
} else {
    $errorMsg = ($stmt1->error ?: '') . ' ' . ($stmt2->error ?: '');
    echo json_encode(["status" => false, "message" => "Lỗi: $errorMsg"]);
}

$stmt1->close();
$stmt2->close();
$conn->close();
