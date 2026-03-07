<?php
include '../koneksi.php';
session_start();

if($_POST) {
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
    $dibuat_oleh = $_POST['dibuat_oleh'];
    $status_layanan = isset($_POST['status_layanan']) ? $_POST['status_layanan'] : 'Aktif';
    
    // Validasi status_layanan sesuai dengan enum di database
    $allowed_status = array('Aktif', 'Selesai', 'Dibatalkan');
    if (!in_array($status_layanan, $allowed_status)) {
        $status_layanan = 'Aktif'; // Default value jika tidak valid
    }
    
    $created_by = $_SESSION['id'];

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
                $lampiran_foto = 'uploads/layanan/' . $new_filename;
            }
        }
    }

    // Insert data layanan BK dengan query langsung
    $tanggal_pelaksanaan = mysqli_real_escape_string($koneksi, $tanggal_pelaksanaan);
    $jenis_layanan = mysqli_real_escape_string($koneksi, $jenis_layanan);
    $topik_materi = mysqli_real_escape_string($koneksi, $topik_materi);
    $bidang_layanan = mysqli_real_escape_string($koneksi, $bidang_layanan);
    $sasaran_layanan = mysqli_real_escape_string($koneksi, $sasaran_layanan);
    $uraian_kegiatan = mysqli_real_escape_string($koneksi, $uraian_kegiatan);
    $evaluasi_proses = mysqli_real_escape_string($koneksi, $evaluasi_proses);
    $evaluasi_hasil = mysqli_real_escape_string($koneksi, $evaluasi_hasil);
    $status_layanan = mysqli_real_escape_string($koneksi, $status_layanan);
    $dibuat_oleh = intval($dibuat_oleh);
    $created_by = intval($created_by);
    
    if($lampiran_foto) {
        $lampiran_foto = mysqli_real_escape_string($koneksi, $lampiran_foto);
        $query_layanan = "INSERT INTO layanan_bk (tanggal_pelaksanaan, jenis_layanan, topik_materi, bidang_layanan, sasaran_layanan, kelas_id, jumlah_peserta, uraian_kegiatan, evaluasi_proses, evaluasi_hasil, dibuat_oleh, status_layanan, lampiran_foto, created_by) 
                          VALUES ('$tanggal_pelaksanaan', '$jenis_layanan', '$topik_materi', '$bidang_layanan', '$sasaran_layanan', " . ($kelas_id ? $kelas_id : 'NULL') . ", $jumlah_peserta, '$uraian_kegiatan', '$evaluasi_proses', '$evaluasi_hasil', $dibuat_oleh, '$status_layanan', '$lampiran_foto', $created_by)";
    } else {
        $query_layanan = "INSERT INTO layanan_bk (tanggal_pelaksanaan, jenis_layanan, topik_materi, bidang_layanan, sasaran_layanan, kelas_id, jumlah_peserta, uraian_kegiatan, evaluasi_proses, evaluasi_hasil, dibuat_oleh, status_layanan, created_by) 
                          VALUES ('$tanggal_pelaksanaan', '$jenis_layanan', '$topik_materi', '$bidang_layanan', '$sasaran_layanan', " . ($kelas_id ? $kelas_id : 'NULL') . ", $jumlah_peserta, '$uraian_kegiatan', '$evaluasi_proses', '$evaluasi_hasil', $dibuat_oleh, '$status_layanan', $created_by)";
    }
    
    // Debug: Tampilkan query
    error_log("Query: " . $query_layanan);
    
    if(mysqli_query($koneksi, $query_layanan)) {
        $layanan_id = mysqli_insert_id($koneksi);
        
        // Insert data peserta jika ada
        if(isset($_POST['siswa_ids']) && !empty($_POST['siswa_ids'])) {
            $siswa_ids = $_POST['siswa_ids'];
            
            foreach($siswa_ids as $siswa_id) {
                if(!empty($siswa_id)) { // Pastikan siswa_id tidak kosong
                    $query_peserta = "INSERT INTO layanan_bk_peserta (layanan_id, siswa_id) VALUES (?, ?)";
                    $stmt_peserta = mysqli_prepare($koneksi, $query_peserta);
                    mysqli_stmt_bind_param($stmt_peserta, 'ii', $layanan_id, $siswa_id);
                    mysqli_stmt_execute($stmt_peserta);
                    mysqli_stmt_close($stmt_peserta);
                }
            }
        }
        
        echo "<script>alert('Data layanan BK berhasil disimpan!'); window.location='layanan_bk.php';</script>";
    } else {
        $error = mysqli_error($koneksi);
        echo "<script>alert('Gagal menyimpan data layanan BK! Error: " . $error . "'); window.location='layanan_bk_tambah.php';</script>";
    }
} else {
    header("location:layanan_bk.php");
}
?>
