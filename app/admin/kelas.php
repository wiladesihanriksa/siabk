<?php include 'header.php'; ?>

<div class="content-wrapper">

  <section class="content-header">
    <h1>
      Kelas
      <small>Data Kelas</small>
    </h1>
    <ol class="breadcrumb">
      <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
      <li class="active">Dashboard</li>
    </ol>
  </section>

  <section class="content">
    <div class="row">
      <section class="col-lg-4">

        <div class="box">
          <div class="box-body">

            <form method="get" action="">
              <div class="form-group">
                <label>Tahun Ajaran</label>
                <select class="form-control" name="ta" required="required">
                  <?php 
                  $ta = mysqli_query($koneksi,"select * from ta");
                  while($j = mysqli_fetch_array($ta)){
                    ?>
                    <option <?php if(isset($_GET['ta'])){ if($_GET['ta'] == $j['ta_id']){ echo "selected='selected'"; } } ?> value="<?php echo $j['ta_id'] ?>"><?php echo $j['ta_nama'] ?> <?php if($j['ta_status'] == "1"){ echo "(Aktif)"; } ?></option>
                    <?php 
                  }
                  ?>
                </select>
              </div>
              <input type="submit" class="btn btn-primary" value="FILTER">
            </form>

          </div>
        </div>
      </section>
      <section class="col-lg-12">
        <div class="box box-primary">

          <div class="box-header">
            <h3 class="box-title">Data Kelas</h3>
            <div class="btn-group pull-right">
              <button type="button" class="btn btn-info btn-sm" data-toggle="modal" data-target="#modal_import">
                <i class="fa fa-upload"></i> &nbsp Import Excel
              </button>
              <a href="template_kelas.php" class="btn btn-warning btn-sm">
                <i class="fa fa-download"></i> &nbsp Download Template Excel
              </a>
              <a href="kelas_tambah.php" class="btn btn-primary btn-sm"><i class="fa fa-plus"></i> &nbsp Tambah Kelas</a>              
            </div>
          </div>
          
          <!-- Modal Import Excel -->
          <div class="modal" id="modal_import">
            <div class="modal-dialog">
              <div class="modal-content">
                <!-- Modal Header -->
                <div class="modal-header">
                  <button type="button" class="close" data-dismiss="modal">&times;</button>
                  <h4 class="modal-title">Import Data Kelas dari Excel</h4>
                </div>
                <!-- Modal body -->
                <div class="modal-body">
                  <div class="alert alert-info">
                    <i class="fa fa-info-circle"></i>
                    <strong>Petunjuk Import:</strong><br>
                    1. Download template Excel terlebih dahulu<br>
                    2. Lihat sheet "Referensi Jurusan" dan "Referensi Tahun Ajaran" untuk mendapatkan ID yang benar<br>
                    3. Isi data kelas di sheet "Data Kelas" menggunakan ID dari sheet referensi<br>
                    4. Upload file Excel yang sudah diisi<br>
                    5. Data akan otomatis ditambahkan ke master kelas
                  </div>
                  
                  <form method="POST" action="import_kelas.php" enctype="multipart/form-data">
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
                    <th>NAMA KELAS</th>
                    <th>JURUSAN</th>
                    <th>TAHUN AJARAN</th>
                    <th>JUMLAH SISWA</th>
                    <th width="15%">OPSI</th>
                  </tr>
                </thead>
                <tbody>
                  <?php 
                  $no=1;
                 
                  if(isset($_GET['ta'])){ 
                    $id_ta = $_GET['ta'];
                    $data = mysqli_query($koneksi,"SELECT * FROM kelas, jurusan, ta where kelas_jurusan=jurusan_id and kelas_ta=ta_id and ta_id='$id_ta' order by kelas_id desc");
                  }else{
                    $data = mysqli_query($koneksi,"SELECT * FROM kelas, jurusan, ta where kelas_jurusan=jurusan_id and kelas_ta=ta_id and ta_status='1' order by kelas_id desc");
                  }
                  
                  while($d = mysqli_fetch_array($data)){
                    ?>
                    <tr>
                      <td><?php echo $no++; ?></td>
                      <td><?php echo $d['kelas_nama']; ?></td>
                      <td><?php echo $d['jurusan_nama']; ?></td>
                      <td><?php echo $d['ta_nama']; ?></td>
                      <td>
                        <?php 
                        $id_kelas = $d['kelas_id'];
                        $sis = mysqli_query($koneksi,"select * from kelas_siswa where ks_kelas='$id_kelas'");
                        echo mysqli_num_rows($sis);
                        ?>
                      </td>
                      <td>                        
                        <a class="btn btn-primary btn-sm" href="kelas_siswa.php?id=<?php echo $d['kelas_id'] ?>"><i class="fa fa-users"></i> Siswa</a>
                        <a class="btn btn-warning btn-sm" href="kelas_edit.php?id=<?php echo $d['kelas_id'] ?>"><i class="fa fa-cog"></i></a>
                        <button type="button" class="btn btn-danger btn-sm btn-hapus-kelas" 
                                data-id="<?php echo $d['kelas_id']; ?>"
                                data-nama="<?php echo htmlspecialchars($d['kelas_nama']); ?>"
                                data-jurusan="<?php echo htmlspecialchars($d['jurusan_nama']); ?>">
                          <i class="fa fa-trash"></i>
                        </button>
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

<script>
$(document).ready(function() {
    // Handle hapus kelas dengan konfirmasi
    $(document).on('click', '.btn-hapus-kelas', function() {
        var id = $(this).data('id');
        var nama = $(this).data('nama');
        var jurusan = $(this).data('jurusan');
        
        // Konfirmasi sebelum menghapus
        if(confirm('Apakah Anda yakin ingin menghapus kelas "' + nama + '" (Jurusan: ' + jurusan + ')?\n\nData yang akan dihapus:\n- Data kelas\n- Data siswa di kelas ini\n- Data prestasi siswa di kelas ini\n- Data pelanggaran siswa di kelas ini\n\nTindakan ini tidak dapat dibatalkan!')) {
            // Redirect ke halaman hapus dengan parameter
            window.location.href = 'kelas_hapus.php?id=' + id;
        }
    });
});
</script>