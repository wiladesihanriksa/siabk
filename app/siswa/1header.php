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
  
  <?php 
  $favicon = getAppFavicon($app_settings);
  if(!empty($favicon)): 
  ?>
  <link rel="icon" type="image/x-icon" href="<?php echo $favicon; ?>">
  <?php endif; ?>
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

</head>
<body class="hold-transition skin-green sidebar-mini">
  <div class="wrapper">

    <header class="main-header">
      <a href="index.php" class="logo">
        <span class="logo-mini">
          <?php 
          $logo = getAppLogo($app_settings);
          if($logo != 'gambar/sistem/user.png'): 
          ?>
            <img src="<?php echo $logo; ?>" style="height: 30px; width: 30px;">
          <?php else: ?>
            <i class="fa fa-trophy"></i>
          <?php endif; ?>
        </span>
        <span class="logo-lg">
          <?php 
          $logo = getAppLogo($app_settings);
          if($logo != 'gambar/sistem/user.png'): 
          ?>
            <img src="<?php echo $logo; ?>" style="height: 40px; margin-right: 10px; display: inline-block; vertical-align: middle;">
          <?php endif; ?>
          <?php echo getSetting($app_settings, 'app_name', 'E-<b>Point</b>'); ?>
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
