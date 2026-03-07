<?php 
include 'header_dynamic.php';

// Ambil ID kasus dari URL
$kasus_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if($kasus_id == 0) {
    header("location:kasus_siswa.php?alert=gagal&pesan=ID kasus tidak valid");
    exit();
}

// Ambil data kasus
$query_kasus = "SELECT k.*, s.siswa_nama, s.siswa_nis 
                FROM kasus_siswa k 
                LEFT JOIN siswa s ON k.siswa_id = s.siswa_id 
                WHERE k.kasus_id = '$kasus_id'";
$result_kasus = mysqli_query($koneksi, $query_kasus);

if(mysqli_num_rows($result_kasus) == 0) {
    header("location:kasus_siswa.php?alert=gagal&pesan=Kasus tidak ditemukan");
    exit();
}

$kasus = mysqli_fetch_assoc($result_kasus);
?>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <section class="content-header">
    <h1>
      <i class="fa fa-edit"></i> Edit Kasus Siswa
      <small><?php echo $kasus['kasus_kode']; ?></small>
    </h1>
    <ol class="breadcrumb">
      <li><a href="index.php"><i class="fa fa-dashboard"></i> Home</a></li>
      <li><a href="kasus_siswa.php">Data Kasus Siswa</a></li>
      <li class="active">Edit Kasus</li>
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
              <i class="fa fa-folder-edit"></i> Form Edit Kasus
            </h3>
          </div>
          
          <form role="form" method="POST" action="kasus_siswa_update.php">
            <input type="hidden" name="kasus_id" value="<?php echo $kasus_id; ?>">
            
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
                        $selected = ($siswa['siswa_id'] == $kasus['siswa_id']) ? 'selected' : '';
                        echo "<option value='".$siswa['siswa_id']."' $selected>".$siswa['siswa_nama']." (".$siswa['siswa_nis'].")</option>";
                      }
                      ?>
                    </select>
                  </div>

                  <div class="form-group">
                    <label for="tanggal_pelaporan">Tanggal Pelaporan <span class="text-red">*</span></label>
                    <input type="date" class="form-control" name="tanggal_pelaporan" id="tanggal_pelaporan" 
                           value="<?php echo $kasus['tanggal_pelaporan']; ?>" required>
                  </div>

                  <div class="form-group">
                    <label for="sumber_kasus">Sumber Kasus/Rujukan <span class="text-red">*</span></label>
                    <select class="form-control" name="sumber_kasus" id="sumber_kasus" required>
                      <option value="">-- Pilih Sumber --</option>
                      <option value="Wali Kelas" <?php echo ($kasus['sumber_kasus'] == 'Wali Kelas') ? 'selected' : ''; ?>>Wali Kelas</option>
                      <option value="Guru Mapel" <?php echo ($kasus['sumber_kasus'] == 'Guru Mapel') ? 'selected' : ''; ?>>Guru Mapel</option>
                      <option value="Orang Tua" <?php echo ($kasus['sumber_kasus'] == 'Orang Tua') ? 'selected' : ''; ?>>Orang Tua</option>
                      <option value="Inisiatif Siswa" <?php echo ($kasus['sumber_kasus'] == 'Inisiatif Siswa') ? 'selected' : ''; ?>>Inisiatif Siswa</option>
                      <option value="Teman" <?php echo ($kasus['sumber_kasus'] == 'Teman') ? 'selected' : ''; ?>>Teman</option>
                      <option value="Temuan Guru BK" <?php echo ($kasus['sumber_kasus'] == 'Temuan Guru BK') ? 'selected' : ''; ?>>Temuan Guru BK</option>
                    </select>
                  </div>

                  <div class="form-group">
                    <label for="kategori_masalah">Kategori Masalah <span class="text-red">*</span></label>
                    <select class="form-control" name="kategori_masalah" id="kategori_masalah" required>
                      <option value="">-- Pilih Kategori --</option>
                      <option value="Pribadi" <?php echo ($kasus['kategori_masalah'] == 'Pribadi') ? 'selected' : ''; ?>>Pribadi</option>
                      <option value="Sosial" <?php echo ($kasus['kategori_masalah'] == 'Sosial') ? 'selected' : ''; ?>>Sosial</option>
                      <option value="Belajar" <?php echo ($kasus['kategori_masalah'] == 'Belajar') ? 'selected' : ''; ?>>Belajar</option>
                      <option value="Karir" <?php echo ($kasus['kategori_masalah'] == 'Karir') ? 'selected' : ''; ?>>Karir</option>
                    </select>
                  </div>
                </div>

                <!-- Kolom Kanan -->
                <div class="col-md-6">
                  <div class="form-group">
                    <label for="judul_kasus">Judul/Perihal Kasus <span class="text-red">*</span></label>
                    <input type="text" class="form-control" name="judul_kasus" id="judul_kasus" 
                           value="<?php echo $kasus['judul_kasus']; ?>" required>
                  </div>

                  <div class="form-group">
                    <label for="deskripsi_awal">Deskripsi Awal Masalah</label>
                    <textarea class="form-control" name="deskripsi_awal" id="deskripsi_awal" 
                              rows="4"><?php echo $kasus['deskripsi_awal']; ?></textarea>
                  </div>

                  <div class="form-group">
                    <label for="status_kasus">Status Kasus</label>
                    <select class="form-control" name="status_kasus" id="status_kasus">
                      <option value="Baru" <?php echo ($kasus['status_kasus'] == 'Baru') ? 'selected' : ''; ?>>Baru</option>
                      <option value="Dalam Proses" <?php echo ($kasus['status_kasus'] == 'Dalam Proses') ? 'selected' : ''; ?>>Dalam Proses</option>
                      <option value="Selesai/Tuntas" <?php echo ($kasus['status_kasus'] == 'Selesai/Tuntas') ? 'selected' : ''; ?>>Selesai/Tuntas</option>
                      <option value="Dirujuk/Alih Tangan Kasus" <?php echo ($kasus['status_kasus'] == 'Dirujuk/Alih Tangan Kasus') ? 'selected' : ''; ?>>Dirujuk/Alih Tangan Kasus</option>
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
                        $selected = ($guru['user_id'] == $kasus['guru_bk_id']) ? 'selected' : '';
                        echo "<option value='".$guru['user_id']."' $selected>".$guru['user_nama']."</option>";
                      }
                      ?>
                    </select>
                  </div>
                </div>
              </div>

              <!-- Informasi Tambahan -->
              <div class="row">
                <div class="col-md-12">
                  <div class="alert alert-info">
                    <h4><i class="fa fa-info-circle"></i> Informasi Kasus</h4>
                    <p><strong>Kode Kasus:</strong> <?php echo $kasus['kasus_kode']; ?></p>
                    <p><strong>Dibuat:</strong> <?php echo date('d/m/Y H:i', strtotime($kasus['created_at'])); ?></p>
                    <p><strong>Terakhir Diupdate:</strong> <?php echo date('d/m/Y H:i', strtotime($kasus['updated_at'])); ?></p>
                  </div>
                </div>
              </div>
            </div>

            <div class="box-footer">
              <button type="submit" name="update" class="btn btn-primary">
                <i class="fa fa-save"></i> Update Kasus
              </button>
              <a href="kasus_siswa_detail.php?id=<?php echo $kasus_id; ?>" class="btn btn-default">
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
<script>
$(document).ready(function() {\n    // Pastikan sidebar tampil
    $('.main-sidebar').show();
    $('.content-wrapper').css('margin-left', '230px');
    
    // Inisialisasi sidebar menu
    $('.sidebar-menu').tree();
    
    
    // Inisialisasi Select2
    $('.select2').select2({
        placeholder: "Pilih salah satu...",
        allowClear: true
    });

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
});
</script>

<?php include 'footer.php'; ?>
