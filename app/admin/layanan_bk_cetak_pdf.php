<?php
include '../koneksi.php';
include '../library/fpdf185/fpdf.php';

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

// Get statistics
$stats_query = "SELECT 
                  COUNT(*) as total_layanan,
                  SUM(jumlah_peserta) as total_peserta,
                  COUNT(DISTINCT jenis_layanan) as jenis_layanan_berbeda,
                  COUNT(DISTINCT bidang_layanan) as bidang_layanan_berbeda
                FROM layanan_bk l 
                $where_clause";

if(!empty($params)) {
    $stats_stmt = mysqli_prepare($koneksi, $stats_query);
    if($stats_stmt) {
        $types = str_repeat('s', count($params));
        mysqli_stmt_bind_param($stats_stmt, $types, ...$params);
        mysqli_stmt_execute($stats_stmt);
        $stats_result = mysqli_stmt_get_result($stats_stmt);
    }
} else {
    $stats_result = mysqli_query($koneksi, $stats_query);
}
$stats = mysqli_fetch_assoc($stats_result);

// Create PDF
$pdf = new FPDF('L', 'mm', 'A4');
$pdf->AddPage();
$pdf->SetFont('Arial', 'B', 16);

// Header
$pdf->Cell(0, 10, 'LAPORAN LAYANAN BK', 0, 1, 'C');
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(0, 5, 'Tanggal Cetak: ' . date('d/m/Y H:i:s'), 0, 1, 'C');

// Filter info
$pdf->Ln(5);
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(0, 8, 'Filter yang Digunakan:', 0, 1, 'L');

$pdf->SetFont('Arial', '', 10);
if(isset($_GET['tanggal_awal']) && !empty($_GET['tanggal_awal'])) {
    $pdf->Cell(0, 5, 'Tanggal Awal: ' . date('d/m/Y', strtotime($_GET['tanggal_awal'])), 0, 1, 'L');
}
if(isset($_GET['tanggal_akhir']) && !empty($_GET['tanggal_akhir'])) {
    $pdf->Cell(0, 5, 'Tanggal Akhir: ' . date('d/m/Y', strtotime($_GET['tanggal_akhir'])), 0, 1, 'L');
}
if(isset($_GET['jenis_layanan']) && !empty($_GET['jenis_layanan'])) {
    $pdf->Cell(0, 5, 'Jenis Layanan: ' . $_GET['jenis_layanan'], 0, 1, 'L');
}
if(isset($_GET['bidang_layanan']) && !empty($_GET['bidang_layanan'])) {
    $pdf->Cell(0, 5, 'Bidang Layanan: ' . $_GET['bidang_layanan'], 0, 1, 'L');
}

// Statistics
$pdf->Ln(5);
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(0, 8, 'Ringkasan:', 0, 1, 'L');

$pdf->SetFont('Arial', '', 10);
$pdf->Cell(0, 5, 'Total Layanan: ' . $stats['total_layanan'], 0, 1, 'L');
$pdf->Cell(0, 5, 'Total Peserta: ' . ($stats['total_peserta'] ? $stats['total_peserta'] : 0), 0, 1, 'L');
$pdf->Cell(0, 5, 'Jenis Layanan Berbeda: ' . $stats['jenis_layanan_berbeda'], 0, 1, 'L');
$pdf->Cell(0, 5, 'Bidang Layanan Berbeda: ' . $stats['bidang_layanan_berbeda'], 0, 1, 'L');

// Data table
$pdf->Ln(10);
$pdf->SetFont('Arial', 'B', 10);

// Table header
$pdf->Cell(10, 8, 'No', 1, 0, 'C');
$pdf->Cell(25, 8, 'Tanggal', 1, 0, 'C');
$pdf->Cell(35, 8, 'Jenis', 1, 0, 'C');
$pdf->Cell(45, 8, 'Topik/Materi', 1, 0, 'C');
$pdf->Cell(20, 8, 'Bidang', 1, 0, 'C');
$pdf->Cell(25, 8, 'Sasaran', 1, 0, 'C');
$pdf->Cell(20, 8, 'Kelas', 1, 0, 'C');
$pdf->Cell(20, 8, 'Peserta', 1, 0, 'C');
$pdf->Cell(25, 8, 'Dibuat Oleh', 1, 1, 'C');

$pdf->SetFont('Arial', '', 8);

$no = 1;
while($data = mysqli_fetch_assoc($result)) {
    // Check if we need a new page
    if($pdf->GetY() > 180) {
        $pdf->AddPage();
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Cell(10, 8, 'No', 1, 0, 'C');
        $pdf->Cell(25, 8, 'Tanggal', 1, 0, 'C');
        $pdf->Cell(35, 8, 'Jenis', 1, 0, 'C');
        $pdf->Cell(45, 8, 'Topik/Materi', 1, 0, 'C');
        $pdf->Cell(20, 8, 'Bidang', 1, 0, 'C');
        $pdf->Cell(25, 8, 'Sasaran', 1, 0, 'C');
        $pdf->Cell(20, 8, 'Kelas', 1, 0, 'C');
        $pdf->Cell(20, 8, 'Peserta', 1, 0, 'C');
        $pdf->Cell(25, 8, 'Dibuat Oleh', 1, 1, 'C');
        $pdf->SetFont('Arial', '', 8);
    }
    
    $pdf->Cell(10, 6, $no++, 1, 0, 'C');
    $pdf->Cell(25, 6, date('d/m/Y', strtotime($data['tanggal_pelaksanaan'])), 1, 0, 'C');
    $pdf->Cell(35, 6, substr($data['jenis_layanan'], 0, 15), 1, 0, 'L');
    $pdf->Cell(45, 6, substr($data['topik_materi'], 0, 20), 1, 0, 'L');
    $pdf->Cell(20, 6, $data['bidang_layanan'], 1, 0, 'C');
    $pdf->Cell(25, 6, substr($data['sasaran_layanan'], 0, 10), 1, 0, 'L');
    $pdf->Cell(20, 6, $data['kelas_nama'] ? substr($data['kelas_nama'], 0, 8) : '-', 1, 0, 'C');
    $pdf->Cell(20, 6, $data['jumlah_peserta'], 1, 0, 'C');
    $pdf->Cell(25, 6, substr($data['user_nama'], 0, 10), 1, 1, 'L');
}

$pdf->Output('I', 'Laporan_Layanan_BK_' . date('Y-m-d') . '.pdf');
?>
