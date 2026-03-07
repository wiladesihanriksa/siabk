<?php include 'header.php'; ?>

<div class="content-wrapper">

  <section class="content-header">
    <h1>
      Prestasi
      <small>Edit Prestasi</small>
    </h1>
    <ol class="breadcrumb">
      <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
      <li class="active">Dashboard</li>
    </ol>
  </section>

  <section class="content">
    <div class="row">
      <section class="col-lg-6">       
        <div class="box">

          <div class="box-header">
            <h3 class="box-title">Edit Prestasi</h3>
            <a href="prestasi.php" class="btn btn-success btn-sm pull-right"><i class="fa fa-reply"></i> &nbsp Kembali</a> 
          </div>
          <div class="box-body">
            <form action="prestasi_update.php" method="post">
              <?php 
              $id_prestasi = $_GET['id'];
              $prestasi = mysqli_query($koneksi,"SELECT * FROM prestasi WHERE prestasi_id='$id_prestasi'");
              while($s=mysqli_fetch_array($prestasi)){
                ?>
                <div class="form-group">
                  <label>Nama Prestasi</label>
                  <input type="hidden" name="id" value="<?php echo $s['prestasi_id'] ?>">
                  <input type="text" class="form-control" name="nama" required="required" placeholder="Masukkan Nama prestasi.." value="<?php echo $s['prestasi_nama'] ?>">
                </div>
                
                <div class="form-group">
                  <label>Point</label>
                  <input type="number" class="form-control" name="point" required="required" placeholder="Masukkan Jumlah Point.." value="<?php echo $s['prestasi_point'] ?>">
                </div>

                <div class="form-group">
                  <input type="submit" class="btn btn-primary" value="Simpan">
                </div>
                <?php 
              }
              ?>
            </form>
          </div>

        </div>
      </section>
    </div>
  </section>

</div>
<?php include 'footer.php'; ?>