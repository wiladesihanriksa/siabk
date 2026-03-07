<?php include 'header_dynamic.php'; ?>

<div class="content-wrapper">

  <section class="content-header">
    <h1>
      Input Pelanggaran
      <small>Input Pelanggaran</small>
    </h1>
    <ol class="breadcrumb">
      <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
      <li class="active">Dashboard</li>
    </ol>
  </section>

  <section class="content">
    <div class="row">
      <section class="col-lg-12">
        <div class="box box-primary">

          <div class="box-header">
            <h3 class="box-title">Input Kasus Pelanggaran</h3>
            <div class="btn-group pull-right">
              <button type="button" class="btn btn-info btn-sm" data-toggle="modal" data-target="#modal_import">
                <i class="fa fa-upload"></i> &nbsp Import Excel
              </button>
              <a href="template_pelanggaran.php" class="btn btn-warning btn-sm">
                <i class="fa fa-download"></i> &nbsp Download Template Excel
              </a>
              <a href="input_pelanggaran_tambah.php" class="btn btn-primary btn-sm"><i class="fa fa-plus"></i> &nbsp Input Pelanggaran</a>              
            </div>
          </div>
          
          <!-- Filter Tahun Ajaran -->
          <div class="box-body">
            <form method="GET" action="">
              <div class="row">
                <div class="col-md-3">
                  <div class="form-group">
                    <label>Tahun Ajaran:</label>
                    <select name="ta_filter" class="form-control" onchange="this.form.submit()">
                      <option value="">-- Semua Tahun Ajaran --</option>
                      <?php
                      // Ambil tahun ajaran aktif untuk default
                      $ta_active_query = mysqli_query($koneksi, "SELECT ta_id FROM ta WHERE ta_status = 1");
                      $ta_active_data = mysqli_fetch_array($ta_active_query);
                      $default_ta = $ta_active_data['ta_id'];
                      
                      $ta_query = mysqli_query($koneksi, "SELECT * FROM ta ORDER BY ta_id DESC");
                      while($ta = mysqli_fetch_array($ta_query)){
                        // Default ke tahun ajaran aktif jika tidak ada filter
                        if(!isset($_GET['ta_filter']) || empty($_GET['ta_filter'])){
                          $selected = ($ta['ta_id'] == $default_ta) ? 'selected' : '';
                        } else {
                          $selected = ($_GET['ta_filter'] == $ta['ta_id']) ? 'selected' : '';
                        }
                        echo "<option value='".$ta['ta_id']."' $selected>".$ta['ta_nama']."</option>";
                      }
                      ?>
                    </select>
                  </div>
                </div>
                <div class="col-md-3">
                  <div class="form-group">
                    <label>&nbsp;</label><br>
                    <a href="input_pelanggaran.php" class="btn btn-default btn-sm"><i class="fa fa-refresh"></i> Reset Filter</a>
                  </div>
                </div>
              </div>
            </form>
          </div>
          <div class="box-body">
            <?php 
            if(isset($_GET['alert'])){
              if($_GET['alert']=="success"){
                echo "<div class='alert alert-success alert-dismissible'>
                        <button type='button' class='close' data-dismiss='alert' aria-hidden='true'>&times;</button>
                        <h4><i class='icon fa fa-check'></i> Berhasil!</h4>
                        ".$_GET['msg']."
                      </div>";
              }elseif($_GET['alert']=="warning"){
                echo "<div class='alert alert-warning alert-dismissible'>
                        <button type='button' class='close' data-dismiss='alert' aria-hidden='true'>&times;</button>
                        <h4><i class='icon fa fa-warning'></i> Peringatan!</h4>
                        ".$_GET['msg']."
                      </div>";
              }elseif($_GET['alert']=="error"){
                echo "<div class='alert alert-danger alert-dismissible'>
                        <button type='button' class='close' data-dismiss='alert' aria-hidden='true'>&times;</button>
                        <h4><i class='icon fa fa-ban'></i> Error!</h4>
                        ".$_GET['msg']."
                      </div>";
              }
            }
            ?>
            
            <?php 
            // Tentukan tahun ajaran yang akan ditampilkan
            if(isset($_GET['ta_filter']) && !empty($_GET['ta_filter'])){
              $ta_filter = $_GET['ta_filter'];
              $ta_selected = mysqli_fetch_array(mysqli_query($koneksi, "SELECT * FROM ta WHERE ta_id = '$_GET[ta_filter]'"));
            } else {
              // Default ke tahun ajaran aktif
              $ta_active = mysqli_fetch_array(mysqli_query($koneksi, "SELECT * FROM ta WHERE ta_status = 1"));
              $ta_filter = $ta_active['ta_id'];
              $ta_selected = $ta_active;
            }
            
            // Pagination settings
            $per_page = 10; // Jumlah data per halaman
            $current_page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
            if($current_page < 1) $current_page = 1;
            
            // Hitung total data berdasarkan filter
            $count_query = "SELECT COUNT(*) as total FROM input_pelanggaran ip 
                           JOIN kelas k ON ip.kelas = k.kelas_id 
                           WHERE k.kelas_ta = '$ta_filter'";
            $count_result = mysqli_fetch_array(mysqli_query($koneksi, $count_query));
            $total_data = $count_result['total'];
            
            // Hitung pagination
            $total_pages = ceil($total_data / $per_page);
            $offset = ($current_page - 1) * $per_page;
            ?>
            
            <div class="alert alert-info">
              <i class="fa fa-info-circle"></i> 
              <?php if(isset($_GET['ta_filter']) && !empty($_GET['ta_filter'])): ?>
                Menampilkan <strong><?php echo $total_data; ?></strong> data pelanggaran untuk tahun ajaran: <strong><?php echo $ta_selected['ta_nama']; ?></strong>
              <?php else: ?>
                Menampilkan <strong><?php echo $total_data; ?></strong> data pelanggaran untuk tahun ajaran aktif: <strong><?php echo $ta_selected['ta_nama']; ?></strong>
              <?php endif; ?>
              <br>
              <small>Halaman <?php echo $current_page; ?> dari <?php echo $total_pages; ?> | Data <?php echo $offset + 1; ?>-<?php echo min($offset + $per_page, $total_data); ?> dari <?php echo $total_data; ?></small>
            </div>

            <div class="table-responsive">
              <table class="table table-bordered table-striped" id="table-datatable">
                <thead>
                  <tr>
                    <th width="1%">NO</th>
                    <th>WAKTU</th>
                    <th>NAMA SISWA</th>
                    <th>KELAS</th>
                    <th>JURUSAN</th>
                    <th>TAHUN AJARAN</th>
                    <th>PELANGGARAN</th>
                    <th>POINT</th>
                    <th width="10%">OPSI</th>
                  </tr>
                </thead>
                <tbody>
                  <?php 
                  $no=1;
                  
                  // Query dengan filter tahun ajaran dan pagination
                  $query = "SELECT * FROM siswa, pelanggaran, jurusan, input_pelanggaran, kelas, ta 
                            WHERE ta_id=kelas_ta 
                            AND jurusan_id=kelas_jurusan 
                            AND input_pelanggaran.kelas=kelas_id 
                            AND input_pelanggaran.siswa=siswa_id 
                            AND input_pelanggaran.pelanggaran=pelanggaran_id 
                            AND kelas_ta='$ta_filter'
                            ORDER BY input_pelanggaran.id DESC
                            LIMIT $per_page OFFSET $offset";
                  
                  $data = mysqli_query($koneksi, $query);
                  while($d = mysqli_fetch_array($data)){
                    ?>
                    <tr>
                      <td><?php echo $offset + $no++; ?></td>
                      <td><?php echo date('d-m-Y H:i:s', strtotime($d['waktu'])); ?></td>
                      <td><?php echo $d['siswa_nama']; ?></td>
                      <td><?php echo $d['kelas_nama']; ?></td>
                      <td><?php echo $d['jurusan_nama']; ?></td>
                      <td><?php echo $d['ta_nama']; ?></td>
                      <td><?php echo $d['pelanggaran_nama']; ?></td>
                      <td><?php echo $d['pelanggaran_point']; ?></td>
                      <td>                        
                        <a class="btn btn-warning btn-sm" href="input_pelanggaran_edit.php?id=<?php echo $d['id'] ?>"><i class="fa fa-cog"></i></a>
                        <a onclick="return confirm('Yakin ingin hapus?')" class="btn btn-danger btn-sm" href="input_pelanggaran_hapus.php?id=<?php echo $d['id'] ?>"><i class="fa fa-trash"></i></a>
                      </td>
                    </tr>
                    <?php 
                  }
                  ?>
                </tbody>
              </table>
            </div>
            
            <!-- Pagination Controls -->
            <?php if($total_pages > 1): ?>
            <div class="row">
              <div class="col-sm-5">
                <div class="dataTables_info">
                  Menampilkan <?php echo $offset + 1; ?> sampai <?php echo min($offset + $per_page, $total_data); ?> dari <?php echo $total_data; ?> data
                </div>
              </div>
              <div class="col-sm-7">
                <div class="dataTables_paginate paging_simple_numbers">
                  <ul class="pagination">
                    <?php if($current_page > 1): ?>
                      <li class="paginate_button previous">
                        <a href="?<?php echo http_build_query(array_merge($_GET, ['page' => $current_page - 1])); ?>">Previous</a>
                      </li>
                    <?php endif; ?>
                    
                    <?php
                    // Hitung range halaman yang akan ditampilkan
                    $start_page = max(1, $current_page - 2);
                    $end_page = min($total_pages, $current_page + 2);
                    
                    // Tampilkan halaman pertama jika tidak dalam range
                    if($start_page > 1): ?>
                      <li class="paginate_button">
                        <a href="?<?php echo http_build_query(array_merge($_GET, ['page' => 1])); ?>">1</a>
                      </li>
                      <?php if($start_page > 2): ?>
                        <li class="paginate_button disabled"><span>...</span></li>
                      <?php endif; ?>
                    <?php endif; ?>
                    
                    <?php for($i = $start_page; $i <= $end_page; $i++): ?>
                      <li class="paginate_button <?php echo ($i == $current_page) ? 'active' : ''; ?>">
                        <a href="?<?php echo http_build_query(array_merge($_GET, ['page' => $i])); ?>"><?php echo $i; ?></a>
                      </li>
                    <?php endfor; ?>
                    
                    <?php
                    // Tampilkan halaman terakhir jika tidak dalam range
                    if($end_page < $total_pages): ?>
                      <?php if($end_page < $total_pages - 1): ?>
                        <li class="paginate_button disabled"><span>...</span></li>
                      <?php endif; ?>
                      <li class="paginate_button">
                        <a href="?<?php echo http_build_query(array_merge($_GET, ['page' => $total_pages])); ?>"><?php echo $total_pages; ?></a>
                      </li>
                    <?php endif; ?>
                    
                    <?php if($current_page < $total_pages): ?>
                      <li class="paginate_button next">
                        <a href="?<?php echo http_build_query(array_merge($_GET, ['page' => $current_page + 1])); ?>">Next</a>
                      </li>
                    <?php endif; ?>
                  </ul>
                </div>
              </div>
            </div>
            <?php endif; ?>
          </div>

        </div>
      </section>
    </div>
  </section>

</div>

<!-- Modal Import Excel -->
<div class="modal fade" id="modal_import" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">Import Data Pelanggaran dari Excel</h4>
      </div>
      <div class="modal-body">
        <div class="alert alert-info">
          <h4><i class="icon fa fa-info"></i> Petunjuk Import:</h4>
          <ol>
            <li>Download template Excel terlebih dahulu</li>
            <li>Isi data pelanggaran sesuai format template</li>
            <li>Gunakan ID Pelanggaran dari sheet "Referensi Pelanggaran"</li>
            <li>Data akan otomatis masuk ke tahun ajaran aktif</li>
            <li>Format tanggal: YYYY-MM-DD (contoh: 2024-12-25)</li>
          </ol>
        </div>
        <form action="import_pelanggaran_universal.php" method="post" enctype="multipart/form-data">
          <div class="form-group">
            <label>Pilih File Excel (.xlsx atau .csv)</label>
            <input type="file" name="file_excel" class="form-control" accept=".csv,.xlsx" required>
            <p class="help-block">Format file yang didukung: .xlsx dan .csv</p>
          </div>
          <div class="form-group">
            <button type="submit" class="btn btn-primary">
              <i class="fa fa-upload"></i> Import Data
            </button>
            <button type="button" class="btn btn-default" data-dismiss="modal">
              <i class="fa fa-times"></i> Batal
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<?php include 'footer.php'; ?>