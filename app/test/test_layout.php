<?php
// Test Layout untuk memastikan semua card tampil
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
  <title>Test Layout - ePoint</title>
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
    
    .test-info {
      background: rgba(255, 255, 255, 0.1);
      padding: 20px;
      border-radius: 15px;
      margin: 20px auto;
      max-width: 600px;
      color: white;
    }
  </style>
</head>
<body>
  <div class="test-header">
    <h1>🧪 Test Layout ePoint</h1>
    <p>Memastikan semua card fitur tampil dengan baik</p>
  </div>
  
  <div class="test-info">
    <h3>📋 Test Information</h3>
    <p><strong>Screen Size:</strong> <span id="screen-size"></span></p>
    <p><strong>Grid Layout:</strong> <span id="grid-info"></span></p>
    <p><strong>Cards Visible:</strong> <span id="cards-visible"></span></p>
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
    // Update test information
    function updateTestInfo() {
      const width = window.innerWidth;
      const height = window.innerHeight;
      
      document.getElementById('screen-size').textContent = `${width}x${height}`;
      
      // Determine grid layout
      let gridInfo = '';
      if (width >= 1200) {
        gridInfo = '3 columns (Desktop)';
      } else if (width >= 768) {
        gridInfo = '2 columns (Tablet)';
      } else {
        gridInfo = '1 column (Mobile)';
      }
      document.getElementById('grid-info').textContent = gridInfo;
      
      // Count visible cards
      const cards = document.querySelectorAll('.feature-card');
      const visibleCards = Array.from(cards).filter(card => {
        const rect = card.getBoundingClientRect();
        return rect.top < window.innerHeight && rect.bottom > 0;
      });
      
      document.getElementById('cards-visible').textContent = `${visibleCards.length}/${cards.length}`;
    }
    
    // Update on load and resize
    window.addEventListener('load', updateTestInfo);
    window.addEventListener('resize', updateTestInfo);
    
    // Add visual indicators
    document.addEventListener('DOMContentLoaded', function() {
      const cards = document.querySelectorAll('.feature-card');
      cards.forEach((card, index) => {
        // Add border to show card boundaries
        card.style.border = '2px solid rgba(102, 126, 234, 0.3)';
        
        // Add number indicator
        const indicator = document.createElement('div');
        indicator.style.cssText = `
          position: absolute;
          top: 10px;
          right: 10px;
          background: #667eea;
          color: white;
          width: 25px;
          height: 25px;
          border-radius: 50%;
          display: flex;
          align-items: center;
          justify-content: center;
          font-size: 12px;
          font-weight: bold;
        `;
        indicator.textContent = index + 1;
        card.style.position = 'relative';
        card.appendChild(indicator);
      });
    });
  </script>
</body>
</html>
