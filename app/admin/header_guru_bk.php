<?php 
// =====================================================
// HEADER GURU BK - 2025
// =====================================================

include '../koneksi.php';
include '../functions_app_settings.php';
include '../functions_color_settings.php';
include 'check_guru_bk_access.php';

// 1. Ambil data profil & definisikan foto profil SEKALI saja di atas
$id_user = $_SESSION['id'];
$profil_query = mysqli_query($koneksi, "SELECT * FROM user WHERE user_id='$id_user'");
$profil = mysqli_fetch_assoc($profil_query);

$baseUrl = function_exists('getSupabaseBaseUrl') ? getSupabaseBaseUrl() : null;
$foto_db = $profil['user_foto'];

if (empty($foto_db)) {
    $user_foto = "../gambar/sistem/user.png";
} else {
    $user_foto = $baseUrl ? $baseUrl . 'gambar/user/' . $foto_db : "../gambar/user/" . $foto_db;
}

// 2. Penentuan Favicon
$fav_setting = function_exists('getAppFavicon') ? getAppFavicon($app_settings, '') : '';
$favicon_path = $fav_setting !== '' ? '../' . $fav_setting : '../gambar/sistem/logo.png';
// Gunakan pengecekan yang lebih ringan
if (!file_exists($favicon_path)) {
    $favicon_path = '../gambar/sistem/login_logo.png';
}
?>

<!DOCTYPE html>
<html>
<head>
  <title>Guru BK - <?php echo htmlspecialchars($app_name . ' ' . $institution); ?></title>
  <link rel="icon" type="image/png" href="<?php echo $favicon_path; ?>?v=1">
  
  <style>
    /* Gabungkan perbaikan sidebar agar lebih efisien */
    .user-panel > .image > img {
        width: 45px !important; height: 45px !important;
        object-fit: cover; border-radius: 50%;
    }
    .main-header .logo .logo-lg { display: inline-flex; align-items: center; gap: 8px; }
    .sidebar-mini.sidebar-collapse .main-header .logo .logo-mini { display: inline-flex !important; }
    .sidebar-mini.sidebar-collapse .main-header .logo .logo-lg,
    .sidebar-mini.sidebar-collapse .user-panel { display: none !important; }
  </style>


</head>
<body class="hold-transition skin-purple sidebar-mini">
  <div class="wrapper">

    <header class="main-header">
      <?php
        // Logo dari setting bila tersedia, fallback ke file default
        $app_logo_path = function_exists('getAppLogo') ? getAppLogo($app_settings, '../gambar/sistem/logo.png') : '../gambar/sistem/logo.png';
        if (!@fopen($app_logo_path, 'r')) {
          $app_logo_path = '../gambar/sistem/login_logo.png';
        }
      ?>
      <a href="guru_bk_dashboard.php" class="logo">
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
            <!-- Notifikasi Konseling dari Siswa -->
            <li class="dropdown notifications-menu">
              <a href="#" class="dropdown-toggle" data-toggle="dropdown" id="notif-bell">
                <i class="fa fa-bell-o"></i>
                <?php 
                // Hitung konseling baru dan feedback baru dari siswa untuk guru BK ini
                // Gunakan query yang lebih eksplisit untuk memastikan hanya menghitung status "Baru"
                // Pastikan query dihitung ulang setiap kali dengan menghindari cache
                $user_id = $_SESSION['id'];
                $guru_bk_query = mysqli_query($koneksi, "SELECT guru_bk_id FROM guru_bk WHERE user_id = '$user_id'");
                $total_notif = 0;
                $total_feedback = 0;
                $guru_bk_id = null;
                
                if($guru_bk_query && mysqli_num_rows($guru_bk_query) > 0) {
                  $guru_bk_data = mysqli_fetch_assoc($guru_bk_query);
                  $guru_bk_id = $guru_bk_data['guru_bk_id'];
                  
                  // Hitung konseling baru - pastikan hanya menghitung yang benar-benar "Baru"
                  $notif_count_query = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM kasus_siswa 
                                                               WHERE guru_bk_id = '$guru_bk_id' 
                                                               AND status_kasus = 'Baru' 
                                                               AND sumber_kasus = 'Inisiatif Siswa'");
                  if($notif_count_query) {
                    $notif_count = mysqli_fetch_assoc($notif_count_query);
                    $total_notif = (int)$notif_count['total'];
                  }
                  
                  // Hitung feedback baru (hanya yang belum dibaca)
                  $feedback_count_query = mysqli_query($koneksi, "SELECT COUNT(*) as total 
                                                                   FROM feedback_siswa f
                                                                   JOIN jurnal_kasus j ON f.jurnal_id = j.jurnal_id
                                                                   JOIN kasus_siswa k ON j.kasus_id = k.kasus_id
                                                                   WHERE k.guru_bk_id = '$guru_bk_id'
                                                                   AND (f.is_read = 0 OR f.is_read IS NULL)
                                                                   AND f.created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)");
                  if($feedback_count_query) {
                    $feedback_count = mysqli_fetch_assoc($feedback_count_query);
                    $total_feedback = $feedback_count['total'];
                  }
                  
                  $total_all = $total_notif + $total_feedback;
                  if($total_all > 0) {
                ?>
                <span class="label label-warning"><?php echo $total_all; ?></span>
                <?php 
                  }
                }
                ?>
              </a>
              <ul class="dropdown-menu">
                <li class="header">
                  <?php 
                  $total_all = (isset($total_notif) ? $total_notif : 0) + (isset($total_feedback) ? $total_feedback : 0);
                  if($total_all > 0) {
                    echo "Anda memiliki " . $total_all . " notifikasi baru";
                  } else {
                    echo "Tidak ada notifikasi baru";
                  }
                  ?>
                </li>
                <li>
                  <ul class="menu">
                    <?php
                    if($guru_bk_id && $total_notif > 0) {
                      $query_notif = "SELECT k.*, s.siswa_nama, s.siswa_nis 
                                     FROM kasus_siswa k 
                                     LEFT JOIN siswa s ON k.siswa_id = s.siswa_id
                                     WHERE k.guru_bk_id = '$guru_bk_id' 
                                     AND TRIM(k.status_kasus) = 'Baru' 
                                     AND TRIM(k.sumber_kasus) = 'Inisiatif Siswa'
                                     ORDER BY k.created_at DESC 
                                     LIMIT 3";
                      $result_notif = mysqli_query($koneksi, $query_notif);
                      if($result_notif && mysqli_num_rows($result_notif) > 0) {
                        while($notif = mysqli_fetch_assoc($result_notif)) {
                    ?>
                    <li>
                      <a href="kasus_siswa_mark_read.php?id=<?php echo $notif['kasus_id']; ?>">
                        <i class="fa fa-heart text-red"></i> 
                        <strong>Pengajuan Konseling Baru</strong><br>
                        <?php echo htmlspecialchars($notif['siswa_nama']); ?> (<?php echo $notif['siswa_nis']; ?>)
                        <br><small><?php echo htmlspecialchars($notif['judul_kasus']); ?></small>
                        <br><small class="text-muted"><?php echo date('d/m/Y H:i', strtotime($notif['created_at'])); ?></small>
                      </a>
                    </li>
                    <?php 
                        }
                      }
                    }
                    
                    // Tampilkan feedback baru (hanya yang belum dibaca)
                    if($guru_bk_id && $total_feedback > 0) {
                      $query_feedback = "SELECT f.*, s.siswa_nama, s.siswa_nis, k.kasus_id, k.kasus_kode, j.tanggal_konseling
                                        FROM feedback_siswa f
                                        JOIN jurnal_kasus j ON f.jurnal_id = j.jurnal_id
                                        JOIN kasus_siswa k ON j.kasus_id = k.kasus_id
                                        JOIN siswa s ON f.siswa_id = s.siswa_id
                                        WHERE k.guru_bk_id = '$guru_bk_id'
                                        AND (f.is_read = 0 OR f.is_read IS NULL)
                                        AND f.created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)
                                        ORDER BY f.created_at DESC 
                                        LIMIT 3";
                      $result_feedback = mysqli_query($koneksi, $query_feedback);
                      if($result_feedback && mysqli_num_rows($result_feedback) > 0) {
                        while($feedback = mysqli_fetch_assoc($result_feedback)) {
                    ?>
                    <li>
                      <a href="kasus_siswa_detail.php?id=<?php echo $feedback['kasus_id']; ?>&feedback_id=<?php echo $feedback['feedback_id']; ?>">
                        <i class="fa fa-commenting text-green"></i> 
                        <strong>Feedback dari Siswa</strong><br>
                        <?php echo htmlspecialchars($feedback['siswa_nama']); ?> (<?php echo $feedback['siswa_nis']; ?>)
                        <br><small><?php echo htmlspecialchars(substr($feedback['feedback_text'], 0, 50)); ?>...</small>
                        <br><small class="text-muted"><?php echo date('d/m/Y H:i', strtotime($feedback['created_at'])); ?></small>
                      </a>
                    </li>
                    <?php 
                        }
                      }
                    }
                    
                    if((!isset($total_notif) || $total_notif == 0) && (!isset($total_feedback) || $total_feedback == 0)) {
                    ?>
                    <li>
                      <a href="#">
                        <i class="fa fa-info text-blue"></i> Tidak ada notifikasi baru
                      </a>
                    </li>
                    <?php } ?>
                  </ul>
                </li>
                <?php if(isset($total_all) && $total_all > 0): ?>
                <li class="footer"><a href="kasus_siswa.php">Lihat semua</a></li>
                <?php endif; ?>
              </ul>
            </li>
            
            <li class="dropdown user user-menu">
				<a href="#" class="dropdown-toggle" data-toggle="dropdown">
				  <?php 
					$baseUrl = function_exists('getSupabaseBaseUrl') ? getSupabaseBaseUrl() : null;
					$user_foto = $profil['user_foto'];
					$img_src = (empty($user_foto)) ? "../gambar/sistem/user.png" : ($baseUrl ? $baseUrl . 'gambar/user/' . $user_foto : '../gambar/user/' . $user_foto);
				  ?>
				  <img src="<?php echo $img_src; ?>" class="user-image" alt="User Image" style="object-fit:cover;">
				  <span class="hidden-xs"><?php echo $profil['user_nama']; ?></span>
				</a>
              <ul class="dropdown-menu">
				<li class="user-header">
				  <img src="<?php echo $img_src; ?>" class="img-circle" alt="User Image" style="object-fit:cover;">
				  <p>
					<?php echo $profil['user_nama']; ?>
					<small>Guru BK</small>
				  </p>
				</li>
                <li class="user-footer">
                  <div class="pull-left">
                    <a href="profil.php" class="btn btn-default btn-flat">Profile</a>
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
			<?php 
			$baseUrl = function_exists('getSupabaseBaseUrl') ? getSupabaseBaseUrl() : null;
			$user_foto = $profil['user_foto'];
			
			// Logika URL: Jika ada baseUrl (di Render) gunakan Supabase, jika tidak gunakan lokal
			if (empty($user_foto)) {
				$img_src = "../gambar/sistem/user.png";
			} else {
				$img_src = $baseUrl ? $baseUrl . 'gambar/user/' . $user_foto : "../gambar/user/" . $user_foto;
			}
			?>
			<img src="<?php echo $img_src; ?>" class="img-circle" alt="User Image" style="height:45px; width:45px; object-fit:cover;">
		  </div>
		  <div class="pull-left info">
			<p><?php echo $profil['user_nama'] ?></p>
			<a href="#"><i class="fa fa-circle text-success"></i> Online</a>
		  </div>
		</div>

        <ul class="sidebar-menu" data-widget="tree">
          <li class="header">GURU BK NAVIGATION</li>
          
          <!-- Dashboard -->
          <li><a href="guru_bk_dashboard.php"><i class="fa fa-dashboard"></i> <span>DASHBOARD</span></a></li>
          
          <!-- Kasus Siswa -->
          <li class="treeview">
            <a href="#">
              <i class="fa fa-folder-open"></i>
              <span>KASUS SISWA</span>
              <span class="pull-right-container">
                <i class="fa fa-angle-left pull-right"></i>
              </span>
            </a>
            <ul class="treeview-menu">
              <li><a href="kasus_siswa.php"><i class="fa fa-list"></i> Data Kasus Siswa</a></li>
              <li><a href="kasus_siswa_tambah.php"><i class="fa fa-plus"></i> Tambah Kasus Baru</a></li>
              <li><a href="laporan_kasus.php"><i class="fa fa-chart-line"></i> Laporan Kasus</a></li>
            </ul>
          </li>
          
          <!-- Jurnal Kasus -->
          <li class="treeview">
            <a href="#">
              <i class="fa fa-book"></i>
              <span>JURNAL KASUS</span>
              <span class="pull-right-container">
                <i class="fa fa-angle-left pull-right"></i>
              </span>
            </a>
            <ul class="treeview-menu">
              <li><a href="jurnal.php"><i class="fa fa-list"></i> Data Jurnal</a></li>
              <li><a href="jurnal_tambah.php"><i class="fa fa-plus"></i> Tambah Jurnal</a></li>
            </ul>
          </li>
          
          <!-- Kunjungan Rumah -->
          <li class="treeview">
            <a href="#">
              <i class="fa fa-home"></i>
              <span>KUNJUNGAN RUMAH</span>
              <span class="pull-right-container">
                <i class="fa fa-angle-left pull-right"></i>
              </span>
            </a>
            <ul class="treeview-menu">
              <li><a href="kunjungan_rumah.php"><i class="fa fa-list"></i> Data Kunjungan</a></li>
              <li><a href="kunjungan_rumah_tambah.php"><i class="fa fa-plus"></i> Tambah Kunjungan</a></li>
              <li><a href="laporan_kunjungan_rumah.php"><i class="fa fa-chart-bar"></i> Laporan Kunjungan</a></li>
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
              <li><a href="layanan_bk_kalender.php"><i class="fa fa-calendar"></i> Kalender Layanan</a></li>
              <li><a href="layanan_bk_laporan.php"><i class="fa fa-chart-bar"></i> Laporan Layanan</a></li>
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
              <li><a href="input_prestasi.php"><i class="fa fa-trophy"></i> Data Prestasi</a></li>
              <li><a href="input_pelanggaran.php"><i class="fa fa-exclamation-triangle"></i> Data Pelanggaran</a></li>
              <li><a href="laporan.php"><i class="fa fa-chart-pie"></i> Laporan Poin</a></li>
              <li><a href="cetak_raport.php"><i class="fa fa-print"></i> Cetak Raport</a></li>
            </ul>
          </li>
          
          <!-- Profil & Akun -->
          <li class="treeview">
            <a href="#">
              <i class="fa fa-user"></i>
              <span>PROFIL & AKUN</span>
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
      <!-- /.sidebar -->
    </aside>
