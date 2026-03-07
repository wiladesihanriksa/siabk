<?php include 'header_dynamic.php'; ?>

<?php
if(!isset($_GET['id'])) {
    header("location:layanan_bk.php");
    exit();
}

$id = $_GET['id'];
$query = "SELECT l.*, k.kelas_nama, u.user_nama, u2.user_nama as dibuat_oleh_nama, u2.user_level as dibuat_oleh_level
          FROM layanan_bk l 
          LEFT JOIN kelas k ON l.kelas_id = k.kelas_id 
          LEFT JOIN user u ON l.created_by = u.user_id 
          LEFT JOIN user u2 ON l.dibuat_oleh = u2.user_id 
          WHERE l.layanan_id = ?";
$stmt = mysqli_prepare($koneksi, $query);
mysqli_stmt_bind_param($stmt, 'i', $id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if(mysqli_num_rows($result) == 0) {
    header("location:layanan_bk.php");
    exit();
}

$data = mysqli_fetch_assoc($result);

// Get peserta dengan filter tahun ajaran aktif
// Ambil tahun ajaran aktif
$query_ta = "SELECT * FROM ta WHERE ta_status = 1 LIMIT 1";
$result_ta = mysqli_query($koneksi, $query_ta);
$ta_aktif = mysqli_fetch_assoc($result_ta);
$ta_id = $ta_aktif ? $ta_aktif['ta_id'] : null;

$query_peserta = "SELECT lp.*, s.siswa_nama, k.kelas_nama 
                  FROM layanan_bk_peserta lp 
                  LEFT JOIN siswa s ON lp.siswa_id = s.siswa_id 
                  LEFT JOIN kelas_siswa ks ON s.siswa_id = ks.ks_siswa 
                  LEFT JOIN kelas k ON ks.ks_kelas = k.kelas_id 
                  WHERE lp.layanan_id = ? 
                  AND k.kelas_ta = '$ta_id'
                  ORDER BY s.siswa_nama";
$stmt_peserta = mysqli_prepare($koneksi, $query_peserta);
mysqli_stmt_bind_param($stmt_peserta, 'i', $id);
mysqli_stmt_execute($stmt_peserta);
$result_peserta = mysqli_stmt_get_result($stmt_peserta);
?>

<div class="content-wrapper">
  <section class="content-header">
    <h1>
      Detail Layanan BK
      <small><?php echo $data['topik_materi']; ?></small>
    </h1>
    <ol class="breadcrumb">
      <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
      <li><a href="layanan_bk.php">Data Layanan BK</a></li>
      <li class="active">Detail Layanan</li>
    </ol>
  </section>

  <section class="content">
    <div class="row">
      <div class="col-md-8">
        <div class="box box-primary">
          <div class="box-header with-border">
            <h3 class="box-title">
              <i class="fa fa-info-circle"></i> Informasi Layanan
            </h3>
            <div class="box-tools pull-right">
              <a href="layanan_bk_cetak_detail.php?id=<?php echo $data['layanan_id']; ?>" class="btn btn-danger btn-sm" target="_blank">
                <i class="fa fa-file-pdf-o"></i> Cetak PDF
              </a>
              <a href="layanan_bk_edit.php?id=<?php echo $data['layanan_id']; ?>" class="btn btn-warning btn-sm">
                <i class="fa fa-edit"></i> Edit
              </a>
            </div>
          </div>
          
          <div class="box-body">
            <div class="row">
              <div class="col-md-6">
                <table class="table table-striped">
                  <tr>
                    <td><strong>Tanggal Pelaksanaan</strong></td>
                    <td><?php echo date('d F Y', strtotime($data['tanggal_pelaksanaan'])); ?></td>
                  </tr>
                  <tr>
                    <td><strong>Jenis Layanan</strong></td>
                    <td>
                      <span class="label label-info"><?php echo $data['jenis_layanan']; ?></span>
                    </td>
                  </tr>
                  <tr>
                    <td><strong>Topik/Materi</strong></td>
                    <td><?php echo $data['topik_materi']; ?></td>
                  </tr>
                  <tr>
                    <td><strong>Bidang Layanan</strong></td>
                    <td>
                      <?php
                      $badge_color = '';
                      switch($data['bidang_layanan']) {
                          case 'Pribadi': $badge_color = 'label-danger'; break;
                          case 'Sosial': $badge_color = 'label-success'; break;
                          case 'Belajar': $badge_color = 'label-warning'; break;
                          case 'Karir': $badge_color = 'label-primary'; break;
                          default: $badge_color = 'label-default';
                      }
                      ?>
                      <span class="label <?php echo $badge_color; ?>"><?php echo $data['bidang_layanan']; ?></span>
                    </td>
                  </tr>
                  <tr>
                    <td><strong>Sasaran Layanan</strong></td>
                    <td>
                      <?php echo $data['sasaran_layanan']; ?>
                      <?php if($data['kelas_nama']): ?>
                        <br><small class="text-muted">Kelas: <?php echo $data['kelas_nama']; ?></small>
                      <?php endif; ?>
                    </td>
                  </tr>
                  <tr>
                    <td><strong>Jumlah Peserta</strong></td>
                    <td>
                      <span class="badge bg-blue"><?php echo $data['jumlah_peserta']; ?> orang</span>
                    </td>
                  </tr>
                  <tr>
                    <td><strong>Dibuat Oleh</strong></td>
                    <td>
                      <?php if($data['dibuat_oleh_nama']): ?>
                        <strong><?php echo $data['dibuat_oleh_nama']; ?></strong>
                      <?php else: ?>
                        <span class="text-muted">Tidak diketahui</span>
                      <?php endif; ?>
                    </td>
                  </tr>
                  <tr>
                    <td><strong>Status Layanan</strong></td>
                    <td>
                      <?php
                      $status_color = '';
                      switch($data['status_layanan']) {
                          case 'Aktif': $status_color = 'label-success'; break;
                          case 'Selesai': $status_color = 'label-info'; break;
                          case 'Dibatalkan': $status_color = 'label-danger'; break;
                          default: $status_color = 'label-default';
                      }
                      ?>
                      <span class="label <?php echo $status_color; ?>"><?php echo $data['status_layanan']; ?></span>
                    </td>
                  </tr>
                </table>
              </div>
              
              <div class="col-md-6">
                <table class="table table-striped">
                  <tr>
                    <td><strong>Dibuat Oleh</strong></td>
                    <td><?php echo $data['user_nama']; ?></td>
                  </tr>
                  <tr>
                    <td><strong>Tanggal Dibuat</strong></td>
                    <td><?php echo date('d/m/Y H:i', strtotime($data['created_at'])); ?></td>
                  </tr>
                  <tr>
                    <td><strong>Terakhir Diupdate</strong></td>
                    <td><?php echo date('d/m/Y H:i', strtotime($data['updated_at'])); ?></td>
                  </tr>
                  <?php if($data['lampiran_foto']): ?>
                  <tr>
                    <td><strong>Lampiran Foto</strong></td>
                    <td>
                      <a href="../<?php echo $data['lampiran_foto']; ?>" target="_blank" class="btn btn-info btn-xs">
                        <i class="fa fa-image"></i> Lihat Foto
                      </a>
                    </td>
                  </tr>
                  <?php endif; ?>
                </table>
              </div>
            </div>

            <div class="row">
              <div class="col-md-12">
                <h4><i class="fa fa-file-text"></i> Uraian Kegiatan</h4>
                <div class="well">
                  <?php 
                  $uraian_kegiatan = trim($data['uraian_kegiatan']);
                  if($uraian_kegiatan !== '' && $uraian_kegiatan !== NULL) {
                    echo nl2br($uraian_kegiatan); 
                  } else {
                    echo '<em class="text-muted">Tidak ada uraian kegiatan yang dicatat.</em>';
                  }
                  ?>
                </div>
              </div>
            </div>

            <div class="row">
              <div class="col-md-6">
                <h4><i class="fa fa-cogs"></i> Evaluasi Proses</h4>
                <div class="well">
                  <?php 
                  $evaluasi_proses = trim($data['evaluasi_proses']);
                  if($evaluasi_proses !== '' && $evaluasi_proses !== NULL) {
                    echo nl2br($evaluasi_proses); 
                  } else {
                    echo '<em class="text-muted">Tidak ada evaluasi proses yang dicatat.</em>';
                  }
                  ?>
                </div>
              </div>
            </div>

            <div class="row">
              <div class="col-md-6">
                <h4><i class="fa fa-check-circle"></i> Evaluasi Hasil</h4>
                <div class="well">
                  <?php 
                  $evaluasi_hasil = trim($data['evaluasi_hasil']);
                  if($evaluasi_hasil !== '' && $evaluasi_hasil !== NULL) {
                    echo nl2br($evaluasi_hasil); 
                  } else {
                    echo '<em class="text-muted">Tidak ada evaluasi hasil yang dicatat.</em>';
                  }
                  ?>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="col-md-4">
        <!-- Peserta Layanan -->
        <?php if(mysqli_num_rows($result_peserta) > 0): ?>
        <div class="box box-success">
          <div class="box-header with-border">
            <h3 class="box-title">
              <i class="fa fa-users"></i> Daftar Peserta
            </h3>
          </div>
          
          <div class="box-body">
            <div class="table-responsive">
              <table class="table table-condensed">
                <thead>
                  <tr>
                    <th>No</th>
                    <th>Nama Siswa</th>
                    <th>Kelas</th>
                  </tr>
                </thead>
                <tbody>
                  <?php
                  $no = 1;
                  while($peserta = mysqli_fetch_assoc($result_peserta)) {
                  ?>
                  <tr>
                    <td><?php echo $no++; ?></td>
                    <td><?php echo $peserta['siswa_nama']; ?></td>
                    <td><?php echo $peserta['kelas_nama']; ?></td>
                  </tr>
                  <?php } ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>
        <?php endif; ?>

        <!-- Aksi -->
        <div class="box box-default">
          <div class="box-header with-border">
            <h3 class="box-title">
              <i class="fa fa-cog"></i> Aksi
            </h3>
          </div>
          
          <div class="box-body">
            <div class="btn-group-vertical btn-block">
              <a href="layanan_bk_edit.php?id=<?php echo $data['layanan_id']; ?>" class="btn btn-warning">
                <i class="fa fa-edit"></i> Edit Layanan
              </a>
              <a href="layanan_bk_hapus.php?id=<?php echo $data['layanan_id']; ?>" 
                 class="btn btn-danger" 
                 onclick="return confirm('Yakin ingin menghapus layanan ini?')">
                <i class="fa fa-trash"></i> Hapus Layanan
              </a>
              <a href="layanan_bk.php" class="btn btn-default">
                <i class="fa fa-arrow-left"></i> Kembali ke Daftar
              </a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
</div>

<?php include 'footer.php'; ?>
