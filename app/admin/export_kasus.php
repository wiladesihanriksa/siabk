<?php
include 'header.php';

// Set header untuk download Excel
header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment; filename="laporan_kasus_' . date('Y-m-d') . '.xls"');

// Ambil parameter filter
$where_clause = "";
if(isset($_GET['tanggal_awal']) && isset($_GET['tanggal_akhir'])) {
    $tanggal_awal = date('Y-m-d', strtotime(str_replace('/', '-', $_GET['tanggal_awal'])));
    $tanggal_akhir = date('Y-m-d', strtotime(str_replace('/', '-', $_GET['tanggal_akhir'])));
    $where_clause = "WHERE DATE(tanggal_pelaporan) BETWEEN '$tanggal_awal' AND '$tanggal_akhir'";
}

if(isset($_GET['status']) && $_GET['status'] != '') {
    $status = $_GET['status'];
    $where_clause .= ($where_clause ? " AND" : "WHERE") . " status_kasus = '$status'";
}

// Query data kasus
$query = "SELECT k.*, s.siswa_nama, s.siswa_nis 
          FROM kasus_siswa k 
          LEFT JOIN siswa s ON k.siswa_id = s.siswa_id 
          $where_clause 
          ORDER BY k.tanggal_pelaporan DESC";
$result = mysqli_query($koneksi, $query);
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Kasus Siswa</title>
</head>
<body>
    <table border="1">
        <thead>
            <tr style="background-color: #f0f0f0;">
                <th colspan="8" style="text-align: center; font-size: 16px; font-weight: bold;">
                    LAPORAN KASUS SISWA<br>
                    <?php echo isset($_GET['tanggal_awal']) ? 'Periode: ' . $_GET['tanggal_awal'] . ' - ' . $_GET['tanggal_akhir'] : 'Semua Periode'; ?>
                </th>
            </tr>
            <tr style="background-color: #e0e0e0;">
                <th>No</th>
                <th>Kode Kasus</th>
                <th>Nama Siswa</th>
                <th>NIS</th>
                <th>Kategori Masalah</th>
                <th>Status Kasus</th>
                <th>Tanggal Pelaporan</th>
                <th>Guru BK</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $no = 1;
            while($row = mysqli_fetch_assoc($result)) {
            ?>
            <tr>
                <td><?php echo $no++; ?></td>
                <td><?php echo $row['kode_kasus']; ?></td>
                <td><?php echo $row['siswa_nama']; ?></td>
                <td><?php echo $row['siswa_nis']; ?></td>
                <td><?php echo $row['kategori_masalah']; ?></td>
                <td><?php echo $row['status_kasus']; ?></td>
                <td><?php echo date('d/m/Y', strtotime($row['tanggal_pelaporan'])); ?></td>
                <td><?php echo $row['guru_bk']; ?></td>
            </tr>
            <?php } ?>
        </tbody>
    </table>
</body>
</html>