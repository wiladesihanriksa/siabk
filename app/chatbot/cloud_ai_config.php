<?php
// Cloud AI Configuration for SISBK Chatbot

// AI Provider Configuration
class CloudAIConfig {
    // Google Gemini Configuration
    const GEMINI_API_KEY = 'YOUR_GEMINI_API_KEY'; // Replace with your actual API key
    const GEMINI_MODEL = 'gemini-pro';
    const GEMINI_MAX_TOKENS = 500;
    
    // OpenAI Configuration (Alternative)
    const OPENAI_API_KEY = 'YOUR_OPENAI_API_KEY'; // Replace with your actual API key
    const OPENAI_MODEL = 'gpt-3.5-turbo';
    
    // Hugging Face Configuration (Free Alternative)
    const HUGGINGFACE_API_KEY = 'YOUR_HF_API_KEY'; // Optional
    const HUGGINGFACE_MODEL = 'microsoft/DialoGPT-medium';
    
    // AI Settings
    const USE_CLOUD_AI = true; // Set to false to use only rule-based
    const AI_PROVIDER = 'gemini'; // Options: 'gemini', 'openai', 'huggingface', 'ollama'
    const FALLBACK_TO_RULES = true; // Fallback to rule-based if cloud AI fails
    
    // Rate Limiting
    const MAX_REQUESTS_PER_MINUTE = 15; // Gemini free tier limit
    const REQUEST_TIMEOUT = 30; // seconds
    
    public static function getConfig() {
        return [
            'use_cloud_ai' => self::USE_CLOUD_AI,
            'provider' => self::AI_PROVIDER,
            'fallback' => self::FALLBACK_TO_RULES,
            'rate_limit' => self::MAX_REQUESTS_PER_MINUTE,
            'timeout' => self::REQUEST_TIMEOUT,
            'api_keys' => [
                'gemini' => self::GEMINI_API_KEY,
                'openai' => self::OPENAI_API_KEY,
                'huggingface' => self::HUGGINGFACE_API_KEY
            ]
        ];
    }
    
    public static function isConfigured() {
        $config = self::getConfig();
        
        switch($config['provider']) {
            case 'gemini':
                return $config['api_keys']['gemini'] !== 'YOUR_GEMINI_API_KEY';
            case 'openai':
                return $config['api_keys']['openai'] !== 'YOUR_OPENAI_API_KEY';
            case 'huggingface':
                return !empty($config['api_keys']['huggingface']);
            default:
                return false;
        }
    }
}
?>
