<?php
include '../koneksi.php';

// Ambil data dari form
$nama_madrasah = mysqli_real_escape_string($koneksi, $_POST['nama_madrasah']);
$jenis_institusi = mysqli_real_escape_string($koneksi, $_POST['jenis_institusi']);
$alamat_madrasah = mysqli_real_escape_string($koneksi, $_POST['alamat_madrasah']);
$kota = mysqli_real_escape_string($koneksi, $_POST['kota']);
$nama_kepala = mysqli_real_escape_string($koneksi, $_POST['nama_kepala']);
$nip_kepala = mysqli_real_escape_string($koneksi, $_POST['nip_kepala']);
$nama_waka = mysqli_real_escape_string($koneksi, $_POST['nama_waka']);
$nip_waka = mysqli_real_escape_string($koneksi, $_POST['nip_waka']);
$nama_guru_bk = mysqli_real_escape_string($koneksi, $_POST['nama_guru_bk']);
$judul_raport = mysqli_real_escape_string($koneksi, $_POST['judul_raport']);
$sub_judul = mysqli_real_escape_string($koneksi, $_POST['sub_judul']);
$logo_url = mysqli_real_escape_string($koneksi, $_POST['logo_url']);

// Cek apakah sudah ada data pengaturan
$check_query = mysqli_query($koneksi, "SELECT id FROM pengaturan_raport ORDER BY id DESC LIMIT 1");
$existing = mysqli_fetch_assoc($check_query);

if ($existing) {
    // Update data yang sudah ada
    $update_query = "UPDATE pengaturan_raport SET 
        nama_madrasah = '$nama_madrasah',
        jenis_institusi = '$jenis_institusi',
        alamat_madrasah = '$alamat_madrasah',
        kota = '$kota',
        nama_kepala = '$nama_kepala',
        nip_kepala = '$nip_kepala',
        nama_waka = '$nama_waka',
        nip_waka = '$nip_waka',
        nama_guru_bk = '$nama_guru_bk',
        judul_raport = '$judul_raport',
        sub_judul = '$sub_judul',
        logo_url = '$logo_url',
        updated_at = NOW()
        WHERE id = '{$existing['id']}'";
    
    mysqli_query($koneksi, $update_query);
} else {
    // Insert data baru
    $insert_query = "INSERT INTO pengaturan_raport (
        nama_madrasah, jenis_institusi, alamat_madrasah, kota, nama_kepala, nip_kepala,
        nama_waka, nip_waka, nama_guru_bk, judul_raport, sub_judul, logo_url
    ) VALUES (
        '$nama_madrasah', '$jenis_institusi', '$alamat_madrasah', '$kota', '$nama_kepala', '$nip_kepala',
        '$nama_waka', '$nip_waka', '$nama_guru_bk', '$judul_raport', '$sub_judul', '$logo_url'
    )";
    
    mysqli_query($koneksi, $insert_query);
}

header('Location: pengaturan_raport.php?alert=success');
exit;
?>
