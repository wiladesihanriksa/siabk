<?php 
include 'header_dynamic.php';
include 'alert_helper.php';

// Ambil ID dari URL
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if($id <= 0) {
    header("location:kunjungan_rumah.php?alert=gagal&pesan=ID tidak valid");
    exit();
}

// Query data kunjungan yang akan diedit
$query = "SELECT k.*, s.siswa_nama, s.siswa_nis, j.jurusan_nama, kls.kelas_nama, u.user_nama as petugas_nama 
          FROM kunjungan_rumah k 
          LEFT JOIN siswa s ON k.siswa_id = s.siswa_id 
          LEFT JOIN jurusan j ON s.siswa_jurusan = j.jurusan_id 
          LEFT JOIN kelas_siswa ks ON s.siswa_id = ks.ks_siswa 
          LEFT JOIN kelas kls ON ks.ks_kelas = kls.kelas_id 
          LEFT JOIN user u ON k.petugas_bk_id = u.user_id 
          WHERE k.kunjungan_id = '$id'";

$result = mysqli_query($koneksi, $query);
if(mysqli_num_rows($result) == 0) {
    header("location:kunjungan_rumah.php?alert=gagal&pesan=Data kunjungan tidak ditemukan");
    exit();
}

$data = mysqli_fetch_assoc($result);

// Query lampiran foto
$query_lampiran = "SELECT * FROM lampiran_kunjungan WHERE kunjungan_id = '$id' ORDER BY created_at ASC";
$result_lampiran = mysqli_query($koneksi, $query_lampiran);
?>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <section class="content-header">
    <h1>
      <i class="fa fa-home"></i> Edit Kunjungan Rumah
      <small><?php echo $data['kunjungan_kode']; ?></small>
    </h1>
    <ol class="breadcrumb">
      <li><a href="index.php"><i class="fa fa-dashboard"></i> Home</a></li>
      <li><a href="kunjungan_rumah.php">Kunjungan Rumah</a></li>
      <li class="active">Edit Kunjungan</li>
    </ol>
  </section>

  <!-- Main content -->
  <section class="content">
    <?php showAlert(); ?>
    
    <div class="row">
      <div class="col-xs-12">
        <div class="box">
          <div class="box-header">
            <h3 class="box-title">
              <i class="fa fa-edit"></i> Edit Data Kunjungan Rumah
            </h3>
            <div class="box-tools pull-right">
              <a href="kunjungan_rumah_detail.php?id=<?php echo $data['kunjungan_id']; ?>" class="btn btn-info btn-sm">
                <i class="fa fa-eye"></i> Lihat Detail
              </a>
              <a href="kunjungan_rumah.php" class="btn btn-default btn-sm">
                <i class="fa fa-arrow-left"></i> Kembali
              </a>
            </div>
          </div>

          <form action="kunjungan_rumah_update.php" method="post" enctype="multipart/form-data">
            <input type="hidden" name="kunjungan_id" value="<?php echo $data['kunjungan_id']; ?>">
            
            <div class="box-body">
              <!-- Data Administrasi Kunjungan -->
              <div class="row">
                <div class="col-md-12">
                  <h4 class="text-primary">
                    <i class="fa fa-info-circle"></i> Data Administrasi Kunjungan
                  </h4>
                  <hr>
                </div>
              </div>

              <div class="row">
                <div class="col-md-6">
                  <div class="form-group">
                    <label>Siswa yang Dikunjungi <span class="text-danger">*</span></label>
                    <select name="siswa_id" class="form-control select2" required>
                      <option value="">-- Pilih Siswa --</option>
                      <?php
                      $query_siswa = "SELECT DISTINCT s.*, j.jurusan_nama, kls.kelas_nama 
                                     FROM siswa s 
                                     JOIN jurusan j ON s.siswa_jurusan = j.jurusan_id 
                                     JOIN kelas_siswa ks ON s.siswa_id = ks.ks_siswa 
                                     JOIN kelas kls ON ks.ks_kelas = kls.kelas_id 
                                     JOIN ta t ON kls.kelas_ta = t.ta_id 
                                     WHERE t.ta_status = 1 AND s.siswa_status = 'aktif' 
                                     ORDER BY s.siswa_nama";
                      $result_siswa = mysqli_query($koneksi, $query_siswa);
                      while($siswa = mysqli_fetch_assoc($result_siswa)) {
                        $selected = ($siswa['siswa_id'] == $data['siswa_id']) ? 'selected' : '';
                        echo "<option value='" . $siswa['siswa_id'] . "' " . $selected . ">" . 
                             $siswa['siswa_nama'] . " (" . $siswa['siswa_nis'] . ") - " . 
                             $siswa['kelas_nama'] . " " . $siswa['jurusan_nama'] . "</option>";
                      }
                      ?>
                    </select>
                  </div>
                </div>

                <div class="col-md-3">
                  <div class="form-group">
                    <label>Tanggal Kunjungan <span class="text-danger">*</span></label>
                    <input type="date" name="tanggal_kunjungan" class="form-control" 
                           value="<?php echo $data['tanggal_kunjungan']; ?>" required>
                  </div>
                </div>

                <div class="col-md-3">
                  <div class="form-group">
                    <label>Waktu Kunjungan <span class="text-danger">*</span></label>
                    <input type="time" name="waktu_kunjungan" class="form-control" 
                           value="<?php echo date('H:i', strtotime($data['waktu_kunjungan'])); ?>" required>
                  </div>
                </div>
              </div>

              <div class="row">
                <div class="col-md-6">
                  <div class="form-group">
                    <label>Alamat Kunjungan <span class="text-danger">*</span></label>
                    <textarea name="alamat_kunjungan" class="form-control" rows="3" 
                              placeholder="Masukkan alamat lengkap kunjungan..." required><?php echo $data['alamat_kunjungan']; ?></textarea>
                  </div>
                </div>

                <div class="col-md-6">
                  <div class="form-group">
                    <label>Petugas/Guru BK yang Berkunjung <span class="text-danger">*</span></label>
                    <select name="petugas_bk_id" class="form-control" required>
                      <option value="">-- Pilih Petugas BK --</option>
                      <?php
                      $query_petugas = "SELECT * FROM user WHERE user_level = 'administrator' OR user_level = 'guru_bk' ORDER BY user_nama";
                      $result_petugas = mysqli_query($koneksi, $query_petugas);
                      while($petugas = mysqli_fetch_assoc($result_petugas)) {
                        $selected = ($petugas['user_id'] == $data['petugas_bk_id']) ? 'selected' : '';
                        echo "<option value='" . $petugas['user_id'] . "' " . $selected . ">" . 
                             $petugas['user_nama'] . "</option>";
                      }
                      ?>
                    </select>
                  </div>
                </div>
              </div>

              <div class="row">
                <div class="col-md-12">
                  <div class="form-group">
                    <label>Tujuan Kunjungan <span class="text-danger">*</span></label>
                    <textarea name="tujuan_kunjungan" class="form-control" rows="2" 
                              placeholder="Contoh: Konfirmasi masalah absensi, Diskusi perkembangan belajar siswa dengan orang tua..." required><?php echo $data['tujuan_kunjungan']; ?></textarea>
                  </div>
                </div>
              </div>

              <!-- Hasil Kunjungan -->
              <div class="row">
                <div class="col-md-12">
                  <h4 class="text-primary">
                    <i class="fa fa-clipboard"></i> Hasil Kunjungan
                  </h4>
                  <hr>
                </div>
              </div>

              <div class="row">
                <div class="col-md-6">
                  <div class="form-group">
                    <label>Pihak yang Ditemui <span class="text-danger">*</span></label>
                    <textarea name="pihak_ditemui" class="form-control" rows="2" 
                              placeholder="Contoh: Ibu kandung dan siswa ybs, Ayah dan Kakak..." required><?php echo $data['pihak_ditemui']; ?></textarea>
                  </div>
                </div>

                <div class="col-md-6">
                  <div class="form-group">
                    <label>Hasil Observasi Lingkungan <span class="text-danger">*</span></label>
                    <textarea name="hasil_observasi" class="form-control" rows="2" 
                              placeholder="Deskripsi objektif mengenai kondisi rumah dan lingkungan sekitar..." required><?php echo $data['hasil_observasi']; ?></textarea>
                  </div>
                </div>
              </div>

              <div class="row">
                <div class="col-md-12">
                  <div class="form-group">
                    <label>Ringkasan Hasil Wawancara <span class="text-danger">*</span></label>
                    <textarea name="ringkasan_wawancara" class="form-control" rows="4" 
                              placeholder="Poin-poin penting dari percakapan dengan keluarga..." required><?php echo $data['ringkasan_wawancara']; ?></textarea>
                  </div>
                </div>
              </div>

              <div class="row">
                <div class="col-md-6">
                  <div class="form-group">
                    <label>Kesimpulan <span class="text-danger">*</span></label>
                    <textarea name="kesimpulan" class="form-control" rows="3" 
                              placeholder="Kesimpulan umum dari hasil kunjungan..." required><?php echo $data['kesimpulan']; ?></textarea>
                  </div>
                </div>

                <div class="col-md-6">
                  <div class="form-group">
                    <label>Rekomendasi/Tindak Lanjut <span class="text-danger">*</span></label>
                    <textarea name="rekomendasi_tindak_lanjut" class="form-control" rows="3" 
                              placeholder="Langkah yang perlu diambil setelah kunjungan..." required><?php echo $data['rekomendasi_tindak_lanjut']; ?></textarea>
                  </div>
                </div>
              </div>

              <!-- Lampiran Foto -->
              <div class="row">
                <div class="col-md-12">
                  <h4 class="text-primary">
                    <i class="fa fa-camera"></i> Lampiran Foto
                  </h4>
                  <hr>
                </div>
              </div>

              <!-- Foto yang sudah ada -->
              <?php if(mysqli_num_rows($result_lampiran) > 0): ?>
              <div class="row">
                <div class="col-md-12">
                  <h5>Foto yang Sudah Ada:</h5>
                  <div class="row">
                    <?php while($lampiran = mysqli_fetch_assoc($result_lampiran)): ?>
                    <div class="col-md-3 col-sm-4 col-xs-6" style="margin-bottom: 15px;">
                      <div class="thumbnail">
                        <img src="../<?php echo $lampiran['path_file']; ?>" 
                             class="img-responsive" 
                             style="height: 100px; width: 100%; object-fit: cover;"
                             alt="<?php echo $lampiran['nama_file']; ?>">
                        <div class="caption text-center">
                          <small><?php echo $lampiran['nama_file']; ?></small><br>
                          <a href="kunjungan_rumah_hapus_foto.php?id=<?php echo $lampiran['lampiran_id']; ?>&kunjungan_id=<?php echo $data['kunjungan_id']; ?>" 
                             class="btn btn-danger btn-xs" 
                             onclick="return confirm('Yakin ingin menghapus foto ini?')">
                            <i class="fa fa-trash"></i> Hapus
                          </a>
                        </div>
                      </div>
                    </div>
                    <?php endwhile; ?>
                  </div>
                </div>
              </div>
              <?php endif; ?>

              <!-- Upload foto baru -->
              <div class="row">
                <div class="col-md-12">
                  <div class="form-group">
                    <label>Tambah Foto Baru (Opsional)</label>
                    <input type="file" name="lampiran_foto[]" class="form-control" 
                           multiple accept="image/*" id="file_input">
                    <small class="text-muted">
                      <i class="fa fa-info-circle"></i> 
                      Format yang diizinkan: JPG, JPEG, PNG. Maksimal 5MB per file. 
                      Dapat mengupload beberapa foto sekaligus.
                    </small>
                  </div>
                </div>
              </div>

              <!-- Preview Foto -->
              <div class="row">
                <div class="col-md-12">
                  <div id="image_preview" class="form-group" style="display: none;">
                    <label>Preview Foto Baru:</label>
                    <div id="preview_container"></div>
                  </div>
                </div>
              </div>
            </div>

            <div class="box-footer">
              <button type="submit" name="update" class="btn btn-primary">
                <i class="fa fa-save"></i> Update Kunjungan
              </button>
              <a href="kunjungan_rumah_detail.php?id=<?php echo $data['kunjungan_id']; ?>" class="btn btn-info">
                <i class="fa fa-eye"></i> Lihat Detail
              </a>
              <a href="kunjungan_rumah.php" class="btn btn-default">
                <i class="fa fa-times"></i> Batal
              </a>
            </div>
          </form>
        </div>
      </div>
    </div>
  </section>
</div>

<!-- Script untuk Select2 dan Preview Foto -->
<script>
$(document).ready(function() {
    // Inisialisasi Select2
    $('.select2').select2({
        width: '100%'
    });

    // Preview foto sebelum upload
    $('#file_input').on('change', function() {
        var files = this.files;
        var previewContainer = $('#preview_container');
        var previewDiv = $('#image_preview');
        
        previewContainer.empty();
        
        if(files.length > 0) {
            previewDiv.show();
            
            for(var i = 0; i < files.length; i++) {
                var file = files[i];
                if(file.type.startsWith('image/')) {
                    var reader = new FileReader();
                    reader.onload = function(e) {
                        previewContainer.append(
                            '<div class="col-md-3" style="margin-bottom: 10px;">' +
                            '<img src="' + e.target.result + '" class="img-responsive img-thumbnail" style="height: 100px; width: 100%; object-fit: cover;">' +
                            '</div>'
                        );
                    };
                    reader.readAsDataURL(file);
                }
            }
        } else {
            previewDiv.hide();
        }
    });

    // Validasi form
    $('form').on('submit', function(e) {
        var isValid = true;
        
        // Cek field yang wajib diisi
        $('input[required], select[required], textarea[required]').each(function() {
            if($(this).val() === '') {
                $(this).addClass('is-invalid');
                isValid = false;
            } else {
                $(this).removeClass('is-invalid');
            }
        });
        
        if(!isValid) {
            e.preventDefault();
            alert('Mohon lengkapi semua field yang wajib diisi!');
        }
    });
});
</script>

<style>
.is-invalid {
    border-color: #dd4b39 !important;
}

.text-danger {
    color: #dd4b39 !important;
}
</style>

<?php include 'footer.php'; ?>
