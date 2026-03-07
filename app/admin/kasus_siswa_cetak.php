<?php 
include '../koneksi.php';
include '../functions_app_settings.php';

// Ambil ID kasus dari URL
$kasus_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if($kasus_id == 0) {
    header("location:kasus_siswa.php?alert=gagal&pesan=ID kasus tidak valid");
    exit();
}

// Ambil data kasus
$query_kasus = "SELECT k.*, s.siswa_nama, s.siswa_nis, j.jurusan_nama, u.user_nama as guru_bk_nama 
                FROM kasus_siswa k 
                LEFT JOIN siswa s ON k.siswa_id = s.siswa_id 
                LEFT JOIN jurusan j ON s.siswa_jurusan = j.jurusan_id
                LEFT JOIN user u ON k.guru_bk_id = u.user_id 
                WHERE k.kasus_id = '$kasus_id'";
$result_kasus = mysqli_query($koneksi, $query_kasus);

if(mysqli_num_rows($result_kasus) == 0) {
    header("location:kasus_siswa.php?alert=gagal&pesan=Kasus tidak ditemukan");
    exit();
}

$kasus = mysqli_fetch_assoc($result_kasus);

// Ambil data jurnal perkembangan
$query_jurnal = "SELECT * FROM jurnal_kasus WHERE kasus_id = '$kasus_id' ORDER BY tanggal_konseling ASC";
$result_jurnal = mysqli_query($koneksi, $query_jurnal);

// Ambil pengaturan raport dari database
$query_pengaturan = "SELECT * FROM pengaturan_raport ORDER BY id DESC LIMIT 1";
$result_pengaturan = mysqli_query($koneksi, $query_pengaturan);

if(mysqli_num_rows($result_pengaturan) > 0) {
    $pengaturan = mysqli_fetch_assoc($result_pengaturan);
    $waka_kesiswaan = $pengaturan['nama_waka'];
    $kota = $pengaturan['kota'];
} else {
    // Default values jika belum ada pengaturan
    $waka_kesiswaan = 'Waka Kesiswaan';
    $kota = 'Kota';
}

// Include FPDF
require_once('../library/fpdf185/fpdf.php');

// Buat PDF
$pdf = new FPDF('P', 'mm', 'A4');
$pdf->AddPage();
$pdf->SetFont('Arial', 'B', 16);

// Header
$pdf->Cell(0, 10, 'LAPORAN KASUS SISWA', 0, 1, 'C');
$pdf->Cell(0, 10, 'Konseling Bimbingan dan Konseling', 0, 1, 'C');
$pdf->Ln(10);

// Informasi Kasus
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(0, 8, 'INFORMASI KASUS', 0, 1, 'L');
$pdf->SetFont('Arial', '', 10);

$pdf->Cell(40, 6, 'Kode Kasus:', 0, 0, 'L');
$pdf->Cell(0, 6, $kasus['kasus_kode'], 0, 1, 'L');

$pdf->Cell(40, 6, 'Nama Siswa:', 0, 0, 'L');
$pdf->Cell(0, 6, $kasus['siswa_nama'], 0, 1, 'L');

$pdf->Cell(40, 6, 'NIS:', 0, 0, 'L');
$pdf->Cell(0, 6, $kasus['siswa_nis'], 0, 1, 'L');

$pdf->Cell(40, 6, 'Jurusan:', 0, 0, 'L');
$pdf->Cell(0, 6, $kasus['jurusan_nama'], 0, 1, 'L');

$pdf->Cell(40, 6, 'Tanggal Pelaporan:', 0, 0, 'L');
$pdf->Cell(0, 6, date('d/m/Y', strtotime($kasus['tanggal_pelaporan'])), 0, 1, 'L');

$pdf->Cell(40, 6, 'Sumber Kasus:', 0, 0, 'L');
$pdf->Cell(0, 6, $kasus['sumber_kasus'], 0, 1, 'L');

$pdf->Cell(40, 6, 'Kategori:', 0, 0, 'L');
$pdf->Cell(0, 6, $kasus['kategori_masalah'], 0, 1, 'L');

$pdf->Cell(40, 6, 'Status:', 0, 0, 'L');
$pdf->Cell(0, 6, $kasus['status_kasus'], 0, 1, 'L');

$pdf->Cell(40, 6, 'Guru BK:', 0, 0, 'L');
$pdf->Cell(0, 6, $kasus['guru_bk_nama'], 0, 1, 'L');

$pdf->Ln(10);

// Deskripsi Masalah
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(0, 8, 'DESKRIPSI MASALAH', 0, 1, 'L');
$pdf->SetFont('Arial', '', 10);

$pdf->Cell(0, 6, 'Judul Kasus:', 0, 1, 'L');
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(0, 6, $kasus['judul_kasus'], 0, 1, 'L');
$pdf->SetFont('Arial', '', 10);

$pdf->Ln(5);
$pdf->Cell(0, 6, 'Deskripsi:', 0, 1, 'L');
$pdf->MultiCell(0, 6, $kasus['deskripsi_awal'], 0, 'L');

$pdf->Ln(10);

// Jurnal Perkembangan
if(mysqli_num_rows($result_jurnal) > 0) {
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(0, 8, 'JURNAL PERKEMBANGAN KASUS', 0, 1, 'L');
    $pdf->SetFont('Arial', '', 10);
    
    $no_jurnal = 1;
    while($jurnal = mysqli_fetch_assoc($result_jurnal)) {
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Cell(0, 6, 'Sesi Konseling #' . $no_jurnal . ' - ' . date('d/m/Y', strtotime($jurnal['tanggal_konseling'])), 0, 1, 'L');
        $pdf->SetFont('Arial', '', 9);
        
        if(!empty($jurnal['uraian_sesi'])) {
            $pdf->Cell(0, 5, 'Uraian Sesi:', 0, 1, 'L');
            $pdf->MultiCell(0, 5, $jurnal['uraian_sesi'], 0, 'L');
            $pdf->Ln(3);
        }
        
        if(!empty($jurnal['analisis_diagnosis'])) {
            $pdf->Cell(0, 5, 'Analisis/Diagnosis:', 0, 1, 'L');
            $pdf->MultiCell(0, 5, $jurnal['analisis_diagnosis'], 0, 'L');
            $pdf->Ln(3);
        }
        
        if(!empty($jurnal['tindakan_intervensi'])) {
            $pdf->Cell(0, 5, 'Tindakan/Intervensi:', 0, 1, 'L');
            $pdf->MultiCell(0, 5, $jurnal['tindakan_intervensi'], 0, 'L');
            $pdf->Ln(3);
        }
        
        if(!empty($jurnal['rencana_tindak_lanjut'])) {
            $pdf->Cell(0, 5, 'Rencana Tindak Lanjut:', 0, 1, 'L');
            $pdf->MultiCell(0, 5, $jurnal['rencana_tindak_lanjut'], 0, 'L');
            $pdf->Ln(3);
        }
        
        $pdf->Ln(5);
        $no_jurnal++;
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
$pdf->Cell(95, 6, $kasus['guru_bk_nama'], 0, 1, 'C');

$pdf->SetFont('Arial', '', 10);
$pdf->Cell(95, 6, 'NIP. -', 0, 0, 'C');
$pdf->Cell(95, 6, 'Guru Bimbingan dan Konseling', 0, 1, 'C');

// Output PDF
$pdf->Output('Laporan_Kasus_' . $kasus['kasus_kode'] . '.pdf', 'D');
?>
