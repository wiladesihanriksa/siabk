<?php 
// File login khusus untuk GURU BK saja
// Hanya menerima user dengan level 'guru_bk'

include 'koneksi.php';

// menangkap data yang dikirim dari form
$username = $_POST['username'];
$password = md5($_POST['password']);

// Query khusus untuk guru_bk saja
$login = mysqli_query($koneksi, "SELECT * FROM user WHERE user_username='$username' AND user_password='$password' AND user_level='guru_bk'");
$cek = mysqli_num_rows($login);

if($cek > 0){
    session_start();
    $data = mysqli_fetch_assoc($login);
    $_SESSION['id'] = $data['user_id'];
    $_SESSION['nama'] = $data['user_nama'];
    $_SESSION['username'] = $data['user_username'];
    $_SESSION['level'] = $data['user_level'];

    // Pastikan hanya guru_bk yang bisa masuk
    if($data['user_level'] == "guru_bk"){
        header("location:admin/guru_bk_dashboard.php");
    }else{
        // Jika bukan guru_bk, tolak akses
        header("location:index.php?alert=role_salah&pesan=Hanya guru BK yang dapat mengakses halaman ini");
    }
}else{
    // Login gagal - username/password salah atau bukan guru_bk
    header("location:index.php?alert=gagal&pesan=Username/password salah atau Anda bukan guru BK");
}
?>
