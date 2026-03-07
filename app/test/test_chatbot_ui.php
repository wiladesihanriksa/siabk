<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Chatbot UI - SISBK</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background: #f5f5f5;
        }
        .test-info {
            background: white;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .test-item {
            margin: 10px 0;
            padding: 10px;
            background: #f8f9fa;
            border-radius: 5px;
        }
        .success { color: #28a745; }
        .warning { color: #ffc107; }
        .error { color: #dc3545; }
    </style>
</head>
<body>
    <div class="test-info">
        <h1>🧪 Test Chatbot UI Improvements</h1>
        <p>Halaman ini untuk test perbaikan tampilan chatbot.</p>
        
        <div class="test-item">
            <h3>✅ Perbaikan yang Dilakukan:</h3>
            <ul>
                <li><strong>Quick Actions:</strong> Dibatasi hanya 4 tombol</li>
                <li><strong>Ukuran Tombol:</strong> Lebih kecil dan rapi</li>
                <li><strong>Max Height:</strong> Quick actions tidak menutupi chat</li>
                <li><strong>Responsive:</strong> Tampilan mobile yang lebih baik</li>
                <li><strong>Jawaban FAQ:</strong> Diperbaiki untuk SISBK</li>
            </ul>
        </div>
        
        <div class="test-item">
            <h3>🎯 Test Cases:</h3>
            <ol>
                <li>Klik tombol chatbot di kanan bawah</li>
                <li>Lihat apakah quick actions hanya 4 tombol</li>
                <li>Test apakah quick actions tidak menutupi area chat</li>
                <li>Klik "Apa itu SISBK?" - lihat jawaban</li>
                <li>Klik "Cara login ke SISBK?" - lihat jawaban</li>
                <li>Test di mobile (resize browser)</li>
            </ol>
        </div>
    </div>
    
    <?php
    // Include chatbot widget
    include 'chatbot_widget.php';
    ?>
    
    <script>
    // Test script
    document.addEventListener('DOMContentLoaded', function() {
        console.log('🧪 Testing Chatbot UI...');
        
        // Test quick actions count
        setTimeout(() => {
            const quickBtns = document.querySelectorAll('.quick-btn');
            console.log('Quick Actions Count:', quickBtns.length);
            
            if (quickBtns.length <= 4) {
                console.log('✅ Quick Actions limited to 4 buttons');
            } else {
                console.log('❌ Too many quick actions');
            }
            
            // Test quick actions height
            const quickActions = document.querySelector('.quick-actions');
            if (quickActions) {
                const maxHeight = window.getComputedStyle(quickActions).maxHeight;
                console.log('Quick Actions Max Height:', maxHeight);
                
                if (maxHeight === '80px') {
                    console.log('✅ Max height set correctly');
                } else {
                    console.log('❌ Max height not set');
                }
            }
        }, 1000);
    });
    </script>
</body>
</html>
