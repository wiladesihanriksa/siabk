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
        header("location: prestasi.php?alert=error&msg=" . urlencode("File harus berformat Excel .xlsx"));
        exit;
    }
    
    // Upload file
    $uploadFile = $uploadDir . uniqid() . '_' . $fileName;
    if (move_uploaded_file($fileTmp, $uploadFile)) {
        try {
            // Baca file Excel
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
                
                $nama_prestasi = mysqli_real_escape_string($koneksi, trim($row[0]));
                $point = isset($row[1]) ? intval(trim($row[1])) : 0;
                
                // Validasi data
                if (empty($nama_prestasi)) {
                    $errorCount++;
                    $errors[] = "Baris " . ($i + 1) . ": Nama prestasi tidak boleh kosong";
                    continue;
                }
                
                if ($point < 0) {
                    $errorCount++;
                    $errors[] = "Baris " . ($i + 1) . ": Point harus berupa angka positif";
                    continue;
                }
                
                // Cek apakah nama prestasi sudah ada
                $cek_prestasi = mysqli_query($koneksi, "SELECT * FROM prestasi WHERE prestasi_nama = '$nama_prestasi'");
                if (mysqli_num_rows($cek_prestasi) > 0) {
                    $errorCount++;
                    $errors[] = "Baris " . ($i + 1) . ": Prestasi '$nama_prestasi' sudah ada";
                    continue;
                }
                
                // Insert prestasi
                $insert_prestasi = mysqli_query($koneksi, "INSERT INTO prestasi (prestasi_nama, prestasi_point) VALUES ('$nama_prestasi', '$point')");
                
                if ($insert_prestasi) {
                    $successCount++;
                } else {
                    $errorCount++;
                    $errors[] = "Baris " . ($i + 1) . ": Gagal menyimpan data prestasi - " . mysqli_error($koneksi);
                }
            }
            
            // Hapus file upload
            unlink($uploadFile);
            
            // Redirect dengan pesan
            $message = "Berhasil import $successCount prestasi";
            if ($errorCount > 0) {
                $message .= ", $errorCount error";
                if (count($errors) > 0 && count($errors) <= 5) {
                    $message .= ": " . implode(", ", $errors);
                }
            }
            
            $alert = $errorCount > 0 ? 'warning' : 'success';
            header("location: prestasi.php?alert=$alert&msg=" . urlencode($message));
            
        } catch (Exception $e) {
            unlink($uploadFile);
            header("location: prestasi.php?alert=error&msg=" . urlencode("Error: " . $e->getMessage()));
        }
    } else {
        header("location: prestasi.php?alert=error&msg=" . urlencode("Gagal upload file"));
    }
} else {
    header("location: prestasi.php?alert=error&msg=" . urlencode("File tidak ditemukan"));
}
?>

