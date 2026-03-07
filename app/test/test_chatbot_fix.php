<?php
// Test file untuk memastikan chatbot berfungsi
session_start();
include 'koneksi.php';
include 'functions_app_settings.php';
include 'functions_chatbot_settings.php';

echo "<h1>🧪 Test Chatbot Fix</h1>";

// Test 1: Database Connection
echo "<h2>1. Test Database Connection</h2>";
if($koneksi) {
    echo "✅ Database connection OK<br>";
} else {
    echo "❌ Database connection failed<br>";
}

// Test 2: App Settings
echo "<h2>2. Test App Settings</h2>";
try {
    $app_settings = getAppSettings($koneksi);
    echo "✅ App settings loaded: " . count($app_settings) . " settings<br>";
    
    $app_name = getSetting($app_settings, 'app_name', 'ePoint');
    echo "App name: " . $app_name . "<br>";
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "<br>";
}

// Test 3: Chatbot Settings
echo "<h2>3. Test Chatbot Settings</h2>";
try {
    $chatbot_config = getChatbotConfig($koneksi);
    echo "✅ Chatbot config loaded<br>";
    echo "Chatbot name: " . $chatbot_config['name'] . "<br>";
    echo "Chatbot enabled: " . $chatbot_config['enabled'] . "<br>";
    echo "Quick actions: " . count($chatbot_config['quick_actions']) . "<br>";
    echo "FAQ: " . count($chatbot_config['faq']) . "<br>";
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "<br>";
}

// Test 4: API Endpoint
echo "<h2>4. Test API Endpoint</h2>";
$api_url = "http://localhost:8001/chatbot/chatbot_api.php";
echo "Testing API: $api_url<br>";

$test_data = json_encode(['message' => 'test']);
$context = stream_context_create([
    'http' => [
        'method' => 'POST',
        'header' => 'Content-Type: application/json',
        'content' => $test_data
    ]
]);

$result = file_get_contents($api_url, false, $context);
if($result !== false) {
    echo "✅ API response received<br>";
    echo "Response: " . htmlspecialchars($result) . "<br>";
} else {
    echo "❌ API request failed<br>";
}

// Test 5: JavaScript Path
echo "<h2>5. Test JavaScript Path</h2>";
$js_path = "./chatbot/chatbot.js";
if(file_exists($js_path)) {
    echo "✅ JavaScript file exists: $js_path<br>";
} else {
    echo "❌ JavaScript file not found: $js_path<br>";
}

$api_path = "./chatbot/chatbot_api.php";
if(file_exists($api_path)) {
    echo "✅ API file exists: $api_path<br>";
} else {
    echo "❌ API file not found: $api_path<br>";
}

echo "<br><h2>🎯 Langkah Perbaikan:</h2>";
echo "1. <a href='setup_chatbot_settings.php'>Setup Database</a><br>";
echo "2. <a href='update_chatbot_data.php'>Update Data</a><br>";
echo "3. <a href='index.php'>Test Chatbot</a><br>";

echo "<br><a href='index.php'>← Kembali ke Halaman Utama</a>";
?>
