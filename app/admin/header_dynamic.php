<?php 
// =====================================================
// HEADER DYNAMIC - 2025
// =====================================================
// File ini akan memilih header yang sesuai berdasarkan level user
// - Administrator: menggunakan header.php (menu penuh)
// - Guru BK: menggunakan header_guru_bk.php (menu terbatas)
// =====================================================

// Include koneksi database saja, fungsi lain akan di-include oleh header yang dipilih
include '../koneksi.php';

if(!isset($_SESSION)) {
    session_start();
}

// Cek session dengan aman
if(!isset($_SESSION['level']) || ($_SESSION['level'] != "administrator" && $_SESSION['level'] != "guru_bk")){
  header("location:../admin.php?alert=belum_login");
  exit();
}

// Pilih header berdasarkan level user
if($_SESSION['level'] == "administrator") {
    // Administrator menggunakan header penuh
    include 'header.php';
} else if($_SESSION['level'] == "guru_bk") {
    // Guru BK menggunakan header terbatas
    include 'header_guru_bk.php';
} else {
    // Level tidak valid, redirect ke login
    header("location:../admin.php?alert=belum_login");
    exit();
}
?>
