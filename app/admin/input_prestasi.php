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
      <section class="col-lg-12">
        <div class="box box-primary">

          <div class="box-header">
            <h3 class="box-title">Input Kasus Prestasi</h3>
            <div class="btn-group pull-right">
              <button type="button" class="btn btn-info btn-sm" data-toggle="modal" data-target="#modal_import">
                <i class="fa fa-upload"></i> &nbsp Import Excel
              </button>
              <a href="template_prestasi.php" class="btn btn-warning btn-sm">
                <i class="fa fa-download"></i> &nbsp Download Template Excel
              </a>
              <a href="input_prestasi_tambah.php" class="btn btn-primary btn-sm"><i class="fa fa-plus"></i> &nbsp Input Prestasi</a>              
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
                    <a href="input_prestasi.php" class="btn btn-default btn-sm"><i class="fa fa-refresh"></i> Reset Filter</a>
                  </div>
                </div>
              </div>
            </form>
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
            $count_query = "SELECT COUNT(*) as total FROM input_prestasi ip 
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
                Menampilkan <strong><?php echo $total_data; ?></strong> data prestasi untuk tahun ajaran: <strong><?php echo $ta_selected['ta_nama']; ?></strong>
              <?php else: ?>
                Menampilkan <strong><?php echo $total_data; ?></strong> data prestasi untuk tahun ajaran aktif: <strong><?php echo $ta_selected['ta_nama']; ?></strong>
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
                    <th>PRESTASI</th>
                    <th>POINT</th>
                    <th width="10%">OPSI</th>
                  </tr>
                </thead>
                <tbody>
                  <?php 
                  $no=1;
                  
                  // Query dengan filter tahun ajaran dan pagination
                  $query = "SELECT * FROM siswa, prestasi, jurusan, input_prestasi, kelas, ta 
                            WHERE ta_id=kelas_ta 
                            AND jurusan_id=kelas_jurusan 
                            AND input_prestasi.kelas=kelas_id 
                            AND input_prestasi.siswa=siswa_id 
                            AND input_prestasi.prestasi=prestasi_id 
                            AND kelas_ta='$ta_filter'
                            ORDER BY input_prestasi.id DESC
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
                      <td><?php echo $d['prestasi_nama']; ?></td>
                      <td><?php echo $d['prestasi_point']; ?></td>
                      <td>                        
                        <a class="btn btn-warning btn-sm" href="input_prestasi_edit.php?id=<?php echo $d['id'] ?>"><i class="fa fa-cog"></i></a>
                        <a onclick="return confirm('Yakin ingin hapus?')" class="btn btn-danger btn-sm" href="input_prestasi_hapus.php?id=<?php echo $d['id'] ?>"><i class="fa fa-trash"></i></a>
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

  <!-- Modal Import Excel -->
  <div class="modal" id="modal_import">
    <div class="modal-dialog">
      <div class="modal-content">

        <!-- Modal Header -->
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">Import Data Prestasi dari Excel</h4>
        </div>

        <!-- Modal body -->
        <div class="modal-body">
          <div class="alert alert-info">
            <i class="fa fa-info-circle"></i>
            <strong>Petunjuk Import:</strong><br>
            1. Download template Excel terlebih dahulu<br>
            2. Isi data prestasi sesuai format template<br>
            3. Upload file Excel yang sudah diisi<br>
            4. Data akan otomatis ditambahkan ke sistem<br>
            <strong>Note:</strong> Data akan otomatis masuk ke tahun ajaran aktif (<?php 
            $ta_aktif = mysqli_query($koneksi, "SELECT ta_nama FROM ta WHERE ta_status = 1");
            $ta_data = mysqli_fetch_assoc($ta_aktif);
            echo $ta_data['ta_nama'];
            ?>)<br>
            <strong>ID Prestasi:</strong> Gunakan ID prestasi dari bagian "Referensi Prestasi" di template CSV
          </div>
          
          <form method="POST" action="import_prestasi_universal.php" enctype="multipart/form-data">
            <div class="form-group">
              <label>Pilih File:</label>
              <input type="file" name="file_excel" class="form-control" accept=".csv,.xlsx" required>
              <small class="text-muted">Format file: .csv atau .xlsx (download template terlebih dahulu)</small>
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

</div>
<?php include 'footer.php'; ?>