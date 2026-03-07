<?php
// Test Font Size untuk memastikan tulisan seimbang
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
  <title>Test Font Size - ePoint</title>
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
    }
    
    .test-header {
      text-align: center;
      padding: 40px 20px;
      color: white;
    }
    
    .test-header h1 {
      font-size: 2.5rem;
      margin-bottom: 10px;
    }
    
    .font-info {
      background: rgba(255, 255, 255, 0.1);
      padding: 20px;
      border-radius: 15px;
      margin: 20px auto;
      max-width: 800px;
      color: white;
    }
    
    .font-comparison {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
      gap: 20px;
      margin: 30px 0;
    }
    
    .font-sample {
      background: white;
      padding: 20px;
      border-radius: 10px;
      text-align: center;
    }
    
    .font-sample h4 {
      color: #333;
      margin-bottom: 10px;
    }
    
    .font-sample p {
      color: #666;
      margin: 0;
    }
  </style>
</head>
<body>
  <div class="test-header">
    <h1>🔍 Test Font Size ePoint</h1>
    <p>Memastikan ukuran font seimbang untuk semua layar</p>
  </div>
  
  <div class="font-info">
    <h3>📊 Font Size Information</h3>
    <p><strong>Screen Size:</strong> <span id="screen-size"></span></p>
    <p><strong>Device Type:</strong> <span id="device-type"></span></p>
    <p><strong>Recommended Font:</strong> <span id="recommended-font"></span></p>
  </div>

  <div class="font-comparison">
    <div class="font-sample">
      <h4 style="font-size: 1.2rem;">Title Kecil (1.2rem)</h4>
      <p style="font-size: 0.9rem;">Deskripsi kecil - sulit dibaca di layar besar</p>
    </div>
    
    <div class="font-sample">
      <h4 style="font-size: 1.5rem;">Title Normal (1.5rem)</h4>
      <p style="font-size: 1.1rem;">Deskripsi normal - seimbang untuk semua layar</p>
    </div>
    
    <div class="font-sample">
      <h4 style="font-size: 1.7rem;">Title Besar (1.7rem)</h4>
      <p style="font-size: 1.2rem;">Deskripsi besar - optimal untuk layar besar</p>
    </div>
  </div>

  <!-- Features Section -->
  <section id="features" class="features-section">
    <div class="container">
      <div class="row text-center mb-5">
        <div class="col-12">
          <h2 style="font-size: 2.5rem; font-weight: 700; color: #333; margin-bottom: 20px;">Fitur Unggulan</h2>
          <p style="font-size: 1.2rem; color: #666; max-width: 600px; margin: 0 auto;">Sistem terintegrasi yang memudahkan manajemen akademik dan non-akademik siswa</p>
        </div>
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

  <script src="assets/bower_components/jquery/dist/jquery.min.js"></script>
  <script src="assets/bower_components/bootstrap/dist/js/bootstrap.min.js"></script>
  
  <script>
    // Update font information
    function updateFontInfo() {
      const width = window.innerWidth;
      const height = window.innerHeight;
      
      document.getElementById('screen-size').textContent = `${width}x${height}`;
      
      // Determine device type
      let deviceType = '';
      let recommendedFont = '';
      
      if (width >= 1400) {
        deviceType = 'Large Desktop (1400px+)';
        recommendedFont = 'Title: 1.7rem, Description: 1.2rem';
      } else if (width >= 1200) {
        deviceType = 'Desktop (1200px-1399px)';
        recommendedFont = 'Title: 1.5rem, Description: 1.1rem';
      } else if (width >= 768) {
        deviceType = 'Tablet (768px-1199px)';
        recommendedFont = 'Title: 1.3rem, Description: 1.0rem';
      } else {
        deviceType = 'Mobile (<768px)';
        recommendedFont = 'Title: 1.2rem, Description: 0.9rem';
      }
      
      document.getElementById('device-type').textContent = deviceType;
      document.getElementById('recommended-font').textContent = recommendedFont;
    }
    
    // Update on load and resize
    window.addEventListener('load', updateFontInfo);
    window.addEventListener('resize', updateFontInfo);
    
    // Add visual indicators for font sizes
    document.addEventListener('DOMContentLoaded', function() {
      const cards = document.querySelectorAll('.feature-card');
      cards.forEach((card, index) => {
        // Add font size indicator
        const indicator = document.createElement('div');
        indicator.style.cssText = `
          position: absolute;
          bottom: 10px;
          left: 10px;
          background: rgba(102, 126, 234, 0.8);
          color: white;
          padding: 2px 6px;
          border-radius: 3px;
          font-size: 10px;
          font-weight: bold;
        `;
        
        const title = card.querySelector('.feature-title');
        const description = card.querySelector('.feature-description');
        const titleSize = window.getComputedStyle(title).fontSize;
        const descSize = window.getComputedStyle(description).fontSize;
        
        indicator.textContent = `T:${titleSize} D:${descSize}`;
        card.style.position = 'relative';
        card.appendChild(indicator);
      });
    });
  </script>
</body>
</html>
