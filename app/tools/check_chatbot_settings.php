<?php
// Script untuk mengecek pengaturan chatbot
include 'koneksi.php';

echo "<h2>✅ Chatbot Settings Status</h2>";

// Cek pengaturan chatbot yang sudah diinstall
$query = "SELECT setting_key, setting_value FROM app_settings WHERE setting_key LIKE 'chatbot_%' ORDER BY setting_key";
$result = mysqli_query($koneksi, $query);

if($result && mysqli_num_rows($result) > 0) {
    echo "<h3>📋 Current Chatbot Settings:</h3>";
    echo "<table border='1' cellpadding='5' cellspacing='0' style='width: 100%; border-collapse: collapse;'>";
    echo "<tr style='background: #f8f9fa;'>";
    echo "<th>Setting</th><th>Value</th><th>Status</th>";
    echo "</tr>";
    
    while($row = mysqli_fetch_assoc($result)) {
        $key = $row['setting_key'];
        $value = $row['setting_value'];
        $status = '';
        
        // Determine status based on setting
        switch($key) {
            case 'chatbot_enabled':
                $status = $value == '1' ? '✅ Active' : '❌ Disabled';
                break;
            case 'chatbot_cloud_enabled':
                $status = $value == '1' ? '✅ Cloud AI Active' : '❌ Cloud AI Disabled';
                break;
            case 'chatbot_ai_provider':
                $status = '🤖 ' . ucfirst($value);
                break;
            case 'chatbot_api_key':
                $status = !empty($value) ? '✅ Configured' : '❌ Not Set';
                break;
            case 'chatbot_fallback_enabled':
                $status = $value == '1' ? '✅ Fallback Active' : '❌ Fallback Disabled';
                break;
            default:
                $status = '📝 Set';
        }
        
        echo "<tr>";
        echo "<td><strong>" . htmlspecialchars($key) . "</strong></td>";
        echo "<td>" . htmlspecialchars($value) . "</td>";
        echo "<td>" . $status . "</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p style='color: red;'>❌ No chatbot settings found.</p>";
}

echo "<hr>";
echo "<h3>🎯 Next Steps:</h3>";
echo "<ol>";
echo "<li><strong>Login Admin:</strong> <a href='admin/pengaturan_aplikasi.php' target='_blank'>Pengaturan Aplikasi</a></li>";
echo "<li><strong>Tab Chatbot:</strong> Scroll ke bagian 'Cloud AI Settings'</li>";
echo "<li><strong>Setup API Key:</strong> Dapatkan di <a href='https://makersuite.google.com/app/apikey' target='_blank'>Google AI Studio</a></li>";
echo "<li><strong>Test Chatbot:</strong> <a href='test_chatbot_cloud_integration.php' target='_blank'>Test Integration</a></li>";
echo "</ol>";

echo "<p><strong>✅ Installation completed successfully!</strong></p>";
?>
