<?php 
// 1. Inisialisasi Session & Koneksi (Paling Atas)
if(!isset($_SESSION)) {
    session_start();
}
include '../koneksi.php';
include '../functions_app_settings.php';
include '../functions_color_settings.php';
include 'check_guru_bk_access.php';

// 2. Proteksi Halaman
if(!isset($_SESSION['level']) || $_SESSION['level'] != "guru_bk"){
    header("location:../admin.php?alert=belum_login");
    exit();
}

// 3. Ambil Pengaturan Aplikasi & Profil
$app_settings = function_exists('getAppSettings') ? getAppSettings($koneksi) : array();
$color_settings = function_exists('getColorSettings') ? getColorSettings($koneksi) : array();

$id_user = $_SESSION['id'];
$profil_res = mysqli_query($koneksi, "SELECT * FROM user WHERE user_id='$id_user'");
$profil = mysqli_fetch_assoc($profil_res);

$app_name = isset($app_settings['app_name']) && $app_settings['app_name'] !== '' ? $app_settings['app_name'] : 'SISBK';
$institution = isset($app_settings['app_author']) && $app_settings['app_author'] !== '' ? $app_settings['app_author'] : 'Madrasah Aliyah Yasmu';

// 4. Logika Favicon
$fav_from_setting = function_exists('getAppFavicon') ? getAppFavicon($app_settings, '') : '';
$favicon_path = $fav_from_setting !== '' ? '../' . $fav_from_setting : '../gambar/sistem/logo.png';
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>Guru BK - <?php echo htmlspecialchars($app_name . ' ' . $institution); ?></title>
  <link rel="icon" type="image/png" href="<?php echo $favicon_path; ?>">
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  
  <link rel="stylesheet" href="../assets/bower_components/bootstrap/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="../assets/bower_components/font-awesome/css/font-awesome.min.css">
  <link rel="stylesheet" href="../assets/bower_components/Ionicons/css/ionicons.min.css">
  <link rel="stylesheet" href="../assets/dist/css/AdminLTE.min.css">
  <link rel="stylesheet" href="../assets/dist/css/skins/_all-skins.min.css">
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">

  <style>
    /* CSS Fix agar sidebar dan menu bisa diklik/dropdown berjalan */
    .sidebar-menu { list-style: none; margin: 0; padding: 0; }
    .main-header .logo { display: flex; align-items: center; justify-content: center; }
    .user-panel > .image > img { width: 45px; height: 45px; object-fit: cover; }
  </style>
</head>
<body class="hold-transition skin-purple sidebar-mini">
<div class="wrapper">

  <header class="main-header">
    <?php
      $app_logo_path = function_exists('getAppLogo') ? getAppLogo($app_settings, '../gambar/sistem/logo.png') : '../gambar/sistem/logo.png';
    ?>
    <a href="guru_bk_dashboard.php" class="logo">
      <span class="logo-mini"><img src="<?php echo $app_logo_path; ?>" style="max-height:22px;"></span>
      <span class="logo-lg">
        <img src="<?php echo $app_logo_path; ?>" style="max-height:28px; margin-right:8px;">
        <b><?php echo htmlspecialchars($app_name); ?></b>
      </span>
    </a>
    
    <nav class="navbar navbar-static-top">
      <a href="#" class="sidebar-toggle" data-toggle="push-menu" role="button">
        <span class="sr-only">Toggle navigation</span>
      </a>

      <div class="navbar-custom-menu">
        <ul class="nav navbar-nav">
          <li class="dropdown notifications-menu">
            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
              <i class="fa fa-bell-o"></i>
              <?php 
                $u_id = $_SESSION['id'];
                $q_bk = mysqli_query($koneksi, "SELECT guru_bk_id FROM guru_bk WHERE user_id = '$u_id'");
                $g_data = mysqli_fetch_assoc($q_bk);
                $g_id = $g_data['guru_bk_id'] ?? 0;
                $notif_res = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM kasus_siswa WHERE guru_bk_id = '$g_id' AND status_kasus = 'Baru' AND sumber_kasus = 'Inisiatif Siswa'");
                $n_count = mysqli_fetch_assoc($notif_res);
                if($n_count['total'] > 0) echo '<span class="label label-warning">'.$n_count['total'].'</span>';
              ?>
            </a>
            <ul class="dropdown-menu">
              <li class="header">Anda memiliki <?php echo $n_count['total']; ?> notifikasi baru</li>
              <li><ul class="menu"></ul></li>
              <li class="footer"><a href="kasus_siswa.php">Lihat semua</a></li>
            </ul>
          </li>

          <li class="dropdown user user-menu">
            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
              <img src="../gambar/user/<?php echo $profil['user_foto']; ?>" class="user-image" style="object-fit:cover;">
              <span class="hidden-xs"><?php echo $profil['user_nama']; ?></span>
            </a>
            <ul class="dropdown-menu">
              <li class="user-header">
                <img src="../gambar/user/<?php echo $profil['user_foto']; ?>" class="img-circle" style="object-fit:cover;">
                <p><?php echo $profil['user_nama']; ?><small>Guru BK</small></p>
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
          <img src="../gambar/user/<?php echo $profil['user_foto']; ?>" class="img-circle" style="height:45px; width:45px; object-fit:cover;">
        </div>
        <div class="pull-left info">
          <p><?php echo $profil['user_nama'] ?></p>
          <a href="#"><i class="fa fa-circle text-success"></i> Online</a>
        </div>
      </div>

      <ul class="sidebar-menu" data-widget="tree">
        <li class="header">GURU BK NAVIGATION</li>
        
        <li><a href="guru_bk_dashboard.php"><i class="fa fa-dashboard"></i> <span>DASHBOARD</span></a></li>
        
        <li class="treeview">
          <a href="#"><i class="fa fa-folder-open"></i> <span>KASUS SISWA</span>
            <span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span>
          </a>
          <ul class="treeview-menu">
            <li><a href="kasus_siswa.php"><i class="fa fa-list"></i> Data Kasus Siswa</a></li>
            <li><a href="kasus_siswa_tambah.php"><i class="fa fa-plus"></i> Tambah Kasus Baru</a></li>
            <li><a href="laporan_kasus.php"><i class="fa fa-chart-line"></i> Laporan Kasus</a></li>
          </ul>
        </li>

        <li class="treeview">
          <a href="#"><i class="fa fa-book"></i> <span>JURNAL KASUS</span>
            <span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span>
          </a>
          <ul class="treeview-menu">
            <li><a href="jurnal.php"><i class="fa fa-list"></i> Data Jurnal</a></li>
            <li><a href="jurnal_tambah.php"><i class="fa fa-plus"></i> Tambah Jurnal</a></li>
          </ul>
        </li>

        <li class="treeview">
          <a href="#"><i class="fa fa-home"></i> <span>KUNJUNGAN RUMAH</span>
            <span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span>
          </a>
          <ul class="treeview-menu">
            <li><a href="kunjungan_rumah.php"><i class="fa fa-list"></i> Data Kunjungan</a></li>
            <li><a href="kunjungan_rumah_tambah.php"><i class="fa fa-plus"></i> Tambah Kunjungan</a></li>
            <li><a href="laporan_kunjungan_rumah.php"><i class="fa fa-chart-bar"></i> Laporan Kunjungan</a></li>
          </ul>
        </li>

        <li class="treeview">
          <a href="#"><i class="fa fa-graduation-cap"></i> <span>LAYANAN BK</span>
            <span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span>
          </a>
          <ul class="treeview-menu">
            <li><a href="layanan_bk.php"><i class="fa fa-list"></i> Data Layanan BK</a></li>
            <li><a href="layanan_bk_kalender.php"><i class="fa fa-calendar"></i> Kalender Layanan</a></li>
            <li><a href="layanan_bk_laporan.php"><i class="fa fa-chart-bar"></i> Laporan Layanan</a></li>
          </ul>
        </li>

        <li class="treeview">
          <a href="#"><i class="fa fa-star"></i> <span>POIN SISWA</span>
            <span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span>
          </a>
          <ul class="treeview-menu">
            <li><a href="input_prestasi.php"><i class="fa fa-trophy"></i> Data Prestasi</a></li>
            <li><a href="input_pelanggaran.php"><i class="fa fa-exclamation-triangle"></i> Data Pelanggaran</a></li>
            <li><a href="laporan.php"><i class="fa fa-chart-pie"></i> Laporan Poin</a></li>
            <li><a href="cetak_raport.php"><i class="fa fa-print"></i> Cetak Raport</a></li>
          </ul>
        </li>

        <li class="treeview">
          <a href="#"><i class="fa fa-user"></i> <span>PROFIL & AKUN</span>
            <span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span>
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