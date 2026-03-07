<?php include 'header.php'; ?>

<div class="content-wrapper">

  <section class="content-header">
    <h1>
      Tahun Ajaran
      <small>Edit Tahun Ajaran</small>
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
            <h3 class="box-title">Edit Tahun Ajaran</h3>
            <a href="ta.php" class="btn btn-primary btn-sm pull-right"><i class="fa fa-reply"></i> &nbsp Kembali</a> 
          </div>
          <div class="box-body">
            <form action="ta_update.php" method="post">
              <?php 
              $id_ta = $_GET['id'];
              $ta = mysqli_query($koneksi,"SELECT * FROM ta WHERE ta_id='$id_ta'");
              while($s=mysqli_fetch_array($ta)){
                ?>
                <div class="form-group">
                  <label>Nama Tahun Ajaran</label>
                  <input type="hidden" name="id" value="<?php echo $s['ta_id'] ?>">
                  <input type="text" class="form-control" name="nama" required="required" placeholder="Misal : 2023/2024.." value="<?php echo $s['ta_nama'] ?>">
                </div>

                <div class="form-group">
                  <label>Status</label>
                  <select class="form-control" name="status" required="required">
                    <option <?php if($s['ta_status'] == "1"){ echo "selected='selected'"; } ?> value="1">Aktif / Sedang Berjalan</option>
                    <option <?php if($s['ta_status'] == "0"){ echo "selected='selected'"; } ?> value="0">Selesai / Telah Berlalu</option>
                  </select>
                </div>

                <div class="form-group">
                  <input type="submit" class="btn btn-sm btn-primary" value="Simpan">
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