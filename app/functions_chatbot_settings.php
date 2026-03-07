<?php
// Fungsi untuk pengaturan Chatbot ePoint
// Terintegrasi dengan sistem pengaturan aplikasi

// Fungsi untuk mendapatkan pengaturan chatbot
if (!function_exists('getChatbotSettings')) {
    function getChatbotSettings($koneksi) {
        $settings = array();
        $query = mysqli_query($koneksi, "SELECT setting_key, setting_value FROM app_settings WHERE setting_key LIKE 'chatbot_%'");
        while($row = mysqli_fetch_assoc($query)) {
            $settings[$row['setting_key']] = $row['setting_value'];
        }
        return $settings;
    }
}

// Fungsi untuk mendapatkan pengaturan chatbot dengan default
if (!function_exists('getChatbotSetting')) {
    function getChatbotSetting($settings, $key, $default = '') {
        return isset($settings[$key]) ? $settings[$key] : $default;
    }
}

// Fungsi untuk mendapatkan nama chatbot
if (!function_exists('getChatbotName')) {
    function getChatbotName($settings, $default = 'ePoint Assistant') {
        return getChatbotSetting($settings, 'chatbot_name', $default);
    }
}

// Fungsi untuk mendapatkan deskripsi chatbot
if (!function_exists('getChatbotDescription')) {
    function getChatbotDescription($settings, $default = 'Saya siap membantu Anda dengan informasi tentang aplikasi ePoint dan layanan konseling.') {
        return getChatbotSetting($settings, 'chatbot_description', $default);
    }
}

// Fungsi untuk mendapatkan status chatbot
if (!function_exists('getChatbotStatus')) {
    function getChatbotStatus($settings, $default = 'Online') {
        return getChatbotSetting($settings, 'chatbot_status', $default);
    }
}

// Fungsi untuk mendapatkan avatar chatbot
if (!function_exists('getChatbotAvatar')) {
    function getChatbotAvatar($settings, $default = 'fas fa-robot') {
        return getChatbotSetting($settings, 'chatbot_avatar', $default);
    }
}

// Fungsi untuk mendapatkan warna tema chatbot
if (!function_exists('getChatbotTheme')) {
    function getChatbotTheme($settings, $default = 'modern') {
        return getChatbotSetting($settings, 'chatbot_theme', $default);
    }
}

// Fungsi untuk mendapatkan posisi chatbot
if (!function_exists('getChatbotPosition')) {
    function getChatbotPosition($settings, $default = 'bottom-right') {
        return getChatbotSetting($settings, 'chatbot_position', $default);
    }
}

// Fungsi untuk mendapatkan quick actions
if (!function_exists('getChatbotQuickActions')) {
    function getChatbotQuickActions($settings, $default = array()) {
        $actions = getChatbotSetting($settings, 'chatbot_quick_actions', '');
        if(!empty($actions)) {
            return json_decode($actions, true);
        }
        return $default;
    }
}

// Fungsi untuk mendapatkan welcome message
if (!function_exists('getChatbotWelcomeMessage')) {
    function getChatbotWelcomeMessage($settings, $default = 'Halo! Saya siap membantu Anda dengan informasi tentang aplikasi ePoint dan layanan konseling. Ada yang bisa saya bantu?') {
        return getChatbotSetting($settings, 'chatbot_welcome_message', $default);
    }
}

// Fungsi untuk mendapatkan informasi aplikasi untuk chatbot
if (!function_exists('getChatbotAppInfo')) {
    function getChatbotAppInfo($settings) {
        $app_info = array(
            'app_name' => getSetting($settings, 'app_name', 'ePoint'),
            'app_description' => getSetting($settings, 'app_description', 'Sistem manajemen sekolah'),
            'app_author' => getSetting($settings, 'app_author', 'MA YASMU'),
            'app_version' => getSetting($settings, 'app_version', '1.0'),
            'app_features' => getChatbotSetting($settings, 'chatbot_app_features', 'Manajemen Point, Layanan BK, Kasus Siswa, Kunjungan Rumah, Laporan Real-time, Mobile Friendly')
        );
        return $app_info;
    }
}

// Fungsi untuk mendapatkan FAQ chatbot
if (!function_exists('getChatbotFAQ')) {
    function getChatbotFAQ($settings, $default = array()) {
        $faq = getChatbotSetting($settings, 'chatbot_faq', '');
        if(!empty($faq)) {
            return json_decode($faq, true);
        }
        return $default;
    }
}

// Fungsi untuk mendapatkan kontak support
if (!function_exists('getChatbotSupport')) {
    function getChatbotSupport($settings) {
        $support = array(
            'email' => getSetting($settings, 'app_email', ''),
            'phone' => getSetting($settings, 'app_phone', ''),
            'website' => getSetting($settings, 'app_website', ''),
            'address' => getSetting($settings, 'app_address', '')
        );
        return $support;
    }
}

// Fungsi untuk menyimpan pengaturan chatbot
if (!function_exists('saveChatbotSettings')) {
    function saveChatbotSettings($koneksi, $settings) {
        foreach($settings as $key => $value) {
            // Cek apakah setting sudah ada
            $check = mysqli_query($koneksi, "SELECT setting_key FROM app_settings WHERE setting_key = '$key'");
            if(mysqli_num_rows($check) > 0) {
                // Update existing setting
                mysqli_query($koneksi, "UPDATE app_settings SET setting_value = '$value' WHERE setting_key = '$key'");
            } else {
                // Insert new setting
                mysqli_query($koneksi, "INSERT INTO app_settings (setting_key, setting_value) VALUES ('$key', '$value')");
            }
        }
        return true;
    }
}

// Fungsi untuk mendapatkan konfigurasi chatbot lengkap
if (!function_exists('getChatbotConfig')) {
    function getChatbotConfig($koneksi) {
        $app_settings = getAppSettings($koneksi);
        $chatbot_settings = getChatbotSettings($koneksi);
        
        $app_name = getSetting($app_settings, 'app_name', 'ePoint');
        
        $config = array(
            'enabled' => getChatbotSetting($chatbot_settings, 'chatbot_enabled', '1'),
            'name' => getChatbotName($chatbot_settings),
            'description' => getChatbotDescription($chatbot_settings),
            'status' => getChatbotStatus($chatbot_settings),
            'avatar' => getChatbotAvatar($chatbot_settings),
            'theme' => getChatbotTheme($chatbot_settings),
            'position' => getChatbotPosition($chatbot_settings),
            'welcome_message' => getChatbotWelcomeMessage($chatbot_settings),
            'quick_actions' => getChatbotQuickActions($chatbot_settings),
            'app_info' => getChatbotAppInfo($app_settings),
            'faq' => getChatbotFAQ($chatbot_settings),
            'support' => getChatbotSupport($app_settings),
            'show_notification' => getChatbotSetting($chatbot_settings, 'chatbot_show_notification', '1'),
            'auto_open' => getChatbotSetting($chatbot_settings, 'chatbot_auto_open', '0'),
            'ollama_enabled' => getChatbotSetting($chatbot_settings, 'chatbot_ollama_enabled', '0'),
            'ollama_url' => getChatbotSetting($chatbot_settings, 'chatbot_ollama_url', 'http://localhost:11434'),
            'ollama_model' => getChatbotSetting($chatbot_settings, 'chatbot_ollama_model', 'llama2:7b')
        );
        
        // Clean hardcoded "ePoint" from all data
        $config['quick_actions'] = cleanChatbotData($config['quick_actions'], $app_name);
        $config['faq'] = cleanChatbotData($config['faq'], $app_name);
        $config['welcome_message'] = cleanChatbotData($config['welcome_message'], $app_name);
        
        return $config;
    }
}

// Fungsi untuk mendapatkan default quick actions berdasarkan aplikasi
if (!function_exists('getDefaultQuickActions')) {
    function getDefaultQuickActions($app_settings) {
        $app_name = getSetting($app_settings, 'app_name', 'ePoint');
        $default_actions = array(
            "Apa itu $app_name?",
            "Cara login ke $app_name",
            "Fitur dashboard $app_name",
            "Manajemen kasus siswa",
            "Laporan dan dokumentasi",
            "Troubleshooting teknis"
        );
        return $default_actions;
    }
}

// Fungsi untuk membersihkan data chatbot dari hardcoded "ePoint"
if (!function_exists('cleanChatbotData')) {
    function cleanChatbotData($data, $app_name) {
        if(is_array($data)) {
            foreach($data as $key => $value) {
                if(is_array($value)) {
                    $data[$key] = cleanChatbotData($value, $app_name);
                } else {
                    $data[$key] = str_replace('ePoint', $app_name, $value);
                }
            }
        } else {
            $data = str_replace('ePoint', $app_name, $data);
        }
        return $data;
    }
}

// Fungsi untuk mendapatkan default FAQ
if (!function_exists('getDefaultFAQ')) {
    function getDefaultFAQ($app_settings) {
        $app_name = getSetting($app_settings, 'app_name', 'ePoint');
        $default_faq = array(
            array(
                'question' => "Apa itu $app_name?",
                'answer' => "$app_name adalah sistem manajemen sekolah yang membantu mengelola point siswa, layanan BK, dan laporan akademik."
            ),
            array(
                'question' => "Bagaimana cara login ke $app_name?",
                'answer' => "Gunakan username dan password yang diberikan oleh administrator. Pilih jenis login sesuai dengan peran Anda (Siswa, Admin, atau Guru BK)."
            ),
            array(
                'question' => "Apa saja fitur utama $app_name?",
                'answer' => "Fitur utama meliputi: Manajemen Point, Layanan BK, Kasus Siswa, Kunjungan Rumah, Laporan Real-time, dan Mobile Friendly."
            )
        );
        return $default_faq;
    }
}
?>
