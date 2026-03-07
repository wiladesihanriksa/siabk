<?php include 'header_dynamic.php'; ?>

<div class="content-wrapper">

  <section class="content-header">
    <h1>
     Input Prestasi
     <small>Input Prestasi</small>
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
          <h3 class="box-title">Edit Prestasi</h3>
          <a href="input_prestasi.php" class="btn btn-primary btn-sm pull-right"><i class="fa fa-reply"></i> &nbsp Kembali</a> 
        </div>
        <div class="box-body">
         <?php 
         $id = $_GET['id'];              
         $prestasi = mysqli_query($koneksi,"SELECT * FROM siswa, prestasi, jurusan, input_prestasi, kelas, ta where ta_id=kelas_ta and jurusan_id=kelas_jurusan and input_prestasi.kelas=kelas_id and input_prestasi.siswa=siswa_id and input_prestasi.prestasi=prestasi_id and input_prestasi.id='$id'");
         // $prestasi = mysqli_query($koneksi, "select * from prestasi where prestasi_id='$id'");
         $pel = mysqli_fetch_assoc($prestasi);
         ?>
         <form action="input_prestasi_update.php" method="post">

          <input type="hidden" name="id" value="<?php echo $pel['id'] ?>">

          <div class="form-group">
            <label>Tahun Ajaran</label>
            <select class="form-control pilih_ta" name="ta" required="required">
              <option value="">- Pilih Tahun Ajaran</option>
              <?php 
              $ta = mysqli_query($koneksi,"select * from ta");
              while($j = mysqli_fetch_array($ta)){
                ?>
                <option <?php if($pel['ta_id'] == $j['ta_id']){ echo "selected='selected'"; } ?> value="<?php echo $j['ta_id'] ?>"><?php echo $j['ta_nama'] ?> <?php if($j['ta_status'] == "1"){ echo "(Aktif)"; } ?></option>
                <?php 
              }
              ?>
            </select>
          </div>

          <div class="form-group">
            <label>Pilih Kelas</label>
            <select class="form-control pilih_kelas" name="kelas" required="required">
              <option value="">- Pilih Kelas</option>
              <?php 
              $id_ta = $pel['ta_id'];
              $kelas = mysqli_query($koneksi,"select * from kelas, jurusan where kelas_jurusan=jurusan_id and kelas_ta='$id_ta' order by kelas_jurusan asc");
              while($k = mysqli_fetch_array($kelas)){
                ?>
                <option <?php if($pel['kelas_id'] == $k['kelas_id']){ echo "selected='selected'"; } ?> value="<?php echo $k['kelas_id'] ?>"><?php echo $k['jurusan_nama'] ?> | <?php echo $k['kelas_nama'] ?></option>
                <?php 
              }
              ?>
            </select>
          </div>

          <div class="form-group">
            <label>Siswa</label>
            <select class="form-control pilih_siswa" name="siswa" required="required">
              <option value="">- Pilih Siswa</option>
              <?php 
              $id_kelas = $pel['kelas_id'];
              // Hanya tampilkan siswa yang aktif di kelas yang dipilih
              $siswa = mysqli_query($koneksi,"SELECT s.* FROM siswa s 
                                              JOIN kelas_siswa ks ON s.siswa_id = ks.ks_siswa 
                                              WHERE ks.ks_kelas='$id_kelas' AND s.siswa_status = 'aktif' 
                                              ORDER BY s.siswa_nama ASC");
              while($k = mysqli_fetch_array($siswa)){
                ?>
                <option <?php if($pel['siswa_id'] == $k['siswa_id']){ echo "selected='selected'"; } ?> value="<?php echo $k['siswa_id'] ?>"><?php echo $k['siswa_nama'] ?> | <?php echo $k['siswa_nis'] ?></option>
                <?php 
              }
              ?>
            </select>
          </div>

          <div class="form-group">
            <label>Tanggal</label>
            <input type="date" class="form-control" name="tanggal" required="required" value="<?php echo date('Y-m-d', strtotime($pel['waktu'])) ?>">
          </div>

          <div class="form-group">
            <label>Jam</label>
            <input type="time" class="form-control" name="jam" required="required" value="<?php echo date('H:i', strtotime($pel['waktu'])) ?>">
          </div>

          <div class="form-group">
            <label>prestasi</label>
            <select class="form-control" name="prestasi" required="required">
              <option value=""> - Pilih prestasi - </option>
              <?php 
              $prestasi = mysqli_query($koneksi,"select * from prestasi order by prestasi_nama asc");
              while($j = mysqli_fetch_array($prestasi)){
                ?>
                <option <?php if($pel['prestasi_id'] == $j['prestasi_id']){ echo "selected='selected'"; } ?> value="<?php echo $j['prestasi_id'] ?>"><?php echo $j['prestasi_nama'] ?> (<?php echo $j['prestasi_point'] ?> Point)</option>
                <?php 
              }
              ?>
            </select>
          </div>

          <div class="form-group">
            <input type="submit" class="btn btn-sm btn-primary" value="Simpan">
          </div>
        </form>
      </div>

    </div>
  </section>
</div>
</section>

</div>
<?php include 'footer.php'; ?>