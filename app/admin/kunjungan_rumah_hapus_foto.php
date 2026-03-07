<?php
include '../koneksi.php';
session_start();

// Cek session - izinkan administrator dan guru_bk
if(!isset($_SESSION['level']) || ($_SESSION['level'] != "administrator" && $_SESSION['level'] != "guru_bk")){
    header("location:../admin.php?alert=belum_login");
    exit();
}

// Ambil ID dari URL
$lampiran_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$kunjungan_id = isset($_GET['kunjungan_id']) ? intval($_GET['kunjungan_id']) : 0;

if($lampiran_id <= 0 || $kunjungan_id <= 0) {
    header("location:kunjungan_rumah_edit.php?id=$kunjungan_id&alert=gagal&pesan=ID tidak valid");
    exit();
}

try {
    // Ambil data lampiran
    $query_lampiran = "SELECT * FROM lampiran_kunjungan WHERE lampiran_id = '$lampiran_id' AND kunjungan_id = '$kunjungan_id'";
    $result_lampiran = mysqli_query($koneksi, $query_lampiran);
    
    if(mysqli_num_rows($result_lampiran) == 0) {
        throw new Exception("Data lampiran tidak ditemukan");
    }
    
    $lampiran = mysqli_fetch_assoc($result_lampiran);
    
    // Hapus file dari server
    $file_path = '../' . $lampiran['path_file'];
    if(file_exists($file_path)) {
        unlink($file_path);
    }
    
    // Hapus data dari database
    $query_delete = "DELETE FROM lampiran_kunjungan WHERE lampiran_id = '$lampiran_id'";
    $result_delete = mysqli_query($koneksi, $query_delete);
    
    if(!$result_delete) {
        throw new Exception("Gagal menghapus data lampiran: " . mysqli_error($koneksi));
    }
    
    header("location:kunjungan_rumah_edit.php?id=$kunjungan_id&alert=sukses&pesan=Foto berhasil dihapus");
    exit();
    
} catch(Exception $e) {
    header("location:kunjungan_rumah_edit.php?id=$kunjungan_id&alert=gagal&pesan=" . urlencode($e->getMessage()));
    exit();
}
?>
