<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

// Include database connection and settings
require_once '../koneksi.php';
require_once '../functions_app_settings.php';
require_once '../functions_chatbot_settings.php';
require_once '../functions_ai_tier.php';

// Start session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Include Ollama AI integration
require_once 'ollama_ai.php';
require_once 'gemini_ai.php';

// Function to get cloud AI response
function getCloudAIResponse($message, $provider, $app_settings) {
    $api_key = getSetting($app_settings, 'chatbot_api_key', '');
    
    if(empty($api_key)) {
        throw new Exception("API key not configured");
    }
    
    switch($provider) {
        case 'gemini':
        case 'gemini-pro':
        case 'gemini-flash':
            $ai = new GeminiAI($api_key);
            return $ai->getResponse($message);
            
        case 'openai':
        case 'openai-gpt4':
            // TODO: Implement OpenAI integration
            throw new Exception("OpenAI integration not implemented yet");
            
        case 'huggingface':
            // TODO: Implement Hugging Face integration
            throw new Exception("Hugging Face integration not implemented yet");
            
        case 'ollama':
            // Use local Ollama
            $ollama_url = getSetting($app_settings, 'chatbot_ollama_url', 'http://localhost:11434');
            $ollama_model = getSetting($app_settings, 'chatbot_ollama_model', 'llama2:7b');
            $ai = new OllamaAI($ollama_url, $ollama_model);
            return $ai->getResponse($message);
            
        default:
            throw new Exception("Unknown AI provider: " . $provider);
    }
}

// Simple AI response system for ePoint Chatbot
class ePointChatbotAI {
    private $responses;
    
    public function __construct() {
        $this->initializeResponses();
    }
    
    private function initializeResponses() {
        $this->responses = [
            // Greetings
            'greeting' => [
                'patterns' => ['halo', 'hai', 'hello', 'hi', 'selamat', 'pagi', 'siang', 'sore', 'malam'],
                'responses' => [
                    'Halo! Selamat datang di ePoint Assistant. Saya siap membantu Anda dengan informasi tentang aplikasi ePoint dan layanan konseling.',
                    'Hai! Ada yang bisa saya bantu tentang ePoint?',
                    'Selamat datang! Saya ePoint Assistant, siap membantu Anda.'
                ]
            ],
            
            // About ePoint
            'about_epoint' => [
                'patterns' => ['apa itu epoint', 'epoint', 'aplikasi epoint', 'sistem epoint', 'apa itu sisbk', 'sisbk', 'aplikasi sisbk', 'sistem sisbk'],
                'responses' => [
                    'SISBK adalah sistem manajemen sekolah yang fokus pada layanan Bimbingan Konseling (BK). Aplikasi ini membantu mengelola kasus siswa, layanan BK, kunjungan rumah, dan laporan akademik.',
                    'SISBK adalah platform digital untuk manajemen layanan BK di sekolah, memudahkan guru BK dalam menangani kasus siswa dan memberikan layanan konseling yang terstruktur.'
                ]
            ],
            
            // BK Services
            'bk_services' => [
                'patterns' => ['layanan bk', 'bimbingan konseling', 'konseling', 'layanan konseling', 'bk'],
                'responses' => [
                    'Layanan BK di ePoint meliputi:\n• Konseling individual\n• Konseling kelompok\n• Konsultasi dengan orang tua\n• Kunjungan rumah\n• Asesmen psikologis\n• Follow-up kasus\n\nSemua layanan dapat dikelola melalui dashboard ePoint.',
                    'ePoint menyediakan berbagai layanan BK seperti konseling siswa, konsultasi orang tua, kunjungan rumah, dan pelaporan kasus secara terintegrasi.'
                ]
            ],
            
            // Case Management
            'case_management' => [
                'patterns' => ['kasus siswa', 'laporan kasus', 'manajemen kasus', 'kasus', 'laporan', 'manajemen kasus siswa'],
                'responses' => [
                    'Manajemen Kasus Siswa di SISBK:\n\n📝 **Cara Input Kasus:**\n1. Login sebagai Guru BK\n2. Masuk ke menu "Kasus Siswa"\n3. Klik "Tambah Kasus Baru"\n4. Isi data:\n   • Nama siswa dan kelas\n   • Jenis kasus (akademik, perilaku, dll)\n   • Deskripsi masalah\n   • Prioritas kasus\n5. Simpan dan lakukan follow-up\n\n📊 **Fitur Kasus:**\n• Tracking status kasus\n• Riwayat konseling\n• Laporan perkembangan\n• Notifikasi follow-up\n• Export laporan PDF',
                    'Sistem Kasus SISBK:\n\n🎯 **Jenis Kasus:**\n• Kasus Akademik (nilai rendah, bolos)\n• Kasus Perilaku (pelanggaran, konflik)\n• Kasus Psikologis (stress, depresi)\n• Kasus Sosial (keluarga, teman)\n\n📈 **Monitoring:**\n• Status: Baru, Proses, Selesai\n• Prioritas: Rendah, Sedang, Tinggi\n• Timeline dan deadline\n• Progress tracking\n• Laporan berkala'
                ]
            ],
            
            // Home Visit
            'home_visit' => [
                'patterns' => ['kunjungan rumah', 'home visit', 'kunjungan', 'visit'],
                'responses' => [
                    'Kunjungan rumah di ePoint:\n1. Buka menu "Kunjungan Rumah"\n2. Pilih siswa yang akan dikunjungi\n3. Isi jadwal dan tujuan kunjungan\n4. Upload foto dan dokumentasi\n5. Buat laporan hasil kunjungan\n\nFitur ini membantu melacak semua kunjungan yang dilakukan.',
                    'Kunjungan rumah adalah bagian penting dari layanan BK. Di ePoint, Anda dapat merencanakan, melacak, dan melaporkan semua kunjungan rumah dengan mudah.'
                ]
            ],
            
            // Reports
            'reports' => [
                'patterns' => ['laporan', 'report', 'raport', 'laporan akademik', 'laporan dan dokumentasi', 'dokumentasi'],
                'responses' => [
                    'Laporan dan Dokumentasi SISBK:\n\n📊 **Jenis Laporan:**\n• Laporan Kasus Siswa (PDF/Excel)\n• Laporan Layanan BK\n• Laporan Kunjungan Rumah\n• Laporan Poin Siswa\n• Laporan Statistik BK\n• Laporan Tahunan\n\n📁 **Cara Generate Laporan:**\n1. Login sebagai Guru BK/Admin\n2. Masuk ke menu "Laporan"\n3. Pilih jenis laporan\n4. Atur filter (tanggal, kelas, status)\n5. Klik "Generate" dan download\n\n💾 **Format Export:**\n• PDF untuk presentasi\n• Excel untuk analisis data\n• Print langsung',
                    'Dokumentasi SISBK:\n\n📋 **Dokumen yang Dapat Dibuat:**\n• Form Konseling Siswa\n• Laporan Kunjungan Rumah\n• Surat Panggilan Orang Tua\n• Rekomendasi Siswa\n• Evaluasi Program BK\n\n🔧 **Fitur Dokumentasi:**\n• Template otomatis\n• Digital signature\n• Backup otomatis\n• Search dan filter\n• Export massal\n• Print dengan header sekolah'
                ]
            ],
            
            // Technical Support
            'technical' => [
                'patterns' => ['error', 'masalah', 'troubleshoot', 'tidak bisa', 'gagal', 'bug', 'troubleshooting teknis'],
                'responses' => [
                    'Troubleshooting SISBK:\n\n🔧 **Masalah Umum:**\n• Login gagal → Cek username/password\n• Halaman blank → Refresh browser\n• Upload gagal → Cek ukuran file\n• Print error → Cek printer driver\n• Slow loading → Cek koneksi internet\n\n💡 **Solusi Cepat:**\n1. Clear cache browser (Ctrl+F5)\n2. Gunakan browser terbaru\n3. Nonaktifkan ad blocker\n4. Cek koneksi internet\n5. Restart browser\n\n📞 **Jika Masalah Berlanjut:**\n• Hubungi IT Support sekolah\n• Screenshot error message\n• Catat langkah yang menyebabkan error',
                    'Panduan Troubleshooting SISBK:\n\n🌐 **Masalah Koneksi:**\n• Pastikan internet stabil (min 2 Mbps)\n• Jangan gunakan VPN\n• Cek firewall sekolah\n• Coba browser lain\n\n💻 **Masalah Browser:**\n• Update ke versi terbaru\n• Enable JavaScript\n• Clear cookies dan cache\n• Disable extensions\n\n📱 **Masalah Mobile:**\n• Gunakan browser mobile (Chrome, Safari)\n• Rotate screen jika perlu\n• Pastikan storage cukup\n• Update OS jika perlu'
                ]
            ],
            
            // Login
            'login' => [
                'patterns' => ['cara login', 'login', 'masuk', 'cara masuk', 'login ke sisbk', 'masuk ke sisbk', 'cara login ke sisbk'],
                'responses' => [
                    'Cara login ke SISBK:\n\n1. Buka website SISBK di browser\n2. Pilih jenis pengguna:\n   • Siswa: Klik "Login Siswa"\n   • Admin: Klik "Login Admin" \n   • Guru BK: Klik "Login Guru BK"\n3. Masukkan kredensial:\n   • Siswa: NIS dan password\n   • Admin: Username dan password admin\n   • Guru BK: Username dan password guru\n4. Klik tombol "Login"\n\nJika lupa password, hubungi administrator sekolah.',
                    'Panduan login SISBK:\n\n• Pastikan koneksi internet stabil\n• Gunakan browser terbaru (Chrome, Firefox, Safari)\n• Jangan gunakan mode incognito\n• Jika gagal login, coba refresh halaman\n• Hubungi IT support jika masalah berlanjut\n\nSetelah login berhasil, Anda akan diarahkan ke dashboard sesuai peran Anda.'
                ]
            ],
            
            // Dashboard
            'dashboard' => [
                'patterns' => ['dashboard', 'fitur dashboard', 'menu utama', 'halaman utama', 'fitur dashboard sisbk', 'dashboard sisbk'],
                'responses' => [
                    'Dashboard SISBK berisi:\n\n📊 **Menu Utama:**\n• Dashboard - Overview sistem\n• Master Data - Kelola data dasar\n• Poin Siswa - Manajemen poin\n• Konseling BK - Layanan konseling\n• Layanan BK - Manajemen layanan\n• Laporan - Berbagai laporan\n\n🎯 **Fitur Dashboard:**\n• Statistik real-time\n• Grafik perkembangan siswa\n• Notifikasi terbaru\n• Quick access menu\n• Monitoring kasus aktif',
                    'Fitur Dashboard SISBK:\n\n👥 **Untuk Admin:**\n• Kelola master data (siswa, guru, kelas)\n• Monitoring semua aktivitas\n• Generate laporan komprehensif\n• Manajemen user dan akses\n\n👨‍🏫 **Untuk Guru BK:**\n• Kelola kasus siswa\n• Input layanan BK\n• Kunjungan rumah\n• Laporan konseling\n• Kalender jadwal\n\n👨‍🎓 **Untuk Siswa:**\n• Lihat poin dan status\n• Akses layanan BK\n• Riwayat konseling\n• Notifikasi penting'
                ]
            ],
            
            // Help
            'help' => [
                'patterns' => ['bantuan', 'help', 'tolong', 'cara', 'panduan'],
                'responses' => [
                    'Saya siap membantu! Anda bisa bertanya tentang:\n• Fitur-fitur SISBK\n• Cara menggunakan layanan BK\n• Manajemen kasus siswa\n• Laporan dan dokumentasi\n• Troubleshooting teknis\n\nApa yang ingin Anda ketahui?',
                    'SISBK Assistant siap membantu Anda dengan:\n• Panduan penggunaan aplikasi\n• Informasi layanan BK\n• Tips konseling siswa\n• Solusi masalah teknis\n\nSilakan ajukan pertanyaan Anda!'
                ]
            ]
        ];
    }
    
    public function getResponse($message) {
        $message = strtolower(trim($message));
        
        // Check for exact matches first
        foreach ($this->responses as $category => $data) {
            foreach ($data['patterns'] as $pattern) {
                if (strpos($message, $pattern) !== false) {
                    return $this->getRandomResponse($data['responses']);
                }
            }
        }
        
        // Default responses for unmatched queries
        $defaultResponses = [
            'Saya belum memahami pertanyaan Anda. Bisa dijelaskan lebih detail?',
            'Maaf, saya perlu informasi lebih spesifik untuk membantu Anda. Coba tanyakan tentang fitur ePoint atau layanan BK.',
            'Saya siap membantu! Coba tanyakan tentang:\n• Apa itu ePoint?\n• Layanan BK\n• Kasus siswa\n• Laporan\n• Kunjungan rumah',
            'Silakan ajukan pertanyaan yang lebih spesifik tentang ePoint atau layanan konseling.'
        ];
        
        return $defaultResponses[array_rand($defaultResponses)];
    }
    
    private function getRandomResponse($responses) {
        return $responses[array_rand($responses)];
    }
}

// Handle POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (isset($input['message'])) {
        // Get chatbot configuration
        $chatbot_config = getChatbotConfig($koneksi);
        
        // Get app settings for AI configuration
        $app_settings = getAppSettings($koneksi);
        
        // Check if chatbot is enabled
        if($chatbot_config['enabled'] != '1') {
            echo json_encode([
                'error' => 'Chatbot is disabled'
            ]);
            exit;
        }
        
        // Check FAQ from database first
        $faq = $chatbot_config['faq'];
        $response = null;
        
        // Check FAQ for exact matches
        foreach($faq as $item) {
            $question = strtolower($item['question']);
            $message_lower = strtolower($input['message']);
            
            if(strpos($question, $message_lower) !== false || strpos($message_lower, $question) !== false) {
                $response = $item['answer'];
                break;
            }
        }
        
               // If no FAQ match, use AI system
               if(!$response) {
                   // Get user tier and AI provider
                   $user_id = $_SESSION['id'] ?? null; // Get from session if available
                   $user_tier = getUserTier($koneksi, $user_id);
                   $ai_provider = getAIProviderForTier($koneksi, $user_tier);
                   
                   // Use default provider if no user tier
                   if(!$ai_provider) {
                       $ai_provider = getSetting($app_settings, 'chatbot_ai_provider', 'gemini-pro');
                   }
                   
                   $cloud_enabled = getSetting($app_settings, 'chatbot_cloud_enabled', '1');
                   $fallback_enabled = getSetting($app_settings, 'chatbot_fallback_enabled', '1');
                   
                   if($cloud_enabled == '1') {
                       // Skip usage limits check for anonymous users
                       if($user_id) {
                           $usage_check = checkAIUsageLimit($koneksi, $user_id, $user_tier);
                           
                           if($usage_check['status'] === 'limit_exceeded') {
                               // Try fallback tier
                               $fallback = getFallbackProvider($koneksi, $user_tier);
                               if($fallback) {
                                   $ai_provider = $fallback['provider'];
                                   $user_tier = $fallback['tier'];
                               } else {
                                   // Use rule-based as final fallback
                                   $ai = new ePointChatbotAI();
                                   $response = $ai->getResponse($input['message']);
                                   $response = $response . "\n\n⚠️ *AI limit reached. Using basic responses.*";
                               }
                           }
                       }
                       
                       if(!$response) {
                           try {
                               error_log("Attempting AI call with provider: " . $ai_provider);
                               error_log("Cloud AI enabled: " . $cloud_enabled);
                               error_log("Message: " . $input['message']);
                               $response = getCloudAIResponse($input['message'], $ai_provider, $app_settings);
                               error_log("AI Response received: " . substr($response, 0, 100));
                               
                               // Record usage
                               if($user_id) {
                                   recordAIUsage($koneksi, $user_id, $user_tier, $ai_provider);
                               }
                           } catch (Exception $e) {
                               error_log("Cloud AI Error: " . $e->getMessage());
                               $response = null;
                           }
                       }
                   }
                   
                   // Fallback to rule-based if cloud AI fails or disabled
                   if(!$response && $fallback_enabled == '1') {
                       $ai = new ePointChatbotAI();
                       $response = $ai->getResponse($input['message']);
                   }
               }
        
        // Add app information to response if needed
        $app_info = $chatbot_config['app_info'];
        if(strpos(strtolower($input['message']), 'epoint') !== false || strpos(strtolower($input['message']), 'aplikasi') !== false) {
            $response = str_replace('ePoint', $app_info['app_name'], $response);
        }
        
        echo json_encode([
            'response' => $response,
            'timestamp' => date('Y-m-d H:i:s'),
            'ai_type' => 'hybrid',
            'chatbot_name' => $chatbot_config['name']
        ]);
    } else {
        echo json_encode([
            'error' => 'Message not provided'
        ]);
    }
} else {
    echo json_encode([
        'error' => 'Method not allowed'
    ]);
}
?>
