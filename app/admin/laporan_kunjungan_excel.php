<?php
include '../koneksi.php';

// Set header untuk download Excel
header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment; filename="Laporan_Kunjungan_Rumah_' . date('Y-m-d') . '.xls"');

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
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Kunjungan Rumah</title>
</head>
<body>
    <table border="1">
        <tr>
            <td colspan="10" style="text-align: center; font-weight: bold; font-size: 16px;">
                MADRASAH ALIYAH YASMU
            </td>
        </tr>
        <tr>
            <td colspan="10" style="text-align: center; font-weight: bold; font-size: 14px;">
                LAPORAN KUNJUNGAN RUMAH (HOME VISIT)
            </td>
        </tr>
        <tr>
            <td colspan="10" style="text-align: center;">
                Bimbingan dan Konseling
            </td>
        </tr>
        <tr>
            <td colspan="10" style="text-align: center;">
                Periode: <?php echo date('d F Y', strtotime($tanggal_mulai)); ?> s/d <?php echo date('d F Y', strtotime($tanggal_akhir)); ?>
            </td>
        </tr>
        <tr>
            <td colspan="10" style="text-align: center;">
                Dicetak pada: <?php echo date('d F Y H:i:s'); ?>
            </td>
        </tr>
        <tr>
            <td colspan="10"></td>
        </tr>
        
        <!-- Header Tabel -->
        <tr style="background-color: #f0f0f0; font-weight: bold;">
            <td style="text-align: center;">No</td>
            <td style="text-align: center;">Kode Kunjungan</td>
            <td style="text-align: center;">Nama Siswa</td>
            <td style="text-align: center;">NIS</td>
            <td style="text-align: center;">Kelas</td>
            <td style="text-align: center;">Tanggal Kunjungan</td>
            <td style="text-align: center;">Waktu</td>
            <td style="text-align: center;">Petugas BK</td>
            <td style="text-align: center;">Tujuan Kunjungan</td>
            <td style="text-align: center;">Alamat Kunjungan</td>
        </tr>
        
        <?php
        $no = 1;
        while($data = mysqli_fetch_assoc($result)) {
        ?>
        <tr>
            <td style="text-align: center;"><?php echo $no++; ?></td>
            <td><?php echo $data['kunjungan_kode']; ?></td>
            <td><?php echo $data['siswa_nama']; ?></td>
            <td><?php echo $data['siswa_nis']; ?></td>
            <td><?php echo $data['kelas_nama'] . ' ' . $data['jurusan_nama']; ?></td>
            <td style="text-align: center;"><?php echo date('d/m/Y', strtotime($data['tanggal_kunjungan'])); ?></td>
            <td style="text-align: center;"><?php echo date('H:i', strtotime($data['waktu_kunjungan'])); ?></td>
            <td><?php echo $data['petugas_nama']; ?></td>
            <td><?php echo $data['tujuan_kunjungan']; ?></td>
            <td><?php echo $data['alamat_kunjungan']; ?></td>
        </tr>
        <?php } ?>
        
        <?php if(mysqli_num_rows($result) == 0): ?>
        <tr>
            <td colspan="10" style="text-align: center;">Tidak ada data kunjungan untuk periode yang dipilih</td>
        </tr>
        <?php endif; ?>
        
        <tr>
            <td colspan="10"></td>
        </tr>
        
        <!-- Statistik -->
        <tr style="background-color: #f0f0f0; font-weight: bold;">
            <td colspan="10">STATISTIK</td>
        </tr>
        <tr>
            <td colspan="2">Total Kunjungan:</td>
            <td colspan="8"><?php echo mysqli_num_rows($result); ?> kunjungan</td>
        </tr>
        <tr>
            <td colspan="2">Periode:</td>
            <td colspan="8"><?php echo date('d F Y', strtotime($tanggal_mulai)); ?> s/d <?php echo date('d F Y', strtotime($tanggal_akhir)); ?></td>
        </tr>
    </table>
</body>
</html>
