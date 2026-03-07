<?php include 'header.php';

// Cek apakah parameter id ada
if(!isset($_GET['id']) || empty($_GET['id'])) {
    header("location:konseling_saya.php?alert=error&msg=" . urlencode("ID konseling tidak valid"));
    exit();
}

$kasus_id = mysqli_real_escape_string($koneksi, $_GET['id']);
$siswa_id = $_SESSION['id'];

// Ambil data kasus
$query_kasus = "SELECT k.*, s.siswa_nama, s.siswa_nis, g.nama_guru_bk 
                FROM kasus_siswa k 
                LEFT JOIN siswa s ON k.siswa_id = s.siswa_id
                LEFT JOIN guru_bk g ON k.guru_bk_id = g.guru_bk_id
                WHERE k.kasus_id = '$kasus_id' AND k.siswa_id = '$siswa_id'";
$result_kasus = mysqli_query($koneksi, $query_kasus);

if(!$result_kasus || mysqli_num_rows($result_kasus) == 0) {
    header("location:konseling_saya.php?alert=error&msg=" . urlencode("Data konseling tidak ditemukan"));
    exit();
}

$kasus = mysqli_fetch_assoc($result_kasus);

// Ambil data jurnal kasus jika ada
$query_jurnal = "SELECT * FROM jurnal_kasus WHERE kasus_id = '$kasus_id' ORDER BY tanggal_konseling DESC";
$result_jurnal = mysqli_query($koneksi, $query_jurnal);
?>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <section class="content-header">
    <h1>
      <i class="fa fa-eye"></i> Detail Konseling
      <small>Informasi Lengkap Konseling</small>
    </h1>
    <ol class="breadcrumb">
      <li><a href="index.php"><i class="fa fa-dashboard"></i> Dashboard</a></li>
      <li><a href="konseling_saya.php">Konseling Saya</a></li>
      <li class="active">Detail Konseling</li>
    </ol>
  </section>

  <!-- Main content -->
  <section class="content">
    <?php 
    if(isset($_GET['alert']) && isset($_GET['msg'])){
      $alert = $_GET['alert'];
      $msg = $_GET['msg'];
      
      if($alert == 'success'){
        echo '<div class="alert alert-success alert-dismissible">
                <button type="button" class="close" data-dismiss="alert">&times;</button>
                <i class="fa fa-check-circle"></i> '.$msg.'
              </div>';
      } else if($alert == 'error'){
        echo '<div class="alert alert-danger alert-dismissible">
                <button type="button" class="close" data-dismiss="alert">&times;</button>
                <i class="fa fa-times-circle"></i> '.$msg.'
              </div>';
      }
    }
    ?>
    <div class="row">
      <!-- Informasi Kasus -->
      <div class="col-md-12">
        <div class="box box-primary">
          <div class="box-header with-border">
            <h3 class="box-title">Informasi Konseling</h3>
            <div class="box-tools pull-right">
              <a href="konseling_saya.php" class="btn btn-default btn-sm">
                <i class="fa fa-arrow-left"></i> Kembali
              </a>
            </div>
          </div>
          
          <div class="box-body">
            <div class="row">
              <div class="col-md-6">
                <table class="table table-bordered">
                  <tr>
                    <th width="40%">Kode Kasus</th>
                    <td><?php echo $kasus['kasus_kode']; ?></td>
                  </tr>
                  <tr>
                    <th>Judul Konseling</th>
                    <td><?php echo htmlspecialchars($kasus['judul_kasus']); ?></td>
                  </tr>
                  <tr>
                    <th>Kategori Masalah</th>
                    <td><?php echo $kasus['kategori_masalah']; ?></td>
                  </tr>
                  <tr>
                    <th>Sumber/Rujukan</th>
                    <td><?php echo $kasus['sumber_kasus']; ?></td>
                  </tr>
                  <tr>
                    <th>Tanggal Pelaporan</th>
                    <td><?php echo date('d/m/Y', strtotime($kasus['tanggal_pelaporan'])); ?></td>
                  </tr>
                </table>
              </div>
              
              <div class="col-md-6">
                <table class="table table-bordered">
                  <tr>
                    <th width="40%">Status</th>
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
                    <th>Guru BK Penanggung Jawab</th>
                    <td><?php echo htmlspecialchars($kasus['nama_guru_bk'] ?: '-'); ?></td>
                  </tr>
                  <tr>
                    <th>Tanggal Dibuat</th>
                    <td><?php echo date('d/m/Y H:i', strtotime($kasus['created_at'])); ?></td>
                  </tr>
                  <tr>
                    <th>Terakhir Diupdate</th>
                    <td><?php echo date('d/m/Y H:i', strtotime($kasus['updated_at'])); ?></td>
                  </tr>
                </table>
              </div>
            </div>
            
            <div class="row">
              <div class="col-md-12">
                <h4>Deskripsi Masalah</h4>
                <div class="well">
                  <?php echo nl2br(htmlspecialchars($kasus['deskripsi_awal'])); ?>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      
      <!-- Jurnal Konseling -->
      <?php if(mysqli_num_rows($result_jurnal) > 0): ?>
      <div class="col-md-12">
        <div class="box box-info">
          <div class="box-header with-border">
            <h3 class="box-title">Riwayat Konseling</h3>
          </div>
          
          <div class="box-body">
            <style>
              /* Sembunyikan garis vertikal timeline yang tidak perlu */
              .timeline::before {
                display: none !important;
              }
              
              /* Atau jika ingin tetap ada tapi lebih pendek, gunakan ini */
              /* .timeline::before {
                height: auto !important;
                bottom: 0 !important;
              } */
              
              /* Perbaiki spacing timeline item */
              .timeline > div {
                margin-bottom: 20px;
              }
              
              .timeline > div:last-child {
                margin-bottom: 0;
              }
            </style>
            <div class="timeline">
              <?php
              $jurnal_count = mysqli_num_rows($result_jurnal);
              $jurnal_index = 0;
              mysqli_data_seek($result_jurnal, 0); // Reset pointer
              
              while($jurnal = mysqli_fetch_assoc($result_jurnal)) {
                $jurnal_index++;
                $is_last = ($jurnal_index == $jurnal_count);
              ?>
              <div class="time-label">
                <span class="bg-blue"><?php echo date('d M Y', strtotime($jurnal['tanggal_konseling'])); ?></span>
              </div>
              
              <div>
                <i class="fa fa-comments bg-blue"></i>
                <div class="timeline-item">
                  <h3 class="timeline-header">Sesi Konseling</h3>
                  <div class="timeline-body">
                    <h4>Uraian Sesi:</h4>
                    <p><?php echo nl2br(htmlspecialchars($jurnal['uraian_sesi'])); ?></p>
                    
                    <?php if(!empty($jurnal['analisis_diagnosis'])): ?>
                    <h4>Analisis & Diagnosis:</h4>
                    <p><?php echo nl2br(htmlspecialchars($jurnal['analisis_diagnosis'])); ?></p>
                    <?php endif; ?>
                    
                    <?php if(!empty($jurnal['tindakan_intervensi'])): ?>
                    <h4>Tindakan Intervensi:</h4>
                    <p><?php echo nl2br(htmlspecialchars($jurnal['tindakan_intervensi'])); ?></p>
                    <?php endif; ?>
                    
                    <?php if(!empty($jurnal['rencana_tindak_lanjut'])): ?>
                    <h4>Rencana Tindak Lanjut:</h4>
                    <p><?php echo nl2br(htmlspecialchars($jurnal['rencana_tindak_lanjut'])); ?></p>
                    <?php endif; ?>
                  </div>
                  <div class="timeline-footer">
                    <small class="text-muted">
                      <i class="fa fa-clock-o"></i> <?php echo date('d/m/Y H:i', strtotime($jurnal['created_at'])); ?>
                    </small>
                  </div>
                </div>
              </div>
              
              <!-- Feedback Siswa untuk Jurnal Ini -->
              <?php
              // Ambil feedback siswa untuk jurnal ini
              $feedback_query = "SELECT f.*, s.siswa_nama 
                                FROM feedback_siswa f 
                                LEFT JOIN siswa s ON f.siswa_id = s.siswa_id 
                                WHERE f.jurnal_id = '" . $jurnal['jurnal_id'] . "' 
                                ORDER BY f.created_at ASC";
              $feedback_result = mysqli_query($koneksi, $feedback_query);
              
              if($feedback_result && mysqli_num_rows($feedback_result) > 0):
                while($feedback = mysqli_fetch_assoc($feedback_result)):
              ?>
              <div>
                <i class="fa fa-reply bg-green"></i>
                <div class="timeline-item">
                  <h3 class="timeline-header">
                    <span class="text-green"><i class="fa fa-user"></i> Feedback Saya</span>
                  </h3>
                  <div class="timeline-body">
                    <div class="well well-sm" style="background-color: #f0f8f0; border-left: 3px solid #00a65a;">
                      <p><?php echo nl2br(htmlspecialchars($feedback['feedback_text'])); ?></p>
                    </div>
                  </div>
                  <div class="timeline-footer">
                    <small class="text-muted">
                      <i class="fa fa-clock-o"></i> <?php echo date('d/m/Y H:i', strtotime($feedback['created_at'])); ?>
                    </small>
                  </div>
                </div>
              </div>
              <?php
                endwhile;
              endif;
              ?>
              
              <!-- Form Feedback Siswa -->
              <div>
                <i class="fa fa-edit bg-yellow"></i>
                <div class="timeline-item">
                  <h3 class="timeline-header">
                    <span class="text-yellow"><i class="fa fa-commenting"></i> Berikan Feedback / Tanggapan</span>
                  </h3>
                  <div class="timeline-body">
                    <div class="alert alert-info" style="margin-bottom: 15px;">
                      <i class="fa fa-info-circle"></i> 
                      <strong>Petunjuk:</strong> Berikan feedback atau tanggapan Anda terhadap arahan dari Guru BK. 
                      Anda dapat menceritakan perkembangan, kesulitan yang dihadapi, atau pertanyaan terkait arahan yang diberikan.
                    </div>
                    <form method="POST" action="konseling_feedback_act.php" class="feedback-form">
                      <input type="hidden" name="jurnal_id" value="<?php echo $jurnal['jurnal_id']; ?>">
                      <input type="hidden" name="kasus_id" value="<?php echo $kasus_id; ?>">
                      <input type="hidden" name="siswa_id" value="<?php echo $siswa_id; ?>">
                      
                      <div class="form-group">
                        <label>Feedback / Tanggapan Anda: <span class="text-red">*</span></label>
                        <textarea name="feedback_text" class="form-control" rows="5" 
                                  placeholder="Contoh: Saya sudah mencoba membaca buku setiap malam seperti yang disarankan, namun masih kesulitan untuk fokus. Apakah ada tips tambahan yang bisa diberikan?" required></textarea>
                        <small class="text-muted">
                          <i class="fa fa-lightbulb-o"></i> 
                          Bagikan perkembangan, kesulitan, atau pertanyaan terkait arahan yang diberikan oleh Guru BK
                        </small>
                      </div>
                      
                      <button type="submit" class="btn btn-success">
                        <i class="fa fa-send"></i> Kirim Feedback
                      </button>
                    </form>
                  </div>
                </div>
              </div>
              <?php
              }
              ?>
              <!-- Tutup timeline dengan elemen akhir untuk mencegah garis panjang -->
              <div>
                <i class="fa fa-clock-o bg-gray"></i>
              </div>
            </div>
          </div>
        </div>
      </div>
      <?php else: ?>
      <div class="col-md-12">
        <div class="box box-info">
          <div class="box-body">
            <div class="alert alert-info">
              <i class="fa fa-info-circle"></i> Belum ada riwayat konseling. Guru BK akan menambahkan jurnal setelah melakukan sesi konseling.
            </div>
          </div>
        </div>
      </div>
      <?php endif; ?>
    </div>
  </section>
</div>

<?php include 'footer.php'; ?>

