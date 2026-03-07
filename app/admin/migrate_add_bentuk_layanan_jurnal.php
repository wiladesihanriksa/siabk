<?php
// Migrasi: Tambah kolom bentuk_layanan ke tabel jurnal_kasus bila belum ada
include '../koneksi.php';

header('Content-Type: text/plain; charset=utf-8');

if(!$koneksi){
  http_response_code(500);
  echo "Koneksi database gagal"; 
  exit();
}

try {
  // Cek apakah kolom sudah ada
  $sqlCheck = "SELECT COUNT(*) AS jml FROM INFORMATION_SCHEMA.COLUMNS 
               WHERE TABLE_SCHEMA = DATABASE() 
                 AND TABLE_NAME = 'jurnal_kasus' 
                 AND COLUMN_NAME = 'bentuk_layanan'";
  $res = mysqli_query($koneksi, $sqlCheck);
  $row = mysqli_fetch_assoc($res);
  $exists = (int)$row['jml'] > 0;

  if($exists){
    echo "Kolom 'bentuk_layanan' sudah ada pada tabel jurnal_kasus.\n";
  } else {
    $alter = "ALTER TABLE jurnal_kasus 
              ADD COLUMN bentuk_layanan VARCHAR(255) NULL AFTER tanggal_konseling";
    if(mysqli_query($koneksi, $alter)){
      echo "SUKSES: Kolom 'bentuk_layanan' berhasil ditambahkan ke jurnal_kasus.\n";
    } else {
      throw new Exception('Gagal ALTER TABLE: ' . mysqli_error($koneksi));
    }
  }

  // Tambahkan index ringan opsional (tidak wajib)
  @mysqli_query($koneksi, "ALTER TABLE jurnal_kasus ADD INDEX idx_tanggal_konseling (tanggal_konseling)");

  echo "Selesai.";
} catch(Exception $e){
  http_response_code(500);
  echo "ERROR: " . $e->getMessage();
}

mysqli_close($koneksi);
?>


