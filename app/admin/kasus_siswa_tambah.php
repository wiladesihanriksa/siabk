<?php 
include 'header_dynamic.php';
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
            <h3 class="box-title">Form Kasus Siswa</h3>
            <div class="box-tools pull-right">
              <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
            </div>
          </div>
          <form action="kasus_siswa_act.php" method="POST" enctype="multipart/form-data">
            <div class="box-body">
              <div class="row">
                <!-- Kolom Kiri -->
                <div class="col-md-6">
                  <div class="form-group">
                    <label for="siswa_id">Pilih Siswa <span class="text-red">*</span></label>
                    <select class="form-control" name="siswa_id" id="siswa_id" required>
                      <option value="">-- Pilih Siswa --</option>
                      <?php
                      include '../koneksi.php';
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
                    <div class="input-group date">
                      <div class="input-group-addon">
                        <i class="fa fa-calendar"></i>
                      </div>
                      <input type="text" name="tanggal_pelaporan" class="form-control pull-right datepicker" id="tanggal_pelaporan" value="<?php echo date('d/m/Y'); ?>" required>
                    </div>
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
                    <input type="text" name="judul_kasus" class="form-control" id="judul_kasus" placeholder="Contoh: Kesulitan Mengikuti Pelajaran Matematika" required>
                  </div>

                  <div class="form-group">
                    <label for="deskripsi_awal">Deskripsi Awal Masalah <span class="text-red">*</span></label>
                    <textarea name="deskripsi_awal" class="form-control" id="deskripsi_awal" rows="4" placeholder="Jelaskan kondisi awal masalah yang dialami siswa" required></textarea>
                  </div>

                  <div class="form-group">
                    <label for="status_kasus">Status Kasus</label>
                    <input type="text" name="status_kasus" class="form-control" id="status_kasus" value="Baru" readonly>
                  </div>

                  <div class="form-group">
                    <label for="guru_bk_id">Guru BK Penanggung Jawab <span class="text-red">*</span></label>
                    <?php
                    // Cek level user yang login
                    $user_level = isset($_SESSION['level']) ? $_SESSION['level'] : '';
                    $user_id = isset($_SESSION['id']) ? $_SESSION['id'] : 0;
                    
                    if($user_level == 'guru_bk') {
                        // Jika user adalah guru_bk, ambil data guru_bk berdasarkan user_id
                        $guru_bk_query = mysqli_query($koneksi, "SELECT guru_bk_id, nama_guru_bk FROM guru_bk WHERE user_id = '$user_id' AND status_guru_bk = 'Aktif'");
                        if($guru_bk_query && mysqli_num_rows($guru_bk_query) > 0) {
                            $guru_bk_data = mysqli_fetch_assoc($guru_bk_query);
                            $guru_bk_id = $guru_bk_data['guru_bk_id'];
                            $guru_bk_nama = $guru_bk_data['nama_guru_bk'];
                            ?>
                            <input type="text" name="guru_bk_nama" class="form-control" id="guru_bk_nama" value="<?php echo htmlspecialchars($guru_bk_nama); ?>" readonly>
                            <input type="hidden" name="guru_bk_id" value="<?php echo $guru_bk_id; ?>">
                            <small class="text-muted">Otomatis terisi berdasarkan user yang login</small>
                            <?php
                        } else {
                            // Jika tidak ditemukan, tampilkan error
                            ?>
                            <div class="alert alert-danger">
                              <i class="fa fa-exclamation-triangle"></i> Data Guru BK tidak ditemukan untuk user ini. Silakan hubungi administrator.
                            </div>
                            <?php
                        }
                    } else if($user_level == 'administrator') {
                        // Jika user adalah administrator, tampilkan dropdown untuk memilih guru BK atau administrator
                        ?>
                        <select class="form-control" name="guru_bk_id" id="guru_bk_id" required>
                          <option value="">-- Pilih Guru BK --</option>
                          <?php
                          // Ambil semua user dengan level administrator atau guru_bk
                          $query_petugas = "SELECT u.user_id, u.user_nama, u.user_level, g.guru_bk_id 
                                           FROM user u 
                                           LEFT JOIN guru_bk g ON u.user_id = g.user_id 
                                           WHERE (u.user_level = 'administrator' OR u.user_level = 'guru_bk')
                                           ORDER BY u.user_level DESC, u.user_nama";
                          $result_petugas = mysqli_query($koneksi, $query_petugas);
                          
                          while($petugas = mysqli_fetch_assoc($result_petugas)) {
                              // Jika ada guru_bk_id, gunakan itu, jika tidak (administrator), gunakan user_id
                              $value = !empty($petugas['guru_bk_id']) ? $petugas['guru_bk_id'] : $petugas['user_id'];
                              $label = $petugas['user_nama'];
                              if($petugas['user_level'] == 'administrator') {
                                  $label .= ' (Administrator)';
                              }
                              echo "<option value='".$value."'>".$label."</option>";
                          }
                          ?>
                        </select>
                        <small class="text-muted">Pilih Guru BK atau Administrator yang akan menangani kasus ini</small>
                        <?php
                    } else {
                        // Jika level tidak valid
                        ?>
                        <div class="alert alert-danger">
                          <i class="fa fa-exclamation-triangle"></i> Level user tidak valid. Silakan login ulang.
                        </div>
                        <?php
                    }
                    ?>
                  </div>

                  <div class="form-group">
                    <label for="lampiran">Lampiran File</label>
                    <input type="file" name="lampiran" class="form-control" id="lampiran" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">
                    <small class="text-muted">Format: PDF, DOC, DOCX, JPG, PNG (Maksimal 5MB)</small>
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

<?php include 'footer.php'; ?>

<!-- Load Select2 AFTER footer (jQuery already loaded in footer) -->
<script src="../assets/bower_components/select2/dist/js/select2.min.js"></script>
<script>
// Init after all scripts (including jQuery from footer) are loaded
$(function() {
  // Inisialisasi Select2 (search box aktif)
  if ($.fn.select2) {
    $('#siswa_id').select2({
      placeholder: 'Ketik untuk mencari siswa...',
      allowClear: true,
      width: '100%'
    });
  }

  // Datepicker
  if ($.fn.datepicker) {
    $('.datepicker').datepicker({
      autoclose: true,
      format: 'dd/mm/yyyy',
      todayHighlight: true
    });
  }

  // Pastikan sidebar tampil
  $('.main-sidebar').show();
  $('.content-wrapper').css('margin-left', '230px');
  $('.sidebar-menu').tree();
});
</script>
