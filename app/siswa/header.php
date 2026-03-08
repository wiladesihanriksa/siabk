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

// Ambil pengaturan aplikasi dan warna
$app_settings = getAppSettings($koneksi);
$color_settings = getColorSettings($koneksi);
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  <title><?php echo getSetting($app_settings, 'app_name', 'Siswa - E-Point'); ?></title>
  
  <?php
    // Logika Favicon Berjenjang (Fallback)
    $fav_from_setting = getAppFavicon($app_settings, '');
    $favicon_path = ($fav_from_setting !== '') ? '../' . $fav_from_setting : '../gambar/sistem/favicon.png';
    
    // Validasi file fisik
    if (!file_exists($favicon_path)) {
        $favicon_path = '../gambar/sistem/logo.png';
        if (!file_exists($favicon_path)) {
            $favicon_path = '../gambar/sistem/login_logo.png';
        }
    }

    $favicon_ext = strtolower(pathinfo($favicon_path, PATHINFO_EXTENSION));
    $favicon_type = ($favicon_ext == 'png') ? 'image/png' : 'image/x-icon';
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
  
  <style>
    /* Sinkronisasi Logo Style dengan Admin */
    .main-header .logo { display: flex !important; align-items: center; justify-content: center; }
    .main-header .logo .logo-mini { display: none; }
    .main-header .logo .logo-lg { display: inline-flex; align-items: center; gap: 8px; }
    .sidebar-mini.sidebar-collapse .main-header .logo .logo-mini { display: inline-flex !important; }
    .sidebar-mini.sidebar-collapse .main-header .logo .logo-lg { display: none !important; }
    
    /* Perbaikan Visual User Panel Sidebar */
    .user-panel > .image > img { height: 45px !important; width: 45px !important; object-fit: cover; }
    .sidebar-menu > li > a { text-transform: uppercase; font-size: 12px; }
  </style>
</head>

<body class="hold-transition <?php echo getSetting($color_settings, 'theme_skin', 'skin-green'); ?> sidebar-mini">
  <div class="wrapper">

    <header class="main-header">
      <?php
        $logo_from_setting = getAppLogo($app_settings, 'gambar/sistem/logo.png');
        $app_logo_path = '../' . $logo_from_setting;
        if (!file_exists($app_logo_path)) {
            $app_logo_path = '../gambar/sistem/logo.png';
        }
        $app_name = getSetting($app_settings, 'app_name', 'E-<b>Point</b>');
      ?>
      
      <a href="index.php" class="logo">
        <span class="logo-mini">
          <img src="<?php echo $app_logo_path; ?>" alt="L" style="max-height:30px; width:auto;"/>
        </span>
        <span class="logo-lg">
          <img src="<?php echo $app_logo_path; ?>" alt="Logo" style="max-height:40px; width:auto;"/>
          <b><?php echo $app_name; ?></b>
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
                <img src="../gambar/sistem/user.png" class="user-image" alt="User Image">
                <span class="hidden-xs">Siswa: <?php echo $_SESSION['nama']; ?></span>
              </a>
              <ul class="dropdown-menu">
                <li class="user-header" style="height: auto; padding-bottom: 15px;">
                  <img src="../gambar/sistem/user.png" class="img-circle" alt="User Image">
                  <p>
                    <?php echo $_SESSION['nama']; ?>
                    <small>Level: Siswa Aktif</small>
                  </p>
                </li>
                <li class="user-footer">
                  <div class="pull-left">
                    <a href="profil.php" class="btn btn-default btn-flat">Profil</a>
                  </div>
                  <div class="pull-right">
                    <a href="logout.php" class="btn btn-danger btn-flat">Logout</a>
                  </div>
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
            <img src="../gambar/sistem/user.png" class="img-circle" alt="User Image">
          </div>
          <div class="pull-left info">
            <p><?php echo substr($_SESSION['nama'], 0, 15); ?>...</p>
            <a href="#"><i class="fa fa-circle text-success"></i> Online</a>
          </div>
        </div>

        <ul class="sidebar-menu" data-widget="tree">
          <li class="header">MENU UTAMA</li>
          
          <li class="<?php echo (basename($_SERVER['PHP_SELF']) == 'index.php') ? 'active' : ''; ?>">
            <a href="index.php"><i class="fa fa-dashboard"></i> <span>DASHBOARD</span></a>
          </li>
          
          <li class="treeview <?php echo in_array(basename($_SERVER['PHP_SELF']), ['prestasi_saya.php', 'pelanggaran_saya.php']) ? 'active' : ''; ?>">
            <a href="#">
              <i class="fa fa-user"></i> <span>DATA SAYA</span>
              <span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span>
            </a>
            <ul class="treeview-menu">
              <li class="<?php echo (basename($_SERVER['PHP_SELF']) == 'prestasi_saya.php') ? 'active' : ''; ?>">
                <a href="prestasi_saya.php"><i class="fa fa-trophy"></i> Prestasi Saya</a>
              </li>
              <li class="<?php echo (basename($_SERVER['PHP_SELF']) == 'pelanggaran_saya.php') ? 'active' : ''; ?>">
                <a href="pelanggaran_saya.php"><i class="fa fa-warning"></i> Pelanggaran Saya</a>
              </li>
            </ul>
          </li>

          <li class="treeview <?php echo in_array(basename($_SERVER['PHP_SELF']), ['prestasi.php', 'pelanggaran.php']) ? 'active' : ''; ?>">
            <a href="#">
              <i class="fa fa-eye"></i> <span>LIHAT DATA</span>
              <span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span>
            </a>
            <ul class="treeview-menu">
              <li><a href="prestasi.php"><i class="fa fa-list-alt"></i> Data Prestasi</a></li>
              <li><a href="pelanggaran.php"><i class="fa fa-list-alt"></i> Data Pelanggaran</a></li>
            </ul>
          </li>

          <li class="treeview <?php echo in_array(basename($_SERVER['PHP_SELF']), ['konseling_saya.php', 'konseling_ajukan.php']) ? 'active' : ''; ?>">
            <a href="#">
              <i class="fa fa-comments-o"></i> <span>KONSELING</span>
              <span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span>
            </a>
            <ul class="treeview-menu">
              <li><a href="konseling_saya.php"><i class="fa fa-list"></i> Konseling Saya</a></li>
              <li><a href="konseling_ajukan.php"><i class="fa fa-plus"></i> Ajukan Konseling</a></li>
            </ul>
          </li>

          <li class="header">AKUN</li>
          <li class="<?php echo (basename($_SERVER['PHP_SELF']) == 'profil.php') ? 'active' : ''; ?>">
            <a href="profil.php"><i class="fa fa-user-circle"></i> <span>PROFIL</span></a>
          </li>
          <li class="<?php echo (basename($_SERVER['PHP_SELF']) == 'gantipassword.php') ? 'active' : ''; ?>">
            <a href="gantipassword.php"><i class="fa fa-lock"></i> <span>GANTI PASSWORD</span></a>
          </li>
          <li>
            <a href="logout.php"><i class="fa fa-sign-out text-red"></i> <span class="text-red">LOGOUT</span></a>
          </li>
        </ul>
      </section>
    </aside>