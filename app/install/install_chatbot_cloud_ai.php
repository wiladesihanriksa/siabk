<?php
// Script untuk install pengaturan Cloud AI chatbot
// Jalankan: http://localhost:8001/install_chatbot_cloud_ai.php

include 'koneksi.php';

echo "<h2>🚀 Installing Chatbot Cloud AI Settings</h2>";

// SQL untuk menambahkan pengaturan Cloud AI
$sql_queries = [
    "INSERT INTO app_settings (setting_key, setting_value, description) VALUES
    ('chatbot_ai_provider', 'gemini', 'AI Provider yang digunakan (gemini, openai, huggingface, ollama)')
    ON DUPLICATE KEY UPDATE 
    setting_value = VALUES(setting_value),
    description = VALUES(description);",
    
    "INSERT INTO app_settings (setting_key, setting_value, description) VALUES
    ('chatbot_api_key', '', 'API Key untuk cloud AI provider')
    ON DUPLICATE KEY UPDATE 
    setting_value = VALUES(setting_value),
    description = VALUES(description);",
    
    "INSERT INTO app_settings (setting_key, setting_value, description) VALUES
    ('chatbot_cloud_enabled', '1', 'Status aktifasi cloud AI (1=aktif, 0=nonaktif)')
    ON DUPLICATE KEY UPDATE 
    setting_value = VALUES(setting_value),
    description = VALUES(description);",
    
    "INSERT INTO app_settings (setting_key, setting_value, description) VALUES
    ('chatbot_fallback_enabled', '1', 'Status fallback ke rule-based (1=aktif, 0=nonaktif)')
    ON DUPLICATE KEY UPDATE 
    setting_value = VALUES(setting_value),
    description = VALUES(description);",
    
    "INSERT IGNORE INTO app_settings (setting_key, setting_value, description) VALUES
    ('chatbot_name', 'SISBK Assistant', 'Nama chatbot yang ditampilkan'),
    ('chatbot_description', 'Saya siap membantu Anda dengan informasi tentang aplikasi SISBK dan layanan konseling.', 'Deskripsi chatbot'),
    ('chatbot_status', 'Online', 'Status chatbot (Online, Busy, Away)'),
    ('chatbot_avatar', 'fas fa-robot', 'Icon avatar chatbot'),
    ('chatbot_theme', 'modern', 'Tema tampilan chatbot'),
    ('chatbot_position', 'bottom-right', 'Posisi widget chatbot'),
    ('chatbot_welcome_message', 'Halo! Saya siap membantu Anda dengan informasi tentang aplikasi SISBK dan layanan konseling. Ada yang bisa saya bantu?', 'Pesan selamat datang'),
    ('chatbot_show_notification', '1', 'Tampilkan notifikasi badge (1=ya, 0=tidak)'),
    ('chatbot_auto_open', '0', 'Buka otomatis untuk user baru (1=ya, 0=tidak)'),
    ('chatbot_quick_actions', '[\"Apa itu SISBK?\", \"Cara login ke SISBK\", \"Fitur dashboard SISBK\", \"Manajemen kasus siswa\", \"Laporan dan dokumentasi\", \"Troubleshooting teknis\"]', 'Quick actions untuk chatbot'),
    ('chatbot_faq', '[{\"question\":\"Apa itu SISBK?\",\"answer\":\"SISBK adalah sistem manajemen sekolah yang membantu mengelola point siswa, layanan BK, dan laporan akademik.\"},{\"question\":\"Bagaimana cara login ke SISBK?\",\"answer\":\"Gunakan username dan password yang diberikan oleh administrator. Pilih jenis login sesuai dengan peran Anda (Siswa, Admin, atau Guru BK).\"},{\"question\":\"Apa saja fitur utama SISBK?\",\"answer\":\"Fitur utama meliputi: Manajemen Point, Layanan BK, Kasus Siswa, Kunjungan Rumah, Laporan Real-time, dan Mobile Friendly.\"}]', 'FAQ untuk chatbot'),
    ('chatbot_ollama_enabled', '0', 'Status aktifasi Ollama AI (1=aktif, 0=nonaktif)'),
    ('chatbot_ollama_url', 'http://localhost:11434', 'URL server Ollama'),
    ('chatbot_ollama_model', 'llama2:7b', 'Model AI Ollama yang digunakan'),
    ('chatbot_enabled', '1', 'Status aktifasi chatbot (1=aktif, 0=nonaktif)');"
];

$success_count = 0;
$error_count = 0;

echo "<h3>📋 Executing SQL Queries...</h3>";

foreach($sql_queries as $index => $sql) {
    echo "<p><strong>Query " . ($index + 1) . ":</strong> ";
    
    if(mysqli_query($koneksi, $sql)) {
        echo "<span style='color: green;'>✅ Success</span></p>";
        $success_count++;
    } else {
        echo "<span style='color: red;'>❌ Error: " . mysqli_error($koneksi) . "</span></p>";
        $error_count++;
    }
}

echo "<hr>";
echo "<h3>📊 Installation Summary</h3>";
echo "<p><strong>Success:</strong> <span style='color: green;'>$success_count queries</span></p>";
echo "<p><strong>Errors:</strong> <span style='color: red;'>$error_count queries</span></p>";

if($error_count == 0) {
    echo "<p style='color: green; font-weight: bold;'>🎉 Installation completed successfully!</p>";
} else {
    echo "<p style='color: orange; font-weight: bold;'>⚠️ Installation completed with some errors.</p>";
}

// Tampilkan pengaturan yang sudah diinstall
echo "<hr>";
echo "<h3>🔍 Current Chatbot Settings</h3>";

$query = "SELECT setting_key, setting_value, description FROM app_settings WHERE setting_key LIKE 'chatbot_%' ORDER BY setting_key";
$result = mysqli_query($koneksi, $query);

if($result && mysqli_num_rows($result) > 0) {
    echo "<table border='1' cellpadding='5' cellspacing='0' style='width: 100%; border-collapse: collapse;'>";
    echo "<tr style='background: #f8f9fa;'>";
    echo "<th>Setting Key</th><th>Value</th><th>Description</th>";
    echo "</tr>";
    
    while($row = mysqli_fetch_assoc($result)) {
        echo "<tr>";
        echo "<td><strong>" . htmlspecialchars($row['setting_key']) . "</strong></td>";
        echo "<td>" . htmlspecialchars($row['setting_value']) . "</td>";
        echo "<td>" . htmlspecialchars($row['setting_description']) . "</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p style='color: red;'>No chatbot settings found.</p>";
}

echo "<hr>";
echo "<h3>🎯 Next Steps</h3>";
echo "<ol>";
echo "<li><strong>Login Admin:</strong> <a href='admin/pengaturan_aplikasi.php' target='_blank'>Pengaturan Aplikasi</a></li>";
echo "<li><strong>Tab Chatbot:</strong> Scroll ke bagian 'Cloud AI Settings'</li>";
echo "<li><strong>Setup API Key:</strong> Dapatkan di <a href='https://makersuite.google.com/app/apikey' target='_blank'>Google AI Studio</a></li>";
echo "<li><strong>Test Chatbot:</strong> <a href='test_chatbot_cloud_integration.php' target='_blank'>Test Integration</a></li>";
echo "</ol>";

echo "<p><strong>✅ Installation completed!</strong> You can now configure chatbot settings in admin panel.</p>";
?>
