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
            <h3 class="box-title">Siswa</h3>
            <div class="btn-group pull-right">
              <a href="siswa_tambah.php" class="btn btn-primary btn-sm"><i class="fa fa-plus"></i> &nbsp Tambah siswa</a>
              <button type="button" class="btn btn-info btn-sm" id="resetAllPasswords"><i class="fa fa-key"></i> &nbsp Reset Password Semua</button>
            </div>
          </div>
          
          <!-- Filter Tahun Ajaran dan Search -->
          <div class="box-body">
            <form method="GET" action="">
              <div class="row">
                <div class="col-md-3">
                  <div class="form-group">
                    <label>Tahun Ajaran:</label>
                    <select name="ta_filter" class="form-control" id="ta_filter">
                      <option value="">-- Semua Tahun Ajaran --</option>
                      <?php
                      $ta_query = mysqli_query($koneksi, "SELECT * FROM ta ORDER BY ta_id DESC");
                      while($ta = mysqli_fetch_array($ta_query)){
                        $selected = (isset($_GET['ta_filter']) && $_GET['ta_filter'] == $ta['ta_id']) ? 'selected' : '';
                        echo "<option value='".$ta['ta_id']."' $selected>".$ta['ta_nama']."</option>";
                      }
                      ?>
                    </select>
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="form-group">
                    <label>Cari Siswa:</label>
                    <input type="text" name="search" class="form-control" placeholder="Cari berdasarkan Nama, NIS, Jurusan, atau Kelas..." value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
                  </div>
                </div>
                <div class="col-md-5">
                  <div class="form-group">
                    <label>&nbsp;</label><br>
                    <button type="submit" class="btn btn-primary btn-sm"><i class="fa fa-search"></i> Cari</button>
                    <a href="siswa.php" class="btn btn-default btn-sm"><i class="fa fa-refresh"></i> Reset Filter</a>
                    <input type="hidden" name="page" value="1">
                  </div>
                </div>
              </div>
            </form>
          </div>
          <div class="box-body">


            <?php 
            // Konfigurasi pagination
            $per_page = 20; // Jumlah data per halaman
            $current_page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
            if($current_page < 1) $current_page = 1;
            $offset = ($current_page - 1) * $per_page;
            
            // Ambil parameter search
            $search_term = isset($_GET['search']) ? trim($_GET['search']) : '';
            $search_condition = '';
            
            if(!empty($search_term)){
              $search_escaped = mysqli_real_escape_string($koneksi, $search_term);
              $search_condition = " AND (
                s.siswa_nama LIKE '%$search_escaped%' OR 
                s.siswa_nis LIKE '%$search_escaped%' OR 
                j.jurusan_nama LIKE '%$search_escaped%' OR 
                COALESCE(k.kelas_nama, '') LIKE '%$search_escaped%'
              )";
            }
            
            // Hitung total data - konsisten dengan query utama
            if(isset($_GET['ta_filter']) && !empty($_GET['ta_filter'])){
              $ta_filter = mysqli_real_escape_string($koneksi, $_GET['ta_filter']);
              // Jika filter tahun ajaran dipilih, hanya tampilkan siswa yang punya kelas di tahun ajaran tersebut
              $count_query = "SELECT COUNT(DISTINCT s.siswa_id) as total 
                             FROM siswa s 
                             JOIN jurusan j ON s.siswa_jurusan = j.jurusan_id
                             JOIN kelas_siswa ks ON s.siswa_id = ks.ks_siswa 
                             JOIN kelas k ON ks.ks_kelas = k.kelas_id 
                             WHERE k.kelas_ta = '$ta_filter' AND s.siswa_status = 'aktif' $search_condition";
              $ta_selected = mysqli_fetch_array(mysqli_query($koneksi, "SELECT * FROM ta WHERE ta_id = '$ta_filter'"));
            } else {
              // Hitung semua siswa aktif, termasuk yang belum punya kelas
              $count_query = "SELECT COUNT(DISTINCT s.siswa_id) as total 
                             FROM siswa s 
                             JOIN jurusan j ON s.siswa_jurusan = j.jurusan_id
                             LEFT JOIN kelas_siswa ks ON s.siswa_id = ks.ks_siswa 
                             LEFT JOIN kelas k ON ks.ks_kelas = k.kelas_id 
                             WHERE s.siswa_status = 'aktif' $search_condition";
            }
            $count_result = mysqli_fetch_array(mysqli_query($koneksi, $count_query));
            $total_data = $count_result['total'];
            
            // Hitung total halaman
            $total_pages = ceil($total_data / $per_page);
            if($current_page > $total_pages && $total_pages > 0) $current_page = $total_pages;
            
            // Hitung range data yang ditampilkan
            $start_data = $total_data > 0 ? $offset + 1 : 0;
            $end_data = min($offset + $per_page, $total_data);
            ?>
            
            <div class="alert alert-info">
              <i class="fa fa-info-circle"></i> 
              <?php if(isset($_GET['ta_filter']) && !empty($_GET['ta_filter'])): ?>
                Menampilkan <strong><?php echo $start_data; ?>-<?php echo $end_data; ?></strong> dari <strong><?php echo $total_data; ?></strong> siswa untuk tahun ajaran: <strong><?php echo $ta_selected['ta_nama']; ?></strong>
              <?php else: ?>
                Menampilkan <strong><?php echo $start_data; ?>-<?php echo $end_data; ?></strong> dari <strong><?php echo $total_data; ?></strong> siswa (semua tahun ajaran)
              <?php endif; ?>
              <?php if(!empty($search_term)): ?>
                <br>Hasil pencarian untuk: <strong>"<?php echo htmlspecialchars($search_term); ?>"</strong>
              <?php endif; ?>
            </div>
            
            <div class="table-responsive">
              <table class="table table-bordered table-striped" id="table-datatable">
                <thead>
                  <tr>
                    <th width="1%">NO</th>
                    <th>NAMA</th>
                    <th>NIS</th>
                    <th>JURUSAN</th>
                    <th>KELAS</th>
                    <th>STATUS</th>
                    <th width="25%">OPSI</th>
                  </tr>
                </thead>
                <tbody>
                  <?php 
                  // Query dengan filter tahun ajaran
                  if(isset($_GET['ta_filter']) && !empty($_GET['ta_filter'])){
                    $ta_filter = mysqli_real_escape_string($koneksi, $_GET['ta_filter']);
                    // Jika filter tahun ajaran dipilih, hanya tampilkan siswa yang punya kelas di tahun ajaran tersebut
                    $query = "SELECT DISTINCT s.*, j.jurusan_nama, k.kelas_nama 
                             FROM siswa s 
                             JOIN jurusan j ON s.siswa_jurusan = j.jurusan_id 
                             JOIN kelas_siswa ks ON s.siswa_id = ks.ks_siswa 
                             JOIN kelas k ON ks.ks_kelas = k.kelas_id 
                             WHERE k.kelas_ta = '$ta_filter' AND s.siswa_status = 'aktif' $search_condition
                             ORDER BY s.siswa_id DESC
                             LIMIT $per_page OFFSET $offset";
                  } else {
                    // Tampilkan semua siswa aktif, termasuk yang belum punya kelas
                    $query = "SELECT DISTINCT s.*, j.jurusan_nama, k.kelas_nama 
                             FROM siswa s 
                             JOIN jurusan j ON s.siswa_jurusan = j.jurusan_id 
                             LEFT JOIN kelas_siswa ks ON s.siswa_id = ks.ks_siswa 
                             LEFT JOIN kelas k ON ks.ks_kelas = k.kelas_id 
                             WHERE s.siswa_status = 'aktif' $search_condition
                             ORDER BY s.siswa_id DESC
                             LIMIT $per_page OFFSET $offset";
                  }
                  $data = mysqli_query($koneksi, $query);
                  
                  // Reset nomor urut berdasarkan halaman
                  $no = $start_data;
                  
                  if(mysqli_num_rows($data) > 0){
                    while($d = mysqli_fetch_array($data)){
                    ?>
                    <tr>
                      <td><?php echo $no++; ?></td>
                      <td><?php echo $d['siswa_nama']; ?></td>
                      <td><?php echo $d['siswa_nis']; ?></td>
                      <td><?php echo $d['jurusan_nama']; ?></td>
                      <td>
                        <?php 
                        if(!empty($d['kelas_nama'])){
                          echo $d['kelas_nama'];
                        } else {
                          echo '<span class="text-muted">-</span>';
                        }
                        ?>
                      </td>
                      <td><?php echo $d['siswa_status']; ?></td>
                      <td>                        
                        <a class="btn btn-success btn-sm" href="siswa_riwayat.php?id=<?php echo $d['siswa_id'] ?>"><i class="fa fa-list"></i> Riwayat</a>
                        <button class="btn btn-info btn-sm reset-password-btn" data-siswa-id="<?php echo $d['siswa_id']; ?>" data-siswa-nama="<?php echo $d['siswa_nama']; ?>" data-siswa-nis="<?php echo $d['siswa_nis']; ?>"><i class="fa fa-key"></i> Reset</button>
                        <a class="btn btn-warning btn-sm" href="siswa_edit.php?id=<?php echo $d['siswa_id'] ?>"><i class="fa fa-cog"></i></a>
                        <a class="btn btn-danger btn-sm" href="siswa_hapus_konfir.php?id=<?php echo $d['siswa_id'] ?>"><i class="fa fa-trash"></i></a>
                      </td>
                    </tr>
                    <?php 
                    }
                  } else {
                    ?>
                    <tr>
                      <td colspan="7" class="text-center">
                        <div class="alert alert-warning" style="margin: 20px 0;">
                          <i class="fa fa-info-circle"></i> Tidak ada data siswa yang ditemukan.
                        </div>
                      </td>
                    </tr>
                    <?php
                  }
                  ?>
                </tbody>
              </table>
            </div>
            
            <?php if($total_pages > 1): ?>
            <!-- Pagination -->
            <div class="box-footer clearfix">
              <ul class="pagination pagination-sm no-margin pull-right">
                <?php
                // Build query string untuk pagination (preserve filter dan search)
                $query_params = array();
                if(isset($_GET['ta_filter']) && !empty($_GET['ta_filter'])){
                  $query_params['ta_filter'] = $_GET['ta_filter'];
                }
                if(!empty($search_term)){
                  $query_params['search'] = $search_term;
                }
                $query_string = !empty($query_params) ? '&' . http_build_query($query_params) : '';
                
                // Tombol Previous
                if($current_page > 1):
                  $prev_page = $current_page - 1;
                ?>
                <li><a href="?page=<?php echo $prev_page; ?><?php echo $query_string; ?>">&laquo;</a></li>
                <?php else: ?>
                <li class="disabled"><span>&laquo;</span></li>
                <?php endif; ?>
                
                <?php
                // Tampilkan nomor halaman
                $start_page = max(1, $current_page - 2);
                $end_page = min($total_pages, $current_page + 2);
                
                // Tampilkan halaman pertama jika tidak dalam range
                if($start_page > 1):
                ?>
                <li><a href="?page=1<?php echo $query_string; ?>">1</a></li>
                <?php if($start_page > 2): ?>
                <li class="disabled"><span>...</span></li>
                <?php endif; ?>
                <?php endif; ?>
                
                <?php
                // Tampilkan halaman dalam range
                for($i = $start_page; $i <= $end_page; $i++):
                  if($i == $current_page):
                ?>
                <li class="active"><span><?php echo $i; ?></span></li>
                <?php else: ?>
                <li><a href="?page=<?php echo $i; ?><?php echo $query_string; ?>"><?php echo $i; ?></a></li>
                <?php
                  endif;
                endfor;
                ?>
                
                <?php
                // Tampilkan halaman terakhir jika tidak dalam range
                if($end_page < $total_pages):
                  if($end_page < $total_pages - 1):
                ?>
                <li class="disabled"><span>...</span></li>
                <?php endif; ?>
                <li><a href="?page=<?php echo $total_pages; ?><?php echo $query_string; ?>"><?php echo $total_pages; ?></a></li>
                <?php endif; ?>
                
                <?php
                // Tombol Next
                if($current_page < $total_pages):
                  $next_page = $current_page + 1;
                ?>
                <li><a href="?page=<?php echo $next_page; ?><?php echo $query_string; ?>">&raquo;</a></li>
                <?php else: ?>
                <li class="disabled"><span>&raquo;</span></li>
                <?php endif; ?>
              </ul>
              
              <div class="pull-left" style="margin-top: 5px;">
                <small class="text-muted">
                  Halaman <?php echo $current_page; ?> dari <?php echo $total_pages; ?>
                </small>
              </div>
            </div>
            <?php endif; ?>
          </div>

        </div>
      </section>
    </div>
  </section>

</div>

<!-- Modal Konfirmasi Reset Password -->
<div class="modal fade" id="resetPasswordModal" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"><i class="fa fa-key"></i> Reset Password Siswa</h5>
        <button type="button" class="close" data-dismiss="modal">
          <span>&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="alert alert-info">
          <i class="fa fa-info-circle"></i> Password akan direset ke: <strong>123456</strong>
        </div>
        <p>Apakah Anda yakin ingin mereset password untuk:</p>
        <div class="well">
          <strong>Nama:</strong> <span id="modal-siswa-nama"></span><br>
          <strong>NIS:</strong> <span id="modal-siswa-nis"></span>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
        <button type="button" class="btn btn-danger" id="confirmResetPassword">
          <i class="fa fa-key"></i> Reset Password
        </button>
      </div>
    </div>
  </div>
</div>

<!-- Modal Konfirmasi Reset Semua Password -->
<div class="modal fade" id="resetAllPasswordModal" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"><i class="fa fa-key"></i> Reset Password Semua Siswa</h5>
        <button type="button" class="close" data-dismiss="modal">
          <span>&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="alert alert-warning">
          <i class="fa fa-warning"></i> <strong>PERINGATAN!</strong>
        </div>
        <p>Apakah Anda yakin ingin mereset password <strong>SEMUA SISWA AKTIF</strong> ke: <strong>123456</strong>?</p>
        <div class="alert alert-info">
          <i class="fa fa-info-circle"></i> Aksi ini akan mempengaruhi <strong><?php echo $total_data; ?></strong> siswa aktif.
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
        <button type="button" class="btn btn-danger" id="confirmResetAllPasswords">
          <i class="fa fa-key"></i> Reset Semua Password
        </button>
      </div>
    </div>
  </div>
</div>

<?php include 'footer.php'; ?>

<script>
$(document).ready(function(){
    var currentSiswaId = null;
    
    // Auto-submit form ketika dropdown tahun ajaran berubah, sambil preserve search term
    $('#ta_filter').on('change', function(){
        // Reset ke halaman 1 saat filter berubah
        $('input[name="page"]').val(1);
        $(this).closest('form').submit();
    });
    
    // Reset ke halaman 1 saat form search di-submit
    $('form').on('submit', function(){
        $('input[name="page"]').val(1);
    });
    
    // Reset password individual
    $('.reset-password-btn').click(function(){
        currentSiswaId = $(this).data('siswa-id');
        var nama = $(this).data('siswa-nama');
        var nis = $(this).data('siswa-nis');
        
        $('#modal-siswa-nama').text(nama);
        $('#modal-siswa-nis').text(nis);
        $('#resetPasswordModal').modal('show');
    });
    
    // Reset semua password
    $('#resetAllPasswords').click(function(){
        $('#resetAllPasswordModal').modal('show');
    });
    
    // Konfirmasi reset password individual
    $('#confirmResetPassword').click(function(){
        if(currentSiswaId) {
            resetPassword(currentSiswaId, false);
        }
    });
    
    // Konfirmasi reset semua password
    $('#confirmResetAllPasswords').click(function(){
        resetPassword(null, true);
    });
    
    function resetPassword(siswaId, resetAll) {
        var button = resetAll ? $('#confirmResetAllPasswords') : $('#confirmResetPassword');
        var originalText = button.html();
        
        // Disable button dan ganti text
        button.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Processing...');
        
        $.ajax({
            url: 'reset_password_siswa_simple.php',
            method: 'POST',
            data: {
                siswa_id: siswaId,
                reset_all: resetAll ? 1 : 0
            },
            success: function(response) {
                try {
                    var data = JSON.parse(response);
                    if(data.success) {
                        // Success
                        if(resetAll) {
                            $('#resetAllPasswordModal').modal('hide');
                            showAlert('success', 'Password semua siswa berhasil direset ke "123456"');
                        } else {
                            $('#resetPasswordModal').modal('hide');
                            showAlert('success', 'Password siswa berhasil direset ke "123456"');
                        }
                    } else {
                        // Error
                        showAlert('danger', data.message || 'Gagal mereset password');
                    }
                } catch(e) {
                    showAlert('danger', 'Terjadi kesalahan pada server');
                }
            },
            error: function(xhr, status, error) {
                showAlert('danger', 'Terjadi kesalahan koneksi');
            },
            complete: function() {
                // Reset button
                button.prop('disabled', false).html(originalText);
            }
        });
    }
    
    function showAlert(type, message) {
        var alertClass = 'alert-' + type;
        var icon = type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle';
        
        var alertHtml = '<div class="alert ' + alertClass + ' alert-dismissible" style="margin-top: 20px;">' +
                       '<button type="button" class="close" data-dismiss="alert">&times;</button>' +
                       '<i class="fa ' + icon + '"></i> ' + message +
                       '</div>';
        
        // Hapus alert sebelumnya
        $('.alert-dismissible').remove();
        
        // Tambahkan alert baru
        $('.content-header').after(alertHtml);
        
        // Auto hide setelah 5 detik
        setTimeout(function(){
            $('.alert-dismissible').fadeOut();
        }, 5000);
    }
});
</script>