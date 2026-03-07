<?php
include '../koneksi.php';
include '../library/fpdf185/fpdf.php';

// Helper format tanggal Indonesia dengan fallback jika Intl tidak tersedia
function format_tanggal_id($dateInput) {
    $timestamp = is_numeric($dateInput) ? (int) $dateInput : strtotime($dateInput);
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
    return strftime('%d %B %Y', $timestamp);
}

// Ambil ID kelas dari URL
$kelas_id = $_GET['kelas_id'];

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

// Ambil data kelas dengan filter tahun ajaran aktif
$kelas_query = mysqli_query($koneksi, "SELECT k.*, j.jurusan_nama, ta.ta_nama 
    FROM kelas k 
    JOIN jurusan j ON k.kelas_jurusan = j.jurusan_id 
    JOIN ta ON k.kelas_ta = ta.ta_id 
    WHERE k.kelas_id = '$kelas_id' AND ta.ta_status = 1");
$kelas = mysqli_fetch_assoc($kelas_query);

if (!$kelas) {
    die('Data kelas tidak ditemukan atau kelas tidak terdaftar di tahun ajaran aktif');
}

// Pastikan tahun ajaran yang ditampilkan adalah tahun ajaran aktif
$kelas['ta_nama'] = $ta_aktif_nama;

// Ambil data siswa di kelas
$siswa_query = mysqli_query($koneksi, "SELECT s.*, ks.ks_id 
    FROM siswa s 
    JOIN kelas_siswa ks ON s.siswa_id = ks.ks_siswa 
    WHERE ks.ks_kelas = '$kelas_id' 
    ORDER BY s.siswa_nama");

$siswa_data = array();
$max_nis_length = 0;
while($s = mysqli_fetch_assoc($siswa_query)) {
    $siswa_data[] = $s;
    // Hitung panjang NIS terpanjang
    $nis_length = strlen($s['siswa_nis']);
    if($nis_length > $max_nis_length) {
        $max_nis_length = $nis_length;
    }
}

// Hitung lebar kolom dinamis
$page_width = 190; // Lebar halaman A4 dalam mm
$col_no = 10; // Kolom No
$col_prestasi = 20; // Kolom Prestasi
$col_pelanggaran = 20; // Kolom Pelanggaran
$col_point = 20; // Kolom Point Bersih
$col_status = 25; // Kolom Status

// Hitung lebar NIS berdasarkan karakter (1 karakter ≈ 1.5mm untuk font Arial 9pt)
$col_nis = max(35, $max_nis_length * 1.5 + 8); // Minimal 35mm, tambah 8mm untuk padding

// Hitung lebar nama yang tersisa
$fixed_width = $col_no + $col_prestasi + $col_pelanggaran + $col_point + $col_status;
$available_width = $page_width - $fixed_width - $col_nis;
$col_nama = max(40, $available_width); // Minimal 40mm untuk nama

// Jika masih tidak cukup, kurangi lebar NIS
if($col_nama < 40) {
    $col_nis = $page_width - $fixed_width - 40;
    $col_nama = 40;
}

if (empty($siswa_data)) {
    die('Tidak ada siswa di kelas ini');
}

// Debug: tampilkan lebar kolom (hapus setelah testing)
// echo "Debug - Max NIS Length: $max_nis_length<br>";
// echo "Debug - Col NIS: $col_nis mm<br>";
// echo "Debug - Col Nama: $col_nama mm<br>";
// echo "Debug - Total Width: " . ($col_no + $col_nama + $col_nis + $col_prestasi + $col_pelanggaran + $col_point + $col_status) . " mm<br>";

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
$pdf->Cell(0, 5, 'Telp: (031) 3930037 | Email: mayasmu@gmail.com', 0, 1, 'C');
// Perkecil spasi setelah telp/email agar header lebih rapat
$pdf->Ln(2);

// Judul Raport
$pdf->SetFont('Arial', 'B', 14);
$pdf->Cell(0, 8, $config['judul_raport'], 0, 1, 'C');
$pdf->SetFont('Arial', '', 12);
$pdf->Cell(0, 5, $config['sub_judul'], 0, 1, 'C');
$pdf->Ln(3);

// Informasi Kelas
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(0, 8, 'INFORMASI KELAS', 0, 1, 'L');
$pdf->SetFont('Arial', '', 11);

$pdf->Cell(40, 6, 'Kelas', 0, 0, 'L');
$pdf->Cell(5, 6, ':', 0, 0, 'C');
$pdf->Cell(0, 6, $kelas['kelas_nama'], 0, 1, 'L');

$pdf->Cell(40, 6, 'Jurusan', 0, 0, 'L');
$pdf->Cell(5, 6, ':', 0, 0, 'C');
$pdf->Cell(0, 6, $kelas['jurusan_nama'], 0, 1, 'L');

$pdf->Cell(40, 6, 'Tahun Ajaran', 0, 0, 'L');
$pdf->Cell(5, 6, ':', 0, 0, 'C');
$pdf->Cell(0, 6, $kelas['ta_nama'], 0, 1, 'L');

$pdf->Cell(40, 6, 'Jumlah Siswa', 0, 0, 'L');
$pdf->Cell(5, 6, ':', 0, 0, 'C');
$pdf->Cell(0, 6, count($siswa_data) . ' orang', 0, 1, 'L');

$pdf->Ln(3);

// Tabel Data Siswa
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(0, 8, 'DATA SISWA', 0, 1, 'L');
$pdf->SetFont('Arial', '', 9);

// Header tabel dengan lebar dinamis
$pdf->Cell($col_no, 8, 'No', 1, 0, 'C');
$pdf->Cell($col_nama, 8, 'Nama Siswa', 1, 0, 'C');
$pdf->Cell($col_nis, 8, 'NIS', 1, 0, 'C');
$pdf->Cell($col_prestasi, 8, 'Prestasi', 1, 0, 'C');
$pdf->Cell($col_pelanggaran, 8, 'Pelanggaran', 1, 0, 'C');
$pdf->Cell($col_point, 8, 'Point Bersih', 1, 0, 'C');
$pdf->Cell($col_status, 8, 'Status', 1, 1, 'C');

$no = 1;
$total_prestasi_kelas = 0;
$total_pelanggaran_kelas = 0;

foreach($siswa_data as $siswa) {
    // Hitung point prestasi hanya dari tahun ajaran aktif
    $prestasi_query = mysqli_query($koneksi, "SELECT SUM(p.prestasi_point) as total 
        FROM input_prestasi ip 
        JOIN prestasi p ON ip.prestasi = p.prestasi_id 
        JOIN kelas k ON ip.kelas = k.kelas_id 
        WHERE ip.siswa = '{$siswa['siswa_id']}' 
        AND k.kelas_ta = '$ta_aktif_id'");
    $prestasi_data = mysqli_fetch_assoc($prestasi_query);
    $total_prestasi = $prestasi_data['total'] ?: 0;
    
    // Hitung point pelanggaran hanya dari tahun ajaran aktif
    $pelanggaran_query = mysqli_query($koneksi, "SELECT SUM(pl.pelanggaran_point) as total 
        FROM input_pelanggaran ip 
        JOIN pelanggaran pl ON ip.pelanggaran = pl.pelanggaran_id 
        JOIN kelas k ON ip.kelas = k.kelas_id 
        WHERE ip.siswa = '{$siswa['siswa_id']}' 
        AND k.kelas_ta = '$ta_aktif_id'");
    $pelanggaran_data = mysqli_fetch_assoc($pelanggaran_query);
    $total_pelanggaran = $pelanggaran_data['total'] ?: 0;
    
    $point_bersih = $total_prestasi - $total_pelanggaran;
    $status = $point_bersih >= 0 ? 'Baik' : 'Perhatian';
    
    $total_prestasi_kelas += $total_prestasi;
    $total_pelanggaran_kelas += $total_pelanggaran;
    
    $pdf->Cell($col_no, 6, $no++, 1, 0, 'C');
    
    // Potong nama jika terlalu panjang
    $nama_display = $siswa['siswa_nama'];
    $max_nama_chars = floor($col_nama / 2); // Estimasi karakter per mm
    if(strlen($nama_display) > $max_nama_chars) {
        $nama_display = substr($nama_display, 0, $max_nama_chars - 3) . '...';
    }
    $pdf->Cell($col_nama, 6, $nama_display, 1, 0, 'L');
    
    $pdf->Cell($col_nis, 6, $siswa['siswa_nis'], 1, 0, 'C');
    $pdf->Cell($col_prestasi, 6, $total_prestasi, 1, 0, 'C');
    $pdf->Cell($col_pelanggaran, 6, $total_pelanggaran, 1, 0, 'C');
    $pdf->Cell($col_point, 6, $point_bersih, 1, 0, 'C');
    $pdf->Cell($col_status, 6, $status, 1, 1, 'C');
}

// Total kelas
$pdf->SetFont('Arial', 'B', 9);
$total_width = $col_no + $col_nama + $col_nis;
$pdf->Cell($total_width, 6, 'TOTAL KELAS', 1, 0, 'R');
$pdf->Cell($col_prestasi, 6, $total_prestasi_kelas, 1, 0, 'C');
$pdf->Cell($col_pelanggaran, 6, $total_pelanggaran_kelas, 1, 0, 'C');
$pdf->Cell($col_point, 6, ($total_prestasi_kelas - $total_pelanggaran_kelas), 1, 0, 'C');
$pdf->Cell($col_status, 6, '', 1, 1, 'C');

$pdf->Ln(130);

// Statistik Kelas
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(0, 8, 'STATISTIK KELAS', 0, 1, 'L');
$pdf->SetFont('Arial', '', 11);

$pdf->Cell(40, 7, 'Total Point Prestasi', 0, 0, 'L');
$pdf->Cell(7, 7, ':', 0, 0, 'C');
$pdf->Cell(0, 7, $total_prestasi_kelas, 0, 1, 'L');

$pdf->Cell(40, 7, 'Total Point Pelanggaran', 0, 0, 'L');
$pdf->Cell(7, 7, ':', 0, 0, 'C');
$pdf->Cell(0, 7, $total_pelanggaran_kelas, 0, 1, 'L');

$pdf->Cell(40, 7, 'Point Bersih Kelas', 0, 0, 'L');
$pdf->Cell(7, 7, ':', 0, 0, 'C');
$pdf->Cell(0, 7, ($total_prestasi_kelas - $total_pelanggaran_kelas), 0, 1, 'L');

$pdf->Cell(40, 7, 'Rata-rata Point/Siswa', 0, 0, 'L');
$pdf->Cell(7, 7, ':', 0, 0, 'C');
$pdf->Cell(0, 7, round(($total_prestasi_kelas - $total_pelanggaran_kelas) / count($siswa_data), 2), 0, 1, 'L');

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
$pdf->Output('Raport_Kelas_' . $kelas['kelas_nama'] . '_' . date('Y-m-d') . '.pdf', 'D');
?>
