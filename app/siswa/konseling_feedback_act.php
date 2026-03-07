<?php
include '../koneksi.php';
session_start();

// Cek session untuk siswa
if(!isset($_SESSION['level']) || $_SESSION['level'] != "siswa"){
    header("location:../index.php?alert=belum_login");
    exit();
}

if(isset($_POST['jurnal_id']) && isset($_POST['kasus_id']) && isset($_POST['siswa_id'])) {
    // Ambil data dari form
    $jurnal_id = mysqli_real_escape_string($koneksi, $_POST['jurnal_id']);
    $kasus_id = mysqli_real_escape_string($koneksi, $_POST['kasus_id']);
    $siswa_id = mysqli_real_escape_string($koneksi, $_POST['siswa_id']);
    $feedback_text = mysqli_real_escape_string($koneksi, $_POST['feedback_text']);
    
    // Validasi
    if(empty($jurnal_id) || empty($kasus_id) || empty($siswa_id) || empty($feedback_text)) {
        header("location:konseling_detail.php?id=" . $kasus_id . "&alert=error&msg=" . urlencode("Data tidak lengkap"));
        exit();
    }
    
    // Verifikasi bahwa siswa_id sesuai dengan session
    if($siswa_id != $_SESSION['id']) {
        header("location:konseling_detail.php?id=" . $kasus_id . "&alert=error&msg=" . urlencode("Akses tidak diizinkan"));
        exit();
    }
    
    // Verifikasi bahwa jurnal_id dan kasus_id valid
    $verify_query = "SELECT j.jurnal_id, j.kasus_id 
                     FROM jurnal_kasus j 
                     WHERE j.jurnal_id = '$jurnal_id' AND j.kasus_id = '$kasus_id'";
    $verify_result = mysqli_query($koneksi, $verify_query);
    
    if(!$verify_result || mysqli_num_rows($verify_result) == 0) {
        header("location:konseling_detail.php?id=" . $kasus_id . "&alert=error&msg=" . urlencode("Data jurnal tidak valid"));
        exit();
    }
    
    // Insert feedback
    $insert_query = "INSERT INTO feedback_siswa (jurnal_id, kasus_id, siswa_id, feedback_text) 
                     VALUES ('$jurnal_id', '$kasus_id', '$siswa_id', '$feedback_text')";
    
    if(mysqli_query($koneksi, $insert_query)) {
        header("location:konseling_detail.php?id=" . $kasus_id . "&alert=success&msg=" . urlencode("Feedback berhasil dikirim!"));
    } else {
        header("location:konseling_detail.php?id=" . $kasus_id . "&alert=error&msg=" . urlencode("Gagal mengirim feedback: " . mysqli_error($koneksi)));
    }
} else {
    header("location:konseling_saya.php?alert=error&msg=" . urlencode("Akses tidak valid"));
}
?>

