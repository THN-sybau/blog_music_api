<?php
require_once "../db.php";
header('Content-Type: application/json');
ini_set('display_errors', 1);
file_put_contents("log_add_blog.txt", print_r($_POST, true));

if (!isset($_POST['type'])) {
    echo json_encode(["status" => false, "message" => "Thiếu tham số 'type'"]);
    exit;
}

$type = $_POST['type'];
$title = $_POST['title'] ?? '';
$image_cover = $_POST['image_cover'] ?? '';
$date = date("Y-m-d");

if ($type === "post") {
    // Kiểm tra dữ liệu bắt buộc cho post
    if (!isset($_POST['title'], $_POST['author'], $_POST['image_cover'], $_POST['subtitle'], $_POST['introduction'], $_POST['main_content'], $_POST['conclusion'], $_POST['tags'])) {
        echo json_encode(["status" => false, "message" => "Thiếu thông tin cho post"]);
        exit;
    }

    $author = $_POST['author'];
    $subtitle = $_POST['subtitle'];
    $introduction = $_POST['introduction'];
    $main_content = $_POST['main_content'];
    $conclusion = $_POST['conclusion'];
    $tags = $_POST['tags'];

    // Thêm vào bảng posts
    $stmt = $conn->prepare("INSERT INTO posts (title, author, image_cover, date) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $title, $author, $image_cover, $date);

    if ($stmt->execute()) {
        $post_id = $stmt->insert_id;

        $stmt2 = $conn->prepare("INSERT INTO post_detail (post_id, subtitle, introduction, main_content, conclusion, tags) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt2->bind_param("isssss", $post_id, $subtitle, $introduction, $main_content, $conclusion, $tags);

        if ($stmt2->execute()) {
            echo json_encode(["status" => true, "message" => "Thêm bài viết thành công"]);
            exit;
        } else {
            echo json_encode(["status" => false, "message" => "Lỗi chi tiết bài viết: " . $stmt2->error]);
            exit;
        }
    } else {
        echo json_encode(["status" => false, "message" => "Lỗi thêm bài viết: " . $stmt->error]);
        exit;
    }

} elseif ($type === "review") {
    // Kiểm tra dữ liệu bắt buộc cho review
    if (!isset($_POST['album_title'], $_POST['artist'], $_POST['release_date'], $_POST['reviewer'], $_POST['image_cover'], $_POST['subtitle'], $_POST['summary'], $_POST['tracklist'], $_POST['main_content'], $_POST['score'], $_POST['conclusion'], $_POST['tags'])) {
        echo json_encode(["status" => false, "message" => "Thiếu thông tin cho review"]);
        exit;
    }

    $artist = $_POST['artist'];
    $reviewer = $_POST['reviewer'];
    $album_title = $_POST['album_title'];
    $genre = $_POST['genre'];
    $subtitle = $_POST['subtitle'];
    $summary = $_POST['summary'];
    $tracklist = $_POST['tracklist'];
    $main_content = $_POST['main_content'];
    $score = $_POST['score'];
    $conclusion = $_POST['conclusion'];
    $release_date  = $_POST['release_date']; 
    $tags = $_POST['tags'];

    // Thêm vào bảng reviews
    $stmt = $conn->prepare("INSERT INTO reviews (album_title, artist, reviewer, genre, image_cover, review_date, release_date) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssssss", $album_title, $artist, $reviewer, $genre, $image_cover, $date, $release_date);

    if ($stmt->execute()) {
        $review_id = $stmt->insert_id;

        $stmt2 = $conn->prepare("INSERT INTO review_detail (review_id, subtitle, summary, tracklist, main_content, score, conclusion, tags) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt2->bind_param("isssssss", $review_id, $subtitle, $summary, $tracklist, $main_content, $score, $conclusion, $tags);

        if ($stmt2->execute()) {
            echo json_encode(["status" => true, "message" => "Thêm review thành công"]);
            exit;
        } else {
            echo json_encode(["status" => false, "message" => "Lỗi chi tiết review: " . $stmt2->error]);
            exit;
        }
    } else {
        echo json_encode(["status" => false, "message" => "Lỗi thêm review: " . $stmt->error]);
        exit;
    }

} else {
    echo json_encode(["status" => false, "message" => "Loại bài viết không hợp lệ"]);
    exit;
}
?>
