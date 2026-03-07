<?php include 'header.php'; ?>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <section class="content-header">
    <h1>
      <i class="fa fa-plus"></i> Ajukan Konseling
      <small>Form Pengajuan Konseling ke Guru BK</small>
    </h1>
    <ol class="breadcrumb">
      <li><a href="index.php"><i class="fa fa-dashboard"></i> Dashboard</a></li>
      <li><a href="konseling_saya.php">Konseling Saya</a></li>
      <li class="active">Ajukan Konseling</li>
    </ol>
  </section>

  <!-- Main content -->
  <section class="content">
    <?php 
    if(isset($_GET['alert']) && isset($_GET['msg'])){
      $alert = $_GET['alert'];
      $msg = $_GET['msg'];
      
      if($alert == 'success'){
        echo '<div class="alert alert-success alert-dismissible">
                <button type="button" class="close" data-dismiss="alert">&times;</button>
                <i class="fa fa-check-circle"></i> '.$msg.'
              </div>';
      } else if($alert == 'error'){
        echo '<div class="alert alert-danger alert-dismissible">
                <button type="button" class="close" data-dismiss="alert">&times;</button>
                <i class="fa fa-times-circle"></i> '.$msg.'
              </div>';
      }
    }
    ?>
    
    <div class="row">
      <div class="col-md-12">
        <div class="box box-primary">
          <div class="box-header with-border">
            <h3 class="box-title">Form Pengajuan Konseling</h3>
            <div class="box-tools pull-right">
              <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
            </div>
          </div>
          
          <form action="konseling_ajukan_act.php" method="POST" enctype="multipart/form-data">
            <div class="box-body">
              <div class="alert alert-info">
                <i class="fa fa-info-circle"></i>
                <strong>Petunjuk:</strong> Isi form di bawah ini untuk mengajukan konseling ke Guru BK. 
                Setelah diajukan, Guru BK akan menerima notifikasi dan akan menindaklanjuti pengajuan Anda.
              </div>
              
              <div class="row">
                <!-- Kolom Kiri -->
                <div class="col-md-6">
                  <div class="form-group">
                    <label>Nama Siswa</label>
                    <?php
                    $siswa_id = $_SESSION['id'];
                    $siswa_query = mysqli_query($koneksi, "SELECT * FROM siswa WHERE siswa_id = '$siswa_id'");
                    $siswa_data = mysqli_fetch_assoc($siswa_query);
                    ?>
                    <input type="text" class="form-control" value="<?php echo htmlspecialchars($siswa_data['siswa_nama']); ?>" readonly>
                    <input type="hidden" name="siswa_id" value="<?php echo $siswa_id; ?>">
                  </div>

                  <div class="form-group">
                    <label>Tanggal Pelaporan <span class="text-red">*</span></label>
                    <div class="input-group date">
                      <div class="input-group-addon">
                        <i class="fa fa-calendar"></i>
                      </div>
                      <input type="text" name="tanggal_pelaporan" class="form-control pull-right datepicker" value="<?php echo date('d/m/Y'); ?>" required>
                    </div>
                  </div>

                  <div class="form-group">
                    <label>Sumber Kasus/Rujukan <span class="text-red">*</span></label>
                    <select class="form-control" name="sumber_kasus" required>
                      <option value="">-- Pilih Sumber --</option>
                      <option value="Inisiatif Siswa" selected>Inisiatif Siswa</option>
                      <option value="Orang Tua">Orang Tua</option>
                      <option value="Teman">Teman</option>
                      <option value="Wali Kelas">Wali Kelas</option>
                      <option value="Guru Mapel">Guru Mapel</option>
                    </select>
                  </div>

                  <div class="form-group">
                    <label>Kategori Masalah <span class="text-red">*</span></label>
                    <select class="form-control" name="kategori_masalah" required>
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
                    <label>Judul/Perihal Konseling <span class="text-red">*</span></label>
                    <input type="text" name="judul_kasus" class="form-control" placeholder="Contoh: Kesulitan Mengikuti Pelajaran Matematika" required>
                  </div>

                  <div class="form-group">
                    <label>Deskripsi Masalah <span class="text-red">*</span></label>
                    <textarea name="deskripsi_awal" class="form-control" rows="6" placeholder="Jelaskan masalah yang ingin dikonsultasikan dengan detail..." required></textarea>
                    <small class="text-muted">Jelaskan dengan detail agar Guru BK dapat memahami masalah Anda dengan baik</small>
                  </div>

                  <div class="form-group">
                    <label>Guru BK Penanggung Jawab</label>
                    <?php
                    // Ambil kelas siswa untuk mendapatkan guru BK
                    $kelas_query = mysqli_query($koneksi, "SELECT k.*, g.guru_bk_id, g.nama_guru_bk 
                                                          FROM kelas_siswa ks 
                                                          JOIN kelas k ON ks.ks_kelas = k.kelas_id 
                                                          LEFT JOIN guru_bk g ON k.kelas_id = k.kelas_id
                                                          WHERE ks.ks_siswa = '$siswa_id' 
                                                          AND k.kelas_ta = (SELECT ta_id FROM ta WHERE ta_status = 1)
                                                          LIMIT 1");
                    
                    // Jika tidak ada guru BK khusus, ambil guru BK aktif pertama
                    $guru_bk_query = mysqli_query($koneksi, "SELECT guru_bk_id, nama_guru_bk FROM guru_bk WHERE status_guru_bk = 'Aktif' ORDER BY nama_guru_bk LIMIT 1");
                    
                    if($kelas_query && mysqli_num_rows($kelas_query) > 0) {
                      $kelas_data = mysqli_fetch_assoc($kelas_query);
                      if(!empty($kelas_data['guru_bk_id'])) {
                        $guru_bk_id = $kelas_data['guru_bk_id'];
                        $guru_bk_nama = $kelas_data['nama_guru_bk'];
                      } else {
                        $guru_bk_data = mysqli_fetch_assoc($guru_bk_query);
                        $guru_bk_id = $guru_bk_data['guru_bk_id'];
                        $guru_bk_nama = $guru_bk_data['nama_guru_bk'];
                      }
                    } else {
                      $guru_bk_data = mysqli_fetch_assoc($guru_bk_query);
                      $guru_bk_id = $guru_bk_data['guru_bk_id'];
                      $guru_bk_nama = $guru_bk_data['nama_guru_bk'];
                    }
                    
                    if($guru_bk_id) {
                      ?>
                      <input type="text" class="form-control" value="<?php echo htmlspecialchars($guru_bk_nama); ?>" readonly>
                      <input type="hidden" name="guru_bk_id" value="<?php echo $guru_bk_id; ?>">
                      <small class="text-muted">Guru BK akan otomatis ditugaskan</small>
                      <?php
                    } else {
                      ?>
                      <div class="alert alert-warning">
                        <i class="fa fa-exclamation-triangle"></i> Tidak ada Guru BK yang tersedia. Silakan hubungi administrator.
                      </div>
                      <?php
                    }
                    ?>
                  </div>

                  <div class="form-group">
                    <label>Lampiran File (Opsional)</label>
                    <input type="file" name="lampiran" class="form-control" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">
                    <small class="text-muted">Format: PDF, DOC, DOCX, JPG, PNG (Maksimal 5MB)</small>
                  </div>
                </div>
              </div>
            </div>

            <div class="box-footer">
              <button type="submit" name="simpan" class="btn btn-primary">
                <i class="fa fa-send"></i> Ajukan Konseling
              </button>
              <a href="konseling_saya.php" class="btn btn-default">
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
<script src="../assets/bower_components/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js"></script>
<script>
$(function() {
  // Datepicker
  if ($.fn.datepicker) {
    $('.datepicker').datepicker({
      autoclose: true,
      format: 'dd/mm/yyyy',
      todayHighlight: true
    });
  }
});
</script>

