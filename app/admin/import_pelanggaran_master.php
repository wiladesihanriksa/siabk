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
        header("location: pelanggaran.php?alert=error&msg=" . urlencode("File harus berformat Excel .xlsx"));
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
                
                $nama_pelanggaran = mysqli_real_escape_string($koneksi, trim($row[0]));
                $point = isset($row[1]) ? intval(trim($row[1])) : 0;
                
                // Validasi data
                if (empty($nama_pelanggaran)) {
                    $errorCount++;
                    $errors[] = "Baris " . ($i + 1) . ": Nama pelanggaran tidak boleh kosong";
                    continue;
                }
                
                if ($point < 0) {
                    $errorCount++;
                    $errors[] = "Baris " . ($i + 1) . ": Point harus berupa angka positif";
                    continue;
                }
                
                // Cek apakah nama pelanggaran sudah ada
                $cek_pelanggaran = mysqli_query($koneksi, "SELECT * FROM pelanggaran WHERE pelanggaran_nama = '$nama_pelanggaran'");
                if (mysqli_num_rows($cek_pelanggaran) > 0) {
                    $errorCount++;
                    $errors[] = "Baris " . ($i + 1) . ": Pelanggaran '$nama_pelanggaran' sudah ada";
                    continue;
                }
                
                // Insert pelanggaran
                $insert_pelanggaran = mysqli_query($koneksi, "INSERT INTO pelanggaran (pelanggaran_nama, pelanggaran_point) VALUES ('$nama_pelanggaran', '$point')");
                
                if ($insert_pelanggaran) {
                    $successCount++;
                } else {
                    $errorCount++;
                    $errors[] = "Baris " . ($i + 1) . ": Gagal menyimpan data pelanggaran - " . mysqli_error($koneksi);
                }
            }
            
            // Hapus file upload
            unlink($uploadFile);
            
            // Redirect dengan pesan
            $message = "Berhasil import $successCount pelanggaran";
            if ($errorCount > 0) {
                $message .= ", $errorCount error";
                if (count($errors) > 0 && count($errors) <= 5) {
                    $message .= ": " . implode(", ", $errors);
                }
            }
            
            $alert = $errorCount > 0 ? 'warning' : 'success';
            header("location: pelanggaran.php?alert=$alert&msg=" . urlencode($message));
            
        } catch (Exception $e) {
            unlink($uploadFile);
            header("location: pelanggaran.php?alert=error&msg=" . urlencode("Error: " . $e->getMessage()));
        }
    } else {
        header("location: pelanggaran.php?alert=error&msg=" . urlencode("Gagal upload file"));
    }
} else {
    header("location: pelanggaran.php?alert=error&msg=" . urlencode("File tidak ditemukan"));
}
?>

