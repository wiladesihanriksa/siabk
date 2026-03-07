<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>🧪 Test AI Tier System - SISBK</title>
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
        .tier-card {
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 15px;
            margin: 10px 0;
            background: #f8f9fa;
        }
        .tier-free { border-left: 4px solid #6c757d; }
        .tier-pro { border-left: 4px solid #007bff; }
        .tier-enterprise { border-left: 4px solid #28a745; }
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
        <h1>🧪 Test AI Tier System</h1>
        <p>Test sistem tier AI untuk user berbeda dengan provider dan limit berbeda.</p>
        
        <!-- Tier Configuration Display -->
        <div class="test-section">
            <h3>📋 Current Tier Configuration</h3>
            <div id="tier-config">
                <p class="info">Loading tier configuration...</p>
            </div>
        </div>
        
        <!-- Test Different Tiers -->
        <div class="test-section">
            <h3>🎯 Test Different User Tiers</h3>
            <div class="row">
                <div class="col-md-4">
                    <div class="tier-card tier-free">
                        <h5><i class="fa fa-user"></i> Free Tier</h5>
                        <p><strong>Provider:</strong> Gemini Flash</p>
                        <p><strong>Limits:</strong> 50/day, 1000/month</p>
                        <button onclick="testTier('free')" class="test-button">Test Free Tier</button>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="tier-card tier-pro">
                        <h5><i class="fa fa-star"></i> Pro Tier</h5>
                        <p><strong>Provider:</strong> Gemini Pro</p>
                        <p><strong>Limits:</strong> 500/day, 10000/month</p>
                        <button onclick="testTier('pro')" class="test-button">Test Pro Tier</button>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="tier-card tier-enterprise">
                        <h5><i class="fa fa-crown"></i> Enterprise Tier</h5>
                        <p><strong>Provider:</strong> OpenAI GPT-4</p>
                        <p><strong>Limits:</strong> 5000/day, 100000/month</p>
                        <button onclick="testTier('enterprise')" class="test-button">Test Enterprise Tier</button>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Test Chatbot with Tier -->
        <div class="test-section">
            <h3>🤖 Test Chatbot with Tier</h3>
            <input type="text" id="test-message" class="test-input" placeholder="Masukkan pesan test..." value="Jelaskan konsep machine learning">
            <select id="test-tier" class="test-input">
                <option value="free">Free Tier</option>
                <option value="pro">Pro Tier</option>
                <option value="enterprise">Enterprise Tier</option>
            </select>
            <button onclick="testChatbotWithTier()" class="test-button">Test Chatbot</button>
            <div id="chatbot-response" class="response-box" style="display: none;">
                <h4>Chatbot Response:</h4>
                <div id="response-content"></div>
            </div>
        </div>
        
        <!-- Usage Statistics -->
        <div class="test-section">
            <h3>📊 Usage Statistics</h3>
            <div id="usage-stats">
                <p class="info">Loading usage statistics...</p>
            </div>
        </div>
    </div>
    
    <script>
    // Test specific tier
    async function testTier(tier) {
        const message = "Test message for " + tier + " tier";
        await testChatbotWithTier(message, tier);
    }
    
    // Test chatbot with specific tier
    async function testChatbotWithTier(message = null, tier = null) {
        const testMessage = message || document.getElementById('test-message').value;
        const testTier = tier || document.getElementById('test-tier').value;
        const responseDiv = document.getElementById('chatbot-response');
        const contentDiv = document.getElementById('response-content');
        
        if(!testMessage.trim()) {
            alert('Masukkan pesan test!');
            return;
        }
        
        responseDiv.style.display = 'block';
        contentDiv.innerHTML = '<p class="info">Testing ' + testTier + ' tier...</p>';
        
        try {
            const startTime = Date.now();
            const response = await fetch('./chatbot/chatbot_api.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ 
                    message: testMessage,
                    user_tier: testTier // Simulate tier
                })
            });
            
            const endTime = Date.now();
            const responseTime = endTime - startTime;
            
            if(response.ok) {
                const data = await response.json();
                contentDiv.innerHTML = `
                    <p><strong>Response:</strong> ${data.response || 'No response'}</p>
                    <p><strong>Response Time:</strong> ${responseTime}ms</p>
                    <p><strong>AI Type:</strong> ${data.ai_type || 'Unknown'}</p>
                    <p><strong>User Tier:</strong> ${testTier}</p>
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
    
    // Load tier configuration
    async function loadTierConfig() {
        try {
            const response = await fetch('./get_tier_config.php');
            const config = await response.json();
            
            const html = `
                <div class="row">
                    <div class="col-md-4">
                        <div class="tier-card tier-free">
                            <h5><i class="fa fa-user"></i> Free Tier</h5>
                            <p><strong>Provider:</strong> ${config.free.provider}</p>
                            <p><strong>Daily Limit:</strong> ${config.free.daily_requests}</p>
                            <p><strong>Monthly Limit:</strong> ${config.free.monthly_requests}</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="tier-card tier-pro">
                            <h5><i class="fa fa-star"></i> Pro Tier</h5>
                            <p><strong>Provider:</strong> ${config.pro.provider}</p>
                            <p><strong>Daily Limit:</strong> ${config.pro.daily_requests}</p>
                            <p><strong>Monthly Limit:</strong> ${config.pro.monthly_requests}</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="tier-card tier-enterprise">
                            <h5><i class="fa fa-crown"></i> Enterprise Tier</h5>
                            <p><strong>Provider:</strong> ${config.enterprise.provider}</p>
                            <p><strong>Daily Limit:</strong> ${config.enterprise.daily_requests}</p>
                            <p><strong>Monthly Limit:</strong> ${config.enterprise.monthly_requests}</p>
                        </div>
                    </div>
                </div>
            `;
            
            document.getElementById('tier-config').innerHTML = html;
        } catch (error) {
            document.getElementById('tier-config').innerHTML = `
                <p class="error">Error loading tier configuration: ${error.message}</p>
            `;
        }
    }
    
    // Load usage statistics
    async function loadUsageStats() {
        try {
            const response = await fetch('./get_usage_stats.php');
            const stats = await response.json();
            
            const html = `
                <div class="row">
                    <div class="col-md-4">
                        <h5>Free Tier Usage</h5>
                        <p><strong>Today:</strong> ${stats.free.today || 0} requests</p>
                        <p><strong>This Month:</strong> ${stats.free.month || 0} requests</p>
                    </div>
                    <div class="col-md-4">
                        <h5>Pro Tier Usage</h5>
                        <p><strong>Today:</strong> ${stats.pro.today || 0} requests</p>
                        <p><strong>This Month:</strong> ${stats.pro.month || 0} requests</p>
                    </div>
                    <div class="col-md-4">
                        <h5>Enterprise Tier Usage</h5>
                        <p><strong>Today:</strong> ${stats.enterprise.today || 0} requests</p>
                        <p><strong>This Month:</strong> ${stats.enterprise.month || 0} requests</p>
                    </div>
                </div>
            `;
            
            document.getElementById('usage-stats').innerHTML = html;
        } catch (error) {
            document.getElementById('usage-stats').innerHTML = `
                <p class="error">Error loading usage statistics: ${error.message}</p>
            `;
        }
    }
    
    // Initialize
    document.addEventListener('DOMContentLoaded', function() {
        loadTierConfig();
        loadUsageStats();
        
        // Auto-refresh every 30 seconds
        setInterval(() => {
            loadUsageStats();
        }, 30000);
    });
    </script>
</body>
</html>
