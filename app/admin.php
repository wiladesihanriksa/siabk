<?php
include 'koneksi.php';
include 'functions_app_settings.php';

// Ambil pengaturan aplikasi
$app_settings = getAppSettings($koneksi);
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title><?php echo getSetting($app_settings, 'app_name', 'Sistem Informasi E-Point Siswa MAN 2 Semarang'); ?></title>
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  
  <?php 
  $favicon = getAppFavicon($app_settings);
  if(!empty($favicon)): 
  ?>
  <link rel="icon" type="image/x-icon" href="<?php echo $favicon; ?>">
  <?php endif; ?>
  
  <!-- Bootstrap & Font Awesome -->
  <link rel="stylesheet" href="assets/bower_components/bootstrap/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="assets/bower_components/font-awesome/css/font-awesome.min.css">
  <link rel="stylesheet" href="assets/bower_components/Ionicons/css/ionicons.min.css">
  
  <!-- Google Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  
  <!-- Custom Styles -->
  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }
    
    body {
      font-family: 'Poppins', sans-serif;
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
    }
    
    .admin-login-container {
      background: rgba(255, 255, 255, 0.95);
      backdrop-filter: blur(10px);
      border-radius: 20px;
      box-shadow: 0 20px 60px rgba(0,0,0,0.3);
      padding: 40px;
      width: 100%;
      max-width: 450px;
      text-align: center;
    }
    
    .admin-logo {
      width: 120px;
      height: 120px;
      border-radius: 50%;
      margin: 0 auto 30px;
      box-shadow: 0 10px 30px rgba(0,0,0,0.2);
      display: block;
    }
    
    .admin-title {
      font-size: 1.8rem;
      font-weight: 700;
      color: #333;
      margin-bottom: 10px;
    }
    
    .admin-subtitle {
      font-size: 1.1rem;
      color: #666;
      margin-bottom: 30px;
    }
    
    .form-group {
      margin-bottom: 20px;
      text-align: left;
    }
    
    .form-group label {
      font-weight: 500;
      color: #333;
      margin-bottom: 8px;
      display: block;
    }
    
    .form-control {
      border-radius: 10px;
      border: 2px solid #e9ecef;
      padding: 12px 15px;
      font-size: 1rem;
      transition: all 0.3s ease;
      width: 100%;
    }
    
    .form-control:focus {
      border-color: #667eea;
      box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
      outline: none;
    }
    
    .btn-admin-login {
      background: linear-gradient(45deg, #667eea, #764ba2);
      border: none;
      border-radius: 10px;
      padding: 12px 30px;
      font-weight: 600;
      font-size: 1.1rem;
      color: white;
      width: 100%;
      transition: all 0.3s ease;
      margin-top: 10px;
    }
    
    .btn-admin-login:hover {
      transform: translateY(-2px);
      box-shadow: 0 10px 30px rgba(102, 126, 234, 0.4);
      color: white;
    }
    
    .back-to-home {
      display: inline-block;
      margin-top: 20px;
      color: #667eea;
      text-decoration: none;
      font-weight: 500;
      transition: all 0.3s ease;
    }
    
    .back-to-home:hover {
      color: #764ba2;
      text-decoration: none;
      transform: translateY(-2px);
    }
    
    .alert {
      border-radius: 10px;
      border: none;
      margin-bottom: 20px;
      padding: 15px;
    }
    
    .alert-danger {
      background: linear-gradient(45deg, #ff6b6b, #ee5a52);
      color: white;
    }
    
    .alert-success {
      background: linear-gradient(45deg, #51cf66, #40c057);
      color: white;
    }
    
    .alert-warning {
      background: linear-gradient(45deg, #ffd43b, #fab005);
      color: #333;
    }
    
    @keyframes fadeInUp {
      from {
        opacity: 0;
        transform: translateY(30px);
      }
      to {
        opacity: 1;
        transform: translateY(0);
      }
    }
    
    .admin-login-container {
      animation: fadeInUp 0.8s ease-out;
    }
  </style>
</head>
<body>
  <div class="admin-login-container">
    <!-- Logo -->
    <?php 
    $login_logo = getLoginLogo($app_settings);
    if($login_logo != '../gambar/sistem/user.png'): 
    ?>
      <img src="<?php echo $login_logo; ?>" class="admin-logo" alt="Logo">
    <?php else: ?>
      <img src="gambar/sistem/logo.png" class="admin-logo" alt="Logo">
    <?php endif; ?>
    
    <!-- Title -->
    <h1 class="admin-title">
      <i class="fa fa-user-shield"></i>
      Login Admin
    </h1>
    <p class="admin-subtitle">
      <?php echo getSetting($app_settings, 'app_name', 'SISTEM INFORMASI E-POINT SISWA'); ?><br>
      <strong><?php echo getSetting($app_settings, 'app_author', 'MA YASMU'); ?></strong>
    </p>

    <!-- Alerts -->
    <?php 
    if(isset($_GET['alert'])){
      if($_GET['alert'] == "gagal"){
        $pesan = isset($_GET['pesan']) ? $_GET['pesan'] : 'Username dan password salah';
        echo "<div class='alert alert-danger'><b>LOGIN GAGAL</b><br> $pesan</div>";
      }else if($_GET['alert'] == "role_salah"){
        $pesan = isset($_GET['pesan']) ? $_GET['pesan'] : 'Anda tidak memiliki akses sebagai administrator';
        echo "<div class='alert alert-warning'><b>AKSES DITOLAK</b><br> $pesan</div>";
      }else if($_GET['alert'] == "deprecated"){
        $pesan = isset($_GET['pesan']) ? $_GET['pesan'] : 'File login ini sudah tidak digunakan';
        echo "<div class='alert alert-info'><b>INFORMASI</b><br> $pesan</div>";
      }else if($_GET['alert'] == "logout"){
        echo "<div class='alert alert-success'>Anda telah berhasil logout</div>";
      }else if($_GET['alert'] == "belum_login"){
        echo "<div class='alert alert-warning'>Anda harus login untuk mengakses halaman admin</div>";
      }
    }
    ?>

    <!-- Login Form -->
    <form action="periksa_admin_only.php" method="POST">
      <div class="form-group">
        <label for="username">
          <i class="fa fa-user"></i> Username
        </label>
        <input type="text" class="form-control" id="username" name="username" placeholder="Masukkan Username" required autocomplete="off">
      </div>
      <div class="form-group">
        <label for="admin_password">
          <i class="fa fa-lock"></i> Password
        </label>
        <input type="password" class="form-control" id="admin_password" name="password" placeholder="Masukkan Password" required autocomplete="off">
      </div>
      <button type="submit" class="btn-admin-login">
        <i class="fa fa-sign-in-alt"></i> Login Admin
      </button>
    </form>

    <!-- Back to Home Link -->
    <a href="index.php" class="back-to-home">
      <i class="fa fa-arrow-left"></i> Kembali ke Beranda
    </a>
  </div>

  <!-- Scripts -->
  <script src="assets/bower_components/jquery/dist/jquery.min.js"></script>
  <script src="assets/bower_components/bootstrap/dist/js/bootstrap.min.js"></script>
  
  <script>
    // Auto-hide alerts after 5 seconds
    setTimeout(function() {
      $('.alert').fadeOut('slow');
    }, 5000);
    
    // Focus on username field when page loads
    $(document).ready(function() {
      $('#username').focus();
    });
  </script>
</body>
</html>
