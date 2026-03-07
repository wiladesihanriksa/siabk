<?php
// Test chatbot responses untuk memastikan jawaban sudah benar
include 'koneksi.php';
include 'functions_app_settings.php';
include 'functions_chatbot_settings.php';

echo "<h1>🧪 Test Chatbot Responses</h1>";

// Test API endpoint dengan berbagai pertanyaan
$test_questions = [
    'Apa itu SISBK?',
    'Cara login ke SISBK',
    'Fitur dashboard SISBK',
    'Manajemen kasus siswa',
    'Laporan dan dokumentasi',
    'Troubleshooting teknis'
];

echo "<h2>📝 Test Questions & Answers:</h2>";

foreach($test_questions as $question) {
    echo "<div style='background: #f8f9fa; padding: 15px; margin: 10px 0; border-radius: 5px;'>";
    echo "<strong>❓ Pertanyaan:</strong> " . htmlspecialchars($question) . "<br><br>";
    
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
            echo "<strong>🤖 Jawaban:</strong><br>";
            echo "<div style='background: white; padding: 10px; border-radius: 3px; margin: 5px 0;'>";
            echo nl2br(htmlspecialchars($data['response']));
            echo "</div>";
            
            // Check if response contains SISBK
            if(strpos($data['response'], 'SISBK') !== false) {
                echo "<span style='color: green;'>✅ Jawaban mengandung 'SISBK'</span><br>";
            } else {
                echo "<span style='color: red;'>❌ Jawaban tidak mengandung 'SISBK'</span><br>";
            }
        } else {
            echo "<span style='color: red;'>❌ Error: Tidak ada response</span><br>";
        }
    } else {
        echo "<span style='color: red;'>❌ Error: API tidak merespon</span><br>";
    }
    
    echo "</div>";
}

echo "<h2>🎯 Checklist Perbaikan:</h2>";
echo "<ul>";
echo "<li>✅ Jawaban 'Apa itu SISBK?' - Harus menjelaskan SISBK</li>";
echo "<li>✅ Jawaban 'Cara login ke SISBK' - Harus ada langkah-langkah login</li>";
echo "<li>✅ Jawaban 'Fitur dashboard SISBK' - Harus menjelaskan menu dan fitur</li>";
echo "<li>✅ Jawaban 'Manajemen kasus siswa' - Harus ada cara input kasus</li>";
echo "<li>✅ Jawaban 'Laporan dan dokumentasi' - Harus menjelaskan jenis laporan</li>";
echo "<li>✅ Jawaban 'Troubleshooting teknis' - Harus ada solusi masalah</li>";
echo "</ul>";

echo "<br><a href='index.php'>← Kembali ke Halaman Utama</a>";
?>
