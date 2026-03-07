<?php 
include 'header.php';
session_start();

// Cek session siswa
if(!isset($_SESSION['level']) || $_SESSION['level'] != "siswa"){
    header("location:../admin.php?alert=belum_login");
    exit();
}

$siswa_id = $_SESSION['user_id'];

// Query data kunjungan rumah untuk siswa ini
$query = "SELECT k.*, u.user_nama as petugas_nama 
          FROM kunjungan_rumah k 
          LEFT JOIN user u ON k.petugas_bk_id = u.user_id 
          WHERE k.siswa_id = '$siswa_id' 
          ORDER BY k.created_at DESC";

$result = mysqli_query($koneksi, $query);
?>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <section class="content-header">
    <h1>
      <i class="fa fa-home"></i> Riwayat Kunjungan Rumah
      <small>Catatan Home Visit Saya</small>
    </h1>
    <ol class="breadcrumb">
      <li><a href="index.php"><i class="fa fa-dashboard"></i> Home</a></li>
      <li class="active">Kunjungan Rumah</li>
    </ol>
  </section>

  <!-- Main content -->
  <section class="content">
    <div class="row">
      <div class="col-xs-12">
        <div class="box">
          <div class="box-header">
            <h3 class="box-title">
              <i class="fa fa-list"></i> Daftar Kunjungan Rumah
            </h3>
            <div class="box-tools pull-right">
              <span class="badge bg-blue">
                <?php echo mysqli_num_rows($result); ?> Kunjungan
              </span>
            </div>
          </div>

          <!-- Tabel Data -->
          <div class="box-body">
            <?php if(mysqli_num_rows($result) > 0): ?>
            <div class="table-responsive">
              <table class="table table-bordered table-striped">
                <thead>
                  <tr>
                    <th width="5%">No</th>
                    <th width="15%">Kode Kunjungan</th>
                    <th width="12%">Tanggal</th>
                    <th width="10%">Waktu</th>
                    <th width="25%">Tujuan Kunjungan</th>
                    <th width="15%">Petugas BK</th>
                    <th width="18%">Aksi</th>
                  </tr>
                </thead>
                <tbody>
                  <?php
                  $no = 1;
                  while($data = mysqli_fetch_assoc($result)) {
                  ?>
                  <tr>
                    <td><?php echo $no++; ?></td>
                    <td><strong><?php echo $data['kunjungan_kode']; ?></strong></td>
                    <td><?php echo date('d/m/Y', strtotime($data['tanggal_kunjungan'])); ?></td>
                    <td><?php echo date('H:i', strtotime($data['waktu_kunjungan'])); ?></td>
                    <td><?php echo substr($data['tujuan_kunjungan'], 0, 50) . '...'; ?></td>
                    <td><?php echo $data['petugas_nama']; ?></td>
                    <td>
                      <div class="btn-group">
                        <a href="kunjungan_rumah_detail.php?id=<?php echo $data['kunjungan_id']; ?>" 
                           class="btn btn-info btn-xs" title="Lihat Detail">
                          <i class="fa fa-eye"></i> Detail
                        </a>
                        <a href="kunjungan_rumah_cetak.php?id=<?php echo $data['kunjungan_id']; ?>" 
                           class="btn btn-success btn-xs" title="Cetak Laporan" target="_blank">
                          <i class="fa fa-print"></i> Cetak
                        </a>
                      </div>
                    </td>
                  </tr>
                  <?php } ?>
                </tbody>
              </table>
            </div>
            <?php else: ?>
            <div class="text-center" style="padding: 50px;">
              <i class="fa fa-home fa-3x text-muted"></i>
              <h4 class="text-muted">Belum Ada Kunjungan Rumah</h4>
              <p class="text-muted">Guru BK belum melakukan kunjungan rumah ke tempat tinggal Anda.</p>
            </div>
            <?php endif; ?>
          </div>
        </div>
      </div>
    </div>

    <!-- Informasi Tambahan -->
    <div class="row">
      <div class="col-md-12">
        <div class="box box-info">
          <div class="box-header">
            <h3 class="box-title">
              <i class="fa fa-info-circle"></i> Informasi Kunjungan Rumah
            </h3>
          </div>
          <div class="box-body">
            <div class="row">
              <div class="col-md-4">
                <h5><strong>Apa itu Kunjungan Rumah?</strong></h5>
                <p>Kunjungan rumah adalah salah satu kegiatan pendukung dalam Bimbingan dan Konseling untuk memahami konteks lingkungan siswa secara langsung.</p>
              </div>
              <div class="col-md-4">
                <h5><strong>Tujuan Kunjungan:</strong></h5>
                <ul>
                  <li>Memahami kondisi lingkungan keluarga</li>
                  <li>Mendiskusikan perkembangan siswa</li>
                  <li>Membangun komunikasi dengan orang tua</li>
                  <li>Memberikan dukungan dan solusi</li>
                </ul>
              </div>
              <div class="col-md-4">
                <h5><strong>Manfaat untuk Siswa:</strong></h5>
                <ul>
                  <li>Dukungan yang lebih personal</li>
                  <li>Koordinasi antara sekolah dan keluarga</li>
                  <li>Solusi masalah yang tepat sasaran</li>
                  <li>Perhatian khusus dari guru BK</li>
                </ul>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
</div>

<?php include 'footer.php'; ?>
