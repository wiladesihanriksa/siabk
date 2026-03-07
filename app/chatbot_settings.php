<?php
// Halaman Pengaturan Chatbot ePoint
session_start();
include 'koneksi.php';
include 'functions_app_settings.php';
include 'functions_chatbot_settings.php';

// Cek apakah user sudah login sebagai admin
if(!isset($_SESSION['level']) || $_SESSION['level'] != 'admin') {
    header('Location: index.php?alert=belum_login');
    exit;
}

// Ambil pengaturan aplikasi
$app_settings = getAppSettings($koneksi);
$chatbot_settings = getChatbotSettings($koneksi);

// Proses form jika ada submit
if($_POST) {
    $new_settings = array();
    
    // Chatbot Basic Settings
    $new_settings['chatbot_enabled'] = isset($_POST['chatbot_enabled']) ? '1' : '0';
    $new_settings['chatbot_name'] = $_POST['chatbot_name'];
    $new_settings['chatbot_description'] = $_POST['chatbot_description'];
    $new_settings['chatbot_status'] = $_POST['chatbot_status'];
    $new_settings['chatbot_avatar'] = $_POST['chatbot_avatar'];
    $new_settings['chatbot_theme'] = $_POST['chatbot_theme'];
    $new_settings['chatbot_position'] = $_POST['chatbot_position'];
    $new_settings['chatbot_welcome_message'] = $_POST['chatbot_welcome_message'];
    
    // Chatbot Display Settings
    $new_settings['chatbot_show_notification'] = isset($_POST['chatbot_show_notification']) ? '1' : '0';
    $new_settings['chatbot_auto_open'] = isset($_POST['chatbot_auto_open']) ? '1' : '0';
    
    // Quick Actions
    $quick_actions = array();
    for($i = 1; $i <= 6; $i++) {
        if(!empty($_POST["quick_action_$i"])) {
            $quick_actions[] = $_POST["quick_action_$i"];
        }
    }
    $new_settings['chatbot_quick_actions'] = json_encode($quick_actions);
    
    // FAQ
    $faq = array();
    for($i = 1; $i <= 5; $i++) {
        if(!empty($_POST["faq_question_$i"]) && !empty($_POST["faq_answer_$i"])) {
            $faq[] = array(
                'question' => $_POST["faq_question_$i"],
                'answer' => $_POST["faq_answer_$i"]
            );
        }
    }
    $new_settings['chatbot_faq'] = json_encode($faq);
    
    // App Features untuk Chatbot
    $new_settings['chatbot_app_features'] = $_POST['chatbot_app_features'];
    
    // AI Provider Settings
    $new_settings['chatbot_ai_provider'] = $_POST['chatbot_ai_provider'];
    $new_settings['chatbot_api_key'] = $_POST['chatbot_api_key'];
    $new_settings['chatbot_cloud_enabled'] = isset($_POST['chatbot_cloud_enabled']) ? '1' : '0';
    $new_settings['chatbot_fallback_enabled'] = isset($_POST['chatbot_fallback_enabled']) ? '1' : '0';
    
    // Ollama Settings
    $new_settings['chatbot_ollama_enabled'] = isset($_POST['chatbot_ollama_enabled']) ? '1' : '0';
    $new_settings['chatbot_ollama_url'] = $_POST['chatbot_ollama_url'];
    $new_settings['chatbot_ollama_model'] = $_POST['chatbot_ollama_model'];
    
    // Simpan pengaturan
    if(saveChatbotSettings($koneksi, $new_settings)) {
        $success_message = "Pengaturan chatbot berhasil disimpan!";
        // Refresh settings
        $chatbot_settings = getChatbotSettings($koneksi);
    } else {
        $error_message = "Gagal menyimpan pengaturan chatbot!";
    }
}

// Ambil konfigurasi chatbot
$chatbot_config = getChatbotConfig($koneksi);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pengaturan Chatbot - <?php echo getSetting($app_settings, 'app_name', 'ePoint'); ?></title>
    
    <!-- Bootstrap & Font Awesome -->
    <link rel="stylesheet" href="assets/bower_components/bootstrap/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/bower_components/font-awesome/css/font-awesome.min.css">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: #f8f9fa;
        }
        
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px 0;
            margin-bottom: 30px;
        }
        
        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            margin-bottom: 30px;
        }
        
        .card-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 15px 15px 0 0 !important;
            border: none;
            padding: 20px 30px;
        }
        
        .form-control, .form-select {
            border-radius: 10px;
            border: 2px solid #e9ecef;
            padding: 12px 15px;
            transition: all 0.3s ease;
        }
        
        .form-control:focus, .form-select:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            border-radius: 10px;
            padding: 12px 30px;
            font-weight: 600;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(102, 126, 234, 0.4);
        }
        
        .preview-chatbot {
            background: #f8f9fa;
            border: 2px dashed #dee2e6;
            border-radius: 15px;
            padding: 20px;
            text-align: center;
            margin-top: 20px;
        }
        
        .chatbot-preview {
            max-width: 300px;
            margin: 0 auto;
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            overflow: hidden;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <h1><i class="fa fa-robot"></i> Pengaturan Chatbot</h1>
                    <p>Kustomisasi chatbot ePoint sesuai kebutuhan aplikasi</p>
                </div>
            </div>
        </div>
    </div>

    <div class="container">
        <?php if(isset($success_message)): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fa fa-check-circle"></i> <?php echo $success_message; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php endif; ?>

        <?php if(isset($error_message)): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fa fa-exclamation-circle"></i> <?php echo $error_message; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php endif; ?>

        <form method="POST">
            <!-- Basic Settings -->
            <div class="card">
                <div class="card-header">
                    <h4><i class="fa fa-cog"></i> Pengaturan Dasar</h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-check form-switch mb-3">
                                <input class="form-check-input" type="checkbox" id="chatbot_enabled" name="chatbot_enabled" 
                                       <?php echo $chatbot_config['enabled'] == '1' ? 'checked' : ''; ?>>
                                <label class="form-check-label" for="chatbot_enabled">
                                    <strong>Aktifkan Chatbot</strong>
                                </label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="chatbot_name">Nama Chatbot</label>
                                <input type="text" class="form-control" id="chatbot_name" name="chatbot_name" 
                                       value="<?php echo htmlspecialchars($chatbot_config['name']); ?>" required>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="chatbot_description">Deskripsi Chatbot</label>
                                <textarea class="form-control" id="chatbot_description" name="chatbot_description" rows="3"><?php echo htmlspecialchars($chatbot_config['description']); ?></textarea>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="chatbot_status">Status</label>
                                <select class="form-select" id="chatbot_status" name="chatbot_status">
                                    <option value="Online" <?php echo $chatbot_config['status'] == 'Online' ? 'selected' : ''; ?>>Online</option>
                                    <option value="Busy" <?php echo $chatbot_config['status'] == 'Busy' ? 'selected' : ''; ?>>Busy</option>
                                    <option value="Away" <?php echo $chatbot_config['status'] == 'Away' ? 'selected' : ''; ?>>Away</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="chatbot_welcome_message">Pesan Selamat Datang</label>
                        <textarea class="form-control" id="chatbot_welcome_message" name="chatbot_welcome_message" rows="2"><?php echo htmlspecialchars($chatbot_config['welcome_message']); ?></textarea>
                    </div>
                </div>
            </div>

            <!-- Appearance Settings -->
            <div class="card">
                <div class="card-header">
                    <h4><i class="fa fa-paint-brush"></i> Tampilan & Posisi</h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="chatbot_avatar">Avatar</label>
                                <select class="form-select" id="chatbot_avatar" name="chatbot_avatar">
                                    <option value="fas fa-robot" <?php echo $chatbot_config['avatar'] == 'fas fa-robot' ? 'selected' : ''; ?>>🤖 Robot</option>
                                    <option value="fas fa-user-tie" <?php echo $chatbot_config['avatar'] == 'fas fa-user-tie' ? 'selected' : ''; ?>>👨‍💼 Professional</option>
                                    <option value="fas fa-graduation-cap" <?php echo $chatbot_config['avatar'] == 'fas fa-graduation-cap' ? 'selected' : ''; ?>>🎓 Education</option>
                                    <option value="fas fa-heart" <?php echo $chatbot_config['avatar'] == 'fas fa-heart' ? 'selected' : ''; ?>>❤️ Friendly</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="chatbot_theme">Tema</label>
                                <select class="form-select" id="chatbot_theme" name="chatbot_theme">
                                    <option value="modern" <?php echo $chatbot_config['theme'] == 'modern' ? 'selected' : ''; ?>>Modern</option>
                                    <option value="classic" <?php echo $chatbot_config['theme'] == 'classic' ? 'selected' : ''; ?>>Classic</option>
                                    <option value="minimal" <?php echo $chatbot_config['theme'] == 'minimal' ? 'selected' : ''; ?>>Minimal</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="chatbot_position">Posisi</label>
                                <select class="form-select" id="chatbot_position" name="chatbot_position">
                                    <option value="bottom-right" <?php echo $chatbot_config['position'] == 'bottom-right' ? 'selected' : ''; ?>>Bottom Right</option>
                                    <option value="bottom-left" <?php echo $chatbot_config['position'] == 'bottom-left' ? 'selected' : ''; ?>>Bottom Left</option>
                                    <option value="top-right" <?php echo $chatbot_config['position'] == 'top-right' ? 'selected' : ''; ?>>Top Right</option>
                                    <option value="top-left" <?php echo $chatbot_config['position'] == 'top-left' ? 'selected' : ''; ?>>Top Left</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-check form-switch mb-3">
                                <input class="form-check-input" type="checkbox" id="chatbot_show_notification" name="chatbot_show_notification" 
                                       <?php echo $chatbot_config['show_notification'] == '1' ? 'checked' : ''; ?>>
                                <label class="form-check-label" for="chatbot_show_notification">
                                    Tampilkan Notifikasi
                                </label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-check form-switch mb-3">
                                <input class="form-check-input" type="checkbox" id="chatbot_auto_open" name="chatbot_auto_open" 
                                       <?php echo $chatbot_config['auto_open'] == '1' ? 'checked' : ''; ?>>
                                <label class="form-check-label" for="chatbot_auto_open">
                                    Buka Otomatis
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="card">
                <div class="card-header">
                    <h4><i class="fa fa-bolt"></i> Quick Actions</h4>
                </div>
                <div class="card-body">
                    <p class="text-muted">Tombol cepat yang muncul di chatbot untuk memudahkan pengguna</p>
                    <?php 
                    $quick_actions = $chatbot_config['quick_actions'];
                    for($i = 1; $i <= 6; $i++): 
                        $value = isset($quick_actions[$i-1]) ? $quick_actions[$i-1] : '';
                    ?>
                    <div class="form-group mb-3">
                        <label for="quick_action_<?php echo $i; ?>">Quick Action <?php echo $i; ?></label>
                        <input type="text" class="form-control" id="quick_action_<?php echo $i; ?>" name="quick_action_<?php echo $i; ?>" 
                               value="<?php echo htmlspecialchars($value); ?>" placeholder="Contoh: Apa itu ePoint?">
                    </div>
                    <?php endfor; ?>
                </div>
            </div>

            <!-- FAQ -->
            <div class="card">
                <div class="card-header">
                    <h4><i class="fa fa-question-circle"></i> FAQ (Frequently Asked Questions)</h4>
                </div>
                <div class="card-body">
                    <p class="text-muted">Pertanyaan dan jawaban yang sering diajukan</p>
                    <?php 
                    $faq = $chatbot_config['faq'];
                    for($i = 1; $i <= 5; $i++): 
                        $question = isset($faq[$i-1]['question']) ? $faq[$i-1]['question'] : '';
                        $answer = isset($faq[$i-1]['answer']) ? $faq[$i-1]['answer'] : '';
                    ?>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="faq_question_<?php echo $i; ?>">Pertanyaan <?php echo $i; ?></label>
                            <input type="text" class="form-control" id="faq_question_<?php echo $i; ?>" name="faq_question_<?php echo $i; ?>" 
                                   value="<?php echo htmlspecialchars($question); ?>" placeholder="Contoh: Apa itu ePoint?">
                        </div>
                        <div class="col-md-6">
                            <label for="faq_answer_<?php echo $i; ?>">Jawaban <?php echo $i; ?></label>
                            <textarea class="form-control" id="faq_answer_<?php echo $i; ?>" name="faq_answer_<?php echo $i; ?>" rows="2" 
                                      placeholder="Jawaban untuk pertanyaan di atas"><?php echo htmlspecialchars($answer); ?></textarea>
                        </div>
                    </div>
                    <?php endfor; ?>
                </div>
            </div>

            <!-- App Information -->
            <div class="card">
                <div class="card-header">
                    <h4><i class="fa fa-info-circle"></i> Informasi Aplikasi</h4>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label for="chatbot_app_features">Fitur Aplikasi (untuk chatbot)</label>
                        <textarea class="form-control" id="chatbot_app_features" name="chatbot_app_features" rows="3" 
                                  placeholder="Daftar fitur aplikasi yang akan diketahui chatbot"><?php echo htmlspecialchars($chatbot_config['app_info']['app_features']); ?></textarea>
                        <small class="form-text text-muted">Pisahkan dengan koma, contoh: Manajemen Point, Layanan BK, Kasus Siswa</small>
                    </div>
                </div>
            </div>

            <!-- Cloud AI Settings -->
            <div class="card">
                <div class="card-header">
                    <h4><i class="fa fa-cloud"></i> Pengaturan AI Cloud</h4>
                </div>
                <div class="card-body">
                    <div class="form-check form-switch mb-3">
                        <input class="form-check-input" type="checkbox" id="chatbot_cloud_enabled" name="chatbot_cloud_enabled" 
                               <?php echo getChatbotSetting($chatbot_settings, 'chatbot_cloud_enabled', '1') == '1' ? 'checked' : ''; ?>>
                        <label class="form-check-label" for="chatbot_cloud_enabled">
                            <strong>Aktifkan Cloud AI</strong>
                            <small class="d-block text-muted">Menggunakan AI cloud untuk respons yang lebih canggih</small>
                        </label>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="chatbot_ai_provider">AI Provider</label>
                                <select class="form-select" id="chatbot_ai_provider" name="chatbot_ai_provider">
                                    <option value="gemini-flash" <?php echo getChatbotSetting($chatbot_settings, 'chatbot_ai_provider', 'gemini-flash') == 'gemini-flash' ? 'selected' : ''; ?>>Gemini Flash (Cepat)</option>
                                    <option value="gemini-pro" <?php echo getChatbotSetting($chatbot_settings, 'chatbot_ai_provider', 'gemini-flash') == 'gemini-pro' ? 'selected' : ''; ?>>Gemini Pro (Kualitas Tinggi)</option>
                                    <option value="openai-gpt4" <?php echo getChatbotSetting($chatbot_settings, 'chatbot_ai_provider', 'gemini-flash') == 'openai-gpt4' ? 'selected' : ''; ?>>OpenAI GPT-4 (Coming Soon)</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="chatbot_api_key">API Key</label>
                                <input type="password" class="form-control" id="chatbot_api_key" name="chatbot_api_key" 
                                       value="<?php echo htmlspecialchars(getChatbotSetting($chatbot_settings, 'chatbot_api_key', '')); ?>" 
                                       placeholder="Masukkan API Key">
                                <small class="form-text text-muted">API Key untuk mengakses layanan AI cloud</small>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-check form-switch mb-3">
                        <input class="form-check-input" type="checkbox" id="chatbot_fallback_enabled" name="chatbot_fallback_enabled" 
                               <?php echo getChatbotSetting($chatbot_settings, 'chatbot_fallback_enabled', '1') == '1' ? 'checked' : ''; ?>>
                        <label class="form-check-label" for="chatbot_fallback_enabled">
                            <strong>Aktifkan Fallback System</strong>
                            <small class="d-block text-muted">Gunakan sistem rule-based jika AI cloud gagal</small>
                        </label>
                    </div>
                </div>
            </div>

            <!-- Ollama AI Settings -->
            <div class="card">
                <div class="card-header">
                    <h4><i class="fa fa-brain"></i> Pengaturan AI Lokal (Ollama)</h4>
                </div>
                <div class="card-body">
                    <div class="form-check form-switch mb-3">
                        <input class="form-check-input" type="checkbox" id="chatbot_ollama_enabled" name="chatbot_ollama_enabled" 
                               <?php echo $chatbot_config['ollama_enabled'] == '1' ? 'checked' : ''; ?>>
                        <label class="form-check-label" for="chatbot_ollama_enabled">
                            <strong>Aktifkan Ollama AI</strong>
                            <small class="d-block text-muted">Untuk AI yang berjalan di server lokal (memerlukan instalasi Ollama)</small>
                        </label>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="chatbot_ollama_url">Ollama URL</label>
                                <input type="text" class="form-control" id="chatbot_ollama_url" name="chatbot_ollama_url" 
                                       value="<?php echo htmlspecialchars($chatbot_config['ollama_url']); ?>" 
                                       placeholder="http://localhost:11434">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="chatbot_ollama_model">Model AI</label>
                                <select class="form-select" id="chatbot_ollama_model" name="chatbot_ollama_model">
                                    <option value="llama2:7b" <?php echo $chatbot_config['ollama_model'] == 'llama2:7b' ? 'selected' : ''; ?>>llama2:7b (Recommended)</option>
                                    <option value="llama2:7b-chat" <?php echo $chatbot_config['ollama_model'] == 'llama2:7b-chat' ? 'selected' : ''; ?>>llama2:7b-chat</option>
                                    <option value="llama2:7b-instruct" <?php echo $chatbot_config['ollama_model'] == 'llama2:7b-instruct' ? 'selected' : ''; ?>>llama2:7b-instruct</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Preview -->
            <div class="card">
                <div class="card-header">
                    <h4><i class="fa fa-eye"></i> Preview Chatbot</h4>
                </div>
                <div class="card-body">
                    <div class="preview-chatbot">
                        <h5>Preview Chatbot Widget</h5>
                        <div class="chatbot-preview">
                            <div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 15px; text-align: center;">
                                <div style="display: flex; align-items: center; gap: 10px;">
                                    <i class="<?php echo $chatbot_config['avatar']; ?>" style="font-size: 20px;"></i>
                                    <div>
                                        <div style="font-weight: bold;"><?php echo htmlspecialchars($chatbot_config['name']); ?></div>
                                        <div style="font-size: 12px; opacity: 0.8;"><?php echo htmlspecialchars($chatbot_config['status']); ?></div>
                                    </div>
                                </div>
                            </div>
                            <div style="padding: 15px; background: #f8f9fa;">
                                <div style="background: white; padding: 10px; border-radius: 10px; margin-bottom: 10px;">
                                    <?php echo htmlspecialchars($chatbot_config['welcome_message']); ?>
                                </div>
                                <div style="display: flex; gap: 5px; flex-wrap: wrap;">
                                    <?php foreach($quick_actions as $action): ?>
                                    <span style="background: #e9ecef; padding: 5px 10px; border-radius: 15px; font-size: 12px;"><?php echo htmlspecialchars($action); ?></span>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Submit Button -->
            <div class="text-center mb-5">
                <button type="submit" class="btn btn-primary btn-lg">
                    <i class="fa fa-save"></i> Simpan Pengaturan
                </button>
                <a href="index.php" class="btn btn-secondary btn-lg ms-3">
                    <i class="fa fa-arrow-left"></i> Kembali
                </a>
            </div>
        </form>
    </div>

    <script src="assets/bower_components/jquery/dist/jquery.min.js"></script>
    <script src="assets/bower_components/bootstrap/dist/js/bootstrap.min.js"></script>
    
    <script>
        // Auto-update preview
        function updatePreview() {
            const name = document.getElementById('chatbot_name').value;
            const status = document.getElementById('chatbot_status').value;
            const avatar = document.getElementById('chatbot_avatar').value;
            const welcome = document.getElementById('chatbot_welcome_message').value;
            
            // Update preview elements
            const previewName = document.querySelector('.chatbot-preview .fa-robot, .chatbot-preview .fa-user-tie, .chatbot-preview .fa-graduation-cap, .chatbot-preview .fa-heart');
            if(previewName) {
                previewName.className = avatar;
            }
        }
        
        // Add event listeners
        document.getElementById('chatbot_name').addEventListener('input', updatePreview);
        document.getElementById('chatbot_status').addEventListener('change', updatePreview);
        document.getElementById('chatbot_avatar').addEventListener('change', updatePreview);
        document.getElementById('chatbot_welcome_message').addEventListener('input', updatePreview);
    </script>
</body>
</html>
