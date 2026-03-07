<?php include 'header.php'; ?>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <section class="content-header">
    <h1>
      <i class="fa fa-heart"></i> Konseling Saya
      <small>Data Konseling yang Telah Diajukan</small>
    </h1>
    <ol class="breadcrumb">
      <li><a href="index.php"><i class="fa fa-dashboard"></i> Dashboard</a></li>
      <li class="active">Konseling Saya</li>
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
          <div class="box-header">
            <h3 class="box-title">Data Konseling Saya</h3>
            <div class="box-tools pull-right">
              <a href="konseling_ajukan.php" class="btn btn-primary btn-sm">
                <i class="fa fa-plus"></i> Ajukan Konseling Baru
              </a>
            </div>
          </div>
          
          <div class="box-body">
            <div class="table-responsive">
              <table class="table table-bordered table-striped" id="table-konseling">
                <thead>
                  <tr>
                    <th width="5%">No</th>
                    <th width="12%">Kode Kasus</th>
                    <th width="20%">Judul Konseling</th>
                    <th width="12%">Kategori</th>
                    <th width="12%">Status</th>
                    <th width="12%">Tanggal</th>
                    <th width="15%">Guru BK</th>
                    <th width="12%">Aksi</th>
                  </tr>
                </thead>
                <tbody>
                  <?php
                  $siswa_id = $_SESSION['id'];
                  $no = 1;
                  $query = "SELECT k.*, g.nama_guru_bk 
                           FROM kasus_siswa k 
                           LEFT JOIN guru_bk g ON k.guru_bk_id = g.guru_bk_id
                           WHERE k.siswa_id = '$siswa_id' 
                           ORDER BY k.created_at DESC";
                  $result = mysqli_query($koneksi, $query);
                  
                  if(mysqli_num_rows($result) > 0) {
                    while($data = mysqli_fetch_assoc($result)) {
                      $status_class = '';
                      switch($data['status_kasus']) {
                        case 'Baru': $status_class = 'label-warning'; break;
                        case 'Dalam Proses': $status_class = 'label-info'; break;
                        case 'Selesai/Tuntas': $status_class = 'label-success'; break;
                        case 'Dirujuk/Alih Tangan Kasus': $status_class = 'label-danger'; break;
                      }
                  ?>
                  <tr>
                    <td><?php echo $no++; ?></td>
                    <td><?php echo $data['kasus_kode']; ?></td>
                    <td><?php echo htmlspecialchars($data['judul_kasus']); ?></td>
                    <td><?php echo $data['kategori_masalah']; ?></td>
                    <td>
                      <span class="label <?php echo $status_class; ?>">
                        <?php echo $data['status_kasus']; ?>
                      </span>
                    </td>
                    <td><?php echo date('d/m/Y', strtotime($data['tanggal_pelaporan'])); ?></td>
                    <td><?php echo htmlspecialchars($data['nama_guru_bk'] ?: '-'); ?></td>
                    <td>
                      <a href="konseling_detail.php?id=<?php echo $data['kasus_id']; ?>" class="btn btn-info btn-xs">
                        <i class="fa fa-eye"></i> Detail
                      </a>
                    </td>
                  </tr>
                  <?php
                    }
                  } else {
                  ?>
                  <tr>
                    <td colspan="8" class="text-center">
                      <p class="text-muted">Belum ada konseling yang diajukan</p>
                      <a href="konseling_ajukan.php" class="btn btn-primary">
                        <i class="fa fa-plus"></i> Ajukan Konseling Pertama
                      </a>
                    </td>
                  </tr>
                  <?php
                  }
                  ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
</div>

<?php include 'footer.php'; ?>

<script>
$(function() {
  $('#table-konseling').DataTable({
    "responsive": true,
    "autoWidth": false,
    "pageLength": 10
  });
});
</script>

