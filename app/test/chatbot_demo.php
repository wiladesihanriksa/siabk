<?php
// ePoint Chatbot Demo Page
// Halaman demo untuk testing chatbot
session_start();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ePoint Chatbot Demo</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 20px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
        }
        .demo-container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            border-radius: 20px;
            padding: 40px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.2);
        }
        .demo-header {
            text-align: center;
            margin-bottom: 40px;
        }
        .demo-header h1 {
            color: #333;
            margin-bottom: 10px;
        }
        .demo-header p {
            color: #666;
            font-size: 16px;
        }
        .demo-features {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 40px;
        }
        .feature-card {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 15px;
            border-left: 4px solid #667eea;
        }
        .feature-card h3 {
            color: #333;
            margin-bottom: 10px;
        }
        .feature-card p {
            color: #666;
            font-size: 14px;
            line-height: 1.5;
        }
        .demo-actions {
            text-align: center;
            margin-bottom: 40px;
        }
        .demo-btn {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            padding: 12px 24px;
            border-radius: 25px;
            cursor: pointer;
            font-size: 16px;
            margin: 0 10px;
            transition: transform 0.3s ease;
        }
        .demo-btn:hover {
            transform: translateY(-2px);
        }
        .demo-info {
            background: #e3f2fd;
            padding: 20px;
            border-radius: 15px;
            border-left: 4px solid #2196f3;
        }
        .demo-info h3 {
            color: #1976d2;
            margin-bottom: 10px;
        }
        .demo-info ul {
            color: #666;
            padding-left: 20px;
        }
        .demo-info li {
            margin-bottom: 5px;
        }
    </style>
</head>
<body>
    <div class="demo-container">
        <div class="demo-header">
            <h1>🤖 ePoint Chatbot Demo</h1>
            <p>Chatbot modern untuk sistem manajemen sekolah ePoint</p>
        </div>
        
        <div class="demo-features">
            <div class="feature-card">
                <h3>🎯 Konsultasi Real-time</h3>
                <p>Dapatkan bantuan langsung tentang fitur ePoint, layanan BK, dan manajemen kasus siswa secara real-time.</p>
            </div>
            
            <div class="feature-card">
                <h3>📱 Responsif & Modern</h3>
                <p>Design modern yang responsif di semua device - desktop, tablet, dan mobile dengan animasi smooth.</p>
            </div>
            
            <div class="feature-card">
                <h3>🔒 Aman & Lokal</h3>
                <p>AI model lokal untuk keamanan data sekolah, tidak ada data yang dikirim ke server eksternal.</p>
            </div>
            
            <div class="feature-card">
                <h3>⚡ Cepat & Efisien</h3>
                <p>Response cepat dengan sistem AI yang dioptimalkan untuk pertanyaan tentang ePoint dan layanan BK.</p>
            </div>
        </div>
        
        <div class="demo-actions">
            <button class="demo-btn" onclick="testChatbot()">🧪 Test Chatbot</button>
            <button class="demo-btn" onclick="showNotification()">🔔 Show Notification</button>
            <button class="demo-btn" onclick="window.open('index.php', '_blank')">🏠 Ke Halaman Utama</button>
        </div>
        
        <div class="demo-info">
            <h3>📋 Fitur Chatbot ePoint:</h3>
            <ul>
                <li><strong>Informasi Umum:</strong> Penjelasan tentang ePoint dan fitur-fiturnya</li>
                <li><strong>Layanan BK:</strong> Panduan penggunaan layanan Bimbingan Konseling</li>
                <li><strong>Kasus Siswa:</strong> Cara mengelola dan melaporkan kasus siswa</li>
                <li><strong>Kunjungan Rumah:</strong> Panduan kunjungan rumah dan dokumentasi</li>
                <li><strong>Laporan:</strong> Cara membuat dan mengakses berbagai laporan</li>
                <li><strong>Troubleshooting:</strong> Solusi masalah teknis aplikasi</li>
            </ul>
        </div>
    </div>

    <!-- Include Chatbot Widget -->
    <?php include 'chatbot_widget.php'; ?>

    <script>
        function testChatbot() {
            // Open chatbot and send test message
            const chatbot = new ePointChatbot();
            chatbot.openChat();
            
            setTimeout(() => {
                const input = document.getElementById('chatbot-input');
                input.value = 'Apa itu ePoint?';
                chatbot.sendMessage();
            }, 500);
        }
        
        function showNotification() {
            const badge = document.getElementById('notification-badge');
            if (badge) {
                badge.style.display = 'flex';
                badge.textContent = '1';
            }
        }
        
        // Auto-show notification after 2 seconds
        setTimeout(() => {
            showNotification();
        }, 2000);
    </script>
</body>
</html>
