<?php include 'header.php'; ?>

<div class="content-wrapper">

  <section class="content-header">
    <h1>
      Dashboard
      <small>Control panel</small>
    </h1>
    <ol class="breadcrumb">
      <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
      <li class="active">Dashboard</li>
    </ol>
  </section>


  <section class="content">

    <div class="row">

      <div class="col-lg-3 col-xs-6">
        <div class="small-box bg-green">
          <div class="inner">
            <?php 
            $siswa = mysqli_query($koneksi,"SELECT * FROM siswa");
            ?>
            <h3><?php echo mysqli_num_rows($siswa); ?></h3>
            <p>Siswa</p>
          </div>
          <div class="icon">
            <i class="ion ion-stats-bars"></i>
          </div>
        </div>
      </div>

      <div class="col-lg-3 col-xs-6">
        <div class="small-box bg-yellow">
          <div class="inner">
            <?php 
            $user = mysqli_query($koneksi,"SELECT * FROM user");
            ?>
            <h3><?php echo mysqli_num_rows($user); ?></h3>
            <p>Admin</p>
          </div>
          <div class="icon">
            <i class="ion ion-person-add"></i>
          </div>
        </div>
      </div>

      <div class="col-lg-3 col-xs-6">
        <div class="small-box bg-red">
          <div class="inner">
            <?php 
            $jurusan = mysqli_query($koneksi,"SELECT * FROM jurusan");
            ?>
            <h3><?php echo mysqli_num_rows($jurusan); ?></h3>
            <p>Jurusan</p>
          </div>
          <div class="icon">
            <i class="ion ion-pie-graph"></i>
          </div>
        </div>
      </div>

      <div class="col-lg-3 col-xs-6">
        <div class="small-box bg-red">
          <div class="inner">
            <?php 
            $kelas = mysqli_query($koneksi,"SELECT * FROM kelas");
            ?>
            <h3><?php echo mysqli_num_rows($kelas); ?></h3>
            <p>Total Kelas</p>
          </div>
          <div class="icon">
            <i class="ion ion-pie-graph"></i>
          </div>
        </div>
      </div>

      <div class="col-lg-3 col-xs-6">
        <div class="small-box bg-red">
          <div class="inner">
            <?php 
            $pelanggaran = mysqli_query($koneksi,"SELECT * FROM pelanggaran");
            ?>
            <h3><?php echo mysqli_num_rows($pelanggaran); ?></h3>
            <p>Jenis Pelanggaran</p>
          </div>
          <div class="icon">
            <i class="ion ion-android-list"></i>
          </div>
        </div>
      </div>


      <div class="col-lg-3 col-xs-6">
        <div class="small-box bg-green">
          <div class="inner">
            <?php 
            $prestasi = mysqli_query($koneksi,"SELECT * FROM prestasi");
            ?>
            <h3><?php echo mysqli_num_rows($prestasi); ?></h3>
            <p>Jenis Prestasi</p>
          </div>
          <div class="icon">
            <i class="ion ion-android-list"></i>
          </div>
        </div>
      </div>

      <div class="col-lg-3 col-xs-6">
        <div class="small-box bg-red">
          <div class="inner">
            <?php 
            $input_pelanggaran = mysqli_query($koneksi,"SELECT * FROM input_pelanggaran");
            ?>
            <h3><?php echo mysqli_num_rows($input_pelanggaran); ?></h3>
            <p>Transaksi Pelanggaran</p>
          </div>
          <div class="icon">
            <i class="ion ion-android-list"></i>
          </div>
        </div>
      </div>

      <div class="col-lg-3 col-xs-6">
        <div class="small-box bg-green">
          <div class="inner">
            <?php 
            $input_prestasi = mysqli_query($koneksi,"SELECT * FROM input_prestasi");
            ?>
            <h3><?php echo mysqli_num_rows($input_prestasi); ?></h3>
            <p>Transaksi Prestasi</p>
          </div>
          <div class="icon">
            <i class="ion ion-android-list"></i>
          </div>
        </div>
      </div>

      <!-- Widget Kasus Siswa -->
      <div class="col-lg-3 col-xs-6">
        <div class="small-box bg-purple">
          <div class="inner">
            <?php 
            $kasus_siswa = mysqli_query($koneksi,"SELECT * FROM kasus_siswa");
            ?>
            <h3><?php echo mysqli_num_rows($kasus_siswa); ?></h3>
            <p>Total Kasus Siswa</p>
          </div>
          <div class="icon">
            <i class="fa fa-folder"></i>
          </div>
          <a href="kasus_siswa.php" class="small-box-footer">
            Lihat Detail <i class="fa fa-arrow-circle-right"></i>
          </a>
        </div>
      </div>

      <div class="col-lg-3 col-xs-6">
        <div class="small-box bg-orange">
          <div class="inner">
            <?php 
            $kasus_baru = mysqli_query($koneksi,"SELECT * FROM kasus_siswa WHERE status_kasus = 'Baru'");
            ?>
            <h3><?php echo mysqli_num_rows($kasus_baru); ?></h3>
            <p>Kasus Baru</p>
          </div>
          <div class="icon">
            <i class="fa fa-plus"></i>
          </div>
          <a href="kasus_siswa.php?filter=Baru" class="small-box-footer">
            Lihat Detail <i class="fa fa-arrow-circle-right"></i>
          </a>
        </div>
      </div>

      <!-- Widget Kasus Dalam Proses -->
      <div class="col-lg-3 col-xs-6">
        <div class="small-box bg-yellow">
          <div class="inner">
            <?php 
            $kasus_proses = mysqli_query($koneksi,"SELECT * FROM kasus_siswa WHERE status_kasus = 'Dalam Proses'");
            ?>
            <h3><?php echo mysqli_num_rows($kasus_proses); ?></h3>
            <p>Kasus Dalam Proses</p>
          </div>
          <div class="icon">
            <i class="fa fa-clock-o"></i>
          </div>
          <a href="kasus_siswa.php?filter=Dalam Proses" class="small-box-footer">
            Lihat Detail <i class="fa fa-arrow-circle-right"></i>
          </a>
        </div>
      </div>

      <!-- Widget Kasus Selesai -->
      <div class="col-lg-3 col-xs-6">
        <div class="small-box bg-green">
          <div class="inner">
            <?php 
            $kasus_selesai = mysqli_query($koneksi,"SELECT * FROM kasus_siswa WHERE status_kasus = 'Selesai/Tuntas'");
            ?>
            <h3><?php echo mysqli_num_rows($kasus_selesai); ?></h3>
            <p>Kasus Selesai</p>
          </div>
          <div class="icon">
            <i class="fa fa-check"></i>
          </div>
          <a href="kasus_siswa.php?filter=Selesai/Tuntas" class="small-box-footer">
            Lihat Detail <i class="fa fa-arrow-circle-right"></i>
          </a>
        </div>
      </div>

      <!-- Widget Guru BK -->
      <div class="col-lg-3 col-xs-6">
        <div class="small-box bg-teal">
          <div class="inner">
            <?php 
            $guru_bk = mysqli_query($koneksi,"SELECT * FROM user WHERE user_level = 'guru_bk'");
            ?>
            <h3><?php echo mysqli_num_rows($guru_bk); ?></h3>
            <p>Guru BK</p>
          </div>
          <div class="icon">
            <i class="fa fa-user-md"></i>
          </div>
          <a href="user.php" class="small-box-footer">
            Lihat Detail <i class="fa fa-arrow-circle-right"></i>
          </a>
        </div>
      </div>

      <!-- Widget Layanan BK -->
      <div class="col-lg-3 col-xs-6">
        <div class="small-box bg-aqua">
          <div class="inner">
            <?php 
            $total_layanan_bk = mysqli_query($koneksi,"SELECT COUNT(*) as total FROM layanan_bk");
            $total_layanan_bk = mysqli_fetch_assoc($total_layanan_bk);
            ?>
            <h3><?php echo $total_layanan_bk['total']; ?></h3>
            <p>Total Layanan BK</p>
          </div>
          <div class="icon">
            <i class="fa fa-graduation-cap"></i>
          </div>
          <a href="layanan_bk.php" class="small-box-footer">
            Lihat Detail <i class="fa fa-arrow-circle-right"></i>
          </a>
        </div>
      </div>

      <div class="col-lg-3 col-xs-6">
        <div class="small-box bg-teal">
          <div class="inner">
            <?php 
            $layanan_bulan_ini = mysqli_query($koneksi,"SELECT COUNT(*) as total FROM layanan_bk WHERE MONTH(tanggal_pelaksanaan) = MONTH(CURDATE()) AND YEAR(tanggal_pelaksanaan) = YEAR(CURDATE())");
            $layanan_bulan_ini = mysqli_fetch_assoc($layanan_bulan_ini);
            ?>
            <h3><?php echo $layanan_bulan_ini['total']; ?></h3>
            <p>Layanan BK Bulan Ini</p>
          </div>
          <div class="icon">
            <i class="fa fa-calendar"></i>
          </div>
          <a href="layanan_bk_kalender.php" class="small-box-footer">
            Lihat Kalender <i class="fa fa-arrow-circle-right"></i>
          </a>
        </div>
      </div>

      <div class="col-lg-3 col-xs-6">
        <div class="small-box bg-yellow">
          <div class="inner">
            <?php 
            $notifikasi_rtl = mysqli_query($koneksi,"SELECT * FROM notifikasi_rtl WHERE status_reminder = 'Belum'");
            ?>
            <h3><?php echo mysqli_num_rows($notifikasi_rtl); ?></h3>
            <p>Notifikasi RTL</p>
          </div>
          <div class="icon">
            <i class="fa fa-bell"></i>
          </div>
          <a href="notifikasi_rtl.php" class="small-box-footer">
            Lihat Detail <i class="fa fa-arrow-circle-right"></i>
          </a>
        </div>
      </div>



      

    </div>

    <div class="row">    
      <section class="col-lg-12">

        <div class="box box-primary">
          <div class="box-body text-center">
            <br>
            <br>
            <h3 class="text-bold">Selamat Datang Di <?php echo getSetting($app_settings, 'app_name', 'Sistem Informasi E-Point Siswa'); ?></h3>
            <p class="text-muted"><?php echo getSetting($app_settings, 'app_author', 'Madrasah Aliyah YASMU Manyar'); ?></p>
            <br>
            <br>
          </div>
        </div>
      </section>
    </div>

  </section>

</div>
<?php include 'footer.php'; ?>