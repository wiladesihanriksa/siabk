<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Cloud AI - SISBK Chatbot</title>
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
        .test-section {
            margin: 20px 0;
            padding: 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        .success { color: #28a745; }
        .error { color: #dc3545; }
        .warning { color: #ffc107; }
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
        <h1>🧪 Test Cloud AI Integration</h1>
        <p>Test berbagai AI provider untuk chatbot SISBK</p>
        
        <!-- Configuration Status -->
        <div class="test-section">
            <h3>📋 Configuration Status</h3>
            <div id="config-status">
                <p class="info">Checking configuration...</p>
            </div>
        </div>
        
        <!-- AI Provider Test -->
        <div class="test-section">
            <h3>🤖 AI Provider Test</h3>
            <div class="test-input-group">
                <input type="text" id="test-message" class="test-input" placeholder="Masukkan pesan test..." value="Apa itu SISBK?">
                <button onclick="testAI()" class="test-button">Test AI Response</button>
            </div>
            <div id="ai-response" class="response-box" style="display: none;">
                <h4>AI Response:</h4>
                <div id="response-content"></div>
            </div>
        </div>
        
        <!-- Provider Comparison -->
        <div class="test-section">
            <h3>⚖️ Provider Comparison</h3>
            <div id="provider-comparison">
                <p class="info">Testing all available providers...</p>
            </div>
        </div>
        
        <!-- Performance Metrics -->
        <div class="test-section">
            <h3>📊 Performance Metrics</h3>
            <div id="performance-metrics">
                <p class="info">Collecting performance data...</p>
            </div>
        </div>
    </div>
    
    <script>
    // Test AI Response
    async function testAI() {
        const message = document.getElementById('test-message').value;
        const responseDiv = document.getElementById('ai-response');
        const contentDiv = document.getElementById('response-content');
        
        if(!message.trim()) {
            alert('Masukkan pesan test!');
            return;
        }
        
        responseDiv.style.display = 'block';
        contentDiv.innerHTML = '<p class="info">Mengirim ke AI...</p>';
        
        try {
            const startTime = Date.now();
            const response = await fetch('./chatbot_api.php', {
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
                    <p><strong>Status:</strong> <span class="success">Success</span></p>
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
    
    // Check Configuration
    async function checkConfiguration() {
        try {
            const response = await fetch('./cloud_ai_config.php');
            const config = await response.json();
            
            document.getElementById('config-status').innerHTML = `
                <p><strong>Cloud AI:</strong> ${config.use_cloud_ai ? '<span class="success">Enabled</span>' : '<span class="warning">Disabled</span>'}</p>
                <p><strong>Provider:</strong> ${config.provider}</p>
                <p><strong>Configured:</strong> ${config.configured ? '<span class="success">Yes</span>' : '<span class="error">No</span>'}</p>
                <p><strong>Fallback:</strong> ${config.fallback ? '<span class="success">Enabled</span>' : '<span class="warning">Disabled</span>'}</p>
            `;
        } catch (error) {
            document.getElementById('config-status').innerHTML = `
                <p class="error">Error checking configuration: ${error.message}</p>
            `;
        }
    }
    
    // Test All Providers
    async function testAllProviders() {
        const providers = ['gemini', 'openai', 'huggingface', 'ollama'];
        const results = [];
        
        for(const provider of providers) {
            try {
                const startTime = Date.now();
                const response = await fetch(`./test_provider.php?provider=${provider}`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ message: 'Test message' })
                });
                const endTime = Date.now();
                
                results.push({
                    provider: provider,
                    status: response.ok ? 'success' : 'error',
                    time: endTime - startTime,
                    configured: response.ok
                });
            } catch (error) {
                results.push({
                    provider: provider,
                    status: 'error',
                    time: 0,
                    configured: false
                });
            }
        }
        
        displayProviderComparison(results);
    }
    
    function displayProviderComparison(results) {
        const html = results.map(result => `
            <div style="margin: 10px 0; padding: 10px; border: 1px solid #ddd; border-radius: 5px;">
                <h4>${result.provider.toUpperCase()}</h4>
                <p><strong>Status:</strong> <span class="${result.status === 'success' ? 'success' : 'error'}">${result.status}</span></p>
                <p><strong>Response Time:</strong> ${result.time}ms</p>
                <p><strong>Configured:</strong> <span class="${result.configured ? 'success' : 'error'}">${result.configured ? 'Yes' : 'No'}</span></p>
            </div>
        `).join('');
        
        document.getElementById('provider-comparison').innerHTML = html;
    }
    
    // Initialize tests
    document.addEventListener('DOMContentLoaded', function() {
        checkConfiguration();
        testAllProviders();
        
        // Auto-test every 30 seconds
        setInterval(() => {
            checkConfiguration();
        }, 30000);
    });
    </script>
</body>
</html>
