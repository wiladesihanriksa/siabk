<?php 
include 'header_dynamic.php';

// Ambil ID jurnal dari URL
$jurnal_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if($jurnal_id == 0) {
    header("location:jurnal.php?alert=gagal&pesan=ID jurnal tidak valid");
    exit();
}

// Ambil data jurnal dengan informasi lengkap
$query_jurnal = "SELECT j.*, k.kasus_kode, k.judul_kasus, k.status_kasus, k.deskripsi_awal, 
                        s.siswa_nama, s.siswa_nis, s.siswa_jurusan,
                        kl.kelas_nama, jr.jurusan_nama,
                        g.nama_guru_bk, g.nip_guru_bk
                 FROM jurnal_kasus j
                 LEFT JOIN kasus_siswa k ON j.kasus_id = k.kasus_id
                 LEFT JOIN siswa s ON k.siswa_id = s.siswa_id
                 LEFT JOIN kelas_siswa ks ON s.siswa_id = ks.ks_siswa
                 LEFT JOIN kelas kl ON ks.ks_kelas = kl.kelas_id
                 LEFT JOIN jurusan jr ON s.siswa_jurusan = jr.jurusan_id
                 LEFT JOIN guru_bk g ON k.guru_bk_id = g.guru_bk_id
                 WHERE j.jurnal_id = '$jurnal_id'";

$result_jurnal = mysqli_query($koneksi, $query_jurnal);

if(mysqli_num_rows($result_jurnal) == 0) {
    header("location:jurnal.php?alert=gagal&pesan=Jurnal tidak ditemukan");
    exit();
}

$jurnal = mysqli_fetch_assoc($result_jurnal);
?>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <section class="content-header">
    <h1>
      <i class="fa fa-book"></i> Detail Jurnal Perkembangan
      <small>Kasus: <?php echo $jurnal['kasus_kode']; ?></small>
    </h1>
    <ol class="breadcrumb">
      <li><a href="guru_bk_dashboard.php"><i class="fa fa-dashboard"></i> Dashboard</a></li>
      <li><a href="jurnal.php">Data Jurnal</a></li>
      <li class="active">Detail Jurnal</li>
    </ol>
  </section>

  <!-- Main content -->
  <section class="content">
    <div class="row">
      <!-- Informasi Kasus -->
      <div class="col-md-4">
        <div class="box box-info">
          <div class="box-header with-border">
            <h3 class="box-title">
              <i class="fa fa-folder-open"></i> Informasi Kasus
            </h3>
          </div>
          <div class="box-body">
            <table class="table table-condensed">
              <tr>
                <td><strong>Kode Kasus:</strong></td>
                <td><?php echo $jurnal['kasus_kode']; ?></td>
              </tr>
              <tr>
                <td><strong>Judul:</strong></td>
                <td><?php echo $jurnal['judul_kasus']; ?></td>
              </tr>
              <tr>
                <td><strong>Status:</strong></td>
                <td>
                  <?php
                  $status_class = '';
                  switch($jurnal['status_kasus']) {
                    case 'Baru': $status_class = 'label-warning'; break;
                    case 'Dalam Proses': $status_class = 'label-info'; break;
                    case 'Selesai/Tuntas': $status_class = 'label-success'; break;
                    case 'Dirujuk/Alih Tangan Kasus': $status_class = 'label-danger'; break;
                  }
                  ?>
                  <span class="label <?php echo $status_class; ?>">
                    <?php echo $jurnal['status_kasus']; ?>
                  </span>
                </td>
              </tr>
              <tr>
                <td><strong>Guru BK:</strong></td>
                <td><?php echo $jurnal['nama_guru_bk']; ?></td>
              </tr>
            </table>
          </div>
        </div>

        <!-- Informasi Siswa -->
        <div class="box box-success">
          <div class="box-header with-border">
            <h3 class="box-title">
              <i class="fa fa-user"></i> Informasi Siswa
            </h3>
          </div>
          <div class="box-body">
            <table class="table table-condensed">
              <tr>
                <td><strong>Nama:</strong></td>
                <td><?php echo $jurnal['siswa_nama']; ?></td>
              </tr>
              <tr>
                <td><strong>NIS:</strong></td>
                <td><?php echo $jurnal['siswa_nis']; ?></td>
              </tr>
              <tr>
                <td><strong>Kelas:</strong></td>
                <td><?php echo isset($jurnal['kelas_nama']) ? $jurnal['kelas_nama'] : '-'; ?></td>
              </tr>
              <tr>
                <td><strong>Jurusan:</strong></td>
                <td><?php echo isset($jurnal['jurusan_nama']) ? $jurnal['jurusan_nama'] : '-'; ?></td>
              </tr>
            </table>
          </div>
        </div>
      </div>

      <!-- Detail Jurnal -->
      <div class="col-md-8">
        <div class="box box-primary">
          <div class="box-header with-border">
            <h3 class="box-title">
              <i class="fa fa-book"></i> Detail Jurnal Perkembangan
            </h3>
            <div class="box-tools pull-right">
              <span class="label label-info">
                <?php echo date('d M Y', strtotime($jurnal['tanggal_konseling'])); ?>
              </span>
            </div>
          </div>
          
          <div class="box-body">
            <div class="row">
              <div class="col-md-6">
                <h5><i class="fa fa-calendar"></i> Tanggal Konseling</h5>
                <p><?php echo date('d F Y', strtotime($jurnal['tanggal_konseling'])); ?></p>
              </div>
              
              <div class="col-md-6">
                <h5><i class="fa fa-cogs"></i> Bentuk Layanan</h5>
                <p><?php echo $jurnal['bentuk_layanan']; ?></p>
              </div>
            </div>
            
            <hr>
            
            <div class="row">
              <div class="col-md-12">
                <h5><i class="fa fa-comment"></i> Uraian Sesi</h5>
                <div class="well">
                  <?php echo nl2br($jurnal['uraian_sesi']); ?>
                </div>
              </div>
            </div>
            
            <?php if(!empty($jurnal['analisis_diagnosis'])) { ?>
            <div class="row">
              <div class="col-md-12">
                <h5><i class="fa fa-search"></i> Analisis/Diagnosis</h5>
                <div class="well">
                  <?php echo nl2br($jurnal['analisis_diagnosis']); ?>
                </div>
              </div>
            </div>
            <?php } ?>
            
            <?php if(!empty($jurnal['tindakan_intervensi'])) { ?>
            <div class="row">
              <div class="col-md-12">
                <h5><i class="fa fa-cogs"></i> Tindakan/Intervensi</h5>
                <div class="well">
                  <?php echo nl2br($jurnal['tindakan_intervensi']); ?>
                </div>
              </div>
            </div>
            <?php } ?>
            
            <?php if(!empty($jurnal['rencana_tindak_lanjut'])) { ?>
            <div class="row">
              <div class="col-md-12">
                <h5><i class="fa fa-tasks"></i> Rencana Tindak Lanjut</h5>
                <div class="well">
                  <?php echo nl2br($jurnal['rencana_tindak_lanjut']); ?>
                </div>
              </div>
            </div>
            <?php } ?>
            
            <?php if(!empty($jurnal['lampiran_file'])) { ?>
            <div class="row">
              <div class="col-md-12">
                <h5><i class="fa fa-paperclip"></i> Lampiran</h5>
                <a href="../<?php echo $jurnal['lampiran_file']; ?>" target="_blank" class="btn btn-info">
                  <i class="fa fa-download"></i> Download File
                </a>
              </div>
            </div>
            <?php } ?>
          </div>
          
          <div class="box-footer">
            <a href="jurnal_edit.php?id=<?php echo $jurnal['jurnal_id']; ?>" class="btn btn-warning">
              <i class="fa fa-edit"></i> Edit Jurnal
            </a>
            <a href="kasus_siswa_detail.php?id=<?php echo $jurnal['kasus_id']; ?>" class="btn btn-primary">
              <i class="fa fa-folder-open"></i> Lihat Kasus Lengkap
            </a>
            <a href="jurnal.php" class="btn btn-default">
              <i class="fa fa-arrow-left"></i> Kembali ke Data Jurnal
            </a>
          </div>
        </div>
      </div>
    </div>
  </section>
</div>

<?php include 'footer.php'; ?>
