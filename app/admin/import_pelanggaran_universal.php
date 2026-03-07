<?php
/**
 * Import Data Pelanggaran Universal - Mendukung CSV dan Excel
 */

include '../koneksi.php';

// Cek apakah file diupload
if (!isset($_FILES['file_excel']) || $_FILES['file_excel']['error'] !== UPLOAD_ERR_OK) {
    header('Location: input_pelanggaran.php?alert=error&msg=File tidak ditemukan atau error saat upload');
    exit;
}

$file = $_FILES['file_excel'];

// Validasi ekstensi file
$fileExtension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
if (!in_array($fileExtension, ['csv', 'xlsx'])) {
    header('Location: input_pelanggaran.php?alert=error&msg=Format file harus .csv atau .xlsx');
    exit;
}

// Ambil tahun ajaran aktif
$ta_aktif_query = mysqli_query($koneksi, "SELECT ta_id FROM ta WHERE ta_status = 1");
$ta_aktif_data = mysqli_fetch_assoc($ta_aktif_query);
$ta_aktif_id = $ta_aktif_data['ta_id'];

$data = array();

// Baca file berdasarkan ekstensi
if ($fileExtension == 'xlsx') {
    // Baca Excel
    include '../library/excel_reader.php';
    $excel = new ExcelReader($file['tmp_name']);
    $data = $excel->read();
} else {
    // Baca CSV
    $handle = fopen($file['tmp_name'], 'r');
    while (($row = fgetcsv($handle, 1000, ',')) !== FALSE) {
        $data[] = $row;
    }
    fclose($handle);
}

if (empty($data)) {
    header('Location: input_pelanggaran.php?alert=error&msg=File kosong atau tidak dapat dibaca');
    exit;
}

$success_count = 0;
$error_count = 0;
$errors = array();

// Proses data
foreach ($data as $index => $row) {
    // Skip header
    if ($index == 0) continue;
    
    // Skip baris kosong
    if (empty(array_filter($row))) continue;
    
    // Skip baris referensi (jika ada)
    if (isset($row[0]) && (strpos($row[0], 'REFERENSI') !== false || strpos($row[0], 'ID Pelanggaran') !== false)) {
        continue;
    }
    
    // Validasi data minimal
    if (count($row) < 6) {
        $error_count++;
        $errors[] = "Baris " . ($index + 1) . ": Data tidak lengkap";
        continue;
    }
    
    $nis = trim($row[0]);
    $nama_siswa = trim($row[1]);
    $kelas_nama = trim($row[2]);
    $jurusan_nama = trim($row[3]);
    $pelanggaran_id = trim($row[4]);
    $tanggal = trim($row[5]);
    
    // Validasi data kosong
    if (empty($nis) || empty($nama_siswa) || empty($kelas_nama) || empty($jurusan_nama) || empty($pelanggaran_id) || empty($tanggal)) {
        $error_count++;
        $errors[] = "Baris " . ($index + 1) . ": Data tidak boleh kosong";
        continue;
    }
    
    // Cari siswa berdasarkan NIS
    $siswa_query = mysqli_query($koneksi, "SELECT siswa_id FROM siswa WHERE siswa_nis = '$nis'");
    if (!$siswa_query || mysqli_num_rows($siswa_query) == 0) {
        $error_count++;
        $errors[] = "Baris " . ($index + 1) . ": Siswa dengan NIS '$nis' tidak ditemukan";
        continue;
    }
    $siswa_data = mysqli_fetch_assoc($siswa_query);
    $siswa_id = $siswa_data['siswa_id'];
    
    // Cari kelas berdasarkan nama kelas, jurusan, dan tahun ajaran aktif
    $kelas_query = mysqli_query($koneksi, "SELECT kelas_id FROM kelas k 
        JOIN jurusan j ON k.kelas_jurusan = j.jurusan_id 
        WHERE k.kelas_nama = '$kelas_nama' 
        AND j.jurusan_nama = '$jurusan_nama' 
        AND k.kelas_ta = '$ta_aktif_id'");
    if (!$kelas_query || mysqli_num_rows($kelas_query) == 0) {
        $error_count++;
        $errors[] = "Baris " . ($index + 1) . ": Kelas '$kelas_nama' dengan jurusan '$jurusan_nama' tidak ditemukan di tahun ajaran aktif";
        continue;
    }
    $kelas_data = mysqli_fetch_assoc($kelas_query);
    $kelas_id = $kelas_data['kelas_id'];
    
    // Validasi pelanggaran ID
    $pelanggaran_query = mysqli_query($koneksi, "SELECT pelanggaran_id FROM pelanggaran WHERE pelanggaran_id = '$pelanggaran_id'");
    if (!$pelanggaran_query || mysqli_num_rows($pelanggaran_query) == 0) {
        $error_count++;
        $errors[] = "Baris " . ($index + 1) . ": Pelanggaran dengan ID '$pelanggaran_id' tidak ditemukan di database";
        continue;
    }
    
    // Validasi format tanggal
    if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $tanggal)) {
        $error_count++;
        $errors[] = "Baris " . ($index + 1) . ": Format tanggal tidak valid. Gunakan format YYYY-MM-DD";
        continue;
    }
    
    // Buat waktu dengan jam default
    $waktu = $tanggal . ' 07:00:00';
    
    // Insert data
    $insert_query = "INSERT INTO input_pelanggaran (waktu, siswa, kelas, pelanggaran) VALUES ('$waktu', '$siswa_id', '$kelas_id', '$pelanggaran_id')";
    
    if (mysqli_query($koneksi, $insert_query)) {
        $success_count++;
    } else {
        $error_count++;
        $errors[] = "Baris " . ($index + 1) . ": Gagal menyimpan data - " . mysqli_error($koneksi);
    }
}

// Redirect dengan hasil
$msg = "Import selesai. Berhasil: $success_count, Gagal: $error_count";
if (!empty($errors)) {
    $msg .= ". Error: " . implode(', ', array_slice($errors, 0, 5));
    if (count($errors) > 5) {
        $msg .= " dan " . (count($errors) - 5) . " error lainnya";
    }
}

$alert_type = ($error_count == 0) ? 'success' : (($success_count > 0) ? 'warning' : 'error');
header("Location: input_pelanggaran.php?alert=$alert_type&msg=" . urlencode($msg));
exit;
?>
