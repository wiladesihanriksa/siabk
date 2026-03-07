<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Console Errors - SISBK</title>
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
        <h1>🧪 Test Console Errors Fix</h1>
        <p>Halaman ini untuk test perbaikan console errors.</p>
        
        <div class="test-item">
            <h3>✅ Perbaikan yang Dilakukan:</h3>
            <ul>
                <li><strong>Font Awesome:</strong> Diubah dari script ke CSS link</li>
                <li><strong>Favicon:</strong> Ditambahkan favicon.ico default</li>
                <li><strong>Duplicate CSS:</strong> Dihapus duplikasi Font Awesome</li>
                <li><strong>MIME Type:</strong> CSS sekarang di-load sebagai stylesheet</li>
            </ul>
        </div>
        
        <div class="test-item">
            <h3>🎯 Test Cases:</h3>
            <ol>
                <li>Buka browser console (F12)</li>
                <li>Refresh halaman</li>
                <li>Lihat apakah error Font Awesome hilang</li>
                <li>Lihat apakah error favicon hilang</li>
                <li>Test chatbot berfungsi normal</li>
            </ol>
        </div>
        
        <div class="test-item">
            <h3>🔍 Console Check:</h3>
            <p>Buka browser console (F12) dan lihat apakah error berikut sudah hilang:</p>
            <ul>
                <li>❌ <code>Refused to execute script from 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css'</code></li>
                <li>❌ <code>Failed to load resource: the server responded with a status of 404 (Not Found)</code></li>
            </ul>
        </div>
    </div>
    
    <?php
    // Include chatbot widget
    include 'chatbot_widget.php';
    ?>
    
    <script>
    // Debug script
    console.log('🧪 Testing Console Errors Fix...');
    
    // Check if Font Awesome is loaded
    if (typeof FontAwesome !== 'undefined') {
        console.log('✅ Font Awesome loaded correctly');
    } else {
        console.log('⚠️ Font Awesome not detected');
    }
    
    // Check for any remaining errors
    window.addEventListener('error', function(e) {
        console.log('❌ Error detected:', e.message);
    });
    
    // Monitor resource loading
    const observer = new PerformanceObserver((list) => {
        for (const entry of list.getEntries()) {
            if (entry.name.includes('font-awesome') || entry.name.includes('favicon')) {
                console.log('📊 Resource loaded:', entry.name, entry.responseStatus);
            }
        }
    });
    observer.observe({entryTypes: ['resource']});
    </script>
</body>
</html>
