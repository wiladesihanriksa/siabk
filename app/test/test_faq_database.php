<?php
// Test FAQ dari database
include 'koneksi.php';
include 'functions_app_settings.php';
include 'functions_chatbot_settings.php';

echo "<h1>🧪 Test FAQ dari Database</h1>";

// Test chatbot config
$chatbot_config = getChatbotConfig($koneksi);

echo "<h2>📊 Chatbot Config:</h2>";
echo "Name: " . $chatbot_config['name'] . "<br>";
echo "Enabled: " . $chatbot_config['enabled'] . "<br>";
echo "Quick Actions: " . count($chatbot_config['quick_actions']) . "<br>";
echo "FAQ: " . count($chatbot_config['faq']) . "<br>";

echo "<h2>❓ FAQ dari Database:</h2>";
foreach($chatbot_config['faq'] as $index => $faq) {
    echo "<div style='background: #f8f9fa; padding: 15px; margin: 10px 0; border-radius: 5px;'>";
    echo "<strong>FAQ " . ($index + 1) . ":</strong><br>";
    echo "<strong>Q:</strong> " . htmlspecialchars($faq['question']) . "<br>";
    echo "<strong>A:</strong> " . htmlspecialchars($faq['answer']) . "<br>";
    echo "</div>";
}

echo "<h2>⚡ Quick Actions dari Database:</h2>";
foreach($chatbot_config['quick_actions'] as $index => $action) {
    echo ($index + 1) . ". " . htmlspecialchars($action) . "<br>";
}

echo "<h2>🧪 Test API dengan FAQ:</h2>";

// Test dengan pertanyaan FAQ
$test_questions = [
    'Apa itu SISBK?',
    'Cara login ke SISBK',
    'Fitur dashboard SISBK'
];

foreach($test_questions as $question) {
    echo "<div style='background: #e9ecef; padding: 10px; margin: 5px 0; border-radius: 3px;'>";
    echo "<strong>Test:</strong> " . htmlspecialchars($question) . "<br>";
    
    // Test API
    $postdata = json_encode(['message' => $question]);
    $opts = [
        'http' => [
            'method' => 'POST',
            'header' => 'Content-Type: application/json',
            'content' => $postdata
        ]
    ];
    
    $context = stream_context_create($opts);
    $result = file_get_contents('http://localhost:8001/chatbot/chatbot_api.php', false, $context);
    
    if($result !== false) {
        $data = json_decode($result, true);
        if(isset($data['response'])) {
            echo "<strong>Response:</strong> " . htmlspecialchars($data['response']) . "<br>";
            
            // Check if response matches FAQ
            $found_in_faq = false;
            foreach($chatbot_config['faq'] as $faq) {
                if(strpos($data['response'], $faq['answer']) !== false) {
                    $found_in_faq = true;
                    break;
                }
            }
            
            if($found_in_faq) {
                echo "<span style='color: green;'>✅ Response dari FAQ database</span><br>";
            } else {
                echo "<span style='color: orange;'>⚠️ Response dari rule-based system</span><br>";
            }
        } else {
            echo "<span style='color: red;'>❌ No response</span><br>";
        }
    } else {
        echo "<span style='color: red;'>❌ API error</span><br>";
    }
    
    echo "</div>";
}

echo "<br><a href='index.php'>← Kembali ke Halaman Utama</a>";
?>
