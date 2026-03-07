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
    $kasus_id = mysqli_real_escape_string($koneksi, $_POST['kasus_id']);
    $tanggal_konseling = mysqli_real_escape_string($koneksi, $_POST['tanggal_konseling']);
    $uraian_sesi = mysqli_real_escape_string($koneksi, $_POST['uraian_sesi']);
    $analisis_diagnosis = mysqli_real_escape_string($koneksi, $_POST['analisis_diagnosis']);
    $tindakan_intervensi = mysqli_real_escape_string($koneksi, $_POST['tindakan_intervensi']);
    $rencana_tindak_lanjut = mysqli_real_escape_string($koneksi, $_POST['rencana_tindak_lanjut']);
    $bentuk_layanan = isset($_POST['bentuk_layanan']) ? mysqli_real_escape_string($koneksi, $_POST['bentuk_layanan']) : '';
    $buat_reminder = isset($_POST['buat_reminder']) ? 1 : 0;
    $tanggal_reminder = mysqli_real_escape_string($koneksi, $_POST['tanggal_reminder']);
    $pesan_reminder = mysqli_real_escape_string($koneksi, $_POST['pesan_reminder']);

    // Validasi data
    if(empty($kasus_id) || empty($tanggal_konseling) || empty($uraian_sesi)) {
        header("location:jurnal_tambah.php?kasus_id=$kasus_id&alert=gagal&pesan=Data tidak lengkap");
        exit();
    }

    // Validasi tanggal konseling tidak boleh lebih dari hari ini
    if($tanggal_konseling > date('Y-m-d')) {
        header("location:jurnal_tambah.php?kasus_id=$kasus_id&alert=gagal&pesan=Tanggal konseling tidak boleh lebih dari hari ini");
        exit();
    }

    // Mulai transaksi
    mysqli_begin_transaction($koneksi);

    try {
        // Handle file upload jika ada
        $lampiran_file = '';
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

            $new_file_name = 'jurnal_' . $kasus_id . '_' . time() . '.' . $file_ext;
            $upload_path = $upload_dir . $new_file_name;
            
            if(move_uploaded_file($file_tmp, $upload_path)) {
                $lampiran_file = 'uploads/kasus/' . $new_file_name;
            } else {
                throw new Exception("Gagal mengupload file");
            }
        }

        // Insert jurnal
        $query_jurnal = "INSERT INTO jurnal_kasus (kasus_id, tanggal_konseling, bentuk_layanan, uraian_sesi, 
                        analisis_diagnosis, tindakan_intervensi, rencana_tindak_lanjut, lampiran_file) 
                        VALUES ('$kasus_id', '$tanggal_konseling', '$bentuk_layanan', '$uraian_sesi', '$analisis_diagnosis', 
                        '$tindakan_intervensi', '$rencana_tindak_lanjut', '$lampiran_file')";
        
        $result_jurnal = mysqli_query($koneksi, $query_jurnal);
        
        if(!$result_jurnal) {
            throw new Exception("Gagal menyimpan jurnal: " . mysqli_error($koneksi));
        }

        $jurnal_id = mysqli_insert_id($koneksi);

        // Buat notifikasi RTL jika diminta
        if($buat_reminder && !empty($rencana_tindak_lanjut)) {
            if(empty($tanggal_reminder)) {
                $tanggal_reminder = date('Y-m-d', strtotime('+7 days')); // Default 7 hari
            }
            
            if(empty($pesan_reminder)) {
                $pesan_reminder = "RTL: " . substr($rencana_tindak_lanjut, 0, 100) . "...";
            }
            
            $query_notif = "INSERT INTO notifikasi_rtl (jurnal_id, tanggal_reminder, pesan_reminder) 
                           VALUES ('$jurnal_id', '$tanggal_reminder', '$pesan_reminder')";
            $result_notif = mysqli_query($koneksi, $query_notif);
            
            if(!$result_notif) {
                throw new Exception("Gagal membuat notifikasi RTL: " . mysqli_error($koneksi));
            }
        }

        // Update status kasus jika ada perkembangan
        if(!empty($tindakan_intervensi) || !empty($rencana_tindak_lanjut)) {
            $query_update_status = "UPDATE kasus_siswa SET status_kasus = 'Dalam Proses' WHERE kasus_id = '$kasus_id'";
            mysqli_query($koneksi, $query_update_status);
        }

        // Commit transaksi
        mysqli_commit($koneksi);
        
        header("location:kasus_siswa_detail.php?id=$kasus_id&alert=sukses&pesan=Jurnal berhasil disimpan");
        exit();

    } catch(Exception $e) {
        // Rollback transaksi
        mysqli_rollback($koneksi);
        
        // Hapus file jika ada
        if(!empty($lampiran_file) && file_exists('../' . $lampiran_file)) {
            unlink('../' . $lampiran_file);
        }
        
        header("location:jurnal_tambah.php?kasus_id=$kasus_id&alert=gagal&pesan=" . urlencode($e->getMessage()));
        exit();
    }
} else {
    header("location:kasus_siswa.php?alert=gagal&pesan=Akses tidak valid");
    exit();
}
?>
