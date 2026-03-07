<?php
include '../koneksi.php';
session_start();

// Cek session - izinkan administrator dan guru_bk
if(!isset($_SESSION['level']) || ($_SESSION['level'] != "administrator" && $_SESSION['level'] != "guru_bk")){
    header("location:../admin.php?alert=belum_login");
    exit();
}

// Ambil ID dari URL
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if($id <= 0) {
    header("location:kunjungan_rumah.php?alert=gagal&pesan=ID tidak valid");
    exit();
}

// Mulai transaksi
mysqli_begin_transaction($koneksi);

try {
    // Ambil data kunjungan untuk audit
    $query_kunjungan = "SELECT k.*, s.siswa_nama, u.user_nama as petugas_nama 
                       FROM kunjungan_rumah k 
                       LEFT JOIN siswa s ON k.siswa_id = s.siswa_id 
                       LEFT JOIN user u ON k.petugas_bk_id = u.user_id 
                       WHERE k.kunjungan_id = '$id'";
    $result_kunjungan = mysqli_query($koneksi, $query_kunjungan);
    
    if(mysqli_num_rows($result_kunjungan) == 0) {
        throw new Exception("Data kunjungan tidak ditemukan");
    }
    
    $data_kunjungan = mysqli_fetch_assoc($result_kunjungan);
    
    // Set user IP untuk audit trail
    $user_ip = $_SERVER['REMOTE_ADDR'];
    mysqli_query($koneksi, "SET @user_ip = '$user_ip'");
    
    // Ambil data lampiran foto untuk dihapus
    $query_lampiran = "SELECT * FROM lampiran_kunjungan WHERE kunjungan_id = '$id'";
    $result_lampiran = mysqli_query($koneksi, $query_lampiran);
    
    // Hapus file lampiran dari server
    while($lampiran = mysqli_fetch_assoc($result_lampiran)) {
        $file_path = '../' . $lampiran['path_file'];
        if(file_exists($file_path)) {
            unlink($file_path);
        }
    }
    
    // Hapus data lampiran dari database
    $query_delete_lampiran = "DELETE FROM lampiran_kunjungan WHERE kunjungan_id = '$id'";
    mysqli_query($koneksi, $query_delete_lampiran);
    
    // Insert audit trail sebelum menghapus
    $query_audit = "INSERT INTO audit_kunjungan (kunjungan_id, user_id, action, description, ip_address) 
                    VALUES ('$id', '{$_SESSION['id']}', 'DELETE', 'Kunjungan rumah dihapus: {$data_kunjungan['kunjungan_kode']} - {$data_kunjungan['siswa_nama']}', '$user_ip')";
    mysqli_query($koneksi, $query_audit);
    
    // Hapus data kunjungan
    $query_delete = "DELETE FROM kunjungan_rumah WHERE kunjungan_id = '$id'";
    $result_delete = mysqli_query($koneksi, $query_delete);
    
    if(!$result_delete) {
        throw new Exception("Gagal menghapus data kunjungan: " . mysqli_error($koneksi));
    }
    
    // Commit transaksi
    mysqli_commit($koneksi);
    
    header("location:kunjungan_rumah.php?alert=sukses&pesan=Kunjungan rumah berhasil dihapus");
    exit();
    
} catch(Exception $e) {
    // Rollback transaksi
    mysqli_rollback($koneksi);
    
    header("location:kunjungan_rumah.php?alert=gagal&pesan=" . urlencode($e->getMessage()));
    exit();
}
?>
