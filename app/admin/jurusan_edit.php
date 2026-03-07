<?php include 'header.php'; ?>

<div class="content-wrapper">

  <section class="content-header">
    <h1>
      Jurusan
      <small>Edit Jurusan</small>
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
            <h3 class="box-title">Edit Jurusan</h3>
            <a href="jurusan.php" class="btn btn-success btn-sm pull-right"><i class="fa fa-reply"></i> &nbsp Kembali</a> 
          </div>
          <div class="box-body">
            <form action="jurusan_update.php" method="post">
              <?php 
              $id_jurusan = $_GET['id'];
              $jurusan = mysqli_query($koneksi,"SELECT * FROM jurusan WHERE jurusan_id='$id_jurusan'");
              while($s=mysqli_fetch_array($jurusan)){
                ?>
                <div class="form-group">
                  <label>Nama Jurusan</label>
                  <input type="hidden" name="id" value="<?php echo $s['jurusan_id'] ?>">
                  <input type="text" class="form-control" name="nama" required="required" placeholder="Masukkan Nama Jurusan.." value="<?php echo $s['jurusan_nama'] ?>">
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