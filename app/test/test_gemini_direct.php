<?php
// Test Gemini API directly
include 'koneksi.php';
include 'functions_app_settings.php';

echo "<h2>🧪 Test Gemini API Direct</h2>";

$app_settings = getAppSettings($koneksi);
$api_key = getSetting($app_settings, 'chatbot_api_key', '');

if(empty($api_key)) {
    echo "<p style='color: red;'>❌ API Key not found</p>";
    exit;
}

echo "<p><strong>API Key:</strong> " . substr($api_key, 0, 20) . "...</p>";

// Test with curl directly
$url = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash:generateContent?key=' . $api_key;

$data = [
    'contents' => [
        [
            'parts' => [
                ['text' => 'Hello, this is a test message. Please respond with a simple greeting.']
            ]
        ]
    ],
    'generationConfig' => [
        'temperature' => 0.7,
        'maxOutputTokens' => 100,
        'topP' => 0.8,
        'topK' => 10
    ]
];

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json'
]);
curl_setopt($ch, CURLOPT_TIMEOUT, 30);

echo "<p>Testing Gemini API...</p>";
echo "<p><strong>URL:</strong> " . $url . "</p>";

$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$error = curl_error($ch);
curl_close($ch);

echo "<p><strong>HTTP Code:</strong> $http_code</p>";

if($error) {
    echo "<p style='color: red;'><strong>cURL Error:</strong> $error</p>";
}

if($http_code == 200) {
    $json = json_decode($response, true);
    if($json && isset($json['candidates'][0]['content']['parts'][0]['text'])) {
        $text = $json['candidates'][0]['content']['parts'][0]['text'];
        echo "<p style='color: green;'>✅ Success!</p>";
        echo "<p><strong>Response:</strong> " . htmlspecialchars($text) . "</p>";
    } else {
        echo "<p style='color: red;'>❌ Invalid response format</p>";
        echo "<pre>" . htmlspecialchars($response) . "</pre>";
    }
} else {
    echo "<p style='color: red;'>❌ HTTP Error: $http_code</p>";
    echo "<pre>" . htmlspecialchars($response) . "</pre>";
}
?>
