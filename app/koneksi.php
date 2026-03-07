<?php
// Mengambil kredensial dari Environment Variables (Render) 
// Jika tidak ada, gunakan default localhost
$DB_HOST = getenv('DB_HOST') ?: 'localhost';
$DB_USER = getenv('DB_USER') ?: 'root';
$DB_PASS = getenv('DB_PASS') ?: '';
$DB_NAME = getenv('DB_NAME') ?: 'siabk';
$DB_PORT = getenv('DB_PORT') ?: 3306;

date_default_timezone_set('Asia/Jakarta');
setlocale(LC_TIME, 'id_ID.UTF-8', 'id_ID', 'id');

$koneksi = mysqli_init();
mysqli_options($koneksi, MYSQLI_OPT_CONNECT_TIMEOUT, 10);

// Cek apakah server database cloud mewajibkan SSL (seperti Aiven/Tidb)
// Jika iya, tambahkan parameter SSL di mysqli_real_connect
if (!mysqli_real_connect($koneksi, $DB_HOST, $DB_USER, $DB_PASS, $DB_NAME, $DB_PORT)) {
    die('Koneksi database gagal: ' . mysqli_connect_error());
}

mysqli_set_charset($koneksi, "utf8");
?>