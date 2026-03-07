<?php
// File untuk menandai kasus sebagai sudah dibaca (update status dari Baru ke Dalam Proses)
include '../koneksi.php';
session_start();

// Cek session
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

// Ambil data kasus untuk cek status
$query_kasus = "SELECT status_kasus, sumber_kasus FROM kasus_siswa WHERE kasus_id = '$kasus_id'";
$result_kasus = mysqli_query($koneksi, $query_kasus);

if(mysqli_num_rows($result_kasus) == 0) {
    header("location:kasus_siswa.php?alert=gagal&pesan=Kasus tidak ditemukan");
    exit();
}

$kasus = mysqli_fetch_assoc($result_kasus);

// Update status jika masih "Baru" dan dari "Inisiatif Siswa"
if($kasus['status_kasus'] == 'Baru' && $kasus['sumber_kasus'] == 'Inisiatif Siswa') {
    // Gunakan transaksi untuk memastikan update berhasil
    mysqli_begin_transaction($koneksi);
    
    $update_status = "UPDATE kasus_siswa SET status_kasus = 'Dalam Proses', updated_at = NOW() WHERE kasus_id = '$kasus_id' AND status_kasus = 'Baru'";
    $result_update = mysqli_query($koneksi, $update_status);
    
    // Verifikasi update berhasil
    if($result_update && mysqli_affected_rows($koneksi) > 0) {
        mysqli_commit($koneksi);
        
        // Verifikasi ulang bahwa status benar-benar berubah
        $verify_query = "SELECT status_kasus FROM kasus_siswa WHERE kasus_id = '$kasus_id'";
        $verify_result = mysqli_query($koneksi, $verify_query);
        if($verify_result) {
            $verify_data = mysqli_fetch_assoc($verify_result);
            if($verify_data['status_kasus'] == 'Dalam Proses') {
                // Status benar-benar berubah, redirect dengan parameter untuk refresh notifikasi
                header("location:kasus_siswa_detail.php?id=" . $kasus_id . "&_refresh=" . time());
                exit();
            }
        }
    } else {
        mysqli_rollback($koneksi);
    }
}

// Jika update gagal atau status sudah bukan "Baru", langsung redirect
header("location:kasus_siswa_detail.php?id=" . $kasus_id);
exit();
?>

