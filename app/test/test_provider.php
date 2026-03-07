<?php
// Test Individual AI Provider
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

require_once 'cloud_ai_config.php';
require_once 'gemini_ai.php';

$provider = $_GET['provider'] ?? 'gemini';
$input = json_decode(file_get_contents('php://input'), true);
$message = $input['message'] ?? 'Test message';

$response = [
    'provider' => $provider,
    'message' => $message,
    'timestamp' => date('Y-m-d H:i:s'),
    'status' => 'error',
    'response' => '',
    'response_time' => 0,
    'configured' => false
];

$start_time = microtime(true);

try {
    switch($provider) {
        case 'gemini':
            if(CloudAIConfig::isConfigured()) {
                $ai = new GeminiAI(CloudAIConfig::GEMINI_API_KEY);
                $response['response'] = $ai->getResponse($message);
                $response['status'] = 'success';
                $response['configured'] = true;
            } else {
                $response['response'] = 'Gemini API key not configured';
                $response['status'] = 'error';
            }
            break;
            
        case 'openai':
            $response['response'] = 'OpenAI integration not implemented yet';
            $response['status'] = 'error';
            break;
            
        case 'huggingface':
            $response['response'] = 'Hugging Face integration not implemented yet';
            $response['status'] = 'error';
            break;
            
        case 'ollama':
            $response['response'] = 'Ollama integration not implemented yet';
            $response['status'] = 'error';
            break;
            
        default:
            $response['response'] = 'Unknown provider';
            $response['status'] = 'error';
    }
    
} catch (Exception $e) {
    $response['response'] = 'Error: ' . $e->getMessage();
    $response['status'] = 'error';
}

$end_time = microtime(true);
$response['response_time'] = round(($end_time - $start_time) * 1000, 2);

echo json_encode($response);
?>
