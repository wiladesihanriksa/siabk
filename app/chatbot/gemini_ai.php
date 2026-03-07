<?php
// Google Gemini AI Integration for SISBK Chatbot
class GeminiAI {
    private $api_key;
    private $api_url = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash:generateContent';
    
    public function __construct($api_key = null) {
        $this->api_key = $api_key ?: 'YOUR_GEMINI_API_KEY'; // Set your API key here
    }
    
    public function getResponse($message, $context = '') {
        try {
            $prompt = $this->buildPrompt($message, $context);
            $response = $this->callAPI($prompt);
            
            if(isset($response['candidates'][0]['content']['parts'][0]['text'])) {
                return $response['candidates'][0]['content']['parts'][0]['text'];
            }
            
            // Check if response was truncated due to max tokens
            if(isset($response['candidates'][0]['finishReason']) && 
               $response['candidates'][0]['finishReason'] === 'MAX_TOKENS') {
                return "Maaf, respons terlalu panjang. Silakan ajukan pertanyaan yang lebih spesifik.";
            }
            
            error_log("Gemini response structure: " . json_encode($response['candidates'][0] ?? []));
            return "Maaf, saya tidak bisa memproses pertanyaan Anda saat ini. Silakan coba lagi.";
            
        } catch (Exception $e) {
            error_log("Gemini AI Error: " . $e->getMessage());
            return "Maaf, terjadi kesalahan pada sistem AI. Silakan coba lagi nanti.";
        }
    }
    
    private function buildPrompt($message, $context) {
        $system_prompt = "Anda adalah asisten konseling sekolah. Jawab dengan ramah dalam bahasa Indonesia. Berikan saran konstruktif untuk masalah siswa.";
        
        return [
            'contents' => [
                [
                    'parts' => [
                        ['text' => $system_prompt . "\n\nPertanyaan: " . $message]
                    ]
                ]
            ],
            'generationConfig' => [
                'temperature' => 0.7,
                'maxOutputTokens' => 150,
                'topP' => 0.8,
                'topK' => 10
            ]
        ];
    }
    
    private function callAPI($data) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->api_url . '?key=' . $this->api_key);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json'
        ]);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        
        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);
        
        error_log("Gemini API Response - HTTP Code: $http_code");
        error_log("Gemini API Response: " . $response);
        
        if($error) {
            throw new Exception("cURL Error: " . $error);
        }
        
        if($http_code !== 200) {
            throw new Exception("API Error: HTTP " . $http_code . " - Response: " . $response);
        }
        
        $json_response = json_decode($response, true);
        
        if(!$json_response) {
            throw new Exception("Invalid JSON response: " . $response);
        }
        
        return $json_response;
    }
    
    public function isConfigured() {
        return $this->api_key !== 'YOUR_GEMINI_API_KEY' && !empty($this->api_key);
    }
}
?>
