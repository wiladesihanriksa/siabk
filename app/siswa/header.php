<?php 
// 1. Inisialisasi Session & Koneksi (Sesuai Standar Admin)
if(!isset($_SESSION)) {
    session_start();
}
include '../koneksi.php';
include '../functions_app_settings.php';
include '../functions_color_settings.php';

// 2. Proteksi Halaman Siswa
if(!isset($_SESSION['level']) || $_SESSION['level'] != "siswa"){
    header("location:../index.php?alert=belum_login");
    exit();
}

// 3. Ambil Pengaturan Aplikasi & Warna
$app_settings = function_exists('getAppSettings') ? getAppSettings($koneksi) : array();
$color_settings = function_exists('getColorSettings') ? getColorSettings($koneksi) : array();

// 4. Ambil Data Profil Siswa & Logika URL Supabase
$id_user = $_SESSION['id'];
$profil_res = mysqli_query($koneksi, "SELECT * FROM siswa WHERE siswa_id='$id_user'");
$profil = mysqli_fetch_assoc($profil_res);

$baseUrl = function_exists('getSupabaseBaseUrl') ? getSupabaseBaseUrl() : null;

// Logika foto profil siswa (mengikuti gaya admin)
$user_foto = (!empty($profil['siswa_foto'])) ? 
             ($baseUrl ? $baseUrl . 'gambar/user/' . $profil['siswa_foto'] : '../gambar/user/' . $profil['siswa_foto']) : 
             "../gambar/sistem/user.png";

$app_name = isset($app_settings['app_name']) && $app_settings['app_name'] !== '' ? $app_settings['app_name'] : 'E-Point';
$institution = isset($app_settings['app_author']) && $app_settings['app_author'] !== '' ? $app_settings['app_author'] : 'Sistem Poin Siswa';

// 5. Logika Favicon (Sama dengan Admin)
$fav_from_setting = function_exists('getAppFavicon') ? getAppFavicon($app_settings, '') : '';
$favicon_path = $fav_from_setting !== '' ? '../' . $fav_from_setting : '../gambar/sistem/logo.png';

if (!@fopen($favicon_path, 'r')) {
    $favicon_path = '../gambar/sistem/login_logo.png';
}
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>Siswa - <?php echo htmlspecialchars($app_name . ' ' . $institution); ?></title>
  <link rel="icon" type="image/png" href="<?php echo $favicon_path; ?>?v=1">
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  
  <link rel="stylesheet" href="../assets/bower_components/bootstrap/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="../assets/bower_components/font-awesome/css/font-awesome.min.css">
  <link rel="stylesheet" href="../assets/bower_components/Ionicons/css/ionicons.min.css">
  <link rel="stylesheet" href="../assets/dist/css/AdminLTE.min.css">
  <link rel="stylesheet" href="../assets/dist/css/skins/_all-skins.min.css">
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">

  <?php if(function_exists('generateDynamicCSS')) echo generateDynamicCSS($color_settings); ?>

  <style>
    /* CSS Fix - Identik dengan Admin */
    .sidebar-menu .treeview.active > .treeview-menu { display: block; }
    .main-header .logo { display: flex; align-items: center; justify-content: center; }
    .sidebar-mini.sidebar-collapse .main-header .logo .logo-mini { display: inline-flex !important; }
    .sidebar-mini.sidebar-collapse .main-header .logo .logo-lg { display: none !important; }
    .user-image, .img-circle { object-fit: cover; }

    /* Menghilangkan panel user saat sidebar mengecil agar tidak terpotong */
    .sidebar-mini.sidebar-collapse .main-sidebar .user-panel {
        display: none !important;
    }

    /* Memastikan foto profil di sidebar tetap bulat dan ukurannya terkunci */
    .user-panel > .image > img {
        width: 45px !important;
        height: 45px !important;
        max-width: 45px !important;
        max-height: 45px !important;
        object-fit: cover;
    }

    .user-panel > .info {
        padding: 5px 5px 5px 15px;
    }
  </style>
</head>
<body class="hold-transition skin-green sidebar-mini">
<div class="wrapper">

  <header class="main-header">
    <?php
      // Logika Logo Supabase (Sesuai Admin)
      $logo_from_setting = function_exists('getAppLogo') ? getAppLogo($app_settings, 'gambar/sistem/logo.png') : 'gambar/sistem/logo.png';
      $app_logo_path = (strpos($logo_from_setting, 'http') !== false) ? $logo_from_setting : '../' . $logo_from_setting;
    ?>
    <a href="index.php" class="logo">
      <span class="logo-mini"><img src="<?php echo $app_logo_path; ?>" alt="L" style="max-height:22px;"></span>
      <span class="logo-lg">
        <img src="<?php echo $app_logo_path; ?>" alt="Logo" style="max-height:28px; margin-right:8px;">
        <b><?php echo htmlspecialchars($app_name); ?></b>
      </span>
    </a>
    
    <nav class="navbar navbar-static-top">
      <a href="#" class="sidebar-toggle" data-toggle="push-menu" role="button">
        <span class="sr-only">Toggle navigation</span>
      </a>

      <div class="navbar-custom-menu">
        <ul class="nav navbar-nav">
          <li class="dropdown user user-menu">
            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
              <img src="<?php echo $user_foto; ?>" class="user-image" alt="User Image">
              <span class="hidden-xs"><?php echo htmlspecialchars($profil['siswa_nama'] ?? 'Siswa'); ?></span>
            </a>
            <ul class="dropdown-menu">
              <li class="user-header">
                <img src="<?php echo $user_foto; ?>" class="img-circle" alt="User Image">
                <p>
                  <?php echo htmlspecialchars($profil['siswa_nama'] ?? 'Siswa'); ?>
                  <small>NIS: <?php echo htmlspecialchars($profil['siswa_nis'] ?? '-'); ?></small>
                </p>
              </li>
              <li class="user-footer">
                <div class="pull-left"><a href="profil.php" class="btn btn-default btn-flat">Profile</a></div>
                <div class="pull-right"><a href="logout.php" class="btn btn-default btn-flat">Sign out</a></div>
              </li>
            </ul>
          </li>
        </ul>
      </div>
    </nav>
  </header>

  <aside class="main-sidebar">
    <section class="sidebar">
      <div class="user-panel">
        <div class="pull-left image">
          <img src="<?php echo $user_foto; ?>" class="img-circle" alt="User Image">
        </div>
        <div class="pull-left info">
          <p><?php echo htmlspecialchars($profil['siswa_nama'] ?? 'Siswa'); ?></p>
          <a href="#"><i class="fa fa-circle text-success"></i> Online</a>
        </div>
      </div>

      <ul class="sidebar-menu" data-widget="tree">
        <li class="header">SISWA NAVIGATION</li>
        
        <li class="<?php echo (basename($_SERVER['PHP_SELF']) == 'index.php') ? 'active' : ''; ?>">
            <a href="index.php"><i class="fa fa-dashboard"></i> <span>DASHBOARD</span></a>
        </li>
        
        <li class="treeview <?php echo in_array(basename($_SERVER['PHP_SELF']), ['prestasi_saya.php', 'pelanggaran_saya.php']) ? 'active' : ''; ?>">
          <a href="#"><i class="fa fa-user"></i> <span>DATA SAYA</span>
            <span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span>
          </a>
          <ul class="treeview-menu">
            <li class="<?php echo (basename($_SERVER['PHP_SELF']) == 'prestasi_saya.php') ? 'active' : ''; ?>"><a href="prestasi_saya.php"><i class="fa fa-trophy"></i> Prestasi Saya</a></li>
            <li class="<?php echo (basename($_SERVER['PHP_SELF']) == 'pelanggaran_saya.php') ? 'active' : ''; ?>"><a href="pelanggaran_saya.php"><i class="fa fa-warning"></i> Pelanggaran Saya</a></li>
          </ul>
        </li>

        <li class="treeview">
          <a href="#"><i class="fa fa-heart"></i> <span>KONSELING</span>
            <span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span>
          </a>
          <ul class="treeview-menu">
            <li><a href="konseling_saya.php"><i class="fa fa-list"></i> Konseling Saya</a></li>
            <li><a href="konseling_ajukan.php"><i class="fa fa-plus"></i> Ajukan Konseling</a></li>
          </ul>
        </li>

        <li class="treeview">
          <a href="#"><i class="fa fa-graduation-cap"></i> <span>LAYANAN BK</span>
            <span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span>
          </a>
          <ul class="treeview-menu">
            <li><a href="layanan_bk_saya.php"><i class="fa fa-heart"></i> Layanan BK Saya</a></li>
            <li><a href="layanan_bk_kalender.php"><i class="fa fa-calendar"></i> Kalender Layanan</a></li>
          </ul>
        </li>

        <li class="header">PENGATURAN</li>
        <li><a href="profil.php"><i class="fa fa-user-circle"></i> <span>PROFIL SAYA</span></a></li>
        <li><a href="gantipassword.php"><i class="fa fa-lock"></i> <span>GANTI PASSWORD</span></a></li>
        <li><a href="logout.php"><i class="fa fa-sign-out text-red"></i> <span class="text-red">LOGOUT</span></a></li>
      </ul>
    </section>
  </aside>