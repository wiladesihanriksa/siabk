<?php 
include 'header.php';
?>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <section class="content-header">
    <h1>
      <i class="fa fa-bell"></i> Notifikasi RTL
      <small>Pengingat Rencana Tindak Lanjut</small>
    </h1>
    <ol class="breadcrumb">
      <li><a href="index.php"><i class="fa fa-dashboard"></i> Home</a></li>
      <li><a href="#">Konseling BK</a></li>
      <li class="active">Notifikasi RTL</li>
    </ol>
  </section>

  <!-- Main content -->
  <section class="content">
    <!-- CSS untuk memastikan sidebar tampil -->
    <style>
      .main-sidebar {
        display: block !important;
        position: fixed !important;
        top: 0 !important;
        left: 0 !important;
        width: 230px !important;
        height: 100% !important;
        z-index: 1000 !important;
      }
      
      .content-wrapper {
        margin-left: 230px !important;
      }
      
      @media (max-width: 767px) {
        .content-wrapper {
          margin-left: 0 !important;
        }
      }
    </style>
    
    <div class="row">
      <div class="col-xs-12">
        <div class="box">
          <div class="box-header">
            <h3 class="box-title">
              <i class="fa fa-bell"></i> Daftar Pengingat RTL
            </h3>
            <div class="box-tools pull-right">
              <button class="btn btn-success btn-sm" onclick="markAllAsRead()">
                <i class="fa fa-check"></i> Tandai Semua Sudah Dibaca
              </button>
            </div>
          </div>
          
          <div class="box-body">
            <div class="table-responsive">
              <table id="tabel_notifikasi" class="table table-bordered table-striped">
                <thead>
                  <tr>
                    <th width="5%">No</th>
                    <th width="15%">Tanggal Reminder</th>
                    <th width="20%">Kasus</th>
                    <th width="30%">Pesan Reminder</th>
                    <th width="10%">Status</th>
                    <th width="10%">Tanggal Dibuat</th>
                    <th width="10%">Aksi</th>
                  </tr>
                </thead>
                <tbody>
                  <?php
                  $no = 1;
                  $query = "SELECT n.*, n.notif_id, k.kasus_id, k.kasus_kode, k.judul_kasus, s.siswa_nama, j.tanggal_konseling
                           FROM notifikasi_rtl n 
                           LEFT JOIN jurnal_kasus j ON n.jurnal_id = j.jurnal_id
                           LEFT JOIN kasus_siswa k ON j.kasus_id = k.kasus_id
                           LEFT JOIN siswa s ON k.siswa_id = s.siswa_id
                           ORDER BY n.tanggal_reminder ASC, n.created_at DESC";
                  $result = mysqli_query($koneksi, $query);
                  
                  if(mysqli_num_rows($result) > 0) {
                    while($data = mysqli_fetch_assoc($result)) {
                      $status_class = '';
                      $status_text = '';
                      switch($data['status_reminder']) {
                        case 'Belum': 
                          $status_class = 'label-warning'; 
                          $status_text = 'Belum Dibaca';
                          break;
                        case 'Sudah': 
                          $status_class = 'label-success'; 
                          $status_text = 'Sudah Dibaca';
                          break;
                        case 'Dibatalkan': 
                          $status_class = 'label-danger'; 
                          $status_text = 'Dibatalkan';
                          break;
                      }
                  ?>
                  <tr <?php echo ($data['status_reminder'] == 'Belum') ? 'style="background-color: #fff3cd;"' : ''; ?>>
                    <td><?php echo $no++; ?></td>
                    <td>
                      <strong><?php echo date('d/m/Y', strtotime($data['tanggal_reminder'])); ?></strong>
                      <?php if($data['tanggal_reminder'] < date('Y-m-d') && $data['status_reminder'] == 'Belum') { ?>
                        <br><small class="text-red"><i class="fa fa-exclamation-triangle"></i> Terlambat</small>
                      <?php } ?>
                    </td>
                    <td>
                      <strong><?php echo $data['kasus_kode']; ?></strong><br>
                      <small><?php echo $data['siswa_nama']; ?></small><br>
                      <small class="text-muted"><?php echo $data['judul_kasus']; ?></small>
                    </td>
                    <td><?php echo $data['pesan_reminder']; ?></td>
                    <td>
                      <span class="label <?php echo $status_class; ?>">
                        <?php echo $status_text; ?>
                      </span>
                    </td>
                    <td><?php echo date('d/m/Y H:i', strtotime($data['created_at'])); ?></td>
                    <td>
                      <div class="btn-group">
                        <?php if($data['status_reminder'] == 'Belum') { ?>
                        <a href="notifikasi_rtl_act.php?action=mark_read&id=<?php echo $data['notif_id']; ?>" 
                           class="btn btn-success btn-xs" title="Tandai Sudah Dibaca">
                          <i class="fa fa-check"></i>
                        </a>
                        <?php } ?>
                        <?php 
                        $kasus_id_link = isset($data['kasus_id']) && !empty($data['kasus_id']) ? $data['kasus_id'] : 0;
                        $notif_id_link = isset($data['notif_id']) ? $data['notif_id'] : 0;
                        if($kasus_id_link > 0) {
                        ?>
                        <a href="kasus_siswa_detail.php?id=<?php echo $kasus_id_link; ?>&notif_id=<?php echo $notif_id_link; ?>" 
                           class="btn btn-info btn-xs" title="Lihat Kasus">
                          <i class="fa fa-eye"></i>
                        </a>
                        <?php } else { ?>
                        <button class="btn btn-info btn-xs" disabled title="Kasus tidak ditemukan">
                          <i class="fa fa-eye"></i>
                        </button>
                        <?php } ?>
                        <a href="notifikasi_rtl_act.php?action=cancel&id=<?php echo $data['notif_id']; ?>" 
                           class="btn btn-danger btn-xs" 
                           onclick="return confirm('Yakin ingin membatalkan notifikasi ini?')" title="Batalkan">
                          <i class="fa fa-times"></i>
                        </a>
                      </div>
                    </td>
                  </tr>
                  <?php 
                    }
                  } else {
                  ?>
                  <tr>
                    <td colspan="7" class="text-center">
                      <i class="fa fa-info-circle"></i> Belum ada notifikasi RTL
                    </td>
                  </tr>
                  <?php } ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
</div>

<!-- Script untuk DataTable -->
<script>
$(document).ready(function() {
    // Pastikan sidebar tampil
    $('.main-sidebar').show();
    $('.content-wrapper').css('margin-left', '230px');
    
    // Inisialisasi sidebar menu
    $('.sidebar-menu').tree();
    
    // Inisialisasi DataTable
    var table = $('#tabel_notifikasi').DataTable({
        "responsive": true,
        "lengthChange": false,
        "autoWidth": false,
        "pageLength": 25,
        "language": {
            "url": "../assets/bower_components/datatables.net-bs/js/Indonesian.json"
        },
        "order": [[1, "asc"]] // Sort by tanggal reminder
    });
});

function markAllAsRead() {
    if(confirm('Yakin ingin menandai semua notifikasi sebagai sudah dibaca?')) {
        window.location.href = 'notifikasi_rtl_act.php?action=mark_all_read';
    }
}
</script>

<?php include 'footer.php'; ?>
