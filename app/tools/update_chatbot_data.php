<?php
// Script untuk mengupdate data chatbot yang sudah ada
// Mengganti "ePoint" hardcoded dengan nama aplikasi yang benar
include 'koneksi.php';

echo "<h1>🔄 Update Data Chatbot</h1>";

// Ambil nama aplikasi dari database
$app_name_query = mysqli_query($koneksi, "SELECT setting_value FROM app_settings WHERE setting_key = 'app_name'");
$app_name = 'ePoint'; // default
if($app_name_query && mysqli_num_rows($app_name_query) > 0) {
    $app_name_row = mysqli_fetch_assoc($app_name_query);
    $app_name = $app_name_row['setting_value'];
}

echo "<p><strong>Nama Aplikasi Saat Ini:</strong> <span style='color: blue; font-weight: bold;'>$app_name</span></p>";

// Update Quick Actions
echo "<h2>📝 Update Quick Actions</h2>";
$quick_actions_query = mysqli_query($koneksi, "SELECT setting_value FROM app_settings WHERE setting_key = 'chatbot_quick_actions'");
if($quick_actions_query && mysqli_num_rows($quick_actions_query) > 0) {
    $quick_actions_row = mysqli_fetch_assoc($quick_actions_query);
    $quick_actions = json_decode($quick_actions_row['setting_value'], true);
    
    if(!empty($quick_actions)) {
        $updated = false;
        foreach($quick_actions as $key => $action) {
            if(strpos($action, 'ePoint') !== false) {
                $quick_actions[$key] = str_replace('ePoint', $app_name, $action);
                $updated = true;
                echo "✅ Updated: " . $action . " → " . $quick_actions[$key] . "<br>";
            }
        }
        
        if($updated) {
            $quick_actions_json = json_encode($quick_actions);
            $update_query = "UPDATE app_settings SET setting_value = ? WHERE setting_key = 'chatbot_quick_actions'";
            $stmt = mysqli_prepare($koneksi, $update_query);
            mysqli_stmt_bind_param($stmt, "s", $quick_actions_json);
            if(mysqli_stmt_execute($stmt)) {
                echo "<strong>✅ Quick Actions berhasil diupdate!</strong><br>";
            } else {
                echo "<strong>❌ Error updating Quick Actions: " . mysqli_error($koneksi) . "</strong><br>";
            }
        } else {
            echo "ℹ️ Quick Actions sudah menggunakan nama aplikasi yang benar.<br>";
        }
    }
} else {
    echo "ℹ️ Quick Actions belum ada di database.<br>";
}

// Update FAQ
echo "<h2>❓ Update FAQ</h2>";
$faq_query = mysqli_query($koneksi, "SELECT setting_value FROM app_settings WHERE setting_key = 'chatbot_faq'");
if($faq_query && mysqli_num_rows($faq_query) > 0) {
    $faq_row = mysqli_fetch_assoc($faq_query);
    $faq = json_decode($faq_row['setting_value'], true);
    
    if(!empty($faq)) {
        $updated = false;
        foreach($faq as $key => $item) {
            $question_updated = false;
            $answer_updated = false;
            
            if(strpos($item['question'], 'ePoint') !== false) {
                $faq[$key]['question'] = str_replace('ePoint', $app_name, $item['question']);
                $question_updated = true;
                echo "✅ Updated Question: " . $item['question'] . " → " . $faq[$key]['question'] . "<br>";
            }
            
            if(strpos($item['answer'], 'ePoint') !== false) {
                $faq[$key]['answer'] = str_replace('ePoint', $app_name, $item['answer']);
                $answer_updated = true;
                echo "✅ Updated Answer: " . $item['answer'] . " → " . $faq[$key]['answer'] . "<br>";
            }
            
            if($question_updated || $answer_updated) {
                $updated = true;
            }
        }
        
        if($updated) {
            $faq_json = json_encode($faq);
            $update_query = "UPDATE app_settings SET setting_value = ? WHERE setting_key = 'chatbot_faq'";
            $stmt = mysqli_prepare($koneksi, $update_query);
            mysqli_stmt_bind_param($stmt, "s", $faq_json);
            if(mysqli_stmt_execute($stmt)) {
                echo "<strong>✅ FAQ berhasil diupdate!</strong><br>";
            } else {
                echo "<strong>❌ Error updating FAQ: " . mysqli_error($koneksi) . "</strong><br>";
            }
        } else {
            echo "ℹ️ FAQ sudah menggunakan nama aplikasi yang benar.<br>";
        }
    }
} else {
    echo "ℹ️ FAQ belum ada di database.<br>";
}

// Update Welcome Message
echo "<h2>💬 Update Welcome Message</h2>";
$welcome_query = mysqli_query($koneksi, "SELECT setting_value FROM app_settings WHERE setting_key = 'chatbot_welcome_message'");
if($welcome_query && mysqli_num_rows($welcome_query) > 0) {
    $welcome_row = mysqli_fetch_assoc($welcome_query);
    $welcome_message = $welcome_row['setting_value'];
    
    if(strpos($welcome_message, 'ePoint') !== false) {
        $new_welcome = str_replace('ePoint', $app_name, $welcome_message);
        $update_query = "UPDATE app_settings SET setting_value = ? WHERE setting_key = 'chatbot_welcome_message'";
        $stmt = mysqli_prepare($koneksi, $update_query);
        mysqli_stmt_bind_param($stmt, "s", $new_welcome);
        if(mysqli_stmt_execute($stmt)) {
            echo "✅ Welcome Message berhasil diupdate!<br>";
            echo "Old: " . $welcome_message . "<br>";
            echo "New: " . $new_welcome . "<br>";
        } else {
            echo "❌ Error updating Welcome Message: " . mysqli_error($koneksi) . "<br>";
        }
    } else {
        echo "ℹ️ Welcome Message sudah menggunakan nama aplikasi yang benar.<br>";
    }
} else {
    echo "ℹ️ Welcome Message belum ada di database.<br>";
}

echo "<br><div style='background: #d4edda; padding: 15px; border-radius: 5px; color: #155724;'>";
echo "🎉 <strong>Update selesai!</strong> Data chatbot telah disesuaikan dengan nama aplikasi: <strong>$app_name</strong>";
echo "</div>";

echo "<br><h3>🚀 Langkah Selanjutnya:</h3>";
echo "1. <a href='admin/pengaturan_aplikasi.php'>Buka Pengaturan Aplikasi</a><br>";
echo "2. Klik tab '🤖 Pengaturan Chatbot' untuk melihat hasil<br>";
echo "3. <a href='index.php'>Test chatbot di halaman utama</a><br>";

echo "<br><a href='index.php'>← Kembali ke Halaman Utama</a>";
?>
