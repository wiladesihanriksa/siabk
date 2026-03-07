<?php 
include 'header_dynamic.php';
include 'alert_helper.php';

// Ambil ID dari URL
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if($id <= 0) {
    header("location:kunjungan_rumah.php?alert=gagal&pesan=ID tidak valid");
    exit();
}

// Query data kunjungan
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
      <i class="fa fa-home"></i> Detail Kunjungan Rumah
      <small><?php echo $data['kunjungan_kode']; ?></small>
    </h1>
    <ol class="breadcrumb">
      <li><a href="index.php"><i class="fa fa-dashboard"></i> Home</a></li>
      <li><a href="kunjungan_rumah.php">Kunjungan Rumah</a></li>
      <li class="active">Detail Kunjungan</li>
    </ol>
  </section>

  <!-- Main content -->
  <section class="content">
    <?php showAlert(); ?>
    
    <div class="row">
      <!-- Informasi Administrasi -->
      <div class="col-md-6">
        <div class="box box-primary">
          <div class="box-header">
            <h3 class="box-title">
              <i class="fa fa-info-circle"></i> Data Administrasi Kunjungan
            </h3>
            <div class="box-tools pull-right">
              <a href="kunjungan_rumah_edit.php?id=<?php echo $data['kunjungan_id']; ?>" 
                 class="btn btn-warning btn-sm">
                <i class="fa fa-edit"></i> Edit
              </a>
              <a href="kunjungan_rumah_cetak.php?id=<?php echo $data['kunjungan_id']; ?>" 
                 class="btn btn-success btn-sm" target="_blank">
                <i class="fa fa-print"></i> Cetak
              </a>
              <a href="kunjungan_rumah_hapus.php?id=<?php echo $data['kunjungan_id']; ?>" 
                 class="btn btn-danger btn-sm" 
                 onclick="return confirm('Yakin ingin menghapus kunjungan ini?')">
                <i class="fa fa-trash"></i> Hapus
              </a>
            </div>
          </div>
          <div class="box-body">
            <table class="table table-bordered">
              <tr>
                <th width="40%">Kode Kunjungan</th>
                <td><strong><?php echo $data['kunjungan_kode']; ?></strong></td>
              </tr>
              <tr>
                <th>Nama Siswa</th>
                <td><?php echo $data['siswa_nama']; ?></td>
              </tr>
              <tr>
                <th>NIS</th>
                <td><?php echo $data['siswa_nis']; ?></td>
              </tr>
              <tr>
                <th>Kelas</th>
                <td><?php echo $data['kelas_nama'] . ' ' . $data['jurusan_nama']; ?></td>
              </tr>
              <tr>
                <th>Tanggal Kunjungan</th>
                <td><?php echo date('d/m/Y', strtotime($data['tanggal_kunjungan'])); ?></td>
              </tr>
              <tr>
                <th>Waktu Kunjungan</th>
                <td><?php echo date('H:i', strtotime($data['waktu_kunjungan'])); ?></td>
              </tr>
              <tr>
                <th>Petugas BK</th>
                <td><?php echo $data['petugas_nama']; ?></td>
              </tr>
            </table>
          </div>
        </div>
      </div>

      <!-- Alamat dan Tujuan -->
      <div class="col-md-6">
        <div class="box box-info">
          <div class="box-header">
            <h3 class="box-title">
              <i class="fa fa-map-marker"></i> Lokasi & Tujuan Kunjungan
            </h3>
          </div>
          <div class="box-body">
            <table class="table table-bordered">
              <tr>
                <th width="30%">Alamat Kunjungan</th>
                <td><?php echo nl2br($data['alamat_kunjungan']); ?></td>
              </tr>
              <tr>
                <th>Tujuan Kunjungan</th>
                <td><?php echo nl2br($data['tujuan_kunjungan']); ?></td>
              </tr>
            </table>
          </div>
        </div>
      </div>
    </div>

    <div class="row">
      <!-- Hasil Kunjungan -->
      <div class="col-md-12">
        <div class="box box-success">
          <div class="box-header">
            <h3 class="box-title">
              <i class="fa fa-clipboard"></i> Hasil Kunjungan
            </h3>
          </div>
          <div class="box-body">
            <div class="row">
              <div class="col-md-6">
                <h5><strong>Pihak yang Ditemui:</strong></h5>
                <p><?php echo nl2br($data['pihak_ditemui']); ?></p>
                
                <h5><strong>Hasil Observasi Lingkungan:</strong></h5>
                <p><?php echo nl2br($data['hasil_observasi']); ?></p>
              </div>
              <div class="col-md-6">
                <h5><strong>Kesimpulan:</strong></h5>
                <p><?php echo nl2br($data['kesimpulan']); ?></p>
                
                <h5><strong>Rekomendasi/Tindak Lanjut:</strong></h5>
                <p><?php echo nl2br($data['rekomendasi_tindak_lanjut']); ?></p>
              </div>
            </div>
            
            <div class="row">
              <div class="col-md-12">
                <h5><strong>Ringkasan Hasil Wawancara:</strong></h5>
                <p><?php echo nl2br($data['ringkasan_wawancara']); ?></p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Lampiran Foto -->
    <?php if(mysqli_num_rows($result_lampiran) > 0): ?>
    <div class="row">
      <div class="col-md-12">
        <div class="box box-warning">
          <div class="box-header">
            <h3 class="box-title">
              <i class="fa fa-camera"></i> Lampiran Foto
            </h3>
          </div>
          <div class="box-body">
            <div class="row">
              <?php while($lampiran = mysqli_fetch_assoc($result_lampiran)): ?>
              <div class="col-md-3 col-sm-4 col-xs-6" style="margin-bottom: 15px;">
                <div class="thumbnail">
                  <a href="../<?php echo $lampiran['path_file']; ?>" target="_blank">
                    <img src="../<?php echo $lampiran['path_file']; ?>" 
                         class="img-responsive" 
                         style="height: 150px; width: 100%; object-fit: cover;"
                         alt="<?php echo $lampiran['nama_file']; ?>">
                  </a>
                  <div class="caption text-center">
                    <small><?php echo $lampiran['nama_file']; ?></small>
                  </div>
                </div>
              </div>
              <?php endwhile; ?>
            </div>
          </div>
        </div>
      </div>
    </div>
    <?php endif; ?>

    <!-- Informasi Sistem -->
    <div class="row">
      <div class="col-md-12">
        <div class="box box-default">
          <div class="box-header">
            <h3 class="box-title">
              <i class="fa fa-info"></i> Informasi Sistem
            </h3>
          </div>
          <div class="box-body">
            <div class="row">
              <div class="col-md-6">
                <strong>Dibuat:</strong> <?php echo date('d/m/Y H:i:s', strtotime($data['created_at'])); ?>
              </div>
              <div class="col-md-6">
                <strong>Terakhir Diupdate:</strong> <?php echo date('d/m/Y H:i:s', strtotime($data['updated_at'])); ?>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Tombol Aksi -->
    <div class="row">
      <div class="col-md-12">
        <div class="box">
          <div class="box-body text-center">
            <a href="kunjungan_rumah_edit.php?id=<?php echo $data['kunjungan_id']; ?>" 
               class="btn btn-warning">
              <i class="fa fa-edit"></i> Edit Kunjungan
            </a>
            <a href="kunjungan_rumah_cetak.php?id=<?php echo $data['kunjungan_id']; ?>" 
               class="btn btn-success" target="_blank">
              <i class="fa fa-print"></i> Cetak Laporan
            </a>
            <a href="kunjungan_rumah.php" class="btn btn-default">
              <i class="fa fa-arrow-left"></i> Kembali ke Daftar
            </a>
          </div>
        </div>
      </div>
    </div>
  </section>
</div>

<!-- Script untuk modal foto -->
<script>
$(document).ready(function() {
    // Modal untuk preview foto yang lebih besar
    $('.thumbnail img').on('click', function() {
        var src = $(this).attr('src');
        var modal = '<div class="modal fade" id="imageModal" tabindex="-1" role="dialog">' +
                    '<div class="modal-dialog modal-lg" role="document">' +
                    '<div class="modal-content">' +
                    '<div class="modal-header">' +
                    '<button type="button" class="close" data-dismiss="modal">&times;</button>' +
                    '<h4 class="modal-title">Preview Foto</h4>' +
                    '</div>' +
                    '<div class="modal-body text-center">' +
                    '<img src="' + src + '" class="img-responsive" style="max-height: 80vh;">' +
                    '</div>' +
                    '</div>' +
                    '</div>' +
                    '</div>';
        
        $('body').append(modal);
        $('#imageModal').modal('show');
        
        $('#imageModal').on('hidden.bs.modal', function() {
            $(this).remove();
        });
    });
});
</script>

<?php include 'footer.php'; ?>
