<?php 
include '../koneksi.php';

$id = $_POST['kelas'];

if(empty($id)) {
    echo '<option value="">- Pilih Siswa</option>';
    exit;
}

// Hanya tampilkan siswa yang aktif di kelas yang dipilih
$siswa = mysqli_query($koneksi,"SELECT s.* FROM siswa s 
                                JOIN kelas_siswa ks ON s.siswa_id = ks.ks_siswa 
                                WHERE ks.ks_kelas='$id' AND s.siswa_status = 'aktif' 
                                ORDER BY s.siswa_nama ASC");

if(!$siswa) {
    echo '<option value="">- Pilih Siswa (Error)</option>';
    exit;
}

// Return only the options, not the full select tag
echo '<option value="">- Pilih Siswa</option>';
while($k = mysqli_fetch_array($siswa)){
    echo '<option value="' . $k['siswa_id'] . '">' . $k['siswa_nama'] . ' | ' . $k['siswa_nis'] . '</option>';
}
?>