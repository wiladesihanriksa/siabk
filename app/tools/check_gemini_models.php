<?php
// Check available Gemini models
include 'koneksi.php';
include 'functions_app_settings.php';

echo "<h2>🔍 Check Available Gemini Models</h2>";

$app_settings = getAppSettings($koneksi);
$api_key = getSetting($app_settings, 'chatbot_api_key', '');

if(empty($api_key)) {
    echo "<p style='color: red;'>❌ API Key not found</p>";
    exit;
}

// List available models
$url = 'https://generativelanguage.googleapis.com/v1beta/models?key=' . $api_key;

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 30);

$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "<p><strong>HTTP Code:</strong> $http_code</p>";

if($http_code == 200) {
    $json = json_decode($response, true);
    if($json && isset($json['models'])) {
        echo "<p style='color: green;'>✅ Models found!</p>";
        echo "<h3>Available Models:</h3>";
        echo "<ul>";
        foreach($json['models'] as $model) {
            $name = $model['name'] ?? 'Unknown';
            $displayName = $model['displayName'] ?? 'Unknown';
            $supportedMethods = $model['supportedGenerationMethods'] ?? [];
            $hasGenerateContent = in_array('generateContent', $supportedMethods);
            
            echo "<li>";
            echo "<strong>$displayName</strong> ($name)";
            if($hasGenerateContent) {
                echo " ✅ <span style='color: green;'>Supports generateContent</span>";
            } else {
                echo " ❌ <span style='color: red;'>No generateContent support</span>";
            }
            echo "</li>";
        }
        echo "</ul>";
    } else {
        echo "<p style='color: red;'>❌ Invalid response format</p>";
        echo "<pre>" . htmlspecialchars($response) . "</pre>";
    }
} else {
    echo "<p style='color: red;'>❌ HTTP Error: $http_code</p>";
    echo "<pre>" . htmlspecialchars($response) . "</pre>";
}
?>
