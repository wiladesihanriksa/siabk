<?php
include '../koneksi.php';
session_start();

if(isset($_POST['update'])) {
    $layanan_id = $_POST['layanan_id'];
    $tanggal_pelaksanaan = $_POST['tanggal_pelaksanaan'];
    $jenis_layanan = $_POST['jenis_layanan'];
    $topik_materi = $_POST['topik_materi'];
    $bidang_layanan = $_POST['bidang_layanan'];
    $sasaran_layanan = $_POST['sasaran_layanan'];
    $kelas_id = isset($_POST['kelas_id']) && !empty($_POST['kelas_id']) ? $_POST['kelas_id'] : NULL;
    $jumlah_peserta = $_POST['jumlah_peserta'];
    $uraian_kegiatan = $_POST['uraian_kegiatan'];
    $evaluasi_proses = $_POST['evaluasi_proses'];
    $evaluasi_hasil = $_POST['evaluasi_hasil'];
    $dibuat_oleh = intval($_POST['dibuat_oleh']); // Pastikan integer
    
    // Validasi dibuat_oleh adalah user yang valid
    $check_user = mysqli_query($koneksi, "SELECT user_id FROM user WHERE user_id = '$dibuat_oleh'");
    if(mysqli_num_rows($check_user) == 0) {
        $dibuat_oleh = $_SESSION['id']; // Gunakan session user jika tidak valid
    }
    $status_layanan = isset($_POST['status_layanan']) ? trim($_POST['status_layanan']) : 'Aktif';
    
    // Validasi status_layanan sesuai dengan enum di database
    $allowed_status = array('Aktif', 'Selesai', 'Dibatalkan');
    if (!in_array($status_layanan, $allowed_status)) {
        $status_layanan = 'Aktif'; // Default value jika tidak valid
    }
    
    // Pastikan nilai sesuai dengan enum database
    switch($status_layanan) {
        case 'Aktif':
            $status_layanan = 'Aktif';
            break;
        case 'Selesai':
            $status_layanan = 'Selesai';
            break;
        case 'Dibatalkan':
            $status_layanan = 'Dibatalkan';
            break;
        default:
            $status_layanan = 'Aktif';
            break;
    }
    
    // Debug: Tampilkan nilai status_layanan
    error_log("Status layanan yang akan disimpan: '" . $status_layanan . "'");
    error_log("Length: " . strlen($status_layanan));
    error_log("Hex: " . bin2hex($status_layanan));
    
    // Force valid enum value
    if($status_layanan === 'Aktif') {
        $status_layanan = 'Aktif';
    } elseif($status_layanan === 'Selesai') {
        $status_layanan = 'Selesai';
    } elseif($status_layanan === 'Dibatalkan') {
        $status_layanan = 'Dibatalkan';
    } else {
        $status_layanan = 'Aktif';
    }

    // Handle file upload
    $lampiran_foto = NULL;
    if(isset($_FILES['lampiran']) && $_FILES['lampiran']['error'] == 0) {
        $upload_dir = '../uploads/layanan/';
        if(!is_dir($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }
        
        $file_extension = pathinfo($_FILES['lampiran']['name'], PATHINFO_EXTENSION);
        $allowed_extensions = array('jpg', 'jpeg', 'png', 'gif');
        
        if(in_array(strtolower($file_extension), $allowed_extensions)) {
            $new_filename = 'layanan_' . time() . '_' . uniqid() . '.' . $file_extension;
            $upload_path = $upload_dir . $new_filename;
            
            if(move_uploaded_file($_FILES['lampiran']['tmp_name'], $upload_path)) {
                // Delete old file if exists
                $old_file_query = "SELECT lampiran_foto FROM layanan_bk WHERE layanan_id = ?";
                $old_stmt = mysqli_prepare($koneksi, $old_file_query);
                mysqli_stmt_bind_param($old_stmt, 'i', $layanan_id);
                mysqli_stmt_execute($old_stmt);
                $old_result = mysqli_stmt_get_result($old_stmt);
                $old_data = mysqli_fetch_assoc($old_result);
                
                if($old_data['lampiran_foto'] && file_exists('../' . $old_data['lampiran_foto'])) {
                    unlink('../' . $old_data['lampiran_foto']);
                }
                
                $lampiran_foto = 'uploads/layanan/' . $new_filename;
            }
        }
    }

    // Update data layanan BK dengan query langsung
    $tanggal_pelaksanaan = mysqli_real_escape_string($koneksi, $tanggal_pelaksanaan);
    $jenis_layanan = mysqli_real_escape_string($koneksi, $jenis_layanan);
    $topik_materi = mysqli_real_escape_string($koneksi, $topik_materi);
    $bidang_layanan = mysqli_real_escape_string($koneksi, $bidang_layanan);
    $sasaran_layanan = mysqli_real_escape_string($koneksi, $sasaran_layanan);
    $uraian_kegiatan = mysqli_real_escape_string($koneksi, $uraian_kegiatan);
    $evaluasi_proses = mysqli_real_escape_string($koneksi, $evaluasi_proses);
    $evaluasi_hasil = mysqli_real_escape_string($koneksi, $evaluasi_hasil);
    
    if($lampiran_foto) {
        $lampiran_foto = mysqli_real_escape_string($koneksi, $lampiran_foto);
        $query_layanan = "UPDATE layanan_bk SET 
            tanggal_pelaksanaan='$tanggal_pelaksanaan', 
            jenis_layanan='$jenis_layanan', 
            topik_materi='$topik_materi', 
            bidang_layanan='$bidang_layanan', 
            sasaran_layanan='$sasaran_layanan', 
            kelas_id=" . ($kelas_id ? $kelas_id : 'NULL') . ", 
            jumlah_peserta=$jumlah_peserta, 
            uraian_kegiatan='$uraian_kegiatan', 
            evaluasi_proses='$evaluasi_proses', 
            evaluasi_hasil='$evaluasi_hasil', 
            dibuat_oleh=$dibuat_oleh, 
            status_layanan='$status_layanan', 
            lampiran_foto='$lampiran_foto' 
            WHERE layanan_id=$layanan_id";
    } else {
        $query_layanan = "UPDATE layanan_bk SET 
            tanggal_pelaksanaan='$tanggal_pelaksanaan', 
            jenis_layanan='$jenis_layanan', 
            topik_materi='$topik_materi', 
            bidang_layanan='$bidang_layanan', 
            sasaran_layanan='$sasaran_layanan', 
            kelas_id=" . ($kelas_id ? $kelas_id : 'NULL') . ", 
            jumlah_peserta=$jumlah_peserta, 
            uraian_kegiatan='$uraian_kegiatan', 
            evaluasi_proses='$evaluasi_proses', 
            evaluasi_hasil='$evaluasi_hasil', 
            dibuat_oleh=$dibuat_oleh, 
            status_layanan='$status_layanan' 
            WHERE layanan_id=$layanan_id";
    }
    
    // Debug: Tampilkan query
    error_log("Query: " . $query_layanan);
    
    if(mysqli_query($koneksi, $query_layanan)) {
        // Delete existing peserta
        $delete_peserta = "DELETE FROM layanan_bk_peserta WHERE layanan_id = ?";
        $stmt_delete = mysqli_prepare($koneksi, $delete_peserta);
        mysqli_stmt_bind_param($stmt_delete, 'i', $layanan_id);
        mysqli_stmt_execute($stmt_delete);
        
        // Insert new peserta if ada
        if(isset($_POST['siswa_ids']) && !empty($_POST['siswa_ids'])) {
            $siswa_ids = $_POST['siswa_ids'];
            
            foreach($siswa_ids as $siswa_id) {
                $query_peserta = "INSERT INTO layanan_bk_peserta (layanan_id, siswa_id) VALUES (?, ?)";
                $stmt_peserta = mysqli_prepare($koneksi, $query_peserta);
                mysqli_stmt_bind_param($stmt_peserta, 'ii', $layanan_id, $siswa_id);
                mysqli_stmt_execute($stmt_peserta);
            }
        }
        
        echo "<script>alert('Data layanan BK berhasil diupdate!'); window.location='layanan_bk_detail.php?id=".$layanan_id."';</script>";
    } else {
        echo "<script>alert('Gagal mengupdate data layanan BK!'); window.location='layanan_bk_edit.php?id=".$layanan_id."';</script>";
    }
    
    mysqli_stmt_close($stmt_layanan);
} else {
    header("location:layanan_bk.php");
}
?>
