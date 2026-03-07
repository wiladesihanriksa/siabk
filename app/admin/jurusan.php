<?php include 'header.php'; ?>

<div class="content-wrapper">

  <section class="content-header">
    <h1>
      Jurusan
      <small>Data Jurusan</small>
    </h1>
    <ol class="breadcrumb">
      <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
      <li class="active">Dashboard</li>
    </ol>
  </section>

  <section class="content">
    <div class="row">
      <section class="col-lg-12">
        <div class="box">

          <div class="box-header">
            <h3 class="box-title">Jurusan</h3>             
            <div class="btn-group pull-right">
              <button type="button" class="btn btn-success btn-sm" data-toggle="modal" data-target="#modal_jurusan">
               <i class="fa fa-plus"></i> &nbsp Tambah Jurusan Baru
             </button>
           </div>

           <!-- The Modal -->
           <div class="modal" id="modal_jurusan">
            <div class="modal-dialog">
              <div class="modal-content">

                <!-- Modal Header -->
                <div class="modal-header">
                  <button type="button" class="close" data-dismiss="modal">&times;</button>
                  <h4 class="modal-title">Jurusan Baru</h4>
                </div>

                <!-- Modal body -->
                <div class="modal-body">
                  <form action="jurusan_act.php" method="post">

                    <div class="form-group">
                      <label>Nama Jurusan</label>
                      <input type="text" class="form-control" name="nama" required="required" placeholder="Masukkan Nama Jurusan..">
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
                  <th>NAMA JURUSAN</th>
                  <th width="10%">OPSI</th>
                </tr>
              </thead>
              <tbody>
                <?php 
                $no=1;
                $data = mysqli_query($koneksi,"SELECT * FROM jurusan");
                while($d = mysqli_fetch_array($data)){
                  ?>
                  <tr>
                    <td><?php echo $no++; ?></td>
                    <td><?php echo $d['jurusan_nama']; ?></td>
                    <td>           
                      <a class="btn btn-warning btn-sm" href="jurusan_edit.php?id=<?php echo $d['jurusan_id'] ?>"><i class="fa fa-cog"></i></a>
                      <a onclick="return confirm('Data yang terhubung akan ikut dihapus')" class="btn btn-danger btn-sm" href="jurusan_hapus.php?id=<?php echo $d['jurusan_id'] ?>"><i class="fa fa-trash"></i></a>
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