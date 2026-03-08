<?php 
include '../koneksi.php';
include '../functions_app_settings.php';
include '../functions_color_settings.php';
session_start();

// Cek session dengan aman
if(!isset($_SESSION['level']) || $_SESSION['level'] != "siswa"){
  header("location:../index.php?alert=belum_login");
  exit();
}

// Ambil pengaturan aplikasi
$app_settings = getAppSettings($koneksi);
$color_settings = getColorSettings($koneksi);
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  <title><?php echo getSetting($app_settings, 'app_name', 'Siswa - Sistem Informasi E-Point Siswa MAN 2 Semarang'); ?></title>
  
  <!-- Favicon: gunakan favicon dari setting bila ada, fallback ke file default -->
  <?php
    // favicon dari setting bila ada, fallback ke file default
    $fav_from_setting = getAppFavicon($app_settings, '');
    if($fav_from_setting !== '') {
      $favicon_path = '../' . $fav_from_setting;
      // Cek apakah file ada
      if (!@fopen($favicon_path, 'r')) {
        // Jika favicon dari setting tidak ditemukan, coba fallback
        $favicon_path = '../gambar/sistem/favicon.png';
        if (!@fopen($favicon_path, 'r')) {
          $favicon_path = '../gambar/sistem/logo.png';
          if (!@fopen($favicon_path, 'r')) {
            $favicon_path = '../gambar/sistem/login_logo.png';
          }
        }
      }
    } else {
      // Jika tidak ada setting, gunakan fallback
      $favicon_path = '../gambar/sistem/favicon.png';
      if (!@fopen($favicon_path, 'r')) {
        $favicon_path = '../gambar/sistem/logo.png';
        if (!@fopen($favicon_path, 'r')) {
          $favicon_path = '../gambar/sistem/login_logo.png';
        }
      }
    }
    
    // Tentukan type berdasarkan ekstensi
    $favicon_ext = strtolower(pathinfo($favicon_path, PATHINFO_EXTENSION));
    $favicon_type = 'image/x-icon';
    if($favicon_ext == 'png') {
      $favicon_type = 'image/png';
    } elseif($favicon_ext == 'jpg' || $favicon_ext == 'jpeg') {
      $favicon_type = 'image/jpeg';
    }
  ?>
  <link rel="icon" type="<?php echo $favicon_type; ?>" href="<?php echo $favicon_path; ?>">
  <link rel="stylesheet" href="../assets/bower_components/bootstrap/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="../assets/bower_components/font-awesome/css/font-awesome.min.css">
  <link rel="stylesheet" href="../assets/bower_components/Ionicons/css/ionicons.min.css">
  <link rel="stylesheet" href="../assets/dist/css/AdminLTE.min.css">

  <link rel="stylesheet" href="../assets/bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css">

  <link rel="stylesheet" href="../assets/dist/css/skins/_all-skins.min.css">
  <link rel="stylesheet" href="../assets/bower_components/morris.js/morris.css">
  <link rel="stylesheet" href="../assets/bower_components/jvectormap/jquery-jvectormap.css">
  <link rel="stylesheet" href="../assets/bower_components/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css">
  <link rel="stylesheet" href="../assets/bower_components/bootstrap-daterangepicker/daterangepicker.css">
  <link rel="stylesheet" href="../assets/plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.min.css">
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">
  
  <?php echo generateDynamicCSS($color_settings); ?>
  
  <!-- CSS untuk logo -->
  <style>
    /* Pastikan logo tidak duplikasi dan tampil sesuai state */
    .main-header .logo { display: flex; align-items: center; justify-content: center; }
    .main-header .logo .logo-mini { display: none; }
    .main-header .logo .logo-lg { display: inline-flex; align-items: center; gap: 8px; }
    /* Saat sidebar-mini + collapsed → tampilkan mini, sembunyikan besar */
    .sidebar-mini.sidebar-collapse .main-header .logo .logo-mini { display: inline-flex !important; }
    .sidebar-mini.sidebar-collapse .main-header .logo .logo-lg { display: none !important; }
    /* Saat sidebar-mini tapi tidak collapsed → tetap tampilkan besar */
    .sidebar-mini:not(.sidebar-collapse) .main-header .logo .logo-mini { display: none !important; }
    .sidebar-mini:not(.sidebar-collapse) .main-header .logo .logo-lg { display: inline-flex !important; }
  </style>

</head>
<body class="hold-transition skin-green sidebar-mini">
  <div class="wrapper">

    <header class="main-header">
      <?php
        // Logo dari setting bila tersedia, fallback ke file default
        $logo_from_setting = getAppLogo($app_settings, 'gambar/sistem/logo.png');
        $app_logo_path = '../' . $logo_from_setting;
        // Cek apakah file ada, jika tidak gunakan fallback
        if (!@fopen($app_logo_path, 'r')) {
          $app_logo_path = '../gambar/sistem/login_logo.png';
          if (!@fopen($app_logo_path, 'r')) {
            $app_logo_path = '../gambar/sistem/logo.png';
          }
        }
        $app_name = getSetting($app_settings, 'app_name', 'E-<b>Point</b>');
      ?>
      <a href="index.php" class="logo">
        <!-- Tampilkan logo pada sidebar mini -->
        <span class="logo-mini" style="display:flex;align-items:center;justify-content:center;">
          <img src="<?php echo $app_logo_path; ?>" alt="Logo" style="max-height:30px; width:auto; display:block;"/>
        </span>
        <!-- Tampilkan logo penuh pada tampilan normal -->
        <span class="logo-lg" style="display:flex;align-items:center;gap:10px;">
          <img src="<?php echo $app_logo_path; ?>" alt="Logo" style="max-height:40px; width:auto; display:block;"/>
          <b><?php echo htmlspecialchars($app_name); ?></b>
        </span>
      </a>
      <nav class="navbar navbar-static-top">
        <a href="#" class="sidebar-toggle" data-toggle="push-menu" role="button">
          <span class="sr-only">Toggle navigation</span>
        </a>

        <div class="navbar-custom-menu">
          <ul class="nav navbar-nav">
            <li>
              <a href="logout.php"><i class="fa fa-sign-out"></i> LOGOUT</a>
            </li>
          </ul>
        </div>
      </nav>
    </header>

    <aside class="main-sidebar">
      <section class="sidebar">
        <div class="user-panel">
          <div class="pull-left image">
            <?php 
            $id_user = $_SESSION['id'];
            $profil = mysqli_query($koneksi,"select * from siswa where siswa_id='$id_user'");
            $profil = mysqli_fetch_assoc($profil);
            ?>
            <img src="../gambar/sistem/user.png" class="img-circle" style="height:45px; width: 45px;">
          </div>
          <div class="pull-left info">
            <p><?php echo $profil['siswa_nama'] ?></p>
            <a href="#"><i class="fa fa-circle text-success"></i> Online</a>
          </div>
        </div>

        <ul class="sidebar-menu" data-widget="tree">
          <li class="header">MAIN NAVIGATION</li>
          
          <!-- Dashboard -->
          <li><a href="index.php"><i class="fa fa-dashboard"></i> <span>DASHBOARD</span></a></li>
          
          <!-- Data Saya -->
          <li class="treeview">
            <a href="#">
              <i class="fa fa-user"></i>
              <span>DATA SAYA</span>
              <span class="pull-right-container">
                <i class="fa fa-angle-left pull-right"></i>
              </span>
            </a>
            <ul class="treeview-menu">
              <li><a href="prestasi_saya.php"><i class="fa fa-trophy"></i> Prestasi Saya</a></li>
              <li><a href="pelanggaran_saya.php"><i class="fa fa-warning"></i> Pelanggaran Saya</a></li>
            </ul>
          </li>
          
          <!-- Lihat Data -->
          <li class="treeview">
            <a href="#">
              <i class="fa fa-eye"></i>
              <span>LIHAT DATA</span>
              <span class="pull-right-container">
                <i class="fa fa-angle-left pull-right"></i>
              </span>
            </a>
            <ul class="treeview-menu">
              <li><a href="prestasi.php"><i class="fa fa-list-alt"></i> Data Prestasi</a></li>
              <li><a href="pelanggaran.php"><i class="fa fa-list-alt"></i> Data Pelanggaran</a></li>
            </ul>
          </li>
          
          <!-- Konseling -->
          <li class="treeview">
            <a href="#">
              <i class="fa fa-heart"></i>
              <span>KONSELING</span>
              <span class="pull-right-container">
                <i class="fa fa-angle-left pull-right"></i>
              </span>
            </a>
            <ul class="treeview-menu">
              <li><a href="konseling_saya.php"><i class="fa fa-list"></i> Konseling Saya</a></li>
              <li><a href="konseling_ajukan.php"><i class="fa fa-plus"></i> Ajukan Konseling</a></li>
            </ul>
          </li>
          
          <!-- Layanan BK -->
          <li class="treeview">
            <a href="#">
              <i class="fa fa-graduation-cap"></i>
              <span>LAYANAN BK</span>
              <span class="pull-right-container">
                <i class="fa fa-angle-left pull-right"></i>
              </span>
            </a>
            <ul class="treeview-menu">
              <li><a href="layanan_bk_saya.php"><i class="fa fa-heart"></i> Layanan BK Saya</a></li>
              <li><a href="layanan_bk_kalender.php"><i class="fa fa-calendar"></i> Kalender Layanan</a></li>
              <li><a href="kunjungan_rumah_saya.php"><i class="fa fa-home"></i> Kunjungan Rumah</a></li>
            </ul>
          </li>
          
          <!-- Pengaturan -->
          <li class="treeview">
            <a href="#">
              <i class="fa fa-cog"></i>
              <span>PENGATURAN</span>
              <span class="pull-right-container">
                <i class="fa fa-angle-left pull-right"></i>
              </span>
            </a>
            <ul class="treeview-menu">
              <li><a href="profil.php"><i class="fa fa-user"></i> Profil Saya</a></li>
              <li><a href="gantipassword.php"><i class="fa fa-lock"></i> Ganti Password</a></li>
              <li><a href="logout.php"><i class="fa fa-sign-out"></i> Logout</a></li>
            </ul>
          </li>
        </ul>
      </section>
    </aside>
