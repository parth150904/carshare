<?php
require '../config.php';
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['id'])) {
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

$origin = isset($_POST['origin']) ? trim($_POST['origin']) : '';
$destination = isset($_POST['destination']) ? trim($_POST['destination']) : '';

if (empty($origin) || empty($destination)) {
    echo json_encode(['error' => 'Origin and destination are required.']);
    exit;
}

$env_file = '../.env';
if (file_exists($env_file)) {
    $lines = file($env_file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) continue;
        list($name, $value) = explode('=', $line, 2);
        $_ENV[trim($name)] = trim($value);
    }
}
$api_key = isset($_ENV['GEMINI_API_KEY']) ? $_ENV['GEMINI_API_KEY'] : '';

if (empty($api_key)) {
    echo json_encode(['error' => 'API Key missing']);
    exit;
}

$prompt = "You are an AI Fare Estimator for a carpooling platform in India (CarShare).
Given a route, estimate a fair price per seat in Indian Rupees (INR).
Assume average fuel cost and average car mileage in India.
Origin: " . $origin . "
Destination: " . $destination . "
Reply ONLY with a raw JSON object with two keys:
- 'estimated_price': an integer representing the fair price per seat in INR.
- 'reason': a very short string explaining the estimate (e.g. 'Approx 30km, avg fuel split among 3 passengers').
Do NOT include markdown like ```json.";

$url = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-3.6-flash:generateContent?key=' . $api_key;
$data = ['contents' => [['parts' => [['text' => $prompt]]]]];
$json_data = json_encode($data);

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $json_data);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($http_code === 429) {
    echo json_encode(['error' => 'AI is busy. Please enter price manually.']);
    exit;
}

$res_obj = json_decode($response, true);
if (isset($res_obj['candidates'][0]['content']['parts'][0]['text'])) {
    $ai_text = trim($res_obj['candidates'][0]['content']['parts'][0]['text']);
    $ai_text = str_replace('```json', '', $ai_text);
    $ai_text = str_replace('```', '', $ai_text);
    
    $fare_data = json_decode($ai_text, true);
    if ($fare_data && isset($fare_data['estimated_price'])) {
        echo json_encode($fare_data);
    } else {
        echo json_encode(['error' => 'Failed to parse AI response.', 'raw' => $ai_text]);
    }
} else {
    echo json_encode(['error' => 'API Error or Rate Limit']);
}
?>
