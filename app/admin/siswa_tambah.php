<?php include 'header.php'; ?>

<div class="content-wrapper">

  <section class="content-header">
    <h1>
      Siswa
      <small>Tambah Siswa Baru</small>
    </h1>
    <ol class="breadcrumb">
      <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
      <li class="active">Dashboard</li>
    </ol>
  </section>

  <section class="content">
    <div class="row">
      <section class="col-lg-6">       
        <div class="box box-primary">

          <div class="box-header">
            <h3 class="box-title">Tambah Siswa Baru</h3>
            <a href="siswa.php" class="btn btn-primary btn-sm pull-right"><i class="fa fa-reply"></i> &nbsp Kembali</a> 
          </div>
          <div class="box-body">
            <form action="siswa_act.php" method="post">

              <div class="form-group">
                <label>Nama</label>
                <input type="text" class="form-control" name="nama" required="required" placeholder="Masukkan Nama ..">
              </div>

              <div class="form-group">
                <label>NIS</label>
                <input type="number" class="form-control" name="nis" required="required" placeholder="Masukkan NIS ..">
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
                <label>Password</label>
                <input type="password" class="form-control" name="password" required="required" placeholder="Masukkan password ..">
              </div>

              <div class="form-group">
                <label>Status siswa</label>
                <select class="form-control" name="status" required="required">
                  <option value=""> - Pilih Jenis siswa - </option>
                  <option value="aktif">aktif</option>
                  <option value="tamat">tamat</option>
                  <option value="pindah">pindah</option>
                  <option value="dikeluarkan">dikeluarkan</option>
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