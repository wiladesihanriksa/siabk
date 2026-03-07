<?php
// Test Ollama Connection for ePoint Chatbot
require_once 'ollama_ai.php';

$ollama = new OllamaAI();
$hybrid = new HybridAI();

echo "<h1>ePoint Chatbot - Ollama Test</h1>";

// Test 1: Koneksi Ollama
echo "<h2>1. Test Koneksi Ollama</h2>";
if ($ollama->testConnection()) {
    echo "✅ <strong>Ollama berjalan dengan baik!</strong><br>";
    echo "URL: " . $ollama->ollama_url . "<br>";
} else {
    echo "❌ <strong>Ollama tidak dapat diakses</strong><br>";
    echo "Pastikan Ollama sudah diinstall dan berjalan di port 11434<br>";
}

// Test 2: Available Models
echo "<h2>2. Model yang Tersedia</h2>";
$models = $ollama->getAvailableModels();
if (!empty($models)) {
    echo "✅ <strong>Model tersedia:</strong><br>";
    foreach ($models as $model) {
        echo "- " . $model['name'] . " (Size: " . round($model['size']/1024/1024/1024, 2) . " GB)<br>";
    }
} else {
    echo "❌ <strong>Tidak ada model yang tersedia</strong><br>";
    echo "Install model dengan: <code>ollama pull llama2:7b</code><br>";
}

// Test 3: AI Response
echo "<h2>3. Test AI Response</h2>";
$test_message = "Apa itu ePoint?";
echo "<strong>Pertanyaan:</strong> " . $test_message . "<br><br>";

try {
    $response = $hybrid->getResponse($test_message);
    echo "<strong>Jawaban:</strong><br>";
    echo "<div style='background: #f0f0f0; padding: 10px; border-radius: 5px; margin: 10px 0;'>";
    echo nl2br(htmlspecialchars($response));
    echo "</div>";
} catch (Exception $e) {
    echo "❌ <strong>Error:</strong> " . $e->getMessage() . "<br>";
}

// Test 4: Performance Test
echo "<h2>4. Performance Test</h2>";
$start_time = microtime(true);
$test_questions = [
    "Apa itu ePoint?",
    "Cara menggunakan layanan BK",
    "Manajemen kasus siswa",
    "Laporan kunjungan rumah"
];

foreach ($test_questions as $question) {
    $start = microtime(true);
    $response = $hybrid->getResponse($question);
    $end = microtime(true);
    $response_time = round(($end - $start) * 1000, 2);
    
    echo "<strong>Q:</strong> " . $question . "<br>";
    echo "<strong>Response Time:</strong> " . $response_time . "ms<br>";
    echo "<strong>Response Length:</strong> " . strlen($response) . " characters<br><br>";
}

$total_time = round((microtime(true) - $start_time) * 1000, 2);
echo "<strong>Total Test Time:</strong> " . $total_time . "ms<br>";

// Test 5: System Info
echo "<h2>5. System Information</h2>";
echo "<strong>PHP Version:</strong> " . phpversion() . "<br>";
echo "<strong>Server:</strong> " . $_SERVER['SERVER_SOFTWARE'] . "<br>";
echo "<strong>Memory Limit:</strong> " . ini_get('memory_limit') . "<br>";
echo "<strong>Max Execution Time:</strong> " . ini_get('max_execution_time') . " seconds<br>";

// Test 6: Recommendations
echo "<h2>6. Recommendations</h2>";
if (!$ollama->testConnection()) {
    echo "⚠️ <strong>Ollama tidak tersedia</strong><br>";
    echo "Chatbot akan menggunakan rule-based AI sebagai fallback<br>";
    echo "Untuk performa optimal, install Ollama:<br>";
    echo "<code>curl -fsSL https://ollama.ai/install.sh | sh</code><br>";
    echo "<code>ollama pull llama2:7b</code><br>";
} else {
    echo "✅ <strong>Ollama berjalan dengan baik</strong><br>";
    echo "Chatbot akan menggunakan hybrid AI (Ollama + Rule-based)<br>";
}

echo "<br><a href='../chatbot_demo.php'>← Kembali ke Demo</a>";
?>
