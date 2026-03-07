<?php
include '../koneksi.php';
include '../library/fpdf185/fpdf.php';

// Helper format tanggal Indonesia dengan fallback jika Intl tidak tersedia
function format_tanggal_id($dateInput) {
    $timestamp = is_numeric($dateInput) ? (int) $dateInput : strtotime($dateInput);
    // Gunakan IntlDateFormatter agar nama bulan selalu Bahasa Indonesia
    if (class_exists('IntlDateFormatter')) {
        $fmt = new IntlDateFormatter(
            'id_ID',
            IntlDateFormatter::LONG,
            IntlDateFormatter::NONE,
            'Asia/Jakarta',
            IntlDateFormatter::GREGORIAN,
            'd MMMM yyyy'
        );
        $formatted = $fmt ? $fmt->format($timestamp) : false;
        if ($formatted !== false) {
            return $formatted;
        }
    }
    // Fallback ke strftime jika Intl tidak ada
    return strftime('%d %B %Y', $timestamp);
}

// Ambil ID siswa dari URL
$siswa_id = $_GET['id'];

// Load konfigurasi raport dari database
$config_query = mysqli_query($koneksi, "SELECT * FROM pengaturan_raport ORDER BY id DESC LIMIT 1");
$config = mysqli_fetch_assoc($config_query);

if (!$config) {
    // Default config jika belum ada data di database
    $config = array(
        'nama_madrasah' => 'Madrasah Aliyah YASMU',
        'jenis_institusi' => 'Madrasah',
        'alamat_madrasah' => 'Jl. Kyai Sahlan I No. 24 Manyarejo',
        'kota' => 'Gresik',
        'nama_kepala' => 'Nur Ismawati, S.Pd.',
        'nip_kepala' => '-',
        'nama_waka' => 'Nurul Faridah, S.Pd',
        'nip_waka' => '-',
        'nama_guru_bk' => 'Guru BK',
        'judul_raport' => 'LAPORAN PRESTASI DAN PELANGGARAN SISWA',
        'sub_judul' => 'Sistem E-Point Siswa',
        'logo_url' => 'gambar/sistem/logo.png'
    );
}

// Ambil tahun ajaran aktif
$ta_aktif_query = mysqli_query($koneksi, "SELECT * FROM ta WHERE ta_status = 1 LIMIT 1");
$ta_aktif = mysqli_fetch_assoc($ta_aktif_query);

if (!$ta_aktif) {
    die('Tahun ajaran aktif tidak ditemukan');
}

$ta_aktif_id = $ta_aktif['ta_id'];
$ta_aktif_nama = $ta_aktif['ta_nama'];

// Ambil data siswa dengan filter tahun ajaran aktif
$siswa_query = mysqli_query($koneksi, "SELECT s.*, k.kelas_nama, j.jurusan_nama, ta.ta_nama 
    FROM siswa s 
    LEFT JOIN kelas_siswa ks ON s.siswa_id = ks.ks_siswa 
    LEFT JOIN kelas k ON ks.ks_kelas = k.kelas_id 
    LEFT JOIN jurusan j ON k.kelas_jurusan = j.jurusan_id 
    LEFT JOIN ta ON k.kelas_ta = ta.ta_id 
    WHERE s.siswa_id = '$siswa_id' AND ta.ta_status = 1");
$siswa = mysqli_fetch_assoc($siswa_query);

if (!$siswa) {
    die('Data siswa tidak ditemukan atau siswa tidak terdaftar di tahun ajaran aktif');
}

// Pastikan tahun ajaran yang ditampilkan adalah tahun ajaran aktif
$siswa['ta_nama'] = $ta_aktif_nama;

// Ambil data prestasi siswa hanya dari tahun ajaran aktif (berdasarkan kelas yang terkait dengan tahun ajaran aktif)
$prestasi_query = mysqli_query($koneksi, "SELECT p.prestasi_nama, p.prestasi_point, ip.waktu 
    FROM input_prestasi ip 
    JOIN prestasi p ON ip.prestasi = p.prestasi_id 
    JOIN kelas k ON ip.kelas = k.kelas_id 
    WHERE ip.siswa = '$siswa_id' 
    AND k.kelas_ta = '$ta_aktif_id'
    ORDER BY ip.waktu DESC");

// Ambil data pelanggaran siswa hanya dari tahun ajaran aktif (berdasarkan kelas yang terkait dengan tahun ajaran aktif)
$pelanggaran_query = mysqli_query($koneksi, "SELECT pl.pelanggaran_nama, pl.pelanggaran_point, ip.waktu 
    FROM input_pelanggaran ip 
    JOIN pelanggaran pl ON ip.pelanggaran = pl.pelanggaran_id 
    JOIN kelas k ON ip.kelas = k.kelas_id 
    WHERE ip.siswa = '$siswa_id' 
    AND k.kelas_ta = '$ta_aktif_id'
    ORDER BY ip.waktu DESC");

// Hitung total point
$total_prestasi = 0;
$total_pelanggaran = 0;

$prestasi_data = array();
while($p = mysqli_fetch_assoc($prestasi_query)) {
    $prestasi_data[] = $p;
    $total_prestasi += $p['prestasi_point'];
}

$pelanggaran_data = array();
while($pl = mysqli_fetch_assoc($pelanggaran_query)) {
    $pelanggaran_data[] = $pl;
    $total_pelanggaran += $pl['pelanggaran_point'];
}

// Buat PDF
$pdf = new FPDF('P', 'mm', 'A4');
$pdf->AddPage();

// Header dengan Logo
$pdf->SetFont('Arial', 'B', 16);

// Cek apakah logo ada dan bisa dibaca
$logo_path = '../' . $config['logo_url'];
if (file_exists($logo_path) && is_readable($logo_path)) {
    // Tambahkan logo lebih ke atas (margin top lebih kecil)
    $pdf->Image($logo_path, 85, 5, 30, 20); // x, y, width, height
    $pdf->Ln(15); // Spasi setelah logo (dipadatkan)
} else {
    // Jika logo tidak ada, tambahkan spasi
$pdf->Ln(4);
}

$pdf->Cell(0, 8, $config['nama_madrasah'], 0, 1, 'C');
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(0, 5, $config['alamat_madrasah'], 0, 1, 'C');
$pdf->Cell(0, 5, 'Telp: (024) 123456 | Email: info@mayasmu.sch.id', 0, 1, 'C');
// Perkecil spasi setelah telp/email agar header lebih rapat
$pdf->Ln(2);

// Judul Raport
$pdf->SetFont('Arial', 'B', 14);
$pdf->Cell(0, 8, $config['judul_raport'], 0, 1, 'C');
$pdf->SetFont('Arial', '', 12);
$pdf->Cell(0, 5, $config['sub_judul'], 0, 1, 'C');
$pdf->Ln(3);

// Identitas Siswa
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(0, 8, 'IDENTITAS SISWA', 0, 1, 'L');
$pdf->SetFont('Arial', '', 11);

$pdf->Cell(40, 6, 'Nama Siswa', 0, 0, 'L');
$pdf->Cell(5, 6, ':', 0, 0, 'C');
$pdf->Cell(0, 6, $siswa['siswa_nama'], 0, 1, 'L');

$pdf->Cell(40, 6, 'NIS', 0, 0, 'L');
$pdf->Cell(5, 6, ':', 0, 0, 'C');
$pdf->Cell(0, 6, $siswa['siswa_nis'], 0, 1, 'L');

$pdf->Cell(40, 6, 'Kelas', 0, 0, 'L');
$pdf->Cell(5, 6, ':', 0, 0, 'C');
$pdf->Cell(0, 6, $siswa['kelas_nama'] ?: 'Belum ada kelas', 0, 1, 'L');

$pdf->Cell(40, 6, 'Jurusan', 0, 0, 'L');
$pdf->Cell(5, 6, ':', 0, 0, 'C');
$pdf->Cell(0, 6, $siswa['jurusan_nama'] ?: 'Belum ada jurusan', 0, 1, 'L');

$pdf->Cell(40, 6, 'Tahun Ajaran', 0, 0, 'L');
$pdf->Cell(5, 6, ':', 0, 0, 'C');
$pdf->Cell(0, 6, $siswa['ta_nama'] ?: 'Belum ada tahun ajaran', 0, 1, 'L');

$pdf->Ln(3);

// Data Prestasi
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(0, 8, 'DATA PRESTASI', 0, 1, 'L');
$pdf->SetFont('Arial', '', 10);

if (!empty($prestasi_data)) {
    $pdf->Cell(10, 6, 'No', 1, 0, 'C');
    $pdf->Cell(80, 6, 'Nama Prestasi', 1, 0, 'C');
    $pdf->Cell(20, 6, 'Point', 1, 0, 'C');
    $pdf->Cell(30, 6, 'Tanggal', 1, 0, 'C');
    $pdf->Cell(30, 6, 'Waktu', 1, 1, 'C');
    
    $no = 1;
    foreach($prestasi_data as $p) {
        $pdf->Cell(10, 6, $no++, 1, 0, 'C');
        $pdf->Cell(80, 6, substr($p['prestasi_nama'], 0, 35), 1, 0, 'L');
        $pdf->Cell(20, 6, $p['prestasi_point'], 1, 0, 'C');
    $pdf->Cell(30, 6, date('d-m-Y', strtotime($p['waktu'])), 1, 0, 'C');
        $pdf->Cell(30, 6, date('H:i', strtotime($p['waktu'])), 1, 1, 'C');
    }
    
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->Cell(90, 6, 'TOTAL PRESTASI', 1, 0, 'R');
    $pdf->Cell(20, 6, $total_prestasi, 1, 0, 'C');
    $pdf->Cell(60, 6, '', 1, 1, 'C');
} else {
    $pdf->Cell(0, 6, 'Tidak ada data prestasi', 1, 1, 'C');
}

$pdf->Ln(3);

// Data Pelanggaran
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(0, 8, 'DATA PELANGGARAN', 0, 1, 'L');
$pdf->SetFont('Arial', '', 10);

if (!empty($pelanggaran_data)) {
    $pdf->Cell(10, 6, 'No', 1, 0, 'C');
    $pdf->Cell(80, 6, 'Nama Pelanggaran', 1, 0, 'C');
    $pdf->Cell(20, 6, 'Point', 1, 0, 'C');
    $pdf->Cell(30, 6, 'Tanggal', 1, 0, 'C');
    $pdf->Cell(30, 6, 'Waktu', 1, 1, 'C');
    
    $no = 1;
    foreach($pelanggaran_data as $pl) {
        $pdf->Cell(10, 6, $no++, 1, 0, 'C');
        $pdf->Cell(80, 6, substr($pl['pelanggaran_nama'], 0, 35), 1, 0, 'L');
        $pdf->Cell(20, 6, $pl['pelanggaran_point'], 1, 0, 'C');
    $pdf->Cell(30, 6, date('d-m-Y', strtotime($pl['waktu'])), 1, 0, 'C');
        $pdf->Cell(30, 6, date('H:i', strtotime($pl['waktu'])), 1, 1, 'C');
    }
    
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->Cell(90, 6, 'TOTAL PELANGGARAN', 1, 0, 'R');
    $pdf->Cell(20, 6, $total_pelanggaran, 1, 0, 'C');
    $pdf->Cell(60, 6, '', 1, 1, 'C');
} else {
    $pdf->Cell(0, 6, 'Tidak ada data pelanggaran', 1, 1, 'C');
}

$pdf->Ln(5);

// Ringkasan
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(0, 8, 'RINGKASAN', 0, 1, 'L');
$pdf->SetFont('Arial', '', 11);

$pdf->Cell(40, 6, 'Total Point Prestasi', 0, 0, 'L');
$pdf->Cell(5, 6, ':', 0, 0, 'C');
$pdf->Cell(0, 6, $total_prestasi, 0, 1, 'L');

$pdf->Cell(40, 6, 'Total Point Pelanggaran', 0, 0, 'L');
$pdf->Cell(5, 6, ':', 0, 0, 'C');
$pdf->Cell(0, 6, $total_pelanggaran, 0, 1, 'L');

$pdf->Cell(40, 6, 'Point Bersih', 0, 0, 'L');
$pdf->Cell(5, 6, ':', 0, 0, 'C');
$pdf->Cell(0, 6, ($total_prestasi - $total_pelanggaran), 0, 1, 'L');

$pdf->Ln(5);

// Tanda Tangan
$pdf->SetFont('Arial', '', 11);
// Posisi tanggal di sebelah kanan
$pdf->Cell(0, 6, $config['kota'] . ', ' . format_tanggal_id(time()), 0, 1, 'R');
$pdf->Ln(5);

// Tanda Tangan Guru BK dan Waka (Baris Pertama)
$pdf->Cell(95, 6, 'Guru BK', 0, 0, 'C');
$pdf->Cell(95, 6, 'Waka Kesiswaan', 0, 1, 'C');
$pdf->Ln(12);

$pdf->SetFont('Arial', 'B', 11);
$pdf->Cell(95, 6, $config['nama_guru_bk'], 0, 0, 'C');
$pdf->Cell(95, 6, $config['nama_waka'], 0, 1, 'C');

$pdf->SetFont('Arial', '', 10);
$pdf->Cell(95, 6, '', 0, 0, 'C'); // Empty cell untuk guru BK (tidak ada NIP)

$pdf->Ln(3);

// Tanda Tangan Kepala (Baris Kedua - Tengah) - Label dinamis berdasarkan jenis institusi
$kepala_label = ($config['jenis_institusi'] == 'Sekolah') ? 'Kepala Sekolah' : 'Kepala Madrasah';
$pdf->Cell(0, 6, $kepala_label, 0, 1, 'C');
$pdf->Ln(12);

$pdf->SetFont('Arial', 'B', 11);
$pdf->Cell(0, 6, $config['nama_kepala'], 0, 1, 'C');

$pdf->SetFont('Arial', '', 10);

// Output PDF
$pdf->Output('Raport_' . $siswa['siswa_nama'] . '_' . date('Y-m-d') . '.pdf', 'D');
?>
