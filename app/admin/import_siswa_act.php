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
    if ($fileExt != 'xlsx' && $fileExt != 'xls') {
        header("location: kelas_siswa.php?id=".$_POST['kelas_id']."&alert=error&msg=File harus berformat Excel (.xlsx atau .xls)");
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
                if (empty($row[0]) || empty($row[1])) {
                    continue;
                }
                
                $nis = mysqli_real_escape_string($koneksi, trim($row[0]));
                $nama = mysqli_real_escape_string($koneksi, trim($row[1]));
                $jurusan_nama = mysqli_real_escape_string($koneksi, trim($row[2]));
                $status = mysqli_real_escape_string($koneksi, isset($row[3]) ? trim($row[3]) : 'Aktif');
                $password = isset($row[4]) ? trim($row[4]) : 'siswa';
                
                // Cek apakah NIS sudah ada
                $cek_nis = mysqli_query($koneksi, "SELECT * FROM siswa WHERE siswa_nis = '$nis'");
                if (mysqli_num_rows($cek_nis) > 0) {
                    $errorCount++;
                    $errors[] = "Baris " . ($i + 1) . ": NIS $nis sudah ada";
                    continue;
                }
                
                // Cari ID jurusan
                $jurusan_query = mysqli_query($koneksi, "SELECT * FROM jurusan WHERE jurusan_nama = '$jurusan_nama'");
                if (mysqli_num_rows($jurusan_query) == 0) {
                    $errorCount++;
                    $errors[] = "Baris " . ($i + 1) . ": Jurusan '$jurusan_nama' tidak ditemukan";
                    continue;
                }
                $jurusan_data = mysqli_fetch_array($jurusan_query);
                $jurusan_id = mysqli_real_escape_string($koneksi, $jurusan_data['jurusan_id']);
                
                // Hash password
                $password_hash = md5($password);
                
                // Insert siswa
                $insert_siswa = mysqli_query($koneksi, "INSERT INTO siswa (siswa_nama, siswa_nis, siswa_jurusan, siswa_status, siswa_password) VALUES ('$nama', '$nis', '$jurusan_id', '$status', '$password_hash')");
                
                if ($insert_siswa) {
                    $siswa_id = mysqli_insert_id($koneksi);
                    
                    // Tambahkan ke kelas jika ada kelas_id
                    if (isset($_POST['kelas_id']) && !empty($_POST['kelas_id'])) {
                        $kelas_id = mysqli_real_escape_string($koneksi, $_POST['kelas_id']);
                        $insert_kelas = mysqli_query($koneksi, "INSERT INTO kelas_siswa (ks_siswa, ks_kelas) VALUES ('$siswa_id', '$kelas_id')");
                        if (!$insert_kelas) {
                            $errorCount++;
                            $errors[] = "Baris " . ($i + 1) . ": Gagal menambahkan ke kelas";
                        }
                    }
                    
                    $successCount++;
                } else {
                    $errorCount++;
                    $errors[] = "Baris " . ($i + 1) . ": Gagal menyimpan data siswa";
                }
            }
            
            // Hapus file upload
            unlink($uploadFile);
            
            // Redirect dengan pesan
            $message = "Berhasil import $successCount siswa";
            if ($errorCount > 0) {
                $message .= ", $errorCount error";
            }
            
            $alert = $errorCount > 0 ? 'warning' : 'success';
            header("location: kelas_siswa.php?id=".$_POST['kelas_id']."&alert=$alert&msg=".urlencode($message));
            
        } catch (Exception $e) {
            unlink($uploadFile);
            header("location: kelas_siswa.php?id=".$_POST['kelas_id']."&alert=error&msg=".urlencode("Error: " . $e->getMessage()));
        }
    } else {
        header("location: kelas_siswa.php?id=".$_POST['kelas_id']."&alert=error&msg=Gagal upload file");
    }
} else {
    header("location: kelas_siswa.php?id=".$_POST['kelas_id']."&alert=error&msg=File tidak ditemukan");
}
?>
