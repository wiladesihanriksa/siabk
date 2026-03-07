<?php 
include '../koneksi.php';

$id = $_POST['ta'];

if(empty($id)) {
    echo '<option value="">- Pilih Kelas</option>';
    exit;
}

$kelas = mysqli_query($koneksi,"select * from kelas, jurusan where kelas_jurusan=jurusan_id and kelas_ta='$id' order by kelas_jurusan asc");

if(!$kelas) {
    echo '<option value="">- Pilih Kelas (Error)</option>';
    exit;
}

$count = mysqli_num_rows($kelas);

// Return only the options, not the full select tag
echo '<option value="">- Pilih Kelas</option>';
while($k = mysqli_fetch_array($kelas)){
    echo '<option value="' . $k['kelas_id'] . '">' . $k['jurusan_nama'] . ' | ' . $k['kelas_nama'] . '</option>';
}
?>