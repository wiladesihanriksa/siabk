<?php
// AI Tier System Functions
if (!function_exists('getUserTier')) {
    function getUserTier($koneksi, $user_id = null) {
        if($user_id === null) {
            // Default untuk guest users
            return 'free';
        }
        
        $query = "SELECT user_tier FROM users WHERE id = ?";
        $stmt = mysqli_prepare($koneksi, $query);
        mysqli_stmt_bind_param($stmt, "i", $user_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        if($row = mysqli_fetch_assoc($result)) {
            return $row['user_tier'] ?: 'free';
        }
        
        return 'free';
    }
}

if (!function_exists('getAIProviderForTier')) {
    function getAIProviderForTier($koneksi, $tier) {
        $query = "SELECT setting_value FROM app_settings WHERE setting_key = ?";
        
        switch($tier) {
            case 'free':
                $key = 'ai_free_tier_provider';
                break;
            case 'pro':
                $key = 'ai_pro_tier_provider';
                break;
            case 'enterprise':
                $key = 'ai_enterprise_tier_provider';
                break;
            default:
                $key = 'ai_free_tier_provider';
        }
        
        $stmt = mysqli_prepare($koneksi, $query);
        mysqli_stmt_bind_param($stmt, "s", $key);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        if($row = mysqli_fetch_assoc($result)) {
            return $row['setting_value'] ?: 'gemini-flash';
        }
        
        return 'gemini-flash';
    }
}

if (!function_exists('getTierLimits')) {
    function getTierLimits($koneksi, $tier) {
        $query = "SELECT setting_value FROM app_settings WHERE setting_key = ?";
        
        switch($tier) {
            case 'free':
                $key = 'ai_free_tier_limits';
                break;
            case 'pro':
                $key = 'ai_pro_tier_limits';
                break;
            case 'enterprise':
                $key = 'ai_enterprise_tier_limits';
                break;
            default:
                $key = 'ai_free_tier_limits';
        }
        
        $stmt = mysqli_prepare($koneksi, $query);
        mysqli_stmt_bind_param($stmt, "s", $key);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        if($row = mysqli_fetch_assoc($result)) {
            return json_decode($row['setting_value'], true) ?: ['daily_requests' => 50, 'monthly_requests' => 1000];
        }
        
        return ['daily_requests' => 50, 'monthly_requests' => 1000];
    }
}

if (!function_exists('checkAIUsageLimit')) {
    function checkAIUsageLimit($koneksi, $user_id, $tier) {
        $limits = getTierLimits($koneksi, $tier);
        
        // Check daily limit
        $today = date('Y-m-d');
        $query = "SELECT SUM(request_count) as daily_usage FROM ai_usage_tracking WHERE user_id = ? AND usage_date = ?";
        $stmt = mysqli_prepare($koneksi, $query);
        mysqli_stmt_bind_param($stmt, "is", $user_id, $today);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $row = mysqli_fetch_assoc($result);
        $daily_usage = $row['daily_usage'] ?: 0;
        
        if($daily_usage >= $limits['daily_requests']) {
            return ['status' => 'limit_exceeded', 'type' => 'daily', 'usage' => $daily_usage, 'limit' => $limits['daily_requests']];
        }
        
        // Check monthly limit
        $month_start = date('Y-m-01');
        $query = "SELECT SUM(request_count) as monthly_usage FROM ai_usage_tracking WHERE user_id = ? AND usage_date >= ?";
        $stmt = mysqli_prepare($koneksi, $query);
        mysqli_stmt_bind_param($stmt, "is", $user_id, $month_start);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $row = mysqli_fetch_assoc($result);
        $monthly_usage = $row['monthly_usage'] ?: 0;
        
        if($monthly_usage >= $limits['monthly_requests']) {
            return ['status' => 'limit_exceeded', 'type' => 'monthly', 'usage' => $monthly_usage, 'limit' => $limits['monthly_requests']];
        }
        
        return ['status' => 'ok', 'daily_usage' => $daily_usage, 'monthly_usage' => $monthly_usage];
    }
}

if (!function_exists('recordAIUsage')) {
    function recordAIUsage($koneksi, $user_id, $tier, $provider) {
        $today = date('Y-m-d');
        
        // Check if record exists for today
        $query = "SELECT id, request_count FROM ai_usage_tracking WHERE user_id = ? AND usage_date = ? AND provider = ?";
        $stmt = mysqli_prepare($koneksi, $query);
        mysqli_stmt_bind_param($stmt, "iss", $user_id, $today, $provider);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        if($row = mysqli_fetch_assoc($result)) {
            // Update existing record
            $new_count = $row['request_count'] + 1;
            $update_query = "UPDATE ai_usage_tracking SET request_count = ? WHERE id = ?";
            $stmt = mysqli_prepare($koneksi, $update_query);
            mysqli_stmt_bind_param($stmt, "ii", $new_count, $row['id']);
            mysqli_stmt_execute($stmt);
        } else {
            // Insert new record
            $insert_query = "INSERT INTO ai_usage_tracking (user_id, tier, provider, request_count, usage_date) VALUES (?, ?, ?, 1, ?)";
            $stmt = mysqli_prepare($koneksi, $insert_query);
            mysqli_stmt_bind_param($stmt, "isss", $user_id, $tier, $provider, $today);
            mysqli_stmt_execute($stmt);
        }
    }
}

if (!function_exists('getAIProviderConfig')) {
    function getAIProviderConfig($provider) {
        $configs = [
            'gemini-flash' => [
                'name' => 'Gemini 2.0 Flash',
                'url' => 'https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash:generateContent',
                'cost_per_1k' => 0.375,
                'speed' => 'fast',
                'quality' => 'good'
            ],
            'gemini-pro' => [
                'name' => 'Gemini 2.5 Pro',
                'url' => 'https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-pro:generateContent',
                'cost_per_1k' => 6.25,
                'speed' => 'slow',
                'quality' => 'excellent'
            ],
            'openai-gpt4' => [
                'name' => 'OpenAI GPT-4',
                'url' => 'https://api.openai.com/v1/chat/completions',
                'cost_per_1k' => 30.0,
                'speed' => 'medium',
                'quality' => 'excellent'
            ]
        ];
        
        return $configs[$provider] ?? $configs['gemini-flash'];
    }
}

if (!function_exists('getFallbackProvider')) {
    function getFallbackProvider($koneksi, $current_tier) {
        $fallback_order = [
            'enterprise' => ['pro', 'free'],
            'pro' => ['free'],
            'free' => []
        ];
        
        $fallbacks = $fallback_order[$current_tier] ?? [];
        
        foreach($fallbacks as $tier) {
            $provider = getAIProviderForTier($koneksi, $tier);
            $limits = getTierLimits($koneksi, $tier);
            
            // Check if fallback tier has available quota
            if($limits['daily_requests'] > 0) {
                return ['tier' => $tier, 'provider' => $provider];
            }
        }
        
        return null;
    }
}
?>
