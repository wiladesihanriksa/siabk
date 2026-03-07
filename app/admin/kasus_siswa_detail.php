<?php 
include 'header_dynamic.php';

// Ambil ID kasus dari URL
$kasus_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if($kasus_id == 0) {
    header("location:kasus_siswa.php?alert=gagal&pesan=ID kasus tidak valid");
    exit();
}

// Ambil data kasus
$query_kasus = "SELECT k.*, s.siswa_nama, s.siswa_nis, j.jurusan_nama, u.user_nama as guru_bk_nama 
                FROM kasus_siswa k 
                LEFT JOIN siswa s ON k.siswa_id = s.siswa_id 
                LEFT JOIN jurusan j ON s.siswa_jurusan = j.jurusan_id
                LEFT JOIN user u ON k.guru_bk_id = u.user_id 
                WHERE k.kasus_id = '$kasus_id'";
$result_kasus = mysqli_query($koneksi, $query_kasus);

if(mysqli_num_rows($result_kasus) == 0) {
    header("location:kasus_siswa.php?alert=gagal&pesan=Kasus tidak ditemukan");
    exit();
}

$kasus = mysqli_fetch_assoc($result_kasus);

// Ambil data jurnal perkembangan
$query_jurnal = "SELECT * FROM jurnal_kasus WHERE kasus_id = '$kasus_id' ORDER BY tanggal_konseling DESC";
$result_jurnal = mysqli_query($koneksi, $query_jurnal);

// Tandai notifikasi RTL sebagai sudah dibaca jika diklik dari notifikasi
if(isset($_GET['notif_id']) && !empty($_GET['notif_id'])) {
    $notif_id = (int)$_GET['notif_id'];
    if($notif_id > 0) {
        // Verifikasi bahwa notifikasi ini terkait dengan kasus yang sedang dilihat
        $verify_notif = "SELECT n.notif_id 
                        FROM notifikasi_rtl n
                        LEFT JOIN jurnal_kasus j ON n.jurnal_id = j.jurnal_id
                        WHERE n.notif_id = '$notif_id' 
                        AND j.kasus_id = '$kasus_id'";
        $verify_result = mysqli_query($koneksi, $verify_notif);
        
        if($verify_result && mysqli_num_rows($verify_result) > 0) {
            // Mark notifikasi sebagai sudah dibaca
            mysqli_query($koneksi, "UPDATE notifikasi_rtl SET status_reminder = 'Sudah' WHERE notif_id = '$notif_id'");
        }
    }
}

// Tandai feedback sebagai sudah dibaca hanya jika diklik dari notifikasi (parameter feedback_id)
if(isset($_GET['feedback_id']) && !empty($_GET['feedback_id'])) {
    $feedback_id = (int)$_GET['feedback_id'];
    if(isset($_SESSION['level']) && $_SESSION['level'] == 'guru_bk') {
        $user_id = $_SESSION['id'];
        $guru_bk_check = mysqli_query($koneksi, "SELECT guru_bk_id FROM guru_bk WHERE user_id = '$user_id'");
        if($guru_bk_check && mysqli_num_rows($guru_bk_check) > 0) {
            $guru_bk_data = mysqli_fetch_assoc($guru_bk_check);
            $guru_bk_id = $guru_bk_data['guru_bk_id'];
            
            // Verifikasi bahwa feedback ini memang untuk kasus yang ditangani oleh guru BK ini
            $verify_feedback = "SELECT f.feedback_id 
                               FROM feedback_siswa f
                               JOIN jurnal_kasus j ON f.jurnal_id = j.jurnal_id
                               JOIN kasus_siswa k ON j.kasus_id = k.kasus_id
                               WHERE f.feedback_id = '$feedback_id' 
                               AND k.kasus_id = '$kasus_id'
                               AND k.guru_bk_id = '$guru_bk_id'";
            $verify_result = mysqli_query($koneksi, $verify_feedback);
            
            if($verify_result && mysqli_num_rows($verify_result) > 0) {
                // Mark feedback sebagai sudah dibaca
                mysqli_query($koneksi, "UPDATE feedback_siswa SET is_read = 1 WHERE feedback_id = '$feedback_id'");
            }
        }
    }
}

// Fungsi untuk mengambil feedback siswa berdasarkan jurnal_id
function getFeedbackSiswa($koneksi, $jurnal_id) {
    if(empty($jurnal_id)) {
        return false;
    }
    $jurnal_id_escaped = mysqli_real_escape_string($koneksi, $jurnal_id);
    $query = "SELECT f.*, s.siswa_nama, s.siswa_nis 
              FROM feedback_siswa f 
              LEFT JOIN siswa s ON f.siswa_id = s.siswa_id 
              WHERE f.jurnal_id = '$jurnal_id_escaped' 
              ORDER BY f.created_at ASC";
    $result = mysqli_query($koneksi, $query);
    return $result;
}
?>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <section class="content-header">
    <h1>
      <i class="fa fa-folder-open"></i> Detail Kasus Siswa
      <small><?php echo $kasus['kasus_kode']; ?></small>
    </h1>
    <ol class="breadcrumb">
      <li><a href="index.php"><i class="fa fa-dashboard"></i> Home</a></li>
      <li><a href="kasus_siswa.php">Data Kasus Siswa</a></li>
      <li class="active">Detail Kasus</li>
    </ol>
  </section>

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
    
  <!-- Main content -->
  <section class="content">
    <?php 
    // Tampilkan pesan jika status berhasil diupdate dan force refresh
    if(isset($_GET['status_updated']) && $_GET['status_updated'] == 1) {
        echo '<div class="alert alert-success alert-dismissible" style="margin-bottom: 15px;">
                <button type="button" class="close" data-dismiss="alert">&times;</button>
                <i class="fa fa-check-circle"></i> Status kasus telah diupdate menjadi "Dalam Proses". Memperbarui notifikasi...
              </div>';
        // Force refresh halaman dengan cache busting untuk update notifikasi di header
        echo '<script>
                // Force reload dengan cache busting untuk memastikan notifikasi ter-update
                setTimeout(function() {
                    // Gunakan replace untuk menghindari back button issue dan force reload
                    window.location.replace("kasus_siswa_detail.php?id=' . $kasus_id . '&_t=" + new Date().getTime());
                }, 200);
              </script>';
    }
    
    // Tampilkan pesan jika feedback berhasil ditandai sebagai sudah dibaca
    if(isset($_GET['feedback_id']) && !empty($_GET['feedback_id'])) {
        echo '<div class="alert alert-info alert-dismissible" style="margin-bottom: 15px;">
                <button type="button" class="close" data-dismiss="alert">&times;</button>
                <i class="fa fa-info-circle"></i> Feedback telah ditandai sebagai sudah dibaca.
              </div>';
        // Hapus parameter feedback_id dari URL setelah ditampilkan
        echo '<script>
                setTimeout(function() {
                    if(window.history && window.history.replaceState) {
                        window.history.replaceState({}, "", "kasus_siswa_detail.php?id=' . $kasus_id . '");
                    }
                }, 1000);
              </script>';
    }
    ?>
    <div class="row">
      <!-- Informasi Kasus -->
      <div class="col-md-4">
        <div class="box box-primary">
          <div class="box-header with-border">
            <h3 class="box-title">
              <i class="fa fa-info-circle"></i> Informasi Kasus
            </h3>
          </div>
          <div class="box-body">
            <table class="table table-striped">
              <tr>
                <td><strong>Kode Kasus:</strong></td>
                <td><?php echo $kasus['kasus_kode']; ?></td>
              </tr>
              <tr>
                <td><strong>Nama Siswa:</strong></td>
                <td><?php echo $kasus['siswa_nama']; ?></td>
              </tr>
              <tr>
                <td><strong>NIS:</strong></td>
                <td><?php echo $kasus['siswa_nis']; ?></td>
              </tr>
              <tr>
                <td><strong>Jurusan:</strong></td>
                <td><?php echo $kasus['jurusan_nama']; ?></td>
              </tr>
              <tr>
                <td><strong>Tanggal Pelaporan:</strong></td>
                <td><?php echo date('d/m/Y', strtotime($kasus['tanggal_pelaporan'])); ?></td>
              </tr>
              <tr>
                <td><strong>Sumber Kasus:</strong></td>
                <td><?php echo $kasus['sumber_kasus']; ?></td>
              </tr>
              <tr>
                <td><strong>Kategori:</strong></td>
                <td><?php echo $kasus['kategori_masalah']; ?></td>
              </tr>
              <tr>
                <td><strong>Status:</strong></td>
                <td>
                  <?php
                  $status_class = '';
                  switch($kasus['status_kasus']) {
                    case 'Baru': $status_class = 'label-warning'; break;
                    case 'Dalam Proses': $status_class = 'label-info'; break;
                    case 'Selesai/Tuntas': $status_class = 'label-success'; break;
                    case 'Dirujuk/Alih Tangan Kasus': $status_class = 'label-danger'; break;
                  }
                  ?>
                  <span class="label <?php echo $status_class; ?>">
                    <?php echo $kasus['status_kasus']; ?>
                  </span>
                </td>
              </tr>
              <tr>
                <td><strong>Guru BK:</strong></td>
                <td><?php echo $kasus['guru_bk_nama']; ?></td>
              </tr>
            </table>
          </div>
          <div class="box-footer">
            <a href="kasus_siswa_edit.php?id=<?php echo $kasus_id; ?>" class="btn btn-warning btn-sm">
              <i class="fa fa-edit"></i> Edit Kasus
            </a>
            <a href="jurnal_tambah.php?kasus_id=<?php echo $kasus_id; ?>" class="btn btn-success btn-sm">
              <i class="fa fa-plus"></i> Tambah Jurnal
            </a>
            <a href="kasus_siswa_cetak.php?id=<?php echo $kasus_id; ?>" class="btn btn-danger btn-sm" target="_blank">
              <i class="fa fa-file-pdf-o"></i> Cetak PDF
            </a>
          </div>
        </div>
      </div>

      <!-- Deskripsi dan Jurnal -->
      <div class="col-md-8">
        <!-- Deskripsi Awal -->
        <div class="box box-info">
          <div class="box-header with-border">
            <h3 class="box-title">
              <i class="fa fa-file-text"></i> Deskripsi Masalah
            </h3>
          </div>
          <div class="box-body">
            <h4><?php echo $kasus['judul_kasus']; ?></h4>
            <p><?php echo nl2br($kasus['deskripsi_awal']); ?></p>
          </div>
        </div>

        <!-- Jurnal Perkembangan -->
        <div class="box box-success">
          <div class="box-header with-border">
            <h3 class="box-title">
              <i class="fa fa-book"></i> Jurnal Perkembangan Kasus
            </h3>
          </div>
          <div class="box-body">
            <style>
            /* Fix timeline visual issues */
            .timeline {
              position: relative;
              padding: 0;
            }
            .timeline::before {
              background: #d2d6de;
              content: '';
              height: 100%;
              left: 30px;
              position: absolute;
              top: 0;
              width: 2px;
              z-index: 1;
            }
            .timeline > div {
              position: relative;
              margin-bottom: 20px;
            }
            .timeline > div > i {
              background: #3c8dbc;
              border-radius: 50%;
              color: #fff;
              font-size: 12px;
              height: 30px;
              left: 18px;
              line-height: 30px;
              position: absolute;
              text-align: center;
              top: 0;
              width: 30px;
              z-index: 2;
            }
            .timeline > div > .timeline-item {
              background: #fff;
              border: 1px solid #d2d6de;
              border-radius: 3px;
              box-shadow: 0 1px 1px rgba(0,0,0,0.1);
              margin-left: 60px;
              margin-right: 15px;
              padding: 0;
              position: relative;
            }
            .timeline > div > .timeline-item > .timeline-header {
              border-bottom: 1px solid #f4f4f4;
              color: #555;
              font-size: 16px;
              line-height: 1.1;
              margin: 0;
              padding: 10px;
            }
            .timeline > div > .timeline-item > .timeline-body,
            .timeline > div > .timeline-item > .timeline-footer {
              padding: 10px;
            }
            .timeline > div > .timeline-item > .timeline-footer {
              background: #f9f9f9;
              border-top: 1px solid #f4f4f4;
            }
            .time-label > span {
              background: #00a65a;
              border-radius: 4px;
              color: #fff;
              display: inline-block;
              font-size: 12px;
              font-weight: 600;
              padding: 5px 10px;
            }
            </style>
            <?php if(mysqli_num_rows($result_jurnal) > 0) { ?>
              <div class="timeline">
                <?php 
                $no_jurnal = 1;
                while($jurnal = mysqli_fetch_assoc($result_jurnal)) { 
                ?>
                <div class="time-label">
                  <span class="bg-green">
                    <?php echo date('d M Y', strtotime($jurnal['tanggal_konseling'])); ?>
                  </span>
                </div>
                
                <div>
                  <i class="fa fa-comments bg-blue"></i>
                  <div class="timeline-item">
                    <h3 class="timeline-header">
                      Sesi Konseling #<?php echo $no_jurnal++; ?>
                    </h3>
                    <div class="timeline-body">
                      <div class="row">
                        <div class="col-md-12">
                          <h5><i class="fa fa-comment"></i> Uraian Sesi:</h5>
                          <p><?php echo nl2br($jurnal['uraian_sesi']); ?></p>
                          
                          <?php if(!empty($jurnal['analisis_diagnosis'])) { ?>
                          <h5><i class="fa fa-search"></i> Analisis/Diagnosis:</h5>
                          <p><?php echo nl2br($jurnal['analisis_diagnosis']); ?></p>
                          <?php } ?>
                          
                          <?php if(!empty($jurnal['tindakan_intervensi'])) { ?>
                          <h5><i class="fa fa-cogs"></i> Tindakan/Intervensi:</h5>
                          <p><?php echo nl2br($jurnal['tindakan_intervensi']); ?></p>
                          <?php } ?>
                          
                          <?php if(!empty($jurnal['rencana_tindak_lanjut'])) { ?>
                          <h5><i class="fa fa-tasks"></i> Rencana Tindak Lanjut:</h5>
                          <p><?php echo nl2br($jurnal['rencana_tindak_lanjut']); ?></p>
                          <?php } ?>
                          
                          <?php if(!empty($jurnal['lampiran_file'])) { ?>
                          <h5><i class="fa fa-paperclip"></i> Lampiran:</h5>
                          <a href="../<?php echo $jurnal['lampiran_file']; ?>" target="_blank" class="btn btn-info btn-xs">
                            <i class="fa fa-download"></i> Download File
                          </a>
                          <?php } ?>
                        </div>
                      </div>
                    </div>
                    <div class="timeline-footer">
                      <a href="jurnal_edit.php?id=<?php echo $jurnal['jurnal_id']; ?>" class="btn btn-warning btn-xs">
                        <i class="fa fa-edit"></i> Edit
                      </a>
                      <a href="jurnal_hapus.php?id=<?php echo $jurnal['jurnal_id']; ?>" 
                         class="btn btn-danger btn-xs" 
                         onclick="return confirm('Yakin ingin menghapus jurnal ini?')">
                        <i class="fa fa-trash"></i> Hapus
                      </a>
                    </div>
                  </div>
                </div>
                
                <!-- Feedback Siswa untuk Jurnal Ini -->
                <?php
                $feedback_result = getFeedbackSiswa($koneksi, $jurnal['jurnal_id']);
                if($feedback_result && mysqli_num_rows($feedback_result) > 0):
                  while($feedback = mysqli_fetch_assoc($feedback_result)):
                ?>
                <div>
                  <i class="fa fa-reply bg-green"></i>
                  <div class="timeline-item">
                    <h3 class="timeline-header">
                      <span class="text-green"><i class="fa fa-user"></i> Feedback dari Siswa</span>
                    </h3>
                    <div class="timeline-body">
                      <div class="well well-sm" style="background-color: #f0f8f0; border-left: 3px solid #00a65a; margin-bottom: 0;">
                        <p style="margin-bottom: 0;"><?php echo nl2br(htmlspecialchars($feedback['feedback_text'])); ?></p>
                      </div>
                    </div>
                    <div class="timeline-footer">
                      <small class="text-muted">
                        <i class="fa fa-clock-o"></i> <?php echo date('d/m/Y H:i', strtotime($feedback['created_at'])); ?>
                        | <i class="fa fa-user"></i> <?php echo htmlspecialchars($feedback['siswa_nama']); ?> (<?php echo $feedback['siswa_nis']; ?>)
                      </small>
                    </div>
                  </div>
                </div>
                <?php
                  endwhile;
                endif;
                ?>
                <?php } ?>
                
                <div>
                  <i class="fa fa-clock-o bg-gray"></i>
                </div>
              </div>
            <?php } else { ?>
              <div class="alert alert-info">
                <i class="fa fa-info-circle"></i> Belum ada jurnal perkembangan untuk kasus ini.
                <a href="jurnal_tambah.php?kasus_id=<?php echo $kasus_id; ?>" class="btn btn-success btn-sm pull-right">
                  <i class="fa fa-plus"></i> Tambah Jurnal Pertama
                </a>
              </div>
            <?php } ?>
          </div>
        </div>
      </div>
    </div>
  </section>
</div>

<?php include 'footer.php'; ?>

<script>
// Update notifikasi badge segera setelah halaman dimuat
$(document).ready(function() {
    // Update notifikasi badge segera (akan di-handle oleh script di footer.php)
    // Jika ada parameter _refresh, force update notifikasi
    <?php if(isset($_GET['_refresh'])): ?>
    setTimeout(function() {
        $.ajax({
            url: 'get_notif_count.php',
            method: 'GET',
            cache: false,
            dataType: 'json',
            success: function(data) {
                var $badge = $('#notif-bell .label');
                if(data.total > 0) {
                    if($badge.length > 0) {
                        $badge.text(data.total);
                    } else {
                        $('#notif-bell').append('<span class="label label-warning">' + data.total + '</span>');
                    }
                } else {
                    $badge.remove();
                }
                
                // Hapus parameter _refresh dari URL
                if(window.history && window.history.replaceState) {
                    var newUrl = window.location.pathname + '?id=<?php echo $kasus_id; ?>';
                    window.history.replaceState({}, '', newUrl);
                }
            }
        });
    }, 100);
    <?php endif; ?>
});
</script>
