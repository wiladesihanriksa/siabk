<?php include 'header.php'; ?>

<div class="content-wrapper">

  <section class="content-header">
    <h1>
      Siswa
      <small>Edit Siswa</small>
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
            <h3 class="box-title">Edit Siswa</h3>
            <a href="siswa.php" class="btn btn-primary btn-sm pull-right"><i class="fa fa-reply"></i> &nbsp Kembali</a> 
          </div>
          <div class="box-body">
            <form action="siswa_update.php" method="post">
              <?php 
              $id = mysqli_real_escape_string($koneksi, $_GET['id']);              
              $data = mysqli_query($koneksi, "select * from siswa where siswa_id='$id'");
              while($d = mysqli_fetch_array($data)){
                ?>
                <div class="form-group">
                  <label>Nama</label>
                  <input type="hidden" name="id" value="<?php echo htmlspecialchars($d['siswa_id']); ?>">
                  <input type="text" class="form-control" name="nama" required="required" placeholder="Masukkan Nama .." value="<?php echo htmlspecialchars($d['siswa_nama'], ENT_QUOTES, 'UTF-8'); ?>">
                </div>

                <div class="form-group">
                  <label>NIS</label>
                  <input type="number" class="form-control" name="nis" required="required" placeholder="Masukkan NIS .." value="<?php echo htmlspecialchars($d['siswa_nis'], ENT_QUOTES, 'UTF-8'); ?>">
                </div>

                <div class="form-group">
                  <label>Jurusan</label>
                  <select class="form-control" name="jurusan" required="required">
                    <option value=""> - Pilih Jurusan - </option>
                    <?php 
                    $jurusan = mysqli_query($koneksi,"select * from jurusan");
                    while($j = mysqli_fetch_array($jurusan)){
                      ?>
                      <option <?php if($d['siswa_jurusan']==$j['jurusan_id']){echo "selected='selected'";} ?> value="<?php echo $j['jurusan_id'] ?>"><?php echo $j['jurusan_nama'] ?></option>
                      <?php 
                    }
                    ?>
                  </select>
                </div>

                <div class="form-group">
                  <label>Password</label>
                  <input type="password" class="form-control" name="password" placeholder="Masukkan password ..">
                  <small class="text-muted">Kosongkan jika tidak ingin mengubah password</small>
                </div>

                <div class="form-group">
                  <label>Status siswa</label>
                  <select class="form-control" name="status" required="required">
                    <option <?php if($d['siswa_status']=="aktif"){echo "selected='selected'";} ?> value="aktif">aktif</option>
                    <option <?php if($d['siswa_status']=="tamat"){echo "selected='selected'";} ?> value="tamat">tamat</option>
                    <option <?php if($d['siswa_status']=="pindah"){echo "selected='selected'";} ?> value="pindah">pindah</option>
                    <option <?php if($d['siswa_status']=="dikeluarkan"){echo "selected='selected'";} ?> value="dikeluarkan">dikeluarkan</option>
                  </select>
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