<?php 
include 'header.php';
?>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <section class="content-header">
    <h1>
      <i class="fa fa-plus"></i> Tambah Kasus Siswa Baru
      <small>Form Input Kasus Konseling</small>
    </h1>
    <ol class="breadcrumb">
      <li><a href="index.php"><i class="fa fa-dashboard"></i> Home</a></li>
      <li><a href="kasus_siswa.php">Data Kasus Siswa</a></li>
      <li class="active">Tambah Kasus Baru</li>
    </ol>
  </section>

  <!-- Main content -->
  <section class="content">
    <!-- Debug Info -->
    <div class="alert alert-warning">
      <h4><i class="fa fa-bug"></i> Debug Select2</h4>
      <p><strong>Status:</strong> <span id="debug-status">Loading...</span></p>
      <p><strong>jQuery:</strong> <span id="debug-jquery">Loading...</span></p>
      <p><strong>Select2:</strong> <span id="debug-select2">Loading...</span></p>
      <p><strong>Element:</strong> <span id="debug-element">Loading...</span></p>
      <p><strong>Script Loaded:</strong> <span id="debug-script">Loading...</span></p>
    </div>
    
    <!-- Inline Debug Script -->
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        document.getElementById('debug-script').textContent = 'DOM Ready';
        
        // Debug dengan vanilla JavaScript
        setTimeout(function() {
            try {
                document.getElementById('debug-script').textContent = 'Script loaded';
                
                // Cek jQuery
                if (typeof $ !== 'undefined') {
                    document.getElementById('debug-jquery').textContent = $.fn.jquery;
                } else {
                    document.getElementById('debug-jquery').textContent = 'jQuery not loaded';
                }
                
                // Cek Select2
                if (typeof $ !== 'undefined' && typeof $.fn.select2 !== 'undefined') {
                    document.getElementById('debug-select2').textContent = 'Available';
                } else {
                    document.getElementById('debug-select2').textContent = 'Not Available';
                }
                
                // Cek Element
                var element = document.getElementById('siswa_id');
                if (element) {
                    document.getElementById('debug-element').textContent = 'Found';
                } else {
                    document.getElementById('debug-element').textContent = 'Not Found';
                }
                
                document.getElementById('debug-status').textContent = 'Debug completed';
                
                // Inisialisasi Select2 jika tersedia
                if (typeof $ !== 'undefined' && typeof $.fn.select2 !== 'undefined') {
                    try {
                        $('#siswa_id').select2({
                            placeholder: "Ketik untuk mencari siswa...",
                            allowClear: true,
                            width: '100%'
                        });
                        document.getElementById('debug-status').textContent = 'Select2 initialized successfully!';
                    } catch (e) {
                        document.getElementById('debug-status').textContent = 'Select2 error: ' + e.message;
                    }
                }
                
            } catch (e) {
                document.getElementById('debug-status').textContent = 'Error: ' + e.message;
            }
        }, 2000);
    });
    </script>
    
    <!-- CSS Select2 -->
    <link rel="stylesheet" href="../assets/bower_components/select2/dist/css/select2.min.css">
    
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
      
      /* CSS untuk Select2 */
      .select2-container {
        width: 100% !important;
        z-index: 9999 !important;
      }
      
      .select2-container--default .select2-selection--single {
        height: 34px !important;
        border: 1px solid #d2d6de !important;
        border-radius: 3px !important;
        background-color: #fff !important;
      }
      
      .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 32px !important;
        padding-left: 12px !important;
        color: #555 !important;
      }
      
      .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 32px !important;
      }
      
      .select2-dropdown {
        border: 1px solid #d2d6de !important;
        border-radius: 3px !important;
        z-index: 9999 !important;
      }
      
      .select2-search--dropdown {
        padding: 4px !important;
      }
      
      .select2-search--dropdown .select2-search__field {
        border: 1px solid #d2d6de !important;
        border-radius: 3px !important;
        padding: 6px 12px !important;
        width: 100% !important;
        font-size: 14px !important;
      }
      
      .select2-results__option {
        padding: 6px 12px !important;
        font-size: 14px !important;
      }
      
      .select2-results__option--highlighted {
        background-color: #3c8dbc !important;
        color: white !important;
      }
      
      /* Force Select2 to show search box */
      .select2-search--dropdown {
        display: block !important;
      }
      
      .select2-search--dropdown .select2-search__field {
        display: block !important;
        width: 100% !important;
        margin: 4px !important;
      }
      
      @media (max-width: 767px) {
        .content-wrapper {
          margin-left: 0 !important;
        }
      }
    </style>
    
    <div class="row">
      <div class="col-md-12">
        <div class="box box-primary">
          <div class="box-header with-border">
            <h3 class="box-title">
              <i class="fa fa-folder-plus"></i> Form Kasus Siswa
            </h3>
          </div>
          
          <form role="form" method="POST" action="kasus_siswa_act.php" enctype="multipart/form-data">
            <div class="box-body">
              <div class="row">
                <!-- Kolom Kiri -->
                <div class="col-md-6">
                  <div class="form-group">
                    <label for="siswa_id">Pilih Siswa <span class="text-red">*</span></label>
                    <select class="form-control select2" name="siswa_id" id="siswa_id" required>
                      <option value="">-- Pilih Siswa --</option>
                      <?php
                      // Hanya tampilkan siswa yang punya kelas di tahun ajaran aktif
                      $query_siswa = "SELECT DISTINCT s.* FROM siswa s 
                                     JOIN kelas_siswa ks ON s.siswa_id = ks.ks_siswa 
                                     JOIN kelas k ON ks.ks_kelas = k.kelas_id 
                                     JOIN ta t ON k.kelas_ta = t.ta_id 
                                     WHERE t.ta_status = 1 AND s.siswa_status = 'aktif' 
                                     ORDER BY s.siswa_nama";
                      $result_siswa = mysqli_query($koneksi, $query_siswa);
                      while($siswa = mysqli_fetch_assoc($result_siswa)) {
                        echo "<option value='".$siswa['siswa_id']."'>".$siswa['siswa_nama']." (".$siswa['siswa_nis'].")</option>";
                      }
                      ?>
                    </select>
                  </div>

                  <div class="form-group">
                    <label for="tanggal_pelaporan">Tanggal Pelaporan <span class="text-red">*</span></label>
                    <input type="date" class="form-control" name="tanggal_pelaporan" id="tanggal_pelaporan" 
                           value="<?php echo date('Y-m-d'); ?>" required>
                  </div>

                  <div class="form-group">
                    <label for="sumber_kasus">Sumber Kasus/Rujukan <span class="text-red">*</span></label>
                    <select class="form-control" name="sumber_kasus" id="sumber_kasus" required>
                      <option value="">-- Pilih Sumber --</option>
                      <option value="Wali Kelas">Wali Kelas</option>
                      <option value="Guru Mapel">Guru Mapel</option>
                      <option value="Orang Tua">Orang Tua</option>
                      <option value="Inisiatif Siswa">Inisiatif Siswa</option>
                      <option value="Teman">Teman</option>
                      <option value="Temuan Guru BK">Temuan Guru BK</option>
                    </select>
                  </div>

                  <div class="form-group">
                    <label for="kategori_masalah">Kategori Masalah <span class="text-red">*</span></label>
                    <select class="form-control" name="kategori_masalah" id="kategori_masalah" required>
                      <option value="">-- Pilih Kategori --</option>
                      <option value="Pribadi">Pribadi</option>
                      <option value="Sosial">Sosial</option>
                      <option value="Belajar">Belajar</option>
                      <option value="Karir">Karir</option>
                    </select>
                  </div>
                </div>

                <!-- Kolom Kanan -->
                <div class="col-md-6">
                  <div class="form-group">
                    <label for="judul_kasus">Judul/Perihal Kasus <span class="text-red">*</span></label>
                    <input type="text" class="form-control" name="judul_kasus" id="judul_kasus" 
                           placeholder="Contoh: Kesulitan Mengikuti Pelajaran Matematika" required>
                  </div>

                  <div class="form-group">
                    <label for="deskripsi_awal">Deskripsi Awal Masalah</label>
                    <textarea class="form-control" name="deskripsi_awal" id="deskripsi_awal" 
                              rows="4" placeholder="Jelaskan kondisi awal masalah yang dialami siswa..."></textarea>
                  </div>

                  <div class="form-group">
                    <label for="status_kasus">Status Kasus</label>
                    <select class="form-control" name="status_kasus" id="status_kasus">
                      <option value="Baru" selected>Baru</option>
                      <option value="Dalam Proses">Dalam Proses</option>
                      <option value="Selesai/Tuntas">Selesai/Tuntas</option>
                      <option value="Dirujuk/Alih Tangan Kasus">Dirujuk/Alih Tangan Kasus</option>
                    </select>
                  </div>

                  <div class="form-group">
                    <label for="guru_bk_id">Guru BK Penanggung Jawab</label>
                    <select class="form-control" name="guru_bk_id" id="guru_bk_id" required>
                      <option value="">-- Pilih Guru BK --</option>
                      <?php
                      $query_guru = "SELECT * FROM user WHERE user_level = 'administrator' OR user_level = 'guru_bk' ORDER BY user_nama";
                      $result_guru = mysqli_query($koneksi, $query_guru);
                      while($guru = mysqli_fetch_assoc($result_guru)) {
                        $selected = ($guru['user_id'] == $_SESSION['id']) ? 'selected' : '';
                        echo "<option value='".$guru['user_id']."' $selected>".$guru['user_nama']."</option>";
                      }
                      ?>
                    </select>
                  </div>
                </div>
              </div>

              <!-- Jurnal Perkembangan Awal (Opsional) -->
              <div class="row">
                <div class="col-md-12">
                  <div class="box box-info">
                    <div class="box-header with-border">
                      <h3 class="box-title">
                        <i class="fa fa-book"></i> Jurnal Perkembangan Awal (Opsional)
                      </h3>
                    </div>
                    <div class="box-body">
                      <div class="row">
                        <div class="col-md-6">
                          <div class="form-group">
                            <label for="tanggal_konseling">Tanggal Konseling</label>
                            <input type="date" class="form-control" name="tanggal_konseling" id="tanggal_konseling">
                          </div>
                        </div>
                        <div class="col-md-6">
                          <div class="form-group">
                            <label for="lampiran_file">Lampiran File</label>
                            <input type="file" class="form-control" name="lampiran_file" id="lampiran_file" 
                                   accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">
                            <small class="text-muted">Format: PDF, DOC, DOCX, JPG, PNG (Max: 5MB)</small>
                          </div>
                        </div>
                      </div>
                      
                      <div class="form-group">
                        <label for="uraian_sesi">Uraian Sesi/Deskripsi Masalah</label>
                        <textarea class="form-control" name="uraian_sesi" id="uraian_sesi" 
                                  rows="3" placeholder="Catatan detail mengenai apa yang dibicarakan pada sesi tersebut..."></textarea>
                      </div>

                      <div class="form-group">
                        <label for="analisis_diagnosis">Analisis/Diagnosis Awal</label>
                        <textarea class="form-control" name="analisis_diagnosis" id="analisis_diagnosis" 
                                  rows="3" placeholder="Analisis singkat mengenai akar permasalahan..."></textarea>
                      </div>

                      <div class="form-group">
                        <label for="tindakan_intervensi">Tindakan/Intervensi yang Diberikan</label>
                        <textarea class="form-control" name="tindakan_intervensi" id="tindakan_intervensi" 
                                  rows="3" placeholder="Layanan yang diberikan pada sesi tersebut..."></textarea>
                      </div>

                      <div class="form-group">
                        <label for="rencana_tindak_lanjut">Rencana Tindak Lanjut (RTL)</label>
                        <textarea class="form-control" name="rencana_tindak_lanjut" id="rencana_tindak_lanjut" 
                                  rows="3" placeholder="Langkah selanjutnya yang akan dilakukan..."></textarea>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <div class="box-footer">
              <button type="submit" name="simpan" class="btn btn-primary">
                <i class="fa fa-save"></i> Simpan Kasus
              </button>
              <a href="kasus_siswa.php" class="btn btn-default">
                <i class="fa fa-arrow-left"></i> Kembali
              </a>
            </div>
          </form>
        </div>
      </div>
    </div>
  </section>
</div>

<!-- Script untuk Select2 dan validasi -->
<script src="../assets/bower_components/select2/dist/js/select2.min.js"></script>
<script>
$(document).ready(function() {
    // Pastikan sidebar tampil
    $('.main-sidebar').show();
    $('.content-wrapper').css('margin-left', '230px');
    
    // Inisialisasi sidebar menu
    $('.sidebar-menu').tree();
    
    // Debug dan Select2 sudah dihandle oleh vanilla JavaScript di atas

    // Validasi form
    $('form').on('submit', function(e) {
        var siswa_id = $('#siswa_id').val();
        var tanggal_pelaporan = $('#tanggal_pelaporan').val();
        var sumber_kasus = $('#sumber_kasus').val();
        var kategori_masalah = $('#kategori_masalah').val();
        var judul_kasus = $('#judul_kasus').val();

        if(!siswa_id || !tanggal_pelaporan || !sumber_kasus || !kategori_masalah || !judul_kasus) {
            e.preventDefault();
            alert('Mohon lengkapi semua field yang wajib diisi!');
            return false;
        }
    });

    // Auto-fill tanggal konseling dengan tanggal pelaporan
    $('#tanggal_pelaporan').on('change', function() {
        $('#tanggal_konseling').val($(this).val());
    });
});
</script>

<?php include 'footer.php'; ?>
