<?php 
include 'header_guru_bk.php';
?>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <section class="content-header">
    <h1>
      <i class="fa fa-user-md"></i> Dashboard Guru BK
      <small>Selamat datang, <?php echo $_SESSION['nama']; ?></small>
    </h1>
    <ol class="breadcrumb">
      <li><a href="index.php"><i class="fa fa-dashboard"></i> Home</a></li>
      <li class="active">Dashboard Guru BK</li>
    </ol>
  </section>

  <!-- Main content -->
  <section class="content">
    <?php 
    // Tampilkan pesan error jika ada
    if(isset($_SESSION['error_message'])) {
        echo '<div class="alert alert-warning alert-dismissible">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                <h4><i class="icon fa fa-warning"></i> Perhatian!</h4>
                ' . $_SESSION['error_message'] . '
              </div>';
        unset($_SESSION['error_message']);
    }
    ?>
    
    <!-- Info boxes -->
    <div class="row">
      <div class="col-lg-3 col-xs-6">
        <div class="small-box bg-aqua">
          <div class="inner">
            <h3><?php
            // Hitung total kasus yang ditangani guru BK ini
            $user_id = $_SESSION['id'];
            $query_kasus = "SELECT COUNT(*) as total FROM kasus_siswa 
                           WHERE guru_bk_id = '$user_id'";
            $result_kasus = mysqli_query($koneksi, $query_kasus);
            $total_kasus = mysqli_fetch_assoc($result_kasus)['total'];
            echo $total_kasus;
            ?></h3>
            <p>Total Kasus Saya</p>
          </div>
          <div class="icon">
            <i class="fa fa-folder"></i>
          </div>
        </div>
      </div>
      
      <div class="col-lg-3 col-xs-6">
        <div class="small-box bg-green">
          <div class="inner">
            <h3><?php
            $query_selesai = "SELECT COUNT(*) as total FROM kasus_siswa 
                             WHERE guru_bk_id = '$user_id' AND status_kasus = 'Selesai/Tuntas'";
            $result_selesai = mysqli_query($koneksi, $query_selesai);
            $kasus_selesai = mysqli_fetch_assoc($result_selesai)['total'];
            echo $kasus_selesai;
            ?></h3>
            <p>Kasus Selesai</p>
          </div>
          <div class="icon">
            <i class="fa fa-check"></i>
          </div>
        </div>
      </div>
      
      <div class="col-lg-3 col-xs-6">
        <div class="small-box bg-yellow">
          <div class="inner">
            <h3><?php
            $query_proses = "SELECT COUNT(*) as total FROM kasus_siswa 
                            WHERE guru_bk_id = '$user_id' AND status_kasus = 'Dalam Proses'";
            $result_proses = mysqli_query($koneksi, $query_proses);
            $kasus_proses = mysqli_fetch_assoc($result_proses)['total'];
            echo $kasus_proses;
            ?></h3>
            <p>Dalam Proses</p>
          </div>
          <div class="icon">
            <i class="fa fa-clock-o"></i>
          </div>
        </div>
      </div>
      
      <div class="col-lg-3 col-xs-6">
        <div class="small-box bg-red">
          <div class="inner">
            <h3><?php
            $query_baru = "SELECT COUNT(*) as total FROM kasus_siswa 
                          WHERE guru_bk_id = '$user_id' AND status_kasus = 'Baru'";
            $result_baru = mysqli_query($koneksi, $query_baru);
            $kasus_baru = mysqli_fetch_assoc($result_baru)['total'];
            echo $kasus_baru;
            ?></h3>
            <p>Kasus Baru</p>
          </div>
          <div class="icon">
            <i class="fa fa-exclamation"></i>
          </div>
        </div>
      </div>
    </div>

    <!-- Widget Tambahan untuk Guru BK -->
    <div class="row">
      <!-- Kunjungan Rumah -->
      <div class="col-lg-3 col-xs-6">
        <div class="small-box bg-blue">
          <div class="inner">
            <h3><?php
            $query_kunjungan = "SELECT COUNT(*) as total FROM kunjungan_rumah 
                               WHERE petugas_bk_id = '$user_id'";
            $result_kunjungan = mysqli_query($koneksi, $query_kunjungan);
            $total_kunjungan = mysqli_fetch_assoc($result_kunjungan)['total'];
            echo $total_kunjungan;
            ?></h3>
            <p>Kunjungan Rumah Saya</p>
          </div>
          <div class="icon">
            <i class="fa fa-home"></i>
          </div>
          <!--<a href="kunjungan_rumah.php" class="small-box-footer">
            Lihat Detail <i class="fa fa-arrow-circle-right"></i>
          </a>-->
        </div>
      </div>

      <!-- Layanan BK -->
      <div class="col-lg-3 col-xs-6">
        <div class="small-box bg-purple">
          <div class="inner">
            <h3><?php
            $query_layanan = "SELECT COUNT(*) as total FROM layanan_bk 
                             WHERE created_by = '$user_id'";
            $result_layanan = mysqli_query($koneksi, $query_layanan);
            $total_layanan = mysqli_fetch_assoc($result_layanan)['total'];
            echo $total_layanan;
            ?></h3>
            <p>Layanan BK Saya</p>
          </div>
          <div class="icon">
            <i class="fa fa-graduation-cap"></i>
          </div>
          <!--<a href="layanan_bk.php" class="small-box-footer">
            Lihat Detail <i class="fa fa-arrow-circle-right"></i>
          </a>-->
        </div>
      </div>

      <!-- Prestasi -->
      <div class="col-lg-3 col-xs-6">
        <div class="small-box bg-green">
          <div class="inner">
            <h3><?php
            $query_prestasi = "SELECT COUNT(*) as total FROM input_prestasi";
            $result_prestasi = mysqli_query($koneksi, $query_prestasi);
            $total_prestasi = mysqli_fetch_assoc($result_prestasi)['total'];
            echo $total_prestasi;
            ?></h3>
            <p>Total Prestasi</p>
          </div>
          <div class="icon">
            <i class="fa fa-trophy"></i>
          </div>
          <!--<a href="input_prestasi.php" class="small-box-footer">
            Lihat Detail <i class="fa fa-arrow-circle-right"></i>
          </a>-->
        </div>
      </div>

      <!-- Pelanggaran -->
      <div class="col-lg-3 col-xs-6">
        <div class="small-box bg-red">
          <div class="inner">
            <h3><?php
            $query_pelanggaran = "SELECT COUNT(*) as total FROM input_pelanggaran";
            $result_pelanggaran = mysqli_query($koneksi, $query_pelanggaran);
            $total_pelanggaran = mysqli_fetch_assoc($result_pelanggaran)['total'];
            echo $total_pelanggaran;
            ?></h3>
            <p>Total Pelanggaran</p>
          </div>
          <div class="icon">
            <i class="fa fa-exclamation-triangle"></i>
          </div>
          <!--<a href="input_pelanggaran.php" class="small-box-footer">
            Lihat Detail <i class="fa fa-arrow-circle-right"></i>
          </a>-->
        </div>
      </div>
    </div>

    <!-- Widget Jurnal Kasus -->
    <div class="row">
      <div class="col-lg-3 col-xs-6">
        <div class="small-box bg-maroon">
          <div class="inner">
            <h3><?php
            $query_jurnal = "SELECT COUNT(*) as total FROM jurnal_kasus jk 
                            JOIN kasus_siswa ks ON jk.kasus_id = ks.kasus_id 
                            WHERE ks.guru_bk_id = '$user_id'";
            $result_jurnal = mysqli_query($koneksi, $query_jurnal);
            $total_jurnal = mysqli_fetch_assoc($result_jurnal)['total'];
            echo $total_jurnal;
            ?></h3>
            <p>Jurnal Kasus Saya</p>
          </div>
          <div class="icon">
            <i class="fa fa-book"></i>
          </div>
          <!--<a href="jurnal.php" class="small-box-footer">
            Lihat Detail <i class="fa fa-arrow-circle-right"></i>
          </a>-->
        </div>
      </div>

      <!-- Widget Siswa Aktif -->
      <div class="col-lg-3 col-xs-6">
        <div class="small-box bg-navy">
          <div class="inner">
            <h3><?php
            $query_siswa = "SELECT COUNT(DISTINCT s.siswa_id) as total 
                           FROM siswa s 
                           JOIN kelas_siswa ks ON s.siswa_id = ks.ks_siswa 
                           JOIN kelas k ON ks.ks_kelas = k.kelas_id 
                           JOIN ta t ON k.kelas_ta = t.ta_id 
                           WHERE t.ta_status = 1 AND s.siswa_status = 'aktif'";
            $result_siswa = mysqli_query($koneksi, $query_siswa);
            $total_siswa = mysqli_fetch_assoc($result_siswa)['total'];
            echo $total_siswa;
            ?></h3>
            <p>Siswa Aktif</p>
          </div>
          <div class="icon">
            <i class="fa fa-users"></i>
          </div>
          <!--<a href="siswa.php" class="small-box-footer">
            //Lihat Detail <i class="fa fa-arrow-circle-right"></i>
          //</a>-->
        </div>
      </div>

      <!-- Widget Kelas -->
      <div class="col-lg-3 col-xs-6">
        <div class="small-box bg-olive">
          <div class="inner">
            <h3><?php
            $query_kelas = "SELECT COUNT(*) as total 
                           FROM kelas k 
                           JOIN ta t ON k.kelas_ta = t.ta_id 
                           WHERE t.ta_status = 1";
            $result_kelas = mysqli_query($koneksi, $query_kelas);
            $total_kelas = mysqli_fetch_assoc($result_kelas)['total'];
            echo $total_kelas;
            ?></h3>
            <p>Total Kelas</p>
          </div>
          <div class="icon">
            <i class="fa fa-building"></i>
          </div>
          <!--<a href="kelas.php" class="small-box-footer">
            Lihat Detail <i class="fa fa-arrow-circle-right"></i>
          </a>-->
        </div>
      </div>

      <!-- Widget Tahun Ajaran -->
      <div class="col-lg-3 col-xs-6">
        <div class="small-box bg-teal">
          <div class="inner">
            <h3><?php
            $query_ta = "SELECT COUNT(*) as total FROM ta WHERE ta_status = 1";
            $result_ta = mysqli_query($koneksi, $query_ta);
            $total_ta = mysqli_fetch_assoc($result_ta)['total'];
            echo $total_ta;
            ?></h3>
            <p>Tahun Ajaran Aktif</p>
          </div>
          <div class="icon">
            <i class="fa fa-calendar"></i>
          </div>
          <!--<a href="ta.php" class="small-box-footer">
            Lihat Detail <i class="fa fa-arrow-circle-right"></i>
          </a>-->
        </div>
      </div>
    </div>

    <!-- Quick Actions -->
    <div class="row">
      <div class="col-md-12">
        <div class="box box-primary">
          <div class="box-header with-border">
            <h3 class="box-title">Aksi Cepat</h3>
          </div>
          <div class="box-body">
            <div class="row">
              <div class="col-md-3">
                <a href="kasus_siswa_tambah.php" class="btn btn-primary btn-block">
                  <i class="fa fa-plus"></i><br>
                  <small>Tambah Kasus Baru</small>
                </a>
              </div>
              <div class="col-md-3">
                <a href="kasus_siswa.php" class="btn btn-info btn-block">
                  <i class="fa fa-folder-open"></i><br>
                  <small>Lihat Semua Kasus</small>
                </a>
              </div>
              <div class="col-md-3">
                <a href="kunjungan_rumah_tambah.php" class="btn btn-success btn-block">
                  <i class="fa fa-home"></i><br>
                  <small>Tambah Kunjungan Rumah</small>
                </a>
              </div>
              <div class="col-md-3">
                <a href="profil.php" class="btn btn-warning btn-block">
                  <i class="fa fa-user"></i><br>
                  <small>Edit Profil</small>
                </a>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Recent Cases -->
    <div class="row">
      <div class="col-md-12">
        <div class="box box-info">
          <div class="box-header with-border">
            <h3 class="box-title">Kasus Terbaru</h3>
          </div>
          <div class="box-body">
            <div class="table-responsive">
              <table class="table table-bordered table-striped">
                <thead>
                  <tr>
                    <th>No</th>
                    <th>Kode Kasus</th>
                    <th>Nama Siswa</th>
                    <th>Kategori</th>
                    <th>Status</th>
                    <th>Tanggal</th>
                    <th>Aksi</th>
                  </tr>
                </thead>
                <tbody>
                  <?php
                  $query_recent = "SELECT k.*, s.siswa_nama, s.siswa_nis 
                                  FROM kasus_siswa k 
                                  JOIN siswa s ON k.siswa_id = s.siswa_id 
                                  WHERE k.guru_bk_id = '$user_id' 
                                  ORDER BY k.tanggal_pelaporan DESC 
                                  LIMIT 10";
                  $result_recent = mysqli_query($koneksi, $query_recent);
                  $no = 1;
                  while($row = mysqli_fetch_assoc($result_recent)) {
                  ?>
                  <tr>
                    <td><?php echo $no++; ?></td>
                    <td><?php echo $row['kasus_kode']; ?></td>
                    <td><?php echo $row['siswa_nama']; ?></td>
                    <td>
                      <span class="label label-info"><?php echo $row['kategori_masalah']; ?></span>
                    </td>
                    <td>
                      <?php
                      $status_class = '';
                      switch($row['status_kasus']) {
                        case 'Baru': $status_class = 'label-danger'; break;
                        case 'Dalam Proses': $status_class = 'label-warning'; break;
                        case 'Selesai/Tuntas': $status_class = 'label-success'; break;
                        case 'Dirujuk/Alih Tangan Kasus': $status_class = 'label-primary'; break;
                      }
                      ?>
                      <span class="label <?php echo $status_class; ?>"><?php echo $row['status_kasus']; ?></span>
                    </td>
                    <td><?php echo date('d/m/Y', strtotime($row['tanggal_pelaporan'])); ?></td>
                    <td>
                      <a href="kasus_siswa_detail.php?id=<?php echo $row['kasus_id']; ?>" 
                         class="btn btn-info btn-xs">
                        <i class="fa fa-eye"></i> Detail
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
  </section>
</div>

<?php include 'footer.php'; ?>
