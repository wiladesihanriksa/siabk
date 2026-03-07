<?php 
include 'header.php';
include 'functions_academic_year.php';

// Update status tahun ajaran berdasarkan tanggal saat ini
updateAcademicYearStatus($koneksi);
?>

<div class="content-wrapper">

  <section class="content-header">
    <h1>
      Siswa
      <small>Data Siswa</small>
    </h1>
    <ol class="breadcrumb">
      <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
      <li class="active">Dashboard</li>
    </ol>
  </section>

  <section class="content">
    <div class="row">
      <section class="col-lg-12">       
        <div class="box box-primary">

          <div class="box-header">
            <h3 class="box-title">Tentang Siswa</h3>
            <a href="siswa.php" class="btn btn-primary btn-sm pull-right"><i class="fa fa-reply"></i> &nbsp Kembali</a> 
          </div>
          
          <!-- Filter Tahun Ajaran -->
          <div class="box-body">
            <form method="GET" action="">
              <input type="hidden" name="id" value="<?php echo $_GET['id']; ?>">
              <div class="row">
                <div class="col-md-4">
                  <div class="form-group">
                    <label>Tahun Ajaran:</label>
                    <select name="ta_filter" class="form-control" onchange="this.form.submit()">
                      <option value="">-- Semua Tahun Ajaran --</option>
                      <?php
                      $ta_query = mysqli_query($koneksi, "SELECT * FROM ta ORDER BY ta_id DESC");
                      while($ta = mysqli_fetch_array($ta_query)){
                        $selected = (isset($_GET['ta_filter']) && $_GET['ta_filter'] == $ta['ta_id']) ? 'selected' : '';
                        // Set tahun ajaran aktif sebagai default jika tidak ada filter
                        if(!isset($_GET['ta_filter']) && $ta['ta_status'] == 1){
                          $selected = 'selected';
                        }
                        echo "<option value='".$ta['ta_id']."' $selected>".$ta['ta_nama']."</option>";
                      }
                      ?>
                    </select>
                  </div>
                </div>
                <div class="col-md-3">
                  <div class="form-group">
                    <label>&nbsp;</label><br>
                    <a href="siswa_riwayat.php?id=<?php echo $_GET['id']; ?>" class="btn btn-default btn-sm"><i class="fa fa-refresh"></i> Reset Filter</a>
                  </div>
                </div>
              </div>
            </form>
          </div>
          <div class="box-body">
            <?php 
            $id = $_GET['id'];              
            $kelas = mysqli_query($koneksi, "select * from siswa, jurusan where siswa_jurusan=jurusan_id and siswa_id='$id'");
            $k = mysqli_fetch_assoc($kelas);
            $id_siswa = $k['siswa_id'];
            
            // Tentukan tahun ajaran yang akan ditampilkan
            if(isset($_GET['ta_filter']) && !empty($_GET['ta_filter'])){
              $ta_filter = $_GET['ta_filter'];
              $ta_selected = mysqli_fetch_array(mysqli_query($koneksi, "SELECT * FROM ta WHERE ta_id = '$ta_filter'"));
            } else {
              // Default ke tahun ajaran aktif
              $ta_active = mysqli_fetch_array(mysqli_query($koneksi, "SELECT * FROM ta WHERE ta_status = 1"));
              $ta_filter = $ta_active['ta_id'];
              $ta_selected = $ta_active;
            }
            ?>

            <div class="alert alert-info">
              <i class="fa fa-info-circle"></i> 
              Menampilkan riwayat untuk tahun ajaran: <strong><?php echo $ta_selected['ta_nama']; ?></strong>
              <?php if($ta_selected['ta_status'] == 1): ?>
                <span class="label label-success">Aktif</span>
              <?php endif; ?>
            </div>

            <div class="table-responsive">
              <table class="table table-bordered">
                <tr>
                  <th width="30%">Nama Siswa</th>
                  <td><?php echo $k['siswa_nama'] ?></td>
                </tr>
                <tr>
                  <th>NIS</th>
                  <td><?php echo $k['siswa_nis'] ?></td>
                </tr>
                <tr>
                  <th>Jurusan</th>
                  <td><?php echo $k['jurusan_nama'] ?></td>
                </tr>
              </table>
            </div>

          </div>

        </div>
      </section>
    </div>


    <div class="row">
      <div class="col-lg-6">

        <div class="box box-success">
          <div class="box-header">
            <h3 class="box-title">Riwayat Prestasi</h3>
            <?php
            $count_prestasi = mysqli_query($koneksi,"SELECT COUNT(*) as total FROM input_prestasi, kelas, ta 
                           WHERE kelas_ta=ta_id 
                           AND input_prestasi.kelas=kelas_id 
                           AND input_prestasi.siswa='$id_siswa'
                           AND ta_id='$ta_filter'");
            $total_prestasi = mysqli_fetch_array($count_prestasi)['total'];
            ?>
            <span class="label label-success pull-right"><?php echo $total_prestasi; ?> data</span>
          </div>
          <div class="box-body">
            <div class="table-responsive">
              <table class="table table-bordered table-striped">
                <thead>
                  <tr>
                    <th width="1%">NO</th>
                    <th>WAKTU</th>
                    <th>KELAS</th>
                    <th>TAHUN AJARAN</th>
                    <th>PRESTASI</th>
                    <th>POINT</th>
                  </tr>
                </thead>
                <tbody>
                  <?php 
                  $no=1;
                  $total = 0;
                  $id = $_GET['id']; 
                  
                  // Query prestasi dengan filter tahun ajaran
                  $data = mysqli_query($koneksi,"SELECT * FROM input_prestasi, prestasi, kelas, ta 
                           WHERE kelas_ta=ta_id 
                           AND input_prestasi.kelas=kelas_id 
                           AND input_prestasi.prestasi=prestasi_id 
                           AND input_prestasi.siswa='$id_siswa'
                           AND ta_id='$ta_filter'");
                  while($d = mysqli_fetch_array($data)){
                    ?>
                    <tr>
                      <td><?php echo $no++; ?></td>
                      <td><?php echo date('d-m-Y H:i:s', strtotime($d['waktu'])); ?></td>
                      <td><?php echo $d['kelas_nama']; ?></td>
                      <td><?php echo $d['ta_nama']; ?></td>
                      <td><?php echo $d['prestasi_nama']; ?></td>
                      <td class="text-center"><?php echo $d['prestasi_point']; ?></td>
                    </tr>
                    <?php 
                    $total += $d['prestasi_point'];
                  }
                  ?>
                </tbody>
                <tfoot>
                  <tr>
                    <td class="text-center text-bold" colspan="5">TOTAL</td>
                    <td class="bg-green text-center text-bold"><?php echo $total ?></td>
                  </tr>
                </tfoot>
              </table>
            </div>
          </div>
        </div>


      </div>
      <div class="col-lg-6">

        <div class="box box-danger">
          <div class="box-header">
            <h3 class="box-title">Riwayat Pelanggaran</h3>
            <?php
            $count_pelanggaran = mysqli_query($koneksi,"SELECT COUNT(*) as total FROM input_pelanggaran, kelas, ta 
                           WHERE kelas_ta=ta_id 
                           AND input_pelanggaran.kelas=kelas_id 
                           AND input_pelanggaran.siswa='$id_siswa'
                           AND ta_id='$ta_filter'");
            $total_pelanggaran = mysqli_fetch_array($count_pelanggaran)['total'];
            ?>
            <span class="label label-danger pull-right"><?php echo $total_pelanggaran; ?> data</span>
          </div>
          <div class="box-body">
            <div class="table-responsive">
              <table class="table table-bordered table-striped">
                <thead>
                  <tr>
                    <th width="1%">NO</th>
                    <th>WAKTU</th>
                    <th>KELAS</th>
                    <th>TAHUN AJARAN</th>
                    <th>PRESTASI</th>
                    <th>POINT</th>
                  </tr>
                </thead>
                <tbody>
                  <?php 
                  $no=1;
                  $total = 0;
                  $id = $_GET['id']; 
                  
                  // Query pelanggaran dengan filter tahun ajaran
                  $data = mysqli_query($koneksi,"SELECT * FROM input_pelanggaran, pelanggaran, kelas, ta 
                           WHERE kelas_ta=ta_id 
                           AND input_pelanggaran.kelas=kelas_id 
                           AND input_pelanggaran.pelanggaran=pelanggaran_id 
                           AND input_pelanggaran.siswa='$id_siswa'
                           AND ta_id='$ta_filter'");
                  while($d = mysqli_fetch_array($data)){
                    ?>
                    <tr>
                      <td><?php echo $no++; ?></td>
                      <td><?php echo date('d-m-Y H:i:s', strtotime($d['waktu'])); ?></td>
                      <td><?php echo $d['kelas_nama']; ?></td>
                      <td><?php echo $d['ta_nama']; ?></td>
                      <td><?php echo $d['pelanggaran_nama']; ?></td>
                      <td class="text-center"><?php echo $d['pelanggaran_point']; ?></td>
                    </tr>
                    <?php 
                    $total += $d['pelanggaran_point'];
                  }
                  ?>
                </tbody>
                <tfoot>
                  <tr>
                    <td class="text-center text-bold" colspan="5">TOTAL</td>
                    <td class="bg-green text-center text-bold"><?php echo $total ?></td>
                  </tr>
                </tfoot>
              </table>
            </div>
          </div>
        </div>

      </div>
    </div>

  </section>

</div>
<?php include 'footer.php'; ?>