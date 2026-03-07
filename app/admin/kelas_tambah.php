<?php include 'header.php'; ?>

<div class="content-wrapper">

  <section class="content-header">
    <h1>
      Kelas
      <small>Tambah Kelas Baru</small>
    </h1>
    <ol class="breadcrumb">
      <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
      <li class="active">Dashboard</li>
    </ol>
  </section>

  <section class="content">
    <div class="row">
      <section class="col-lg-6 col-lg-offset-3">       
        <div class="box box-primary">

          <div class="box-header">
            <h3 class="box-title">Tambah kelas Baru</h3>
            <a href="kelas.php" class="btn btn-primary btn-sm pull-right"><i class="fa fa-reply"></i> &nbsp Kembali</a> 
          </div>
          <div class="box-body">
            <form action="kelas_act.php" method="post">

              <div class="form-group">
                <label>Nama Kelas</label>
                <input type="text" class="form-control" name="nama" required="required" placeholder="Masukkan Nama Kelas ..">
              </div>

              <div class="form-group">
                <label>Jurusan</label>
                <select class="form-control" name="jurusan" required="required">
                  <option value=""> - Pilih Jurusan - </option>
                  <?php 
                  $jurusan = mysqli_query($koneksi,"select * from jurusan");
                  while($j = mysqli_fetch_array($jurusan)){
                    ?>
                    <option value="<?php echo $j['jurusan_id'] ?>"><?php echo $j['jurusan_nama'] ?></option>
                    <?php 
                  }
                  ?>
                </select>
              </div>

              <div class="form-group">
                <label>Tahun Ajaran</label>
                <select class="form-control" name="ta" required="required">
                  <?php 
                  $ta = mysqli_query($koneksi,"select * from ta");
                  while($j = mysqli_fetch_array($ta)){
                    ?>
                    <option value="<?php echo $j['ta_id'] ?>"><?php echo $j['ta_nama'] ?> <?php if($j['ta_status'] == "1"){ echo "(Aktif)"; } ?></option>
                    <?php 
                  }
                  ?>
                </select>
              </div>

              <div class="form-group">
                <input type="submit" class="btn btn-primary" value="Simpan">
              </div>
            </form>
          </div>

        </div>
      </section>
    </div>
  </section>

</div>
<?php include 'footer.php'; ?>