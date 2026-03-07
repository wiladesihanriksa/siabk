<?php
// Cloud AI Status Check
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

require_once 'cloud_ai_config.php';

$config = CloudAIConfig::getConfig();
$configured = CloudAIConfig::isConfigured();

$status = [
    'use_cloud_ai' => $config['use_cloud_ai'],
    'provider' => $config['provider'],
    'configured' => $configured,
    'fallback' => $config['fallback'],
    'rate_limit' => $config['rate_limit'],
    'timeout' => $config['timeout'],
    'api_keys' => [
        'gemini' => !empty($config['api_keys']['gemini']) && $config['api_keys']['gemini'] !== 'YOUR_GEMINI_API_KEY',
        'openai' => !empty($config['api_keys']['openai']) && $config['api_keys']['openai'] !== 'YOUR_OPENAI_API_KEY',
        'huggingface' => !empty($config['api_keys']['huggingface'])
    ],
    'recommendations' => []
];

// Add recommendations based on configuration
if (!$configured) {
    $status['recommendations'][] = 'Configure API key for ' . $config['provider'];
}

if ($config['provider'] === 'gemini' && !$status['api_keys']['gemini']) {
    $status['recommendations'][] = 'Get Gemini API key from https://makersuite.google.com/app/apikey';
}

if ($config['provider'] === 'openai' && !$status['api_keys']['openai']) {
    $status['recommendations'][] = 'Get OpenAI API key from https://platform.openai.com/api-keys';
}

if (!$config['fallback']) {
    $status['recommendations'][] = 'Enable fallback to rule-based responses for reliability';
}

echo json_encode($status);
?>
