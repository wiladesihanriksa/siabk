<?php
// Hybrid AI System with Cloud Options
require_once 'gemini_ai.php';

class HybridAICloud {
    private $rule_based_ai;
    private $gemini_ai;
    private $use_cloud_ai;
    
    public function __construct($use_cloud = true, $gemini_api_key = null) {
        $this->rule_based_ai = new ePointChatbotAI();
        $this->use_cloud_ai = $use_cloud;
        
        if($use_cloud && $gemini_api_key) {
            $this->gemini_ai = new GeminiAI($gemini_api_key);
        }
    }
    
    public function getResponse($message) {
        // First, try rule-based responses (fast and free)
        $rule_response = $this->rule_based_ai->getResponse($message);
        
        // If rule-based gives generic response, try cloud AI
        if($this->isGenericResponse($rule_response) && $this->use_cloud_ai && $this->gemini_ai) {
            try {
                $cloud_response = $this->gemini_ai->getResponse($message);
                if(!empty($cloud_response) && !$this->isGenericResponse($cloud_response)) {
                    return $cloud_response;
                }
            } catch (Exception $e) {
                error_log("Cloud AI Error: " . $e->getMessage());
            }
        }
        
        return $rule_response;
    }
    
    private function isGenericResponse($response) {
        $generic_responses = [
            'Saya belum memahami pertanyaan Anda',
            'Maaf, saya perlu informasi lebih spesifik',
            'Saya siap membantu',
            'Silakan ajukan pertanyaan'
        ];
        
        foreach($generic_responses as $generic) {
            if(strpos($response, $generic) !== false) {
                return true;
            }
        }
        
        return false;
    }
    
    public function getAIStatus() {
        $status = [
            'rule_based' => 'active',
            'cloud_ai' => 'inactive',
            'gemini_configured' => false
        ];
        
        if($this->use_cloud_ai && $this->gemini_ai) {
            $status['cloud_ai'] = 'active';
            $status['gemini_configured'] = $this->gemini_ai->isConfigured();
        }
        
        return $status;
    }
}
?>
