<?php include 'header.php'; ?>

<div class="content-wrapper">

  <section class="content-header">
    <h1>
      Kelas
      <small>Siswa Kelas</small>
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
            <h3 class="box-title">Tentang kelas</h3>
            <a href="kelas.php" class="btn btn-primary btn-sm pull-right"><i class="fa fa-reply"></i> &nbsp Kembali</a> 
          </div>
          <div class="box-body">
            <?php 
            $id = $_GET['id'];              
            $kelas = mysqli_query($koneksi, "select * from kelas, jurusan, ta where kelas_ta=ta_id and kelas_jurusan=jurusan_id and kelas_id='$id'");
            $k = mysqli_fetch_assoc($kelas);
            $id_kelas = $k['kelas_id'];
            $id_ta = $k['kelas_ta'];
            ?>

            <div class="table-responsive">
              <table class="table table-bordered">
                <tr>
                  <th width="30%">Nama Kelas</th>
                  <td><?php echo $k['kelas_nama'] ?></td>
                </tr>
                <tr>
                  <th>Jurusan</th>
                  <td><?php echo $k['jurusan_nama'] ?></td>
                </tr>
                <tr>
                  <th>Tahun Ajaran</th>
                  <td><?php echo $k['ta_nama'] ?></td>
                </tr>
              </table>
            </div>

          </div>

        </div>
      </section>
    </div>


    <div class="box box-primary">

     <div class="box-header">
      <h3 class="box-title">Siswa Kelas</h3>             
      <div class="btn-group pull-right">
        <button type="button" class="btn btn-info btn-sm" data-toggle="modal" data-target="#modal_import">
         <i class="fa fa-upload"></i> &nbsp Import Excel
       </button>
        <a href="template_siswa.php?id=<?php echo $id_kelas; ?>" class="btn btn-warning btn-sm">
         <i class="fa fa-download"></i> &nbsp Download Template
       </a>
        <button type="button" class="btn btn-success btn-sm" data-toggle="modal" data-target="#modal_jurusan">
         <i class="fa fa-plus"></i> &nbsp Tambahkan Siswa Ke Kelas
       </button>
     </div>

     <!-- The Modal -->
     <div class="modal" id="modal_jurusan">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">

          <!-- Modal Header -->
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal">&times;</button>
            <h4 class="modal-title">Pilih Siswa</h4>
          </div>

          <!-- Modal body -->
          <div class="modal-body">

            <div class="table-responsive">
              <table class="table table-bordered table-striped" id="table-datatable">
                <thead>
                  <tr>
                    <th width="1%">NO</th>
                    <th>NAMA</th>
                    <th>NIS</th>
                    <th>JURUSAN</th>
                    <th>STATUS</th>
                    <th width="10%">OPSI</th>
                  </tr>
                </thead>
                <tbody>
                  <?php 
                  $no=1;
                  $id = $_GET['id']; 
                  // Pastikan id_ta tidak kosong
                  if(!empty($id_ta)) {
                    // Cari siswa yang belum ada di tahun ajaran yang sama
                    $data = mysqli_query($koneksi,"SELECT * FROM siswa, jurusan where siswa_jurusan=jurusan_id and siswa_id not in (select ks_siswa from kelas_siswa, kelas where ks_kelas=kelas_id and kelas_ta='$id_ta') order by siswa_id desc");
                  } else {
                    // Jika id_ta kosong, tampilkan semua siswa yang belum ada di kelas manapun
                    $data = mysqli_query($koneksi,"SELECT * FROM siswa, jurusan where siswa_jurusan=jurusan_id and siswa_id not in (select ks_siswa from kelas_siswa) order by siswa_id desc");
                  }
                  while($d = mysqli_fetch_array($data)){
                    ?>
                    <tr>
                      <td><?php echo $no++; ?></td>
                      <td><?php echo $d['siswa_nama']; ?></td>
                      <td><?php echo $d['siswa_nis']; ?></td>
                      <td><?php echo $d['jurusan_nama']; ?></td>
                      <td><?php echo $d['siswa_status']; ?></td>
                      <td>                        
                        <a class="btn btn-warning btn-sm" href="kelas_siswa_act.php?siswa=<?php echo $d['siswa_id'] ?>&kelas=<?php echo $id_kelas ?>"><i class="fa fa-check"></i> Pilih</a>
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
          <h4 class="modal-title">Import Data Siswa dari Excel</h4>
        </div>

        <!-- Modal body -->
        <div class="modal-body">
          <div class="alert alert-info">
            <i class="fa fa-info-circle"></i>
            <strong>Petunjuk Import:</strong><br>
            1. Download template Excel terlebih dahulu<br>
            2. Isi data siswa sesuai format template<br>
            3. Upload file Excel yang sudah diisi<br>
            4. Data akan otomatis ditambahkan ke kelas ini
          </div>
          
          <form method="POST" action="import_siswa_csv.php" enctype="multipart/form-data">
            <input type="hidden" name="kelas_id" value="<?php echo $id_kelas; ?>">
            
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
          <th>NAMA</th>
          <th>NIS</th>
          <th>JURUSAN</th>
          <th>STATUS</th>
          <th width="10%">OPSI</th>
        </tr>
      </thead>
      <tbody>
        <?php 
        $no=1;
        $id = $_GET['id']; 
        $data = mysqli_query($koneksi,"SELECT * FROM siswa, jurusan where siswa_jurusan=jurusan_id and siswa_id in (select ks_siswa from kelas_siswa where ks_kelas='$id') order by siswa_id desc");
        while($d = mysqli_fetch_array($data)){
          ?>
          <tr>
            <td><?php echo $no++; ?></td>
            <td><?php echo $d['siswa_nama']; ?></td>
            <td><?php echo $d['siswa_nis']; ?></td>
            <td><?php echo $d['jurusan_nama']; ?></td>
            <td><?php echo $d['siswa_status']; ?></td>
            <td>                        
              <a class="btn btn-danger btn-sm" href="kelas_siswa_keluarkan.php?siswa=<?php echo $d['siswa_id'] ?>&kelas=<?php echo $id_kelas ?>"><i class="fa fa-close"></i> Keluarkan</a>
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
<?php include 'footer.php'; ?>