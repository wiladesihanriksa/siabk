<?php
// Test file untuk memastikan semua fungsi berfungsi dengan baik
session_start();
include 'koneksi.php';
include 'functions_app_settings.php';
include 'functions_chatbot_settings.php';

echo "<h1>🧪 Test Functions ePoint</h1>";

// Test 1: App Settings
echo "<h2>1. Test App Settings</h2>";
try {
    $app_settings = getAppSettings($koneksi);
    echo "✅ getAppSettings() berfungsi<br>";
    echo "Jumlah pengaturan: " . count($app_settings) . "<br>";
    
    $app_name = getSetting($app_settings, 'app_name', 'ePoint');
    echo "Nama aplikasi: " . $app_name . "<br>";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "<br>";
}

// Test 2: Chatbot Settings
echo "<h2>2. Test Chatbot Settings</h2>";
try {
    $chatbot_settings = getChatbotSettings($koneksi);
    echo "✅ getChatbotSettings() berfungsi<br>";
    echo "Jumlah pengaturan chatbot: " . count($chatbot_settings) . "<br>";
    
    $chatbot_name = getChatbotName($chatbot_settings);
    echo "Nama chatbot: " . $chatbot_name . "<br>";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "<br>";
}

// Test 3: Chatbot Config
echo "<h2>3. Test Chatbot Config</h2>";
try {
    $chatbot_config = getChatbotConfig($koneksi);
    echo "✅ getChatbotConfig() berfungsi<br>";
    echo "Chatbot enabled: " . $chatbot_config['enabled'] . "<br>";
    echo "Chatbot name: " . $chatbot_config['name'] . "<br>";
    echo "Chatbot status: " . $chatbot_config['status'] . "<br>";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "<br>";
}

// Test 4: Function Exists Check
echo "<h2>4. Test Function Exists</h2>";
$functions_to_check = [
    'getAppSettings',
    'getSetting', 
    'getAppLogo',
    'getAppFavicon',
    'getLoginLogo',
    'getChatbotSettings',
    'getChatbotSetting',
    'getChatbotName',
    'getChatbotConfig'
];

foreach($functions_to_check as $func) {
    if(function_exists($func)) {
        echo "✅ $func() exists<br>";
    } else {
        echo "❌ $func() not found<br>";
    }
}

echo "<h2>5. Test Database Connection</h2>";
if($koneksi) {
    echo "✅ Database connection OK<br>";
    
    // Test query
    $result = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM app_settings");
    if($result) {
        $row = mysqli_fetch_assoc($result);
        echo "Total settings: " . $row['total'] . "<br>";
    }
} else {
    echo "❌ Database connection failed<br>";
}

echo "<br><a href='index.php'>← Kembali ke Halaman Utama</a>";
?>
