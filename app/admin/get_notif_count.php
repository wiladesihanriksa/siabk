<?php
// Endpoint untuk mendapatkan jumlah notifikasi terbaru via AJAX
include '../koneksi.php';
session_start();

// Cek session
if(!isset($_SESSION['level']) || ($_SESSION['level'] != "guru_bk" && $_SESSION['level'] != "administrator")){
    echo json_encode(['total' => 0, 'error' => 'Unauthorized']);
    exit();
}

// Set header untuk JSON
header('Content-Type: application/json');

$user_level = $_SESSION['level'];
$total_all = 0;

if($user_level == "administrator") {
    // Untuk administrator: hitung notifikasi RTL yang belum dibaca
    $notif_count_query = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM notifikasi_rtl WHERE status_reminder = 'Belum'");
    if($notif_count_query) {
        $notif_count = mysqli_fetch_assoc($notif_count_query);
        $total_all = (int)$notif_count['total'];
    }
} else if($user_level == "guru_bk") {
    // Untuk guru BK: hitung konseling baru dan feedback baru
    $user_id = $_SESSION['id'];
    $guru_bk_query = mysqli_query($koneksi, "SELECT guru_bk_id FROM guru_bk WHERE user_id = '$user_id'");
    $total_notif = 0;
    $total_feedback = 0;
    $guru_bk_id = null;

    if($guru_bk_query && mysqli_num_rows($guru_bk_query) > 0) {
        $guru_bk_data = mysqli_fetch_assoc($guru_bk_query);
        $guru_bk_id = $guru_bk_data['guru_bk_id'];
        
        // Hitung konseling baru
        $notif_count_query = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM kasus_siswa 
                                                     WHERE guru_bk_id = '$guru_bk_id' 
                                                     AND status_kasus = 'Baru' 
                                                     AND sumber_kasus = 'Inisiatif Siswa'");
        if($notif_count_query) {
            $notif_count = mysqli_fetch_assoc($notif_count_query);
            $total_notif = (int)$notif_count['total'];
        }
        
        // Hitung feedback baru (hanya yang belum dibaca)
        $feedback_count_query = mysqli_query($koneksi, "SELECT COUNT(*) as total 
                                                          FROM feedback_siswa f
                                                          JOIN jurnal_kasus j ON f.jurnal_id = j.jurnal_id
                                                          JOIN kasus_siswa k ON j.kasus_id = k.kasus_id
                                                          WHERE k.guru_bk_id = '$guru_bk_id'
                                                          AND (f.is_read = 0 OR f.is_read IS NULL)
                                                          AND f.created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)");
        if($feedback_count_query) {
            $feedback_count = mysqli_fetch_assoc($feedback_count_query);
            $total_feedback = (int)$feedback_count['total'];
        }
    }
    
    $total_all = $total_notif + $total_feedback;
}

echo json_encode([
    'total' => $total_all
]);
exit();
?>

