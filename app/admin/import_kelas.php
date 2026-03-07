<?php
include '../koneksi.php';
include '../library/excel_reader.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['file_excel'])) {
    $uploadDir = '../uploads/';
    if (!file_exists($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }
    
    $fileName = $_FILES['file_excel']['name'];
    $fileTmp = $_FILES['file_excel']['tmp_name'];
    $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
    
    // Validasi file
    if ($fileExt != 'xlsx') {
        header("location: kelas.php?alert=error&msg=" . urlencode("File harus berformat Excel .xlsx"));
        exit;
    }
    
    // Upload file
    $uploadFile = $uploadDir . uniqid() . '_' . $fileName;
    if (move_uploaded_file($fileTmp, $uploadFile)) {
        try {
            // Baca file Excel (sheet pertama - Data Kelas)
            $excel = new ExcelReader($uploadFile);
            $data = $excel->read();
            
            $successCount = 0;
            $errorCount = 0;
            $errors = array();
            
            // Skip header (baris pertama)
            for ($i = 1; $i < count($data); $i++) {
                $row = $data[$i];
                
                // Pastikan ada data minimal
                if (empty($row[0])) {
                    continue; // Skip baris kosong
                }
                
                $nama_kelas = mysqli_real_escape_string($koneksi, trim($row[0]));
                $jurusan_id = isset($row[1]) ? intval(trim($row[1])) : 0;
                $ta_id = isset($row[2]) ? intval(trim($row[2])) : 0;
                
                // Validasi data
                if (empty($nama_kelas)) {
                    $errorCount++;
                    $errors[] = "Baris " . ($i + 1) . ": Nama kelas tidak boleh kosong";
                    continue;
                }
                
                if ($jurusan_id <= 0) {
                    $errorCount++;
                    $errors[] = "Baris " . ($i + 1) . ": ID Jurusan tidak valid";
                    continue;
                }
                
                if ($ta_id <= 0) {
                    $errorCount++;
                    $errors[] = "Baris " . ($i + 1) . ": ID Tahun Ajaran tidak valid";
                    continue;
                }
                
                // Cek apakah jurusan ada
                $cek_jurusan = mysqli_query($koneksi, "SELECT * FROM jurusan WHERE jurusan_id = '$jurusan_id'");
                if (mysqli_num_rows($cek_jurusan) == 0) {
                    $errorCount++;
                    $errors[] = "Baris " . ($i + 1) . ": Jurusan dengan ID $jurusan_id tidak ditemukan";
                    continue;
                }
                
                // Cek apakah tahun ajaran ada
                $cek_ta = mysqli_query($koneksi, "SELECT * FROM ta WHERE ta_id = '$ta_id'");
                if (mysqli_num_rows($cek_ta) == 0) {
                    $errorCount++;
                    $errors[] = "Baris " . ($i + 1) . ": Tahun Ajaran dengan ID $ta_id tidak ditemukan";
                    continue;
                }
                
                // Cek apakah kelas sudah ada (nama kelas + tahun ajaran + jurusan)
                $cek_kelas = mysqli_query($koneksi, "SELECT * FROM kelas WHERE kelas_nama = '$nama_kelas' AND kelas_ta = '$ta_id' AND kelas_jurusan = '$jurusan_id'");
                if (mysqli_num_rows($cek_kelas) > 0) {
                    $errorCount++;
                    $errors[] = "Baris " . ($i + 1) . ": Kelas '$nama_kelas' sudah ada untuk tahun ajaran dan jurusan tersebut";
                    continue;
                }
                
                // Insert kelas
                $insert_kelas = mysqli_query($koneksi, "INSERT INTO kelas (kelas_nama, kelas_jurusan, kelas_ta) VALUES ('$nama_kelas', '$jurusan_id', '$ta_id')");
                
                if ($insert_kelas) {
                    $successCount++;
                } else {
                    $errorCount++;
                    $errors[] = "Baris " . ($i + 1) . ": Gagal menyimpan data kelas - " . mysqli_error($koneksi);
                }
            }
            
            // Hapus file upload
            unlink($uploadFile);
            
            // Redirect dengan pesan
            $message = "Berhasil import $successCount kelas";
            if ($errorCount > 0) {
                $message .= ", $errorCount error";
                if (count($errors) > 0 && count($errors) <= 5) {
                    $message .= ": " . implode(", ", $errors);
                }
            }
            
            $alert = $errorCount > 0 ? 'warning' : 'success';
            header("location: kelas.php?alert=$alert&msg=" . urlencode($message));
            
        } catch (Exception $e) {
            unlink($uploadFile);
            header("location: kelas.php?alert=error&msg=" . urlencode("Error: " . $e->getMessage()));
        }
    } else {
        header("location: kelas.php?alert=error&msg=" . urlencode("Gagal upload file"));
    }
} else {
    header("location: kelas.php?alert=error&msg=" . urlencode("File tidak ditemukan"));
}
?>

