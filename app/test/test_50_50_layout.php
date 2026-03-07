<?php
// Test 50:50 Layout untuk ePoint
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
  <title>Test 50:50 Layout - ePoint</title>
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  
  <!-- Bootstrap & Font Awesome -->
  <link rel="stylesheet" href="assets/bower_components/bootstrap/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="assets/bower_components/font-awesome/css/font-awesome.min.css">
  
  <!-- Features Fix CSS -->
  <link rel="stylesheet" href="assets/css/features-fix.css">
  
  <!-- Google Fonts -->
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  
  <style>
    body {
      font-family: 'Poppins', sans-serif;
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      min-height: 100vh;
      margin: 0;
      padding: 0;
      overflow-x: hidden;
    }
    
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
    }
    
    .hero-subtitle {
      font-size: 1.3rem;
      font-weight: 300;
      margin-bottom: 40px;
      opacity: 0.9;
    }
    
    .school-name {
      font-size: 1.5rem;
      font-weight: 600;
      margin-bottom: 50px;
      color: #ffd700;
    }
    
    /* Login Buttons */
    .login-buttons {
      display: flex;
      justify-content: center;
      gap: 20px;
      margin-top: 40px;
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
      transform: translateY(-2px);
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
      width: 100%;
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
      min-height: 60px;
    }
    
    .feature-icon i {
      font-size: 3rem;
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
      min-height: 2.6rem;
      display: flex;
      align-items: center;
      justify-content: center;
    }
    
    .feature-description {
      color: #666;
      line-height: 1.6;
      font-size: 1.1rem;
      flex-grow: 1;
      display: flex;
      align-items: center;
      justify-content: center;
      text-align: center;
    }
    
    /* Layout Info */
    .layout-info {
      position: fixed;
      top: 20px;
      right: 20px;
      background: rgba(0, 0, 0, 0.8);
      color: white;
      padding: 15px;
      border-radius: 10px;
      font-size: 14px;
      z-index: 1000;
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
  </style>
</head>
<body>
  <!-- Layout Info -->
  <div class="layout-info">
    <div><strong>Layout:</strong> <span id="layout-type">50:50</span></div>
    <div><strong>Screen:</strong> <span id="screen-size"></span></div>
    <div><strong>Left:</strong> <span id="left-width">50%</span></div>
    <div><strong>Right:</strong> <span id="right-width">50%</span></div>
  </div>

  <!-- Main Layout 50:50 -->
  <div class="main-layout">
    <!-- Hero Section - Left 50% -->
    <section id="home" class="hero-section">
      <div class="hero-content">
        <div class="floating">
          <img src="gambar/sistem/logo.png" class="img-fluid mb-4" style="width: 200px; border-radius: 50%; box-shadow: 0 20px 60px rgba(0,0,0,0.3);">
        </div>
        
        <h1 class="hero-title">SISBK</h1>
        <p class="hero-subtitle">Platform Digital Terintegrasi untuk Manajemen Point Siswa</p>
        <h3 class="school-name">Madrasah Aliyah Yasmu</h3>
        
        <div class="login-buttons">
          <a href="#" class="login-btn btn-student">
            <i class="fa fa-user-graduate"></i> Login Siswa
          </a>
          <a href="#" class="login-btn btn-admin">
            <i class="fa fa-user-shield"></i> Login Admin
          </a>
          <a href="#" class="login-btn btn-guru-bk">
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

  <script src="assets/bower_components/jquery/dist/jquery.min.js"></script>
  <script src="assets/bower_components/bootstrap/dist/js/bootstrap.min.js"></script>
  
  <script>
    // Update layout information
    function updateLayoutInfo() {
      const width = window.innerWidth;
      const height = window.innerHeight;
      
      document.getElementById('screen-size').textContent = `${width}x${height}`;
      
      // Determine layout type
      let layoutType = '';
      let leftWidth = '';
      let rightWidth = '';
      
      if (width >= 1200) {
        layoutType = '50:50 (Side by Side)';
        leftWidth = '50%';
        rightWidth = '50%';
      } else {
        layoutType = 'Stacked (Vertical)';
        leftWidth = '100%';
        rightWidth = '100%';
      }
      
      document.getElementById('layout-type').textContent = layoutType;
      document.getElementById('left-width').textContent = leftWidth;
      document.getElementById('right-width').textContent = rightWidth;
    }
    
    // Update on load and resize
    window.addEventListener('load', updateLayoutInfo);
    window.addEventListener('resize', updateLayoutInfo);
    
    // Add visual indicators
    document.addEventListener('DOMContentLoaded', function() {
      // Add border to show sections
      const heroSection = document.querySelector('.hero-section');
      const featuresSection = document.querySelector('.features-section');
      
      if (heroSection) {
        heroSection.style.border = '3px solid rgba(255, 255, 255, 0.3)';
      }
      
      if (featuresSection) {
        featuresSection.style.border = '3px solid rgba(102, 126, 234, 0.3)';
      }
    });
  </script>
</body>
</html>
