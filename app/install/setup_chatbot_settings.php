<?php
// Setup chatbot settings in database
include 'koneksi.php';

echo "<h1>🤖 Setup Chatbot Settings</h1>";

// SQL untuk menambahkan pengaturan chatbot
$sql_queries = [
    // Pengaturan Dasar Chatbot
    "INSERT IGNORE INTO app_settings (setting_key, setting_value) VALUES ('chatbot_enabled', '1')",
    "INSERT IGNORE INTO app_settings (setting_key, setting_value) VALUES ('chatbot_name', 'ePoint Assistant')",
    "INSERT IGNORE INTO app_settings (setting_key, setting_value) VALUES ('chatbot_description', 'Saya siap membantu Anda dengan informasi tentang aplikasi ePoint dan layanan konseling.')",
    "INSERT IGNORE INTO app_settings (setting_key, setting_value) VALUES ('chatbot_status', 'Online')",
    "INSERT IGNORE INTO app_settings (setting_key, setting_value) VALUES ('chatbot_avatar', 'fas fa-robot')",
    "INSERT IGNORE INTO app_settings (setting_key, setting_value) VALUES ('chatbot_theme', 'modern')",
    "INSERT IGNORE INTO app_settings (setting_key, setting_value) VALUES ('chatbot_position', 'bottom-right')",
    "INSERT IGNORE INTO app_settings (setting_key, setting_value) VALUES ('chatbot_welcome_message', 'Halo! Saya ePoint Assistant. Saya siap membantu Anda dengan informasi tentang aplikasi ePoint dan layanan konseling. Ada yang bisa saya bantu?')",
    
    // Pengaturan Tampilan
    "INSERT IGNORE INTO app_settings (setting_key, setting_value) VALUES ('chatbot_show_notification', '1')",
    "INSERT IGNORE INTO app_settings (setting_key, setting_value) VALUES ('chatbot_auto_open', '0')",
    
    // Quick Actions (JSON format)
    "INSERT IGNORE INTO app_settings (setting_key, setting_value) VALUES ('chatbot_quick_actions', '[\"Apa itu ePoint?\",\"Cara login ke ePoint\",\"Fitur dashboard ePoint\",\"Manajemen kasus siswa\",\"Laporan dan dokumentasi\",\"Troubleshooting teknis\"]')",
    
    // FAQ (JSON format)
    "INSERT IGNORE INTO app_settings (setting_key, setting_value) VALUES ('chatbot_faq', '[{\"question\":\"Apa itu ePoint?\",\"answer\":\"ePoint adalah sistem manajemen sekolah yang membantu mengelola point siswa, layanan BK, dan laporan akademik.\"},{\"question\":\"Bagaimana cara login ke ePoint?\",\"answer\":\"Gunakan username dan password yang diberikan oleh administrator. Pilih jenis login sesuai dengan peran Anda (Siswa, Admin, atau Guru BK).\"},{\"question\":\"Apa saja fitur utama ePoint?\",\"answer\":\"Fitur utama meliputi: Manajemen Point, Layanan BK, Kasus Siswa, Kunjungan Rumah, Laporan Real-time, dan Mobile Friendly.\"}]')",
    
    // Informasi Aplikasi untuk Chatbot
    "INSERT IGNORE INTO app_settings (setting_key, setting_value) VALUES ('chatbot_app_features', 'Manajemen Point, Layanan BK, Kasus Siswa, Kunjungan Rumah, Laporan Real-time, Mobile Friendly')",
    
    // Pengaturan Ollama AI
    "INSERT IGNORE INTO app_settings (setting_key, setting_value) VALUES ('chatbot_ollama_enabled', '0')",
    "INSERT IGNORE INTO app_settings (setting_key, setting_value) VALUES ('chatbot_ollama_url', 'http://localhost:11434')",
    "INSERT IGNORE INTO app_settings (setting_key, setting_value) VALUES ('chatbot_ollama_model', 'llama2:7b')"
];

$success_count = 0;
$error_count = 0;

foreach($sql_queries as $sql) {
    if(mysqli_query($koneksi, $sql)) {
        $success_count++;
        echo "✅ " . substr($sql, 0, 50) . "...<br>";
    } else {
        $error_count++;
        echo "❌ Error: " . mysqli_error($koneksi) . "<br>";
    }
}

echo "<br><h2>📊 Hasil Setup:</h2>";
echo "✅ Berhasil: $success_count<br>";
echo "❌ Error: $error_count<br>";

if($error_count == 0) {
    echo "<br><div style='background: #d4edda; padding: 15px; border-radius: 5px; color: #155724;'>";
    echo "🎉 <strong>Setup berhasil!</strong> Pengaturan chatbot telah ditambahkan ke database.";
    echo "</div>";
    
    echo "<br><h3>🚀 Langkah Selanjutnya:</h3>";
    echo "1. <a href='admin/pengaturan_aplikasi.php'>Buka Pengaturan Aplikasi</a><br>";
    echo "2. Klik tab <strong>'Pengaturan Chatbot'</strong><br>";
    echo "3. Sesuaikan pengaturan sesuai kebutuhan<br>";
    echo "4. Klik <strong>'Simpan Pengaturan'</strong><br>";
    echo "5. <a href='index.php'>Test chatbot di halaman utama</a><br>";
} else {
    echo "<br><div style='background: #f8d7da; padding: 15px; border-radius: 5px; color: #721c24;'>";
    echo "⚠️ <strong>Ada error!</strong> Silakan cek koneksi database dan jalankan ulang.";
    echo "</div>";
}

echo "<br><a href='index.php'>← Kembali ke Halaman Utama</a>";
?>
