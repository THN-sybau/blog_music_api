<?php
require_once __DIR__ . '/vendor/autoload.php';
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

$input = json_decode(file_get_contents("php://input"), true);
$prompt = trim($input['message'] ?? '');

if (empty($prompt)) {
    echo json_encode(["status" => false, "message" => "Vui lòng nhập nội dung câu hỏi."]);
    exit;
}

$apiKey = $_ENV['OPENAI_API_KEY'];
$url = "https://openrouter.ai/api/v1/chat/completions";

$data = [
    "model" => "openai/gpt-4o", 
    "messages" => [
        ["role" => "system", "content" => "Bạn là trợ lý AI chỉ đưa ra gợi ý âm nhạc liên quan đến nội dung người dùng đang đọc. Nếu người dùng hỏi về chủ đề ngoài âm nhạc, hãy nói: “Xin lỗi, tôi chỉ hỗ trợ nội dung về âm nhạc trong blog này.”"],
        ["role" => "user", "content" => $prompt]
    ],
    "temperature" => 0.7,
    "max_tokens" => 1000
];

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Authorization: Bearer ' . $apiKey,
    'HTTP-Referer: https://localhost',
    'X-Title: BlogMusicChatbot'
]);
$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($http_code == 200) {
    $json = json_decode($response, true);
    $reply = $json['choices'][0]['message']['content'] ?? 'Không có phản hồi từ AI.';
    echo json_encode(["status" => true, "message" => $reply]);
} else {
    echo json_encode([
        "status" => false,
        "message" => "Lỗi gọi OpenAI API: $http_code",
        "raw" => $response
    ]);
}
