<?php include 'header.php'; 

// Ambil data siswa
$id_saya = $_SESSION['id'];
$profil = mysqli_query($koneksi, "SELECT s.*, j.jurusan_nama, k.kelas_nama 
                                  FROM siswa s 
                                  LEFT JOIN jurusan j ON s.siswa_jurusan = j.jurusan_id 
                                  LEFT JOIN kelas_siswa ks ON s.siswa_id = ks.ks_siswa 
                                  LEFT JOIN kelas k ON ks.ks_kelas = k.kelas_id 
                                  WHERE s.siswa_id = '$id_saya'");
$profil = mysqli_fetch_assoc($profil);

// Hitung total point prestasi
$prestasi_query = mysqli_query($koneksi, "SELECT COALESCE(SUM(prestasi_point), 0) as total 
                                           FROM input_prestasi ip 
                                           JOIN prestasi p ON ip.prestasi = p.prestasi_id 
                                           WHERE ip.siswa = '$id_saya'");
$prestasi_data = mysqli_fetch_assoc($prestasi_query);
$total_prestasi = $prestasi_data['total'] ?: 0;

// Hitung total point pelanggaran
$pelanggaran_query = mysqli_query($koneksi, "SELECT COALESCE(SUM(pelanggaran_point), 0) as total 
                                             FROM input_pelanggaran ip 
                                             JOIN pelanggaran p ON ip.pelanggaran = p.pelanggaran_id 
                                             WHERE ip.siswa = '$id_saya'");
$pelanggaran_data = mysqli_fetch_assoc($pelanggaran_query);
$total_pelanggaran = $pelanggaran_data['total'] ?: 0;

// Hitung total point (prestasi - pelanggaran)
$total_point = $total_prestasi - $total_pelanggaran;

// Hitung jumlah prestasi
$count_prestasi = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM input_prestasi WHERE siswa = '$id_saya'");
$count_prestasi = mysqli_fetch_assoc($count_prestasi);
$jumlah_prestasi = $count_prestasi['total'] ?: 0;

// Hitung jumlah pelanggaran
$count_pelanggaran = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM input_pelanggaran WHERE siswa = '$id_saya'");
$count_pelanggaran = mysqli_fetch_assoc($count_pelanggaran);
$jumlah_pelanggaran = $count_pelanggaran['total'] ?: 0;

// Ambil prestasi terbaru (5 terakhir)
$prestasi_terbaru = mysqli_query($koneksi, "SELECT ip.*, p.prestasi_nama, p.prestasi_point, ip.waktu 
                                             FROM input_prestasi ip 
                                             JOIN prestasi p ON ip.prestasi = p.prestasi_id 
                                             WHERE ip.siswa = '$id_saya' 
                                             ORDER BY ip.waktu DESC 
                                             LIMIT 5");

// Ambil pelanggaran terbaru (5 terakhir)
$pelanggaran_terbaru = mysqli_query($koneksi, "SELECT ip.*, p.pelanggaran_nama, p.pelanggaran_point, ip.waktu 
                                               FROM input_pelanggaran ip 
                                               JOIN pelanggaran p ON ip.pelanggaran = p.pelanggaran_id 
                                               WHERE ip.siswa = '$id_saya' 
                                               ORDER BY ip.waktu DESC 
                                               LIMIT 5");

// Ambil konseling terbaru (3 terakhir)
$konseling_terbaru = mysqli_query($koneksi, "SELECT k.*, gb.nama_guru_bk 
                                             FROM kasus_siswa k 
                                             LEFT JOIN guru_bk gb ON k.guru_bk_id = gb.guru_bk_id 
                                             WHERE k.siswa_id = '$id_saya' 
                                             ORDER BY k.created_at DESC 
                                             LIMIT 3");

// Hitung jumlah konseling aktif
$konseling_aktif = mysqli_query($koneksi, "SELECT COUNT(*) as total 
                                          FROM kasus_siswa 
                                          WHERE siswa_id = '$id_saya' 
                                          AND status_kasus IN ('Baru', 'Dalam Proses')");
$konseling_aktif = mysqli_fetch_assoc($konseling_aktif);
$jumlah_konseling_aktif = $konseling_aktif['total'] ?: 0;
?>

<div class="content-wrapper">
  <section class="content-header">
    <h1>
      <i class="fa fa-dashboard"></i> Dashboard
      <small>Selamat datang, <?php echo htmlspecialchars($profil['siswa_nama']); ?>!</small>
    </h1>
    <ol class="breadcrumb">
      <li><a href="index.php"><i class="fa fa-dashboard"></i> Home</a></li>
      <li class="active">Dashboard</li>
    </ol>
  </section>

  <section class="content">
    <!-- Info Box Row -->
    <div class="row">
      <!-- Total Point Card -->
      <div class="col-lg-3 col-xs-6">
        <div class="info-box" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 8px; box-shadow: 0 4px 15px rgba(0,0,0,0.1);">
          <span class="info-box-icon" style="background: rgba(255,255,255,0.2); border-radius: 50%; width: 70px; height: 70px; display: flex; align-items: center; justify-content: center;">
            <i class="fa fa-star" style="color: white; font-size: 32px;"></i>
          </span>
          <div class="info-box-content" style="color: white; padding-left: 15px;">
            <span class="info-box-text" style="font-size: 14px; font-weight: 500;">Total Point</span>
            <span class="info-box-number" style="font-size: 32px; font-weight: bold; margin-top: 5px;">
              <?php echo number_format($total_point, 0, ',', '.'); ?>
            </span>
            <div class="progress" style="background: rgba(255,255,255,0.3); height: 6px; margin-top: 8px; border-radius: 3px;">
              <div class="progress-bar" style="background: white; width: <?php echo min(100, max(0, ($total_point + 100) / 2)); ?>%; border-radius: 3px;"></div>
            </div>
            <span class="progress-description" style="font-size: 12px; margin-top: 5px; opacity: 0.9;">
              Prestasi: <?php echo $total_prestasi; ?> | Pelanggaran: <?php echo $total_pelanggaran; ?>
            </span>
          </div>
        </div>
      </div>

      <!-- Prestasi Card -->
      <div class="col-lg-3 col-xs-6">
        <div class="info-box bg-green" style="border-radius: 8px; box-shadow: 0 4px 15px rgba(0,0,0,0.1);">
          <span class="info-box-icon" style="background: rgba(255,255,255,0.2); border-radius: 50%; width: 70px; height: 70px; display: flex; align-items: center; justify-content: center;">
            <i class="fa fa-trophy" style="color: white; font-size: 32px;"></i>
          </span>
          <div class="info-box-content" style="color: white; padding-left: 15px;">
            <span class="info-box-text" style="font-size: 14px; font-weight: 500;">Total Prestasi</span>
            <span class="info-box-number" style="font-size: 32px; font-weight: bold; margin-top: 5px;">
              <?php echo number_format($total_prestasi, 0, ',', '.'); ?>
            </span>
            <span class="info-box-text" style="font-size: 12px; margin-top: 5px; opacity: 0.9;">
              <?php echo $jumlah_prestasi; ?> kali prestasi
            </span>
            <a href="prestasi_saya.php" class="btn btn-sm btn-default" style="margin-top: 8px; background: rgba(255,255,255,0.2); border: none; color: white;">
              <i class="fa fa-eye"></i> Lihat Detail
            </a>
          </div>
        </div>
      </div>

      <!-- Pelanggaran Card -->
      <div class="col-lg-3 col-xs-6">
        <div class="info-box bg-red" style="border-radius: 8px; box-shadow: 0 4px 15px rgba(0,0,0,0.1);">
          <span class="info-box-icon" style="background: rgba(255,255,255,0.2); border-radius: 50%; width: 70px; height: 70px; display: flex; align-items: center; justify-content: center;">
            <i class="fa fa-exclamation-triangle" style="color: white; font-size: 32px;"></i>
          </span>
          <div class="info-box-content" style="color: white; padding-left: 15px;">
            <span class="info-box-text" style="font-size: 14px; font-weight: 500;">Total Pelanggaran</span>
            <span class="info-box-number" style="font-size: 32px; font-weight: bold; margin-top: 5px;">
              <?php echo number_format($total_pelanggaran, 0, ',', '.'); ?>
            </span>
            <span class="info-box-text" style="font-size: 12px; margin-top: 5px; opacity: 0.9;">
              <?php echo $jumlah_pelanggaran; ?> kali pelanggaran
            </span>
            <a href="pelanggaran_saya.php" class="btn btn-sm btn-default" style="margin-top: 8px; background: rgba(255,255,255,0.2); border: none; color: white;">
              <i class="fa fa-eye"></i> Lihat Detail
            </a>
          </div>
        </div>
      </div>

      <!-- Konseling Card -->
      <div class="col-lg-3 col-xs-6">
        <div class="info-box bg-blue" style="border-radius: 8px; box-shadow: 0 4px 15px rgba(0,0,0,0.1);">
          <span class="info-box-icon" style="background: rgba(255,255,255,0.2); border-radius: 50%; width: 70px; height: 70px; display: flex; align-items: center; justify-content: center;">
            <i class="fa fa-heart" style="color: white; font-size: 32px;"></i>
          </span>
          <div class="info-box-content" style="color: white; padding-left: 15px;">
            <span class="info-box-text" style="font-size: 14px; font-weight: 500;">Konseling Aktif</span>
            <span class="info-box-number" style="font-size: 32px; font-weight: bold; margin-top: 5px;">
              <?php echo $jumlah_konseling_aktif; ?>
            </span>
            <span class="info-box-text" style="font-size: 12px; margin-top: 5px; opacity: 0.9;">
              Kasus dalam proses
            </span>
            <a href="konseling_saya.php" class="btn btn-sm btn-default" style="margin-top: 8px; background: rgba(255,255,255,0.2); border: none; color: white;">
              <i class="fa fa-eye"></i> Lihat Detail
            </a>
          </div>
        </div>
      </div>
    </div>

    <!-- Main Content Row -->
    <div class="row">
      <!-- Profil Card -->
      <div class="col-md-4">
        <div class="box box-primary" style="border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
          <div class="box-header with-border" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 8px 8px 0 0;">
            <h3 class="box-title" style="color: white; font-weight: 600;">
              <i class="fa fa-user"></i> Profil Saya
            </h3>
          </div>
          <div class="box-body box-profile" style="padding: 20px;">
            <div class="text-center">
              <?php if(!empty($profil['siswa_foto'])): ?>
                <img class="profile-user-img img-responsive img-circle" 
                     src="../gambar/user/<?php echo htmlspecialchars($profil['siswa_foto']); ?>" 
                     alt="Foto Profil" style="width: 100px; height: 100px; object-fit: cover; border: 4px solid #667eea;">
              <?php else: ?>
                <img class="profile-user-img img-responsive img-circle" 
                     src="../gambar/sistem/user.png" 
                     alt="Foto Profil" style="width: 100px; height: 100px; object-fit: cover; border: 4px solid #667eea;">
              <?php endif; ?>
            </div>

            <h3 class="profile-username text-center" style="margin-top: 15px; font-weight: 600;">
              <?php echo htmlspecialchars($profil['siswa_nama']); ?>
            </h3>

            <p class="text-muted text-center" style="margin-bottom: 20px;">
              <i class="fa fa-id-card"></i> NIS: <?php echo htmlspecialchars($profil['siswa_nis']); ?>
            </p>

            <ul class="list-group list-group-unbordered" style="margin-bottom: 15px;">
              <li class="list-group-item" style="border: none; padding: 10px 0; border-bottom: 1px solid #f4f4f4;">
                <b><i class="fa fa-graduation-cap text-purple"></i> Jurusan</b>
                <a class="pull-right"><?php echo htmlspecialchars($profil['jurusan_nama'] ?: '-'); ?></a>
              </li>
              <li class="list-group-item" style="border: none; padding: 10px 0; border-bottom: 1px solid #f4f4f4;">
                <b><i class="fa fa-users text-blue"></i> Kelas</b>
                <a class="pull-right"><?php echo htmlspecialchars($profil['kelas_nama'] ?: '-'); ?></a>
              </li>
              <li class="list-group-item" style="border: none; padding: 10px 0;">
                <b><i class="fa fa-check-circle text-green"></i> Status</b>
                <span class="label label-success pull-right"><?php echo htmlspecialchars($profil['siswa_status'] ?: 'Aktif'); ?></span>
              </li>
            </ul>

            <a href="profil.php" class="btn btn-primary btn-block" style="border-radius: 5px; font-weight: 600;">
              <i class="fa fa-edit"></i> Edit Profil
            </a>
          </div>
        </div>
      </div>

      <!-- Prestasi Terbaru -->
      <div class="col-md-4">
        <div class="box box-success" style="border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
          <div class="box-header with-border" style="background: #00a65a; border-radius: 8px 8px 0 0;">
            <h3 class="box-title" style="color: white; font-weight: 600;">
              <i class="fa fa-trophy"></i> Prestasi Terbaru
            </h3>
            <div class="box-tools pull-right">
              <a href="prestasi_saya.php" class="btn btn-sm btn-default" style="background: rgba(255,255,255,0.2); border: none; color: white;">
                <i class="fa fa-list"></i> Semua
              </a>
            </div>
          </div>
          <div class="box-body" style="max-height: 400px; overflow-y: auto; padding: 15px;">
            <?php if(mysqli_num_rows($prestasi_terbaru) > 0): ?>
              <ul class="products-list product-list-in-box">
                <?php while($p = mysqli_fetch_assoc($prestasi_terbaru)): ?>
                <li class="item" style="padding: 10px 0; border-bottom: 1px solid #f4f4f4;">
                  <div class="product-img" style="background: linear-gradient(135deg, #00a65a 0%, #00c853 100%); width: 50px; height: 50px; border-radius: 8px; display: flex; align-items: center; justify-content: center; margin-right: 10px;">
                    <i class="fa fa-trophy" style="color: white; font-size: 24px;"></i>
                  </div>
                  <div class="product-info">
                    <a href="prestasi_saya.php" class="product-title" style="font-weight: 600; color: #333;">
                      <?php echo htmlspecialchars($p['prestasi_nama']); ?>
                      <span class="label label-success pull-right" style="font-size: 11px;">
                        +<?php echo $p['prestasi_point']; ?> pt
                      </span>
                    </a>
                    <span class="product-description" style="font-size: 12px; color: #999;">
                      <i class="fa fa-calendar"></i> <?php echo date('d/m/Y', strtotime($p['waktu'])); ?>
                    </span>
                  </div>
                </li>
                <?php endwhile; ?>
              </ul>
            <?php else: ?>
              <div class="text-center" style="padding: 30px;">
                <i class="fa fa-trophy" style="font-size: 48px; color: #ddd;"></i>
                <p class="text-muted" style="margin-top: 10px;">Belum ada prestasi</p>
                <a href="prestasi.php" class="btn btn-sm btn-success">
                  <i class="fa fa-eye"></i> Lihat Data Prestasi
                </a>
              </div>
            <?php endif; ?>
          </div>
        </div>
      </div>

      <!-- Pelanggaran Terbaru -->
      <div class="col-md-4">
        <div class="box box-danger" style="border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
          <div class="box-header with-border" style="background: #dd4b39; border-radius: 8px 8px 0 0;">
            <h3 class="box-title" style="color: white; font-weight: 600;">
              <i class="fa fa-exclamation-triangle"></i> Pelanggaran Terbaru
            </h3>
            <div class="box-tools pull-right">
              <a href="pelanggaran_saya.php" class="btn btn-sm btn-default" style="background: rgba(255,255,255,0.2); border: none; color: white;">
                <i class="fa fa-list"></i> Semua
              </a>
            </div>
          </div>
          <div class="box-body" style="max-height: 400px; overflow-y: auto; padding: 15px;">
            <?php if(mysqli_num_rows($pelanggaran_terbaru) > 0): ?>
              <ul class="products-list product-list-in-box">
                <?php while($p = mysqli_fetch_assoc($pelanggaran_terbaru)): ?>
                <li class="item" style="padding: 10px 0; border-bottom: 1px solid #f4f4f4;">
                  <div class="product-img" style="background: linear-gradient(135deg, #dd4b39 0%, #c62828 100%); width: 50px; height: 50px; border-radius: 8px; display: flex; align-items: center; justify-content: center; margin-right: 10px;">
                    <i class="fa fa-exclamation-triangle" style="color: white; font-size: 24px;"></i>
                  </div>
                  <div class="product-info">
                    <a href="pelanggaran_saya.php" class="product-title" style="font-weight: 600; color: #333;">
                      <?php echo htmlspecialchars($p['pelanggaran_nama']); ?>
                      <span class="label label-danger pull-right" style="font-size: 11px;">
                        -<?php echo $p['pelanggaran_point']; ?> pt
                      </span>
                    </a>
                    <span class="product-description" style="font-size: 12px; color: #999;">
                      <i class="fa fa-calendar"></i> <?php echo date('d/m/Y', strtotime($p['waktu'])); ?>
                    </span>
                  </div>
                </li>
                <?php endwhile; ?>
              </ul>
            <?php else: ?>
              <div class="text-center" style="padding: 30px;">
                <i class="fa fa-check-circle" style="font-size: 48px; color: #5cb85c;"></i>
                <p class="text-success" style="margin-top: 10px; font-weight: 600;">Tidak ada pelanggaran</p>
                <p class="text-muted" style="font-size: 12px;">Pertahankan prestasi Anda!</p>
              </div>
            <?php endif; ?>
          </div>
        </div>
      </div>
    </div>

    <!-- Konseling Terbaru Row -->
    <?php if(mysqli_num_rows($konseling_terbaru) > 0): ?>
    <div class="row">
      <div class="col-md-12">
        <div class="box box-info" style="border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
          <div class="box-header with-border" style="background: #00c0ef; border-radius: 8px 8px 0 0;">
            <h3 class="box-title" style="color: white; font-weight: 600;">
              <i class="fa fa-heart"></i> Konseling Terbaru
            </h3>
            <div class="box-tools pull-right">
              <a href="konseling_saya.php" class="btn btn-sm btn-default" style="background: rgba(255,255,255,0.2); border: none; color: white;">
                <i class="fa fa-list"></i> Semua Konseling
              </a>
            </div>
          </div>
          <div class="box-body">
            <div class="table-responsive">
              <table class="table table-hover">
                <thead>
                  <tr>
                    <th width="5%">#</th>
                    <th width="15%">Kode Kasus</th>
                    <th width="30%">Judul</th>
                    <th width="15%">Kategori</th>
                    <th width="15%">Status</th>
                    <th width="15%">Guru BK</th>
                    <th width="5%">Aksi</th>
                  </tr>
                </thead>
                <tbody>
                  <?php 
                  $no = 1;
                  mysqli_data_seek($konseling_terbaru, 0);
                  while($k = mysqli_fetch_assoc($konseling_terbaru)): 
                    $status_class = '';
                    switch($k['status_kasus']) {
                      case 'Baru': $status_class = 'label-warning'; break;
                      case 'Dalam Proses': $status_class = 'label-info'; break;
                      case 'Selesai/Tuntas': $status_class = 'label-success'; break;
                      case 'Dirujuk/Alih Tangan Kasus': $status_class = 'label-danger'; break;
                      default: $status_class = 'label-default';
                    }
                  ?>
                  <tr>
                    <td><?php echo $no++; ?></td>
                    <td><strong><?php echo htmlspecialchars($k['kasus_kode']); ?></strong></td>
                    <td><?php echo htmlspecialchars($k['judul_kasus']); ?></td>
                    <td><?php echo htmlspecialchars($k['kategori_masalah']); ?></td>
                    <td><span class="label <?php echo $status_class; ?>"><?php echo htmlspecialchars($k['status_kasus']); ?></span></td>
                    <td><?php echo htmlspecialchars($k['nama_guru_bk'] ?: '-'); ?></td>
                    <td>
                      <a href="konseling_detail.php?id=<?php echo $k['kasus_id']; ?>" class="btn btn-sm btn-info">
                        <i class="fa fa-eye"></i>
                      </a>
                    </td>
                  </tr>
                  <?php endwhile; ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
    <?php endif; ?>

    <!-- Quick Actions Row -->
    <div class="row">
      <div class="col-md-12">
        <div class="box box-default" style="border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
          <div class="box-header with-border">
            <h3 class="box-title" style="font-weight: 600;">
              <i class="fa fa-bolt"></i> Aksi Cepat
            </h3>
          </div>
          <div class="box-body">
            <div class="row">
              <div class="col-md-3 col-sm-6">
                <a href="konseling_ajukan.php" class="btn btn-block btn-primary btn-lg" style="border-radius: 8px; padding: 20px; margin-bottom: 15px;">
                  <i class="fa fa-plus-circle fa-2x"></i><br>
                  <strong>Ajukan Konseling</strong>
                </a>
              </div>
              <div class="col-md-3 col-sm-6">
                <a href="prestasi_saya.php" class="btn btn-block btn-success btn-lg" style="border-radius: 8px; padding: 20px; margin-bottom: 15px;">
                  <i class="fa fa-trophy fa-2x"></i><br>
                  <strong>Lihat Prestasi</strong>
                </a>
              </div>
              <div class="col-md-3 col-sm-6">
                <a href="pelanggaran_saya.php" class="btn btn-block btn-danger btn-lg" style="border-radius: 8px; padding: 20px; margin-bottom: 15px;">
                  <i class="fa fa-exclamation-triangle fa-2x"></i><br>
                  <strong>Lihat Pelanggaran</strong>
                </a>
              </div>
              <div class="col-md-3 col-sm-6">
                <a href="profil.php" class="btn btn-block btn-warning btn-lg" style="border-radius: 8px; padding: 20px; margin-bottom: 15px;">
                  <i class="fa fa-user fa-2x"></i><br>
                  <strong>Edit Profil</strong>
                </a>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

  </section>
</div>

<?php include 'footer.php'; ?>
