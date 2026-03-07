<?php include 'header.php'; ?>

<div class="content-wrapper">

  <section class="content-header">
    <h1>
      Pelanggaran
      <small>Data Pelanggaran</small>
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
            <h3 class="box-title">Pelanggaran</h3>             
            <div class="btn-group pull-right">
              <button type="button" class="btn btn-info btn-sm" data-toggle="modal" data-target="#modal_import">
               <i class="fa fa-upload"></i> &nbsp Import Excel
             </button>
              <a href="template_pelanggaran_master.php" class="btn btn-warning btn-sm">
               <i class="fa fa-download"></i> &nbsp Download Template
             </a>
              <button type="button" class="btn btn-success btn-sm" data-toggle="modal" data-target="#modal_pelanggaran">
               <i class="fa fa-plus"></i> &nbsp Tambah Pelanggaran Baru
             </button>
            </div>

           <!-- The Modal -->
           <div class="modal" id="modal_pelanggaran">
            <div class="modal-dialog">
              <div class="modal-content">

                <!-- Modal Header -->
                <div class="modal-header">
                  <button type="button" class="close" data-dismiss="modal">&times;</button>
                  <h4 class="modal-title">Pelanggaran Baru</h4>
                </div>

                <!-- Modal body -->
                <div class="modal-body">
                  <form action="pelanggaran_act.php" method="post">

                    <div class="form-group">
                      <label>Nama Pelanggaran</label>
                      <input type="text" class="form-control" name="nama" required="required" placeholder="Masukkan Nama Pelanggaran..">
                    </div>

                     <div class="form-group">
                      <label>Point</label>
                      <input type="number" class="form-control" name="point" required="required" placeholder="Masukkan Jumlah Point..">
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
        
        <!-- Modal Import Excel -->
        <div class="modal" id="modal_import">
          <div class="modal-dialog">
            <div class="modal-content">
              <!-- Modal Header -->
              <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Import Data Pelanggaran dari Excel</h4>
              </div>
              <!-- Modal body -->
              <div class="modal-body">
                <div class="alert alert-info">
                  <i class="fa fa-info-circle"></i>
                  <strong>Petunjuk Import:</strong><br>
                  1. Download template Excel terlebih dahulu<br>
                  2. Isi data pelanggaran sesuai format template<br>
                  3. Upload file Excel yang sudah diisi<br>
                  4. Data akan otomatis ditambahkan ke master pelanggaran
                </div>
                
                <form method="POST" action="import_pelanggaran_master.php" enctype="multipart/form-data">
                  <div class="form-group">
                    <label>Pilih File Excel:</label>
                    <input type="file" name="file_excel" class="form-control" accept=".xlsx" required>
                    <small class="text-muted">Format file: .xlsx (download template terlebih dahulu)</small>
                  </div>
                  
                  <div class="form-group">
                    <button type="submit" class="btn btn-primary">
                      <i class="fa fa-upload"></i> Import Data
                    </button>
                    <button type="button" class="btn btn-default" data-dismiss="modal">Batal</button>
                  </div>
                </form>
              </div>
            </div>
          </div>
        </div>
        
        <div class="box-body">
          <?php 
          if(isset($_GET['alert']) && isset($_GET['msg'])){
            $alert = $_GET['alert'];
            $msg = $_GET['msg'];
            
            if($alert == 'success'){
              echo '<div class="alert alert-success alert-dismissible">
                      <button type="button" class="close" data-dismiss="alert">&times;</button>
                      <i class="fa fa-check-circle"></i> '.$msg.'
                    </div>';
            } else if($alert == 'warning'){
              echo '<div class="alert alert-warning alert-dismissible">
                      <button type="button" class="close" data-dismiss="alert">&times;</button>
                      <i class="fa fa-exclamation-triangle"></i> '.$msg.'
                    </div>';
            } else if($alert == 'error'){
              echo '<div class="alert alert-danger alert-dismissible">
                      <button type="button" class="close" data-dismiss="alert">&times;</button>
                      <i class="fa fa-times-circle"></i> '.$msg.'
                    </div>';
            }
          }
          ?>
          <div class="table-responsive">
            <table class="table table-bordered table-striped" id="table-datatable">
              <thead>
                <tr>
                  <th width="1%">NO</th>
                  <th>NAMA PELANGGARAN</th>
                  <th>POINT</th>
                  <th width="10%">OPSI</th>
                </tr>
              </thead>
              <tbody>
                <?php 
                $no=1;
                $data = mysqli_query($koneksi,"SELECT * FROM pelanggaran");
                while($d = mysqli_fetch_array($data)){
                  ?>
                  <tr>
                    <td><?php echo $no++; ?></td>
                    <td><?php echo $d['pelanggaran_nama']; ?></td>
                    <td><?php echo $d['pelanggaran_point']; ?> Point</td>
                    <td>           
                        <a class="btn btn-warning btn-sm" href="pelanggaran_edit.php?id=<?php echo $d['pelanggaran_id'] ?>"><i class="fa fa-cog"></i></a>
                        <a onclick="return confirm('Data yang terhubung akan ikut dihapus')" class="btn btn-danger btn-sm" href="pelanggaran_hapus.php?id=<?php echo $d['pelanggaran_id'] ?>"><i class="fa fa-trash"></i></a>
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