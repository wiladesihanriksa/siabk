<?php 
include 'header_dynamic.php';
include 'alert_helper.php';
?>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <section class="content-header">
    <h1>
      <i class="fa fa-file-text"></i> Laporan Kunjungan Rumah
      <small>Laporan dan Statistik Home Visit</small>
    </h1>
    <ol class="breadcrumb">
      <li><a href="index.php"><i class="fa fa-dashboard"></i> Home</a></li>
      <li><a href="kunjungan_rumah.php">Kunjungan Rumah</a></li>
      <li class="active">Laporan Kunjungan</li>
    </ol>
  </section>

  <!-- Main content -->
  <section class="content">
    <?php showAlert(); ?>
    
    <div class="row">
      <!-- Filter Laporan -->
      <div class="col-md-12">
        <div class="box box-primary">
          <div class="box-header">
            <h3 class="box-title">
              <i class="fa fa-filter"></i> Filter Laporan
            </h3>
          </div>
          <div class="box-body">
            <form method="GET" action="">
              <div class="row">
                <div class="col-md-3">
                  <div class="form-group">
                    <label>Tanggal Mulai:</label>
                    <input type="date" name="tanggal_mulai" class="form-control" 
                           value="<?php echo isset($_GET['tanggal_mulai']) ? $_GET['tanggal_mulai'] : date('Y-m-01'); ?>">
                  </div>
                </div>
                <div class="col-md-3">
                  <div class="form-group">
                    <label>Tanggal Akhir:</label>
                    <input type="date" name="tanggal_akhir" class="form-control" 
                           value="<?php echo isset($_GET['tanggal_akhir']) ? $_GET['tanggal_akhir'] : date('Y-m-d'); ?>">
                  </div>
                </div>
                <div class="col-md-3">
                  <div class="form-group">
                    <label>Petugas BK:</label>
                    <select name="petugas_id" class="form-control">
                      <option value="">Semua Petugas</option>
                      <?php
                      $query_petugas = "SELECT * FROM user WHERE user_level = 'administrator' OR user_level = 'guru_bk' ORDER BY user_nama";
                      $result_petugas = mysqli_query($koneksi, $query_petugas);
                      while($petugas = mysqli_fetch_assoc($result_petugas)) {
                        $selected = (isset($_GET['petugas_id']) && $_GET['petugas_id'] == $petugas['user_id']) ? 'selected' : '';
                        echo "<option value='" . $petugas['user_id'] . "' " . $selected . ">" . $petugas['user_nama'] . "</option>";
                      }
                      ?>
                    </select>
                  </div>
                </div>
                <div class="col-md-3">
                  <div class="form-group">
                    <label>&nbsp;</label>
                    <button type="submit" class="btn btn-primary btn-block">
                      <i class="fa fa-search"></i> Filter Laporan
                    </button>
                  </div>
                </div>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>

    <div class="row">
      <!-- Statistik Kunjungan -->
      <div class="col-md-12">
        <div class="box box-info">
          <div class="box-header">
            <h3 class="box-title">
              <i class="fa fa-chart-bar"></i> Statistik Kunjungan Rumah
            </h3>
          </div>
          <div class="box-body">
            <?php
            // Ambil parameter filter
            $tanggal_mulai = isset($_GET['tanggal_mulai']) ? $_GET['tanggal_mulai'] : date('Y-m-01');
            $tanggal_akhir = isset($_GET['tanggal_akhir']) ? $_GET['tanggal_akhir'] : date('Y-m-d');
            $petugas_id = isset($_GET['petugas_id']) ? $_GET['petugas_id'] : '';

            // Query statistik
            $where_clause = "WHERE k.tanggal_kunjungan BETWEEN '$tanggal_mulai' AND '$tanggal_akhir'";
            if(!empty($petugas_id)) {
                $where_clause .= " AND k.petugas_bk_id = '$petugas_id'";
            }

            // Total kunjungan
            $query_total = "SELECT COUNT(*) as total FROM kunjungan_rumah k $where_clause";
            $result_total = mysqli_query($koneksi, $query_total);
            $total_kunjungan = mysqli_fetch_assoc($result_total)['total'];

            // Kunjungan per petugas
            $query_petugas_stats = "SELECT u.user_nama, COUNT(*) as jumlah 
                                   FROM kunjungan_rumah k 
                                   LEFT JOIN user u ON k.petugas_bk_id = u.user_id 
                                   $where_clause 
                                   GROUP BY k.petugas_bk_id, u.user_nama 
                                   ORDER BY jumlah DESC";
            $result_petugas_stats = mysqli_query($koneksi, $query_petugas_stats);

            // Kunjungan per bulan (untuk chart)
            $query_bulan = "SELECT DATE_FORMAT(tanggal_kunjungan, '%Y-%m') as bulan, COUNT(*) as jumlah 
                           FROM kunjungan_rumah k 
                           $where_clause 
                           GROUP BY DATE_FORMAT(tanggal_kunjungan, '%Y-%m') 
                           ORDER BY bulan ASC";
            $result_bulan = mysqli_query($koneksi, $query_bulan);
            ?>

            <div class="row">
              <div class="col-md-3">
                <div class="info-box">
                  <span class="info-box-icon bg-blue"><i class="fa fa-home"></i></span>
                  <div class="info-box-content">
                    <span class="info-box-text">Total Kunjungan</span>
                    <span class="info-box-number"><?php echo $total_kunjungan; ?></span>
                  </div>
                </div>
              </div>
              <div class="col-md-3">
                <div class="info-box">
                  <span class="info-box-icon bg-green"><i class="fa fa-users"></i></span>
                  <div class="info-box-content">
                    <span class="info-box-text">Petugas Aktif</span>
                    <span class="info-box-number"><?php echo mysqli_num_rows($result_petugas_stats); ?></span>
                  </div>
                </div>
              </div>
              <div class="col-md-3">
                <div class="info-box">
                  <span class="info-box-icon bg-yellow"><i class="fa fa-calendar"></i></span>
                  <div class="info-box-content">
                    <span class="info-box-text">Periode</span>
                    <span class="info-box-number"><?php echo date('d/m/Y', strtotime($tanggal_mulai)) . ' - ' . date('d/m/Y', strtotime($tanggal_akhir)); ?></span>
                  </div>
                </div>
              </div>
              <div class="col-md-3">
                <div class="info-box">
                  <span class="info-box-icon bg-red"><i class="fa fa-camera"></i></span>
                  <div class="info-box-content">
                    <span class="info-box-text">Kunjungan dengan Foto</span>
                    <?php
                    $query_foto = "SELECT COUNT(DISTINCT k.kunjungan_id) as total 
                                  FROM kunjungan_rumah k 
                                  LEFT JOIN lampiran_kunjungan l ON k.kunjungan_id = l.kunjungan_id 
                                  $where_clause AND l.lampiran_id IS NOT NULL";
                    $result_foto = mysqli_query($koneksi, $query_foto);
                    $total_foto = mysqli_fetch_assoc($result_foto)['total'];
                    ?>
                    <span class="info-box-number"><?php echo $total_foto; ?></span>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="row">
      <!-- Daftar Kunjungan -->
      <div class="col-md-12">
        <div class="box box-success">
          <div class="box-header">
            <h3 class="box-title">
              <i class="fa fa-list"></i> Daftar Kunjungan (<?php echo $total_kunjungan; ?> data)
            </h3>
            <div class="box-tools pull-right">
              <?php
              $filter_params = http_build_query([
                  'tanggal_mulai' => $tanggal_mulai,
                  'tanggal_akhir' => $tanggal_akhir,
                  'petugas_id' => $petugas_id
              ]);
              ?>
              <a href="laporan_kunjungan_cetak.php?<?php echo $filter_params; ?>" 
                 class="btn btn-success btn-sm" target="_blank">
                <i class="fa fa-print"></i> Cetak Laporan
              </a>
              <a href="laporan_kunjungan_excel.php?<?php echo $filter_params; ?>" 
                 class="btn btn-warning btn-sm">
                <i class="fa fa-file-excel-o"></i> Export Excel
              </a>
            </div>
          </div>
          <div class="box-body">
            <div class="table-responsive">
              <table class="table table-bordered table-striped">
                <thead>
                  <tr>
                    <th width="5%">No</th>
                    <th width="12%">Kode</th>
                    <th width="15%">Nama Siswa</th>
                    <th width="10%">Tanggal</th>
                    <th width="10%">Waktu</th>
                    <th width="15%">Tujuan</th>
                    <th width="12%">Petugas BK</th>
                    <th width="8%">Foto</th>
                    <th width="13%">Aksi</th>
                  </tr>
                </thead>
                <tbody>
                  <?php
                  $no = 1;
                  $query_data = "SELECT k.*, s.siswa_nama, u.user_nama as petugas_nama,
                                (SELECT COUNT(*) FROM lampiran_kunjungan l WHERE l.kunjungan_id = k.kunjungan_id) as jumlah_foto
                                FROM kunjungan_rumah k 
                                LEFT JOIN siswa s ON k.siswa_id = s.siswa_id 
                                LEFT JOIN user u ON k.petugas_bk_id = u.user_id 
                                $where_clause 
                                ORDER BY k.tanggal_kunjungan DESC, k.waktu_kunjungan DESC";
                  $result_data = mysqli_query($koneksi, $query_data);
                  
                  if(mysqli_num_rows($result_data) > 0) {
                    while($data = mysqli_fetch_assoc($result_data)) {
                  ?>
                  <tr>
                    <td><?php echo $no++; ?></td>
                    <td><strong><?php echo $data['kunjungan_kode']; ?></strong></td>
                    <td><?php echo $data['siswa_nama']; ?></td>
                    <td><?php echo date('d/m/Y', strtotime($data['tanggal_kunjungan'])); ?></td>
                    <td><?php echo date('H:i', strtotime($data['waktu_kunjungan'])); ?></td>
                    <td><?php echo substr($data['tujuan_kunjungan'], 0, 40) . '...'; ?></td>
                    <td><?php echo $data['petugas_nama']; ?></td>
                    <td class="text-center">
                      <?php if($data['jumlah_foto'] > 0): ?>
                        <span class="label label-success"><?php echo $data['jumlah_foto']; ?> foto</span>
                      <?php else: ?>
                        <span class="label label-default">Tidak ada</span>
                      <?php endif; ?>
                    </td>
                    <td>
                      <div class="btn-group">
                        <a href="kunjungan_rumah_detail.php?id=<?php echo $data['kunjungan_id']; ?>" 
                           class="btn btn-info btn-xs" title="Detail">
                          <i class="fa fa-eye"></i>
                        </a>
                        <a href="kunjungan_rumah_cetak.php?id=<?php echo $data['kunjungan_id']; ?>" 
                           class="btn btn-success btn-xs" title="Cetak" target="_blank">
                          <i class="fa fa-print"></i>
                        </a>
                      </div>
                    </td>
                  </tr>
                  <?php 
                    }
                  } else {
                  ?>
                  <tr>
                    <td colspan="9" class="text-center">
                      <i class="fa fa-info-circle"></i> Tidak ada data kunjungan untuk periode yang dipilih
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

    <!-- Grafik Kunjungan per Bulan -->
    <?php if(mysqli_num_rows($result_bulan) > 0): ?>
    <div class="row">
      <div class="col-md-12">
        <div class="box box-warning">
          <div class="box-header">
            <h3 class="box-title">
              <i class="fa fa-line-chart"></i> Grafik Kunjungan per Bulan
            </h3>
          </div>
          <div class="box-body">
            <canvas id="chartKunjungan" width="400" height="100"></canvas>
          </div>
        </div>
      </div>
    </div>
    <?php endif; ?>

    <!-- Statistik per Petugas -->
    <?php if(mysqli_num_rows($result_petugas_stats) > 0): ?>
    <div class="row">
      <div class="col-md-12">
        <div class="box box-default">
          <div class="box-header">
            <h3 class="box-title">
              <i class="fa fa-users"></i> Statistik Kunjungan per Petugas BK
            </h3>
          </div>
          <div class="box-body">
            <div class="row">
              <?php 
              mysqli_data_seek($result_petugas_stats, 0);
              while($stat = mysqli_fetch_assoc($result_petugas_stats)): 
                $persentase = $total_kunjungan > 0 ? ($stat['jumlah'] / $total_kunjungan) * 100 : 0;
              ?>
              <div class="col-md-4">
                <div class="progress-group">
                  <span class="progress-text"><?php echo $stat['user_nama']; ?></span>
                  <span class="float-right"><b><?php echo $stat['jumlah']; ?></b>/<?php echo $total_kunjungan; ?></span>
                  <div class="progress progress-sm">
                    <div class="progress-bar progress-bar-primary" style="width: <?php echo $persentase; ?>%"></div>
                  </div>
                </div>
              </div>
              <?php endwhile; ?>
            </div>
          </div>
        </div>
      </div>
    </div>
    <?php endif; ?>
  </section>
</div>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
<?php if(mysqli_num_rows($result_bulan) > 0): ?>
// Grafik Kunjungan per Bulan
var ctx = document.getElementById('chartKunjungan').getContext('2d');
var chartData = {
    labels: [
        <?php 
        mysqli_data_seek($result_bulan, 0);
        $labels = [];
        while($bulan = mysqli_fetch_assoc($result_bulan)) {
            $labels[] = "'" . date('M Y', strtotime($bulan['bulan'] . '-01')) . "'";
        }
        echo implode(', ', $labels);
        ?>
    ],
    datasets: [{
        label: 'Jumlah Kunjungan',
        data: [
            <?php 
            mysqli_data_seek($result_bulan, 0);
            $data = [];
            while($bulan = mysqli_fetch_assoc($result_bulan)) {
                $data[] = $bulan['jumlah'];
            }
            echo implode(', ', $data);
            ?>
        ],
        backgroundColor: 'rgba(54, 162, 235, 0.2)',
        borderColor: 'rgba(54, 162, 235, 1)',
        borderWidth: 1
    }]
};

var myChart = new Chart(ctx, {
    type: 'line',
    data: chartData,
    options: {
        responsive: true,
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    stepSize: 1
                }
            }
        }
    }
});
<?php endif; ?>
</script>

<?php include 'footer.php'; ?>
