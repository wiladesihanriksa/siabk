<?php include 'header.php'; ?>

<div class="content-wrapper">
  <section class="content-header">
    <h1>
      Laporan Layanan BK
      <small>Filter dan Cetak Laporan Layanan Bimbingan dan Konseling</small>
    </h1>
    <ol class="breadcrumb">
      <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
      <li><a href="layanan_bk.php">Data Layanan BK</a></li>
      <li class="active">Laporan</li>
    </ol>
  </section>

  <section class="content">
    <div class="row">
      <div class="col-md-12">
        <div class="box box-primary">
          <div class="box-header with-border">
            <h3 class="box-title">
              <i class="fa fa-filter"></i> Filter Laporan
            </h3>
          </div>
          
          <form method="GET" action="" id="filterForm">
            <div class="box-body">
              <div class="row">
                <div class="col-md-3">
                  <div class="form-group">
                    <label>Tanggal Awal:</label>
                    <input type="date" name="tanggal_awal" class="form-control" 
                           value="<?php echo isset($_GET['tanggal_awal']) ? $_GET['tanggal_awal'] : date('Y-m-01'); ?>">
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
                <div class="col-md-3">
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
              </div>
              
              <div class="row">
                <div class="col-md-3">
                  <div class="form-group">
                    <label>Kelas:</label>
                    <select name="kelas_id" class="form-control">
                      <option value="">Semua Kelas</option>
                      <?php
                      $kelas = mysqli_query($koneksi,"SELECT * FROM kelas ORDER BY kelas_nama");
                      while($k = mysqli_fetch_assoc($kelas)) {
                        $selected = (isset($_GET['kelas_id']) && $_GET['kelas_id'] == $k['kelas_id']) ? 'selected' : '';
                        echo "<option value='".$k['kelas_id']."' $selected>".$k['kelas_nama']."</option>";
                      }
                      ?>
                    </select>
                  </div>
                </div>
                <div class="col-md-3">
                  <div class="form-group">
                    <label>Tampilkan:</label>
                    <select name="format" class="form-control">
                      <option value="tabel" <?php echo (!isset($_GET['format']) || $_GET['format'] == 'tabel') ? 'selected' : ''; ?>>Tabel</option>
                      <option value="pdf" <?php echo (isset($_GET['format']) && $_GET['format'] == 'pdf') ? 'selected' : ''; ?>>PDF</option>
                      <option value="excel" <?php echo (isset($_GET['format']) && $_GET['format'] == 'excel') ? 'selected' : ''; ?>>Excel</option>
                    </select>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group">
                    <label>&nbsp;</label>
                    <div>
                      <button type="submit" class="btn btn-info">
                        <i class="fa fa-search"></i> Tampilkan Laporan
                      </button>
                      <button type="button" class="btn btn-success" onclick="cetakLaporan()">
                        <i class="fa fa-print"></i> Cetak PDF
                      </button>
                      <button type="button" class="btn btn-warning" onclick="exportExcel()">
                        <i class="fa fa-file-excel-o"></i> Export Excel
                      </button>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>

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
    
    if(isset($_GET['kelas_id']) && !empty($_GET['kelas_id'])) {
        $where_conditions[] = "l.kelas_id = ?";
        $params[] = $_GET['kelas_id'];
    }
    
    $where_clause = !empty($where_conditions) ? "WHERE " . implode(" AND ", $where_conditions) : "";
    
    // Get data for report
    $query = "SELECT l.*, k.kelas_nama, u.user_nama 
              FROM layanan_bk l 
              LEFT JOIN kelas k ON l.kelas_id = k.kelas_id 
              LEFT JOIN user u ON l.created_by = u.user_id 
              $where_clause 
              ORDER BY l.tanggal_pelaksanaan DESC";
    
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

    // Get statistics
    $stats_query = "SELECT 
                      COUNT(*) as total_layanan,
                      SUM(jumlah_peserta) as total_peserta,
                      COUNT(DISTINCT jenis_layanan) as jenis_layanan_berbeda,
                      COUNT(DISTINCT bidang_layanan) as bidang_layanan_berbeda
                    FROM layanan_bk l 
                    $where_clause";
    
    if(!empty($params)) {
        $stats_stmt = mysqli_prepare($koneksi, $stats_query);
        if($stats_stmt) {
            $types = str_repeat('s', count($params));
            mysqli_stmt_bind_param($stats_stmt, $types, ...$params);
            mysqli_stmt_execute($stats_stmt);
            $stats_result = mysqli_stmt_get_result($stats_stmt);
        }
    } else {
        $stats_result = mysqli_query($koneksi, $stats_query);
    }
    $stats = mysqli_fetch_assoc($stats_result);
    ?>

    <!-- Statistics Cards -->
    <div class="row">
      <div class="col-lg-3 col-xs-6">
        <div class="small-box bg-aqua">
          <div class="inner">
            <h3><?php echo $stats['total_layanan']; ?></h3>
            <p>Total Layanan</p>
          </div>
          <div class="icon">
            <i class="fa fa-heart"></i>
          </div>
        </div>
      </div>

      <div class="col-lg-3 col-xs-6">
        <div class="small-box bg-green">
          <div class="inner">
            <h3><?php echo $stats['total_peserta'] ? $stats['total_peserta'] : 0; ?></h3>
            <p>Total Peserta</p>
          </div>
          <div class="icon">
            <i class="fa fa-users"></i>
          </div>
        </div>
      </div>

      <div class="col-lg-3 col-xs-6">
        <div class="small-box bg-yellow">
          <div class="inner">
            <h3><?php echo $stats['jenis_layanan_berbeda']; ?></h3>
            <p>Jenis Layanan</p>
          </div>
          <div class="icon">
            <i class="fa fa-list"></i>
          </div>
        </div>
      </div>

      <div class="col-lg-3 col-xs-6">
        <div class="small-box bg-red">
          <div class="inner">
            <h3><?php echo $stats['bidang_layanan_berbeda']; ?></h3>
            <p>Bidang Layanan</p>
          </div>
          <div class="icon">
            <i class="fa fa-tags"></i>
          </div>
        </div>
      </div>
    </div>

    <!-- Report Table -->
    <?php if(mysqli_num_rows($result) > 0): ?>
    <div class="row">
      <div class="col-xs-12">
        <div class="box">
          <div class="box-header">
            <h3 class="box-title">Data Laporan Layanan BK</h3>
          </div>
          
          <div class="box-body">
            <div class="table-responsive">
              <table id="reportTable" class="table table-bordered table-striped">
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
                    <th>Dibuat Oleh</th>
                  </tr>
                </thead>
                <tbody>
                  <?php
                  $no = 1;
                  while($data = mysqli_fetch_assoc($result)) {
                  ?>
                  <tr>
                    <td><?php echo $no++; ?></td>
                    <td><?php echo date('d/m/Y', strtotime($data['tanggal_pelaksanaan'])); ?></td>
                    <td>
                      <span class="label label-info"><?php echo $data['jenis_layanan']; ?></span>
                    </td>
                    <td><?php echo $data['topik_materi']; ?></td>
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
                    <td><?php echo $data['sasaran_layanan']; ?></td>
                    <td><?php echo $data['kelas_nama'] ? $data['kelas_nama'] : '-'; ?></td>
                    <td>
                      <span class="badge bg-blue"><?php echo $data['jumlah_peserta']; ?> orang</span>
                    </td>
                    <td><?php echo $data['user_nama']; ?></td>
                  </tr>
                  <?php } ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
    <?php else: ?>
    <div class="row">
      <div class="col-xs-12">
        <div class="alert alert-info">
          <h4><i class="fa fa-info"></i> Tidak Ada Data</h4>
          <p>Tidak ditemukan data layanan BK sesuai dengan filter yang dipilih.</p>
        </div>
      </div>
    </div>
    <?php endif; ?>
  </section>
</div>

<script>
function cetakLaporan() {
    var params = new URLSearchParams(window.location.search);
    params.set('format', 'pdf');
    window.open('layanan_bk_cetak_pdf.php?' + params.toString(), '_blank');
}

function exportExcel() {
    var params = new URLSearchParams(window.location.search);
    params.set('format', 'excel');
    window.location.href = 'layanan_bk_export_excel.php?' + params.toString();
}

$(document).ready(function() {
    $('#reportTable').DataTable({
        "responsive": true,
        "lengthChange": false,
        "autoWidth": false,
        "buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"]
    }).buttons().container().appendTo('#reportTable_wrapper .col-md-6:eq(0)');
});
</script>

<?php include 'footer.php'; ?>
