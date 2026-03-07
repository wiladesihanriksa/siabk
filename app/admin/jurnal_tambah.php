<?php 
// Setelah validasi, baru include header
include 'header_dynamic.php';

// Validasi parameter SETELAH include header
$kasus_id = isset($_GET['kasus_id']) ? (int)$_GET['kasus_id'] : 0;

if($kasus_id == 0) {
    // Jika tidak ada kasus_id, tampilkan form untuk memilih kasus
    $show_kasus_selector = true;
} else {
    $show_kasus_selector = false;
}

// Ambil data kasus jika ada kasus_id
if($kasus_id > 0) {
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
} else {
    // Jika tidak ada kasus_id, ambil semua kasus untuk dipilih
    $query_all_kasus = "SELECT k.*, s.siswa_nama, s.siswa_nis 
                        FROM kasus_siswa k 
                        LEFT JOIN siswa s ON k.siswa_id = s.siswa_id 
                        ORDER BY k.tanggal_pelaporan DESC";
    
    $result_all_kasus = mysqli_query($koneksi, $query_all_kasus);
}
?>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <section class="content-header">
    <h1>
      <i class="fa fa-plus"></i> Tambah Jurnal Perkembangan
      <?php if($kasus_id > 0): ?>
        <small>Kasus: <?php echo $kasus['kasus_kode']; ?></small>
      <?php else: ?>
        <small>Pilih Kasus Terlebih Dahulu</small>
      <?php endif; ?>
    </h1>
    <ol class="breadcrumb">
      <li><a href="index.php"><i class="fa fa-dashboard"></i> Home</a></li>
      <li><a href="kasus_siswa.php">Data Kasus Siswa</a></li>
      <?php if($kasus_id > 0): ?>
        <li><a href="kasus_siswa_detail.php?id=<?php echo $kasus_id; ?>">Detail Kasus</a></li>
      <?php endif; ?>
      <li class="active">Tambah Jurnal</li>
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
        <?php if($kasus_id > 0): ?>
        <!-- Form Jurnal jika kasus sudah dipilih -->
        <div class="box box-primary">
          <div class="box-header with-border">
            <h3 class="box-title">
              <i class="fa fa-book"></i> Form Jurnal Perkembangan
            </h3>
          </div>
          
          <form role="form" method="POST" action="jurnal_act.php" enctype="multipart/form-data">
            <input type="hidden" name="kasus_id" value="<?php echo $kasus_id; ?>">
            
            <div class="box-body">
              <!-- Informasi Kasus -->
              <div class="alert alert-info">
                <h4><i class="fa fa-info-circle"></i> Informasi Kasus</h4>
                <p><strong>Siswa:</strong> <?php echo $kasus['siswa_nama']; ?> (<?php echo $kasus['siswa_nis']; ?>)</p>
                <p><strong>Judul Kasus:</strong> <?php echo $kasus['judul_kasus']; ?></p>
                <p><strong>Status:</strong> 
                  <span class="label label-info"><?php echo $kasus['status_kasus']; ?></span>
                </p>
              </div>
        <?php else: ?>
        <!-- Form Pilih Kasus jika belum ada kasus_id -->
        <div class="box box-primary">
          <div class="box-header with-border">
            <h3 class="box-title">
              <i class="fa fa-list"></i> Pilih Kasus untuk Jurnal
            </h3>
          </div>
          
          <div class="box-body">
            <div class="alert alert-warning">
              <h4><i class="fa fa-warning"></i> Pilih Kasus Terlebih Dahulu</h4>
              <p>Silakan pilih kasus yang akan ditambahkan jurnal perkembangannya.</p>
            </div>
            
            <div class="table-responsive">
              <table class="table table-bordered table-striped">
                <thead>
                  <tr>
                    <th>No</th>
                    <th>Kode Kasus</th>
                    <th>Nama Siswa</th>
                    <th>Judul Kasus</th>
                    <th>Status</th>
                    <th>Tanggal</th>
                    <th>Aksi</th>
                  </tr>
                </thead>
                <tbody>
                  <?php 
                  $no = 1;
                  while($row = mysqli_fetch_assoc($result_all_kasus)): 
                  ?>
                  <tr>
                    <td><?php echo $no++; ?></td>
                    <td><?php echo $row['kasus_kode']; ?></td>
                    <td><?php echo $row['siswa_nama']; ?></td>
                    <td><?php echo $row['judul_kasus']; ?></td>
                    <td>
                      <span class="label label-info"><?php echo $row['status_kasus']; ?></span>
                    </td>
                    <td><?php echo date('d/m/Y', strtotime($row['tanggal_pelaporan'])); ?></td>
                    <td>
                      <a href="jurnal_tambah.php?kasus_id=<?php echo $row['kasus_id']; ?>" 
                         class="btn btn-primary btn-sm">
                        <i class="fa fa-plus"></i> Tambah Jurnal
                      </a>
                    </td>
                  </tr>
                  <?php endwhile; ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>
        <?php endif; ?>

              <div class="row">
                <div class="col-md-6">
                  <div class="form-group">
                    <label for="tanggal_konseling">Tanggal Konseling <span class="text-red">*</span></label>
                    <input type="date" class="form-control" name="tanggal_konseling" id="tanggal_konseling" 
                           value="<?php echo date('Y-m-d'); ?>" required>
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
                <label for="uraian_sesi">Uraian Sesi/Deskripsi Masalah <span class="text-red">*</span></label>
                <textarea class="form-control" name="uraian_sesi" id="uraian_sesi" 
                          rows="4" placeholder="Catatan detail mengenai apa yang dibicarakan pada sesi tersebut (fakta, keluhan siswa, pengamatan guru)..." required></textarea>
              </div>

              <div class="row">
                <div class="col-md-6">
                  <div class="form-group">
                    <label for="bentuk_layanan">Bentuk Layanan <span class="text-red">*</span></label>
                    <select class="form-control" name="bentuk_layanan" id="bentuk_layanan" required>
                      <option value="">- Pilih Bentuk Layanan -</option>
                      <option value="Konseling Individu">Konseling Individu</option>
                      <option value="Konseling Kelompok">Konseling Kelompok</option>
                      <option value="Konsultasi Orang Tua/Wali">Konsultasi Orang Tua/Wali</option>
                      <option value="Koordinasi dengan Wali Kelas">Koordinasi dengan Wali Kelas</option>
                      <option value="Home Visit">Home Visit</option>
                      <option value="Rujukan">Rujukan</option>
                      <option value="Lainnya">Lainnya (isi manual)</option>
                    </select>
                  </div>
                </div>
                <div class="col-md-6" id="bentuk_layanan_lainnya_wrap" style="display:none;">
                  <div class="form-group">
                    <label for="bentuk_layanan_lainnya">Bentuk Layanan (Lainnya)</label>
                    <input type="text" class="form-control" id="bentuk_layanan_lainnya" placeholder="Isi nama layanan lain...">
                    <small class="text-muted">Jika memilih "Lainnya", isi nama layanan di sini.</small>
                  </div>
                </div>
              </div>

              <div class="form-group">
                <label for="analisis_diagnosis">Analisis/Diagnosis Awal</label>
                <textarea class="form-control" name="analisis_diagnosis" id="analisis_diagnosis" 
                          rows="3" placeholder="Analisis singkat dari guru BK mengenai akar permasalahan berdasarkan sesi tersebut..."></textarea>
              </div>

              <div class="form-group">
                <label for="tindakan_intervensi">Tindakan/Intervensi yang Diberikan</label>
                <textarea class="form-control" name="tindakan_intervensi" id="tindakan_intervensi" 
                          rows="3" placeholder="Layanan yang diberikan pada sesi itu (misal: Konseling individu, pemberian nasihat, latihan relaksasi)..."></textarea>
              </div>

              <div class="form-group">
                <label for="rencana_tindak_lanjut">Rencana Tindak Lanjut (RTL)</label>
                <textarea class="form-control" name="rencana_tindak_lanjut" id="rencana_tindak_lanjut" 
                          rows="3" placeholder="Langkah selanjutnya yang akan dilakukan (misal: 'Menjadwalkan pertemuan berikutnya minggu depan', 'Menghubungi wali kelas', 'Merencanakan home visit')..."></textarea>
              </div>

              <!-- Notifikasi RTL -->
              <div class="form-group">
                <div class="checkbox">
                  <label>
                    <input type="checkbox" name="buat_reminder" id="buat_reminder" value="1">
                    <strong>Buat Pengingat RTL</strong>
                  </label>
                </div>
                <div id="reminder_options" style="display: none;">
                  <div class="row">
                    <div class="col-md-6">
                      <label for="tanggal_reminder">Tanggal Pengingat:</label>
                      <input type="date" class="form-control" name="tanggal_reminder" id="tanggal_reminder">
                    </div>
                    <div class="col-md-6">
                      <label for="pesan_reminder">Pesan Pengingat:</label>
                      <input type="text" class="form-control" name="pesan_reminder" id="pesan_reminder" 
                             placeholder="Pesan pengingat untuk RTL ini...">
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <div class="box-footer">
              <button type="submit" name="simpan" class="btn btn-primary">
                <i class="fa fa-save"></i> Simpan Jurnal
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

<!-- Script untuk validasi dan interaksi -->
<script>
$(document).ready(function() {\n    // Pastikan sidebar tampil
    $('.main-sidebar').show();
    $('.content-wrapper').css('margin-left', '230px');
    
    // Inisialisasi sidebar menu
    $('.sidebar-menu').tree();
    
    // Bentuk layanan lainnya toggle
    $('#bentuk_layanan').on('change', function(){
        if($(this).val() === 'Lainnya') {
            $('#bentuk_layanan_lainnya_wrap').slideDown();
            $('#bentuk_layanan_lainnya').attr('required', true);
        } else {
            if($('#bentuk_layanan_lainnya').val() && $(this).val() !== '') {
                $('#bentuk_layanan_lainnya').val('');
            }
            $('#bentuk_layanan_lainnya_wrap').slideUp();
            $('#bentuk_layanan_lainnya').attr('required', false);
        }
    });

    
    // Toggle reminder options
    $('#buat_reminder').on('change', function() {
        if($(this).is(':checked')) {
            $('#reminder_options').slideDown();
            // Set default tanggal reminder (7 hari dari sekarang)
            var nextWeek = new Date();
            nextWeek.setDate(nextWeek.getDate() + 7);
            $('#tanggal_reminder').val(nextWeek.toISOString().split('T')[0]);
        } else {
            $('#reminder_options').slideUp();
        }
    });

    // Validasi form
    $('form').on('submit', function(e) {
        var tanggal_konseling = $('#tanggal_konseling').val();
        var uraian_sesi = $('#uraian_sesi').val();
        var bentuk = $('#bentuk_layanan').val();

        if(!tanggal_konseling || !uraian_sesi || !bentuk) {
            e.preventDefault();
            alert('Mohon lengkapi field yang wajib diisi!');
            return false;
        }

        if(bentuk === 'Lainnya') {
            var custom = $('#bentuk_layanan_lainnya').val().trim();
            if(!custom) {
                e.preventDefault();
                alert('Silakan isi Bentuk Layanan (Lainnya).');
                return false;
            }
            var opt = new Option(custom, custom, true, true);
            $('#bentuk_layanan').append(opt);
        }

        // Validasi tanggal konseling tidak boleh lebih dari hari ini
        var today = new Date().toISOString().split('T')[0];
        if(tanggal_konseling > today) {
            e.preventDefault();
            alert('Tanggal konseling tidak boleh lebih dari hari ini!');
            return false;
        }
    });

    // Auto-fill pesan reminder
    $('#rencana_tindak_lanjut').on('keyup', function() {
        if($('#buat_reminder').is(':checked')) {
            var rtl = $(this).val();
            if(rtl.length > 0) {
                $('#pesan_reminder').val('RTL: ' + rtl.substring(0, 100) + (rtl.length > 100 ? '...' : ''));
            }
        }
    });
});
</script>

<?php include 'footer.php'; ?>
