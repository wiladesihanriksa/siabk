<?php
include '../koneksi.php';
include '../library/fpdf185/fpdf.php';
include '../functions_app_settings.php';

// Get app settings
$app_settings = getAppSettings($koneksi);

// Get pengaturan raport untuk kota dan waka kesiswaan
$query_pengaturan = "SELECT * FROM pengaturan_raport ORDER BY id DESC LIMIT 1";
$result_pengaturan = mysqli_query($koneksi, $query_pengaturan);

if(mysqli_num_rows($result_pengaturan) > 0) {
    $pengaturan = mysqli_fetch_assoc($result_pengaturan);
    $kota = $pengaturan['kota'];
    $waka_kesiswaan = $pengaturan['nama_waka'];
    $nip_waka = $pengaturan['nip_waka'];
} else {
    $kota = 'Gresik';
    $waka_kesiswaan = 'Waka Kesiswaan';
    $nip_waka = '-';
}

// Check if ID is provided
if(!isset($_GET['id']) || empty($_GET['id'])) {
    die('ID layanan tidak ditemukan');
}

$id = $_GET['id'];

// Get layanan data
$query = "SELECT l.*, k.kelas_nama, u.user_nama, u2.user_nama as dibuat_oleh_nama
          FROM layanan_bk l 
          LEFT JOIN kelas k ON l.kelas_id = k.kelas_id 
          LEFT JOIN user u ON l.created_by = u.user_id 
          LEFT JOIN user u2 ON l.dibuat_oleh = u2.user_id 
          WHERE l.layanan_id = ?";
$stmt = mysqli_prepare($koneksi, $query);
mysqli_stmt_bind_param($stmt, 'i', $id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if(mysqli_num_rows($result) == 0) {
    die('Data layanan tidak ditemukan');
}

$data = mysqli_fetch_assoc($result);

// Get peserta
$query_peserta = "SELECT lp.*, s.siswa_nama, k.kelas_nama 
                  FROM layanan_bk_peserta lp 
                  LEFT JOIN siswa s ON lp.siswa_id = s.siswa_id 
                  LEFT JOIN kelas_siswa ks ON s.siswa_id = ks.ks_siswa 
                  LEFT JOIN kelas k ON ks.ks_kelas = k.kelas_id 
                  WHERE lp.layanan_id = ? 
                  ORDER BY s.siswa_nama";
$stmt_peserta = mysqli_prepare($koneksi, $query_peserta);
mysqli_stmt_bind_param($stmt_peserta, 'i', $id);
mysqli_stmt_execute($stmt_peserta);
$result_peserta = mysqli_stmt_get_result($stmt_peserta);

// Create PDF
$pdf = new FPDF('P', 'mm', 'A4');
$pdf->AddPage();

// Header with border
$pdf->SetDrawColor(0, 0, 0);
$pdf->SetLineWidth(0.5);
$pdf->Rect(10, 10, 190, 25);

// Title
$pdf->SetFont('Arial', 'B', 14);
$pdf->Cell(0, 8, 'DETAIL LAYANAN BIMBINGAN DAN KONSELING', 0, 1, 'C');
$pdf->Cell(0, 5, $app_settings['app_name'], 0, 1, 'C');
$pdf->SetFont('Arial', '', 9);
$pdf->Cell(0, 4, $app_settings['app_address'], 0, 1, 'C');
$pdf->Ln(10);

// Informasi Layanan
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(0, 8, 'INFORMASI LAYANAN', 0, 1);
$pdf->SetFont('Arial', '', 10);

// Create table for service information
$pdf->SetFillColor(240, 240, 240);
$pdf->SetTextColor(0);

// Service details
$details = array(
    'ID Layanan' => $data['layanan_id'],
    'Tanggal Pelaksanaan' => date('d F Y', strtotime($data['tanggal_pelaksanaan'])),
    'Jenis Layanan' => $data['jenis_layanan'],
    'Topik/Materi' => $data['topik_materi'],
    'Bidang Layanan' => $data['bidang_layanan'],
    'Sasaran Layanan' => $data['sasaran_layanan'],
    'Kelas' => $data['kelas_nama'] ? $data['kelas_nama'] : '-',
    'Jumlah Peserta' => $data['jumlah_peserta'] . ' orang',
    'Dibuat Oleh' => $data['dibuat_oleh_nama'] ? $data['dibuat_oleh_nama'] : $data['user_nama'],
    'Tanggal Dibuat' => date('d F Y H:i', strtotime($data['created_at'])),
    'Lampiran' => $data['lampiran_foto'] ? 'Ada' : 'Tidak ada'
);

foreach($details as $label => $value) {
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->Cell(50, 7, $label . ':', 0, 0, 'L');
    $pdf->SetFont('Arial', '', 10);
    $pdf->Cell(0, 7, $value, 0, 1, 'L');
}

$pdf->Ln(5);

// Uraian Kegiatan
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(0, 8, 'URAIAN KEGIATAN', 0, 1);
$pdf->SetFont('Arial', '', 10);
$uraian_text = (trim($data['uraian_kegiatan']) !== '' && trim($data['uraian_kegiatan']) !== NULL) ? trim($data['uraian_kegiatan']) : 'Tidak ada uraian kegiatan yang dicatat.';
$pdf->MultiCell(0, 6, $uraian_text, 0, 'L');
$pdf->Ln(3);

// Evaluasi Proses
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(0, 8, 'EVALUASI PROSES (EVALA-PRO)', 0, 1);
$pdf->SetFont('Arial', '', 10);
$evaluasi_proses_text = (trim($data['evaluasi_proses']) !== '' && trim($data['evaluasi_proses']) !== NULL) ? trim($data['evaluasi_proses']) : 'Tidak ada evaluasi proses yang dicatat.';
$pdf->MultiCell(0, 6, $evaluasi_proses_text, 0, 'L');
$pdf->Ln(3);

// Evaluasi Hasil
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(0, 8, 'EVALUASI HASIL (EVALA-HASIL)', 0, 1);
$pdf->SetFont('Arial', '', 10);
$evaluasi_hasil_text = (trim($data['evaluasi_hasil']) !== '' && trim($data['evaluasi_hasil']) !== NULL) ? trim($data['evaluasi_hasil']) : 'Tidak ada evaluasi hasil yang dicatat.';
$pdf->MultiCell(0, 6, $evaluasi_hasil_text, 0, 'L');
$pdf->Ln(5);

// Daftar Peserta (if any)
if(mysqli_num_rows($result_peserta) > 0) {
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(0, 8, 'DAFTAR PESERTA LAYANAN', 0, 1);
    
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->SetFillColor(200, 200, 200);
    $pdf->Cell(10, 8, 'No', 1, 0, 'C', true);
    $pdf->Cell(80, 8, 'Nama Siswa', 1, 0, 'C', true);
    $pdf->Cell(40, 8, 'Kelas', 1, 0, 'C', true);
    $pdf->Cell(30, 8, 'Tanggal', 1, 1, 'C', true);
    
    $pdf->SetFont('Arial', '', 9);
    $no = 1;
    while($peserta = mysqli_fetch_assoc($result_peserta)) {
        $pdf->Cell(10, 6, $no, 1, 0, 'C');
        $pdf->Cell(80, 6, $peserta['siswa_nama'], 1, 0, 'L');
        $pdf->Cell(40, 6, $peserta['kelas_nama'] ? $peserta['kelas_nama'] : '-', 1, 0, 'C');
        $pdf->Cell(30, 6, date('d/m/Y', strtotime($peserta['created_at'])), 1, 1, 'C');
        $no++;
    }
}

// Tanda Tangan
$pdf->Ln(20);
$pdf->SetFont('Arial', '', 11);
$pdf->Cell(0, 6, $kota . ', ' . date('d F Y'), 0, 1, 'C');
$pdf->Ln(10);

// Tanda Tangan Waka Kesiswaan dan Guru BK
$pdf->Cell(95, 6, 'Waka Kesiswaan', 0, 0, 'C');
$pdf->Cell(95, 6, 'Guru BK', 0, 1, 'C');
$pdf->Ln(20);

$pdf->SetFont('Arial', 'B', 11);
$pdf->Cell(95, 6, $waka_kesiswaan, 0, 0, 'C');
$pdf->Cell(95, 6, $data['dibuat_oleh_nama'] ? $data['dibuat_oleh_nama'] : $data['user_nama'], 0, 1, 'C');

$pdf->SetFont('Arial', '', 10);
$pdf->Cell(95, 6, 'NIP. ' . $nip_waka, 0, 0, 'C');
$pdf->Cell(95, 6, 'Guru Bimbingan dan Konseling', 0, 1, 'C');

$pdf->Ln(10);

// Footer
$pdf->SetFont('Arial', 'I', 8);
$pdf->Cell(0, 5, 'Dokumen ini dicetak pada: ' . date('d F Y H:i:s'), 0, 1, 'L');

// Output PDF
$filename = 'Detail_Layanan_BK_' . $data['layanan_id'] . '_' . date('Y-m-d') . '.pdf';
$pdf->Output('I', $filename);
?>
