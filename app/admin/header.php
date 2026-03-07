<?php 
include '../koneksi.php';
include '../functions_app_settings.php';
include '../functions_color_settings.php';

if(!isset($_SESSION)) {
    session_start();
}

// Cek session dengan aman
if(!isset($_SESSION['level']) || ($_SESSION['level'] != "administrator" && $_SESSION['level'] != "guru_bk")){
  header("location:../admin.php?alert=belum_login");
  exit();
}

// Ambil pengaturan aplikasi
$app_settings = array();
$color_settings = array();

// Cek apakah fungsi ada sebelum dipanggil
if(function_exists('getAppSettings')) {
    $app_settings = getAppSettings($koneksi);
}
if(function_exists('getColorSettings')) {
    $color_settings = getColorSettings($koneksi);
}
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <?php
    $app_name = isset($app_settings['app_name']) && $app_settings['app_name'] !== '' ? $app_settings['app_name'] : 'SISBK';
    $institution = isset($app_settings['app_author']) && $app_settings['app_author'] !== '' ? $app_settings['app_author'] : 'Madrasah Aliyah Yasmu';
  ?>
  <title>Administrator - <?php echo htmlspecialchars($app_name . ' ' . $institution); ?></title>
  
  <!-- Favicon: gunakan logo aplikasi bila ada, fallback ke favicon default -->
  <?php
    // favicon dari setting bila ada, fallback ke file default
    $fav_from_setting = function_exists('getAppFavicon') ? getAppFavicon($app_settings, '') : '';
    $favicon_path = $fav_from_setting !== '' ? '../' . $fav_from_setting : '../gambar/sistem/logo.png';
    if (!@fopen($favicon_path, 'r')) {
      $favicon_path = '../gambar/sistem/login_logo.png';
    }
  ?>
  <link rel="icon" type="image/png" href="<?php echo $favicon_path; ?>">
  
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
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
  
  <!-- Dynamic CSS disabled -->
  
  <!-- CSS untuk memperbaiki sidebar -->
  <style>
    .sidebar-menu {
      list-style: none;
      margin: 0;
      padding: 0;
    }
    
    .sidebar-menu > li {
      position: relative;
      margin: 0;
      padding: 0;
    }
    
    .sidebar-menu > li > a {
      position: relative;
      display: block;
      padding: 12px 5px 12px 15px;
      color: #b8c7ce;
      text-decoration: none;
    }
    
    .sidebar-menu > li > a:hover {
      color: #fff;
      background: #1e282c;
    }
    
    .sidebar-menu .treeview-menu {
      display: none;
      list-style: none;
      padding: 0;
      margin: 0;
      background: #2c3b41;
    }
    
    .sidebar-menu .treeview-menu > li {
      margin: 0;
    }
    
    .sidebar-menu .treeview-menu > li > a {
      display: block;
      padding: 5px 5px 5px 30px;
      font-size: 14px;
      color: #8aa4af;
      text-decoration: none;
    }
    
    .sidebar-menu .treeview-menu > li > a:hover {
      color: #fff;
      background: #2c3b41;
    }
    
    .sidebar-menu .treeview.active > .treeview-menu {
      display: block;
    }
    
    .sidebar-menu .treeview.active > a > .fa-angle-left {
      transform: rotate(-90deg);
    }

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
<body class="hold-transition skin-purple sidebar-mini">
  <div class="wrapper">

    <header class="main-header">
      <?php
        // Logo dari setting bila tersedia, fallback ke file default
        $logo_from_setting = function_exists('getAppLogo') ? getAppLogo($app_settings, 'gambar/sistem/logo.png') : 'gambar/sistem/logo.png';
        $app_logo_path = '../' . $logo_from_setting;
        if (!@fopen($app_logo_path, 'r')) {
          $app_logo_path = '../gambar/sistem/login_logo.png';
        }
      ?>
      <a href="index.php" class="logo">
        <!-- Tampilkan logo pada sidebar mini -->
        <span class="logo-mini" style="display:flex;align-items:center;justify-content:center;">
          <img src="<?php echo $app_logo_path; ?>" alt="Logo" style="max-height:22px; width:auto; display:block;"/>
        </span>
        <!-- Tampilkan logo penuh pada tampilan normal -->
        <span class="logo-lg" style="display:flex;align-items:center;gap:10px;">
          <img src="<?php echo $app_logo_path; ?>" alt="Logo" style="max-height:28px; width:auto; display:block;"/>
          <b><?php echo htmlspecialchars($app_name); ?></b>
        </span>
      </a>
      <nav class="navbar navbar-static-top">
        <a href="#" class="sidebar-toggle" data-toggle="push-menu" role="button">
          <span class="sr-only">Toggle navigation</span>
        </a>

        <div class="navbar-custom-menu">
          <ul class="nav navbar-nav">
            <!-- Notifikasi RTL -->
            <li class="dropdown notifications-menu">
              <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                <i class="fa fa-bell-o"></i>
                <?php 
                $notif_count = mysqli_query($koneksi,"SELECT COUNT(*) as total FROM notifikasi_rtl WHERE status_reminder = 'Belum'");
                $notif_count = mysqli_fetch_assoc($notif_count);
                if($notif_count['total'] > 0) {
                ?>
                <span class="label label-warning"><?php echo $notif_count['total']; ?></span>
                <?php } ?>
              </a>
              <ul class="dropdown-menu">
                <li class="header">Anda memiliki <?php echo $notif_count['total']; ?> notifikasi RTL</li>
                <li>
                  <ul class="menu">
                    <?php
                    $query_notif = "SELECT n.*, n.notif_id, k.kasus_id, k.kasus_kode, k.judul_kasus, s.siswa_nama 
                                   FROM notifikasi_rtl n 
                                   LEFT JOIN jurnal_kasus j ON n.jurnal_id = j.jurnal_id
                                   LEFT JOIN kasus_siswa k ON j.kasus_id = k.kasus_id
                                   LEFT JOIN siswa s ON k.siswa_id = s.siswa_id
                                   WHERE n.status_reminder = 'Belum' 
                                   ORDER BY n.tanggal_reminder ASC 
                                   LIMIT 5";
                    $result_notif = mysqli_query($koneksi, $query_notif);
                    if(mysqli_num_rows($result_notif) > 0) {
                      while($notif = mysqli_fetch_assoc($result_notif)) {
                        // Pastikan kasus_id ada sebelum membuat link
                        $kasus_id = isset($notif['kasus_id']) && !empty($notif['kasus_id']) ? $notif['kasus_id'] : 0;
                        $notif_id = isset($notif['notif_id']) ? $notif['notif_id'] : 0;
                        $link_url = ($kasus_id > 0) ? "kasus_siswa_detail.php?id=" . $kasus_id . "&notif_id=" . $notif_id : "#";
                    ?>
                    <li>
                      <a href="<?php echo $link_url; ?>">
                        <i class="fa fa-bell text-yellow"></i> 
                        <?php 
                        if(!empty($notif['kasus_kode']) && !empty($notif['siswa_nama'])) {
                          echo htmlspecialchars($notif['kasus_kode']) . ' - ' . htmlspecialchars($notif['siswa_nama']);
                        } else {
                          echo 'Notifikasi RTL';
                        }
                        ?>
                        <br><small><?php echo date('d/m/Y', strtotime($notif['tanggal_reminder'])); ?></small>
                      </a>
                    </li>
                    <?php 
                      }
                    } else {
                    ?>
                    <li>
                      <a href="#">
                        <i class="fa fa-info text-blue"></i> Tidak ada notifikasi
                      </a>
                    </li>
                    <?php } ?>
                  </ul>
                </li>
                <li class="footer"><a href="notifikasi_rtl.php">Lihat semua notifikasi</a></li>
              </ul>
            </li>

            <li class="dropdown user user-menu">
              <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                <?php 
                $id_user = $_SESSION['id'] ?? null;
                $profil = null;
                if ($id_user) {
                  $resultProfil = mysqli_query($koneksi,"select * from user where user_id='$id_user'");
                  $profil = mysqli_fetch_assoc($resultProfil);
                }

                $user_foto = $profil['user_foto'] ?? '';
                if(empty($user_foto)){ 
                ?>
                  <img src="../gambar/sistem/user.png" class="user-image">
                <?php }else{ ?>
                  <img src="../gambar/user/<?php echo htmlspecialchars($user_foto); ?>" class="user-image">
                <?php } ?>
                <span class="hidden-xs">
                  <?php echo htmlspecialchars($_SESSION['level'] ?? ($profil['user_nama'] ?? 'Administrator')); ?>
                </span>
              </a>
            </li>
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
            $id_user = $_SESSION['id'] ?? null;
            $profil = null;
            if ($id_user) {
              $resultProfilSidebar = mysqli_query($koneksi,"select * from user where user_id='$id_user'");
              $profil = mysqli_fetch_assoc($resultProfilSidebar);
            }

            $user_foto_sidebar = $profil['user_foto'] ?? '';
            if(empty($user_foto_sidebar)){ 
            ?>
              <img src="../gambar/sistem/user.png" class="img-circle" style="height:45px; width: 45px;">
            <?php }else{ ?>
              <img src="../gambar/user/<?php echo htmlspecialchars($user_foto_sidebar); ?>" class="img-circle" style="height:45px; width: 45px;" style="max-height:45px">
            <?php } ?>
          </div>
          <div class="pull-left info">
            <p><?php echo htmlspecialchars($profil['user_nama'] ?? 'Administrator'); ?></p>
            <a href="#"><i class="fa fa-circle text-success"></i> Online</a>
          </div>
        </div>

        <ul class="sidebar-menu" data-widget="tree">
          <li class="header">MAIN NAVIGATION</li>
          
          <!-- Dashboard -->
          <li><a href="index.php"><i class="fa fa-dashboard"></i> <span>DASHBOARD</span></a></li>
          
          <!-- Master Data -->
          <li class="treeview">
            <a href="#">
              <i class="fa fa-database"></i>
              <span>MASTER DATA</span>
              <span class="pull-right-container">
                <i class="fa fa-angle-left pull-right"></i>
              </span>
            </a>
            <ul class="treeview-menu">
              <li><a href="siswa.php"><i class="fa fa-users"></i> Data Siswa</a></li>
              <li><a href="jurusan.php"><i class="fa fa-university"></i> Data Jurusan</a></li>
              <li><a href="kelas.php"><i class="fa fa-book"></i> Data Kelas</a></li>
              <li><a href="ta.php"><i class="fa fa-calendar"></i> Data Tahun Ajaran</a></li>
              <li><a href="guru_bk.php"><i class="fa fa-user-md"></i> Data Guru BK</a></li>
              <li><a href="user.php"><i class="fa fa-user-secret"></i> Data Admin</a></li>
            </ul>
          </li>
          
          <!-- Poin Siswa -->
          <li class="treeview">
            <a href="#">
              <i class="fa fa-star"></i>
              <span>POIN SISWA</span>
              <span class="pull-right-container">
                <i class="fa fa-angle-left pull-right"></i>
              </span>
            </a>
            <ul class="treeview-menu">
              <li><a href="input_prestasi.php"><i class="fa fa-trophy"></i> Input Prestasi</a></li>
              <li><a href="input_pelanggaran.php"><i class="fa fa-warning"></i> Input Pelanggaran</a></li>
              <li><a href="prestasi.php"><i class="fa fa-list-alt"></i> Data Prestasi</a></li>
              <li><a href="pelanggaran.php"><i class="fa fa-list-alt"></i> Data Pelanggaran</a></li>
              <li><a href="laporan.php"><i class="fa fa-file"></i> Laporan Poin</a></li>
              <li><a href="cetak_raport.php"><i class="fa fa-print"></i> Cetak Raport</a></li>
            </ul>
          </li>
          
          <!-- Konseling BK -->
          <li class="treeview">
            <a href="#">
              <i class="fa fa-heart"></i>
              <span>KONSELING BK</span>
              <span class="pull-right-container">
                <i class="fa fa-angle-left pull-right"></i>
              </span>
            </a>
            <ul class="treeview-menu">
              <li><a href="kasus_siswa.php"><i class="fa fa-folder-open"></i> Data Kasus Siswa</a></li>
              <li><a href="kasus_siswa_tambah.php"><i class="fa fa-plus"></i> Tambah Kasus Baru</a></li>
              <li><a href="kunjungan_rumah.php"><i class="fa fa-home"></i> Kunjungan Rumah</a></li>
              <li><a href="kunjungan_rumah_tambah.php"><i class="fa fa-plus-circle"></i> Tambah Kunjungan</a></li>
              <li><a href="laporan_kunjungan_rumah.php"><i class="fa fa-chart-bar"></i> Laporan Kunjungan</a></li>
              <li><a href="notifikasi_rtl.php"><i class="fa fa-bell"></i> Notifikasi RTL</a></li>
              <li><a href="laporan_kasus.php"><i class="fa fa-chart-line"></i> Laporan Kasus</a></li>
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
              <li><a href="layanan_bk.php"><i class="fa fa-list"></i> Data Layanan BK</a></li>
              <li><a href="layanan_bk_tambah.php"><i class="fa fa-plus"></i> Tambah Layanan</a></li>
              <li><a href="layanan_bk_kalender.php"><i class="fa fa-calendar"></i> Kalender Layanan</a></li>
              <li><a href="layanan_bk_laporan.php"><i class="fa fa-chart-bar"></i> Laporan Layanan</a></li>
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
              <li><a href="pengaturan_aplikasi.php"><i class="fa fa-cogs"></i> Pengaturan Aplikasi</a></li>
              <li><a href="gantipassword.php"><i class="fa fa-lock"></i> Ganti Password</a></li>
              <li><a href="logout.php"><i class="fa fa-sign-out"></i> Logout</a></li>
            </ul>
          </li>
        </ul>
      </section>
      <!-- /.sidebar -->
    </aside>
