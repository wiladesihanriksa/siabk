<?php
include '../koneksi.php';
session_start();

// Cek session - izinkan administrator dan guru_bk
if(!isset($_SESSION['level']) || ($_SESSION['level'] != "administrator" && $_SESSION['level'] != "guru_bk")){
    header("location:../admin.php?alert=belum_login");
    exit();
}

// Mulai transaksi
mysqli_begin_transaction($koneksi);

try {
    // Ambil data dari form
    $kunjungan_id = isset($_POST['kunjungan_id']) ? intval($_POST['kunjungan_id']) : 0;
    $siswa_id = isset($_POST['siswa_id']) ? intval($_POST['siswa_id']) : 0;
    $tanggal_kunjungan = isset($_POST['tanggal_kunjungan']) ? mysqli_real_escape_string($koneksi, $_POST['tanggal_kunjungan']) : '';
    $waktu_kunjungan = isset($_POST['waktu_kunjungan']) ? mysqli_real_escape_string($koneksi, $_POST['waktu_kunjungan']) : '';
    $alamat_kunjungan = isset($_POST['alamat_kunjungan']) ? mysqli_real_escape_string($koneksi, $_POST['alamat_kunjungan']) : '';
    $petugas_bk_id = isset($_POST['petugas_bk_id']) ? intval($_POST['petugas_bk_id']) : 0;
    $tujuan_kunjungan = isset($_POST['tujuan_kunjungan']) ? mysqli_real_escape_string($koneksi, $_POST['tujuan_kunjungan']) : '';
    $pihak_ditemui = isset($_POST['pihak_ditemui']) ? mysqli_real_escape_string($koneksi, $_POST['pihak_ditemui']) : '';
    $hasil_observasi = isset($_POST['hasil_observasi']) ? mysqli_real_escape_string($koneksi, $_POST['hasil_observasi']) : '';
    $ringkasan_wawancara = isset($_POST['ringkasan_wawancara']) ? mysqli_real_escape_string($koneksi, $_POST['ringkasan_wawancara']) : '';
    $kesimpulan = isset($_POST['kesimpulan']) ? mysqli_real_escape_string($koneksi, $_POST['kesimpulan']) : '';
    $rekomendasi_tindak_lanjut = isset($_POST['rekomendasi_tindak_lanjut']) ? mysqli_real_escape_string($koneksi, $_POST['rekomendasi_tindak_lanjut']) : '';

    // Validasi data
    if($kunjungan_id <= 0) {
        throw new Exception("ID kunjungan tidak valid");
    }
    
    if($siswa_id <= 0) {
        throw new Exception("Pilih siswa yang akan dikunjungi");
    }
    
    if(empty($tanggal_kunjungan)) {
        throw new Exception("Tanggal kunjungan harus diisi");
    }
    
    if(empty($waktu_kunjungan)) {
        throw new Exception("Waktu kunjungan harus diisi");
    }
    
    if(empty($alamat_kunjungan)) {
        throw new Exception("Alamat kunjungan harus diisi");
    }
    
    if($petugas_bk_id <= 0) {
        throw new Exception("Pilih petugas BK yang berkunjung");
    }
    
    if(empty($tujuan_kunjungan)) {
        throw new Exception("Tujuan kunjungan harus diisi");
    }
    
    if(empty($pihak_ditemui)) {
        throw new Exception("Pihak yang ditemui harus diisi");
    }
    
    if(empty($hasil_observasi)) {
        throw new Exception("Hasil observasi lingkungan harus diisi");
    }
    
    if(empty($ringkasan_wawancara)) {
        throw new Exception("Ringkasan hasil wawancara harus diisi");
    }
    
    if(empty($kesimpulan)) {
        throw new Exception("Kesimpulan harus diisi");
    }
    
    if(empty($rekomendasi_tindak_lanjut)) {
        throw new Exception("Rekomendasi/tindak lanjut harus diisi");
    }

    // Set user IP untuk audit trail
    $user_ip = $_SERVER['REMOTE_ADDR'];
    mysqli_query($koneksi, "SET @user_ip = '$user_ip'");

    // Ambil data lama untuk audit
    $query_old = "SELECT k.*, s.siswa_nama, u.user_nama as petugas_nama 
                  FROM kunjungan_rumah k 
                  LEFT JOIN siswa s ON k.siswa_id = s.siswa_id 
                  LEFT JOIN user u ON k.petugas_bk_id = u.user_id 
                  WHERE k.kunjungan_id = '$kunjungan_id'";
    $result_old = mysqli_query($koneksi, $query_old);
    
    if(mysqli_num_rows($result_old) == 0) {
        throw new Exception("Data kunjungan tidak ditemukan");
    }
    
    $data_old = mysqli_fetch_assoc($result_old);

    // Update data kunjungan
    $query_update = "UPDATE kunjungan_rumah SET 
                     siswa_id = '$siswa_id',
                     tanggal_kunjungan = '$tanggal_kunjungan',
                     waktu_kunjungan = '$waktu_kunjungan',
                     alamat_kunjungan = '$alamat_kunjungan',
                     petugas_bk_id = '$petugas_bk_id',
                     tujuan_kunjungan = '$tujuan_kunjungan',
                     pihak_ditemui = '$pihak_ditemui',
                     hasil_observasi = '$hasil_observasi',
                     ringkasan_wawancara = '$ringkasan_wawancara',
                     kesimpulan = '$kesimpulan',
                     rekomendasi_tindak_lanjut = '$rekomendasi_tindak_lanjut',
                     updated_at = NOW()
                     WHERE kunjungan_id = '$kunjungan_id'";

    $result_update = mysqli_query($koneksi, $query_update);
    
    if(!$result_update) {
        throw new Exception("Gagal mengupdate data kunjungan: " . mysqli_error($koneksi));
    }

    // Insert audit trail untuk update
    $query_audit = "INSERT INTO audit_kunjungan (kunjungan_id, user_id, action, description, ip_address) 
                    VALUES ('$kunjungan_id', '{$_SESSION['id']}', 'UPDATE', 'Data kunjungan rumah diperbarui', '$user_ip')";
    mysqli_query($koneksi, $query_audit);

    // Handle file upload jika ada
    if(isset($_FILES['lampiran_foto']) && !empty($_FILES['lampiran_foto']['name'][0])) {
        $upload_dir = '../uploads/kunjungan/';
        if(!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        $allowed_extensions = array('jpg', 'jpeg', 'png');
        $max_file_size = 5 * 1024 * 1024; // 5MB

        foreach($_FILES['lampiran_foto']['name'] as $key => $filename) {
            if($_FILES['lampiran_foto']['error'][$key] == 0) {
                $file_size = $_FILES['lampiran_foto']['size'][$key];
                $file_tmp = $_FILES['lampiran_foto']['tmp_name'][$key];
                $file_ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

                // Validasi ukuran file
                if($file_size > $max_file_size) {
                    throw new Exception("File " . $filename . " terlalu besar. Maksimal 5MB per file.");
                }

                // Validasi ekstensi file
                if(!in_array($file_ext, $allowed_extensions)) {
                    throw new Exception("File " . $filename . " tidak didukung. Hanya JPG, JPEG, PNG yang diizinkan.");
                }

                // Generate nama file unik
                $new_filename = 'kunjungan_' . $kunjungan_id . '_' . time() . '_' . ($key + 1) . '.' . $file_ext;
                $file_path = $upload_dir . $new_filename;

                // Upload file
                if(move_uploaded_file($file_tmp, $file_path)) {
                    // Insert ke database
                    $query_lampiran = "INSERT INTO lampiran_kunjungan (kunjungan_id, nama_file, path_file, ukuran_file, tipe_file) 
                                       VALUES ('$kunjungan_id', '$filename', 'uploads/kunjungan/$new_filename', '$file_size', '$file_ext')";
                    mysqli_query($koneksi, $query_lampiran);
                } else {
                    throw new Exception("Gagal mengupload file " . $filename);
                }
            }
        }
    }

    // Commit transaksi
    mysqli_commit($koneksi);
    
    header("location:kunjungan_rumah_detail.php?id=$kunjungan_id&alert=sukses&pesan=Data kunjungan rumah berhasil diperbarui");
    exit();
    
} catch(Exception $e) {
    // Rollback transaksi
    mysqli_rollback($koneksi);
    
    header("location:kunjungan_rumah_edit.php?id=$kunjungan_id&alert=gagal&pesan=" . urlencode($e->getMessage()));
    exit();
}
?>
