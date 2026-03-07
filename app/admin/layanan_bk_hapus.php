<?php
include 'header.php';

if(!isset($_GET['id'])) {
    header("location:layanan_bk.php");
    exit();
}

$id = $_GET['id'];

// Get file info before deletion
$query_file = "SELECT lampiran_foto FROM layanan_bk WHERE layanan_id = ?";
$stmt_file = mysqli_prepare($koneksi, $query_file);
mysqli_stmt_bind_param($stmt_file, 'i', $id);
mysqli_stmt_execute($stmt_file);
$result_file = mysqli_stmt_get_result($stmt_file);
$file_data = mysqli_fetch_assoc($result_file);

// Delete peserta first (due to foreign key constraint)
$delete_peserta = "DELETE FROM layanan_bk_peserta WHERE layanan_id = ?";
$stmt_delete_peserta = mysqli_prepare($koneksi, $delete_peserta);
mysqli_stmt_bind_param($stmt_delete_peserta, 'i', $id);
mysqli_stmt_execute($stmt_delete_peserta);

// Delete layanan BK
$delete_layanan = "DELETE FROM layanan_bk WHERE layanan_id = ?";
$stmt_delete_layanan = mysqli_prepare($koneksi, $delete_layanan);
mysqli_stmt_bind_param($stmt_delete_layanan, 'i', $id);

if(mysqli_stmt_execute($stmt_delete_layanan)) {
    // Delete file if exists
    if($file_data['lampiran_foto'] && file_exists('../' . $file_data['lampiran_foto'])) {
        unlink('../' . $file_data['lampiran_foto']);
    }
    
    echo "<script>alert('Data layanan BK berhasil dihapus!'); window.location='layanan_bk.php';</script>";
} else {
    echo "<script>alert('Gagal menghapus data layanan BK!'); window.location='layanan_bk.php';</script>";
}

mysqli_stmt_close($stmt_delete_layanan);
mysqli_stmt_close($stmt_delete_peserta);
?>
