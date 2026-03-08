<?php 
include 'header.php';
include 'functions_academic_year.php';

// Update status tahun ajaran berdasarkan tanggal saat ini
updateAcademicYearStatus($koneksi);

// Ambil tahun ajaran aktif untuk fallback info
$ta_aktif_res = mysqli_query($koneksi, "SELECT * FROM ta WHERE ta_status = 1 LIMIT 1");
$ta_aktif = mysqli_fetch_assoc($ta_aktif_res);
?>

<div class="content-wrapper">
  <section class="content-header">
    <h1>
      Siswa
      <small>Data Siswa Aktif</small>
    </h1>
    <ol class="breadcrumb">
      <li><a href="index.php"><i class="fa fa-dashboard"></i> Home</a></li>
      <li class="active">Siswa</li>
    </ol>
  </section>

  <section class="content">
    <div class="row">
      <section class="col-lg-12">
        <div class="box box-primary">

          <div class="box-header with-border">
            <h3 class="box-title"><i class="fa fa-users"></i> Daftar Siswa</h3>
            <div class="btn-group pull-right">
              <a href="siswa_tambah.php" class="btn btn-primary btn-sm"><i class="fa fa-plus"></i> Tambah Siswa</a>
              <button type="button" class="btn btn-info btn-sm" id="resetAllPasswords"><i class="fa fa-key"></i> Reset Semua Password</button>
            </div>
          </div>
          
          <div class="box-body" style="background: #f9f9f9; border-bottom: 1px solid #eee;">
            <form method="GET" action="">
              <div class="row">
                <div class="col-md-3">
                  <div class="form-group">
                    <label>Filter Tahun Ajaran:</label>
                    <select name="ta_filter" class="form-control select2" id="ta_filter">
                      <option value="">-- Tahun Ajaran Aktif --</option>
                      <?php
                      $ta_query = mysqli_query($koneksi, "SELECT * FROM ta ORDER BY ta_id DESC");
                      while($ta = mysqli_fetch_array($ta_query)){
                        $selected = (isset($_GET['ta_filter']) && $_GET['ta_filter'] == $ta['ta_id']) ? 'selected' : '';
                        $status_label = ($ta['ta_status'] == 1) ? ' (Aktif)' : '';
                        echo "<option value='".$ta['ta_id']."' $selected>".$ta['ta_nama'].$status_label."</option>";
                      }
                      ?>
                    </select>
                  </div>
                </div>
                <div class="col-md-5">
                  <div class="form-group">
                    <label>Pencarian:</label>
                    <div class="input-group">
                      <input type="text" name="search" class="form-control" placeholder="Nama, NIS, Jurusan, atau Kelas..." value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
                      <span class="input-group-btn">
                        <button type="submit" class="btn btn-primary btn-flat"><i class="fa fa-search"></i> Cari</button>
                      </span>
                    </div>
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="form-group">
                    <label>&nbsp;</label><br>
                    <a href="siswa.php" class="btn btn-default btn-flat"><i class="fa fa-refresh"></i> Reset</a>
                    <input type="hidden" name="page" value="1">
                  </div>
                </div>
              </div>
            </form>
          </div>

          <div class="box-body">
            <?php 
            // Konfigurasi pagination
            $per_page = 20;
            $current_page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
            $offset = ($current_page - 1) * $per_page;
            
            $search_term = isset($_GET['search']) ? trim($_GET['search']) : '';
            $search_condition = "";
            if(!empty($search_term)){
              $s = mysqli_real_escape_string($koneksi, $search_term);
              $search_condition = " AND (s.siswa_nama LIKE '%$s%' OR s.siswa_nis LIKE '%$s%' OR j.jurusan_nama LIKE '%$s%' OR k.kelas_nama LIKE '%$s%')";
            }

            // Penentuan Tahun Ajaran yang digunakan
            $filter_ta = (!empty($_GET['ta_filter'])) ? mysqli_real_escape_string($koneksi, $_GET['ta_filter']) : ($ta_aktif['ta_id'] ?? 0);

            // Query Hitung Total (Sesuai dengan filter TA)
            $count_query = "SELECT COUNT(DISTINCT s.siswa_id) as total 
                            FROM siswa s 
                            JOIN jurusan j ON s.siswa_jurusan = j.jurusan_id
                            LEFT JOIN kelas_siswa ks ON s.siswa_id = ks.ks_siswa 
                            LEFT JOIN kelas k ON ks.ks_kelas = k.kelas_id 
                            WHERE (k.kelas_ta = '$filter_ta' OR k.kelas_ta IS NULL) 
                            AND s.siswa_status = 'aktif' $search_condition";
            
            $count_res = mysqli_fetch_assoc(mysqli_query($koneksi, $count_query));
            $total_data = $count_res['total'];
            $total_pages = ceil($total_data / $per_page);

            // Info Alert
            $ta_name_display = "Tahun Ajaran Aktif";
            if(!empty($_GET['ta_filter'])) {
                $check_ta = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT ta_nama FROM ta WHERE ta_id='$filter_ta'"));
                $ta_name_display = $check_ta['ta_nama'];
            }
            ?>

            <div class="callout callout-info" style="margin-bottom: 15px;">
              <h4><i class="icon fa fa-info"></i> Informasi Data</h4>
              <p>Menampilkan data siswa pada <strong><?php echo $ta_name_display; ?></strong>. Total: <b><?php echo $total_data; ?></b> siswa ditemukan.</p>
            </div>

            <div class="table-responsive">
              <table class="table table-bordered table-hover">
                <thead>
                  <tr class="bg-gray">
                    <th width="1%">NO</th>
                    <th>NAMA LENGKAP</th>
                    <th width="12%">NIS</th>
                    <th>JURUSAN</th>
                    <th width="10%">KELAS</th>
                    <th width="8%" class="text-center">STATUS</th>
                    <th width="22%" class="text-center">OPSI</th>
                  </tr>
                </thead>
                <tbody>
                  <?php 
                  $main_query = "SELECT DISTINCT s.*, j.jurusan_nama, k.kelas_nama 
                                 FROM siswa s 
                                 JOIN jurusan j ON s.siswa_jurusan = j.jurusan_id 
                                 LEFT JOIN kelas_siswa ks ON s.siswa_id = ks.ks_siswa 
                                 LEFT JOIN kelas k ON ks.ks_kelas = k.kelas_id 
                                 WHERE (k.kelas_ta = '$filter_ta' OR k.kelas_ta IS NULL) 
                                 AND s.siswa_status = 'aktif' $search_condition
                                 ORDER BY s.siswa_nama ASC 
                                 LIMIT $per_page OFFSET $offset";
                  
                  $res = mysqli_query($koneksi, $main_query);
                  $no = $offset + 1;

                  if(mysqli_num_rows($res) > 0){
                    while($d = mysqli_fetch_array($res)){
                    ?>
                    <tr>
                      <td><?php echo $no++; ?></td>
                      <td><b><?php echo $d['siswa_nama']; ?></b></td>
                      <td><code><?php echo $d['siswa_nis']; ?></code></td>
                      <td><?php echo $d['jurusan_nama']; ?></td>
                      <td><?php echo $d['kelas_nama'] ?: '<span class="label label-default">Belum Plotting</span>'; ?></td>
                      <td class="text-center"><span class="label label-success"><?php echo strtoupper($d['siswa_status']); ?></span></td>
                      <td class="text-center">                        
                        <div class="btn-group">
                          <a class="btn btn-default btn-sm" href="siswa_riwayat.php?id=<?php echo $d['siswa_id'] ?>" title="Riwayat"><i class="fa fa-history"></i></a>
                          <button class="btn btn-default btn-sm reset-password-btn" data-siswa-id="<?php echo $d['siswa_id']; ?>" data-siswa-nama="<?php echo $d['siswa_nama']; ?>" data-siswa-nis="<?php echo $d['siswa_nis']; ?>" title="Reset Password"><i class="fa fa-key text-yellow"></i></button>
                          <a class="btn btn-default btn-sm" href="siswa_edit.php?id=<?php echo $d['siswa_id'] ?>" title="Edit"><i class="fa fa-edit text-blue"></i></a>
                          <a class="btn btn-default btn-sm" href="siswa_hapus_konfir.php?id=<?php echo $d['siswa_id'] ?>" title="Hapus"><i class="fa fa-trash text-red"></i></a>
                        </div>
                      </td>
                    </tr>
                    <?php 
                    }
                  } else {
                    echo "<tr><td colspan='7' class='text-center'>Data tidak ditemukan atau siswa belum di-plotting ke kelas pada tahun ajaran ini.</td></tr>";
                  }
                  ?>
                </tbody>
              </table>
            </div>

            <?php if($total_pages > 1): ?>
            <div class="box-footer clearfix">
              <ul class="pagination pagination-sm no-margin pull-right">
                <?php 
                $params = $_GET;
                unset($params['page']);
                $qs = http_build_query($params);
                $qs = $qs ? "&$qs" : "";

                if($current_page > 1): ?>
                  <li><a href="?page=<?php echo $current_page-1 . $qs; ?>">&laquo;</a></li>
                <?php endif; ?>

                <?php for($i=1; $i<=$total_pages; $i++): ?>
                  <li class="<?php echo ($i == $current_page) ? 'active' : ''; ?>">
                    <a href="?page=<?php echo $i . $qs; ?>"><?php echo $i; ?></a>
                  </li>
                <?php endfor; ?>

                <?php if($current_page < $total_pages): ?>
                  <li><a href="?page=<?php echo $current_page+1 . $qs; ?>">&raquo;</a></li>
                <?php endif; ?>
              </ul>
            </div>
            <?php endif; ?>
          </div>
        </div>
      </section>
    </div>
  </section>
</div>

<?php 
// Sertakan Modals (Reset Password) di sini atau biarkan tetap di bawah
?>

<?php include 'footer.php'; ?>