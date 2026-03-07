<?php include 'header.php'; ?>

<div class="content-wrapper">

  <section class="content-header">
    <h1>
      Pelanggaran
      <small>Edit Pelanggaran</small>
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
            <h3 class="box-title">Edit Pelanggaran</h3>
            <a href="pelanggaran.php" class="btn btn-success btn-sm pull-right"><i class="fa fa-reply"></i> &nbsp Kembali</a> 
          </div>
          <div class="box-body">
            <form action="pelanggaran_update.php" method="post">
              <?php 
              $id_pelanggaran = $_GET['id'];
              $pelanggaran = mysqli_query($koneksi,"SELECT * FROM pelanggaran WHERE pelanggaran_id='$id_pelanggaran'");
              while($s=mysqli_fetch_array($pelanggaran)){
                ?>
                <div class="form-group">
                  <label>Nama Pelanggaran</label>
                  <input type="hidden" name="id" value="<?php echo $s['pelanggaran_id'] ?>">
                  <input type="text" class="form-control" name="nama" required="required" placeholder="Masukkan Nama pelanggaran.." value="<?php echo $s['pelanggaran_nama'] ?>">
                </div>
                
                <div class="form-group">
                  <label>Point</label>
                  <input type="number" class="form-control" name="point" required="required" placeholder="Masukkan Jumlah Point.." value="<?php echo $s['pelanggaran_point'] ?>">
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