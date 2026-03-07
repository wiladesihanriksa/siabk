<?php include 'header.php'; ?>

<?php
if(!isset($_GET['id'])) {
    header("location:layanan_bk_saya.php");
    exit();
}

$id = $_GET['id'];
$siswa_id = $_SESSION['id'];

// Cek apakah siswa ini benar-benar mengikuti layanan ini
$query_check = "SELECT l.*, k.kelas_nama, u.user_nama 
                FROM layanan_bk l 
                LEFT JOIN kelas k ON l.kelas_id = k.kelas_id 
                LEFT JOIN user u ON l.created_by = u.user_id 
                LEFT JOIN layanan_bk_peserta lp ON l.layanan_id = lp.layanan_id 
                WHERE l.layanan_id = ? AND lp.siswa_id = ?";

$stmt_check = mysqli_prepare($koneksi, $query_check);
mysqli_stmt_bind_param($stmt_check, 'ii', $id, $siswa_id);
mysqli_stmt_execute($stmt_check);
$result_check = mysqli_stmt_get_result($stmt_check);

if(mysqli_num_rows($result_check) == 0) {
    header("location:layanan_bk_saya.php");
    exit();
}

$data = mysqli_fetch_assoc($result_check);

// Get peserta lain yang mengikuti layanan yang sama
$query_peserta = "SELECT lp.*, s.siswa_nama, k.kelas_nama 
                  FROM layanan_bk_peserta lp 
                  LEFT JOIN siswa s ON lp.siswa_id = s.siswa_id 
                  LEFT JOIN kelas k ON s.kelas_id = k.kelas_id 
                  WHERE lp.layanan_id = ? 
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
      <li><a href="layanan_bk_saya.php">Layanan BK Saya</a></li>
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

            <?php if($data['uraian_kegiatan']): ?>
            <div class="row">
              <div class="col-md-12">
                <h4><i class="fa fa-file-text"></i> Uraian Kegiatan</h4>
                <div class="well">
                  <?php echo nl2br($data['uraian_kegiatan']); ?>
                </div>
              </div>
            </div>
            <?php endif; ?>

            <?php if($data['evaluasi_proses']): ?>
            <div class="row">
              <div class="col-md-6">
                <h4><i class="fa fa-cogs"></i> Evaluasi Proses</h4>
                <div class="well">
                  <?php echo nl2br($data['evaluasi_proses']); ?>
                </div>
              </div>
            </div>
            <?php endif; ?>

            <?php if($data['evaluasi_hasil']): ?>
            <div class="row">
              <div class="col-md-6">
                <h4><i class="fa fa-check-circle"></i> Evaluasi Hasil</h4>
                <div class="well">
                  <?php echo nl2br($data['evaluasi_hasil']); ?>
                </div>
              </div>
            </div>
            <?php endif; ?>
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
                      $highlight = ($peserta['siswa_id'] == $siswa_id) ? 'style="background-color: #d4edda;"' : '';
                  ?>
                  <tr <?php echo $highlight; ?>>
                    <td><?php echo $no++; ?></td>
                    <td>
                      <?php echo $peserta['siswa_nama']; ?>
                      <?php if($peserta['siswa_id'] == $siswa_id): ?>
                        <span class="label label-success">Anda</span>
                      <?php endif; ?>
                    </td>
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
              <a href="layanan_bk_saya.php" class="btn btn-default">
                <i class="fa fa-arrow-left"></i> Kembali ke Daftar
              </a>
            </div>
          </div>
        </div>

        <!-- Informasi Tambahan -->
        <div class="box box-info">
          <div class="box-header with-border">
            <h3 class="box-title">
              <i class="fa fa-lightbulb-o"></i> Tips
            </h3>
          </div>
          
          <div class="box-body">
            <div class="alert alert-info">
              <h5><i class="fa fa-info"></i> Manfaat Layanan BK</h5>
              <ul class="list-unstyled">
                <li><i class="fa fa-check text-green"></i> Membantu mengatasi masalah pribadi</li>
                <li><i class="fa fa-check text-green"></i> Mengembangkan keterampilan sosial</li>
                <li><i class="fa fa-check text-green"></i> Meningkatkan prestasi belajar</li>
                <li><i class="fa fa-check text-green"></i> Merencanakan karir masa depan</li>
              </ul>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
</div>

<?php include 'footer.php'; ?>
