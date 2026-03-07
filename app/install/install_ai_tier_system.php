<?php
// Script untuk install AI Tier System
include 'koneksi.php';

echo "<h2>🚀 Installing AI Tier System</h2>";

// SQL untuk menambahkan tier system
$sql_queries = [
    // Add user_tier column if not exists
    "ALTER TABLE user ADD COLUMN user_tier ENUM('free', 'pro', 'enterprise') DEFAULT 'free'",
    
    // Add tier system settings
    "INSERT INTO app_settings (setting_key, setting_value, description) VALUES
    ('ai_tier_system_enabled', '1', 'Status sistem tier AI (1=aktif, 0=nonaktif)')
    ON DUPLICATE KEY UPDATE 
    setting_value = VALUES(setting_value),
    description = VALUES(description);",
    
    "INSERT INTO app_settings (setting_key, setting_value, description) VALUES
    ('ai_free_tier_provider', 'gemini-flash', 'AI provider untuk user free tier')
    ON DUPLICATE KEY UPDATE 
    setting_value = VALUES(setting_value),
    description = VALUES(description);",
    
    "INSERT INTO app_settings (setting_key, setting_value, description) VALUES
    ('ai_pro_tier_provider', 'gemini-pro', 'AI provider untuk user pro tier')
    ON DUPLICATE KEY UPDATE 
    setting_value = VALUES(setting_value),
    description = VALUES(description);",
    
    "INSERT INTO app_settings (setting_key, setting_value, description) VALUES
    ('ai_enterprise_tier_provider', 'openai-gpt4', 'AI provider untuk user enterprise tier')
    ON DUPLICATE KEY UPDATE 
    setting_value = VALUES(setting_value),
    description = VALUES(description);",
    
    "INSERT INTO app_settings (setting_key, setting_value, description) VALUES
    ('ai_free_tier_limits', '{\"daily_requests\": 50, \"monthly_requests\": 1000}', 'Limit untuk free tier')
    ON DUPLICATE KEY UPDATE 
    setting_value = VALUES(setting_value),
    description = VALUES(description);",
    
    "INSERT INTO app_settings (setting_key, setting_value, description) VALUES
    ('ai_pro_tier_limits', '{\"daily_requests\": 500, \"monthly_requests\": 10000}', 'Limit untuk pro tier')
    ON DUPLICATE KEY UPDATE 
    setting_value = VALUES(setting_value),
    description = VALUES(description);",
    
    "INSERT INTO app_settings (setting_key, setting_value, description) VALUES
    ('ai_enterprise_tier_limits', '{\"daily_requests\": 5000, \"monthly_requests\": 100000}', 'Limit untuk enterprise tier')
    ON DUPLICATE KEY UPDATE 
    setting_value = VALUES(setting_value),
    description = VALUES(description);",
    
    "INSERT INTO app_settings (setting_key, setting_value, description) VALUES
    ('ai_fallback_enabled', '1', 'Fallback ke tier lebih rendah jika limit habis')
    ON DUPLICATE KEY UPDATE 
    setting_value = VALUES(setting_value),
    description = VALUES(description);",
    
    // Create AI usage tracking table
    "CREATE TABLE IF NOT EXISTS ai_usage_tracking (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT,
        tier ENUM('free', 'pro', 'enterprise'),
        provider VARCHAR(50),
        request_count INT DEFAULT 1,
        usage_date DATE,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        INDEX idx_user_date (user_id, usage_date),
        INDEX idx_tier_date (tier, usage_date)
    )",
    
    // Update existing users to free tier
    "UPDATE user SET user_tier = 'free' WHERE user_tier IS NULL"
];

$success_count = 0;
$error_count = 0;

echo "<h3>📋 Executing SQL Queries...</h3>";

foreach($sql_queries as $index => $sql) {
    echo "<p><strong>Query " . ($index + 1) . ":</strong> ";
    
    if(mysqli_query($koneksi, $sql)) {
        echo "<span style='color: green;'>✅ Success</span></p>";
        $success_count++;
    } else {
        echo "<span style='color: red;'>❌ Error: " . mysqli_error($koneksi) . "</span></p>";
        $error_count++;
    }
}

echo "<hr>";
echo "<h3>📊 Installation Summary</h3>";
echo "<p><strong>Success:</strong> <span style='color: green;'>$success_count queries</span></p>";
echo "<p><strong>Errors:</strong> <span style='color: red;'>$error_count queries</span></p>";

if($error_count == 0) {
    echo "<p style='color: green; font-weight: bold;'>🎉 AI Tier System installed successfully!</p>";
} else {
    echo "<p style='color: orange; font-weight: bold;'>⚠️ Installation completed with some errors.</p>";
}

// Display tier configuration
echo "<hr>";
echo "<h3>🎯 AI Tier Configuration</h3>";

$tiers = [
    'free' => ['name' => 'Free Tier', 'color' => 'default', 'icon' => 'user'],
    'pro' => ['name' => 'Pro Tier', 'color' => 'primary', 'icon' => 'star'],
    'enterprise' => ['name' => 'Enterprise Tier', 'color' => 'success', 'icon' => 'crown']
];

foreach($tiers as $tier => $info) {
    $provider_key = "ai_{$tier}_tier_provider";
    $limits_key = "ai_{$tier}_tier_limits";
    
    $query = "SELECT setting_value FROM app_settings WHERE setting_key = ?";
    $stmt = mysqli_prepare($koneksi, $query);
    mysqli_stmt_bind_param($stmt, "s", $provider_key);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $provider = mysqli_fetch_assoc($result)['setting_value'] ?? 'Not set';
    
    $stmt = mysqli_prepare($koneksi, $query);
    mysqli_stmt_bind_param($stmt, "s", $limits_key);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $limits = json_decode(mysqli_fetch_assoc($result)['setting_value'] ?? '{}', true);
    
    echo "<div class='panel panel-{$info['color']}' style='margin: 10px 0;'>";
    echo "<div class='panel-heading'>";
    echo "<h6><i class='fa fa-{$info['icon']}'></i> {$info['name']}</h6>";
    echo "</div>";
    echo "<div class='panel-body'>";
    echo "<p><strong>Provider:</strong> $provider</p>";
    echo "<p><strong>Daily Limit:</strong> " . ($limits['daily_requests'] ?? 'Not set') . " requests</p>";
    echo "<p><strong>Monthly Limit:</strong> " . ($limits['monthly_requests'] ?? 'Not set') . " requests</p>";
    echo "</div>";
    echo "</div>";
}

echo "<hr>";
echo "<h3>🎯 Next Steps</h3>";
echo "<ol>";
echo "<li><strong>Configure Tiers:</strong> <a href='admin/pengaturan_aplikasi.php' target='_blank'>Admin Panel</a></li>";
echo "<li><strong>Set User Tiers:</strong> Update user_tier in users table</li>";
echo "<li><strong>Test System:</strong> <a href='test_chatbot_final.php' target='_blank'>Test Chatbot</a></li>";
echo "<li><strong>Monitor Usage:</strong> Check ai_usage_tracking table</li>";
echo "</ol>";

echo "<p><strong>✅ AI Tier System ready!</strong> Users can now have different AI providers based on their tier.</p>";
?>
