<?php
include '../koneksi.php';
session_start();

// Cek session untuk administrator dan guru BK
if(!isset($_SESSION['level']) || ($_SESSION['level'] != "administrator" && $_SESSION['level'] != "guru_bk")){
    header("location:../admin.php?alert=belum_login");
    exit();
}

if(isset($_POST['update'])) {
    // Ambil data dari form
    $jurnal_id = mysqli_real_escape_string($koneksi, $_POST['jurnal_id']);
    $kasus_id = mysqli_real_escape_string($koneksi, $_POST['kasus_id']);
    $tanggal_konseling = mysqli_real_escape_string($koneksi, $_POST['tanggal_konseling']);
    $uraian_sesi = mysqli_real_escape_string($koneksi, $_POST['uraian_sesi']);
    $analisis_diagnosis = mysqli_real_escape_string($koneksi, $_POST['analisis_diagnosis']);
    $tindakan_intervensi = mysqli_real_escape_string($koneksi, $_POST['tindakan_intervensi']);
    $rencana_tindak_lanjut = mysqli_real_escape_string($koneksi, $_POST['rencana_tindak_lanjut']);
    $bentuk_layanan = isset($_POST['bentuk_layanan']) ? mysqli_real_escape_string($koneksi, $_POST['bentuk_layanan']) : '';

    // Validasi data
    if(empty($jurnal_id) || empty($kasus_id) || empty($tanggal_konseling) || empty($uraian_sesi)) {
        header("location:jurnal_edit.php?id=$jurnal_id&alert=gagal&pesan=Data tidak lengkap");
        exit();
    }

    // Validasi tanggal konseling tidak boleh lebih dari hari ini
    if($tanggal_konseling > date('Y-m-d')) {
        header("location:jurnal_edit.php?id=$jurnal_id&alert=gagal&pesan=Tanggal konseling tidak boleh lebih dari hari ini");
        exit();
    }

    // Cek apakah jurnal ada
    $query_cek = "SELECT * FROM jurnal_kasus WHERE jurnal_id = '$jurnal_id'";
    $result_cek = mysqli_query($koneksi, $query_cek);
    
    if(mysqli_num_rows($result_cek) == 0) {
        header("location:kasus_siswa.php?alert=gagal&pesan=Jurnal tidak ditemukan");
        exit();
    }

    $jurnal_lama = mysqli_fetch_assoc($result_cek);

    // Mulai transaksi
    mysqli_begin_transaction($koneksi);

    try {
        // Handle file upload jika ada
        $lampiran_file = $jurnal_lama['lampiran_file']; // Keep old file by default
        
        if(isset($_FILES['lampiran_file']) && $_FILES['lampiran_file']['error'] == 0) {
            $upload_dir = '../uploads/kasus/';
            if(!is_dir($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }

            $file_name = $_FILES['lampiran_file']['name'];
            $file_tmp = $_FILES['lampiran_file']['tmp_name'];
            $file_size = $_FILES['lampiran_file']['size'];
            $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
            
            // Validasi file
            $allowed_ext = array('pdf', 'doc', 'docx', 'jpg', 'jpeg', 'png');
            if(!in_array($file_ext, $allowed_ext)) {
                throw new Exception("Format file tidak diizinkan. Gunakan: PDF, DOC, DOCX, JPG, PNG");
            }
            
            if($file_size > 5 * 1024 * 1024) { // 5MB
                throw new Exception("Ukuran file terlalu besar. Maksimal 5MB");
            }

            // Hapus file lama jika ada
            if(!empty($jurnal_lama['lampiran_file']) && file_exists('../' . $jurnal_lama['lampiran_file'])) {
                unlink('../' . $jurnal_lama['lampiran_file']);
            }

            $new_file_name = 'jurnal_' . $kasus_id . '_' . time() . '.' . $file_ext;
            $upload_path = $upload_dir . $new_file_name;
            
            if(move_uploaded_file($file_tmp, $upload_path)) {
                $lampiran_file = 'uploads/kasus/' . $new_file_name;
            } else {
                throw new Exception("Gagal mengupload file");
            }
        }

        // Update jurnal
        $query_update = "UPDATE jurnal_kasus SET 
                        tanggal_konseling = '$tanggal_konseling',
                        bentuk_layanan = '$bentuk_layanan',
                        uraian_sesi = '$uraian_sesi',
                        analisis_diagnosis = '$analisis_diagnosis',
                        tindakan_intervensi = '$tindakan_intervensi',
                        rencana_tindak_lanjut = '$rencana_tindak_lanjut',
                        lampiran_file = '$lampiran_file',
                        updated_at = NOW()
                        WHERE jurnal_id = '$jurnal_id'";
        
        $result_update = mysqli_query($koneksi, $query_update);
        
        if(!$result_update) {
            throw new Exception("Gagal mengupdate jurnal: " . mysqli_error($koneksi));
        }

        // Update status kasus jika ada perkembangan
        if(!empty($tindakan_intervensi) || !empty($rencana_tindak_lanjut)) {
            $query_update_status = "UPDATE kasus_siswa SET status_kasus = 'Dalam Proses' WHERE kasus_id = '$kasus_id'";
            mysqli_query($koneksi, $query_update_status);
        }

        // Commit transaksi
        mysqli_commit($koneksi);
        
        // Redirect berdasarkan level user
        if($_SESSION['level'] == "guru_bk") {
            header("location:jurnal.php?alert=sukses&pesan=Jurnal berhasil diupdate");
        } else {
            header("location:kasus_siswa_detail.php?id=$kasus_id&alert=sukses&pesan=Jurnal berhasil diupdate");
        }
        exit();

    } catch(Exception $e) {
        // Rollback transaksi
        mysqli_rollback($koneksi);
        
        // Hapus file baru jika ada error
        if(isset($new_file_name) && file_exists($upload_dir . $new_file_name)) {
            unlink($upload_dir . $new_file_name);
        }
        
        header("location:jurnal_edit.php?id=$jurnal_id&alert=gagal&pesan=" . urlencode($e->getMessage()));
        exit();
    }
} else {
    header("location:kasus_siswa.php?alert=gagal&pesan=Akses tidak valid");
    exit();
}
?>
