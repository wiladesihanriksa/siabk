<?php
include '../koneksi.php';
session_start();

// Cek session - izinkan administrator dan guru_bk
if(!isset($_SESSION['level']) || ($_SESSION['level'] != "administrator" && $_SESSION['level'] != "guru_bk")){
    header("location:../admin.php?alert=belum_login");
    exit();
}

// Ambil ID jurnal dari URL
$jurnal_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if($jurnal_id == 0) {
    header("location:kasus_siswa.php?alert=gagal&pesan=ID jurnal tidak valid");
    exit();
}

// Cek apakah jurnal ada
$query_cek = "SELECT * FROM jurnal_kasus WHERE jurnal_id = '$jurnal_id'";
$result_cek = mysqli_query($koneksi, $query_cek);

if(mysqli_num_rows($result_cek) == 0) {
    header("location:kasus_siswa.php?alert=gagal&pesan=Jurnal tidak ditemukan");
    exit();
}

$jurnal = mysqli_fetch_assoc($result_cek);
$kasus_id = $jurnal['kasus_id'];

// Mulai transaksi
mysqli_begin_transaction($koneksi);

try {
    // Hapus file lampiran jika ada
    if(!empty($jurnal['lampiran_file']) && file_exists('../' . $jurnal['lampiran_file'])) {
        unlink('../' . $jurnal['lampiran_file']);
    }

    // Hapus notifikasi RTL terkait
    $query_delete_notif = "DELETE FROM notifikasi_rtl WHERE jurnal_id = '$jurnal_id'";
    mysqli_query($koneksi, $query_delete_notif);

    // Hapus jurnal
    $query_delete = "DELETE FROM jurnal_kasus WHERE jurnal_id = '$jurnal_id'";
    $result_delete = mysqli_query($koneksi, $query_delete);
    
    if(!$result_delete) {
        throw new Exception("Gagal menghapus jurnal: " . mysqli_error($koneksi));
    }

    // Commit transaksi
    mysqli_commit($koneksi);
    
    header("location:kasus_siswa_detail.php?id=$kasus_id&alert=sukses&pesan=Jurnal berhasil dihapus");
    exit();

} catch(Exception $e) {
    // Rollback transaksi
    mysqli_rollback($koneksi);
    
    header("location:kasus_siswa_detail.php?id=$kasus_id&alert=gagal&pesan=" . urlencode($e->getMessage()));
    exit();
}
?>
