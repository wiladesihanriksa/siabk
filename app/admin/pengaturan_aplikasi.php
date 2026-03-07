<?php
session_start();

// Check if user is logged in and is administrator
if(!isset($_SESSION['level']) || $_SESSION['level'] != "administrator"){
    header("location:../index.php?alert=access_denied&pesan=Hanya administrator yang dapat mengakses halaman ini");
    exit();
}

include 'header.php';
include '../koneksi.php';

// Ambil semua pengaturan
$settings = array();
$query = mysqli_query($koneksi, "SELECT * FROM app_settings ORDER BY setting_key");
while($row = mysqli_fetch_assoc($query)) {
    $settings[$row['setting_key']] = $row;
}

// Ambil pengaturan warna
$color_settings = getColorSettings($koneksi);

// Proses update pengaturan
if(isset($_POST['update_settings'])) {
    foreach($_POST as $key => $value) {
        if($key != 'update_settings' && strpos($key, 'setting_') === 0) {
            $setting_key = str_replace('setting_', '', $key);
            $update_query = "UPDATE app_settings SET setting_value = ? WHERE setting_key = ?";
            $stmt = mysqli_prepare($koneksi, $update_query);
            mysqli_stmt_bind_param($stmt, "ss", $value, $setting_key);
            mysqli_stmt_execute($stmt);
        }
    }
    
    // Handle chatbot quick actions
    $quick_actions = array();
    $app_name = $_POST['setting_app_name'] ?? 'ePoint'; // Ambil nama aplikasi dari form
    
    for($i = 0; $i < 6; $i++) {
        if(!empty($_POST['quick_action_' . $i])) {
            $action = $_POST['quick_action_' . $i];
            // Ganti placeholder {APP_NAME} dengan nama aplikasi sebenarnya
            $action = str_replace('{APP_NAME}', $app_name, $action);
            // Ganti hardcoded "ePoint" dengan nama aplikasi sebenarnya
            $action = str_replace('ePoint', $app_name, $action);
            $quick_actions[] = $action;
        }
    }
    if(!empty($quick_actions)) {
        $quick_actions_json = json_encode($quick_actions);
        $update_query = "UPDATE app_settings SET setting_value = ? WHERE setting_key = 'chatbot_quick_actions'";
        $stmt = mysqli_prepare($koneksi, $update_query);
        mysqli_stmt_bind_param($stmt, "s", $quick_actions_json);
        mysqli_stmt_execute($stmt);
    }
    
    // Handle chatbot FAQ
    $faq = array();
    for($i = 0; $i < 5; $i++) {
        if(!empty($_POST['faq_question_' . $i]) && !empty($_POST['faq_answer_' . $i])) {
            $question = $_POST['faq_question_' . $i];
            $answer = $_POST['faq_answer_' . $i];
            
            // Ganti placeholder {APP_NAME} dengan nama aplikasi sebenarnya
            $question = str_replace('{APP_NAME}', $app_name, $question);
            $answer = str_replace('{APP_NAME}', $app_name, $answer);
            
            // Ganti hardcoded "ePoint" dengan nama aplikasi sebenarnya
            $question = str_replace('ePoint', $app_name, $question);
            $answer = str_replace('ePoint', $app_name, $answer);
            
            $faq[] = array(
                'question' => $question,
                'answer' => $answer
            );
        }
    }
    if(!empty($faq)) {
        $faq_json = json_encode($faq);
        $update_query = "UPDATE app_settings SET setting_value = ? WHERE setting_key = 'chatbot_faq'";
        $stmt = mysqli_prepare($koneksi, $update_query);
        mysqli_stmt_bind_param($stmt, "s", $faq_json);
        mysqli_stmt_execute($stmt);
    }
    
    // Handle chatbot cloud AI settings
    $chatbot_ai_provider = $_POST['setting_chatbot_ai_provider'] ?? 'gemini';
    $chatbot_api_key = $_POST['setting_chatbot_api_key'] ?? '';
    $chatbot_cloud_enabled = isset($_POST['setting_chatbot_cloud_enabled']) ? '1' : '0';
    $chatbot_fallback_enabled = isset($_POST['setting_chatbot_fallback_enabled']) ? '1' : '0';
    
    // Update chatbot AI settings
    $ai_settings = [
        'chatbot_ai_provider' => $chatbot_ai_provider,
        'chatbot_api_key' => $chatbot_api_key,
        'chatbot_cloud_enabled' => $chatbot_cloud_enabled,
        'chatbot_fallback_enabled' => $chatbot_fallback_enabled
    ];
    
    foreach($ai_settings as $key => $value) {
        $update_query = "UPDATE app_settings SET setting_value = ? WHERE setting_key = ?";
        $stmt = mysqli_prepare($koneksi, $update_query);
        mysqli_stmt_bind_param($stmt, "ss", $value, $key);
        mysqli_stmt_execute($stmt);
    }
    
    // Handle file uploads
    $upload_dir = '../gambar/sistem/';
    if (!file_exists($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }
    
    // Upload logo aplikasi
    if(isset($_FILES['app_logo']) && $_FILES['app_logo']['error'] == 0) {
        $logo_file = $upload_dir . 'logo.png';
        if(move_uploaded_file($_FILES['app_logo']['tmp_name'], $logo_file)) {
            $update_query = "UPDATE app_settings SET setting_value = 'logo.png' WHERE setting_key = 'app_logo'";
            mysqli_query($koneksi, $update_query);
        }
    }
    
    // Upload favicon (mendukung .ico, .png, .jpg, .jpeg)
    if(isset($_FILES['app_favicon']) && $_FILES['app_favicon']['error'] == 0) {
        $ext = strtolower(pathinfo($_FILES['app_favicon']['name'], PATHINFO_EXTENSION));
        $allowed_ext = ['ico', 'png', 'jpg', 'jpeg'];

        if(in_array($ext, $allowed_ext)) {
            // Simpan dengan nama favicon.<ext> di folder gambar/sistem
            $favicon_name = 'favicon.' . $ext;
            $favicon_file = $upload_dir . $favicon_name;

            if(move_uploaded_file($_FILES['app_favicon']['tmp_name'], $favicon_file)) {
                $update_query = "UPDATE app_settings SET setting_value = '" . mysqli_real_escape_string($koneksi, $favicon_name) . "' WHERE setting_key = 'app_favicon'";
                mysqli_query($koneksi, $update_query);
            }
        }
    }
    
    // Upload logo login
    if(isset($_FILES['login_logo']) && $_FILES['login_logo']['error'] == 0) {
        $login_logo_file = $upload_dir . 'login_logo.png';
        if(move_uploaded_file($_FILES['login_logo']['tmp_name'], $login_logo_file)) {
            $update_query = "UPDATE app_settings SET setting_value = 'login_logo.png' WHERE setting_key = 'login_logo'";
            mysqli_query($koneksi, $update_query);
        }
    }
    
    echo "<script>alert('Pengaturan berhasil diperbarui!'); window.location='pengaturan_aplikasi.php';</script>";
}

// Reload settings after update
$settings = array();
$query = mysqli_query($koneksi, "SELECT * FROM app_settings ORDER BY setting_key");
while($row = mysqli_fetch_assoc($query)) {
    $settings[$row['setting_key']] = $row;
}
?>

<div class="content-wrapper">
  <section class="content-header">
    <h1>
      Pengaturan Aplikasi
      <small>Kelola pengaturan sistem</small>
    </h1>
    <ol class="breadcrumb">
      <li><a href="index.php"><i class="fa fa-dashboard"></i> Home</a></li>
      <li class="active">Pengaturan Aplikasi</li>
    </ol>
  </section>

  <section class="content">
    <div class="row">
      <div class="col-md-12">
        <div class="box box-primary">
          <div class="box-header with-border">
            <h3 class="box-title">Pengaturan Aplikasi</h3>
          </div>
          
          <!-- Nav tabs -->
          <ul class="nav nav-tabs" role="tablist">
            <li role="presentation" class="active">
              <a href="#tab-info" aria-controls="tab-info" role="tab" data-toggle="tab">
                <i class="fa fa-info-circle"></i> Informasi Aplikasi
              </a>
            </li>
            <li role="presentation">
              <a href="#tab-colors" aria-controls="tab-colors" role="tab" data-toggle="tab">
                <i class="fa fa-paint-brush"></i> Pengaturan Warna
              </a>
            </li>
            <li role="presentation">
              <a href="#tab-files" aria-controls="tab-files" role="tab" data-toggle="tab">
                <i class="fa fa-image"></i> Upload File
              </a>
            </li>
            <li role="presentation">
              <a href="#tab-chatbot" aria-controls="tab-chatbot" role="tab" data-toggle="tab">
                <i class="fa fa-robot"></i> Pengaturan Chatbot
              </a>
            </li>
          </ul>

          <form method="POST" enctype="multipart/form-data">
            <div class="tab-content">
              <!-- Tab Informasi Aplikasi -->
              <div role="tabpanel" class="tab-pane active" id="tab-info">
                <div class="box-body">
              
              <!-- Informasi Dasar -->
              <div class="row">
                <div class="col-md-6">
                  <h4><i class="fa fa-info-circle"></i> Informasi Dasar</h4>
                  
                  <div class="form-group">
                    <label>Nama Aplikasi</label>
                    <input type="text" class="form-control" name="setting_app_name" 
                           value="<?php echo htmlspecialchars($settings['app_name']['setting_value'] ?? ''); ?>" required>
                  </div>
                  
                  <div class="form-group">
                    <label>Deskripsi Aplikasi</label>
                    <textarea class="form-control" name="setting_app_description" rows="3"><?php echo htmlspecialchars($settings['app_description']['setting_value'] ?? ''); ?></textarea>
                  </div>
                  
                  <div class="form-group">
                    <label>Versi Aplikasi</label>
                    <input type="text" class="form-control" name="setting_app_version" 
                           value="<?php echo htmlspecialchars($settings['app_version']['setting_value'] ?? ''); ?>">
                  </div>
                  
                  <div class="form-group">
                    <label>Nama Institusi</label>
                    <input type="text" class="form-control" name="setting_app_author" 
                           value="<?php echo htmlspecialchars($settings['app_author']['setting_value'] ?? ''); ?>">
                  </div>
                </div>
                
                <div class="col-md-6">
                  <h4><i class="fa fa-envelope"></i> Informasi Kontak</h4>
                  
                  <div class="form-group">
                    <label>Email Kontak</label>
                    <input type="email" class="form-control" name="setting_app_email" 
                           value="<?php echo htmlspecialchars($settings['app_email']['setting_value'] ?? ''); ?>">
                  </div>
                  
                  <div class="form-group">
                    <label>Nomor Telepon</label>
                    <input type="text" class="form-control" name="setting_app_phone" 
                           value="<?php echo htmlspecialchars($settings['app_phone']['setting_value'] ?? ''); ?>">
                  </div>
                  
                  <div class="form-group">
                    <label>Alamat Institusi</label>
                    <textarea class="form-control" name="setting_app_address" rows="3"><?php echo htmlspecialchars($settings['app_address']['setting_value'] ?? ''); ?></textarea>
                  </div>
                </div>
              </div>
                </div>
              </div>
              
              <!-- Tab Pengaturan Warna -->
              <div role="tabpanel" class="tab-pane" id="tab-colors">
                <div class="box-body">
                  <div class="row">
                    <div class="col-md-6">
                      <h4><i class="fa fa-paint-brush"></i> Warna Utama</h4>
                      
                      <div class="form-group">
                        <label>Warna Primary</label>
                        <div class="input-group">
                          <input type="color" class="form-control" name="setting_primary_color" 
                                 value="<?php echo getColor($color_settings, 'primary_color', '#3c8dbc'); ?>" 
                                 style="width: 50px; height: 34px;">
                          <input type="text" class="form-control" name="setting_primary_color_text" 
                                 value="<?php echo getColor($color_settings, 'primary_color', '#3c8dbc'); ?>" 
                                 placeholder="#3c8dbc">
                        </div>
                        <p class="help-block">Warna untuk header, button utama, dan elemen primary</p>
                      </div>
                      
                      <div class="form-group">
                        <label>Warna Secondary</label>
                        <div class="input-group">
                          <input type="color" class="form-control" name="setting_secondary_color" 
                                 value="<?php echo getColor($color_settings, 'secondary_color', '#f39c12'); ?>" 
                                 style="width: 50px; height: 34px;">
                          <input type="text" class="form-control" name="setting_secondary_color_text" 
                                 value="<?php echo getColor($color_settings, 'secondary_color', '#f39c12'); ?>" 
                                 placeholder="#f39c12">
                        </div>
                        <p class="help-block">Warna untuk elemen secondary dan accent</p>
                      </div>
                      
                      <div class="form-group">
                        <label>Warna Success</label>
                        <div class="input-group">
                          <input type="color" class="form-control" name="setting_success_color" 
                                 value="<?php echo getColor($color_settings, 'success_color', '#00a65a'); ?>" 
                                 style="width: 50px; height: 34px;">
                          <input type="text" class="form-control" name="setting_success_color_text" 
                                 value="<?php echo getColor($color_settings, 'success_color', '#00a65a'); ?>" 
                                 placeholder="#00a65a">
                        </div>
                        <p class="help-block">Warna untuk status sukses dan konfirmasi</p>
                      </div>
                      
                      <div class="form-group">
                        <label>Warna Warning</label>
                        <div class="input-group">
                          <input type="color" class="form-control" name="setting_warning_color" 
                                 value="<?php echo getColor($color_settings, 'warning_color', '#f39c12'); ?>" 
                                 style="width: 50px; height: 34px;">
                          <input type="text" class="form-control" name="setting_warning_color_text" 
                                 value="<?php echo getColor($color_settings, 'warning_color', '#f39c12'); ?>" 
                                 placeholder="#f39c12">
                        </div>
                        <p class="help-block">Warna untuk peringatan dan alert</p>
                      </div>
                    </div>
                    
                    <div class="col-md-6">
                      <h4><i class="fa fa-palette"></i> Warna Tambahan</h4>
                      
                      <div class="form-group">
                        <label>Warna Danger</label>
                        <div class="input-group">
                          <input type="color" class="form-control" name="setting_danger_color" 
                                 value="<?php echo getColor($color_settings, 'danger_color', '#dd4b39'); ?>" 
                                 style="width: 50px; height: 34px;">
                          <input type="text" class="form-control" name="setting_danger_color_text" 
                                 value="<?php echo getColor($color_settings, 'danger_color', '#dd4b39'); ?>" 
                                 placeholder="#dd4b39">
                        </div>
                        <p class="help-block">Warna untuk error dan bahaya</p>
                      </div>
                      
                      <div class="form-group">
                        <label>Warna Info</label>
                        <div class="input-group">
                          <input type="color" class="form-control" name="setting_info_color" 
                                 value="<?php echo getColor($color_settings, 'info_color', '#3c8dbc'); ?>" 
                                 style="width: 50px; height: 34px;">
                          <input type="text" class="form-control" name="setting_info_color_text" 
                                 value="<?php echo getColor($color_settings, 'info_color', '#3c8dbc'); ?>" 
                                 placeholder="#3c8dbc">
                        </div>
                        <p class="help-block">Warna untuk informasi dan notifikasi</p>
                      </div>
                      
                      <div class="form-group">
                        <label>Warna Sidebar</label>
                        <div class="input-group">
                          <input type="color" class="form-control" name="setting_sidebar_color" 
                                 value="<?php echo getColor($color_settings, 'sidebar_color', '#222d32'); ?>" 
                                 style="width: 50px; height: 34px;">
                          <input type="text" class="form-control" name="setting_sidebar_color_text" 
                                 value="<?php echo getColor($color_settings, 'sidebar_color', '#222d32'); ?>" 
                                 placeholder="#222d32">
                        </div>
                        <p class="help-block">Warna background sidebar</p>
                      </div>
                      
                      <div class="form-group">
                        <label>Warna Text</label>
                        <div class="input-group">
                          <input type="color" class="form-control" name="setting_text_color" 
                                 value="<?php echo getColor($color_settings, 'text_color', '#333333'); ?>" 
                                 style="width: 50px; height: 34px;">
                          <input type="text" class="form-control" name="setting_text_color_text" 
                                 value="<?php echo getColor($color_settings, 'text_color', '#333333'); ?>" 
                                 placeholder="#333333">
                        </div>
                        <p class="help-block">Warna text utama</p>
                      </div>
                    </div>
                  </div>
                  
                  <div class="row">
                    <div class="col-md-12">
                      <h4><i class="fa fa-eye"></i> Preview Warna</h4>
                      <div class="row">
                        <div class="col-md-2">
                          <div class="color-preview" style="background-color: <?php echo getColor($color_settings, 'primary_color', '#3c8dbc'); ?>; height: 50px; border-radius: 5px; margin: 5px;">
                            <div class="text-center" style="color: white; padding-top: 15px; font-weight: bold;">Primary</div>
                          </div>
                        </div>
                        <div class="col-md-2">
                          <div class="color-preview" style="background-color: <?php echo getColor($color_settings, 'success_color', '#00a65a'); ?>; height: 50px; border-radius: 5px; margin: 5px;">
                            <div class="text-center" style="color: white; padding-top: 15px; font-weight: bold;">Success</div>
                          </div>
                        </div>
                        <div class="col-md-2">
                          <div class="color-preview" style="background-color: <?php echo getColor($color_settings, 'warning_color', '#f39c12'); ?>; height: 50px; border-radius: 5px; margin: 5px;">
                            <div class="text-center" style="color: white; padding-top: 15px; font-weight: bold;">Warning</div>
                          </div>
                        </div>
                        <div class="col-md-2">
                          <div class="color-preview" style="background-color: <?php echo getColor($color_settings, 'danger_color', '#dd4b39'); ?>; height: 50px; border-radius: 5px; margin: 5px;">
                            <div class="text-center" style="color: white; padding-top: 15px; font-weight: bold;">Danger</div>
                          </div>
                        </div>
                        <div class="col-md-2">
                          <div class="color-preview" style="background-color: <?php echo getColor($color_settings, 'info_color', '#3c8dbc'); ?>; height: 50px; border-radius: 5px; margin: 5px;">
                            <div class="text-center" style="color: white; padding-top: 15px; font-weight: bold;">Info</div>
                          </div>
                        </div>
                        <div class="col-md-2">
                          <div class="color-preview" style="background-color: <?php echo getColor($color_settings, 'secondary_color', '#f39c12'); ?>; height: 50px; border-radius: 5px; margin: 5px;">
                            <div class="text-center" style="color: white; padding-top: 15px; font-weight: bold;">Secondary</div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              
              <!-- Tab Upload File -->
              <div role="tabpanel" class="tab-pane" id="tab-files">
                <div class="box-body">
                  <!-- Upload File -->
                  <div class="row">
                <div class="col-md-4">
                  <h4><i class="fa fa-image"></i> Logo Aplikasi</h4>
                  <div class="form-group">
                    <label>Logo Header (PNG, maks 2MB)</label>
                    <input type="file" class="form-control" name="app_logo" accept="image/png,image/jpeg">
                    <?php if(!empty($settings['app_logo']['setting_value'])): ?>
                      <p class="help-block">
                        Logo saat ini: 
                        <a href="../gambar/sistem/<?php echo $settings['app_logo']['setting_value']; ?>" target="_blank">
                          <?php echo $settings['app_logo']['setting_value']; ?>
                        </a>
                      </p>
                    <?php endif; ?>
                  </div>
                </div>
                
                <div class="col-md-4">
                  <h4><i class="fa fa-star"></i> Logo Login</h4>
                  <div class="form-group">
                    <label>Logo Halaman Login (PNG, maks 2MB)</label>
                    <input type="file" class="form-control" name="login_logo" accept="image/png,image/jpeg">
                    <?php if(!empty($settings['login_logo']['setting_value'])): ?>
                      <p class="help-block">
                        Logo login saat ini: 
                        <a href="../gambar/sistem/<?php echo $settings['login_logo']['setting_value']; ?>" target="_blank">
                          <?php echo $settings['login_logo']['setting_value']; ?>
                        </a>
                      </p>
                    <?php endif; ?>
                  </div>
                </div>
                
                <div class="col-md-4">
                  <h4><i class="fa fa-favicon"></i> Favicon</h4>
                  <div class="form-group">
                    <label>Favicon (ICO, maks 1MB)</label>
                    <input type="file" class="form-control" name="app_favicon" accept=".ico,image/png,image/jpeg,image/jpg">
                    <?php if(!empty($settings['app_favicon']['setting_value'])): ?>
                      <p class="help-block">
                        Favicon saat ini: 
                        <a href="../gambar/sistem/<?php echo $settings['app_favicon']['setting_value']; ?>" target="_blank">
                          <?php echo $settings['app_favicon']['setting_value']; ?>
                        </a>
                      </p>
                    <?php endif; ?>
                  </div>
                </div>
              </div>
              
                </div>
              </div>
              
              <!-- Tab Pengaturan Chatbot -->
              <div role="tabpanel" class="tab-pane" id="tab-chatbot">
                <div class="box-body">
                  <div class="row">
                    <div class="col-md-12">
                      <h4><i class="fa fa-robot"></i> Pengaturan Chatbot</h4>
                      <p class="text-muted">Konfigurasi chatbot untuk membantu pengguna dengan informasi aplikasi.</p>
                    </div>
                  </div>
                  
                  <div class="row">
                    <div class="col-md-6">
                      <h5><i class="fa fa-cog"></i> Pengaturan Dasar</h5>
                      
                      <div class="form-group">
                        <label>Nama Chatbot</label>
                        <input type="text" class="form-control" name="setting_chatbot_name" 
                               value="<?php echo htmlspecialchars($settings['chatbot_name']['setting_value'] ?? 'ePoint Assistant'); ?>">
                        <small class="text-muted">Nama yang akan ditampilkan di widget chatbot</small>
                      </div>
                      
                      <div class="form-group">
                        <label>Deskripsi Chatbot</label>
                        <textarea class="form-control" name="setting_chatbot_description" rows="3"><?php echo htmlspecialchars($settings['chatbot_description']['setting_value'] ?? 'Saya siap membantu Anda dengan informasi tentang aplikasi ePoint dan layanan konseling.'); ?></textarea>
                        <small class="text-muted">Deskripsi singkat tentang fungsi chatbot</small>
                      </div>
                      
                      <div class="form-group">
                        <label>Status Chatbot</label>
                        <select class="form-control" name="setting_chatbot_status">
                          <option value="Online" <?php echo ($settings['chatbot_status']['setting_value'] ?? 'Online') == 'Online' ? 'selected' : ''; ?>>Online</option>
                          <option value="Busy" <?php echo ($settings['chatbot_status']['setting_value'] ?? '') == 'Busy' ? 'selected' : ''; ?>>Busy</option>
                          <option value="Away" <?php echo ($settings['chatbot_status']['setting_value'] ?? '') == 'Away' ? 'selected' : ''; ?>>Away</option>
                        </select>
                      </div>
                      
                      <div class="form-group">
                        <label>Avatar Chatbot</label>
                        <select class="form-control" name="setting_chatbot_avatar">
                          <option value="fas fa-robot" <?php echo ($settings['chatbot_avatar']['setting_value'] ?? 'fas fa-robot') == 'fas fa-robot' ? 'selected' : ''; ?>>🤖 Robot</option>
                          <option value="fas fa-user-tie" <?php echo ($settings['chatbot_avatar']['setting_value'] ?? '') == 'fas fa-user-tie' ? 'selected' : ''; ?>>👔 Professional</option>
                          <option value="fas fa-graduation-cap" <?php echo ($settings['chatbot_avatar']['setting_value'] ?? '') == 'fas fa-graduation-cap' ? 'selected' : ''; ?>>🎓 Education</option>
                          <option value="fas fa-heart" <?php echo ($settings['chatbot_avatar']['setting_value'] ?? '') == 'fas fa-heart' ? 'selected' : ''; ?>>❤️ Friendly</option>
                        </select>
                      </div>
                    </div>
                    
                    <div class="col-md-6">
                      <h5><i class="fa fa-paint-brush"></i> Tampilan & Posisi</h5>
                      
                      <div class="form-group">
                        <label>Tema Chatbot</label>
                        <select class="form-control" name="setting_chatbot_theme">
                          <option value="modern" <?php echo ($settings['chatbot_theme']['setting_value'] ?? 'modern') == 'modern' ? 'selected' : ''; ?>>Modern</option>
                          <option value="classic" <?php echo ($settings['chatbot_theme']['setting_value'] ?? '') == 'classic' ? 'selected' : ''; ?>>Classic</option>
                          <option value="minimal" <?php echo ($settings['chatbot_theme']['setting_value'] ?? '') == 'minimal' ? 'selected' : ''; ?>>Minimal</option>
                        </select>
                      </div>
                      
                      <div class="form-group">
                        <label>Posisi Widget</label>
                        <select class="form-control" name="setting_chatbot_position">
                          <option value="bottom-right" <?php echo ($settings['chatbot_position']['setting_value'] ?? 'bottom-right') == 'bottom-right' ? 'selected' : ''; ?>>Bottom Right</option>
                          <option value="bottom-left" <?php echo ($settings['chatbot_position']['setting_value'] ?? '') == 'bottom-left' ? 'selected' : ''; ?>>Bottom Left</option>
                          <option value="top-right" <?php echo ($settings['chatbot_position']['setting_value'] ?? '') == 'top-right' ? 'selected' : ''; ?>>Top Right</option>
                          <option value="top-left" <?php echo ($settings['chatbot_position']['setting_value'] ?? '') == 'top-left' ? 'selected' : ''; ?>>Top Left</option>
                        </select>
                      </div>
                      
                      <div class="form-group">
                        <label>Pesan Selamat Datang</label>
                        <textarea class="form-control" name="setting_chatbot_welcome_message" rows="3"><?php echo htmlspecialchars($settings['chatbot_welcome_message']['setting_value'] ?? 'Halo! Saya siap membantu Anda dengan informasi tentang aplikasi ePoint dan layanan konseling. Ada yang bisa saya bantu?'); ?></textarea>
                        <small class="text-muted">Pesan yang ditampilkan saat chatbot pertama kali dibuka</small>
                      </div>
                      
                      <div class="form-group">
                        <label>
                          <input type="checkbox" name="setting_chatbot_show_notification" value="1" 
                                 <?php echo ($settings['chatbot_show_notification']['setting_value'] ?? '1') == '1' ? 'checked' : ''; ?>> 
                          Tampilkan notifikasi badge
                        </label>
                      </div>
                      
                      <div class="form-group">
                        <label>
                          <input type="checkbox" name="setting_chatbot_auto_open" value="1" 
                                 <?php echo ($settings['chatbot_auto_open']['setting_value'] ?? '0') == '1' ? 'checked' : ''; ?>> 
                          Buka otomatis untuk user baru
                        </label>
                      </div>
                    </div>
                  </div>
                  
                  <div class="row">
                    <div class="col-md-12">
                      <h5><i class="fa fa-bolt"></i> Quick Actions (Tombol Cepat)</h5>
                      <p class="text-muted">Konfigurasi tombol-tombol cepat yang akan ditampilkan di chatbot.</p>
                      
                      <div class="alert alert-info">
                        <i class="fa fa-info-circle"></i> 
                        <strong>Nama Aplikasi Saat Ini:</strong> <span class="label label-primary"><?php echo htmlspecialchars($app_name); ?></span>
                        <br><small>Quick Actions akan otomatis menggunakan nama aplikasi ini jika dikosongkan.</small>
                      </div>
                      
                      <?php
                      // Ambil nama aplikasi dari database
                      $app_name = $settings['app_name']['setting_value'] ?? 'ePoint';
                      
                      $quick_actions = json_decode($settings['chatbot_quick_actions']['setting_value'] ?? '[]', true);
                      if(empty($quick_actions)) {
                        $quick_actions = [
                          "Apa itu $app_name?", 
                          "Cara login ke $app_name", 
                          "Fitur dashboard $app_name", 
                          "Manajemen kasus siswa", 
                          "Laporan dan dokumentasi", 
                          "Troubleshooting teknis"
                        ];
                      } else {
                        // Replace any hardcoded "ePoint" with current app name
                        foreach($quick_actions as $key => $action) {
                          $quick_actions[$key] = str_replace('ePoint', $app_name, $action);
                        }
                      }
                      ?>
                      
                      <?php for($i = 0; $i < 6; $i++): ?>
                      <div class="form-group">
                        <label>Quick Action <?php echo $i + 1; ?></label>
                        <input type="text" class="form-control" name="quick_action_<?php echo $i; ?>" 
                               value="<?php echo htmlspecialchars($quick_actions[$i] ?? ''); ?>" 
                               placeholder="Contoh: Apa itu <?php echo $app_name; ?>? (gunakan {APP_NAME} untuk nama aplikasi dinamis)">
                      </div>
                      <?php endfor; ?>
                    </div>
                  </div>
                  
                  <div class="row">
                    <div class="col-md-12">
                      <h5><i class="fa fa-question-circle"></i> FAQ (Frequently Asked Questions)</h5>
                      <p class="text-muted">Konfigurasi pertanyaan dan jawaban yang sering diajukan.</p>
                      
                      <div class="alert alert-info">
                        <i class="fa fa-info-circle"></i> 
                        <strong>FAQ akan otomatis menggunakan nama aplikasi:</strong> <span class="label label-primary"><?php echo htmlspecialchars($app_name); ?></span>
                        <br><small>Jika dikosongkan, FAQ akan menggunakan nama aplikasi dari pengaturan.</small>
                      </div>
                      
                      <?php
                      $faq = json_decode($settings['chatbot_faq']['setting_value'] ?? '[]', true);
                      if(empty($faq)) {
                        $faq = [
                          ['question' => "Apa itu $app_name?", 'answer' => "$app_name adalah sistem manajemen sekolah yang membantu mengelola point siswa, layanan BK, dan laporan akademik."],
                          ['question' => "Bagaimana cara login ke $app_name?", 'answer' => 'Gunakan username dan password yang diberikan oleh administrator. Pilih jenis login sesuai dengan peran Anda (Siswa, Admin, atau Guru BK).'],
                          ['question' => "Apa saja fitur utama $app_name?", 'answer' => 'Fitur utama meliputi: Manajemen Point, Layanan BK, Kasus Siswa, Kunjungan Rumah, Laporan Real-time, dan Mobile Friendly.']
                        ];
                      } else {
                        // Replace any hardcoded "ePoint" with current app name
                        foreach($faq as $key => $item) {
                          $faq[$key]['question'] = str_replace('ePoint', $app_name, $item['question']);
                          $faq[$key]['answer'] = str_replace('ePoint', $app_name, $item['answer']);
                        }
                      }
                      ?>
                      
                      <?php for($i = 0; $i < 5; $i++): ?>
                      <div class="panel panel-default">
                        <div class="panel-heading">
                          <h6>FAQ <?php echo $i + 1; ?></h6>
                        </div>
                        <div class="panel-body">
                          <div class="form-group">
                            <label>Pertanyaan</label>
                            <input type="text" class="form-control" name="faq_question_<?php echo $i; ?>" 
                                   value="<?php echo htmlspecialchars($faq[$i]['question'] ?? ''); ?>" 
                                   placeholder="Contoh: Apa itu <?php echo $app_name; ?>? (gunakan {APP_NAME} untuk nama aplikasi dinamis)">
                          </div>
                          <div class="form-group">
                            <label>Jawaban</label>
                            <textarea class="form-control" name="faq_answer_<?php echo $i; ?>" rows="2" 
                                      placeholder="Contoh: <?php echo $app_name; ?> adalah sistem manajemen sekolah... (gunakan {APP_NAME} untuk nama aplikasi dinamis)"><?php echo htmlspecialchars($faq[$i]['answer'] ?? ''); ?></textarea>
                          </div>
                        </div>
                      </div>
                      <?php endfor; ?>
                    </div>
                  </div>
                  
                  <div class="row">
                    <div class="col-md-6">
                      <h5><i class="fa fa-cloud"></i> Cloud AI Settings (Recommended)</h5>
                      <p class="text-muted">Konfigurasi AI cloud untuk production (Google Gemini, OpenAI, dll).</p>
                      
                      <div class="form-group">
                        <label>AI Tier System</label>
                        <select class="form-control" name="setting_ai_tier_system_enabled">
                          <option value="1" <?php echo ($settings['ai_tier_system_enabled']['setting_value'] ?? '1') == '1' ? 'selected' : ''; ?>>Aktifkan Tier System</option>
                          <option value="0" <?php echo ($settings['ai_tier_system_enabled']['setting_value'] ?? '') == '0' ? 'selected' : ''; ?>>Nonaktifkan (Single Provider)</option>
                        </select>
                        <small class="text-muted">Tier system memungkinkan user berbeda menggunakan AI provider berbeda</small>
                      </div>
                      
                      <div class="form-group">
                        <label>AI Provider (Default)</label>
                        <select class="form-control" name="setting_chatbot_ai_provider">
                          <option value="gemini-flash" <?php echo ($settings['chatbot_ai_provider']['setting_value'] ?? 'gemini-flash') == 'gemini-flash' ? 'selected' : ''; ?>>Google Gemini Flash (Gratis)</option>
                          <option value="gemini-pro" <?php echo ($settings['chatbot_ai_provider']['setting_value'] ?? '') == 'gemini-pro' ? 'selected' : ''; ?>>Google Gemini Pro (Berbayar)</option>
                          <option value="openai-gpt4" <?php echo ($settings['chatbot_ai_provider']['setting_value'] ?? '') == 'openai-gpt4' ? 'selected' : ''; ?>>OpenAI GPT-4 (Berbayar)</option>
                          <option value="ollama" <?php echo ($settings['chatbot_ai_provider']['setting_value'] ?? '') == 'ollama' ? 'selected' : ''; ?>>Ollama Local (Gratis)</option>
                        </select>
                      </div>
                      
                      <div class="form-group">
                        <label>API Key</label>
                        <input type="password" class="form-control" name="setting_chatbot_api_key" 
                               value="<?php echo htmlspecialchars($settings['chatbot_api_key']['setting_value'] ?? ''); ?>" 
                               placeholder="Masukkan API key sesuai provider">
                        <small class="text-muted">
                          <strong>Gemini:</strong> Dapatkan di <a href="https://makersuite.google.com/app/apikey" target="_blank">Google AI Studio</a><br>
                          <strong>OpenAI:</strong> Dapatkan di <a href="https://platform.openai.com/api-keys" target="_blank">OpenAI Platform</a>
                        </small>
                      </div>
                      
                      <div class="form-group">
                        <label>
                          <input type="checkbox" name="setting_chatbot_cloud_enabled" value="1" 
                                 <?php echo ($settings['chatbot_cloud_enabled']['setting_value'] ?? '1') == '1' ? 'checked' : ''; ?>> 
                          Aktifkan Cloud AI
                        </label>
                        <small class="text-muted">Gunakan AI cloud untuk jawaban yang lebih cerdas</small>
                      </div>
                      
                      <div class="form-group">
                        <label>
                          <input type="checkbox" name="setting_chatbot_fallback_enabled" value="1" 
                                 <?php echo ($settings['chatbot_fallback_enabled']['setting_value'] ?? '1') == '1' ? 'checked' : ''; ?>> 
                          Aktifkan Fallback ke Rule-based
                        </label>
                        <small class="text-muted">Jika cloud AI gagal, gunakan jawaban rule-based</small>
                      </div>
                    </div>
                    
                    <div class="col-md-6">
                      <h5><i class="fa fa-cogs"></i> Local AI Settings (Ollama)</h5>
                      <p class="text-muted">Konfigurasi AI lokal menggunakan Ollama (opsional).</p>
                      
                      <div class="form-group">
                        <label>
                          <input type="checkbox" name="setting_chatbot_ollama_enabled" value="1" 
                                 <?php echo ($settings['chatbot_ollama_enabled']['setting_value'] ?? '0') == '1' ? 'checked' : ''; ?>> 
                          Aktifkan Ollama AI
                        </label>
                        <small class="text-muted">Perlu install Ollama terlebih dahulu</small>
                      </div>
                      
                      <div class="form-group">
                        <label>Ollama URL</label>
                        <input type="text" class="form-control" name="setting_chatbot_ollama_url" 
                               value="<?php echo htmlspecialchars($settings['chatbot_ollama_url']['setting_value'] ?? 'http://localhost:11434'); ?>">
                        <small class="text-muted">URL server Ollama (default: http://localhost:11434)</small>
                      </div>
                      
                      <div class="form-group">
                        <label>Model AI</label>
                        <input type="text" class="form-control" name="setting_chatbot_ollama_model" 
                               value="<?php echo htmlspecialchars($settings['chatbot_ollama_model']['setting_value'] ?? 'llama2:7b'); ?>">
                        <small class="text-muted">Model AI yang akan digunakan (contoh: llama2:7b, mistral:7b)</small>
                      </div>
                    </div>
                  </div>
                  
                  <div class="row">
                    <div class="col-md-12">
                      <h5><i class="fa fa-layer-group"></i> AI Tier Configuration</h5>
                      <p class="text-muted">Konfigurasi AI provider untuk setiap tier user.</p>
                      
                      <div class="row">
                        <div class="col-md-4">
                          <div class="panel panel-default">
                            <div class="panel-heading">
                              <h6><i class="fa fa-user"></i> Free Tier</h6>
                            </div>
                            <div class="panel-body">
                              <div class="form-group">
                                <label>AI Provider</label>
                                <select class="form-control" name="setting_ai_free_tier_provider">
                                  <option value="gemini-flash" <?php echo ($settings['ai_free_tier_provider']['setting_value'] ?? 'gemini-flash') == 'gemini-flash' ? 'selected' : ''; ?>>Gemini Flash</option>
                                  <option value="gemini-pro" <?php echo ($settings['ai_free_tier_provider']['setting_value'] ?? '') == 'gemini-pro' ? 'selected' : ''; ?>>Gemini Pro</option>
                                  <option value="ollama" <?php echo ($settings['ai_free_tier_provider']['setting_value'] ?? '') == 'ollama' ? 'selected' : ''; ?>>Ollama Local</option>
                                </select>
                              </div>
                              <div class="form-group">
                                <label>Daily Limit</label>
                                <input type="number" class="form-control" name="setting_ai_free_tier_daily" 
                                       value="<?php echo json_decode($settings['ai_free_tier_limits']['setting_value'] ?? '{"daily_requests": 50}', true)['daily_requests'] ?? 50; ?>">
                              </div>
                              <div class="form-group">
                                <label>Monthly Limit</label>
                                <input type="number" class="form-control" name="setting_ai_free_tier_monthly" 
                                       value="<?php echo json_decode($settings['ai_free_tier_limits']['setting_value'] ?? '{"monthly_requests": 1000}', true)['monthly_requests'] ?? 1000; ?>">
                              </div>
                            </div>
                          </div>
                        </div>
                        
                        <div class="col-md-4">
                          <div class="panel panel-primary">
                            <div class="panel-heading">
                              <h6><i class="fa fa-star"></i> Pro Tier</h6>
                            </div>
                            <div class="panel-body">
                              <div class="form-group">
                                <label>AI Provider</label>
                                <select class="form-control" name="setting_ai_pro_tier_provider">
                                  <option value="gemini-flash" <?php echo ($settings['ai_pro_tier_provider']['setting_value'] ?? 'gemini-flash') == 'gemini-flash' ? 'selected' : ''; ?>>Gemini Flash</option>
                                  <option value="gemini-pro" <?php echo ($settings['ai_pro_tier_provider']['setting_value'] ?? 'gemini-pro') == 'gemini-pro' ? 'selected' : ''; ?>>Gemini Pro</option>
                                  <option value="openai-gpt4" <?php echo ($settings['ai_pro_tier_provider']['setting_value'] ?? '') == 'openai-gpt4' ? 'selected' : ''; ?>>OpenAI GPT-4</option>
                                </select>
                              </div>
                              <div class="form-group">
                                <label>Daily Limit</label>
                                <input type="number" class="form-control" name="setting_ai_pro_tier_daily" 
                                       value="<?php echo json_decode($settings['ai_pro_tier_limits']['setting_value'] ?? '{"daily_requests": 500}', true)['daily_requests'] ?? 500; ?>">
                              </div>
                              <div class="form-group">
                                <label>Monthly Limit</label>
                                <input type="number" class="form-control" name="setting_ai_pro_tier_monthly" 
                                       value="<?php echo json_decode($settings['ai_pro_tier_limits']['setting_value'] ?? '{"monthly_requests": 10000}', true)['monthly_requests'] ?? 10000; ?>">
                              </div>
                            </div>
                          </div>
                        </div>
                        
                        <div class="col-md-4">
                          <div class="panel panel-success">
                            <div class="panel-heading">
                              <h6><i class="fa fa-crown"></i> Enterprise Tier</h6>
                            </div>
                            <div class="panel-body">
                              <div class="form-group">
                                <label>AI Provider</label>
                                <select class="form-control" name="setting_ai_enterprise_tier_provider">
                                  <option value="gemini-pro" <?php echo ($settings['ai_enterprise_tier_provider']['setting_value'] ?? 'gemini-pro') == 'gemini-pro' ? 'selected' : ''; ?>>Gemini Pro</option>
                                  <option value="openai-gpt4" <?php echo ($settings['ai_enterprise_tier_provider']['setting_value'] ?? '') == 'openai-gpt4' ? 'selected' : ''; ?>>OpenAI GPT-4</option>
                                </select>
                              </div>
                              <div class="form-group">
                                <label>Daily Limit</label>
                                <input type="number" class="form-control" name="setting_ai_enterprise_tier_daily" 
                                       value="<?php echo json_decode($settings['ai_enterprise_tier_limits']['setting_value'] ?? '{"daily_requests": 5000}', true)['daily_requests'] ?? 5000; ?>">
                              </div>
                              <div class="form-group">
                                <label>Monthly Limit</label>
                                <input type="number" class="form-control" name="setting_ai_enterprise_tier_monthly" 
                                       value="<?php echo json_decode($settings['ai_enterprise_tier_limits']['setting_value'] ?? '{"monthly_requests": 100000}', true)['monthly_requests'] ?? 100000; ?>">
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                  
                  <div class="row">
                    <div class="col-md-12">
                      <div class="alert alert-info">
                        <h5><i class="fa fa-info-circle"></i> Panduan Setup AI</h5>
                        <p><strong>Untuk Production (Recommended):</strong></p>
                        <ol>
                          <li><strong>Google Gemini Flash:</strong> Gratis, 15 requests/minute, kualitas baik</li>
                          <li><strong>Google Gemini Pro:</strong> Berbayar, kualitas excellent, reasoning advanced</li>
                          <li><strong>OpenAI GPT-4:</strong> Berbayar, sangat powerful, cost tinggi</li>
                        </ol>
                        <p><strong>Tier System Benefits:</strong></p>
                        <ul>
                          <li><strong>Free:</strong> Gemini Flash (50/day, 1000/month)</li>
                          <li><strong>Pro:</strong> Gemini Pro (500/day, 10000/month)</li>
                          <li><strong>Enterprise:</strong> GPT-4 (5000/day, 100000/month)</li>
                        </ul>
                        <p><strong>Lihat panduan lengkap:</strong> <a href="../CHATBOT_PRODUCTION_GUIDE.md" target="_blank">CHATBOT_PRODUCTION_GUIDE.md</a></p>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            
            <div class="box-footer">
              <button type="submit" name="update_settings" class="btn btn-primary">
                <i class="fa fa-save"></i> Simpan Pengaturan
              </button>
              <a href="index.php" class="btn btn-default">
                <i class="fa fa-arrow-left"></i> Kembali
              </a>
            </div>
          </form>
        </div>
      </div>
    </div>
  </section>
</div>

<?php include 'footer.php'; ?>

<script>
// Color picker synchronization (after jQuery loaded in footer)
$(function() {
  // Sync color picker with text input
  $('input[type="color"]').on('change', function() {
    var textInput = $(this).siblings('input[type="text"]');
    textInput.val($(this).val());
    updateColorPreview();
  });

  // Sync text input with color picker
  $('input[type="text"]').on('input', function() {
    var colorInput = $(this).siblings('input[type="color"]');
    if (isValidHex($(this).val())) {
      colorInput.val($(this).val());
      updateColorPreview();
    }
  });

  function updateColorPreview() {
    $('.color-preview').each(function() {
      var colorType = $(this).find('div').text().toLowerCase();
      var colorValue = $('input[name="setting_' + colorType + '_color"]').val();
      if (colorValue) {
        $(this).css('background-color', colorValue);
      }
    });
  }

  function isValidHex(hex) {
    return /^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/.test(hex);
  }

  updateColorPreview();
});
</script>
