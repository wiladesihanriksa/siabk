<?php
// ePoint Chatbot Widget Integration
// File ini untuk di-include di halaman utama ePoint

// Include functions
include 'functions_app_settings.php';
include 'functions_chatbot_settings.php';

// Cek apakah user sudah login (opsional)
$is_logged_in = isset($_SESSION['user_id']) || isset($_SESSION['username']);

// Ambil konfigurasi chatbot dari database
$chatbot_config = getChatbotConfig($koneksi);

// Cek apakah chatbot diaktifkan
if($chatbot_config['enabled'] != '1') {
    return; // Jangan tampilkan chatbot jika tidak diaktifkan
}
?>

<!-- ePoint Chatbot Widget -->
<link rel="stylesheet" href="chatbot/chatbot.css">
<!-- Font Awesome already loaded in main page -->

<div id="chatbot-widget" class="chatbot-widget">
    <div class="chatbot-toggle" id="chatbot-toggle">
        <i class="fas fa-comments"></i>
        <span class="notification-badge" id="notification-badge" style="display: none;">1</span>
    </div>
    
    <div class="chatbot-container" id="chatbot-container">
        <div class="chatbot-header">
            <div class="chatbot-avatar">
                <i class="<?php echo htmlspecialchars($chatbot_config['avatar']); ?>"></i>
            </div>
            <div class="chatbot-info">
                <h3><?php echo htmlspecialchars($chatbot_config['name']); ?></h3>
                <p><?php echo htmlspecialchars($chatbot_config['status']); ?></p>
            </div>
            <button class="chatbot-close" id="chatbot-close">
                <i class="fas fa-times"></i>
            </button>
        </div>
        
        <div class="chatbot-messages" id="chatbot-messages">
            <div class="message bot-message">
                <div class="message-avatar">
                    <i class="<?php echo htmlspecialchars($chatbot_config['avatar']); ?>"></i>
                </div>
                <div class="message-content">
                    <p><?php echo htmlspecialchars($chatbot_config['welcome_message']); ?></p>
                    <div class="message-time">Baru saja</div>
                </div>
            </div>
        </div>
        
        <div class="chatbot-input-container">
            <div class="quick-actions">
                <?php 
                $quick_actions = $chatbot_config['quick_actions'];
                $display_count = 0;
                foreach($quick_actions as $action): 
                    if(!empty($action) && $display_count < 4): // Batasi hanya 4 quick actions
                        $display_count++;
                ?>
                <button class="quick-btn" data-message="<?php echo htmlspecialchars($action); ?>"><?php echo htmlspecialchars($action); ?></button>
                <?php 
                    endif;
                endforeach; 
                ?>
            </div>
            <div class="input-wrapper">
                <input type="text" id="chatbot-input" placeholder="Ketik pesan Anda..." autocomplete="off">
                <button id="chatbot-send" class="send-btn">
                    <i class="fas fa-paper-plane"></i>
                </button>
            </div>
        </div>
    </div>
</div>

<script src="./chatbot/chatbot.js"></script>

<script>
// ePoint Chatbot Integration Script
document.addEventListener('DOMContentLoaded', function() {
    // Initialize chatbot
    const chatbot = new ePointChatbot();
    
    // Show welcome notification after 3 seconds
    setTimeout(() => {
        const badge = document.getElementById('notification-badge');
        if (badge && <?php echo $chatbot_config['show_notification'] == '1' ? 'true' : 'false'; ?>) {
            badge.style.display = 'flex';
        }
    }, 3000);
    
    // Auto-open for first-time users (optional)
    <?php if ($chatbot_config['auto_open'] == '1'): ?>
    setTimeout(() => {
        chatbot.openChat();
    }, 5000);
    <?php endif; ?>
    
    // Quick actions are handled by chatbot.js
    // No need to duplicate event listeners here
});
</script>

<style>
/* Additional ePoint-specific styles */
.chatbot-widget {
    z-index: 9999; /* Ensure it's above other elements */
}

/* Customize colors to match ePoint theme */
.chatbot-toggle {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

.chatbot-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

.send-btn {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

.user-message .message-content {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

.quick-btn:hover {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

/* Responsive adjustments for ePoint layout */
@media (max-width: 768px) {
    .chatbot-widget {
        bottom: 10px;
        right: 10px;
    }
}
</style>
