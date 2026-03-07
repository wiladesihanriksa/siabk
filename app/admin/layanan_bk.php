<?php include 'header_dynamic.php'; ?>

<div class="content-wrapper">
  <section class="content-header">
    <h1>
      Data Layanan BK
      <small>Pencatatan Layanan Bimbingan dan Konseling</small>
    </h1>
    <ol class="breadcrumb">
      <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
      <li><a href="#">Konseling BK</a></li>
      <li class="active">Data Layanan BK</li>
    </ol>
  </section>

  <section class="content">
    <div class="row">
      <div class="col-xs-12">
        <div class="box">
          <div class="box-header">
            <h3 class="box-title">Daftar Layanan BK</h3>
            <div class="box-tools pull-right">
              <a href="layanan_bk_tambah.php" class="btn btn-primary btn-sm">
                <i class="fa fa-plus"></i> Tambah Layanan Baru
              </a>
            </div>
          </div>
          
          <!-- Filter Section -->
          <div class="box-body">
            <form method="GET" action="">
              <div class="row">
                <div class="col-md-3">
                  <div class="form-group">
                    <label>Filter Tanggal:</label>
                    <input type="date" name="tanggal_awal" class="form-control" 
                           value="<?php echo isset($_GET['tanggal_awal']) ? $_GET['tanggal_awal'] : ''; ?>">
                  </div>
                </div>
                <div class="col-md-3">
                  <div class="form-group">
                    <label>&nbsp;</label>
                    <input type="date" name="tanggal_akhir" class="form-control" 
                           value="<?php echo isset($_GET['tanggal_akhir']) ? $_GET['tanggal_akhir'] : ''; ?>">
                  </div>
                </div>
                <div class="col-md-2">
                  <div class="form-group">
                    <label>Jenis Layanan:</label>
                    <select name="jenis_layanan" class="form-control">
                      <option value="">Semua Jenis</option>
                      <option value="Layanan Klasikal" <?php echo (isset($_GET['jenis_layanan']) && $_GET['jenis_layanan'] == 'Layanan Klasikal') ? 'selected' : ''; ?>>Layanan Klasikal</option>
                      <option value="Bimbingan Kelompok" <?php echo (isset($_GET['jenis_layanan']) && $_GET['jenis_layanan'] == 'Bimbingan Kelompok') ? 'selected' : ''; ?>>Bimbingan Kelompok</option>
                      <option value="Konseling Kelompok" <?php echo (isset($_GET['jenis_layanan']) && $_GET['jenis_layanan'] == 'Konseling Kelompok') ? 'selected' : ''; ?>>Konseling Kelompok</option>
                      <option value="Konsultasi" <?php echo (isset($_GET['jenis_layanan']) && $_GET['jenis_layanan'] == 'Konsultasi') ? 'selected' : ''; ?>>Konsultasi</option>
                      <option value="Mediasi" <?php echo (isset($_GET['jenis_layanan']) && $_GET['jenis_layanan'] == 'Mediasi') ? 'selected' : ''; ?>>Mediasi</option>
                      <option value="Layanan Advokasi" <?php echo (isset($_GET['jenis_layanan']) && $_GET['jenis_layanan'] == 'Layanan Advokasi') ? 'selected' : ''; ?>>Layanan Advokasi</option>
                      <option value="Layanan Peminatan" <?php echo (isset($_GET['jenis_layanan']) && $_GET['jenis_layanan'] == 'Layanan Peminatan') ? 'selected' : ''; ?>>Layanan Peminatan</option>
                      <option value="Lainnya" <?php echo (isset($_GET['jenis_layanan']) && $_GET['jenis_layanan'] == 'Lainnya') ? 'selected' : ''; ?>>Lainnya</option>
                    </select>
                  </div>
                </div>
                <div class="col-md-2">
                  <div class="form-group">
                    <label>Bidang Layanan:</label>
                    <select name="bidang_layanan" class="form-control">
                      <option value="">Semua Bidang</option>
                      <option value="Pribadi" <?php echo (isset($_GET['bidang_layanan']) && $_GET['bidang_layanan'] == 'Pribadi') ? 'selected' : ''; ?>>Pribadi</option>
                      <option value="Sosial" <?php echo (isset($_GET['bidang_layanan']) && $_GET['bidang_layanan'] == 'Sosial') ? 'selected' : ''; ?>>Sosial</option>
                      <option value="Belajar" <?php echo (isset($_GET['bidang_layanan']) && $_GET['bidang_layanan'] == 'Belajar') ? 'selected' : ''; ?>>Belajar</option>
                      <option value="Karir" <?php echo (isset($_GET['bidang_layanan']) && $_GET['bidang_layanan'] == 'Karir') ? 'selected' : ''; ?>>Karir</option>
                    </select>
                  </div>
                </div>
                <div class="col-md-2">
                  <div class="form-group">
                    <label>&nbsp;</label>
                    <button type="submit" class="btn btn-info btn-block">
                      <i class="fa fa-search"></i> Filter
                    </button>
                  </div>
                </div>
              </div>
            </form>
          </div>

          <div class="box-body">
            <div class="table-responsive">
              <table id="example1" class="table table-bordered table-striped">
                <thead>
                  <tr>
                    <th>No</th>
                    <th>Tanggal</th>
                    <th>Jenis Layanan</th>
                    <th>Topik/Materi</th>
                    <th>Bidang</th>
                    <th>Sasaran</th>
                    <th>Jumlah Peserta</th>
                    <th>Dibuat Oleh</th>
                    <th>Aksi</th>
                  </tr>
                </thead>
                <tbody>
                  <?php
                  // Build query dengan filter
                  $where_conditions = array();
                  $params = array();
                  
                  if(isset($_GET['tanggal_awal']) && !empty($_GET['tanggal_awal'])) {
                      $where_conditions[] = "l.tanggal_pelaksanaan >= ?";
                      $params[] = $_GET['tanggal_awal'];
                  }
                  
                  if(isset($_GET['tanggal_akhir']) && !empty($_GET['tanggal_akhir'])) {
                      $where_conditions[] = "l.tanggal_pelaksanaan <= ?";
                      $params[] = $_GET['tanggal_akhir'];
                  }
                  
                  if(isset($_GET['jenis_layanan']) && !empty($_GET['jenis_layanan'])) {
                      $where_conditions[] = "l.jenis_layanan = ?";
                      $params[] = $_GET['jenis_layanan'];
                  }
                  
                  if(isset($_GET['bidang_layanan']) && !empty($_GET['bidang_layanan'])) {
                      $where_conditions[] = "l.bidang_layanan = ?";
                      $params[] = $_GET['bidang_layanan'];
                  }
                  
                  $where_clause = !empty($where_conditions) ? "WHERE " . implode(" AND ", $where_conditions) : "";
                  
                  $query = "SELECT l.*, k.kelas_nama, u.user_nama, u2.user_nama as dibuat_oleh_nama, u2.user_level as dibuat_oleh_level
                           FROM layanan_bk l 
                           LEFT JOIN kelas k ON l.kelas_id = k.kelas_id 
                           LEFT JOIN user u ON l.created_by = u.user_id 
                           LEFT JOIN user u2 ON l.dibuat_oleh = u2.user_id 
                           $where_clause 
                           ORDER BY l.tanggal_pelaksanaan DESC, l.created_at DESC";
                  
                  if(!empty($params)) {
                      $stmt = mysqli_prepare($koneksi, $query);
                      if($stmt) {
                          $types = str_repeat('s', count($params));
                          mysqli_stmt_bind_param($stmt, $types, ...$params);
                          mysqli_stmt_execute($stmt);
                          $result = mysqli_stmt_get_result($stmt);
                      }
                  } else {
                      $result = mysqli_query($koneksi, $query);
                  }
                  
                  $no = 1;
                  while($data = mysqli_fetch_assoc($result)) {
                  ?>
                  <tr>
                    <td><?php echo $no++; ?></td>
                    <td><?php echo date('d/m/Y', strtotime($data['tanggal_pelaksanaan'])); ?></td>
                    <td>
                      <span class="label label-info"><?php echo $data['jenis_layanan']; ?></span>
                    </td>
                    <td>
                      <strong><?php echo $data['topik_materi']; ?></strong>
                    </td>
                    <td>
                      <?php
                      $badge_color = '';
                      switch($data['bidang_layanan']) {
                          case 'Pribadi': $badge_color = 'label-danger'; break;
                          case 'Sosial': $badge_color = 'label-success'; break;
                          case 'Belajar': $badge_color = 'label-warning'; break;
                          case 'Karir': $badge_color = 'label-primary'; break;
                          default: $badge_color = 'label-default';
                      }
                      ?>
                      <span class="label <?php echo $badge_color; ?>"><?php echo $data['bidang_layanan']; ?></span>
                    </td>
                    <td>
                      <?php echo $data['sasaran_layanan']; ?>
                      <?php if($data['kelas_nama'] && $data['sasaran_layanan'] != 'Individu'): ?>
                        <br><small class="text-muted">(<?php echo $data['kelas_nama']; ?>)</small>
                      <?php endif; ?>
                    </td>
                    <td>
                      <span class="badge bg-blue"><?php echo $data['jumlah_peserta']; ?> orang</span>
                    </td>
                    <td>
                      <?php if($data['dibuat_oleh_nama']): ?>
                        <strong><?php echo $data['dibuat_oleh_nama']; ?></strong>
                      <?php else: ?>
                        <span class="text-muted">Tidak diketahui</span>
                      <?php endif; ?>
                    </td>
                    <td>
                      <a href="layanan_bk_detail.php?id=<?php echo $data['layanan_id']; ?>" 
                         class="btn btn-info btn-xs" title="Lihat Detail">
                        <i class="fa fa-eye"></i>
                      </a>
                      <a href="layanan_bk_edit.php?id=<?php echo $data['layanan_id']; ?>" 
                         class="btn btn-warning btn-xs" title="Edit">
                        <i class="fa fa-edit"></i>
                      </a>
                      <a href="layanan_bk_hapus.php?id=<?php echo $data['layanan_id']; ?>" 
                         class="btn btn-danger btn-xs" title="Hapus" 
                         onclick="return confirm('Yakin ingin menghapus layanan ini?')">
                        <i class="fa fa-trash"></i>
                      </a>
                    </td>
                  </tr>
                  <?php } ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Statistik Cards -->
    <div class="row">
      <div class="col-lg-3 col-xs-6">
        <div class="small-box bg-aqua">
          <div class="inner">
            <?php 
            $total_layanan = mysqli_query($koneksi,"SELECT COUNT(*) as total FROM layanan_bk");
            $total_layanan = mysqli_fetch_assoc($total_layanan);
            ?>
            <h3><?php echo $total_layanan['total']; ?></h3>
            <p>Total Layanan BK</p>
          </div>
          <div class="icon">
            <i class="fa fa-heart"></i>
          </div>
        </div>
      </div>

      <div class="col-lg-3 col-xs-6">
        <div class="small-box bg-green">
          <div class="inner">
            <?php 
            $layanan_bulan_ini = mysqli_query($koneksi,"SELECT COUNT(*) as total FROM layanan_bk WHERE MONTH(tanggal_pelaksanaan) = MONTH(CURDATE()) AND YEAR(tanggal_pelaksanaan) = YEAR(CURDATE())");
            $layanan_bulan_ini = mysqli_fetch_assoc($layanan_bulan_ini);
            ?>
            <h3><?php echo $layanan_bulan_ini['total']; ?></h3>
            <p>Layanan Bulan Ini</p>
          </div>
          <div class="icon">
            <i class="fa fa-calendar"></i>
          </div>
        </div>
      </div>

      <div class="col-lg-3 col-xs-6">
        <div class="small-box bg-yellow">
          <div class="inner">
            <?php 
            $total_peserta = mysqli_query($koneksi,"SELECT SUM(jumlah_peserta) as total FROM layanan_bk");
            $total_peserta = mysqli_fetch_assoc($total_peserta);
            ?>
            <h3><?php echo $total_peserta['total'] ? $total_peserta['total'] : 0; ?></h3>
            <p>Total Peserta</p>
          </div>
          <div class="icon">
            <i class="fa fa-users"></i>
          </div>
        </div>
      </div>

      <div class="col-lg-3 col-xs-6">
        <div class="small-box bg-red">
          <div class="inner">
            <a href="layanan_bk_laporan.php" style="color: white;">
              <h3><i class="fa fa-file-text"></i></h3>
              <p>Laporan Layanan</p>
            </a>
          </div>
          <div class="icon">
            <i class="fa fa-chart-line"></i>
          </div>
        </div>
      </div>
    </div>
  </section>
</div>

<?php include 'footer.php'; ?>
