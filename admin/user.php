<?php
require_once '../db.php';
header('Content-Type: application/json');

$sql = "SELECT user_id, name, email, created_at, role FROM users ORDER BY created_at DESC";
$result = $conn->query($sql);

$users = [];

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $users[] = $row;
    }
}

echo json_encode($users);
?>
