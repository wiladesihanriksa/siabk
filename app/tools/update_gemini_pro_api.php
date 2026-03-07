<?php
// Script untuk update Gemini Pro API key
include 'koneksi.php';

echo "<h2>🚀 Update Gemini Pro API Key</h2>";

// Ganti dengan API key Gemini Pro Anda
$new_api_key = 'YOUR_GEMINI_PRO_API_KEY_HERE'; // Ganti dengan API key Anda

if($new_api_key === 'YOUR_GEMINI_PRO_API_KEY_HERE') {
    echo "<p style='color: red;'>❌ Silakan ganti 'YOUR_GEMINI_PRO_API_KEY_HERE' dengan API key Gemini Pro Anda!</p>";
    echo "<p><strong>Contoh:</strong> \$new_api_key = 'AIzaSyC...';</p>";
    exit;
}

// Update API key di database
$update_query = "UPDATE app_settings SET setting_value = ? WHERE setting_key = 'chatbot_api_key'";
$stmt = mysqli_prepare($koneksi, $update_query);
mysqli_stmt_bind_param($stmt, "s", $new_api_key);

if(mysqli_stmt_execute($stmt)) {
    echo "<p style='color: green;'>✅ API Key berhasil diupdate!</p>";
    echo "<p><strong>New API Key:</strong> " . substr($new_api_key, 0, 20) . "...</p>";
} else {
    echo "<p style='color: red;'>❌ Error updating API key: " . mysqli_error($koneksi) . "</p>";
}

// Test API key
echo "<h3>🧪 Testing Gemini Pro API</h3>";

try {
    $url = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-pro:generateContent?key=' . $new_api_key;
    
    $data = [
        'contents' => [
            [
                'parts' => [
                    ['text' => 'Hello, this is a test for Gemini Pro. Please respond briefly.']
                ]
            ]
        ],
        'generationConfig' => [
            'temperature' => 0.7,
            'maxOutputTokens' => 100
        ]
    ];
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    
    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    if($http_code == 200) {
        $json = json_decode($response, true);
        if($json && isset($json['candidates'][0]['content']['parts'][0]['text'])) {
            $text = $json['candidates'][0]['content']['parts'][0]['text'];
            echo "<p style='color: green;'>✅ Gemini Pro API working!</p>";
            echo "<p><strong>Response:</strong> " . htmlspecialchars($text) . "</p>";
        } else {
            echo "<p style='color: red;'>❌ Invalid response format</p>";
            echo "<pre>" . htmlspecialchars($response) . "</pre>";
        }
    } else {
        echo "<p style='color: red;'>❌ HTTP Error: $http_code</p>";
        echo "<pre>" . htmlspecialchars($response) . "</pre>";
    }
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error: " . htmlspecialchars($e->getMessage()) . "</p>";
}

echo "<hr>";
echo "<h3>🎯 Next Steps:</h3>";
echo "<ol>";
echo "<li><strong>Test Chatbot:</strong> <a href='test_chatbot_final.php' target='_blank'>Test Final</a></li>";
echo "<li><strong>Test di Halaman Utama:</strong> <a href='index.php' target='_blank'>Halaman Utama</a></li>";
echo "<li><strong>Monitor Performance:</strong> Gemini Pro lebih powerful dari Flash</li>";
echo "</ol>";

echo "<p><strong>✅ Gemini Pro API setup completed!</strong></p>";
?>
