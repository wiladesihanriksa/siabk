<?php
include '../koneksi.php';

// Cek session admin
session_start();
if(!isset($_SESSION['level']) || $_SESSION['level'] != "administrator"){
    header("location:../admin.php?alert=belum_login");
    exit();
}

// Build query dengan filter
$where_conditions = array();
$params = array();

if(isset($_GET['tanggal_awal']) && !empty($_GET['tanggal_awal'])) {
    $where_conditions[] = "l.tanggal_pelaksanaan >= ?";
    $params[] = $_GET['tanggal_awal'];
}

if(isset($_GET['tanggal_akhir']) && !empty($_GET['tanggal_akhir'])) {
    $where_conditions[] = "l.tanggal_pelaksanaan <= ?";
    $params[] = $_GET['tanggal_akhir'];
}

if(isset($_GET['jenis_layanan']) && !empty($_GET['jenis_layanan'])) {
    $where_conditions[] = "l.jenis_layanan = ?";
    $params[] = $_GET['jenis_layanan'];
}

if(isset($_GET['bidang_layanan']) && !empty($_GET['bidang_layanan'])) {
    $where_conditions[] = "l.bidang_layanan = ?";
    $params[] = $_GET['bidang_layanan'];
}

if(isset($_GET['kelas_id']) && !empty($_GET['kelas_id'])) {
    $where_conditions[] = "l.kelas_id = ?";
    $params[] = $_GET['kelas_id'];
}

$where_clause = !empty($where_conditions) ? "WHERE " . implode(" AND ", $where_conditions) : "";

// Get data
$query = "SELECT l.*, k.kelas_nama, u.user_nama 
          FROM layanan_bk l 
          LEFT JOIN kelas k ON l.kelas_id = k.kelas_id 
          LEFT JOIN user u ON l.created_by = u.user_id 
          $where_clause 
          ORDER BY l.tanggal_pelaksanaan DESC";

if(!empty($params)) {
    $stmt = mysqli_prepare($koneksi, $query);
    if($stmt) {
        $types = str_repeat('s', count($params));
        mysqli_stmt_bind_param($stmt, $types, ...$params);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
    }
} else {
    $result = mysqli_query($koneksi, $query);
}

// Set headers for Excel download
$filename = 'Laporan_Layanan_BK_' . date('Y-m-d_H-i-s') . '.xls';
header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment; filename="' . $filename . '"');
header('Cache-Control: max-age=0');

// Start Excel content
echo '<table border="1">';
echo '<tr>';
echo '<th>No</th>';
echo '<th>Tanggal Pelaksanaan</th>';
echo '<th>Jenis Layanan</th>';
echo '<th>Topik/Materi</th>';
echo '<th>Bidang Layanan</th>';
echo '<th>Sasaran Layanan</th>';
echo '<th>Kelas</th>';
echo '<th>Jumlah Peserta</th>';
echo '<th>Dibuat Oleh</th>';
echo '<th>Uraian Kegiatan</th>';
echo '<th>Evaluasi Proses</th>';
echo '<th>Evaluasi Hasil</th>';
echo '</tr>';

$no = 1;
while($data = mysqli_fetch_assoc($result)) {
    echo '<tr>';
    echo '<td>' . $no++ . '</td>';
    echo '<td>' . date('d/m/Y', strtotime($data['tanggal_pelaksanaan'])) . '</td>';
    echo '<td>' . $data['jenis_layanan'] . '</td>';
    echo '<td>' . $data['topik_materi'] . '</td>';
    echo '<td>' . $data['bidang_layanan'] . '</td>';
    echo '<td>' . $data['sasaran_layanan'] . '</td>';
    echo '<td>' . ($data['kelas_nama'] ? $data['kelas_nama'] : '-') . '</td>';
    echo '<td>' . $data['jumlah_peserta'] . '</td>';
    echo '<td>' . $data['user_nama'] . '</td>';
    echo '<td>' . str_replace(array("\r\n", "\r", "\n"), " ", $data['uraian_kegiatan']) . '</td>';
    echo '<td>' . str_replace(array("\r\n", "\r", "\n"), " ", $data['evaluasi_proses']) . '</td>';
    echo '<td>' . str_replace(array("\r\n", "\r", "\n"), " ", $data['evaluasi_hasil']) . '</td>';
    echo '</tr>';
}

echo '</table>';
?>
