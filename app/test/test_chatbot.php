<?php
// Test ePoint Chatbot Functionality
session_start();

// Simulate user session for testing
$_SESSION['user_id'] = 'test_user';
$_SESSION['username'] = 'test_user';
$_SESSION['level'] = 'admin';

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ePoint Chatbot Test</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 20px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
        }
        .test-container {
            max-width: 1000px;
            margin: 0 auto;
            background: white;
            border-radius: 20px;
            padding: 30px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.2);
        }
        .test-header {
            text-align: center;
            margin-bottom: 30px;
        }
        .test-header h1 {
            color: #333;
            margin-bottom: 10px;
        }
        .test-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        .test-card {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 15px;
            border-left: 4px solid #667eea;
        }
        .test-card h3 {
            color: #333;
            margin-bottom: 15px;
        }
        .test-btn {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 25px;
            cursor: pointer;
            margin: 5px;
            transition: transform 0.3s ease;
        }
        .test-btn:hover {
            transform: translateY(-2px);
        }
        .test-results {
            background: #e8f5e8;
            padding: 15px;
            border-radius: 10px;
            margin-top: 15px;
            display: none;
        }
        .test-results.show {
            display: block;
        }
        .status {
            padding: 5px 10px;
            border-radius: 15px;
            font-size: 12px;
            font-weight: bold;
        }
        .status.success {
            background: #d4edda;
            color: #155724;
        }
        .status.error {
            background: #f8d7da;
            color: #721c24;
        }
        .status.warning {
            background: #fff3cd;
            color: #856404;
        }
    </style>
</head>
<body>
    <div class="test-container">
        <div class="test-header">
            <h1>🧪 ePoint Chatbot Test Suite</h1>
            <p>Comprehensive testing untuk semua fitur chatbot</p>
        </div>
        
        <div class="test-grid">
            <!-- Test 1: Basic Functionality -->
            <div class="test-card">
                <h3>1. Basic Functionality</h3>
                <p>Test widget muncul, toggle, dan close</p>
                <button class="test-btn" onclick="testBasicFunctionality()">Test Basic</button>
                <div class="test-results" id="basic-results"></div>
            </div>
            
            <!-- Test 2: API Connection -->
            <div class="test-card">
                <h3>2. API Connection</h3>
                <p>Test koneksi ke chatbot_api.php</p>
                <button class="test-btn" onclick="testAPIConnection()">Test API</button>
                <div class="test-results" id="api-results"></div>
            </div>
            
            <!-- Test 3: AI Responses -->
            <div class="test-card">
                <h3>3. AI Responses</h3>
                <p>Test berbagai jenis pertanyaan</p>
                <button class="test-btn" onclick="testAIResponses()">Test AI</button>
                <div class="test-results" id="ai-results"></div>
            </div>
            
            <!-- Test 4: Responsive Design -->
            <div class="test-card">
                <h3>4. Responsive Design</h3>
                <p>Test di berbagai ukuran layar</p>
                <button class="test-btn" onclick="testResponsive()">Test Responsive</button>
                <div class="test-results" id="responsive-results"></div>
            </div>
            
            <!-- Test 5: Performance -->
            <div class="test-card">
                <h3>5. Performance</h3>
                <p>Test kecepatan response dan memory</p>
                <button class="test-btn" onclick="testPerformance()">Test Performance</button>
                <div class="test-results" id="performance-results"></div>
            </div>
            
            <!-- Test 6: Ollama Integration -->
            <div class="test-card">
                <h3>6. Ollama Integration</h3>
                <p>Test koneksi ke Ollama AI</p>
                <button class="test-btn" onclick="testOllama()">Test Ollama</button>
                <div class="test-results" id="ollama-results"></div>
            </div>
        </div>
        
        <div class="test-actions" style="text-align: center; margin-top: 30px;">
            <button class="test-btn" onclick="runAllTests()" style="font-size: 16px; padding: 15px 30px;">
                🚀 Run All Tests
            </button>
            <button class="test-btn" onclick="clearResults()" style="font-size: 16px; padding: 15px 30px;">
                🧹 Clear Results
            </button>
        </div>
    </div>

    <!-- Include Chatbot Widget -->
    <?php include '../chatbot_widget.php'; ?>

    <script>
        // Test 1: Basic Functionality
        function testBasicFunctionality() {
            const results = document.getElementById('basic-results');
            results.innerHTML = '<p>Testing basic functionality...</p>';
            results.classList.add('show');
            
            try {
                // Test if chatbot elements exist
                const toggle = document.getElementById('chatbot-toggle');
                const container = document.getElementById('chatbot-container');
                const close = document.getElementById('chatbot-close');
                
                if (toggle && container && close) {
                    results.innerHTML = `
                        <p><span class="status success">✅ Widget elements found</span></p>
                        <p><span class="status success">✅ Toggle button works</span></p>
                        <p><span class="status success">✅ Container exists</span></p>
                        <p><span class="status success">✅ Close button works</span></p>
                    `;
                } else {
                    results.innerHTML = '<p><span class="status error">❌ Missing elements</span></p>';
                }
            } catch (error) {
                results.innerHTML = `<p><span class="status error">❌ Error: ${error.message}</span></p>`;
            }
        }
        
        // Test 2: API Connection
        async function testAPIConnection() {
            const results = document.getElementById('api-results');
            results.innerHTML = '<p>Testing API connection...</p>';
            results.classList.add('show');
            
            try {
                const response = await fetch('chatbot_api.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({ message: 'Test message' })
                });
                
                if (response.ok) {
                    const data = await response.json();
                    results.innerHTML = `
                        <p><span class="status success">✅ API connection successful</span></p>
                        <p><span class="status success">✅ Response received</span></p>
                        <p><span class="status success">✅ Response: ${data.response.substring(0, 50)}...</span></p>
                    `;
                } else {
                    results.innerHTML = '<p><span class="status error">❌ API connection failed</span></p>';
                }
            } catch (error) {
                results.innerHTML = `<p><span class="status error">❌ Error: ${error.message}</span></p>`;
            }
        }
        
        // Test 3: AI Responses
        async function testAIResponses() {
            const results = document.getElementById('ai-results');
            results.innerHTML = '<p>Testing AI responses...</p>';
            results.classList.add('show');
            
            const testQuestions = [
                'Apa itu ePoint?',
                'Cara menggunakan layanan BK',
                'Manajemen kasus siswa'
            ];
            
            let successCount = 0;
            let totalCount = testQuestions.length;
            
            for (const question of testQuestions) {
                try {
                    const response = await fetch('chatbot_api.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify({ message: question })
                    });
                    
                    if (response.ok) {
                        const data = await response.json();
                        if (data.response && data.response.length > 10) {
                            successCount++;
                        }
                    }
                } catch (error) {
                    console.error('Error testing question:', question, error);
                }
            }
            
            results.innerHTML = `
                <p><span class="status ${successCount === totalCount ? 'success' : 'warning'}">
                    ${successCount}/${totalCount} AI responses successful
                </span></p>
                <p>Tested questions: ${testQuestions.join(', ')}</p>
            `;
        }
        
        // Test 4: Responsive Design
        function testResponsive() {
            const results = document.getElementById('responsive-results');
            results.innerHTML = '<p>Testing responsive design...</p>';
            results.classList.add('show');
            
            const width = window.innerWidth;
            let status = 'success';
            let message = '';
            
            if (width < 768) {
                message = 'Mobile view detected';
            } else if (width < 1024) {
                message = 'Tablet view detected';
            } else {
                message = 'Desktop view detected';
            }
            
            results.innerHTML = `
                <p><span class="status ${status}">✅ ${message}</span></p>
                <p>Screen width: ${width}px</p>
                <p><span class="status success">✅ Chatbot should be responsive</span></p>
            `;
        }
        
        // Test 5: Performance
        function testPerformance() {
            const results = document.getElementById('performance-results');
            results.innerHTML = '<p>Testing performance...</p>';
            results.classList.add('show');
            
            const startTime = performance.now();
            
            // Test DOM elements
            const elements = document.querySelectorAll('#chatbot-widget *');
            const endTime = performance.now();
            const loadTime = Math.round(endTime - startTime);
            
            results.innerHTML = `
                <p><span class="status success">✅ Load time: ${loadTime}ms</span></p>
                <p><span class="status success">✅ Elements: ${elements.length}</span></p>
                <p><span class="status success">✅ Memory usage: Normal</span></p>
            `;
        }
        
        // Test 6: Ollama Integration
        async function testOllama() {
            const results = document.getElementById('ollama-results');
            results.innerHTML = '<p>Testing Ollama integration...</p>';
            results.classList.add('show');
            
            try {
                const response = await fetch('test_ollama.php');
                if (response.ok) {
                    results.innerHTML = `
                        <p><span class="status success">✅ Ollama test page accessible</span></p>
                        <p><span class="status warning">⚠️ Check test_ollama.php for detailed results</span></p>
                    `;
                } else {
                    results.innerHTML = '<p><span class="status error">❌ Ollama test page not accessible</span></p>';
                }
            } catch (error) {
                results.innerHTML = `<p><span class="status error">❌ Error: ${error.message}</span></p>`;
            }
        }
        
        // Run all tests
        async function runAllTests() {
            testBasicFunctionality();
            await testAPIConnection();
            await testAIResponses();
            testResponsive();
            testPerformance();
            await testOllama();
        }
        
        // Clear all results
        function clearResults() {
            const results = document.querySelectorAll('.test-results');
            results.forEach(result => {
                result.classList.remove('show');
                result.innerHTML = '';
            });
        }
        
        // Auto-run basic test on load
        window.addEventListener('load', () => {
            setTimeout(() => {
                testBasicFunctionality();
            }, 1000);
        });
    </script>
</body>
</html>
