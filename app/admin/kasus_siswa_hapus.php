<?php
include '../koneksi.php';
session_start();

// Cek session - izinkan administrator dan guru_bk
if(!isset($_SESSION['level']) || ($_SESSION['level'] != "administrator" && $_SESSION['level'] != "guru_bk")){
    header("location:../admin.php?alert=belum_login");
    exit();
}

// Ambil ID kasus dari URL
$kasus_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if($kasus_id == 0) {
    header("location:kasus_siswa.php?alert=gagal&pesan=ID kasus tidak valid");
    exit();
}

// Cek apakah kasus ada
$query_cek = "SELECT * FROM kasus_siswa WHERE kasus_id = '$kasus_id'";
$result_cek = mysqli_query($koneksi, $query_cek);

if(mysqli_num_rows($result_cek) == 0) {
    header("location:kasus_siswa.php?alert=gagal&pesan=Kasus tidak ditemukan");
    exit();
}

$kasus = mysqli_fetch_assoc($result_cek);

// Mulai transaksi
mysqli_begin_transaction($koneksi);

try {
    // Set user IP untuk audit trail
    $user_ip = $_SERVER['REMOTE_ADDR'];
    mysqli_query($koneksi, "SET @user_ip = '$user_ip'");

    // Ambil data jurnal untuk hapus file lampiran
    $query_jurnal = "SELECT lampiran_file FROM jurnal_kasus WHERE kasus_id = '$kasus_id' AND lampiran_file IS NOT NULL AND lampiran_file != ''";
    $result_jurnal = mysqli_query($koneksi, $query_jurnal);
    
    while($jurnal = mysqli_fetch_assoc($result_jurnal)) {
        if(!empty($jurnal['lampiran_file']) && file_exists('../' . $jurnal['lampiran_file'])) {
            unlink('../' . $jurnal['lampiran_file']);
        }
    }

    // Hapus notifikasi RTL terkait
    $query_delete_notif = "DELETE n FROM notifikasi_rtl n 
                          INNER JOIN jurnal_kasus j ON n.jurnal_id = j.jurnal_id 
                          WHERE j.kasus_id = '$kasus_id'";
    mysqli_query($koneksi, $query_delete_notif);

    // Hapus jurnal kasus
    $query_delete_jurnal = "DELETE FROM jurnal_kasus WHERE kasus_id = '$kasus_id'";
    $result_delete_jurnal = mysqli_query($koneksi, $query_delete_jurnal);
    
    if(!$result_delete_jurnal) {
        throw new Exception("Gagal menghapus jurnal kasus: " . mysqli_error($koneksi));
    }

    // Hapus audit trail
    $query_delete_audit = "DELETE FROM audit_kasus WHERE kasus_id = '$kasus_id'";
    mysqli_query($koneksi, $query_delete_audit);

    // Hapus kasus utama
    $query_delete_kasus = "DELETE FROM kasus_siswa WHERE kasus_id = '$kasus_id'";
    $result_delete_kasus = mysqli_query($koneksi, $query_delete_kasus);
    
    if(!$result_delete_kasus) {
        throw new Exception("Gagal menghapus kasus: " . mysqli_error($koneksi));
    }

    // Commit transaksi
    mysqli_commit($koneksi);
    
    header("location:kasus_siswa.php?alert=sukses&pesan=Kasus berhasil dihapus");
    exit();

} catch(Exception $e) {
    // Rollback transaksi
    mysqli_rollback($koneksi);
    
    header("location:kasus_siswa.php?alert=gagal&pesan=" . urlencode($e->getMessage()));
    exit();
}
?>
