<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>✅ Chatbot Final Test - SISBK</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background: #f5f5f5;
        }
        .test-container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .success { color: #28a745; }
        .error { color: #dc3545; }
        .info { color: #17a2b8; }
        .test-input {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            margin: 10px 0;
        }
        .test-button {
            background: #007bff;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            margin: 5px;
        }
        .test-button:hover {
            background: #0056b3;
        }
        .response-box {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            margin: 10px 0;
            border-left: 4px solid #007bff;
        }
    </style>
</head>
<body>
    <div class="test-container">
        <h1>✅ Chatbot Final Test</h1>
        <p>Test chatbot dengan Gemini AI yang sudah diperbaiki.</p>
        
        <div class="test-section">
            <h3>🤖 Test Chatbot Response</h3>
            <input type="text" id="test-message" class="test-input" placeholder="Masukkan pesan test..." value="Halo, apa kabar?">
            <button onclick="testChatbot()" class="test-button">Test Chatbot</button>
            <div id="chatbot-response" class="response-box" style="display: none;">
                <h4>Chatbot Response:</h4>
                <div id="response-content"></div>
            </div>
        </div>
        
        <div class="test-section">
            <h3>📋 Test Cases</h3>
            <button onclick="testCase('Halo')" class="test-button">Test: Halo</button>
            <button onclick="testCase('Apa itu SISBK?')" class="test-button">Test: Apa itu SISBK?</button>
            <button onclick="testCase('Cara login')" class="test-button">Test: Cara login</button>
            <button onclick="testCase('Fitur dashboard')" class="test-button">Test: Fitur dashboard</button>
            <button onclick="testCase('Laporan kasus')" class="test-button">Test: Laporan kasus</button>
        </div>
        
        <div class="test-section">
            <h3>🎯 Status</h3>
            <div id="status-info">
                <p class="info">Testing chatbot functionality...</p>
            </div>
        </div>
    </div>
    
    <script>
    async function testChatbot() {
        const message = document.getElementById('test-message').value;
        const responseDiv = document.getElementById('chatbot-response');
        const contentDiv = document.getElementById('response-content');
        
        if(!message.trim()) {
            alert('Masukkan pesan test!');
            return;
        }
        
        responseDiv.style.display = 'block';
        contentDiv.innerHTML = '<p class="info">Mengirim ke chatbot...</p>';
        
        try {
            const startTime = Date.now();
            const response = await fetch('./chatbot/chatbot_api.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ message: message })
            });
            
            const endTime = Date.now();
            const responseTime = endTime - startTime;
            
            if(response.ok) {
                const data = await response.json();
                contentDiv.innerHTML = `
                    <p><strong>Response:</strong> ${data.response || 'No response'}</p>
                    <p><strong>Response Time:</strong> ${responseTime}ms</p>
                    <p><strong>AI Type:</strong> ${data.ai_type || 'Unknown'}</p>
                    <p><strong>Chatbot Name:</strong> ${data.chatbot_name || 'Unknown'}</p>
                    <p><strong>Status:</strong> <span class="success">✅ Success</span></p>
                `;
            } else {
                contentDiv.innerHTML = `
                    <p><strong>Error:</strong> <span class="error">HTTP ${response.status}</span></p>
                    <p><strong>Response Time:</strong> ${responseTime}ms</p>
                `;
            }
        } catch (error) {
            contentDiv.innerHTML = `
                <p><strong>Error:</strong> <span class="error">${error.message}</span></p>
            `;
        }
    }
    
    async function testCase(message) {
        document.getElementById('test-message').value = message;
        await testChatbot();
    }
    
    // Auto-test on load
    document.addEventListener('DOMContentLoaded', function() {
        testChatbot();
    });
    </script>
</body>
</html>
