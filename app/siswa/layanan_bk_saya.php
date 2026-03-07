<?php include 'header.php'; ?>

<div class="content-wrapper">
  <section class="content-header">
    <h1>
      Layanan BK Saya
      <small>Riwayat Layanan Bimbingan dan Konseling yang Saya Ikuti</small>
    </h1>
    <ol class="breadcrumb">
      <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
      <li><a href="#">Layanan BK</a></li>
      <li class="active">Layanan BK Saya</li>
    </ol>
  </section>

  <section class="content">
    <div class="row">
      <div class="col-xs-12">
        <div class="box">
          <div class="box-header">
            <h3 class="box-title">Daftar Layanan BK yang Saya Ikuti</h3>
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
                    <th>Kelas</th>
                    <th>Jumlah Peserta</th>
                    <th>Aksi</th>
                  </tr>
                </thead>
                <tbody>
                  <?php
                  $siswa_id = $_SESSION['id'];
                  
                  // Query untuk mendapatkan layanan BK yang diikuti oleh siswa
                  $query = "SELECT l.*, k.kelas_nama, u.user_nama 
                           FROM layanan_bk l 
                           LEFT JOIN kelas k ON l.kelas_id = k.kelas_id 
                           LEFT JOIN user u ON l.created_by = u.user_id 
                           LEFT JOIN layanan_bk_peserta lp ON l.layanan_id = lp.layanan_id 
                           WHERE lp.siswa_id = ? 
                           ORDER BY l.tanggal_pelaksanaan DESC";
                  
                  $stmt = mysqli_prepare($koneksi, $query);
                  mysqli_stmt_bind_param($stmt, 'i', $siswa_id);
                  mysqli_stmt_execute($stmt);
                  $result = mysqli_stmt_get_result($stmt);
                  
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
                    </td>
                    <td>
                      <?php echo $data['kelas_nama'] ? $data['kelas_nama'] : '-'; ?>
                    </td>
                    <td>
                      <span class="badge bg-blue"><?php echo $data['jumlah_peserta']; ?> orang</span>
                    </td>
                    <td>
                      <a href="layanan_bk_detail.php?id=<?php echo $data['layanan_id']; ?>" 
                         class="btn btn-info btn-xs" title="Lihat Detail">
                        <i class="fa fa-eye"></i>
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
      <div class="col-lg-4 col-xs-6">
        <div class="small-box bg-aqua">
          <div class="inner">
            <?php 
            $total_layanan_saya = mysqli_query($koneksi,"SELECT COUNT(*) as total FROM layanan_bk l LEFT JOIN layanan_bk_peserta lp ON l.layanan_id = lp.layanan_id WHERE lp.siswa_id = '$siswa_id'");
            $total_layanan_saya = mysqli_fetch_assoc($total_layanan_saya);
            ?>
            <h3><?php echo $total_layanan_saya['total']; ?></h3>
            <p>Total Layanan yang Saya Ikuti</p>
          </div>
          <div class="icon">
            <i class="fa fa-heart"></i>
          </div>
        </div>
      </div>

      <div class="col-lg-4 col-xs-6">
        <div class="small-box bg-green">
          <div class="inner">
            <?php 
            $layanan_bulan_ini = mysqli_query($koneksi,"SELECT COUNT(*) as total FROM layanan_bk l LEFT JOIN layanan_bk_peserta lp ON l.layanan_id = lp.layanan_id WHERE lp.siswa_id = '$siswa_id' AND MONTH(l.tanggal_pelaksanaan) = MONTH(CURDATE()) AND YEAR(l.tanggal_pelaksanaan) = YEAR(CURDATE())");
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

      <div class="col-lg-4 col-xs-6">
        <div class="small-box bg-yellow">
          <div class="inner">
            <?php 
            $bidang_berbeda = mysqli_query($koneksi,"SELECT COUNT(DISTINCT l.bidang_layanan) as total FROM layanan_bk l LEFT JOIN layanan_bk_peserta lp ON l.layanan_id = lp.layanan_id WHERE lp.siswa_id = '$siswa_id'");
            $bidang_berbeda = mysqli_fetch_assoc($bidang_berbeda);
            ?>
            <h3><?php echo $bidang_berbeda['total']; ?></h3>
            <p>Bidang Layanan Berbeda</p>
          </div>
          <div class="icon">
            <i class="fa fa-tags"></i>
          </div>
        </div>
      </div>
    </div>

    <!-- Informasi Tambahan -->
    <div class="row">
      <div class="col-md-12">
        <div class="box box-info">
          <div class="box-header with-border">
            <h3 class="box-title">
              <i class="fa fa-info-circle"></i> Informasi
            </h3>
          </div>
          
          <div class="box-body">
            <div class="alert alert-info">
              <h4><i class="fa fa-info"></i> Tentang Layanan BK</h4>
              <p>Halaman ini menampilkan riwayat layanan Bimbingan dan Konseling yang telah Anda ikuti. Data ini mencakup:</p>
              <ul>
                <li><strong>Layanan Klasikal:</strong> Layanan yang diberikan kepada seluruh kelas</li>
                <li><strong>Bimbingan Kelompok:</strong> Layanan yang diberikan kepada kelompok siswa</li>
                <li><strong>Konseling Kelompok:</strong> Konseling yang dilakukan dalam kelompok</li>
                <li><strong>Konsultasi:</strong> Konsultasi individual atau kelompok</li>
                <li><strong>Mediasi:</strong> Mediasi untuk menyelesaikan konflik</li>
                <li><strong>Layanan Advokasi:</strong> Advokasi untuk kepentingan siswa</li>
                <li><strong>Layanan Peminatan:</strong> Bimbingan pemilihan jurusan/karir</li>
              </ul>
              <p>Jika Anda memiliki pertanyaan tentang layanan BK yang Anda ikuti, silakan hubungi guru BK.</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
</div>

<?php include 'footer.php'; ?>
