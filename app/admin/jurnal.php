<?php 
include 'header_dynamic.php';

// Ambil data jurnal dengan informasi kasus dan siswa
$query_jurnal = "SELECT j.jurnal_id, j.kasus_id, j.tanggal_konseling, j.bentuk_layanan, j.uraian_sesi, j.analisis_diagnosis, j.tindakan_intervensi, j.rencana_tindak_lanjut, j.lampiran_file, j.created_at,
                 k.kasus_kode, k.judul_kasus, k.status_kasus, s.siswa_nama, s.siswa_nis, g.nama_guru_bk
                 FROM jurnal_kasus j
                 LEFT JOIN kasus_siswa k ON j.kasus_id = k.kasus_id
                 LEFT JOIN siswa s ON k.siswa_id = s.siswa_id
                 LEFT JOIN guru_bk g ON k.guru_bk_id = g.guru_bk_id
                 ORDER BY j.tanggal_konseling DESC, j.created_at DESC";

$result_jurnal = mysqli_query($koneksi, $query_jurnal);
?>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <section class="content-header">
    <h1>
      <i class="fa fa-book"></i> Data Jurnal Perkembangan
      <small>Monitoring Perkembangan Kasus Siswa</small>
    </h1>
    <ol class="breadcrumb">
      <li><a href="guru_bk_dashboard.php"><i class="fa fa-dashboard"></i> Dashboard</a></li>
      <li class="active">Data Jurnal</li>
    </ol>
  </section>

  <!-- Main content -->
  <section class="content">
    <div class="row">
      <div class="col-md-12">
        <!-- Info Boxes -->
        <div class="row">
          <div class="col-lg-3 col-xs-6">
            <div class="small-box bg-aqua">
              <div class="inner">
                <h3><?php echo mysqli_num_rows($result_jurnal); ?></h3>
                <p>Total Jurnal</p>
              </div>
              <div class="icon">
                <i class="fa fa-book"></i>
              </div>
            </div>
          </div>
          
          <div class="col-lg-3 col-xs-6">
            <div class="small-box bg-green">
              <div class="inner">
                <h3><?php 
                $query_selesai = "SELECT COUNT(*) as total FROM kasus_siswa WHERE status_kasus = 'Selesai/Tuntas'";
                $result_selesai = mysqli_query($koneksi, $query_selesai);
                $selesai = mysqli_fetch_assoc($result_selesai);
                echo $selesai['total'];
                ?></h3>
                <p>Kasus Selesai</p>
              </div>
              <div class="icon">
                <i class="fa fa-check"></i>
              </div>
            </div>
          </div>
          
          <div class="col-lg-3 col-xs-6">
            <div class="small-box bg-yellow">
              <div class="inner">
                <h3><?php 
                $query_proses = "SELECT COUNT(*) as total FROM kasus_siswa WHERE status_kasus = 'Dalam Proses'";
                $result_proses = mysqli_query($koneksi, $query_proses);
                $proses = mysqli_fetch_assoc($result_proses);
                echo $proses['total'];
                ?></h3>
                <p>Dalam Proses</p>
              </div>
              <div class="icon">
                <i class="fa fa-clock-o"></i>
              </div>
            </div>
          </div>
          
          <div class="col-lg-3 col-xs-6">
            <div class="small-box bg-red">
              <div class="inner">
                <h3><?php 
                $query_baru = "SELECT COUNT(*) as total FROM kasus_siswa WHERE status_kasus = 'Baru'";
                $result_baru = mysqli_query($koneksi, $query_baru);
                $baru = mysqli_fetch_assoc($result_baru);
                echo $baru['total'];
                ?></h3>
                <p>Kasus Baru</p>
              </div>
              <div class="icon">
                <i class="fa fa-exclamation"></i>
              </div>
            </div>
          </div>
        </div>

        <!-- Data Jurnal -->
        <div class="box box-primary">
          <div class="box-header with-border">
            <h3 class="box-title">
              <i class="fa fa-list"></i> Daftar Jurnal Perkembangan
            </h3>
            <div class="box-tools pull-right">
              <a href="jurnal_tambah.php" class="btn btn-success btn-sm">
                <i class="fa fa-plus"></i> Tambah Jurnal Baru
              </a>
            </div>
          </div>
          
          <div class="box-body">
            <?php if(mysqli_num_rows($result_jurnal) > 0) { ?>
            <div class="table-responsive">
              <table class="table table-bordered table-striped">
                <thead>
                  <tr>
                    <th width="5%">No</th>
                    <th width="15%">Tanggal Konseling</th>
                    <th width="20%">Kasus</th>
                    <th width="15%">Siswa</th>
                    <th width="15%">Bentuk Layanan</th>
                    <th width="10%">Status Kasus</th>
                    <th width="10%">Guru BK</th>
                    <th width="10%">Aksi</th>
                  </tr>
                </thead>
                <tbody>
                  <?php 
                  $no = 1;
                  // Reset result untuk loop
                  mysqli_data_seek($result_jurnal, 0);
                  while($jurnal = mysqli_fetch_assoc($result_jurnal)) { 
                  ?>
                  <tr>
                    <td><?php echo $no++; ?></td>
                    <td>
                      <span class="label label-info">
                        <?php echo date('d/m/Y', strtotime($jurnal['tanggal_konseling'])); ?>
                      </span>
                    </td>
                    <td>
                      <strong><?php echo $jurnal['kasus_kode']; ?></strong><br>
                      <small><?php echo $jurnal['judul_kasus']; ?></small>
                    </td>
                    <td>
                      <strong><?php echo $jurnal['siswa_nama']; ?></strong><br>
                      <small>NIS: <?php echo $jurnal['siswa_nis']; ?></small>
                    </td>
                    <td><?php echo isset($jurnal['bentuk_layanan']) && $jurnal['bentuk_layanan'] !== '' ? $jurnal['bentuk_layanan'] : '-'; ?></td>
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
                    <td><?php echo $jurnal['nama_guru_bk']; ?></td>
                    <td>
                      <a href="jurnal_detail.php?id=<?php echo $jurnal['jurnal_id']; ?>" 
                         class="btn btn-info btn-xs" title="Lihat Detail">
                        <i class="fa fa-eye"></i>
                      </a>
                      <a href="jurnal_edit.php?id=<?php echo $jurnal['jurnal_id']; ?>" 
                         class="btn btn-warning btn-xs" title="Edit">
                        <i class="fa fa-edit"></i>
                      </a>
                      <a href="kasus_siswa_detail.php?id=<?php echo $jurnal['kasus_id']; ?>" 
                         class="btn btn-primary btn-xs" title="Lihat Kasus">
                        <i class="fa fa-folder-open"></i>
                      </a>
                    </td>
                  </tr>
                  <?php } ?>
                </tbody>
              </table>
            </div>
            <?php } else { ?>
            <div class="alert alert-info">
              <i class="fa fa-info-circle"></i> Belum ada jurnal perkembangan yang tercatat.
              <a href="jurnal_tambah.php" class="btn btn-success btn-sm pull-right">
                <i class="fa fa-plus"></i> Tambah Jurnal Pertama
              </a>
            </div>
            <?php } ?>
          </div>
        </div>
      </div>
    </div>
  </section>
</div>

<?php include 'footer.php'; ?>
