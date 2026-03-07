<?php
// Debug chatbot untuk melihat error yang terjadi
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>🐛 Debug Chatbot</h1>";

// Test 1: Include files
echo "<h2>1. Test Include Files</h2>";
try {
    include 'koneksi.php';
    echo "✅ koneksi.php loaded<br>";
} catch (Exception $e) {
    echo "❌ Error loading koneksi.php: " . $e->getMessage() . "<br>";
}

try {
    include 'functions_app_settings.php';
    echo "✅ functions_app_settings.php loaded<br>";
} catch (Exception $e) {
    echo "❌ Error loading functions_app_settings.php: " . $e->getMessage() . "<br>";
}

try {
    include 'functions_chatbot_settings.php';
    echo "✅ functions_chatbot_settings.php loaded<br>";
} catch (Exception $e) {
    echo "❌ Error loading functions_chatbot_settings.php: " . $e->getMessage() . "<br>";
}

// Test 2: Database connection
echo "<h2>2. Test Database Connection</h2>";
if(isset($koneksi) && $koneksi) {
    echo "✅ Database connection OK<br>";
} else {
    echo "❌ Database connection failed<br>";
}

// Test 3: Functions
echo "<h2>3. Test Functions</h2>";
if(function_exists('getAppSettings')) {
    echo "✅ getAppSettings() exists<br>";
} else {
    echo "❌ getAppSettings() not found<br>";
}

if(function_exists('getChatbotConfig')) {
    echo "✅ getChatbotConfig() exists<br>";
} else {
    echo "❌ getChatbotConfig() not found<br>";
}

// Test 4: Chatbot config
echo "<h2>4. Test Chatbot Config</h2>";
try {
    $chatbot_config = getChatbotConfig($koneksi);
    echo "✅ Chatbot config loaded<br>";
    echo "Name: " . $chatbot_config['name'] . "<br>";
    echo "Enabled: " . $chatbot_config['enabled'] . "<br>";
} catch (Exception $e) {
    echo "❌ Error loading chatbot config: " . $e->getMessage() . "<br>";
}

// Test 5: API endpoint
echo "<h2>5. Test API Endpoint</h2>";
$api_url = "http://localhost:8001/chatbot/chatbot_api.php";
echo "Testing: $api_url<br>";

$postdata = json_encode(['message' => 'test']);
$opts = [
    'http' => [
        'method' => 'POST',
        'header' => 'Content-Type: application/json',
        'content' => $postdata
    ]
];

$context = stream_context_create($opts);
$result = file_get_contents($api_url, false, $context);

if($result !== false) {
    echo "✅ API response received<br>";
    echo "Response: " . htmlspecialchars($result) . "<br>";
} else {
    echo "❌ API request failed<br>";
    $error = error_get_last();
    if($error) {
        echo "Error: " . $error['message'] . "<br>";
    }
}

// Test 6: JavaScript console
echo "<h2>6. JavaScript Test</h2>";
echo "<script>
console.log('Testing chatbot JavaScript...');
try {
    // Test if chatbot elements exist
    const toggle = document.getElementById('chatbot-toggle');
    const container = document.getElementById('chatbot-container');
    const input = document.getElementById('chatbot-input');
    
    console.log('Toggle element:', toggle);
    console.log('Container element:', container);
    console.log('Input element:', input);
    
    if(toggle && container && input) {
        console.log('✅ All chatbot elements found');
    } else {
        console.log('❌ Some chatbot elements missing');
    }
} catch(e) {
    console.error('JavaScript error:', e);
}
</script>";

echo "<br><a href='index.php'>← Kembali ke Halaman Utama</a>";
?>
