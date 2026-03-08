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

if(function_exists('getAppSettings')) {
    $app_settings = getAppSettings($koneksi);
}
if(function_exists('getColorSettings')) {
    $color_settings = getColorSettings($koneksi);
}

// Persiapkan data profil satu kali saja untuk efisiensi
$id_user = $_SESSION['id'] ?? null;
$profil = null;
if ($id_user) {
    $resultProfil = mysqli_query($koneksi, "SELECT * FROM user WHERE user_id='$id_user'");
    $profil = mysqli_fetch_assoc($resultProfil);
}

// Fungsi bantu untuk URL Foto (Opsional: bisa dipindah ke functions.php nanti)
$baseUrl = function_exists('getSupabaseBaseUrl') ? getSupabaseBaseUrl() : null;
$user_foto = (!empty($profil['user_foto'])) ? 
             ($baseUrl ? $baseUrl . 'gambar/user/' . $profil['user_foto'] : '../gambar/user/' . $profil['user_foto']) : 
             "../gambar/sistem/user.png";
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
  
  <?php
    $fav_from_setting = function_exists('getAppFavicon') ? getAppFavicon($app_settings, '') : '';
    $favicon_path = $fav_from_setting !== '' ? '../' . $fav_from_setting : '../gambar/sistem/logo.png';
    // Fallback sederhana jika file tidak ditemukan
    if (strpos($favicon_path, 'http') === false && !file_exists($favicon_path)) {
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
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">

  <style>
    /* Sidebar & Layout Fixes */
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
		object-fit: cover; /* Menjaga rasio foto agar tidak gepeng */
	}

	/* Mengatur jarak teks agar lebih rapi */
	.user-panel > .info {
		padding: 5px 5px 5px 15px;
	}
  </style>
</head>

<body class="hold-transition skin-purple sidebar-mini">
  <div class="wrapper">

    <header class="main-header">
      <?php
        $logo_from_setting = function_exists('getAppLogo') ? getAppLogo($app_settings, 'gambar/sistem/logo.png') : 'gambar/sistem/logo.png';
        $app_logo_path = (strpos($logo_from_setting, 'http') !== false) ? $logo_from_setting : '../' . $logo_from_setting;
      ?>
      <a href="index.php" class="logo">
        <span class="logo-mini"><img src="<?php echo $app_logo_path; ?>" alt="L" style="max-height:22px;"/></span>
        <span class="logo-lg">
          <img src="<?php echo $app_logo_path; ?>" alt="Logo" style="max-height:28px; margin-right:5px;"/>
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
                $q_notif = mysqli_query($koneksi,"SELECT COUNT(*) as total FROM notifikasi_rtl WHERE status_reminder = 'Belum'");
                $n_count = mysqli_fetch_assoc($q_notif);
                if($n_count['total'] > 0) echo '<span class="label label-warning">'.$n_count['total'].'</span>';
                ?>
              </a>
              <ul class="dropdown-menu">
                <li class="header">Ada <?php echo $n_count['total']; ?> RTL belum diproses</li>
                <li>
                  <ul class="menu">
                    <?php
                    $res_notif = mysqli_query($koneksi, "SELECT n.*, k.kasus_kode, s.siswa_nama FROM notifikasi_rtl n 
                                 LEFT JOIN jurnal_kasus j ON n.jurnal_id = j.jurnal_id
                                 LEFT JOIN kasus_siswa k ON j.kasus_id = k.kasus_id
                                 LEFT JOIN siswa s ON k.siswa_id = s.siswa_id
                                 WHERE n.status_reminder = 'Belum' ORDER BY n.tanggal_reminder ASC LIMIT 5");
                    while($n = mysqli_fetch_assoc($res_notif)){
                      echo "<li><a href='notifikasi_rtl.php'><i class='fa fa-warning text-yellow'></i> {$n['kasus_kode']} - {$n['siswa_nama']}</a></li>";
                    }
                    ?>
                  </ul>
                </li>
              </ul>
            </li>

            <li class="dropdown user user-menu">
              <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                <img src="<?php echo $user_foto; ?>" class="user-image" alt="User Image">
                <span class="hidden-xs"><?php echo htmlspecialchars($profil['user_nama'] ?? 'Admin'); ?></span>
              </a>
              <ul class="dropdown-menu">
                <li class="user-header">
                  <img src="<?php echo $user_foto; ?>" class="img-circle" alt="User Image">
                  <p>
                    <?php echo htmlspecialchars($profil['user_nama'] ?? 'Admin'); ?>
                    <small><?php echo strtoupper($_SESSION['level']); ?></small>
                  </p>
                </li>
                <li class="user-footer">
                  <div class="pull-left">
                    <a href="profil.php" class="btn btn-default btn-flat">Profil</a>
                  </div>
                  <div class="pull-right">
                    <a href="logout.php" class="btn btn-default btn-flat">Sign out</a>
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
			<img src="<?php echo $user_foto; ?>" class="img-circle" style="height:45px; width:45px; object-fit:cover;">
		  </div>
		  <div class="pull-left info">
			<p><?php echo htmlspecialchars($profil['user_nama'] ?? 'Admin'); ?></p>
			<a href="#"><i class="fa fa-circle text-success"></i> Online</a>
		  </div>
		</div>

        <ul class="sidebar-menu" data-widget="tree">
          <li class="header">MAIN NAVIGATION</li>
          <li><a href="index.php"><i class="fa fa-dashboard"></i> <span>DASHBOARD</span></a></li>
          
          <li class="treeview">
            <a href="#"><i class="fa fa-database"></i> <span>MASTER DATA</span>
              <span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span>
            </a>
            <ul class="treeview-menu">
              <li><a href="siswa.php"><i class="fa fa-circle-o"></i> Siswa</a></li>
              <li><a href="jurusan.php"><i class="fa fa-circle-o"></i> Jurusan</a></li>
              <li><a href="kelas.php"><i class="fa fa-circle-o"></i> Kelas</a></li>
              <li><a href="ta.php"><i class="fa fa-circle-o"></i> Tahun Ajaran</a></li>
              <li><a href="guru_bk.php"><i class="fa fa-circle-o"></i> Guru BK</a></li>
              <li><a href="user.php"><i class="fa fa-circle-o"></i> Admin</a></li>
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
              <li><a href="laporan_kunjungan_rumah.php"><i class="fa fa-file"></i> Laporan Kunjungan</a></li>
              <li><a href="notifikasi_rtl.php"><i class="fa fa-bell"></i> Notifikasi RTL</a></li>
              <li><a href="laporan_kasus.php"><i class="fa fa-file"></i> Laporan Kasus</a></li>
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
              <li><a href="layanan_bk_laporan.php"><i class="fa fa-file"></i> Laporan Layanan</a></li>
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
