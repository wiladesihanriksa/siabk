<?php include 'header_dynamic.php'; ?>

<div class="content-wrapper">
  <section class="content-header">
    <h1>
      Cetak Raport
      <small>Pilih Kelas untuk Cetak Raport PDF</small>
    </h1>
    <ol class="breadcrumb">
      <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
      <li><a href="laporan.php">Laporan</a></li>
      <li class="active">Cetak Raport</li>
    </ol>
  </section>

  <section class="content">
    <div class="row">
      <section class="col-lg-12">
        <div class="box box-primary">
          <div class="box-header">
            <h3 class="box-title">Pilih Kelas untuk Cetak Raport</h3>
          </div>
          <div class="box-body">
            <div class="row">
              <?php
              $kelas_query = mysqli_query($koneksi, "SELECT k.*, j.jurusan_nama, ta.ta_nama, 
                  (SELECT COUNT(*) FROM kelas_siswa ks WHERE ks.ks_kelas = k.kelas_id) as jumlah_siswa
                  FROM kelas k 
                  JOIN jurusan j ON k.kelas_jurusan = j.jurusan_id 
                  JOIN ta ON k.kelas_ta = ta.ta_id 
                  WHERE ta.ta_status = 1 
                  ORDER BY k.kelas_nama");
              
              while($k = mysqli_fetch_assoc($kelas_query)) {
                echo "<div class='col-md-4'>";
                echo "<div class='box box-info'>";
                echo "<div class='box-header with-border'>";
                echo "<h3 class='box-title'>{$k['kelas_nama']}</h3>";
                echo "</div>";
                echo "<div class='box-body'>";
                echo "<p><strong>Jurusan:</strong> {$k['jurusan_nama']}</p>";
                echo "<p><strong>Tahun Ajaran:</strong> {$k['ta_nama']}</p>";
                echo "<p><strong>Jumlah Siswa:</strong> {$k['jumlah_siswa']} orang</p>";
                echo "<div class='btn-group'>";
                echo "<a href='raport_kelas_pdf.php?kelas_id={$k['kelas_id']}' target='_blank' class='btn btn-primary btn-sm'>";
                echo "<i class='fa fa-file-pdf-o'></i> Cetak Raport Kelas";
                echo "</a>";
                echo "</div>";
                echo "</div>";
                echo "</div>";
                echo "</div>";
              }
              ?>
            </div>
          </div>
        </div>

        <div class="box box-success">
          <div class="box-header">
            <h3 class="box-title">Cetak Raport Per Siswa</h3>
          </div>
          <div class="box-body">
            <p>Untuk mencetak raport per siswa, silakan:</p>
            <ol>
              <li>Buka halaman <a href="laporan.php">Laporan</a></li>
              <li>Filter data sesuai kebutuhan</li>
              <li>Klik tombol "Raport" di kolom Opsi untuk setiap siswa</li>
            </ol>
          </div>
        </div>
      </section>
    </div>
  </section>
</div>

<?php include 'footer.php'; ?>
