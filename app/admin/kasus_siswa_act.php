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
    $tanggal_pelaporan_raw = mysqli_real_escape_string($koneksi, $_POST['tanggal_pelaporan']);
    $sumber_kasus = mysqli_real_escape_string($koneksi, $_POST['sumber_kasus']);
    $kategori_masalah = mysqli_real_escape_string($koneksi, $_POST['kategori_masalah']);
    $judul_kasus = mysqli_real_escape_string($koneksi, $_POST['judul_kasus']);
    $deskripsi_awal = mysqli_real_escape_string($koneksi, $_POST['deskripsi_awal']);
    $status_kasus = mysqli_real_escape_string($koneksi, $_POST['status_kasus']);
    $guru_bk_id = mysqli_real_escape_string($koneksi, $_POST['guru_bk_id']);

    // Konversi format tanggal dari dd/mm/yyyy ke yyyy-mm-dd
    $tanggal_pelaporan = '';
    if(!empty($tanggal_pelaporan_raw)) {
        $date_parts = explode('/', $tanggal_pelaporan_raw);
        if(count($date_parts) == 3) {
            $tanggal_pelaporan = $date_parts[2] . '-' . $date_parts[1] . '-' . $date_parts[0];
        } else {
            // Jika format tidak sesuai, gunakan tanggal hari ini
            $tanggal_pelaporan = date('Y-m-d');
        }
    } else {
        $tanggal_pelaporan = date('Y-m-d');
    }

    // Data jurnal (opsional)
    $tanggal_konseling = mysqli_real_escape_string($koneksi, $_POST['tanggal_konseling']);
    $uraian_sesi = mysqli_real_escape_string($koneksi, $_POST['uraian_sesi']);
    $analisis_diagnosis = mysqli_real_escape_string($koneksi, $_POST['analisis_diagnosis']);
    $tindakan_intervensi = mysqli_real_escape_string($koneksi, $_POST['tindakan_intervensi']);
    $rencana_tindak_lanjut = mysqli_real_escape_string($koneksi, $_POST['rencana_tindak_lanjut']);

    // Validasi data
    if(empty($siswa_id) || empty($tanggal_pelaporan) || empty($sumber_kasus) || 
       empty($kategori_masalah) || empty($judul_kasus) || empty($guru_bk_id)) {
        header("location:kasus_siswa_tambah.php?alert=gagal&pesan=Data tidak lengkap");
        exit();
    }

    // Mulai transaksi
    mysqli_begin_transaction($koneksi);

    try {
        // Set user IP untuk audit trail
        $user_ip = $_SERVER['REMOTE_ADDR'];
        mysqli_query($koneksi, "SET @user_ip = '$user_ip'");

        // Generate kode kasus otomatis
        $year_part = date('Y');
        $month_part = date('m');
        $prefix = 'KS' . $year_part . $month_part;
        
        // Cari nomor urut terakhir untuk bulan ini
        $query_count = "SELECT COALESCE(MAX(CAST(SUBSTRING(kasus_kode, 8) AS UNSIGNED)), 0) + 1 as next_number 
                        FROM kasus_siswa 
                        WHERE kasus_kode LIKE '$prefix%'";
        $result_count = mysqli_query($koneksi, $query_count);
        $data_count = mysqli_fetch_assoc($result_count);
        $next_number = $data_count['next_number'];
        
        $kasus_kode = $prefix . str_pad($next_number, 4, '0', STR_PAD_LEFT);

        // Insert data kasus
        $query_kasus = "INSERT INTO kasus_siswa (kasus_kode, siswa_id, tanggal_pelaporan, sumber_kasus, kategori_masalah, 
                        judul_kasus, deskripsi_awal, status_kasus, guru_bk_id) 
                        VALUES ('$kasus_kode', '$siswa_id', '$tanggal_pelaporan', '$sumber_kasus', '$kategori_masalah', 
                        '$judul_kasus', '$deskripsi_awal', '$status_kasus', '$guru_bk_id')";
        
        $result_kasus = mysqli_query($koneksi, $query_kasus);
        
        if(!$result_kasus) {
            throw new Exception("Gagal menyimpan data kasus: " . mysqli_error($koneksi));
        }

        $kasus_id = mysqli_insert_id($koneksi);

        // Handle file upload jika ada
        $lampiran_file = '';
        if(isset($_FILES['lampiran']) && $_FILES['lampiran']['error'] == 0) {
            $upload_dir = '../uploads/kasus/';
            if(!is_dir($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }

            $file_name = $_FILES['lampiran']['name'];
            $file_tmp = $_FILES['lampiran']['tmp_name'];
            $file_size = $_FILES['lampiran']['size'];
            $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
            
            // Validasi file
            $allowed_ext = array('pdf', 'doc', 'docx', 'jpg', 'jpeg', 'png');
            if(!in_array($file_ext, $allowed_ext)) {
                throw new Exception("Format file tidak diizinkan. Gunakan: PDF, DOC, DOCX, JPG, PNG");
            }
            
            if($file_size > 5 * 1024 * 1024) { // 5MB
                throw new Exception("Ukuran file terlalu besar. Maksimal 5MB");
            }

            $new_file_name = 'kasus_' . $kasus_id . '_' . time() . '.' . $file_ext;
            $upload_path = $upload_dir . $new_file_name;
            
            if(move_uploaded_file($file_tmp, $upload_path)) {
                $lampiran_file = 'uploads/kasus/' . $new_file_name;
            } else {
                throw new Exception("Gagal mengupload file");
            }
        }

        // Insert jurnal jika ada data
        if(!empty($tanggal_konseling) && !empty($uraian_sesi)) {
            $query_jurnal = "INSERT INTO jurnal_kasus (kasus_id, tanggal_konseling, uraian_sesi, 
                            analisis_diagnosis, tindakan_intervensi, rencana_tindak_lanjut, lampiran_file) 
                            VALUES ('$kasus_id', '$tanggal_konseling', '$uraian_sesi', '$analisis_diagnosis', 
                            '$tindakan_intervensi', '$rencana_tindak_lanjut', '$lampiran_file')";
            
            $result_jurnal = mysqli_query($koneksi, $query_jurnal);
            
            if(!$result_jurnal) {
                throw new Exception("Gagal menyimpan jurnal: " . mysqli_error($koneksi));
            }

            $jurnal_id = mysqli_insert_id($koneksi);

            // Buat notifikasi RTL jika ada
            if(!empty($rencana_tindak_lanjut)) {
                $tanggal_reminder = date('Y-m-d', strtotime('+7 days')); // Default 7 hari
                $pesan_reminder = "RTL untuk kasus " . $judul_kasus . ": " . substr($rencana_tindak_lanjut, 0, 100) . "...";
                
                $query_notif = "INSERT INTO notifikasi_rtl (jurnal_id, tanggal_reminder, pesan_reminder) 
                               VALUES ('$jurnal_id', '$tanggal_reminder', '$pesan_reminder')";
                mysqli_query($koneksi, $query_notif);
            }
        }

        // Commit transaksi
        mysqli_commit($koneksi);
        
        header("location:kasus_siswa.php?alert=sukses&pesan=Kasus berhasil disimpan");
        exit();

    } catch(Exception $e) {
        // Rollback transaksi
        mysqli_rollback($koneksi);
        
        // Hapus file jika ada
        if(!empty($lampiran_file) && file_exists('../' . $lampiran_file)) {
            unlink('../' . $lampiran_file);
        }
        
        header("location:kasus_siswa_tambah.php?alert=gagal&pesan=" . urlencode($e->getMessage()));
        exit();
    }
} else {
    header("location:kasus_siswa.php?alert=gagal&pesan=Akses tidak valid");
    exit();
}
?>
