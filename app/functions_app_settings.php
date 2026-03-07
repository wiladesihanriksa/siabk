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
        if(!empty($logo)) {
            $logo_path = 'gambar/sistem/' . $logo;
            // Cek apakah file ada tanpa menggunakan file_exists untuk menghindari open_basedir restriction
            if(@fopen($logo_path, 'r')) {
                fclose(@fopen($logo_path, 'r'));
                return $logo_path;
            }
        }
        return $default;
    }
}

// Fungsi untuk menampilkan favicon
if (!function_exists('getAppFavicon')) {
    function getAppFavicon($settings, $default = '') {
        $favicon = getSetting($settings, 'app_favicon');

        // 1. Jika ada setting favicon, coba pakai dari folder gambar/sistem
        if (!empty($favicon)) {
            $favicon_path = 'gambar/sistem/' . $favicon;
            if (file_exists($favicon_path)) {
                return $favicon_path;
            }
        }

        // 2. Fallback ke favicon di root project jika ada
        if (file_exists('favicon.ico')) {
            return 'favicon.ico';
        }

        // 3. Terakhir, gunakan default (jika dikirim dari pemanggil)
        return $default;
    }
}

// Fungsi untuk menampilkan logo login
if (!function_exists('getLoginLogo')) {
    function getLoginLogo($settings, $default = 'gambar/sistem/user.png') {
        $logo = getSetting($settings, 'login_logo');
        if(!empty($logo)) {
            $logo_path = 'gambar/sistem/' . $logo;
            // Cek apakah file ada tanpa menggunakan file_exists untuk menghindari open_basedir restriction
            if(@fopen($logo_path, 'r')) {
                fclose(@fopen($logo_path, 'r'));
                return $logo_path;
            }
        }
        return $default;
    }
}
?>
