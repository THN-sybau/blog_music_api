<?php
require_once "../db.php";

if (!isset($_GET['artist']) || !isset($_GET['id'])) {
    http_response_code(400); // Bad Request
    echo json_encode(["error" => "Missing parameters"]);
    exit;
}

$artist = $_GET['artist'];
$id = intval($_GET['id']);

$sql = "SELECT * FROM reviews WHERE artist = ? AND id != ? ORDER BY review_date DESC LIMIT 4";
$stmt = $conn->prepare($sql);
$stmt->bind_param("si", $artist, $id);
$stmt->execute();
$result = $stmt->get_result();

$reviews = [];
while ($row = $result->fetch_assoc()) {
    $reviews[] = $row;
}

echo json_encode(["reviews" => $reviews]);
