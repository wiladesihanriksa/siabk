<?php 
// File login khusus untuk ADMINISTRATOR saja
// Hanya menerima user dengan level 'administrator'

include 'koneksi.php';

// menangkap data yang dikirim dari form
$username = $_POST['username'];
$password = md5($_POST['password']);

// Query khusus untuk administrator saja
$login = mysqli_query($koneksi, "SELECT * FROM user WHERE user_username='$username' AND user_password='$password' AND user_level='administrator'");
$cek = mysqli_num_rows($login);

if($cek > 0){
    session_start();
    $data = mysqli_fetch_assoc($login);
    $_SESSION['id'] = $data['user_id'];
    $_SESSION['nama'] = $data['user_nama'];
    $_SESSION['username'] = $data['user_username'];
    $_SESSION['level'] = $data['user_level'];

    // Pastikan hanya administrator yang bisa masuk
    if($data['user_level'] == "administrator"){
        header("location:admin/");
    }else{
        // Jika bukan administrator, tolak akses
        header("location:admin.php?alert=role_salah&pesan=Hanya administrator yang dapat mengakses halaman ini");
    }
}else{
    // Login gagal - username/password salah atau bukan administrator
    header("location:admin.php?alert=gagal&pesan=Username/password salah atau Anda bukan administrator");
}
?>
