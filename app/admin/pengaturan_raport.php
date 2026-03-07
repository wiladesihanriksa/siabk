<?php 
session_start();

// Check if user is logged in and is administrator
if(!isset($_SESSION['level']) || $_SESSION['level'] != "administrator"){
    header("location:../index.php?alert=access_denied&pesan=Hanya administrator yang dapat mengakses halaman ini");
    exit();
}

include 'header.php'; 
?>

<div class="content-wrapper">
  <section class="content-header">
    <h1>
      Pengaturan Raport
      <small>Konfigurasi Header dan Tanda Tangan Raport</small>
    </h1>
    <ol class="breadcrumb">
      <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
      <li><a href="laporan.php">Laporan</a></li>
      <li class="active">Pengaturan Raport</li>
    </ol>
  </section>

  <section class="content">
    <div class="row">
      <section class="col-lg-12">
        <div class="box box-primary">
          <div class="box-header">
            <h3 class="box-title">Konfigurasi Raport PDF</h3>
          </div>
          <div class="box-body">
            <?php 
            if(isset($_GET['alert'])){
              if($_GET['alert']=="success"){
                echo "<div class='alert alert-success alert-dismissible'>
                        <button type='button' class='close' data-dismiss='alert' aria-hidden='true'>&times;</button>
                        <h4><i class='icon fa fa-check'></i> Berhasil!</h4>
                        Pengaturan raport berhasil disimpan.
                      </div>";
              }
            }
            ?>

            <?php
            // Ambil data pengaturan dari database
            $pengaturan_query = mysqli_query($koneksi, "SELECT * FROM pengaturan_raport ORDER BY id DESC LIMIT 1");
            $pengaturan = mysqli_fetch_assoc($pengaturan_query);
            
            // Default values jika belum ada data
            $nama_madrasah = $pengaturan['nama_madrasah'] ?? 'Madrasah Aliyah YASMU';
            $jenis_institusi = $pengaturan['jenis_institusi'] ?? 'Madrasah';
            $alamat_madrasah = $pengaturan['alamat_madrasah'] ?? 'Jl. Kyai Sahlan I No. 24 Manyarejo';
            $kota = $pengaturan['kota'] ?? 'Gresik';
            $nama_kepala = $pengaturan['nama_kepala'] ?? 'Nur Ismawati, S.Pd.';
            $nip_kepala = $pengaturan['nip_kepala'] ?? '-';
            $nama_waka = $pengaturan['nama_waka'] ?? 'Nurul Faridah, S.Pd';
            $nip_waka = $pengaturan['nip_waka'] ?? '-';
            $nama_guru_bk = $pengaturan['nama_guru_bk'] ?? 'Guru BK';
            $judul_raport = $pengaturan['judul_raport'] ?? 'LAPORAN PRESTASI DAN PELANGGARAN SISWA';
            $sub_judul = $pengaturan['sub_judul'] ?? 'Sistem E-Point Siswa';
            
            // Ambil logo dari pengaturan aplikasi jika ada, fallback ke pengaturan raport
            $app_logo = getAppLogo($app_settings, 'gambar/sistem/logo.png');
            $logo_url = $pengaturan['logo_url'] ?? $app_logo;
            ?>

            <form method="post" action="pengaturan_raport_act.php">
              <div class="row">
                <div class="col-md-6">
                  <div class="form-group">
                    <label>Jenis Institusi</label>
                    <select name="jenis_institusi" class="form-control" required>
                      <option value="Madrasah" <?php echo ($jenis_institusi == 'Madrasah') ? 'selected' : ''; ?>>Madrasah</option>
                      <option value="Sekolah" <?php echo ($jenis_institusi == 'Sekolah') ? 'selected' : ''; ?>>Sekolah</option>
                    </select>
                    <p class="help-block">Pilih jenis institusi untuk menentukan label di raport</p>
                  </div>

                  <div class="form-group">
                    <label>Nama Institusi</label>
                    <input type="text" name="nama_madrasah" class="form-control" value="<?php echo htmlspecialchars($nama_madrasah); ?>" required>
                    <p class="help-block">Nama lengkap institusi (contoh: Madrasah Aliyah YASMU atau SMP Negeri 1 Gresik)</p>
                  </div>
                  
                  <div class="form-group">
                    <label>Alamat Institusi</label>
                    <textarea name="alamat_madrasah" class="form-control" rows="3" required><?php echo htmlspecialchars($alamat_madrasah); ?></textarea>
                  </div>

                  <div class="form-group">
                    <label>Kota</label>
                    <input type="text" name="kota" class="form-control" value="<?php echo htmlspecialchars($kota); ?>" required>
                  </div>

                  <div class="form-group">
                    <label id="label_nama_kepala">Nama Kepala <?php echo $jenis_institusi; ?></label>
                    <input type="text" name="nama_kepala" class="form-control" value="<?php echo htmlspecialchars($nama_kepala); ?>" required>
                  </div>

                  <div class="form-group">
                    <label id="label_nip_kepala">NIP Kepala <?php echo $jenis_institusi; ?></label>
                    <input type="text" name="nip_kepala" class="form-control" value="<?php echo htmlspecialchars($nip_kepala); ?>" required>
                  </div>
                </div>

                <div class="col-md-6">
                  <div class="form-group">
                    <label>Nama Waka Kesiswaan</label>
                    <input type="text" name="nama_waka" class="form-control" value="<?php echo htmlspecialchars($nama_waka); ?>" required>
                  </div>

                  <div class="form-group">
                    <label>NIP Waka Kesiswaan</label>
                    <input type="text" name="nip_waka" class="form-control" value="<?php echo htmlspecialchars($nip_waka); ?>" required>
                  </div>

                  <div class="form-group">
                    <label>Nama Guru BK</label>
                    <input type="text" name="nama_guru_bk" class="form-control" value="<?php echo htmlspecialchars($nama_guru_bk); ?>" required>
                  </div>

                  <div class="form-group">
                    <label>Judul Raport</label>
                    <input type="text" name="judul_raport" class="form-control" value="<?php echo htmlspecialchars($judul_raport); ?>" required>
                  </div>

                  <div class="form-group">
                    <label>Sub Judul</label>
                    <input type="text" name="sub_judul" class="form-control" value="<?php echo htmlspecialchars($sub_judul); ?>" required>
                  </div>

                  <div class="form-group">
                    <label>Logo Madrasah</label>
                    <div class="input-group">
                      <input type="text" name="logo_url" class="form-control" value="<?php echo htmlspecialchars($logo_url); ?>" id="logo_input">
                      <span class="input-group-btn">
                        <button type="button" class="btn btn-info" onclick="useAppLogo()">
                          <i class="fa fa-image"></i> Gunakan Logo Aplikasi
                        </button>
                      </span>
                    </div>
                    <p class="help-block">Path relatif ke file logo (contoh: gambar/sistem/logo.png)</p>
                    
                    <!-- Preview Logo -->
                    <div class="logo-preview" style="margin-top: 10px;">
                      <label>Preview Logo:</label><br>
                      <img src="../<?php echo htmlspecialchars($logo_url); ?>" alt="Logo Preview" 
                           style="max-width: 150px; max-height: 150px; border: 1px solid #ddd; padding: 5px;" 
                           id="logo_preview" 
                           onerror="this.src='../gambar/sistem/logo.png'">
                    </div>
                  </div>
                </div>
              </div>

              <div class="form-group">
                <button type="submit" class="btn btn-primary">
                  <i class="fa fa-save"></i> Simpan Pengaturan
                </button>
                <a href="laporan.php" class="btn btn-default">
                  <i class="fa fa-arrow-left"></i> Kembali ke Laporan
                </a>
              </div>
            </form>
          </div>
        </div>
      </section>
    </div>
  </section>
</div>

<?php include 'footer.php'; ?>

<script>
// Fungsi untuk menggunakan logo aplikasi
function useAppLogo() {
    // Ambil logo dari pengaturan aplikasi
    var appLogo = '<?php echo htmlspecialchars($app_logo); ?>';
    
    // Set nilai input
    document.getElementById('logo_input').value = appLogo;
    
    // Update preview
    updateLogoPreview(appLogo);
    
    // Show success message
    alert('Logo aplikasi berhasil diterapkan!');
}

// Fungsi untuk update preview logo
function updateLogoPreview(logoPath) {
    var preview = document.getElementById('logo_preview');
    preview.src = '../' + logoPath;
    preview.onerror = function() {
        this.src = '../gambar/sistem/logo.png';
    };
}

// Update preview saat input berubah
document.getElementById('logo_input').addEventListener('input', function() {
    updateLogoPreview(this.value);
});

// Update label berdasarkan jenis institusi
document.querySelector('select[name="jenis_institusi"]').addEventListener('change', function() {
    var jenisInstitusi = this.value;
    var labelNamaKepala = document.getElementById('label_nama_kepala');
    var labelNipKepala = document.getElementById('label_nip_kepala');
    
    if (jenisInstitusi === 'Sekolah') {
        labelNamaKepala.textContent = 'Nama Kepala Sekolah';
        labelNipKepala.textContent = 'NIP Kepala Sekolah';
    } else {
        labelNamaKepala.textContent = 'Nama Kepala Madrasah';
        labelNipKepala.textContent = 'NIP Kepala Madrasah';
    }
});
</script>
