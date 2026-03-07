<?php include 'header.php'; ?>

<div class="content-wrapper">
  <section class="content-header">
    <h1>
      Daftar Prestasi
      <small>Referensi untuk Import Excel</small>
    </h1>
    <ol class="breadcrumb">
      <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
      <li class="active">Daftar Prestasi</li>
    </ol>
  </section>

  <section class="content">
    <div class="row">
      <section class="col-lg-12">
        <div class="box box-primary">
          <div class="box-header">
            <h3 class="box-title">Daftar Prestasi yang Tersedia</h3>
            <a href="input_prestasi.php" class="btn btn-primary btn-sm pull-right"><i class="fa fa-reply"></i> &nbsp Kembali</a>
          </div>
          <div class="box-body">
            <div class="alert alert-info">
              <i class="fa fa-info-circle"></i>
              <strong>Petunjuk:</strong> Gunakan nama prestasi yang persis sama dengan yang ada di daftar ini saat mengisi template Excel untuk import.
            </div>
            
            <div class="table-responsive">
              <table class="table table-bordered table-striped">
                <thead>
                  <tr>
                    <th width="1%">NO</th>
                    <th>NAMA PRESTASI</th>
                    <th>POINT</th>
                  </tr>
                </thead>
                <tbody>
                  <?php 
                  $no = 1;
                  $prestasi = mysqli_query($koneksi, "SELECT * FROM prestasi ORDER BY prestasi_nama ASC");
                  while($p = mysqli_fetch_array($prestasi)){
                    ?>
                    <tr>
                      <td><?php echo $no++; ?></td>
                      <td><?php echo $p['prestasi_nama']; ?></td>
                      <td><?php echo $p['prestasi_point']; ?></td>
                    </tr>
                    <?php 
                  }
                  ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </section>
    </div>
  </section>
</div>

<?php include 'footer.php'; ?>
