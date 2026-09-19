<?php
// Test Case 2: AI Fare Estimator API Logic Test
echo "<h3>Test Case 2: AI Fare Estimator API</h3>";

// We simulate a request to the local API endpoint
$url = "http://localhost/carshare/user/api_fare_estimator.php";
$data = ['origin' => 'Ahmedabad', 'destination' => 'Surat'];

$options = [
    'http' => [
        'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
        'method'  => 'POST',
        'content' => http_build_query($data)
    ]
];

$context  = stream_context_create($options);
$result = @file_get_contents($url, false, $context);

if ($result === FALSE) {
    echo "<p style='color:red;'>&#10008; FAIL: Could not reach the API endpoint (Ensure XAMPP Apache is running).</p>";
} else {
    $json = json_decode($result, true);
    if (isset($json['estimated_price']) && isset($json['reason'])) {
        echo "<p style='color:green;'>&#10004; PASS: API returned a valid estimated price: &#8377;" . htmlspecialchars($json['estimated_price']) . "</p>";
        echo "<p style='color:gray; font-size:12px;'>Reason: " . htmlspecialchars($json['reason']) . "</p>";
    } elseif (isset($json['error'])) {
        echo "<p style='color:orange;'>&#9888; WARNING: API returned an error (API Key might be missing or rate-limited): " . htmlspecialchars($json['error']) . "</p>";
    } else {
        echo "<p style='color:red;'>&#10008; FAIL: Invalid JSON response.</p>";
    }
}
?>

