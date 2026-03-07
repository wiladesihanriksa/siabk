<?php
include 'koneksi.php';
include 'functions_app_settings.php';

// Ambil pengaturan aplikasi
$app_settings = getAppSettings($koneksi);
?>
<!DOCTYPE html>
<html lang="id">
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
  <?php else: ?>
  <link rel="icon" type="image/x-icon" href="favicon.ico">
  <?php endif; ?>
  
  <!-- Bootstrap & Font Awesome -->
  <link rel="stylesheet" href="assets/bower_components/bootstrap/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="assets/bower_components/font-awesome/css/font-awesome.min.css">
  <link rel="stylesheet" href="assets/bower_components/Ionicons/css/ionicons.min.css">
  
  <!-- Features Fix CSS -->
  <link rel="stylesheet" href="assets/css/features-fix.css">
  
  <!-- Fallback Font Awesome CDN -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
  
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
      overflow-x: hidden;
    }
    
    /* Navigation - REMOVED */
    
    /* Main Layout - 50:50 Split */
    .main-layout {
      display: flex;
      min-height: 100vh;
    }
    
    /* Hero Section - Left 50% */
    .hero-section {
      flex: 1;
      padding: 80px 40px;
      text-align: center;
      color: white;
      position: relative;
      display: flex;
      align-items: center;
      justify-content: center;
    }
    
    .hero-section::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1000 1000"><polygon fill="rgba(255,255,255,0.05)" points="0,1000 1000,0 1000,1000"/></svg>');
      background-size: cover;
    }
    
    .hero-content {
      position: relative;
      z-index: 2;
    }
    
    .hero-title {
      font-size: 3.5rem;
      font-weight: 700;
      margin-bottom: 20px;
      text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
      animation: fadeInUp 1s ease-out;
    }
    
    .hero-subtitle {
      font-size: 1.3rem;
      font-weight: 300;
      margin-bottom: 40px;
      opacity: 0.9;
      animation: fadeInUp 1s ease-out 0.2s both;
    }
    
    .school-name {
      font-size: 1.5rem;
      font-weight: 600;
      margin-bottom: 50px;
      color: #ffd700;
      animation: fadeInUp 1s ease-out 0.4s both;
    }
    
    /* Login Buttons */
    .login-buttons {
      display: flex;
      justify-content: center;
      gap: 20px;
      margin-top: 40px;
      animation: fadeInUp 1s ease-out 0.6s both;
      flex-wrap: wrap;
    }
    
    .login-btn {
      padding: 15px 40px;
      font-size: 1.1rem;
      font-weight: 600;
      border: none;
      border-radius: 50px;
      text-decoration: none;
      transition: all 0.3s ease;
      box-shadow: 0 10px 30px rgba(0,0,0,0.2);
      position: relative;
      overflow: hidden;
    }
    
    .login-btn::before {
      content: '';
      position: absolute;
      top: 0;
      left: -100%;
      width: 100%;
      height: 100%;
      background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
      transition: left 0.5s;
    }
    
    .login-btn:hover::before {
      left: 100%;
    }
    
    .btn-student {
      background: linear-gradient(45deg, #4CAF50, #45a049);
      color: white;
    }
    
    .btn-admin {
      background: linear-gradient(45deg, #2196F3, #1976D2);
      color: white;
    }
    
    .btn-guru-bk {
      background: linear-gradient(45deg, #FF9800, #F57C00);
      color: white;
    }
    
    .login-btn:hover {
      transform: translateY(-5px);
      box-shadow: 0 15px 40px rgba(0,0,0,0.3);
      color: white;
      text-decoration: none;
    }
    
    /* Features Section - Right 50% */
    .features-section {
      flex: 1;
      padding: 40px;
      background: rgba(255, 255, 255, 0.95);
      backdrop-filter: blur(10px);
      display: flex;
      align-items: center;
      justify-content: center;
      overflow-y: auto;
    }
    
    .features-content {
      width: 100%;
      max-width: 100%;
    }
    
    .features-header {
      text-align: center;
      margin-bottom: 40px;
    }
    
    .features-row {
      display: grid;
      grid-template-columns: repeat(3, 1fr);
      gap: 30px;
      max-width: 1200px;
      margin: 0 auto;
    }
    
    .feature-col {
      width: 100%;
    }
    
    @media (min-width: 1400px) {
      .features-row {
        max-width: 1400px;
        gap: 40px;
      }
      
      .feature-card {
        min-height: 320px;
        padding: 40px 30px;
      }
      
      .feature-title {
        font-size: 1.7rem;
      }
      
      .feature-description {
        font-size: 1.2rem;
      }
      
      .feature-icon i {
        font-size: 3.5rem;
      }
    }
    
    @media (max-width: 1200px) {
      .features-row {
        grid-template-columns: repeat(2, 1fr);
        gap: 25px;
        padding: 0 20px;
      }
      
      .feature-card {
        min-height: 260px;
        padding: 25px 20px;
      }
    }
    
    @media (max-width: 768px) {
      .features-row {
        grid-template-columns: 1fr;
        gap: 20px;
        padding: 0 15px;
      }
      
      .feature-card {
        min-height: 240px;
        padding: 20px 15px;
      }
      
      .feature-icon i {
        font-size: 2rem;
      }
      
      .feature-title {
        font-size: 1.2rem;
      }
      
      .feature-description {
        font-size: 0.9rem;
      }
    }
    
    .feature-card {
      background: white;
      border-radius: 20px;
      padding: 30px 25px;
      text-align: center;
      box-shadow: 0 20px 60px rgba(0,0,0,0.1);
      transition: all 0.3s ease;
      border: 1px solid rgba(255,255,255,0.2);
      margin-bottom: 0;
      min-height: 280px;
      display: flex;
      flex-direction: column;
      justify-content: space-between;
    }
    
    .feature-card:hover {
      transform: translateY(-10px);
      box-shadow: 0 30px 80px rgba(0,0,0,0.15);
    }
    
    .feature-icon {
      margin-bottom: 20px;
      display: flex;
      justify-content: center;
      align-items: center;
    }
    
    .feature-icon i {
      font-size: 2.5rem;
      color: #667eea;
      display: block;
      margin: 0 auto;
    }
    
    .feature-title {
      font-size: 1.5rem;
      font-weight: 600;
      margin-bottom: 15px;
      color: #333;
      line-height: 1.3;
    }
    
    .feature-description {
      color: #666;
      line-height: 1.6;
      font-size: 1.1rem;
      flex-grow: 1;
    }
    
    /* Modal Styles */
    .modal-content {
      border-radius: 20px;
      border: none;
      box-shadow: 0 20px 60px rgba(0,0,0,0.3);
    }
    
    .modal-header {
      background: linear-gradient(45deg, #667eea, #764ba2);
      color: white;
      border-radius: 20px 20px 0 0;
      border: none;
      padding: 25px 30px;
    }
    
    .modal-title {
      font-weight: 600;
      font-size: 1.3rem;
    }
    
    .close {
      color: white;
      opacity: 0.8;
      font-size: 1.5rem;
    }
    
    .close:hover {
      opacity: 1;
    }
    
    .modal-body {
      padding: 30px;
    }
    
    .form-control {
      border-radius: 10px;
      border: 2px solid #e9ecef;
      padding: 12px 15px;
      font-size: 1rem;
      transition: all 0.3s ease;
    }
    
    .form-control:focus {
      border-color: #667eea;
      box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
    }
    
    .btn-login {
      background: linear-gradient(45deg, #667eea, #764ba2);
      border: none;
      border-radius: 10px;
      padding: 12px 30px;
      font-weight: 600;
      font-size: 1.1rem;
      color: white;
      width: 100%;
      transition: all 0.3s ease;
    }
    
    .btn-login:hover {
      transform: translateY(-2px);
      box-shadow: 0 10px 30px rgba(102, 126, 234, 0.4);
      color: white;
    }
    
    /* Animations */
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
    
    @keyframes float {
      0%, 100% { transform: translateY(0px); }
      50% { transform: translateY(-20px); }
    }
    
    .floating {
      animation: float 6s ease-in-out infinite;
    }
    
    /* Responsive Design */
    @media (max-width: 1200px) {
      .main-layout {
        flex-direction: column;
      }
      
      .hero-section {
        flex: none;
        min-height: 60vh;
      }
      
      .features-section {
        flex: none;
        min-height: 40vh;
      }
    }
    
    @media (max-width: 768px) {
      .main-layout {
        flex-direction: column;
      }
      
      .hero-section {
        padding: 40px 20px;
        min-height: 50vh;
      }
      
      .features-section {
        padding: 30px 20px;
        min-height: 50vh;
      }
      
      .hero-title {
        font-size: 2.5rem;
      }
      
      .hero-subtitle {
        font-size: 1.1rem;
      }
      
      .login-buttons {
        flex-direction: column;
        align-items: center;
        gap: 15px;
      }
      
      .login-btn {
        width: 250px;
        font-size: 1rem;
        padding: 12px 30px;
      }
      
      .features-row {
        grid-template-columns: repeat(2, 1fr);
        gap: 20px;
      }
    }
    
    @media (max-width: 480px) {
      .features-row {
        grid-template-columns: 1fr;
        gap: 15px;
      }
    }
    
    /* Alert Styles */
    .alert {
      border-radius: 10px;
      border: none;
      margin-bottom: 20px;
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
  </style>
</head>
<body>
  <!-- Navigation REMOVED -->

  <!-- Main Layout 50:50 -->
  <div class="main-layout">
    <!-- Hero Section - Left 50% -->
    <section id="home" class="hero-section">
      <div class="hero-content">
        <div class="floating">
          <?php 
          $login_logo = getLoginLogo($app_settings);
          if($login_logo != '../gambar/sistem/user.png'): 
          ?>
            <img src="<?php echo $login_logo; ?>" class="img-fluid mb-4" style="width: 200px; border-radius: 50%; box-shadow: 0 20px 60px rgba(0,0,0,0.3);">
          <?php else: ?>
            <img src="gambar/sistem/logo.png" class="img-fluid mb-4" style="width: 200px; border-radius: 50%; box-shadow: 0 20px 60px rgba(0,0,0,0.3);">
          <?php endif; ?>
        </div>
        
        <h1 class="hero-title"><?php echo getSetting($app_settings, 'app_name', 'SISTEM INFORMASI E-POINT SISWA'); ?></h1>
        <p class="hero-subtitle">Platform Digital Terintegrasi untuk Manajemen Administrasi BK</p>
        <h3 class="school-name"><?php echo getSetting($app_settings, 'app_author', 'AZCO'); ?></h3>
        
        <div class="login-buttons">
          <a href="#" class="login-btn btn-student" data-toggle="modal" data-target="#studentLoginModal">
            <i class="fa fa-user-graduate"></i> Login Siswa
          </a>
          <a href="#" class="login-btn btn-admin" data-toggle="modal" data-target="#adminLoginModal">
            <i class="fa fa-user-shield"></i> Login Admin
          </a>
          <a href="#" class="login-btn btn-guru-bk" data-toggle="modal" data-target="#guruBkLoginModal">
            <i class="fa fa-user-md"></i> Login Guru BK
          </a>
        </div>
      </div>
    </section>

    <!-- Features Section - Right 50% -->
    <section id="features" class="features-section">
      <div class="features-content">
        <div class="features-header">
          <h2 style="font-size: 2.5rem; font-weight: 700; color: #333; margin-bottom: 20px;">Fitur Unggulan</h2>
          <p style="font-size: 1.2rem; color: #666; max-width: 600px; margin: 0 auto;">Sistem terintegrasi yang memudahkan manajemen akademik dan non-akademik siswa</p>
        </div>
      
      <div class="features-row">
        <div class="feature-col">
          <div class="feature-card">
            <div class="feature-icon">
              <i class="fa fa-star"></i>
            </div>
            <h4 class="feature-title">Manajemen Point</h4>
            <p class="feature-description">Sistem tracking point siswa untuk prestasi dan pelanggaran dengan transparansi penuh</p>
          </div>
        </div>
        
        <div class="feature-col">
          <div class="feature-card">
            <div class="feature-icon">
              <i class="fa fa-calendar"></i>
            </div>
            <h4 class="feature-title">Layanan BK</h4>
            <p class="feature-description">Konseling dan bimbingan siswa dengan sistem appointment yang terintegrasi</p>
          </div>
        </div>
        
        <div class="feature-col">
          <div class="feature-card">
            <div class="feature-icon">
              <i class="fa fa-home"></i>
            </div>
            <h4 class="feature-title">Kunjungan Rumah</h4>
            <p class="feature-description">Monitoring dan dokumentasi kunjungan rumah untuk pendampingan siswa</p>
          </div>
        </div>
        
        <div class="feature-col">
          <div class="feature-card">
            <div class="feature-icon">
              <i class="fa fa-exclamation-triangle"></i>
            </div>
            <h4 class="feature-title">Kasus Siswa</h4>
            <p class="feature-description">Manajemen kasus siswa dengan tracking progress dan solusi terstruktur</p>
          </div>
        </div>
        
        <div class="feature-col">
          <div class="feature-card">
            <div class="feature-icon">
              <i class="fa fa-bar-chart"></i>
            </div>
            <h4 class="feature-title">Laporan Real-time</h4>
            <p class="feature-description">Dashboard dan laporan real-time untuk monitoring perkembangan siswa</p>
          </div>
        </div>
        
        <div class="feature-col">
          <div class="feature-card">
            <div class="feature-icon">
              <i class="fa fa-mobile"></i>
            </div>
            <h4 class="feature-title">Mobile Friendly</h4>
            <p class="feature-description">Akses mudah dari berbagai perangkat dengan tampilan responsif</p>
          </div>
        </div>
      </div>
      </div>
    </section>
  </div>

  <!-- Student Login Modal -->
  <div class="modal fade" id="studentLoginModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">
            <i class="fa fa-user-graduate"></i> Login Siswa
          </h5>
          <button type="button" class="close" data-dismiss="modal">
            <span>&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <?php 
          if(isset($_GET['alert'])){
            if($_GET['alert'] == "gagal"){
              echo "<div class='alert alert-danger'><b>LOGIN GAGAL</b><br> Username dan password salah</div>";
            }else if($_GET['alert'] == "logout"){
              echo "<div class='alert alert-success'>Anda telah berhasil logout</div>";
            }else if($_GET['alert'] == "belum_login"){
              echo "<div class='alert alert-warning'>Anda harus login untuk mengakses halaman admin</div>";
            }else if($_GET['alert'] == "access_denied"){
              $pesan = isset($_GET['pesan']) ? $_GET['pesan'] : 'Anda tidak memiliki akses ke halaman ini';
              echo "<div class='alert alert-warning'><b>AKSES DITOLAK</b><br> $pesan</div>";
            }
          }
          ?>
          
          <form action="periksa_login.php" method="POST">
            <div class="form-group">
              <label for="nis">NIS</label>
              <input type="number" class="form-control" id="nis" name="nis" placeholder="Masukkan NIS" required>
            </div>
            <div class="form-group">
              <label for="password">Password</label>
              <input type="password" class="form-control" id="password" name="password" placeholder="Masukkan Password" required>
            </div>
            <button type="submit" class="btn btn-login">
              <i class="fa fa-sign-in-alt"></i> Login
            </button>
          </form>
        </div>
      </div>
    </div>
  </div>

  <!-- Admin Login Modal -->
  <div class="modal fade" id="adminLoginModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">
            <i class="fa fa-user-shield"></i> Login Admin
          </h5>
          <button type="button" class="close" data-dismiss="modal">
            <span>&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <form action="periksa_admin_only.php" method="POST">
            <div class="form-group">
              <label for="username">Username</label>
              <input type="text" class="form-control" id="username" name="username" placeholder="Masukkan Username" required>
            </div>
            <div class="form-group">
              <label for="admin_password">Password</label>
              <input type="password" class="form-control" id="admin_password" name="password" placeholder="Masukkan Password" required>
            </div>
            <button type="submit" class="btn btn-login">
              <i class="fa fa-sign-in-alt"></i> Login
            </button>
          </form>
        </div>
      </div>
    </div>
  </div>

  <!-- Guru BK Login Modal -->
  <div class="modal fade" id="guruBkLoginModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header" style="background: linear-gradient(45deg, #FF9800, #F57C00);">
          <h5 class="modal-title">
            <i class="fa fa-user-md"></i> Login Guru BK
          </h5>
          <button type="button" class="close" data-dismiss="modal">
            <span>&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <?php 
          if(isset($_GET['alert'])){
            if($_GET['alert'] == "gagal"){
              $pesan = isset($_GET['pesan']) ? $_GET['pesan'] : 'Username dan password salah';
              echo "<div class='alert alert-danger'><b>LOGIN GAGAL</b><br> $pesan</div>";
            }else if($_GET['alert'] == "role_salah"){
              $pesan = isset($_GET['pesan']) ? $_GET['pesan'] : 'Anda tidak memiliki akses sebagai guru BK';
              echo "<div class='alert alert-warning'><b>AKSES DITOLAK</b><br> $pesan</div>";
            }
          }
          ?>
          <form action="periksa_guru_bk.php" method="POST">
            <div class="form-group">
              <label for="guru_username">Username</label>
              <input type="text" class="form-control" id="guru_username" name="username" placeholder="Masukkan Username Guru BK" required>
            </div>
            <div class="form-group">
              <label for="guru_password">Password</label>
              <input type="password" class="form-control" id="guru_password" name="password" placeholder="Masukkan Password" required>
            </div>
            <button type="submit" class="btn btn-login" style="background: linear-gradient(45deg, #FF9800, #F57C00);">
              <i class="fa fa-sign-in-alt"></i> Login Guru BK
            </button>
          </form>
          <div class="text-center mt-3">
            <small class="text-muted">
              <i class="fa fa-info-circle"></i> 
              Username dan password diberikan oleh administrator
            </small>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Scripts -->
  <script src="assets/bower_components/jquery/dist/jquery.min.js"></script>
  <script src="assets/bower_components/bootstrap/dist/js/bootstrap.min.js"></script>
  
  <script>
    // Auto-hide alerts after 5 seconds
    setTimeout(function() {
      $('.alert').fadeOut('slow');
    }, 5000);
    
    // Auto-open Guru BK modal if there's an alert
    $(document).ready(function() {
      <?php if(isset($_GET['alert']) && ($_GET['alert'] == 'gagal' || $_GET['alert'] == 'role_salah')): ?>
        $('#guruBkLoginModal').modal('show');
      <?php endif; ?>
    });
    
    // Smooth scrolling for navigation links (if any)
    $('a[href^="#"]').on('click', function(event) {
      var target = $(this.getAttribute('href'));
      if( target.length ) {
        event.preventDefault();
        $('html, body').stop().animate({
          scrollTop: target.offset().top
        }, 1000);
      }
    });
  </script>
  
  <!-- ePoint Chatbot Widget -->
  <?php include 'chatbot_widget.php'; ?>
</body>
</html>
