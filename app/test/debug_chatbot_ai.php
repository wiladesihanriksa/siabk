<?php
// Debug script untuk chatbot AI
include 'koneksi.php';
include 'functions_app_settings.php';
include 'chatbot/gemini_ai.php';

echo "<h2>🔍 Debug Chatbot AI</h2>";

// Get app settings
$app_settings = getAppSettings($koneksi);

echo "<h3>📋 Current Settings:</h3>";
echo "<ul>";
echo "<li><strong>AI Provider:</strong> " . getSetting($app_settings, 'chatbot_ai_provider', 'gemini') . "</li>";
echo "<li><strong>Cloud AI Enabled:</strong> " . getSetting($app_settings, 'chatbot_cloud_enabled', '0') . "</li>";
echo "<li><strong>API Key:</strong> " . (getSetting($app_settings, 'chatbot_api_key', '') ? 'Set' : 'Not Set') . "</li>";
echo "<li><strong>Fallback Enabled:</strong> " . getSetting($app_settings, 'chatbot_fallback_enabled', '0') . "</li>";
echo "</ul>";

// Test Gemini AI directly
echo "<h3>🤖 Testing Gemini AI:</h3>";

try {
    $api_key = getSetting($app_settings, 'chatbot_api_key', '');
    if(empty($api_key)) {
        echo "<p style='color: red;'>❌ API Key not set</p>";
    } else {
        echo "<p style='color: green;'>✅ API Key found</p>";
        
        $gemini = new GeminiAI($api_key);
        echo "<p>Testing Gemini AI connection...</p>";
        
        $response = $gemini->getResponse("Hello, test message");
        echo "<p><strong>Response:</strong> " . htmlspecialchars($response) . "</p>";
        echo "<p style='color: green;'>✅ Gemini AI working!</p>";
    }
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error: " . htmlspecialchars($e->getMessage()) . "</p>";
    echo "<p><strong>Error Details:</strong></p>";
    echo "<pre>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
}

// Test chatbot API
echo "<h3>🧪 Testing Chatbot API:</h3>";

$test_message = "Hello, test message";
$data = json_encode(['message' => $test_message]);

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'http://localhost:8001/chatbot/chatbot_api.php');
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 30);

$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "<p><strong>HTTP Code:</strong> $http_code</p>";
echo "<p><strong>Response:</strong></p>";
echo "<pre>" . htmlspecialchars($response) . "</pre>";

if($http_code == 200) {
    $json = json_decode($response, true);
    if($json && isset($json['response'])) {
        echo "<p style='color: green;'>✅ Chatbot API working!</p>";
    } else {
        echo "<p style='color: red;'>❌ Invalid response format</p>";
    }
} else {
    echo "<p style='color: red;'>❌ HTTP Error: $http_code</p>";
}
?>
