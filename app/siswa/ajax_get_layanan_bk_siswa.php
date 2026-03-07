<?php
include '../koneksi.php';

// Set header untuk JSON
header('Content-Type: application/json');

// Get layanan BK data untuk siswa
$query = "SELECT l.*, k.kelas_nama, u.user_nama 
          FROM layanan_bk l 
          LEFT JOIN kelas k ON l.kelas_id = k.kelas_id 
          LEFT JOIN user u ON l.created_by = u.user_id 
          ORDER BY l.tanggal_pelaksanaan ASC";

$result = mysqli_query($koneksi, $query);

$events = array();

while($data = mysqli_fetch_assoc($result)) {
    // Determine color based on jenis layanan
    $color = '';
    switch($data['jenis_layanan']) {
        case 'Layanan Klasikal':
            $color = '#3c8dbc'; // blue
            break;
        case 'Bimbingan Kelompok':
            $color = '#00a65a'; // green
            break;
        case 'Konseling Kelompok':
            $color = '#f39c12'; // yellow
            break;
        case 'Konsultasi':
            $color = '#dd4b39'; // red
            break;
        case 'Mediasi':
            $color = '#605ca8'; // purple
            break;
        case 'Layanan Advokasi':
            $color = '#ff851b'; // orange
            break;
        case 'Layanan Peminatan':
            $color = '#39cccc'; // teal
            break;
        default:
            $color = '#777'; // gray
            break;
    }
    
    // Create event title
    $title = $data['jenis_layanan'] . ' - ' . $data['topik_materi'];
    if($data['kelas_nama']) {
        $title .= ' (' . $data['kelas_nama'] . ')';
    }
    
    // Create event description
    $description = 'Bidang: ' . $data['bidang_layanan'] . "\n";
    $description .= 'Sasaran: ' . $data['sasaran_layanan'] . "\n";
    $description .= 'Peserta: ' . $data['jumlah_peserta'] . ' orang' . "\n";
    $description .= 'Dibuat oleh: ' . $data['user_nama'];
    
    $events[] = array(
        'id' => $data['layanan_id'],
        'title' => $title,
        'start' => $data['tanggal_pelaksanaan'],
        'color' => $color,
        'description' => $description,
        'url' => 'layanan_bk_detail.php?id=' . $data['layanan_id']
    );
}

// Output JSON
echo json_encode($events);
?>
