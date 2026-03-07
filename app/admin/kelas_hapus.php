<?php 
include '../koneksi.php';

// Cek apakah parameter id ada
if(!isset($_GET['id']) || empty($_GET['id'])) {
    header("location:kelas.php?alert=error&msg=" . urlencode("ID kelas tidak valid!"));
    exit();
}

$id = mysqli_real_escape_string($koneksi, $_GET['id']);

// Ambil nama kelas untuk notifikasi
$query_kelas = mysqli_query($koneksi, "SELECT kelas_nama FROM kelas WHERE kelas_id='$id'");
if(mysqli_num_rows($query_kelas) == 0) {
    header("location:kelas.php?alert=error&msg=" . urlencode("Kelas tidak ditemukan!"));
    exit();
}

$kelas_data = mysqli_fetch_assoc($query_kelas);
$nama_kelas = $kelas_data['kelas_nama'];

// Mulai transaksi untuk memastikan semua data terhapus atau tidak ada yang terhapus
mysqli_begin_transaction($koneksi);

try {
    // Hapus data terkait terlebih dahulu (child records)
    $delete_kelas_siswa = mysqli_query($koneksi, "DELETE FROM kelas_siswa WHERE ks_kelas='$id'");
    if(!$delete_kelas_siswa) {
        throw new Exception("Gagal menghapus data siswa di kelas: " . mysqli_error($koneksi));
    }
    
    $delete_prestasi = mysqli_query($koneksi, "DELETE FROM input_prestasi WHERE kelas='$id'");
    if(!$delete_prestasi) {
        throw new Exception("Gagal menghapus data prestasi: " . mysqli_error($koneksi));
    }
    
    $delete_pelanggaran = mysqli_query($koneksi, "DELETE FROM input_pelanggaran WHERE kelas='$id'");
    if(!$delete_pelanggaran) {
        throw new Exception("Gagal menghapus data pelanggaran: " . mysqli_error($koneksi));
    }
    
    // Hapus kelas (parent record)
    $delete_kelas = mysqli_query($koneksi, "DELETE FROM kelas WHERE kelas_id='$id'");
    if(!$delete_kelas) {
        throw new Exception("Gagal menghapus kelas: " . mysqli_error($koneksi));
    }
    
    // Commit transaksi jika semua berhasil
    mysqli_commit($koneksi);
    
    // Redirect dengan notifikasi sukses
    $ta_param = isset($_GET['ta']) ? '&ta=' . $_GET['ta'] : '';
    header("location:kelas.php?alert=success&msg=" . urlencode("Kelas '" . $nama_kelas . "' berhasil dihapus!") . $ta_param);
    exit();
    
} catch(Exception $e) {
    // Rollback transaksi jika ada error
    mysqli_rollback($koneksi);
    
    // Redirect dengan notifikasi error
    $ta_param = isset($_GET['ta']) ? '&ta=' . $_GET['ta'] : '';
    header("location:kelas.php?alert=error&msg=" . urlencode("Gagal menghapus kelas: " . $e->getMessage()) . $ta_param);
    exit();
}