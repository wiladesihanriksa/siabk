<?php 
include 'header_dynamic.php';
include 'functions_academic_year.php';

// Update status tahun ajaran berdasarkan tanggal saat ini
updateAcademicYearStatus($koneksi);
?>

<div class="content-wrapper">

  <section class="content-header">
    <h1>
      LAPORAN
      <small>Data Laporan Point Siswa</small>
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
            <h3 class="box-title">Filter Laporan Point</h3>
            <div class="btn-group pull-right">
              <a href="cetak_raport.php" class="btn btn-info btn-sm">
                <i class="fa fa-print"></i> &nbsp Cetak Raport
              </a>
              <?php if($_SESSION['level'] == 'administrator'): ?>
                <a href="pengaturan_raport.php" class="btn btn-warning btn-sm">
                  <i class="fa fa-cog"></i> &nbsp Pengaturan Raport
                </a>
              <?php else: ?>
                <button onclick="showAccessDeniedAlert()" class="btn btn-warning btn-sm">
                  <i class="fa fa-cog"></i> &nbsp Pengaturan Raport
                </button>
              <?php endif; ?>
            </div>
          </div>
          <div class="box-body">
            <form method="get" action="">
              <div class="row">
                <div class="col-md-4">

                  <div class="form-group">
                    <label>Mulai Tanggal</label>
                    <input autocomplete="off" type="text" value="<?php if(isset($_GET['tanggal_dari'])){echo $_GET['tanggal_dari'];}else{echo "";} ?>" name="tanggal_dari" class="form-control datepicker2" placeholder="Mulai Tanggal" required="required">
                  </div>

                  <div class="form-group">
                    <label>Sampai Tanggal</label>
                    <input autocomplete="off" type="text" value="<?php if(isset($_GET['tanggal_sampai'])){echo $_GET['tanggal_sampai'];}else{echo "";} ?>" name="tanggal_sampai" class="form-control datepicker2" placeholder="Sampai Tanggal" required="required">
                  </div>

                  <div class="form-group">
                    <label>Kelas</label>
                    <select name="kelas_id" class="form-control">
                      <option value="">Semua Kelas</option>
                      <?php
                      $kelas_query = mysqli_query($koneksi, "SELECT k.*, j.jurusan_nama, ta.ta_nama 
                          FROM kelas k 
                          JOIN jurusan j ON k.kelas_jurusan = j.jurusan_id 
                          JOIN ta ON k.kelas_ta = ta.ta_id 
                          WHERE ta.ta_status = 1 
                          ORDER BY k.kelas_nama");
                      while($k = mysqli_fetch_assoc($kelas_query)) {
                          $selected = (isset($_GET['kelas_id']) && $_GET['kelas_id'] == $k['kelas_id']) ? 'selected' : '';
                          echo "<option value='{$k['kelas_id']}' $selected>{$k['kelas_nama']} - {$k['jurusan_nama']}</option>";
                      }
                      ?>
                    </select>
                  </div>

                  <div class="form-group">
                    <label>Urutkan</label>
                    <select name="urutkan" class="form-control">
                      <option value="">Tanpa Pengurutan</option>
                      <option <?php if(isset($_GET['urutkan'])){ if($_GET['urutkan'] == "prestasi_terbanyak"){echo "selected='selected'";}} ?> value="prestasi_terbanyak">Prestasi Terbanyak</option>
                      <option <?php if(isset($_GET['urutkan'])){ if($_GET['urutkan'] == "pelanggaran_terbanyak"){echo "selected='selected'";}} ?> value="pelanggaran_terbanyak">Pelanggaran Terbanyak</option>
                    </select>
                  </div>

                  <div class="form-group">
                    <input type="submit" value="TAMPILKAN" class="btn btn-sm btn-primary">
                  </div>

                </div>
              </div>
            </form>
          </div>
        </div>

        <div class="box box-primary">
          <div class="box-header">
            <h3 class="box-title">Laporan</h3>
          </div>
          <div class="box-body">

            <?php 
            if(isset($_GET['tanggal_sampai']) && isset($_GET['tanggal_dari']) && isset($_GET['urutkan'])){
              $tgl_dari = $_GET['tanggal_dari'];
              $tgl_sampai = $_GET['tanggal_sampai'];
              $urutkan = $_GET['urutkan'];
              ?>

              <div class="row">
                <div class="col-lg-6">
                  <table class="table table-bordered">
                    <tr>
                      <th width="30%">DARI TANGGAL</th>
                      <th width="1%">:</th>
                      <td><?php echo $tgl_dari; ?></td>
                    </tr>
                    <tr>
                      <th>SAMPAI TANGGAL</th>
                      <th>:</th>
                      <td><?php echo $tgl_sampai; ?></td>
                    </tr>
                    <tr>
                      <th>URUTKAN</th>
                      <th>:</th>
                      <td><?php echo $urutkan; ?></td>
                    </tr>
                    <?php if(isset($_GET['kelas_id']) && !empty($_GET['kelas_id'])): ?>
                    <tr>
                      <th>KELAS</th>
                      <th>:</th>
                      <td>
                        <?php 
                        $kelas_info = mysqli_query($koneksi, "SELECT k.kelas_nama, j.jurusan_nama FROM kelas k JOIN jurusan j ON k.kelas_jurusan = j.jurusan_id WHERE k.kelas_id = '{$_GET['kelas_id']}'");
                        $kelas_data = mysqli_fetch_assoc($kelas_info);
                        echo $kelas_data['kelas_nama'] . ' - ' . $kelas_data['jurusan_nama'];
                        ?>
                      </td>
                    </tr>
                    <?php endif; ?>
                  </table>
                  
                </div>
              </div>


              <div class="btn-group">
                <a href="laporan_pdf.php?tanggal_dari=<?php echo $tgl_dari ?>&tanggal_sampai=<?php echo $tgl_sampai ?>&urutkan=<?php echo $urutkan ?>" target="_blank" class="btn btn-sm btn-success"><i class="fa fa-file-pdf-o"></i> &nbsp CETAK PDF LAMA</a>
                <?php if(isset($_GET['kelas_id']) && !empty($_GET['kelas_id'])): ?>
                  <a href="raport_kelas_pdf.php?kelas_id=<?php echo $_GET['kelas_id'] ?>" target="_blank" class="btn btn-sm btn-primary"><i class="fa fa-file-pdf-o"></i> &nbsp RAPORT KELAS</a>
                <?php endif; ?>
              </div>

              <div class="table-responsive">
                <table class="table table-bordered table-striped" id="table-datatable">
                  <thead>
                    <tr>
                      <th width="1%">NO</th>
                      <th>NAMA SISWA</th>
                      <th class="text-center">NIS</th>
                      <th class="text-center">TOTAL PRESTASI</th>
                      <th class="text-center">TOTAL PELANGGARAN</th>
                      <th class="text-center">OPSI</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php 
                    $no=1;
                    if(isset($_GET['urutkan'])){
                      if($urutkan == ""){
                        $u = "";
                      }else if($urutkan == "prestasi_terbanyak"){
                        $u = " order by total_prestasi desc";
                      }else if($urutkan == "pelanggaran_terbanyak"){
                        $u = " order by total_pelanggaran desc";
                      }
                    }else{
                      $u = "";
                    }
                    
                    // Query untuk mendapatkan semua siswa yang memenuhi filter
                    $where_conditions = array();
                    
                    // Filter kelas jika dipilih
                    if(isset($_GET['kelas_id']) && !empty($_GET['kelas_id'])){
                        $kelas_id = $_GET['kelas_id'];
                        $where_conditions[] = "ks.ks_kelas = '$kelas_id'";
                    }
                    
                    $where_clause = "";
                    if(!empty($where_conditions)){
                        $where_clause = "WHERE " . implode(" AND ", $where_conditions);
                    }
                    
                    $query = "SELECT DISTINCT s.siswa_id, s.siswa_nama, s.siswa_nis,
                        COALESCE(SUM(CASE WHEN ip.waktu >= '$tgl_dari' AND ip.waktu <= '$tgl_sampai' THEN p.prestasi_point ELSE 0 END), 0) as total_prestasi,
                        COALESCE(SUM(CASE WHEN ipl.waktu >= '$tgl_dari' AND ipl.waktu <= '$tgl_sampai' THEN pl.pelanggaran_point ELSE 0 END), 0) as total_pelanggaran
                        FROM siswa s
                        LEFT JOIN kelas_siswa ks ON s.siswa_id = ks.ks_siswa
                        LEFT JOIN input_prestasi ip ON s.siswa_id = ip.siswa
                        LEFT JOIN prestasi p ON ip.prestasi = p.prestasi_id
                        LEFT JOIN input_pelanggaran ipl ON s.siswa_id = ipl.siswa
                        LEFT JOIN pelanggaran pl ON ipl.pelanggaran = pl.pelanggaran_id
                        $where_clause
                        GROUP BY s.siswa_id $u";
                    
                    // Debug: tampilkan query untuk troubleshooting
                    // echo "<div class='alert alert-info'>Query: " . $query . "</div>";
                    
                    $data = mysqli_query($koneksi, $query);
                    
                    if (!$data) {
                        echo "<tr><td colspan='6' class='text-center text-danger'>Error: " . mysqli_error($koneksi) . "</td></tr>";
                        echo "<tr><td colspan='6' class='text-center text-info'>Query: " . $query . "</td></tr>";
                    } else {
                        while($d = mysqli_fetch_array($data)){
                      $id_siswa = $d['siswa_id'];
                      ?>
                      <tr>
                        <td><?php echo $no++; ?></td>
                        <td><?php echo $d['siswa_nama']; ?></td>
                        <td class="text-center"><?php echo $d['siswa_nis']; ?></td>
                        <td class="text-center">
                          <?php echo $d['total_prestasi']; ?>
                        </td>
                        <td class="text-center">
                          <?php echo $d['total_pelanggaran']; ?>
                        </td>
                        <td class="text-center">
                           <a class="btn btn-success btn-sm" target="_blank" href="siswa_riwayat.php?id=<?php echo $d['siswa_id'] ?>"><i class="fa fa-info-circle"></i> Detail</a>
                           <a class="btn btn-primary btn-sm" target="_blank" href="raport_siswa_pdf.php?id=<?php echo $d['siswa_id'] ?>"><i class="fa fa-file-pdf-o"></i> Raport</a>
                        </td>
                      </tr>
                      <?php 
                        }
                    }
                    ?>
                  </tbody>
                </table>
              </div>

              <?php 
            }else{
              ?>

              <div class="alert alert-primary text-center">
                <h4><i class="icon fa fa-info"></i> Petunjuk Cetak Raport</h4>
                <div class="row">
                  <div class="col-md-6">
                    <div class="alert alert-success">
                      <h5><i class="fa fa-print"></i> Cetak Raport Per Siswa</h5>
                      <p>Untuk mencetak raport per siswa, silakan:</p>
                      <ol style="text-align: left;">
                        <li>Isi <strong>Mulai Tanggal</strong> dan <strong>Sampai Tanggal</strong></li>
                        <li>Pilih <strong>Kelas</strong> (opsional)</li>
                        <li>Pilih <strong>Urutkan</strong> (opsional)</li>
                        <li>Klik <strong>TAMPILKAN</strong></li>
                        <li>Klik tombol <strong>"Raport"</strong> di kolom Opsi untuk setiap siswa</li>
                      </ol>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="alert alert-info">
                      <h5><i class="fa fa-print"></i> Cetak Raport Per Kelas</h5>
                      <p>Untuk mencetak raport seluruh kelas:</p>
                      <ol style="text-align: left;">
                        <li>Isi <strong>Mulai Tanggal</strong> dan <strong>Sampai Tanggal</strong></li>
                        <li>Pilih <strong>Kelas</strong> yang ingin dicetak</li>
                        <li>Klik <strong>TAMPILKAN</strong></li>
                        <li>Klik tombol <strong>"RAPORT KELAS"</strong> yang muncul</li>
                      </ol>
                    </div>
                  </div>
                </div>
                <p><strong>Atau gunakan menu:</strong></p>
                <a href="cetak_raport.php" class="btn btn-info">
                  <i class="fa fa-print"></i> Cetak Raport Kelas
                </a>
                <?php if($_SESSION['level'] == 'administrator'): ?>
                  <a href="pengaturan_raport.php" class="btn btn-warning">
                    <i class="fa fa-cog"></i> Pengaturan Raport
                  </a>
                <?php else: ?>
                  <button onclick="showAccessDeniedAlert()" class="btn btn-warning">
                    <i class="fa fa-cog"></i> Pengaturan Raport
                  </button>
                <?php endif; ?>
              </div>

              <?php
            }
            ?>

          </div>
        </div>
      </section>
    </div>
  </section>

</div>

<script>
function showAccessDeniedAlert() {
    alert('Anda tidak berhak mengakses fitur ini. Hanya administrator yang dapat mengakses fitur cetak raport dan pengaturan raport.');
}
</script>

<?php include 'footer.php'; ?>