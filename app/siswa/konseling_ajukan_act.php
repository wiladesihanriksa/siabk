<?php
include '../koneksi.php';
session_start();

// Cek session untuk siswa
if(!isset($_SESSION['level']) || $_SESSION['level'] != "siswa"){
    header("location:../index.php?alert=belum_login");
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
    $guru_bk_id = mysqli_real_escape_string($koneksi, $_POST['guru_bk_id']);
    $status_kasus = 'Baru';

    // Konversi format tanggal dari dd/mm/yyyy ke yyyy-mm-dd
    $tanggal_pelaporan = '';
    if(!empty($tanggal_pelaporan_raw)) {
        $date_parts = explode('/', $tanggal_pelaporan_raw);
        if(count($date_parts) == 3) {
            $tanggal_pelaporan = $date_parts[2] . '-' . $date_parts[1] . '-' . $date_parts[0];
        } else {
            $tanggal_pelaporan = date('Y-m-d');
        }
    } else {
        $tanggal_pelaporan = date('Y-m-d');
    }

    // Validasi data
    if(empty($siswa_id) || empty($tanggal_pelaporan) || empty($sumber_kasus) || 
       empty($kategori_masalah) || empty($judul_kasus) || empty($guru_bk_id)) {
        header("location:konseling_ajukan.php?alert=error&msg=" . urlencode("Data tidak lengkap"));
        exit();
    }

    // Mulai transaksi
    mysqli_begin_transaction($koneksi);

    try {
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
            throw new Exception("Gagal menyimpan data konseling: " . mysqli_error($koneksi));
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
                
                // Update jurnal dengan lampiran jika ada
                // (Untuk saat ini, lampiran bisa disimpan di jurnal nanti)
            } else {
                throw new Exception("Gagal mengupload file");
            }
        }

        // Commit transaksi
        mysqli_commit($koneksi);
        
        header("location:konseling_saya.php?alert=success&msg=" . urlencode("Pengajuan konseling berhasil dikirim! Kode kasus: " . $kasus_kode));
        exit();
        
    } catch(Exception $e) {
        // Rollback transaksi
        mysqli_rollback($koneksi);
        
        header("location:konseling_ajukan.php?alert=error&msg=" . urlencode($e->getMessage()));
        exit();
    }
} else {
    header("location:konseling_ajukan.php?alert=error&msg=" . urlencode("Akses tidak valid"));
    exit();
}
?>

