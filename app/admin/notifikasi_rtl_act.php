<?php
include '../koneksi.php';
session_start();

// Cek session
if(!isset($_SESSION['level']) || $_SESSION['level'] != "administrator"){
    header("location:../admin.php?alert=belum_login");
    exit();
}

$action = isset($_GET['action']) ? $_GET['action'] : '';
$notif_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

switch($action) {
    case 'mark_read':
        if($notif_id > 0) {
            $query_update = "UPDATE notifikasi_rtl SET status_reminder = 'Sudah' WHERE notif_id = '$notif_id'";
            $result = mysqli_query($koneksi, $query_update);
            
            if($result) {
                header("location:notifikasi_rtl.php?alert=sukses&pesan=Notifikasi ditandai sudah dibaca");
            } else {
                header("location:notifikasi_rtl.php?alert=gagal&pesan=Gagal mengupdate notifikasi");
            }
        } else {
            header("location:notifikasi_rtl.php?alert=gagal&pesan=ID notifikasi tidak valid");
        }
        break;
        
    case 'cancel':
        if($notif_id > 0) {
            $query_update = "UPDATE notifikasi_rtl SET status_reminder = 'Dibatalkan' WHERE notif_id = '$notif_id'";
            $result = mysqli_query($koneksi, $query_update);
            
            if($result) {
                header("location:notifikasi_rtl.php?alert=sukses&pesan=Notifikasi dibatalkan");
            } else {
                header("location:notifikasi_rtl.php?alert=gagal&pesan=Gagal membatalkan notifikasi");
            }
        } else {
            header("location:notifikasi_rtl.php?alert=gagal&pesan=ID notifikasi tidak valid");
        }
        break;
        
    case 'mark_all_read':
        $query_update = "UPDATE notifikasi_rtl SET status_reminder = 'Sudah' WHERE status_reminder = 'Belum'";
        $result = mysqli_query($koneksi, $query_update);
        
        if($result) {
            header("location:notifikasi_rtl.php?alert=sukses&pesan=Semua notifikasi ditandai sudah dibaca");
        } else {
            header("location:notifikasi_rtl.php?alert=gagal&pesan=Gagal mengupdate notifikasi");
        }
        break;
        
    default:
        header("location:notifikasi_rtl.php?alert=gagal&pesan=Aksi tidak valid");
        break;
}
?>
