<?php 
include 'header_dynamic.php';
include 'alert_helper.php';
?>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <section class="content-header">
    <h1>
      <i class="fa fa-folder-open"></i> Data Kasus Siswa
      <small>Manajemen Konseling Individu</small>
    </h1>
    <ol class="breadcrumb">
      <li><a href="index.php"><i class="fa fa-dashboard"></i> Home</a></li>
      <li><a href="#">Konseling BK</a></li>
      <li class="active">Data Kasus Siswa</li>
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
              <i class="fa fa-list"></i> Daftar Kasus Siswa
            </h3>
            <div class="box-tools pull-right">
              <a href="kasus_siswa_tambah.php" class="btn btn-primary btn-sm">
                <i class="fa fa-plus"></i> Tambah Kasus Baru
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
                  <label>Status Kasus:</label>
                  <select id="filter_status" class="form-control">
                    <option value="">Semua Status</option>
                    <option value="Baru">Baru</option>
                    <option value="Dalam Proses">Dalam Proses</option>
                    <option value="Selesai/Tuntas">Selesai/Tuntas</option>
                    <option value="Dirujuk/Alih Tangan Kasus">Dirujuk/Alih Tangan Kasus</option>
                  </select>
                </div>
              </div>
              <div class="col-md-2">
                <div class="form-group">
                  <label>Kategori:</label>
                  <select id="filter_kategori" class="form-control">
                    <option value="">Semua Kategori</option>
                    <option value="Pribadi">Pribadi</option>
                    <option value="Sosial">Sosial</option>
                    <option value="Belajar">Belajar</option>
                    <option value="Karir">Karir</option>
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
            </div>
          </div>

          <!-- Tabel Data -->
          <div class="box-body">
            <div class="table-responsive">
              <table id="tabel_kasus" class="table table-bordered table-striped">
                <thead>
                  <tr>
                    <th width="5%">No</th>
                    <th width="10%">Kode Kasus</th>
                    <th width="15%">Nama Siswa</th>
                    <th width="20%">Judul Kasus</th>
                    <th width="10%">Kategori</th>
                    <th width="10%">Status</th>
                    <th width="10%">Tanggal</th>
                    <th width="10%">Guru BK</th>
                    <th width="10%">Aksi</th>
                  </tr>
                </thead>
                <tbody>
                  <?php
                  $no = 1;
                  $query = "SELECT k.*, s.siswa_nama, u.user_nama as guru_bk_nama 
                           FROM kasus_siswa k 
                           LEFT JOIN siswa s ON k.siswa_id = s.siswa_id 
                           LEFT JOIN user u ON k.guru_bk_id = u.user_id 
                           ORDER BY k.created_at DESC";
                  $result = mysqli_query($koneksi, $query);
                  
                  if(mysqli_num_rows($result) > 0) {
                    while($data = mysqli_fetch_assoc($result)) {
                      $status_class = '';
                      switch($data['status_kasus']) {
                        case 'Baru': $status_class = 'label-warning'; break;
                        case 'Dalam Proses': $status_class = 'label-info'; break;
                        case 'Selesai/Tuntas': $status_class = 'label-success'; break;
                        case 'Dirujuk/Alih Tangan Kasus': $status_class = 'label-danger'; break;
                      }
                  ?>
                  <tr>
                    <td><?php echo $no++; ?></td>
                    <td><strong><?php echo $data['kasus_kode']; ?></strong></td>
                    <td><?php echo $data['siswa_nama']; ?></td>
                    <td><?php echo $data['judul_kasus']; ?></td>
                    <td><?php echo $data['kategori_masalah']; ?></td>
                    <td>
                      <span class="label <?php echo $status_class; ?>">
                        <?php echo $data['status_kasus']; ?>
                      </span>
                    </td>
                    <td><?php echo date('d/m/Y', strtotime($data['tanggal_pelaporan'])); ?></td>
                    <td><?php echo $data['guru_bk_nama']; ?></td>
                    <td>
                      <div class="btn-group">
                        <a href="kasus_siswa_detail.php?id=<?php echo $data['kasus_id']; ?>" 
                           class="btn btn-info btn-xs" title="Detail">
                          <i class="fa fa-eye"></i>
                        </a>
                        <a href="kasus_siswa_edit.php?id=<?php echo $data['kasus_id']; ?>" 
                           class="btn btn-warning btn-xs" title="Edit">
                          <i class="fa fa-edit"></i>
                        </a>
                        <a href="kasus_siswa_hapus.php?id=<?php echo $data['kasus_id']; ?>" 
                           class="btn btn-danger btn-xs" 
                           onclick="return confirm('Yakin ingin menghapus kasus ini?')" title="Hapus">
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
                      <i class="fa fa-info-circle"></i> Belum ada data kasus siswa
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
function initKasusSiswa() {
    // Pastikan sidebar tampil
    $('.main-sidebar').show();
    $('.content-wrapper').css('margin-left', '230px');
    
    // Inisialisasi sidebar menu
    $('.sidebar-menu').tree();
    
    // Inisialisasi DataTable
    var table = $('#tabel_kasus').DataTable({
        "responsive": true,
        "lengthChange": false,
        "autoWidth": false,
        "pageLength": 25,
        "language": {
            "lengthMenu": "Tampilkan _MENU_ data per halaman",
            "zeroRecords": "Data tidak ditemukan",
            "info": "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
            "infoEmpty": "Menampilkan 0 sampai 0 dari 0 data",
            "infoFiltered": "(disaring dari _MAX_ total data)",
            "search": "Cari:",
            "paginate": {
                "first": "Pertama",
                "last": "Terakhir",
                "next": "Selanjutnya",
                "previous": "Sebelumnya"
            }
        }
    });

    // Filter berdasarkan pencarian
    $('#search_siswa').on('keyup', function() {
        table.column(2).search(this.value).draw();
    });

    // Filter berdasarkan status
    $('#filter_status').on('change', function() {
        table.column(5).search(this.value).draw();
    });

    // Filter berdasarkan kategori
    $('#filter_kategori').on('change', function() {
        table.column(4).search(this.value).draw();
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
        $('#filter_status').val('');
        $('#filter_kategori').val('');
        $('#filter_tanggal_awal').val('');
        $('#filter_tanggal_akhir').val('');
        table.search('').columns().search('').draw();
    });
}

// Initialize when DOM is ready
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initKasusSiswa);
} else {
    initKasusSiswa();
}
</script>

<?php include 'footer.php'; ?>
