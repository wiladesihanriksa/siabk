<?php
// Simple reset password endpoint
include '../koneksi.php';

// Set content type
header('Content-Type: text/html; charset=utf-8');

// Get POST data
$siswa_id = isset($_POST['siswa_id']) ? intval($_POST['siswa_id']) : 0;
$reset_all = isset($_POST['reset_all']) ? intval($_POST['reset_all']) : 0;

// Password to reset
$password_hash = md5('123456');

if($reset_all == 1) {
    // Reset all students (tanpa filter status)
    $query = "UPDATE siswa SET siswa_password = '$password_hash'";
    $result = mysqli_query($koneksi, $query);
    
    if($result) {
        $affected = mysqli_affected_rows($koneksi);
        echo json_encode([
            'success' => true,
            'message' => "Password berhasil direset untuk $affected siswa"
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Gagal reset password: ' . mysqli_error($koneksi)
        ]);
    }
} else {
    // Reset specific student (tanpa filter status)
    if($siswa_id > 0) {
        $query = "UPDATE siswa SET siswa_password = '$password_hash' WHERE siswa_id = '$siswa_id'";
        $result = mysqli_query($koneksi, $query);
        
        if($result) {
            $affected = mysqli_affected_rows($koneksi);
            if($affected > 0) {
                // Get student data
                $siswa_query = mysqli_query($koneksi, "SELECT siswa_nama, siswa_nis FROM siswa WHERE siswa_id = '$siswa_id'");
                $siswa_data = mysqli_fetch_assoc($siswa_query);
                
                echo json_encode([
                    'success' => true,
                    'message' => "Password berhasil direset untuk " . $siswa_data['siswa_nama'] . " (NIS: " . $siswa_data['siswa_nis'] . ")"
                ]);
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'Siswa tidak ditemukan'
                ]);
            }
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Gagal reset password: ' . mysqli_error($koneksi)
            ]);
        }
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'ID siswa tidak valid'
        ]);
    }
}

mysqli_close($koneksi);
?>
