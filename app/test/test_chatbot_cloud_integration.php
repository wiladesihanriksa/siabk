<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Chatbot Cloud AI Integration - SISBK</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background: #f5f5f5;
        }
        .test-container {
            max-width: 1000px;
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
        .config-table {
            width: 100%;
            border-collapse: collapse;
            margin: 10px 0;
        }
        .config-table th, .config-table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        .config-table th {
            background: #f8f9fa;
        }
    </style>
</head>
<body>
    <div class="test-container">
        <h1>🧪 Test Chatbot Cloud AI Integration</h1>
        <p>Test integrasi chatbot dengan cloud AI (Gemini, OpenAI, dll) melalui pengaturan aplikasi.</p>
        
        <!-- Configuration Status -->
        <div class="test-section">
            <h3>📋 Current Configuration</h3>
            <div id="config-status">
                <p class="info">Loading configuration...</p>
            </div>
        </div>
        
        <!-- AI Provider Test -->
        <div class="test-section">
            <h3>🤖 Test AI Response</h3>
            <div class="test-input-group">
                <input type="text" id="test-message" class="test-input" placeholder="Masukkan pesan test..." value="Apa itu SISBK?">
                <button onclick="testAI()" class="test-button">Test AI Response</button>
                <button onclick="testAllProviders()" class="test-button">Test All Providers</button>
            </div>
            <div id="ai-response" class="response-box" style="display: none;">
                <h4>AI Response:</h4>
                <div id="response-content"></div>
            </div>
        </div>
        
        <!-- Provider Status -->
        <div class="test-section">
            <h3>⚖️ Provider Status</h3>
            <div id="provider-status">
                <p class="info">Checking provider status...</p>
            </div>
        </div>
        
        <!-- Setup Guide -->
        <div class="test-section">
            <h3>🛠️ Setup Guide</h3>
            <div class="alert alert-info">
                <h5>Setup Cloud AI:</h5>
                <ol>
                    <li><strong>Login Admin:</strong> <a href="admin/pengaturan_aplikasi.php" target="_blank">Pengaturan Aplikasi</a></li>
                    <li><strong>Tab Chatbot:</strong> Scroll ke bagian "Cloud AI Settings"</li>
                    <li><strong>Pilih Provider:</strong> Google Gemini (Recommended)</li>
                    <li><strong>API Key:</strong> Dapatkan di <a href="https://makersuite.google.com/app/apikey" target="_blank">Google AI Studio</a></li>
                    <li><strong>Save Settings:</strong> Klik "Simpan Pengaturan"</li>
                </ol>
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
    
    // Test All Providers
    async function testAllProviders() {
        const providers = ['gemini', 'openai', 'huggingface', 'ollama'];
        const results = [];
        
        for(const provider of providers) {
            try {
                const startTime = Date.now();
                const response = await fetch(`./chatbot/test_provider.php?provider=${provider}`, {
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
        
        displayProviderStatus(results);
    }
    
    function displayProviderStatus(results) {
        const html = results.map(result => `
            <div style="margin: 10px 0; padding: 10px; border: 1px solid #ddd; border-radius: 5px;">
                <h4>${result.provider.toUpperCase()}</h4>
                <p><strong>Status:</strong> <span class="${result.status === 'success' ? 'success' : 'error'}">${result.status}</span></p>
                <p><strong>Response Time:</strong> ${result.time}ms</p>
                <p><strong>Configured:</strong> <span class="${result.configured ? 'success' : 'error'}">${result.configured ? 'Yes' : 'No'}</span></p>
            </div>
        `).join('');
        
        document.getElementById('provider-status').innerHTML = html;
    }
    
    // Check Configuration
    async function checkConfiguration() {
        try {
            const response = await fetch('./chatbot/cloud_ai_status.php');
            const config = await response.json();
            
            const html = `
                <table class="config-table">
                    <tr>
                        <th>Setting</th>
                        <th>Value</th>
                        <th>Status</th>
                    </tr>
                    <tr>
                        <td>Cloud AI Enabled</td>
                        <td>${config.use_cloud_ai ? 'Yes' : 'No'}</td>
                        <td><span class="${config.use_cloud_ai ? 'success' : 'warning'}">${config.use_cloud_ai ? 'Active' : 'Inactive'}</span></td>
                    </tr>
                    <tr>
                        <td>AI Provider</td>
                        <td>${config.provider}</td>
                        <td><span class="info">Selected</span></td>
                    </tr>
                    <tr>
                        <td>Configured</td>
                        <td>${config.configured ? 'Yes' : 'No'}</td>
                        <td><span class="${config.configured ? 'success' : 'error'}">${config.configured ? 'Ready' : 'Not Ready'}</span></td>
                    </tr>
                    <tr>
                        <td>Fallback Enabled</td>
                        <td>${config.fallback ? 'Yes' : 'No'}</td>
                        <td><span class="${config.fallback ? 'success' : 'warning'}">${config.fallback ? 'Active' : 'Inactive'}</span></td>
                    </tr>
                </table>
                
                <h5>Recommendations:</h5>
                <ul>
                    ${config.recommendations.map(rec => `<li>${rec}</li>`).join('')}
                </ul>
            `;
            
            document.getElementById('config-status').innerHTML = html;
        } catch (error) {
            document.getElementById('config-status').innerHTML = `
                <p class="error">Error checking configuration: ${error.message}</p>
            `;
        }
    }
    
    // Initialize tests
    document.addEventListener('DOMContentLoaded', function() {
        checkConfiguration();
        testAllProviders();
        
        // Auto-refresh every 30 seconds
        setInterval(() => {
            checkConfiguration();
        }, 30000);
    });
    </script>
</body>
</html>
