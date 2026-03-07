<?php
include '../koneksi.php';
include '../library/fpdf185/fpdf.php';

// Ambil parameter filter dari URL
$tanggal_mulai = isset($_GET['tanggal_mulai']) ? $_GET['tanggal_mulai'] : date('Y-m-01');
$tanggal_akhir = isset($_GET['tanggal_akhir']) ? $_GET['tanggal_akhir'] : date('Y-m-d');
$petugas_id = isset($_GET['petugas_id']) ? $_GET['petugas_id'] : '';

// Query data kunjungan dengan filter
$where_clause = "WHERE k.tanggal_kunjungan BETWEEN '$tanggal_mulai' AND '$tanggal_akhir'";
if(!empty($petugas_id)) {
    $where_clause .= " AND k.petugas_bk_id = '$petugas_id'";
}

$query = "SELECT k.*, s.siswa_nama, s.siswa_nis, j.jurusan_nama, kls.kelas_nama, u.user_nama as petugas_nama 
          FROM kunjungan_rumah k 
          LEFT JOIN siswa s ON k.siswa_id = s.siswa_id 
          LEFT JOIN jurusan j ON s.siswa_jurusan = j.jurusan_id 
          LEFT JOIN kelas_siswa ks ON s.siswa_id = ks.ks_siswa 
          LEFT JOIN kelas kls ON ks.ks_kelas = kls.kelas_id 
          LEFT JOIN user u ON k.petugas_bk_id = u.user_id 
          $where_clause 
          ORDER BY k.tanggal_kunjungan DESC, k.waktu_kunjungan DESC";

$result = mysqli_query($koneksi, $query);

// Load konfigurasi dari database atau default
$config_query = mysqli_query($koneksi, "SELECT * FROM pengaturan_raport ORDER BY id DESC LIMIT 1");
$config = mysqli_fetch_assoc($config_query);

if (!$config) {
    $config = array(
        'nama_madrasah' => 'Madrasah Aliyah YASMU',
        'alamat_madrasah' => 'Jl. Kyai Sahlan I No. 24 Manyarejo',
        'kota' => 'Gresik',
        'nama_waka' => 'Nurul Faridah, S.Pd',
        'nip_waka' => '-',
        'nama_guru_bk' => 'Guru BK',
        'nip_guru_bk' => '-',
    );
}

// Buat PDF
$pdf = new FPDF('L', 'mm', 'A4'); // Landscape untuk laporan
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

// Periode Laporan
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(0, 5, 'Periode: ' . date('d F Y', strtotime($tanggal_mulai)) . ' s/d ' . date('d F Y', strtotime($tanggal_akhir)), 0, 1, 'C');
$pdf->Ln(10);

// Statistik Ringkas
$total_kunjungan = mysqli_num_rows($result);
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(0, 8, 'RINGKASAN STATISTIK', 0, 1, 'L');
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(50, 6, 'Total Kunjungan:', 0, 0, 'L');
$pdf->Cell(0, 6, $total_kunjungan . ' kunjungan', 0, 1, 'L');
$pdf->Cell(50, 6, 'Tanggal Cetak:', 0, 0, 'L');
$pdf->Cell(0, 6, date('d F Y H:i:s'), 0, 1, 'L');
$pdf->Ln(5);

// Tabel Data Kunjungan
if($total_kunjungan > 0) {
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->Cell(15, 8, 'No', 1, 0, 'C');
    $pdf->Cell(25, 8, 'Kode', 1, 0, 'C');
    $pdf->Cell(40, 8, 'Nama Siswa', 1, 0, 'C');
    $pdf->Cell(25, 8, 'Tanggal', 1, 0, 'C');
    $pdf->Cell(20, 8, 'Waktu', 1, 0, 'C');
    $pdf->Cell(50, 8, 'Tujuan Kunjungan', 1, 0, 'C');
    $pdf->Cell(30, 8, 'Guru BK', 1, 0, 'C');
    $pdf->Cell(25, 8, 'Alamat', 1, 1, 'C');

    $no = 1;
    mysqli_data_seek($result, 0);
    while($data = mysqli_fetch_assoc($result)) {
        $pdf->SetFont('Arial', '', 8);
        $pdf->Cell(15, 6, $no++, 1, 0, 'C');
        $pdf->Cell(25, 6, $data['kunjungan_kode'], 1, 0, 'C');
        $pdf->Cell(40, 6, substr($data['siswa_nama'], 0, 20), 1, 0, 'L');
        $pdf->Cell(25, 6, date('d/m/Y', strtotime($data['tanggal_kunjungan'])), 1, 0, 'C');
        $pdf->Cell(20, 6, date('H:i', strtotime($data['waktu_kunjungan'])), 1, 0, 'C');
        $pdf->Cell(50, 6, substr($data['tujuan_kunjungan'], 0, 25), 1, 0, 'L');
        $pdf->Cell(30, 6, substr($data['petugas_nama'], 0, 15), 1, 0, 'L');
        $pdf->Cell(25, 6, substr($data['alamat_kunjungan'], 0, 15), 1, 1, 'L');
    }
} else {
    $pdf->SetFont('Arial', '', 10);
    $pdf->Cell(0, 8, 'Tidak ada data kunjungan untuk periode yang dipilih', 1, 1, 'C');
}

$pdf->Ln(15);

// Statistik per Petugas
if($total_kunjungan > 0) {
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(0, 8, 'STATISTIK KUNJUNGAN PER GURU BK', 0, 1, 'L');
    $pdf->Ln(3);

    // Query statistik petugas
    $query_petugas = "SELECT u.user_nama, COUNT(*) as jumlah 
                      FROM kunjungan_rumah k 
                      LEFT JOIN user u ON k.petugas_bk_id = u.user_id 
                      $where_clause 
                      GROUP BY k.petugas_bk_id, u.user_nama 
                      ORDER BY jumlah DESC";
    $result_petugas = mysqli_query($koneksi, $query_petugas);

    $pdf->SetFont('Arial', 'B', 10);
    $pdf->Cell(80, 8, 'Nama Guru BK', 1, 0, 'C');
    $pdf->Cell(30, 8, 'Jumlah Kunjungan', 1, 0, 'C');
    $pdf->Cell(30, 8, 'Persentase', 1, 1, 'C');

    while($petugas = mysqli_fetch_assoc($result_petugas)) {
        $persentase = $total_kunjungan > 0 ? ($petugas['jumlah'] / $total_kunjungan) * 100 : 0;
        
        $pdf->SetFont('Arial', '', 10);
        $pdf->Cell(80, 6, $petugas['user_nama'], 1, 0, 'L');
        $pdf->Cell(30, 6, $petugas['jumlah'], 1, 0, 'C');
        $pdf->Cell(30, 6, number_format($persentase, 1) . '%', 1, 1, 'C');
    }
}

$pdf->Ln(15);

// Tanda Tangan
$pdf->SetFont('Arial', '', 11);
$pdf->Cell(0, 6, $config['kota'] . ', ' . date('d F Y'), 0, 1, 'C');
$pdf->Ln(10);

// Tanda Tangan Waka Kesiswaan dan Guru BK
$pdf->Cell(95, 6, 'Waka Kesiswaan', 0, 0, 'C');
$pdf->Cell(95, 6, 'Guru BK', 0, 1, 'C');
$pdf->Ln(20);

$pdf->SetFont('Arial', 'B', 11);
$pdf->Cell(95, 6, $config['nama_waka'], 0, 0, 'C');
$pdf->Cell(95, 6, $config['nama_guru_bk'], 0, 1, 'C');

$pdf->SetFont('Arial', '', 10);
$pdf->Cell(95, 6, 'NIP. ' . $config['nip_waka'], 0, 0, 'C');
$pdf->Cell(95, 6, 'NIP. ' . $config['nip_guru_bk'], 0, 1, 'C');

// Footer
$pdf->Ln(10);
$pdf->SetFont('Arial', 'I', 8);
$pdf->Cell(0, 6, 'Dokumen ini dicetak secara otomatis pada ' . date('d/m/Y H:i:s'), 0, 1, 'C');
$pdf->Cell(0, 6, 'Sistem E-Point - Madrasah Aliyah YASMU', 0, 1, 'C');

// Output PDF
$filename = 'Laporan_Kunjungan_Rumah_' . date('Y-m-d_H-i-s') . '.pdf';
$pdf->Output($filename, 'D');
?>
