<?php
/**
 * Import Data Prestasi dari Excel
 */

include '../koneksi.php';
include '../library/excel_reader.php';

// Ambil tahun ajaran aktif
$ta_aktif_query = mysqli_query($koneksi, "SELECT ta_id FROM ta WHERE ta_status = 1");
$ta_aktif_data = mysqli_fetch_assoc($ta_aktif_query);
$ta_aktif_id = $ta_aktif_data['ta_id'];

// Cek apakah file diupload
if (!isset($_FILES['file_excel']) || $_FILES['file_excel']['error'] !== UPLOAD_ERR_OK) {
    header('Location: input_prestasi.php?alert=error&msg=File Excel tidak ditemukan atau error saat upload');
    exit;
}

$file = $_FILES['file_excel'];

// Validasi ekstensi file
$fileExtension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
if ($fileExtension !== 'xlsx') {
    header('Location: input_prestasi.php?alert=error&msg=Format file harus .xlsx');
    exit;
}

// Baca file Excel
$excel = new ExcelReader($file['tmp_name']);
$data = $excel->read();

$successCount = 0;
$errorCount = 0;
$errors = array();

// Cek data
$totalRows = count($data);

// Proses setiap baris data (skip header)
for ($i = 1; $i < $totalRows; $i++) {
    $row = $data[$i];
    
    // Skip baris kosong
    if (empty($row[1]) || empty($row[2]) || empty($row[3]) || empty($row[4]) || empty($row[5]) || empty($row[6])) {
        continue;
    }
    
    $nis = trim($row[1]);
    $nama_siswa = trim($row[2]);
    $kelas_nama = trim($row[3]);
    $jurusan_nama = trim($row[4]);
    $prestasi_id = trim($row[5]);
    $tanggal = trim($row[6]);
    
    // Validasi data
    if (empty($nis) || empty($nama_siswa) || empty($kelas_nama) || empty($jurusan_nama) || empty($prestasi_id) || empty($tanggal)) {
        $errorCount++;
        $errors[] = "Baris " . ($i + 1) . ": Data tidak lengkap";
        continue;
    }
    
    // Cari siswa berdasarkan NIS
    $siswa_query = mysqli_query($koneksi, "SELECT siswa_id FROM siswa WHERE siswa_nis = '$nis'");
    if (mysqli_num_rows($siswa_query) == 0) {
        $errorCount++;
        $errors[] = "Baris " . ($i + 1) . ": Siswa dengan NIS '$nis' tidak ditemukan";
        continue;
    }
    $siswa_data = mysqli_fetch_assoc($siswa_query);
    $siswa_id = $siswa_data['siswa_id'];
    
    // Cari kelas berdasarkan nama kelas, jurusan, dan tahun ajaran aktif
    $kelas_query = mysqli_query($koneksi, "SELECT kelas_id FROM kelas k, jurusan j WHERE k.kelas_nama = '$kelas_nama' AND k.kelas_jurusan = j.jurusan_id AND j.jurusan_nama = '$jurusan_nama' AND k.kelas_ta = '$ta_aktif_id'");
    if (mysqli_num_rows($kelas_query) == 0) {
        $errorCount++;
        $errors[] = "Baris " . ($i + 1) . ": Kelas '$kelas_nama' dengan jurusan '$jurusan_nama' tidak ditemukan di tahun ajaran aktif";
        continue;
    }
    $kelas_data = mysqli_fetch_assoc($kelas_query);
    $kelas_id = $kelas_data['kelas_id'];
    
    // Validasi ID prestasi
    $prestasi_query = mysqli_query($koneksi, "SELECT prestasi_id FROM prestasi WHERE prestasi_id = '$prestasi_id'");
    if (mysqli_num_rows($prestasi_query) == 0) {
        $errorCount++;
        $errors[] = "Baris " . ($i + 1) . ": ID Prestasi '$prestasi_id' tidak ditemukan di database";
        continue;
    }
    
    // Validasi format tanggal
    $tanggal_formatted = date('Y-m-d H:i:s', strtotime($tanggal));
    if ($tanggal_formatted === '1970-01-01 00:00:00') {
        $errorCount++;
        $errors[] = "Baris " . ($i + 1) . ": Format tanggal tidak valid";
        continue;
    }
    
    // Cek duplikasi (siswa, kelas, prestasi, tanggal yang sama)
    $duplicate_query = mysqli_query($koneksi, "SELECT id FROM input_prestasi WHERE siswa = '$siswa_id' AND kelas = '$kelas_id' AND prestasi = '$prestasi_id' AND DATE(waktu) = DATE('$tanggal_formatted')");
    if (mysqli_num_rows($duplicate_query) > 0) {
        $errorCount++;
        $errors[] = "Baris " . ($i + 1) . ": Data prestasi sudah ada (duplikasi)";
        continue;
    }
    
    // Insert data prestasi
    $insert_query = "INSERT INTO input_prestasi (waktu, siswa, kelas, prestasi) VALUES ('$tanggal_formatted', '$siswa_id', '$kelas_id', '$prestasi_id')";
    
    if (mysqli_query($koneksi, $insert_query)) {
        $successCount++;
    } else {
        $errorCount++;
        $errors[] = "Baris " . ($i + 1) . ": Error database - " . mysqli_error($koneksi);
    }
}

// Redirect dengan pesan hasil
$message = "Import selesai. Berhasil: $successCount, Gagal: $errorCount";
if ($errorCount > 0) {
    $message .= ". Error: " . implode(", ", array_slice($errors, 0, 5));
    if (count($errors) > 5) {
        $message .= " dan " . (count($errors) - 5) . " error lainnya";
    }
}

if ($successCount > 0 && $errorCount == 0) {
    header('Location: input_prestasi.php?alert=success&msg=' . urlencode($message));
} else if ($successCount > 0 && $errorCount > 0) {
    header('Location: input_prestasi.php?alert=warning&msg=' . urlencode($message));
} else {
    header('Location: input_prestasi.php?alert=error&msg=' . urlencode($message));
}
exit;
?>
