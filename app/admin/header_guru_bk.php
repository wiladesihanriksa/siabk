<?php 
// 1. Inisialisasi Session & Koneksi
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

// 3. Ambil Data Settings & Profil (Satu kali proses)
$app_settings = function_exists('getAppSettings') ? getAppSettings($koneksi) : array();
$color_settings = function_exists('getColorSettings') ? getColorSettings($koneksi) : array();
$id_user = $_SESSION['id'];
$profil_query = mysqli_query($koneksi, "SELECT * FROM user WHERE user_id='$id_user'");
$profil = mysqli_fetch_assoc($profil_query);

// 4. Logika Gambar Profil & Favicon (Satu variabel untuk semua)
$baseUrl = function_exists('getSupabaseBaseUrl') ? getSupabaseBaseUrl() : null;
$user_foto_db = $profil['user_foto'];
$img_src = (empty($user_foto_db)) ? "../gambar/sistem/user.png" : ($baseUrl ? $baseUrl . 'gambar/user/' . $user_foto_db : '../gambar/user/' . $user_foto_db);

$app_name = isset($app_settings['app_name']) && $app_settings['app_name'] !== '' ? $app_settings['app_name'] : 'SIABK';
$institution = isset($app_settings['app_author']) && $app_settings['app_author'] !== '' ? $app_settings['app_author'] : 'Sekolah';

$fav_from_setting = function_exists('getAppFavicon') ? getAppFavicon($app_settings, '') : '';
$favicon_path = $fav_from_setting !== '' ? '../' . $fav_from_setting : '../gambar/sistem/logo.png';
if (strpos($favicon_path, 'http') === false && !file_exists($favicon_path)) {
    $favicon_path = '../gambar/sistem/login_logo.png';
}

// Tambahan: Jalankan fungsi akses
checkGuruBkAccess();
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>Guru BK - <?php echo htmlspecialchars($app_name . ' ' . $institution); ?></title>
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  
  <link rel="stylesheet" href="../assets/bower_components/bootstrap/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="../assets/bower_components/font-awesome/css/font-awesome.min.css">
  <link rel="stylesheet" href="../assets/dist/css/AdminLTE.min.css">
  <link rel="stylesheet" href="../assets/dist/css/skins/_all-skins.min.css">
  <link rel="icon" type="image/png" href="<?php echo $favicon_path; ?>">
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">

  <style>
    .user-panel > .image > img { width: 45px !important; height: 45px !important; object-fit: cover; border-radius: 50%; }
    .main-header .logo .logo-lg { display: inline-flex; align-items: center; gap: 8px; }
    .sidebar-mini.sidebar-collapse .user-panel { display: none !important; }
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
        <img src="<?php echo $app_logo_path; ?>" style="max-height:28px;">
        <b><?php echo htmlspecialchars($app_name); ?></b>
      </span>
    </a>
    
    <nav class="navbar navbar-static-top">
      <a href="#" class="sidebar-toggle" data-toggle="push-menu" role="button">
        <span class="sr-only">Toggle navigation</span>
      </a>

      <div class="navbar-custom-menu">
        <ul class="nav navbar-nav">
          <?php 
          $u_id = $_SESSION['id'];
          $q_bk = mysqli_query($koneksi, "SELECT guru_bk_id FROM guru_bk WHERE user_id = '$u_id'");
          $g_data = mysqli_fetch_assoc($q_bk);
          $g_id = $g_data['guru_bk_id'] ?? 0;
          
          $c_kasus = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) as tot FROM kasus_siswa WHERE guru_bk_id = '$g_id' AND status_kasus = 'Baru' AND sumber_kasus = 'Inisiatif Siswa'"));
          $c_feed = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) as tot FROM feedback_siswa f JOIN jurnal_kasus j ON f.jurnal_id = j.jurnal_id JOIN kasus_siswa k ON j.kasus_id = k.kasus_id WHERE k.guru_bk_id = '$g_id' AND (f.is_read = 0 OR f.is_read IS NULL)"));
          $total_all = $c_kasus['tot'] + $c_feed['tot'];
          ?>
          
          <li class="dropdown notifications-menu">
            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
              <i class="fa fa-bell-o"></i>
              <?php if($total_all > 0) echo "<span class='label label-warning'>$total_all</span>"; ?>
            </a>
            <ul class="dropdown-menu">
              <li class="header">Anda memiliki <?php echo $total_all; ?> notifikasi</li>
              <li>
                <ul class="menu">
                  </ul>
              </li>
            </ul>
          </li>

          <li class="dropdown user user-menu">
            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
              <img src="<?php echo $img_src; ?>" class="user-image" alt="User Image">
              <span class="hidden-xs"><?php echo htmlspecialchars($profil['user_nama']); ?></span>
            </a>
            <ul class="dropdown-menu">
              <li class="user-header">
                <img src="<?php echo $img_src; ?>" class="img-circle" alt="User Image">
                <p><?php echo htmlspecialchars($profil['user_nama']); ?><small>Guru BK</small></p>
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
          <img src="<?php echo $img_src; ?>" class="img-circle" style="height:45px; width:45px;">
        </div>
        <div class="pull-left info">
          <p><?php echo htmlspecialchars($profil['user_nama']); ?></p>
          <a href="#"><i class="fa fa-circle text-success"></i> Online</a>
        </div>
      </div>
      
      <ul class="sidebar-menu" data-widget="tree">
        <li class="header">GURU BK NAVIGATION</li>
        <li><a href="guru_bk_dashboard.php"><i class="fa fa-dashboard"></i> <span>DASHBOARD</span></a></li>
        </ul>
    </section>
  </aside>