<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Duplicate Fix - SISBK</title>
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
        <h1>🧪 Test Duplicate Fix</h1>
        <p>Halaman ini untuk test perbaikan duplikasi quick button.</p>
        
        <div class="test-item">
            <h3>✅ Perbaikan yang Dilakukan:</h3>
            <ul>
                <li><strong>Event Listener:</strong> Dihapus duplikasi event listener</li>
                <li><strong>Prevent Duplicate:</strong> Cek pesan terakhir untuk mencegah duplikasi</li>
                <li><strong>Stop Propagation:</strong> Mencegah event bubbling</li>
                <li><strong>Clean Binding:</strong> Event listener dibersihkan sebelum ditambah</li>
            </ul>
        </div>
        
        <div class="test-item">
            <h3>🎯 Test Cases:</h3>
            <ol>
                <li>Klik tombol chatbot di kanan bawah</li>
                <li>Klik quick button "Apa itu SISBK?"</li>
                <li>Lihat apakah hanya muncul 1 kali</li>
                <li>Klik quick button lain</li>
                <li>Test apakah tidak ada duplikasi</li>
            </ol>
        </div>
        
        <div class="test-item">
            <h3>🔍 Debug Info:</h3>
            <p>Buka browser console (F12) untuk melihat log debug.</p>
        </div>
    </div>
    
    <?php
    // Include chatbot widget
    include 'chatbot_widget.php';
    ?>
    
    <script>
    // Debug script
    console.log('🧪 Testing Duplicate Fix...');
    
    // Monitor quick button clicks
    document.addEventListener('DOMContentLoaded', function() {
        let clickCount = 0;
        
        // Monitor all quick button clicks
        document.addEventListener('click', function(e) {
            if (e.target.classList.contains('quick-btn')) {
                clickCount++;
                console.log('Quick button clicked:', clickCount, 'times');
                console.log('Message:', e.target.getAttribute('data-message'));
            }
        });
        
        // Monitor message additions
        const originalAddMessage = window.ePointChatbot?.prototype?.addMessage;
        if (originalAddMessage) {
            console.log('Monitoring message additions...');
        }
    });
    </script>
</body>
</html>
