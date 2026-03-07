<?php 
include 'header_dynamic.php';

// Ambil ID jurnal dari URL
$jurnal_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if($jurnal_id == 0) {
    header("location:kasus_siswa.php?alert=gagal&pesan=ID jurnal tidak valid");
    exit();
}

// Ambil data jurnal
$query_jurnal = "SELECT j.*, k.kasus_kode, k.judul_kasus, s.siswa_nama, s.siswa_nis 
                 FROM jurnal_kasus j 
                 LEFT JOIN kasus_siswa k ON j.kasus_id = k.kasus_id 
                 LEFT JOIN siswa s ON k.siswa_id = s.siswa_id 
                 WHERE j.jurnal_id = '$jurnal_id'";
$result_jurnal = mysqli_query($koneksi, $query_jurnal);

if(mysqli_num_rows($result_jurnal) == 0) {
    header("location:kasus_siswa.php?alert=gagal&pesan=Jurnal tidak ditemukan");
    exit();
}

$jurnal = mysqli_fetch_assoc($result_jurnal);
?>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <section class="content-header">
    <h1>
      <i class="fa fa-edit"></i> Edit Jurnal Perkembangan
      <small>Kasus: <?php echo $jurnal['kasus_kode']; ?></small>
    </h1>
    <ol class="breadcrumb">
      <li><a href="index.php"><i class="fa fa-dashboard"></i> Home</a></li>
      <li><a href="kasus_siswa.php">Data Kasus Siswa</a></li>
      <li><a href="kasus_siswa_detail.php?id=<?php echo $jurnal['kasus_id']; ?>">Detail Kasus</a></li>
      <li class="active">Edit Jurnal</li>
    </ol>
  </section>

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
    
  <!-- Main content -->
  <section class="content">
    <div class="row">
      <div class="col-md-12">
        <div class="box box-primary">
          <div class="box-header with-border">
            <h3 class="box-title">
              <i class="fa fa-book"></i> Form Edit Jurnal
            </h3>
          </div>
          
          <form role="form" method="POST" action="jurnal_update.php" enctype="multipart/form-data">
            <input type="hidden" name="jurnal_id" value="<?php echo $jurnal_id; ?>">
            <input type="hidden" name="kasus_id" value="<?php echo $jurnal['kasus_id']; ?>">
            
            <div class="box-body">
              <!-- Informasi Kasus -->
              <div class="alert alert-info">
                <h4><i class="fa fa-info-circle"></i> Informasi Kasus</h4>
                <p><strong>Siswa:</strong> <?php echo $jurnal['siswa_nama']; ?> (<?php echo $jurnal['siswa_nis']; ?>)</p>
                <p><strong>Judul Kasus:</strong> <?php echo $jurnal['judul_kasus']; ?></p>
              </div>

              <div class="row">
                <div class="col-md-6">
                  <div class="form-group">
                    <label for="tanggal_konseling">Tanggal Konseling <span class="text-red">*</span></label>
                    <input type="date" class="form-control" name="tanggal_konseling" id="tanggal_konseling" 
                           value="<?php echo $jurnal['tanggal_konseling']; ?>" required>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group">
                    <label for="lampiran_file">Lampiran File Baru</label>
                    <input type="file" class="form-control" name="lampiran_file" id="lampiran_file" 
                           accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">
                    <small class="text-muted">Format: PDF, DOC, DOCX, JPG, PNG (Max: 5MB)</small>
                    <?php if(!empty($jurnal['lampiran_file'])) { ?>
                    <div class="alert alert-warning">
                      <strong>File Lama:</strong> 
                      <a href="../<?php echo $jurnal['lampiran_file']; ?>" target="_blank" class="btn btn-info btn-xs">
                        <i class="fa fa-download"></i> Download File Lama
                      </a>
                      <small class="text-muted">(File akan diganti jika upload file baru)</small>
                    </div>
                    <?php } ?>
                  </div>
                </div>
              </div>
              
              <div class="form-group">
                <label for="bentuk_layanan">Bentuk Layanan <span class="text-red">*</span></label>
                <select class="form-control" name="bentuk_layanan" id="bentuk_layanan" required>
                  <?php $bl = isset($jurnal['bentuk_layanan']) ? $jurnal['bentuk_layanan'] : ''; ?>
                  <option value="">- Pilih Bentuk Layanan -</option>
                  <option value="Konseling Individu" <?php echo ($bl=='Konseling Individu')?'selected':''; ?>>Konseling Individu</option>
                  <option value="Konseling Kelompok" <?php echo ($bl=='Konseling Kelompok')?'selected':''; ?>>Konseling Kelompok</option>
                  <option value="Konsultasi Orang Tua/Wali" <?php echo ($bl=='Konsultasi Orang Tua/Wali')?'selected':''; ?>>Konsultasi Orang Tua/Wali</option>
                  <option value="Koordinasi dengan Wali Kelas" <?php echo ($bl=='Koordinasi dengan Wali Kelas')?'selected':''; ?>>Koordinasi dengan Wali Kelas</option>
                  <option value="Home Visit" <?php echo ($bl=='Home Visit')?'selected':''; ?>>Home Visit</option>
                  <option value="Rujukan" <?php echo ($bl=='Rujukan')?'selected':''; ?>>Rujukan</option>
                  <?php if($bl && !in_array($bl, ['Konseling Individu','Konseling Kelompok','Konsultasi Orang Tua/Wali','Koordinasi dengan Wali Kelas','Home Visit','Rujukan'])): ?>
                  <option value="<?php echo htmlspecialchars($bl); ?>" selected><?php echo htmlspecialchars($bl); ?></option>
                  <?php endif; ?>
                  <option value="Lainnya">Lainnya (isi manual)</option>
                </select>
              </div>

              <div class="form-group" id="bentuk_layanan_lainnya_wrap" style="display:none;">
                <label for="bentuk_layanan_lainnya">Bentuk Layanan (Lainnya)</label>
                <input type="text" class="form-control" id="bentuk_layanan_lainnya" placeholder="Isi nama layanan lain...">
                <small class="text-muted">Jika memilih "Lainnya", isi nama layanan di sini.</small>
              </div>

              <script>
              $(function(){
                var current = $('#bentuk_layanan').val();
                if(current==='Lainnya'){
                  $('#bentuk_layanan_lainnya_wrap').show();
                  $('#bentuk_layanan_lainnya').val('');
                }
                $('#bentuk_layanan').on('change', function(){
                  if($(this).val()==='Lainnya'){
                    $('#bentuk_layanan_lainnya_wrap').slideDown();
                  } else {
                    $('#bentuk_layanan_lainnya_wrap').slideUp();
                    $('#bentuk_layanan_lainnya').val('');
                  }
                });
              });
              </script>
                <label for="uraian_sesi">Uraian Sesi/Deskripsi Masalah <span class="text-red">*</span></label>
                <textarea class="form-control" name="uraian_sesi" id="uraian_sesi" 
                          rows="4" required><?php echo $jurnal['uraian_sesi']; ?></textarea>
              </div>

              <div class="form-group">
                <label for="analisis_diagnosis">Analisis/Diagnosis Awal</label>
                <textarea class="form-control" name="analisis_diagnosis" id="analisis_diagnosis" 
                          rows="3"><?php echo $jurnal['analisis_diagnosis']; ?></textarea>
              </div>

              <div class="form-group">
                <label for="tindakan_intervensi">Tindakan/Intervensi yang Diberikan</label>
                <textarea class="form-control" name="tindakan_intervensi" id="tindakan_intervensi" 
                          rows="3"><?php echo $jurnal['tindakan_intervensi']; ?></textarea>
              </div>

              <div class="form-group">
                <label for="rencana_tindak_lanjut">Rencana Tindak Lanjut (RTL)</label>
                <textarea class="form-control" name="rencana_tindak_lanjut" id="rencana_tindak_lanjut" 
                          rows="3"><?php echo $jurnal['rencana_tindak_lanjut']; ?></textarea>
              </div>

              <!-- Informasi Tambahan -->
              <div class="row">
                <div class="col-md-12">
                  <div class="alert alert-info">
                    <h4><i class="fa fa-info-circle"></i> Informasi Jurnal</h4>
                    <p><strong>Dibuat:</strong> <?php echo date('d/m/Y H:i', strtotime($jurnal['created_at'])); ?></p>
                    <p><strong>Terakhir Diupdate:</strong> <?php echo date('d/m/Y H:i', strtotime($jurnal['updated_at'])); ?></p>
                  </div>
                </div>
              </div>
            </div>

            <div class="box-footer">
              <button type="submit" name="update" class="btn btn-primary">
                <i class="fa fa-save"></i> Update Jurnal
              </button>
              <a href="kasus_siswa_detail.php?id=<?php echo $jurnal['kasus_id']; ?>" class="btn btn-default">
                <i class="fa fa-arrow-left"></i> Kembali
              </a>
            </div>
          </form>
        </div>
      </div>
    </div>
  </section>
</div>

<!-- Script untuk validasi -->
<script>
$(document).ready(function() {\n    // Pastikan sidebar tampil
    $('.main-sidebar').show();
    $('.content-wrapper').css('margin-left', '230px');
    
    // Inisialisasi sidebar menu
    $('.sidebar-menu').tree();
    
    
    // Validasi form
    $('form').on('submit', function(e) {
        var tanggal_konseling = $('#tanggal_konseling').val();
        var uraian_sesi = $('#uraian_sesi').val();

        if(!tanggal_konseling || !uraian_sesi) {
            e.preventDefault();
            alert('Mohon lengkapi field yang wajib diisi!');
            return false;
        }

        // Validasi tanggal konseling tidak boleh lebih dari hari ini
        var today = new Date().toISOString().split('T')[0];
        if(tanggal_konseling > today) {
            e.preventDefault();
            alert('Tanggal konseling tidak boleh lebih dari hari ini!');
            return false;
        }
    });
});
</script>

<?php include 'footer.php'; ?>
