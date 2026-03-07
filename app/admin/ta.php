<?php 
include 'header.php';
// Hapus auto-update status agar user bisa mengubah status secara manual
// include 'functions_academic_year.php';
// updateAcademicYearStatus($koneksi);
?>

<div class="content-wrapper">

  <section class="content-header">
    <h1>
      Tahun Ajaran
      <small>Data Tahun Ajaran</small>
    </h1>
    <ol class="breadcrumb">
      <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
      <li class="active">Dashboard</li>
    </ol>
  </section>

  <section class="content">
    <div class="row">
      <section class="col-lg-10 col-lg-offset-1">
        <div class="box box-primary">

          <div class="box-header">

            <h3 class="box-title">Tahun Ajaran</h3> 

            <div class="btn-group pull-right">
              <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#modal_ta">
               <i class="fa fa-plus"></i> &nbsp Tambah Tahun Ajaran Baru
             </button>
           </div>

           <!-- The Modal -->
           <div class="modal" id="modal_ta">
            <div class="modal-dialog">
              <div class="modal-content">

                <!-- Modal Header -->
                <div class="modal-header">
                  <button type="button" class="close" data-dismiss="modal">&times;</button>
                  <h4 class="modal-title">Tahun Ajaran Baru</h4>
                </div>

                <!-- Modal body -->
                <div class="modal-body">
                  <form action="ta_act.php" method="post">

                    <div class="form-group">
                      <label>Nama Tahun Ajaran</label>
                      <input type="text" class="form-control" name="nama" required="required" placeholder="Misal : 2023/2024..">
                    </div>


                    <div class="form-group">
                      <label>Status</label>
                      <select class="form-control" name="status" required="required">
                        <option value="1">Aktif / Sedang Berjalan</option>
                        <option value="0">Selesai / Telah Berlalu</option>
                      </select>
                    </div>

                    <div class="form-group">
                      <button type="button" class="btn btn-sm btn-danger" data-dismiss="modal">Batal</button>
                      <input type="submit" class="btn btn-sm btn-primary" value="Simpan">
                    </div>
                  </form>
                </div>

              </div>
            </div>
          </div>
        </div>
        <div class="box-body">
          <div class="table-responsive">
            <table class="table table-bordered table-striped" id="table-datatable">
              <thead>
                <tr>
                  <th width="1%">NO</th>
                  <th>TAHUN AJARAN</th>
                  <th>STATUS</th>
                  <th width="10%">OPSI</th>
                </tr>
              </thead>
              <tbody>
                <?php 
                $no=1;
                $data = mysqli_query($koneksi,"SELECT * FROM ta");
                while($d = mysqli_fetch_array($data)){
                  ?>
                  <tr>
                    <td><?php echo $no++; ?></td>
                    <td><?php echo $d['ta_nama']; ?></td>
                    <td>
                      <?php 
                      if($d['ta_status'] == "0"){
                        echo "<span class='label label-success'>Selesai / Telah Berlalu</span>";
                      }else{
                        echo "<span class='label label-primary'>Aktif / Sedang Berjalan</span>";
                      } 
                      ?>
                    </td>
                    <td>           
                      <a class="btn btn-warning btn-sm" href="ta_edit.php?id=<?php echo $d['ta_id'] ?>"><i class="fa fa-cog"></i></a>
                      <a class="btn btn-danger btn-sm" href="ta_hapus.php?id=<?php echo $d['ta_id'] ?>"><i class="fa fa-trash"></i></a>
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
    </section>
  </div>
</section>

</div>
<?php include 'footer.php'; ?>