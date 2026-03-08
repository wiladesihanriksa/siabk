<?php
// Fungsi untuk mengambil pengaturan aplikasi
if (!function_exists('getAppSettings')) {
    function getAppSettings($koneksi) {
        $settings = array();
        $query = mysqli_query($koneksi, "SELECT setting_key, setting_value FROM app_settings");
        while($row = mysqli_fetch_assoc($query)) {
            $settings[$row['setting_key']] = $row['setting_value'];
        }
        return $settings;
    }
}

// Fungsi Helper untuk mendapatkan Base URL Supabase
if (!function_exists('getSupabaseBaseUrl')) {
    function getSupabaseBaseUrl() {
        $url = getenv('SUPABASE_URL');
        $bucket = getenv('SUPABASE_BUCKET');
        if (!$url || !$bucket) return null;
        return rtrim($url, '/') . "/storage/v1/object/public/" . $bucket . "/";
    }
}

// Fungsi untuk mendapatkan nilai pengaturan dengan default
if (!function_exists('getSetting')) {
    function getSetting($settings, $key, $default = '') {
        return isset($settings[$key]) ? $settings[$key] : $default;
    }
}

// Fungsi untuk menampilkan logo aplikasi
if (!function_exists('getAppLogo')) {
    function getAppLogo($settings, $default = 'gambar/sistem/user.png') {
        $logo = getSetting($settings, 'app_logo');
        $baseUrl = getSupabaseBaseUrl();
        
        if(!empty($logo)) {
            // Jika database menyimpan URL lengkap, langsung kembalikan
            if (strpos($logo, 'http') === 0) return $logo;
            
            // Jika menggunakan Supabase Cloud
            if ($baseUrl) return $baseUrl . 'gambar/sistem/' . $logo;
            
            // Fallback Lokal (untuk development)
            return 'gambar/sistem/' . $logo;
        }
        return $baseUrl ? $baseUrl . $default : $default;
    }
}

// Fungsi untuk menampilkan favicon
if (!function_exists('getAppFavicon')) {
    function getAppFavicon($settings, $default = '') {
        $favicon = getSetting($settings, 'app_favicon');
        $baseUrl = getSupabaseBaseUrl();

        if (!empty($favicon)) {
            if (strpos($favicon, 'http') === 0) return $favicon;
            if ($baseUrl) return $baseUrl . 'gambar/sistem/' . $favicon;
            return 'gambar/sistem/' . $favicon;
        }
        return $default;
    }
}

// Fungsi untuk menampilkan logo login
if (!function_exists('getLoginLogo')) {
    function getLoginLogo($settings, $default = 'gambar/sistem/user.png') {
        $logo = getSetting($settings, 'login_logo');
        $baseUrl = getSupabaseBaseUrl();

        if(!empty($logo)) {
            if (strpos($logo, 'http') === 0) return $logo;
            if ($baseUrl) return $baseUrl . 'gambar/sistem/' . $logo;
            return 'gambar/sistem/' . $logo;
        }
        return $baseUrl ? $baseUrl . $default : $default;
    }
}
?>