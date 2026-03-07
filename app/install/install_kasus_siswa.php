<?php
// File untuk menginstall tabel kasus siswa
include 'koneksi.php';

echo "<h2>Installing Kasus Siswa Database...</h2>";

// SQL untuk membuat tabel
$sql_queries = array(
    // Tabel kasus_siswa
    "CREATE TABLE IF NOT EXISTS `kasus_siswa` (
      `kasus_id` int NOT NULL AUTO_INCREMENT,
      `kasus_kode` varchar(20) NOT NULL,
      `siswa_id` int NOT NULL,
      `tanggal_pelaporan` date NOT NULL,
      `sumber_kasus` enum('Wali Kelas','Guru Mapel','Orang Tua','Inisiatif Siswa','Teman','Temuan Guru BK') NOT NULL,
      `kategori_masalah` enum('Pribadi','Sosial','Belajar','Karir') NOT NULL,
      `judul_kasus` varchar(255) NOT NULL,
      `deskripsi_awal` text,
      `status_kasus` enum('Baru','Dalam Proses','Selesai/Tuntas','Dirujuk/Alih Tangan Kasus') NOT NULL DEFAULT 'Baru',
      `guru_bk_id` int NOT NULL,
      `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
      `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
      PRIMARY KEY (`kasus_id`),
      UNIQUE KEY `kasus_kode` (`kasus_kode`),
      KEY `fk_kasus_siswa` (`siswa_id`),
      KEY `fk_kasus_guru_bk` (`guru_bk_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci",
    
    // Tabel jurnal_kasus
    "CREATE TABLE IF NOT EXISTS `jurnal_kasus` (
      `jurnal_id` int NOT NULL AUTO_INCREMENT,
      `kasus_id` int NOT NULL,
      `tanggal_konseling` date NOT NULL,
      `uraian_sesi` text NOT NULL,
      `analisis_diagnosis` text,
      `tindakan_intervensi` text,
      `rencana_tindak_lanjut` text,
      `lampiran_file` varchar(255) DEFAULT NULL,
      `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
      `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
      PRIMARY KEY (`jurnal_id`),
      KEY `fk_jurnal_kasus` (`kasus_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci",
    
    // Tabel notifikasi_rtl
    "CREATE TABLE IF NOT EXISTS `notifikasi_rtl` (
      `notif_id` int NOT NULL AUTO_INCREMENT,
      `jurnal_id` int NOT NULL,
      `tanggal_reminder` date NOT NULL,
      `pesan_reminder` text NOT NULL,
      `status_reminder` enum('Belum','Sudah','Dibatalkan') NOT NULL DEFAULT 'Belum',
      `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
      PRIMARY KEY (`notif_id`),
      KEY `fk_notif_jurnal` (`jurnal_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci",
    
    // Tabel audit_kasus
    "CREATE TABLE IF NOT EXISTS `audit_kasus` (
      `audit_id` int NOT NULL AUTO_INCREMENT,
      `kasus_id` int NOT NULL,
      `user_id` int NOT NULL,
      `action` enum('CREATE','READ','UPDATE','DELETE') NOT NULL,
      `description` text,
      `ip_address` varchar(45),
      `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
      PRIMARY KEY (`audit_id`),
      KEY `fk_audit_kasus` (`kasus_id`),
      KEY `fk_audit_user` (`user_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci"
);

$success_count = 0;
$error_count = 0;

foreach($sql_queries as $sql) {
    if(mysqli_query($koneksi, $sql)) {
        echo "<p style='color: green;'>✓ Tabel berhasil dibuat</p>";
        $success_count++;
    } else {
        echo "<p style='color: red;'>✗ Error: " . mysqli_error($koneksi) . "</p>";
        $error_count++;
    }
}

// Buat folder uploads jika belum ada
if(!is_dir('uploads/kasus')) {
    if(mkdir('uploads/kasus', 0777, true)) {
        echo "<p style='color: green;'>✓ Folder uploads/kasus berhasil dibuat</p>";
    } else {
        echo "<p style='color: red;'>✗ Gagal membuat folder uploads/kasus</p>";
    }
} else {
    echo "<p style='color: blue;'>ℹ Folder uploads/kasus sudah ada</p>";
}

echo "<h3>Hasil Instalasi:</h3>";
echo "<p>Berhasil: $success_count tabel</p>";
echo "<p>Error: $error_count tabel</p>";

if($error_count == 0) {
    echo "<p style='color: green; font-weight: bold;'>✓ Instalasi berhasil! Fitur Kasus Siswa siap digunakan.</p>";
    echo "<p><a href='admin/kasus_siswa.php'>Klik di sini untuk mengakses Data Kasus Siswa</a></p>";
} else {
    echo "<p style='color: red; font-weight: bold;'>✗ Ada error dalam instalasi. Silakan periksa database.</p>";
}
?>
