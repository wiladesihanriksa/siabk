<?php
// AJAX endpoint untuk reset password siswa
include '../koneksi.php';

// Set header untuk JSON response
header('Content-Type: application/json');

// Cek method POST
if($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Method tidak diizinkan']);
    exit();
}

// Ambil data dari POST
$siswa_id = isset($_POST['siswa_id']) ? intval($_POST['siswa_id']) : null;
$reset_all = isset($_POST['reset_all']) ? intval($_POST['reset_all']) : 0;

// Validasi input
if($reset_all == 0 && empty($siswa_id)) {
    echo json_encode(['success' => false, 'message' => 'ID siswa tidak valid']);
    exit();
}

// Password baru yang akan direset
$new_password = '123456';
$password_hash = md5($new_password);

// Cek koneksi database
if(!$koneksi) {
    echo json_encode(['success' => false, 'message' => 'Koneksi database gagal']);
    exit();
}

try {
    if($reset_all == 1) {
        // Reset password semua siswa aktif
        $query = "UPDATE siswa SET siswa_password = '$password_hash' WHERE siswa_status = 'aktif'";
        $result = mysqli_query($koneksi, $query);
        
        if(!$result) {
            throw new Exception('Update gagal: ' . mysqli_error($koneksi));
        }
        
        $affected_rows = mysqli_affected_rows($koneksi);
        
        echo json_encode([
            'success' => true, 
            'message' => "Password berhasil direset untuk $affected_rows siswa aktif",
            'affected_rows' => $affected_rows
        ]);
        
    } else {
        // Reset password siswa tertentu
        $query = "UPDATE siswa SET siswa_password = '$password_hash' WHERE siswa_id = '$siswa_id' AND siswa_status = 'aktif'";
        $result = mysqli_query($koneksi, $query);
        
        if(!$result) {
            throw new Exception('Update gagal: ' . mysqli_error($koneksi));
        }
        
        $affected_rows = mysqli_affected_rows($koneksi);
        
        if($affected_rows > 0) {
            // Ambil data siswa untuk response
            $siswa_query = mysqli_query($koneksi, "SELECT siswa_nama, siswa_nis FROM siswa WHERE siswa_id = '$siswa_id'");
            $siswa_data = mysqli_fetch_assoc($siswa_query);
            
            echo json_encode([
                'success' => true, 
                'message' => "Password berhasil direset untuk " . $siswa_data['siswa_nama'] . " (NIS: " . $siswa_data['siswa_nis'] . ")",
                'siswa_nama' => $siswa_data['siswa_nama'],
                'siswa_nis' => $siswa_data['siswa_nis']
            ]);
        } else {
            echo json_encode([
                'success' => false, 
                'message' => 'Siswa tidak ditemukan atau sudah tidak aktif'
            ]);
        }
    }
    
} catch(Exception $e) {
    // Log error untuk debugging
    error_log("Reset password error: " . $e->getMessage());
    
    echo json_encode([
        'success' => false, 
        'message' => 'Terjadi kesalahan: ' . $e->getMessage()
    ]);
}

// Tutup koneksi
mysqli_close($koneksi);
?>
