<?php 
include 'header_dynamic.php';
include 'alert_helper.php';
?>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <section class="content-header">
    <h1>
      <i class="fa fa-home"></i> Catatan Kunjungan Rumah
      <small>Manajemen Home Visit</small>
    </h1>
    <ol class="breadcrumb">
      <li><a href="index.php"><i class="fa fa-dashboard"></i> Home</a></li>
      <li><a href="#">Konseling BK</a></li>
      <li class="active">Kunjungan Rumah</li>
    </ol>
  </section>

  <!-- Main content -->
  <section class="content">
    <?php showAlert(); ?>
    
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
    
    <div class="row">
      <div class="col-xs-12">
        <div class="box">
          <div class="box-header">
            <h3 class="box-title">
              <i class="fa fa-list"></i> Daftar Kunjungan Rumah
            </h3>
            <div class="box-tools pull-right">
              <a href="kunjungan_rumah_tambah.php" class="btn btn-primary btn-sm">
                <i class="fa fa-plus"></i> Tambah Kunjungan Baru
              </a>
            </div>
          </div>
          
          <!-- Filter dan Pencarian -->
          <div class="box-body">
            <div class="row">
              <div class="col-md-3">
                <div class="form-group">
                  <label>Cari Siswa:</label>
                  <input type="text" id="search_siswa" class="form-control" placeholder="Nama siswa...">
                </div>
              </div>
              <div class="col-md-2">
                <div class="form-group">
                  <label>Petugas BK:</label>
                  <select id="filter_petugas" class="form-control">
                    <option value="">Semua Petugas</option>
                    <?php
                    $query_petugas = "SELECT * FROM user WHERE user_level = 'administrator' OR user_level = 'guru_bk' ORDER BY user_nama";
                    $result_petugas = mysqli_query($koneksi, $query_petugas);
                    while($petugas = mysqli_fetch_assoc($result_petugas)) {
                      echo "<option value='" . $petugas['user_nama'] . "'>" . $petugas['user_nama'] . "</option>";
                    }
                    ?>
                  </select>
                </div>
              </div>
              <div class="col-md-3">
                <div class="form-group">
                  <label>Tanggal:</label>
                  <div class="input-group">
                    <input type="date" id="filter_tanggal_awal" class="form-control">
                    <span class="input-group-addon">s/d</span>
                    <input type="date" id="filter_tanggal_akhir" class="form-control">
                  </div>
                </div>
              </div>
              <div class="col-md-2">
                <div class="form-group">
                  <label>&nbsp;</label>
                  <button type="button" id="btn_filter" class="btn btn-info btn-block">
                    <i class="fa fa-search"></i> Filter
                  </button>
                </div>
              </div>
              <div class="col-md-2">
                <div class="form-group">
                  <label>&nbsp;</label>
                  <button type="button" id="btn_reset" class="btn btn-default btn-block">
                    <i class="fa fa-refresh"></i> Reset
                  </button>
                </div>
              </div>
            </div>
          </div>

          <!-- Tabel Data -->
          <div class="box-body">
            <div class="table-responsive">
              <table id="tabel_kunjungan" class="table table-bordered table-striped">
                <thead>
                  <tr>
                    <th width="5%">No</th>
                    <th width="10%">Kode Kunjungan</th>
                    <th width="15%">Nama Siswa</th>
                    <th width="10%">Tanggal</th>
                    <th width="10%">Waktu</th>
                    <th width="20%">Tujuan Kunjungan</th>
                    <th width="10%">Petugas BK</th>
                    <th width="10%">Alamat</th>
                    <th width="10%">Aksi</th>
                  </tr>
                </thead>
                <tbody>
                  <?php
                  $no = 1;
                  $query = "SELECT k.*, s.siswa_nama, u.user_nama as petugas_nama 
                           FROM kunjungan_rumah k 
                           LEFT JOIN siswa s ON k.siswa_id = s.siswa_id 
                           LEFT JOIN user u ON k.petugas_bk_id = u.user_id 
                           ORDER BY k.created_at DESC";
                  $result = mysqli_query($koneksi, $query);
                  
                  if(mysqli_num_rows($result) > 0) {
                    while($data = mysqli_fetch_assoc($result)) {
                  ?>
                  <tr>
                    <td><?php echo $no++; ?></td>
                    <td><strong><?php echo $data['kunjungan_kode']; ?></strong></td>
                    <td><?php echo $data['siswa_nama']; ?></td>
                    <td><?php echo date('d/m/Y', strtotime($data['tanggal_kunjungan'])); ?></td>
                    <td><?php echo date('H:i', strtotime($data['waktu_kunjungan'])); ?></td>
                    <td><?php echo substr($data['tujuan_kunjungan'], 0, 50) . '...'; ?></td>
                    <td><?php echo $data['petugas_nama']; ?></td>
                    <td><?php echo substr($data['alamat_kunjungan'], 0, 30) . '...'; ?></td>
                    <td>
                      <div class="btn-group">
                        <a href="kunjungan_rumah_detail.php?id=<?php echo $data['kunjungan_id']; ?>" 
                           class="btn btn-info btn-xs" title="Detail">
                          <i class="fa fa-eye"></i>
                        </a>
                        <a href="kunjungan_rumah_edit.php?id=<?php echo $data['kunjungan_id']; ?>" 
                           class="btn btn-warning btn-xs" title="Edit">
                          <i class="fa fa-edit"></i>
                        </a>
                        <a href="kunjungan_rumah_cetak.php?id=<?php echo $data['kunjungan_id']; ?>" 
                           class="btn btn-success btn-xs" title="Cetak Laporan" target="_blank">
                          <i class="fa fa-print"></i>
                        </a>
                        <a href="kunjungan_rumah_hapus.php?id=<?php echo $data['kunjungan_id']; ?>" 
                           class="btn btn-danger btn-xs" 
                           onclick="return confirm('Yakin ingin menghapus kunjungan ini?')" title="Hapus">
                          <i class="fa fa-trash"></i>
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
                      <i class="fa fa-info-circle"></i> Belum ada data kunjungan rumah
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

<!-- Script untuk DataTable dan Filter -->
<script>
$(document).ready(function() {
    // Pastikan sidebar tampil
    $('.main-sidebar').show();
    $('.content-wrapper').css('margin-left', '230px');
    
    // Inisialisasi sidebar menu
    $('.sidebar-menu').tree();
    
    // Inisialisasi DataTable
    var table = $('#tabel_kunjungan').DataTable({
        "responsive": true,
        "lengthChange": false,
        "autoWidth": false,
        "pageLength": 25,
        "language": {
            "url": "../assets/bower_components/datatables.net-bs/js/Indonesian.json"
        }
    });

    // Filter berdasarkan pencarian
    $('#search_siswa').on('keyup', function() {
        table.column(2).search(this.value).draw();
    });

    // Filter berdasarkan petugas
    $('#filter_petugas').on('change', function() {
        table.column(6).search(this.value).draw();
    });

    // Filter berdasarkan tanggal
    $('#btn_filter').on('click', function() {
        var tanggal_awal = $('#filter_tanggal_awal').val();
        var tanggal_akhir = $('#filter_tanggal_akhir').val();
        
        if(tanggal_awal && tanggal_akhir) {
            // Implementasi filter tanggal jika diperlukan
        }
    });

    // Reset filter
    $('#btn_reset').on('click', function() {
        $('#search_siswa').val('');
        $('#filter_petugas').val('');
        $('#filter_tanggal_awal').val('');
        $('#filter_tanggal_akhir').val('');
        table.search('').columns().search('').draw();
    });
});
</script>

<?php include 'footer.php'; ?>
