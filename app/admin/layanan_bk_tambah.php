<?php 
include 'header_dynamic.php';
include 'functions_academic_year.php';

// Update status tahun ajaran berdasarkan tanggal saat ini
updateAcademicYearStatus($koneksi);

// Get active academic year ID
$active_ta_id = getActiveAcademicYearId($koneksi);
?>

<div class="content-wrapper">
  <section class="content-header">
    <h1>
      Tambah Layanan BK
      <small>Pencatatan Layanan Bimbingan dan Konseling</small>
    </h1>
    <ol class="breadcrumb">
      <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
      <li><a href="layanan_bk.php">Data Layanan BK</a></li>
      <li class="active">Tambah Layanan</li>
    </ol>
  </section>
  
  <?php 
  $active_ta_name = getActiveAcademicYearName($koneksi);
  if($active_ta_name): ?>
  <div class="alert alert-info">
    <i class="fa fa-info-circle"></i> 
    <strong>Tahun Ajaran Aktif:</strong> <?php echo $active_ta_name; ?> - 
    Kelas dan siswa yang ditampilkan hanya untuk tahun ajaran aktif ini.
  </div>
  <?php endif; ?>

  <section class="content">
    <div class="row">
      <section class="col-lg-12">
        <div class="box box-primary">

          <div class="box-header">
            <h3 class="box-title">Form Tambah Layanan BK</h3>
          </div>
          
          <form role="form" method="POST" action="layanan_bk_act.php" enctype="multipart/form-data">
            <div class="box-body">
              
              <!-- Informasi Dasar Layanan -->
              <div class="row">
                <div class="col-md-6">
                  <div class="form-group">
                    <label>Tanggal Pelaksanaan <span class="text-red">*</span></label>
                    <input type="date" name="tanggal_pelaksanaan" class="form-control" required>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group">
                    <label>Jenis Layanan <span class="text-red">*</span></label>
                    <select name="jenis_layanan" class="form-control" required>
                      <option value="">-- Pilih Jenis Layanan --</option>
                      <option value="Layanan Klasikal">Layanan Klasikal</option>
                      <option value="Bimbingan Kelompok">Bimbingan Kelompok</option>
                      <option value="Konseling Kelompok">Konseling Kelompok</option>
                      <option value="Konsultasi">Konsultasi</option>
                      <option value="Mediasi">Mediasi</option>
                      <option value="Layanan Advokasi">Layanan Advokasi</option>
                      <option value="Layanan Peminatan">Layanan Peminatan</option>
                      <option value="Lainnya">Lainnya</option>
                    </select>
                  </div>
                </div>
              </div>

              <div class="row">
                <div class="col-md-6">
                  <div class="form-group">
                    <label>Topik/Materi Layanan <span class="text-red">*</span></label>
                    <input type="text" name="topik_materi" class="form-control" 
                           placeholder="Contoh: Etika Pergaulan di Media Sosial" required>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group">
                    <label>Bidang Layanan <span class="text-red">*</span></label>
                    <select name="bidang_layanan" class="form-control" required>
                      <option value="">-- Pilih Bidang Layanan --</option>
                      <option value="Pribadi">Pribadi</option>
                      <option value="Sosial">Sosial</option>
                      <option value="Belajar">Belajar</option>
                      <option value="Karir">Karir</option>
                    </select>
                  </div>
                </div>
              </div>

              <!-- Field Dibuat Oleh -->
              <div class="row">
                <div class="col-md-6">
                  <div class="form-group">
                    <label>Dibuat Oleh <span class="text-red">*</span></label>
                    <select name="dibuat_oleh" class="form-control" required>
                      <option value="">-- Pilih Pembuat Layanan --</option>
                      <?php
                      // Query untuk mendapatkan daftar administrator dan guru BK
                      $query_user = "SELECT * FROM user WHERE user_level = 'administrator' OR user_level = 'guru_bk' ORDER BY user_nama";
                      $user_result = mysqli_query($koneksi, $query_user);
                      while($u = mysqli_fetch_assoc($user_result)) {
                        echo "<option value='".$u['user_id']."'>".$u['user_nama']." (".ucfirst($u['user_level']).")</option>";
                      }
                      ?>
                    </select>
                    <small class="text-muted">Pilih user yang membuat layanan ini</small>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group">
                    <label>Status Layanan</label>
                    <select name="status_layanan" class="form-control">
                      <option value="Aktif" selected>Aktif</option>
                      <option value="Selesai">Selesai</option>
                      <option value="Dibatalkan">Dibatalkan</option>
                    </select>
                  </div>
                </div>
              </div>

              <div class="row">
                <div class="col-md-6">
                  <div class="form-group">
                    <label>Sasaran Layanan <span class="text-red">*</span></label>
                    <select id="sasaran_layanan" name="sasaran_layanan" class="form-control" required>
                      <option value="">-- Pilih Sasaran Layanan --</option>
                      <option value="Satu Kelas">Satu Kelas</option>
                      <option value="Kelompok Siswa">Kelompok Siswa</option>
                      <option value="Individu">Individu</option>
                    </select>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group">
                    <label>Jumlah Peserta <span class="text-red">*</span></label>
                    <input type="number" id="jumlah_peserta" name="jumlah_peserta" class="form-control" 
                           placeholder="Jumlah peserta" readonly>
                  </div>
                </div>
              </div>

              <!-- Detail Peserta -->
              <div id="kelas_selection" style="display: none;">
                <div class="form-group">
                  <label>Kelas <span class="text-red">*</span></label>
                  <select id="kelas_id" name="kelas_id" class="form-control select2" style="width: 100%;">
                    <option value="">-- Pilih Kelas --</option>
                    <?php
                    // Query kelas yang aktif di tahun ajaran aktif
                    $kelas_query = "SELECT k.*, j.jurusan_nama 
                                   FROM kelas k 
                                   JOIN jurusan j ON k.kelas_jurusan = j.jurusan_id 
                                   WHERE k.kelas_ta = '$active_ta_id' 
                                   ORDER BY j.jurusan_nama ASC, k.kelas_nama ASC";
                    $kelas = mysqli_query($koneksi, $kelas_query);
                    while($k = mysqli_fetch_array($kelas)){
                      echo "<option value='".$k['kelas_id']."'>".$k['jurusan_nama']." | ".$k['kelas_nama']."</option>";
                    }
                    ?>
                  </select>
                  <p class="help-block">Ketik untuk mencari nama kelas</p>
                </div>
              </div>
              
              <div id="siswa_selection" style="display: none;">
                <div class="form-group">
                  <label>Siswa <span class="text-red">*</span></label>
                  <select id="siswa_ids" name="siswa_ids[]" class="form-control select2" multiple style="width: 100%;">
                    <option value="">-- Pilih Siswa --</option>
                    <?php
                    // Query siswa yang aktif di kelas yang aktif di tahun ajaran aktif
                    $siswa_query = "SELECT DISTINCT s.* 
                                   FROM siswa s 
                                   JOIN kelas_siswa ks ON s.siswa_id = ks.ks_siswa 
                                   JOIN kelas k ON ks.ks_kelas = k.kelas_id 
                                   WHERE k.kelas_ta = '$active_ta_id' 
                                   AND s.siswa_status = 'aktif'
                                   ORDER BY s.siswa_nama ASC";
                    $siswa = mysqli_query($koneksi, $siswa_query);
                    while($s = mysqli_fetch_array($siswa)){
                      echo "<option value='".$s['siswa_id']."'>".$s['siswa_nama']."</option>";
                    }
                    ?>
                  </select>
                  <p class="help-block">Ketik untuk mencari nama siswa</p>
                </div>
              </div>

              <!-- Dokumentasi & Evaluasi -->
              <div class="form-group">
                <label>Uraian Singkat Kegiatan</label>
                <textarea name="uraian_kegiatan" class="form-control" rows="3" 
                          placeholder="Jelaskan ringkas tentang jalannya kegiatan..."></textarea>
              </div>

              <div class="form-group">
                <label>Evaluasi Proses (Evala-Pro)</label>
                <textarea name="evaluasi_proses" class="form-control" rows="2" 
                          placeholder="Catatan tentang bagaimana proses berjalan (antusiasme siswa, kendala, dll)..."></textarea>
              </div>

              <div class="form-group">
                <label>Evaluasi Hasil (Evala-Hasil)</label>
                <textarea name="evaluasi_hasil" class="form-control" rows="2" 
                          placeholder="Catatan tentang hasil yang dicapai (pemahaman siswa, perubahan sikap, dll)..."></textarea>
              </div>

              <div class="form-group">
                <label>Lampiran/Foto Kegiatan</label>
                <input type="file" name="lampiran" class="form-control" accept="image/*">
                <p class="help-block">Format: JPG, PNG, GIF. Maksimal 2MB</p>
              </div>

            </div>
            
            <div class="box-footer">
              <button type="submit" class="btn btn-primary">
                <i class="fa fa-save"></i> Simpan
              </button>
              <button type="button" class="btn btn-default" onclick="window.history.back()">
                <i class="fa fa-arrow-left"></i> Batal
              </button>
            </div>
          </form>

        </div>
      </section>
    </div>
  </section>

</div>
<?php include 'footer.php'; ?>

<script>
$(document).ready(function() {
    // Form functionality untuk sasaran layanan
    $('#sasaran_layanan').change(function() {
        var sasaran = $(this).val();
        
        if(sasaran == 'Satu Kelas') {
            $('#kelas_selection').show();
            $('#siswa_selection').hide();
            $('#siswa_ids').removeAttr('required');
            $('#kelas_id').attr('required', 'required');
            
            // Reinitialize Select2 for kelas when showing
            setTimeout(function() {
                $('#kelas_id').select2({
                    placeholder: "Ketik untuk mencari...",
                    allowClear: true,
                    width: '100%'
                });
            }, 100);
        } else if(sasaran == 'Kelompok Siswa' || sasaran == 'Individu') {
            $('#kelas_selection').hide();
            $('#siswa_selection').show();
            $('#kelas_id').removeAttr('required');
            $('#siswa_ids').attr('required', 'required');
            
            // Reinitialize Select2 for siswa when showing
            setTimeout(function() {
                $('#siswa_ids').select2({
                    placeholder: "Ketik untuk mencari...",
                    allowClear: true,
                    width: '100%'
                });
            }, 100);
        } else {
            $('#kelas_selection').hide();
            $('#siswa_selection').hide();
            $('#siswa_ids').removeAttr('required');
            $('#kelas_id').removeAttr('required');
        }
    });

    // Update jumlah peserta when siswa selection changes
    $('#siswa_ids').on('select2:select select2:unselect', function() {
        var selectedCount = $(this).val() ? $(this).val().length : 0;
        $('#jumlah_peserta').val(selectedCount);
    });
});
</script>
