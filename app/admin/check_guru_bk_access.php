<?php
// =====================================================
// CHECK GURU BK ACCESS - 2025
// =====================================================
// File ini untuk membatasi akses guru BK ke halaman tertentu
// 
// Halaman yang TIDAK boleh diakses guru BK:
// - Master Data (siswa.php, jurusan.php, kelas.php, ta.php, user.php, guru_bk.php)
// - Pengaturan Aplikasi (pengaturan_aplikasi.php)
// - Laporan umum (laporan.php, cetak_raport.php)
// - Poin Siswa (input_prestasi.php, input_pelanggaran.php, prestasi.php, pelanggaran.php)
// =====================================================

// Cek apakah user adalah guru BK
if(isset($_SESSION['level']) && $_SESSION['level'] == 'guru_bk') {
    
    // Daftar halaman yang tidak boleh diakses guru BK
    $restricted_pages = [
        // Master Data
        'siswa.php',
        'jurusan.php', 
        'kelas.php',
        'ta.php',
        'user.php',
        'guru_bk.php',
        
        // Pengaturan Aplikasi
        'pengaturan_aplikasi.php',
        
        // Laporan Umum (diblokir untuk guru BK)
        // 'laporan.php', // Diizinkan untuk guru BK
        // 'cetak_raport.php', // Diizinkan untuk guru BK
        
        // Poin Siswa (diizinkan untuk guru BK)
        // 'input_prestasi.php', // Diizinkan untuk guru BK
        // 'input_pelanggaran.php', // Diizinkan untuk guru BK
        // 'prestasi.php', // Diizinkan untuk guru BK
        // 'pelanggaran.php', // Diizinkan untuk guru BK
        
        // Halaman admin lainnya yang tidak relevan
        'index.php', // Dashboard admin utama
    ];
    
    // Dapatkan nama file yang sedang diakses
    $current_page = basename($_SERVER['PHP_SELF']);
    
    // Cek apakah halaman saat ini termasuk yang dilarang
    if(in_array($current_page, $restricted_pages)) {
        // Redirect ke dashboard guru BK dengan pesan error
        $_SESSION['error_message'] = 'Anda tidak memiliki akses ke halaman ini. Akses dibatasi untuk administrator.';
        header('location: guru_bk_dashboard.php');
        exit();
    }
}

// =====================================================
// FUNCTIONS UNTUK MEMBATASI AKSES
// =====================================================

function checkGuruBkAccess($page_name = null) {
    if(isset($_SESSION['level']) && $_SESSION['level'] == 'guru_bk') {
        
        // Jika tidak ada parameter page_name, gunakan current page
        if($page_name === null) {
            $page_name = basename($_SERVER['PHP_SELF']);
        }
        
        $restricted_pages = [
            'siswa.php', 'jurusan.php', 'kelas.php', 'ta.php', 'user.php', 'guru_bk.php',
            'pengaturan_aplikasi.php',
            // 'laporan.php', // Diizinkan untuk guru BK
            // 'cetak_raport.php', // Diizinkan untuk guru BK
            // 'input_prestasi.php', // Diizinkan untuk guru BK
            // 'input_pelanggaran.php', // Diizinkan untuk guru BK
            // 'prestasi.php', // Diizinkan untuk guru BK
            // 'pelanggaran.php', // Diizinkan untuk guru BK
            'index.php'
        ];
        
        if(in_array($page_name, $restricted_pages)) {
            $_SESSION['error_message'] = 'Akses ditolak: Halaman ini hanya untuk administrator.';
            header('location: guru_bk_dashboard.php');
            exit();
        }
    }
}

function isGuruBk() {
    return isset($_SESSION['level']) && $_SESSION['level'] == 'guru_bk';
}

function isAdministrator() {
    return isset($_SESSION['level']) && $_SESSION['level'] == 'administrator';
}

function showAccessDenied($message = 'Anda tidak memiliki akses ke halaman ini.') {
    $_SESSION['error_message'] = $message;
    if(isGuruBk()) {
        header('location: guru_bk_dashboard.php');
    } else {
        header('location: index.php');
    }
    exit();
}

// =====================================================
// AUTO-CHECK ACCESS (UNCOMMENT JIKA DIPERLUKAN)
// =====================================================
// Uncomment baris di bawah ini untuk auto-check di setiap halaman
// checkGuruBkAccess();
?>
