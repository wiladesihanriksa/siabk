<?php
include '../koneksi.php';
session_start();

// Cek session - izinkan administrator dan guru_bk
if(!isset($_SESSION['level']) || ($_SESSION['level'] != "administrator" && $_SESSION['level'] != "guru_bk")){
    header("location:../admin.php?alert=belum_login");
    exit();
}

if(isset($_POST['update'])) {
    // Ambil data dari form
    $kasus_id = mysqli_real_escape_string($koneksi, $_POST['kasus_id']);
    $siswa_id = mysqli_real_escape_string($koneksi, $_POST['siswa_id']);
    $tanggal_pelaporan = mysqli_real_escape_string($koneksi, $_POST['tanggal_pelaporan']);
    $sumber_kasus = mysqli_real_escape_string($koneksi, $_POST['sumber_kasus']);
    $kategori_masalah = mysqli_real_escape_string($koneksi, $_POST['kategori_masalah']);
    $judul_kasus = mysqli_real_escape_string($koneksi, $_POST['judul_kasus']);
    $deskripsi_awal = mysqli_real_escape_string($koneksi, $_POST['deskripsi_awal']);
    $status_kasus = mysqli_real_escape_string($koneksi, $_POST['status_kasus']);
    $guru_bk_id = mysqli_real_escape_string($koneksi, $_POST['guru_bk_id']);

    // Validasi data
    if(empty($kasus_id) || empty($siswa_id) || empty($tanggal_pelaporan) || 
       empty($sumber_kasus) || empty($kategori_masalah) || empty($judul_kasus) || 
       empty($guru_bk_id)) {
        header("location:kasus_siswa_edit.php?id=$kasus_id&alert=gagal&pesan=Data tidak lengkap");
        exit();
    }

    // Cek apakah kasus ada
    $query_cek = "SELECT * FROM kasus_siswa WHERE kasus_id = '$kasus_id'";
    $result_cek = mysqli_query($koneksi, $query_cek);
    
    if(mysqli_num_rows($result_cek) == 0) {
        header("location:kasus_siswa.php?alert=gagal&pesan=Kasus tidak ditemukan");
        exit();
    }

    // Mulai transaksi
    mysqli_begin_transaction($koneksi);

    try {
        // Set user IP untuk audit trail
        $user_ip = $_SERVER['REMOTE_ADDR'];
        mysqli_query($koneksi, "SET @user_ip = '$user_ip'");

        // Update data kasus
        $query_update = "UPDATE kasus_siswa SET 
                        siswa_id = '$siswa_id',
                        tanggal_pelaporan = '$tanggal_pelaporan',
                        sumber_kasus = '$sumber_kasus',
                        kategori_masalah = '$kategori_masalah',
                        judul_kasus = '$judul_kasus',
                        deskripsi_awal = '$deskripsi_awal',
                        status_kasus = '$status_kasus',
                        guru_bk_id = '$guru_bk_id',
                        updated_at = NOW()
                        WHERE kasus_id = '$kasus_id'";
        
        $result_update = mysqli_query($koneksi, $query_update);
        
        if(!$result_update) {
            throw new Exception("Gagal mengupdate data kasus: " . mysqli_error($koneksi));
        }

        // Commit transaksi
        mysqli_commit($koneksi);
        
        header("location:kasus_siswa_detail.php?id=$kasus_id&alert=sukses&pesan=Kasus berhasil diupdate");
        exit();

    } catch(Exception $e) {
        // Rollback transaksi
        mysqli_rollback($koneksi);
        
        header("location:kasus_siswa_edit.php?id=$kasus_id&alert=gagal&pesan=" . urlencode($e->getMessage()));
        exit();
    }
} else {
    header("location:kasus_siswa.php?alert=gagal&pesan=Akses tidak valid");
    exit();
}
?>
