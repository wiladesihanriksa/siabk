<?php
// Ollama AI Integration for ePoint Chatbot
// Sistem AI lokal menggunakan Ollama

class OllamaAI {
    private $ollama_url;
    private $model;
    private $fallback_responses;
    
    public function __construct($ollama_url = 'http://localhost:11434', $model = 'llama2') {
        $this->ollama_url = $ollama_url;
        $this->model = $model;
        $this->initializeFallbackResponses();
    }
    
    private function initializeFallbackResponses() {
        $this->fallback_responses = [
            'Maaf, sistem AI sedang tidak tersedia. Silakan coba lagi nanti.',
            'Saya mengalami kesulitan memproses permintaan Anda. Coba tanyakan hal lain.',
            'Sistem AI sedang dalam maintenance. Silakan gunakan fitur bantuan manual.',
            'Koneksi ke AI lokal terputus. Coba refresh halaman dan coba lagi.'
        ];
    }
    
    public function generateResponse($message, $context = '') {
        // Cek koneksi Ollama
        if (!$this->isOllamaAvailable()) {
            return $this->getFallbackResponse();
        }
        
        // Siapkan prompt untuk AI
        $prompt = $this->buildPrompt($message, $context);
        
        try {
            // Kirim request ke Ollama
            $response = $this->sendToOllama($prompt);
            
            if ($response && isset($response['response'])) {
                return $this->cleanResponse($response['response']);
            }
            
            return $this->getFallbackResponse();
            
        } catch (Exception $e) {
            error_log('Ollama AI Error: ' . $e->getMessage());
            return $this->getFallbackResponse();
        }
    }
    
    private function isOllamaAvailable() {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->ollama_url . '/api/tags');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 3);
        
        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        return $http_code === 200;
    }
    
    private function buildPrompt($message, $context) {
        $system_prompt = "Anda adalah ePoint Assistant, chatbot untuk sistem manajemen sekolah ePoint. 
        Anda membantu pengguna dengan informasi tentang:
        - Aplikasi ePoint dan fitur-fiturnya
        - Layanan Bimbingan Konseling (BK)
        - Manajemen kasus siswa
        - Kunjungan rumah
        - Laporan dan dokumentasi
        - Troubleshooting teknis
        
        Jawab dalam bahasa Indonesia yang ramah dan informatif.
        Jika tidak tahu jawabannya, arahkan ke fitur bantuan atau kontak administrator.
        
        Konteks: " . $context . "
        
        Pertanyaan pengguna: " . $message;
        
        return $system_prompt;
    }
    
    private function sendToOllama($prompt) {
        $data = [
            'model' => $this->model,
            'prompt' => $prompt,
            'stream' => false,
            'options' => [
                'temperature' => 0.7,
                'max_tokens' => 500
            ]
        ];
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->ollama_url . '/api/generate');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json'
        ]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        
        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        if ($http_code === 200) {
            return json_decode($response, true);
        }
        
        return false;
    }
    
    private function cleanResponse($response) {
        // Bersihkan response dari karakter yang tidak diinginkan
        $response = trim($response);
        $response = preg_replace('/\n+/', "\n", $response);
        $response = strip_tags($response);
        
        // Batasi panjang response
        if (strlen($response) > 1000) {
            $response = substr($response, 0, 1000) . '...';
        }
        
        return $response;
    }
    
    private function getFallbackResponse() {
        return $this->fallback_responses[array_rand($this->fallback_responses)];
    }
    
    // Method untuk menguji koneksi Ollama
    public function testConnection() {
        return $this->isOllamaAvailable();
    }
    
    // Method untuk mendapatkan model yang tersedia
    public function getAvailableModels() {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->ollama_url . '/api/tags');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        
        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        if ($http_code === 200) {
            $data = json_decode($response, true);
            return $data['models'] ?? [];
        }
        
        return [];
    }
}

// Hybrid AI System - Kombinasi Ollama dan Rule-based
class HybridAI {
    private $ollama_ai;
    private $rule_based_ai;
    
    public function __construct() {
        $this->ollama_ai = new OllamaAI();
        $this->rule_based_ai = new ePointChatbotAI();
    }
    
    public function getResponse($message) {
        // Coba Ollama AI dulu
        if ($this->ollama_ai->testConnection()) {
            $response = $this->ollama_ai->generateResponse($message);
            
            // Jika response tidak kosong dan tidak error
            if (!empty($response) && !strpos($response, 'Maaf, sistem AI sedang tidak tersedia')) {
                return $response;
            }
        }
        
        // Fallback ke rule-based AI
        return $this->rule_based_ai->getResponse($message);
    }
}

// Usage example:
/*
$ai = new HybridAI();
$response = $ai->getResponse("Apa itu ePoint?");
echo $response;
*/
?>
