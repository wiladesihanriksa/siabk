<?php
include '../koneksi.php';
include '../library/fpdf185/fpdf.php';
session_start();

// Cek session siswa
if(!isset($_SESSION['level']) || $_SESSION['level'] != "siswa"){
    header("location:../admin.php?alert=belum_login");
    exit();
}

$siswa_id = $_SESSION['user_id'];
$kunjungan_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if($kunjungan_id <= 0) {
    die('ID kunjungan tidak valid');
}

// Query data kunjungan dengan validasi kepemilikan
$query = "SELECT k.*, s.siswa_nama, s.siswa_nis, j.jurusan_nama, kls.kelas_nama, u.user_nama as petugas_nama 
          FROM kunjungan_rumah k 
          LEFT JOIN siswa s ON k.siswa_id = s.siswa_id 
          LEFT JOIN jurusan j ON s.siswa_jurusan = j.jurusan_id 
          LEFT JOIN kelas_siswa ks ON s.siswa_id = ks.ks_siswa 
          LEFT JOIN kelas kls ON ks.ks_kelas = kls.kelas_id 
          LEFT JOIN user u ON k.petugas_bk_id = u.user_id 
          WHERE k.kunjungan_id = '$kunjungan_id' AND k.siswa_id = '$siswa_id'";

$result = mysqli_query($koneksi, $query);
if(mysqli_num_rows($result) == 0) {
    die('Data kunjungan tidak ditemukan');
}

$data = mysqli_fetch_assoc($result);

// Load konfigurasi dari database atau default
$config_query = mysqli_query($koneksi, "SELECT * FROM pengaturan_raport ORDER BY id DESC LIMIT 1");
$config = mysqli_fetch_assoc($config_query);

if (!$config) {
    // Default config jika belum ada data di database
    $config = array(
        'nama_madrasah' => 'Madrasah Aliyah YASMU',
        'alamat_madrasah' => 'Jl. Kyai Sahlan I No. 24 Manyarejo',
        'kota' => 'Gresik',
        'nama_kepala' => 'Nur Ismawati, S.Pd.',
        'nip_kepala' => '-',
        'nama_waka' => 'Nurul Faridah, S.Pd',
        'nip_waka' => '-',
        'logo_url' => 'gambar/sistem/logo.png'
    );
}

// Buat PDF
$pdf = new FPDF('P', 'mm', 'A4');
$pdf->AddPage();

// Header
$pdf->SetFont('Arial', 'B', 16);
$pdf->Cell(0, 8, $config['nama_madrasah'], 0, 1, 'C');
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(0, 5, $config['alamat_madrasah'], 0, 1, 'C');
$pdf->Cell(0, 5, 'Telp: (024) 123456 | Email: info@mayasmu.sch.id', 0, 1, 'C');
$pdf->Ln(5);

// Judul Laporan
$pdf->SetFont('Arial', 'B', 14);
$pdf->Cell(0, 8, 'LAPORAN KUNJUNGAN RUMAH (HOME VISIT)', 0, 1, 'C');
$pdf->SetFont('Arial', '', 12);
$pdf->Cell(0, 5, 'Bimbingan dan Konseling', 0, 1, 'C');
$pdf->Ln(10);

// Identitas Siswa
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(0, 8, 'IDENTITAS SISWA', 0, 1, 'L');
$pdf->SetFont('Arial', '', 11);

$pdf->Cell(40, 6, 'Nama Siswa', 0, 0, 'L');
$pdf->Cell(5, 6, ':', 0, 0, 'C');
$pdf->Cell(0, 6, $data['siswa_nama'], 0, 1, 'L');

$pdf->Cell(40, 6, 'NIS', 0, 0, 'L');
$pdf->Cell(5, 6, ':', 0, 0, 'C');
$pdf->Cell(0, 6, $data['siswa_nis'], 0, 1, 'L');

$pdf->Cell(40, 6, 'Kelas', 0, 0, 'L');
$pdf->Cell(5, 6, ':', 0, 0, 'C');
$pdf->Cell(0, 6, $data['kelas_nama'] . ' ' . $data['jurusan_nama'], 0, 1, 'L');

$pdf->Ln(10);

// Data Kunjungan
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(0, 8, 'DATA KUNJUNGAN', 0, 1, 'L');
$pdf->SetFont('Arial', '', 11);

$pdf->Cell(40, 6, 'Kode Kunjungan', 0, 0, 'L');
$pdf->Cell(5, 6, ':', 0, 0, 'C');
$pdf->Cell(0, 6, $data['kunjungan_kode'], 0, 1, 'L');

$pdf->Cell(40, 6, 'Tanggal Kunjungan', 0, 0, 'L');
$pdf->Cell(5, 6, ':', 0, 0, 'C');
$pdf->Cell(0, 6, date('d F Y', strtotime($data['tanggal_kunjungan'])), 0, 1, 'L');

$pdf->Cell(40, 6, 'Waktu Kunjungan', 0, 0, 'L');
$pdf->Cell(5, 6, ':', 0, 0, 'C');
$pdf->Cell(0, 6, date('H:i', strtotime($data['waktu_kunjungan'])), 0, 1, 'L');

$pdf->Cell(40, 6, 'Petugas BK', 0, 0, 'L');
$pdf->Cell(5, 6, ':', 0, 0, 'C');
$pdf->Cell(0, 6, $data['petugas_nama'], 0, 1, 'L');

$pdf->Ln(10);

// Alamat Kunjungan
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(0, 8, 'ALAMAT KUNJUNGAN', 0, 1, 'L');
$pdf->SetFont('Arial', '', 11);
$pdf->MultiCell(0, 6, $data['alamat_kunjungan'], 0, 'L');

$pdf->Ln(5);

// Tujuan Kunjungan
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(0, 8, 'TUJUAN KUNJUNGAN', 0, 1, 'L');
$pdf->SetFont('Arial', '', 11);
$pdf->MultiCell(0, 6, $data['tujuan_kunjungan'], 0, 'L');

$pdf->Ln(5);

// Hasil Kunjungan
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(0, 8, 'HASIL KUNJUNGAN', 0, 1, 'L');

// Pihak yang Ditemui
$pdf->SetFont('Arial', 'B', 11);
$pdf->Cell(0, 6, '1. Pihak yang Ditemui:', 0, 1, 'L');
$pdf->SetFont('Arial', '', 11);
$pdf->MultiCell(0, 6, $data['pihak_ditemui'], 0, 'L');

$pdf->Ln(3);

// Hasil Observasi
$pdf->SetFont('Arial', 'B', 11);
$pdf->Cell(0, 6, '2. Hasil Observasi Lingkungan:', 0, 1, 'L');
$pdf->SetFont('Arial', '', 11);
$pdf->MultiCell(0, 6, $data['hasil_observasi'], 0, 'L');

$pdf->Ln(3);

// Ringkasan Wawancara
$pdf->SetFont('Arial', 'B', 11);
$pdf->Cell(0, 6, '3. Ringkasan Hasil Wawancara:', 0, 1, 'L');
$pdf->SetFont('Arial', '', 11);
$pdf->MultiCell(0, 6, $data['ringkasan_wawancara'], 0, 'L');

$pdf->Ln(3);

// Kesimpulan
$pdf->SetFont('Arial', 'B', 11);
$pdf->Cell(0, 6, '4. Kesimpulan:', 0, 1, 'L');
$pdf->SetFont('Arial', '', 11);
$pdf->MultiCell(0, 6, $data['kesimpulan'], 0, 'L');

$pdf->Ln(3);

// Rekomendasi
$pdf->SetFont('Arial', 'B', 11);
$pdf->Cell(0, 6, '5. Rekomendasi/Tindak Lanjut:', 0, 1, 'L');
$pdf->SetFont('Arial', '', 11);
$pdf->MultiCell(0, 6, $data['rekomendasi_tindak_lanjut'], 0, 'L');

$pdf->Ln(15);

// Tanda Tangan
$pdf->SetFont('Arial', '', 11);
$pdf->Cell(0, 6, $config['kota'] . ', ' . date('d F Y'), 0, 1, 'C');
$pdf->Ln(10);

// Tanda Tangan Kepala dan Petugas BK
$pdf->Cell(95, 6, 'Kepala Madrasah', 0, 0, 'C');
$pdf->Cell(95, 6, 'Petugas BK', 0, 1, 'C');
$pdf->Ln(20);

$pdf->SetFont('Arial', 'B', 11);
$pdf->Cell(95, 6, $config['nama_kepala'], 0, 0, 'C');
$pdf->Cell(95, 6, $data['petugas_nama'], 0, 1, 'C');

$pdf->SetFont('Arial', '', 10);
$pdf->Cell(95, 6, 'NIP. ' . $config['nip_kepala'], 0, 0, 'C');
$pdf->Cell(95, 6, 'Guru Bimbingan dan Konseling', 0, 1, 'C');

$pdf->Ln(10);

// Footer
$pdf->SetFont('Arial', 'I', 8);
$pdf->Cell(0, 6, 'Dokumen ini dicetak secara otomatis pada ' . date('d/m/Y H:i:s'), 0, 1, 'C');
$pdf->Cell(0, 6, 'Sistem E-Point - Madrasah Aliyah YASMU', 0, 1, 'C');

// Output PDF
$filename = 'Laporan_Kunjungan_' . $data['kunjungan_kode'] . '_' . date('Y-m-d') . '.pdf';
$pdf->Output($filename, 'D');
?>
