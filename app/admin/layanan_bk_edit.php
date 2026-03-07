<?php 
include 'header_dynamic.php';
include 'functions_academic_year.php';

// Update status tahun ajaran berdasarkan tanggal saat ini
updateAcademicYearStatus($koneksi);

// Get active academic year ID
$active_ta_id = getActiveAcademicYearId($koneksi);

if(!isset($_GET['id'])) {
    header("location:layanan_bk.php");
    exit();
}

$id = $_GET['id'];
$query = "SELECT * FROM layanan_bk WHERE layanan_id = ?";
$stmt = mysqli_prepare($koneksi, $query);
mysqli_stmt_bind_param($stmt, 'i', $id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if(mysqli_num_rows($result) == 0) {
    header("location:layanan_bk.php");
    exit();
}

$data = mysqli_fetch_assoc($result);

// Get peserta yang sudah dipilih
$query_peserta = "SELECT siswa_id FROM layanan_bk_peserta WHERE layanan_id = ?";
$stmt_peserta = mysqli_prepare($koneksi, $query_peserta);
mysqli_stmt_bind_param($stmt_peserta, 'i', $id);
mysqli_stmt_execute($stmt_peserta);
$result_peserta = mysqli_stmt_get_result($stmt_peserta);

$selected_siswa = array();
while($p = mysqli_fetch_assoc($result_peserta)) {
    $selected_siswa[] = $p['siswa_id'];
}
?>

<div class="content-wrapper">
  <section class="content-header">
    <h1>
      Edit Layanan BK
      <small>Ubah Data Layanan Bimbingan dan Konseling</small>
    </h1>
    <ol class="breadcrumb">
      <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
      <li><a href="layanan_bk.php">Data Layanan BK</a></li>
      <li class="active">Edit Layanan</li>
    </ol>
  </section>

  <section class="content">
    <div class="row">
      <div class="col-md-12">
        <div class="box box-primary">
          <div class="box-header with-border">
            <h3 class="box-title">Form Edit Layanan BK</h3>
          </div>
          
          <form role="form" method="POST" action="layanan_bk_update.php" enctype="multipart/form-data">
            <input type="hidden" name="layanan_id" value="<?php echo $data['layanan_id']; ?>">
            
            <div class="box-body">
              
              <!-- Informasi Dasar Layanan -->
              <div class="row">
                <div class="col-md-6">
                  <div class="form-group">
                    <label>Tanggal Pelaksanaan <span class="text-red">*</span></label>
                    <input type="date" name="tanggal_pelaksanaan" class="form-control" 
                           value="<?php echo $data['tanggal_pelaksanaan']; ?>" required>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group">
                    <label>Jenis Layanan <span class="text-red">*</span></label>
                    <select name="jenis_layanan" class="form-control" required>
                      <option value="">-- Pilih Jenis Layanan --</option>
                      <option value="Layanan Klasikal" <?php echo ($data['jenis_layanan'] == 'Layanan Klasikal') ? 'selected' : ''; ?>>Layanan Klasikal</option>
                      <option value="Bimbingan Kelompok" <?php echo ($data['jenis_layanan'] == 'Bimbingan Kelompok') ? 'selected' : ''; ?>>Bimbingan Kelompok</option>
                      <option value="Konseling Kelompok" <?php echo ($data['jenis_layanan'] == 'Konseling Kelompok') ? 'selected' : ''; ?>>Konseling Kelompok</option>
                      <option value="Konsultasi" <?php echo ($data['jenis_layanan'] == 'Konsultasi') ? 'selected' : ''; ?>>Konsultasi</option>
                      <option value="Mediasi" <?php echo ($data['jenis_layanan'] == 'Mediasi') ? 'selected' : ''; ?>>Mediasi</option>
                      <option value="Layanan Advokasi" <?php echo ($data['jenis_layanan'] == 'Layanan Advokasi') ? 'selected' : ''; ?>>Layanan Advokasi</option>
                      <option value="Layanan Peminatan" <?php echo ($data['jenis_layanan'] == 'Layanan Peminatan') ? 'selected' : ''; ?>>Layanan Peminatan</option>
                      <option value="Lainnya" <?php echo ($data['jenis_layanan'] == 'Lainnya') ? 'selected' : ''; ?>>Lainnya</option>
                    </select>
                  </div>
                </div>
              </div>

              <div class="row">
                <div class="col-md-6">
                  <div class="form-group">
                    <label>Topik/Materi Layanan <span class="text-red">*</span></label>
                    <input type="text" name="topik_materi" class="form-control" 
                           value="<?php echo $data['topik_materi']; ?>" required>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group">
                    <label>Bidang Layanan <span class="text-red">*</span></label>
                    <select name="bidang_layanan" class="form-control" required>
                      <option value="">-- Pilih Bidang Layanan --</option>
                      <option value="Pribadi" <?php echo ($data['bidang_layanan'] == 'Pribadi') ? 'selected' : ''; ?>>Pribadi</option>
                      <option value="Sosial" <?php echo ($data['bidang_layanan'] == 'Sosial') ? 'selected' : ''; ?>>Sosial</option>
                      <option value="Belajar" <?php echo ($data['bidang_layanan'] == 'Belajar') ? 'selected' : ''; ?>>Belajar</option>
                      <option value="Karir" <?php echo ($data['bidang_layanan'] == 'Karir') ? 'selected' : ''; ?>>Karir</option>
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
                        $selected = ($data['dibuat_oleh'] == $u['user_id']) ? 'selected' : '';
                        echo "<option value='".$u['user_id']."' $selected>".$u['user_nama']." (".ucfirst($u['user_level']).")</option>";
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
                      <option value="Aktif" <?php echo ($data['status_layanan'] == 'Aktif') ? 'selected' : ''; ?>>Aktif</option>
                      <option value="Selesai" <?php echo ($data['status_layanan'] == 'Selesai') ? 'selected' : ''; ?>>Selesai</option>
                      <option value="Dibatalkan" <?php echo ($data['status_layanan'] == 'Dibatalkan') ? 'selected' : ''; ?>>Dibatalkan</option>
                    </select>
                  </div>
                </div>
              </div>

              <!-- Detail Peserta -->
              <div class="row">
                <div class="col-md-6">
                  <div class="form-group">
                    <label>Sasaran Layanan <span class="text-red">*</span></label>
                    <select name="sasaran_layanan" class="form-control" required id="sasaran_layanan">
                      <option value="">-- Pilih Sasaran Layanan --</option>
                      <option value="Satu Kelas" <?php echo ($data['sasaran_layanan'] == 'Satu Kelas') ? 'selected' : ''; ?>>Satu Kelas</option>
                      <option value="Kelompok Siswa" <?php echo ($data['sasaran_layanan'] == 'Kelompok Siswa') ? 'selected' : ''; ?>>Kelompok Siswa</option>
                      <option value="Individu" <?php echo ($data['sasaran_layanan'] == 'Individu') ? 'selected' : ''; ?>>Individu</option>
                    </select>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group" id="kelas_selection" <?php echo ($data['sasaran_layanan'] != 'Satu Kelas') ? 'style="display: none;"' : ''; ?>>
                    <label>Pilih Kelas</label>
                    <select name="kelas_id" class="form-control" id="kelas_id">
                      <option value="">-- Pilih Kelas --</option>
                      <?php
                      $kelas = mysqli_query($koneksi,"SELECT * FROM kelas ORDER BY kelas_nama");
                      while($k = mysqli_fetch_assoc($kelas)) {
                        $selected = ($data['kelas_id'] == $k['kelas_id']) ? 'selected' : '';
                        echo "<option value='".$k['kelas_id']."' $selected>".$k['kelas_nama']."</option>";
                      }
                      ?>
                    </select>
                  </div>
                </div>
              </div>

              <!-- Pilihan Siswa -->
              <div class="row" id="siswa_selection" <?php echo ($data['sasaran_layanan'] == 'Satu Kelas') ? 'style="display: none;"' : ''; ?>>
                <div class="col-md-12">
                  <div class="form-group">
                    <label>Pilih Siswa</label>
                    <div class="row">
                      <div class="col-md-6">
                        <select name="siswa_ids[]" class="form-control" multiple id="siswa_ids" style="height: 200px;">
                          <?php
                          $siswa = mysqli_query($koneksi,"SELECT DISTINCT s.*, k.kelas_nama 
                                                          FROM siswa s 
                                                          JOIN kelas_siswa ks ON s.siswa_id = ks.ks_siswa 
                                                          JOIN kelas k ON ks.ks_kelas = k.kelas_id 
                                                          WHERE k.kelas_ta = '$active_ta_id' 
                                                          AND s.siswa_status = 'aktif'
                                                          ORDER BY s.siswa_nama");
                          while($s = mysqli_fetch_assoc($siswa)) {
                            $selected = (in_array($s['siswa_id'], $selected_siswa)) ? 'selected' : '';
                            echo "<option value='".$s['siswa_id']."' $selected>".$s['siswa_nama']." - ".$s['kelas_nama']."</option>";
                          }
                          ?>
                        </select>
                        <small class="text-muted">Gunakan Ctrl+Click untuk memilih multiple siswa</small>
                      </div>
                      <div class="col-md-6">
                        <div class="form-group">
                          <label>Jumlah Peserta</label>
                          <input type="number" name="jumlah_peserta" class="form-control" id="jumlah_peserta" 
                                 value="<?php echo $data['jumlah_peserta']; ?>" min="1">
                          <small class="text-muted">Jumlah akan terisi otomatis berdasarkan pilihan siswa</small>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Dokumentasi & Evaluasi -->
              <div class="row">
                <div class="col-md-12">
                  <div class="form-group">
                    <label>Uraian Singkat Kegiatan</label>
                    <textarea name="uraian_kegiatan" class="form-control" rows="4"><?php echo $data['uraian_kegiatan']; ?></textarea>
                  </div>
                </div>
              </div>

              <div class="row">
                <div class="col-md-6">
                  <div class="form-group">
                    <label>Evaluasi Proses (Evala-Pro)</label>
                    <textarea name="evaluasi_proses" class="form-control" rows="3"><?php echo $data['evaluasi_proses']; ?></textarea>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group">
                    <label>Evaluasi Hasil (Evala-Hasil)</label>
                    <textarea name="evaluasi_hasil" class="form-control" rows="3"><?php echo $data['evaluasi_hasil']; ?></textarea>
                  </div>
                </div>
              </div>

              <div class="row">
                <div class="col-md-12">
                  <div class="form-group">
                    <label>Lampiran/Foto Kegiatan (Opsional)</label>
                    <?php if($data['lampiran_foto']): ?>
                      <div class="form-group">
                        <p>Foto saat ini: <a href="../<?php echo $data['lampiran_foto']; ?>" target="_blank">Lihat Foto</a></p>
                      </div>
                    <?php endif; ?>
                    <input type="file" name="lampiran" class="form-control" accept="image/*">
                    <small class="text-muted">Format yang didukung: JPG, PNG, GIF. Maksimal 2MB. Kosongkan jika tidak ingin mengubah foto.</small>
                  </div>
                </div>
              </div>

            </div>
            
            <div class="box-footer">
              <button type="submit" name="update" class="btn btn-primary">
                <i class="fa fa-save"></i> Update Layanan
              </button>
              <a href="layanan_bk_detail.php?id=<?php echo $data['layanan_id']; ?>" class="btn btn-default">
                <i class="fa fa-arrow-left"></i> Kembali ke Detail
              </a>
            </div>
          </form>
        </div>
      </div>
    </div>
  </section>
</div>

<script>
$(document).ready(function() {
    // Handle sasaran layanan change
    $('#sasaran_layanan').change(function() {
        var sasaran = $(this).val();
        
        if(sasaran == 'Satu Kelas') {
            $('#kelas_selection').show();
            $('#siswa_selection').hide();
            $('#siswa_ids').removeAttr('required');
            $('#kelas_id').attr('required', 'required');
        } else if(sasaran == 'Kelompok Siswa' || sasaran == 'Individu') {
            $('#kelas_selection').hide();
            $('#siswa_selection').show();
            $('#kelas_id').removeAttr('required');
            $('#siswa_ids').attr('required', 'required');
        } else {
            $('#kelas_selection').hide();
            $('#siswa_selection').hide();
            $('#siswa_ids').removeAttr('required');
            $('#kelas_id').removeAttr('required');
        }
    });

    // Update jumlah peserta when siswa selection changes
    $('#siswa_ids').change(function() {
        var selectedCount = $(this).find('option:selected').length;
        $('#jumlah_peserta').val(selectedCount);
    });

    // Trigger change on page load
    $('#sasaran_layanan').trigger('change');
});
</script>

<?php include 'footer.php'; ?>
