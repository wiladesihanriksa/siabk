<?php 
include 'header_dynamic.php';
?>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <section class="content-header">
    <h1>
      <i class="fa fa-chart-line"></i> Laporan Kasus Siswa
      <small>Analisis dan Statistik Konseling</small>
    </h1>
    <ol class="breadcrumb">
      <li><a href="index.php"><i class="fa fa-dashboard"></i> Home</a></li>
      <li><a href="#">Konseling BK</a></li>
      <li class="active">Laporan Kasus</li>
    </ol>
  </section>

  <!-- Main content -->
  <section class="content">
    <!-- CSS untuk memastikan sidebar tampil -->
    <style>
      .main-sidebar {
        display: block !important;
        position: fixed !important;
        top: 0 !important;
        left: 0 !important;
        width: 230px !important;
        height: 100% !important;
        z-index: 1000 !important;
      }
      
      .content-wrapper {
        margin-left: 230px !important;
      }
      
      @media (max-width: 767px) {
        .content-wrapper {
          margin-left: 0 !important;
        }
      }
    </style>
    
    <!-- Filter Laporan -->
    <div class="row">
      <div class="col-md-12">
        <div class="box box-primary">
          <div class="box-header with-border">
            <h3 class="box-title">
              <i class="fa fa-filter"></i> Filter Laporan
            </h3>
          </div>
          <div class="box-body">
            <form method="GET" action="">
              <div class="row">
                <div class="col-md-3">
                  <div class="form-group">
                    <label>Periode Awal:</label>
                    <input type="text" class="form-control datepicker" name="tanggal_awal" 
                           value="<?php echo isset($_GET['tanggal_awal']) ? $_GET['tanggal_awal'] : date('01/m/Y'); ?>" 
                           placeholder="dd/mm/yyyy">
                  </div>
                </div>
                <div class="col-md-3">
                  <div class="form-group">
                    <label>Periode Akhir:</label>
                    <input type="text" class="form-control datepicker" name="tanggal_akhir" 
                           value="<?php echo isset($_GET['tanggal_akhir']) ? $_GET['tanggal_akhir'] : date('d/m/Y'); ?>" 
                           placeholder="dd/mm/yyyy">
                  </div>
                </div>
                <div class="col-md-3">
                  <div class="form-group">
                    <label>Status:</label>
                    <select class="form-control" name="status">
                      <option value="">Semua Status</option>
                      <option value="Baru" <?php echo (isset($_GET['status']) && $_GET['status'] == 'Baru') ? 'selected' : ''; ?>>Baru</option>
                      <option value="Dalam Proses" <?php echo (isset($_GET['status']) && $_GET['status'] == 'Dalam Proses') ? 'selected' : ''; ?>>Dalam Proses</option>
                      <option value="Selesai" <?php echo (isset($_GET['status']) && $_GET['status'] == 'Selesai') ? 'selected' : ''; ?>>Selesai</option>
                      <option value="Dirujuk" <?php echo (isset($_GET['status']) && $_GET['status'] == 'Dirujuk') ? 'selected' : ''; ?>>Dirujuk</option>
                    </select>
                  </div>
                </div>
                <div class="col-md-3">
                  <div class="form-group">
                    <label>&nbsp;</label><br>
                    <button type="submit" class="btn btn-primary">
                      <i class="fa fa-search"></i> Filter
                    </button>
                    <a href="laporan_kasus.php" class="btn btn-default">
                      <i class="fa fa-refresh"></i> Reset
                    </a>
                  </div>
                </div>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>

    <!-- Statistik Ringkasan -->
    <div class="row">
      <div class="col-lg-3 col-xs-6">
        <div class="small-box bg-aqua">
          <div class="inner">
            <h3><?php
            $where_clause = "";
            if(isset($_GET['tanggal_awal']) && isset($_GET['tanggal_akhir'])) {
              $tanggal_awal = date('Y-m-d', strtotime(str_replace('/', '-', $_GET['tanggal_awal'])));
              $tanggal_akhir = date('Y-m-d', strtotime(str_replace('/', '-', $_GET['tanggal_akhir'])));
              $where_clause = "WHERE DATE(tanggal_pelaporan) BETWEEN '$tanggal_awal' AND '$tanggal_akhir'";
            }
            $query_total = "SELECT COUNT(*) as total FROM kasus_siswa k $where_clause";
            $result_total = mysqli_query($koneksi, $query_total);
            $total_kasus = mysqli_fetch_assoc($result_total)['total'];
            echo $total_kasus;
            ?></h3>
            <p>Total Kasus</p>
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
            $where_status = $where_clause;
            if(isset($_GET['status']) && $_GET['status'] != '') {
              $status = $_GET['status'];
              $where_status .= ($where_clause ? " AND" : "WHERE") . " status_kasus = '$status'";
            } else {
              $where_status .= ($where_clause ? " AND" : "WHERE") . " status_kasus = 'Selesai'";
            }
            $query_selesai = "SELECT COUNT(*) as total FROM kasus_siswa k $where_status";
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
            $query_proses = "SELECT COUNT(*) as total FROM kasus_siswa k " . 
                           ($where_clause ? $where_clause . " AND" : "WHERE") . " status_kasus = 'Dalam Proses'";
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
            $query_baru = "SELECT COUNT(*) as total FROM kasus_siswa k " . 
                         ($where_clause ? $where_clause . " AND" : "WHERE") . " status_kasus = 'Baru'";
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

    <!-- Grafik dan Tabel -->
    <div class="row">
      <!-- Grafik Kategori -->
      <div class="col-md-6">
        <div class="box box-primary">
          <div class="box-header with-border">
            <h3 class="box-title">Kasus Berdasarkan Kategori</h3>
          </div>
          <div class="box-body">
            <canvas id="chartKategori" width="400" height="200"></canvas>
          </div>
        </div>
      </div>
      
      <!-- Grafik Status -->
      <div class="col-md-6">
        <div class="box box-success">
          <div class="box-header with-border">
            <h3 class="box-title">Kasus Berdasarkan Status</h3>
          </div>
          <div class="box-body">
            <canvas id="chartStatus" width="400" height="200"></canvas>
          </div>
        </div>
      </div>
    </div>

    <!-- Tabel Detail Kasus -->
    <div class="row">
      <div class="col-md-12">
        <div class="box box-info">
          <div class="box-header with-border">
            <h3 class="box-title">Detail Kasus</h3>
            <div class="box-tools pull-right">
              <button class="btn btn-success btn-sm" onclick="exportToExcel()">
                <i class="fa fa-file-excel-o"></i> Export Excel
              </button>
            </div>
          </div>
          <div class="box-body">
            <div class="table-responsive">
              <table id="tabel_laporan" class="table table-bordered table-striped">
                <thead>
                  <tr>
                    <th>No</th>
                    <th>Kode Kasus</th>
                    <th>Nama Siswa</th>
                    <th>Kategori</th>
                    <th>Status</th>
                    <th>Tanggal Pelaporan</th>
                    <th>Guru BK</th>
                    <th>Aksi</th>
                  </tr>
                </thead>
                <tbody>
                  <?php
                  $query_detail = "SELECT k.*, s.siswa_nama, s.siswa_nis, g.nama_guru_bk
                                  FROM kasus_siswa k 
                                  LEFT JOIN siswa s ON k.siswa_id = s.siswa_id 
                                  LEFT JOIN guru_bk g ON k.guru_bk_id = g.user_id
                                  $where_clause 
                                  ORDER BY k.tanggal_pelaporan DESC";
                  $result_detail = mysqli_query($koneksi, $query_detail);
                  $no = 1;
                  while($row = mysqli_fetch_assoc($result_detail)) {
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
                        case 'Selesai': $status_class = 'label-success'; break;
                        case 'Dirujuk': $status_class = 'label-primary'; break;
                      }
                      ?>
                      <span class="label <?php echo $status_class; ?>"><?php echo $row['status_kasus']; ?></span>
                    </td>
                    <td><?php echo date('d/m/Y', strtotime($row['tanggal_pelaporan'])); ?></td>
                    <td><?php echo $row['nama_guru_bk']; ?></td>
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

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
<!-- XLSX for Excel export -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>

<script>
// Wait for jQuery to be loaded
function initCharts() {
    
    // Check if Chart.js is loaded
    if (typeof Chart === 'undefined') {
        return;
    }
    
    
    // Data untuk chart kategori
    <?php
    $query_kategori = "SELECT kategori_masalah, COUNT(*) as total FROM kasus_siswa k $where_clause GROUP BY kategori_masalah";
    $result_kategori = mysqli_query($koneksi, $query_kategori);
    $kategori_labels = array();
    $kategori_data = array();
    $kategori_colors = array();
    
    while($row = mysqli_fetch_assoc($result_kategori)) {
        $kategori_labels[] = $row['kategori_masalah'];
        $kategori_data[] = $row['total'];
        
        // Warna berdasarkan kategori
        switch($row['kategori_masalah']) {
            case 'Pribadi': $kategori_colors[] = '#d9534f'; break;
            case 'Sosial': $kategori_colors[] = '#5cb85c'; break;
            case 'Belajar': $kategori_colors[] = '#f0ad4e'; break;
            case 'Karir': $kategori_colors[] = '#337ab7'; break;
            default: $kategori_colors[] = '#777777';
        }
    }
    ?>
    
    
    // Check if data is empty
    if (<?php echo json_encode($kategori_labels); ?>.length === 0) {
        document.getElementById('chartKategori').innerHTML = '<div style="text-align: center; padding: 50px; color: #999;">Tidak ada data kategori</div>';
        return;
    }
    
    // Chart Kategori
    var ctxKategori = document.getElementById('chartKategori');
    if (!ctxKategori) {
        return;
    }
    
    try {
        var chartKategori = new Chart(ctxKategori.getContext('2d'), {
        type: 'doughnut',
        data: {
            labels: <?php echo json_encode($kategori_labels); ?>,
            datasets: [{
                data: <?php echo json_encode($kategori_data); ?>,
                backgroundColor: <?php echo json_encode($kategori_colors); ?>,
                borderWidth: 2,
                borderColor: '#fff'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        padding: 20,
                        usePointStyle: true
                    }
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            var label = context.label || '';
                            var value = context.parsed;
                            var total = context.dataset.data.reduce((a, b) => a + b, 0);
                            var percentage = ((value / total) * 100).toFixed(1);
                            return label + ': ' + value + ' kasus (' + percentage + '%)';
                        }
                    }
                }
            }
        }
    });
    } catch (error) {
    }
    
    // Data untuk chart status
    <?php
    $query_status = "SELECT status_kasus, COUNT(*) as total FROM kasus_siswa k $where_clause GROUP BY status_kasus";
    $result_status = mysqli_query($koneksi, $query_status);
    $status_labels = array();
    $status_data = array();
    $status_colors = array();
    
    while($row = mysqli_fetch_assoc($result_status)) {
        $status_labels[] = $row['status_kasus'];
        $status_data[] = $row['total'];
        
        // Warna berdasarkan status
        switch($row['status_kasus']) {
            case 'Baru': $status_colors[] = '#d9534f'; break;
            case 'Dalam Proses': $status_colors[] = '#f0ad4e'; break;
            case 'Selesai/Tuntas': $status_colors[] = '#5cb85c'; break;
            case 'Dirujuk/Alih Tangan Kasus': $status_colors[] = '#337ab7'; break;
            default: $status_colors[] = '#777777';
        }
    }
    ?>
    
    
    // Check if status data is empty
    if (<?php echo json_encode($status_labels); ?>.length === 0) {
        document.getElementById('chartStatus').innerHTML = '<div style="text-align: center; padding: 50px; color: #999;">Tidak ada data status</div>';
        return;
    }
    
    // Chart Status
    var ctxStatus = document.getElementById('chartStatus');
    if (!ctxStatus) {
        return;
    }
    
    try {
        var chartStatus = new Chart(ctxStatus.getContext('2d'), {
        type: 'bar',
        data: {
            labels: <?php echo json_encode($status_labels); ?>,
            datasets: [{
                label: 'Jumlah Kasus',
                data: <?php echo json_encode($status_data); ?>,
                backgroundColor: <?php echo json_encode($status_colors); ?>,
                borderColor: <?php echo json_encode($status_colors); ?>,
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return context.label + ': ' + context.parsed.y + ' kasus';
                        }
                    }
                }
            },
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
    } catch (error) {
    }
    
    // Export to Excel function
    window.exportToExcel = function() {
        var table = document.getElementById('tabel_laporan');
        var wb = XLSX.utils.table_to_book(table, {sheet: "Laporan Kasus"});
        XLSX.writeFile(wb, "laporan_kasus_" + new Date().toISOString().slice(0,10) + ".xlsx");
    };
}

// Initialize charts when DOM is ready
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initCharts);
} else {
    initCharts();
}
</script>

<?php include 'footer.php'; ?>