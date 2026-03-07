<?php
include '../koneksi.php';
session_start();

// Cek session untuk administrator dan guru BK
if(!isset($_SESSION['level']) || ($_SESSION['level'] != "administrator" && $_SESSION['level'] != "guru_bk")){
    header("location:../admin.php?alert=belum_login");
    exit();
}

if(isset($_POST['simpan'])) {
    // Ambil data dari form
    $siswa_id = mysqli_real_escape_string($koneksi, $_POST['siswa_id']);
    $tanggal_kunjungan = mysqli_real_escape_string($koneksi, $_POST['tanggal_kunjungan']);
    $waktu_kunjungan = mysqli_real_escape_string($koneksi, $_POST['waktu_kunjungan']);
    $alamat_kunjungan = mysqli_real_escape_string($koneksi, $_POST['alamat_kunjungan']);
    $petugas_bk_id = mysqli_real_escape_string($koneksi, $_POST['petugas_bk_id']);
    $tujuan_kunjungan = mysqli_real_escape_string($koneksi, $_POST['tujuan_kunjungan']);
    $pihak_ditemui = mysqli_real_escape_string($koneksi, $_POST['pihak_ditemui']);
    $hasil_observasi = mysqli_real_escape_string($koneksi, $_POST['hasil_observasi']);
    $ringkasan_wawancara = mysqli_real_escape_string($koneksi, $_POST['ringkasan_wawancara']);
    $kesimpulan = mysqli_real_escape_string($koneksi, $_POST['kesimpulan']);
    $rekomendasi_tindak_lanjut = mysqli_real_escape_string($koneksi, $_POST['rekomendasi_tindak_lanjut']);

    // Validasi data
    if(empty($siswa_id) || empty($tanggal_kunjungan) || empty($waktu_kunjungan) || 
       empty($alamat_kunjungan) || empty($petugas_bk_id) || empty($tujuan_kunjungan) ||
       empty($pihak_ditemui) || empty($hasil_observasi) || empty($ringkasan_wawancara) ||
       empty($kesimpulan) || empty($rekomendasi_tindak_lanjut)) {
        header("location:kunjungan_rumah_tambah.php?alert=gagal&pesan=Data tidak lengkap");
        exit();
    }

    // Mulai transaksi
    mysqli_begin_transaction($koneksi);

    try {
        // Set user IP untuk audit trail
        $user_ip = $_SERVER['REMOTE_ADDR'];
        mysqli_query($koneksi, "SET @user_ip = '$user_ip'");

        // Generate kode kunjungan otomatis
        $year_part = date('Y');
        $month_part = date('m');
        $prefix = 'KV' . $year_part . $month_part;
        
        // Cari nomor urut terakhir untuk bulan ini
        $query_count = "SELECT COALESCE(MAX(CAST(SUBSTRING(kunjungan_kode, 8) AS UNSIGNED)), 0) + 1 as next_number 
                        FROM kunjungan_rumah 
                        WHERE kunjungan_kode LIKE '$prefix%'";
        $result_count = mysqli_query($koneksi, $query_count);
        $data_count = mysqli_fetch_assoc($result_count);
        $next_number = $data_count['next_number'];
        
        $kunjungan_kode = $prefix . str_pad($next_number, 4, '0', STR_PAD_LEFT);

        // Insert data kunjungan
        $query_kunjungan = "INSERT INTO kunjungan_rumah (kunjungan_kode, siswa_id, tanggal_kunjungan, waktu_kunjungan, 
                           alamat_kunjungan, petugas_bk_id, tujuan_kunjungan, pihak_ditemui, hasil_observasi, 
                           ringkasan_wawancara, kesimpulan, rekomendasi_tindak_lanjut) 
                           VALUES ('$kunjungan_kode', '$siswa_id', '$tanggal_kunjungan', '$waktu_kunjungan', 
                           '$alamat_kunjungan', '$petugas_bk_id', '$tujuan_kunjungan', '$pihak_ditemui', 
                           '$hasil_observasi', '$ringkasan_wawancara', '$kesimpulan', '$rekomendasi_tindak_lanjut')";
        
        $result_kunjungan = mysqli_query($koneksi, $query_kunjungan);
        
        if(!$result_kunjungan) {
            throw new Exception("Gagal menyimpan data kunjungan: " . mysqli_error($koneksi));
        }

        $kunjungan_id = mysqli_insert_id($koneksi);

        // Insert audit trail manual (tanpa trigger)
        $query_audit = "INSERT INTO audit_kunjungan (kunjungan_id, user_id, action, description, ip_address) 
                        VALUES ('$kunjungan_id', '$petugas_bk_id', 'CREATE', 'Kunjungan rumah baru dibuat', '$user_ip')";
        mysqli_query($koneksi, $query_audit);

        // Handle file upload jika ada
        if(isset($_FILES['lampiran_foto']) && !empty($_FILES['lampiran_foto']['name'][0])) {
            $upload_dir = '../uploads/kunjungan/';
            if(!is_dir($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }

            $files = $_FILES['lampiran_foto'];
            $file_count = count($files['name']);
            
            for($i = 0; $i < $file_count; $i++) {
                if($files['error'][$i] == 0) {
                    $file_name = $files['name'][$i];
                    $file_tmp = $files['tmp_name'][$i];
                    $file_size = $files['size'][$i];
                    $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
                    
                    // Validasi file
                    $allowed_ext = array('jpg', 'jpeg', 'png');
                    if(!in_array($file_ext, $allowed_ext)) {
                        throw new Exception("Format file tidak diizinkan. Gunakan: JPG, JPEG, PNG");
                    }
                    
                    if($file_size > 5 * 1024 * 1024) { // 5MB
                        throw new Exception("Ukuran file terlalu besar. Maksimal 5MB per file");
                    }

                    $new_file_name = 'kunjungan_' . $kunjungan_id . '_' . time() . '_' . ($i + 1) . '.' . $file_ext;
                    $upload_path = $upload_dir . $new_file_name;
                    
                    if(move_uploaded_file($file_tmp, $upload_path)) {
                        // Simpan info file ke database
                        $query_lampiran = "INSERT INTO lampiran_kunjungan (kunjungan_id, nama_file, path_file, ukuran_file, tipe_file) 
                                          VALUES ('$kunjungan_id', '$file_name', 'uploads/kunjungan/$new_file_name', '$file_size', '$file_ext')";
                        mysqli_query($koneksi, $query_lampiran);
                    } else {
                        throw new Exception("Gagal mengupload file: " . $file_name);
                    }
                }
            }
        }

        // Commit transaksi
        mysqli_commit($koneksi);
        
        header("location:kunjungan_rumah.php?alert=sukses&pesan=Kunjungan rumah berhasil disimpan");
        exit();

    } catch(Exception $e) {
        // Rollback transaksi
        mysqli_rollback($koneksi);
        
        // Hapus file yang sudah terupload jika ada
        if(isset($upload_path) && file_exists($upload_path)) {
            unlink($upload_path);
        }
        
        header("location:kunjungan_rumah_tambah.php?alert=gagal&pesan=" . urlencode($e->getMessage()));
        exit();
    }
} else {
    header("location:kunjungan_rumah.php?alert=gagal&pesan=Akses tidak valid");
    exit();
}
?>
