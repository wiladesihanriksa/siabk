<?php
// Fungsi untuk mengambil pengaturan warna
function getColorSettings($koneksi) {
    $colors = array();
    $query = mysqli_query($koneksi, "SELECT setting_key, setting_value FROM app_settings WHERE setting_key LIKE '%_color' OR setting_key LIKE '%_skin'");
    while($row = mysqli_fetch_assoc($query)) {
        $colors[$row['setting_key']] = $row['setting_value'];
    }
    return $colors;
}

// Fungsi untuk mendapatkan nilai warna dengan default
function getColor($colors, $key, $default = '#3c8dbc') {
    return isset($colors[$key]) ? $colors[$key] : $default;
}

// Fungsi untuk generate CSS dinamis
function generateDynamicCSS($colors) {
    $primary = getColor($colors, 'primary_color', '#3c8dbc');
    $secondary = getColor($colors, 'secondary_color', '#f39c12');
    $success = getColor($colors, 'success_color', '#00a65a');
    $warning = getColor($colors, 'warning_color', '#f39c12');
    $danger = getColor($colors, 'danger_color', '#dd4b39');
    $info = getColor($colors, 'info_color', '#3c8dbc');
    $sidebar = getColor($colors, 'sidebar_color', '#222d32');
    $sidebar_hover = getColor($colors, 'sidebar_hover', '#1e282c');
    $text = getColor($colors, 'text_color', '#333333');
    $background = getColor($colors, 'background_color', '#f4f4f4');
    
    $css = "
    <style>
    /* Dynamic Color Variables */
    :root {
        --primary-color: {$primary};
        --secondary-color: {$secondary};
        --success-color: {$success};
        --warning-color: {$warning};
        --danger-color: {$danger};
        --info-color: {$info};
        --sidebar-color: {$sidebar};
        --sidebar-hover: {$sidebar_hover};
        --text-color: {$text};
        --background-color: {$background};
    }
    
    /* Primary Color Overrides */
    .btn-primary, .btn-primary:hover, .btn-primary:focus {
        background-color: {$primary} !important;
        border-color: {$primary} !important;
    }
    
    .bg-primary {
        background-color: {$primary} !important;
    }
    
    .text-primary {
        color: {$primary} !important;
    }
    
    /* Secondary Color */
    .btn-secondary, .btn-secondary:hover, .btn-secondary:focus {
        background-color: {$secondary} !important;
        border-color: {$secondary} !important;
    }
    
    /* Success Color */
    .btn-success, .btn-success:hover, .btn-success:focus {
        background-color: {$success} !important;
        border-color: {$success} !important;
    }
    
    .bg-success {
        background-color: {$success} !important;
    }
    
    /* Warning Color */
    .btn-warning, .btn-warning:hover, .btn-warning:focus {
        background-color: {$warning} !important;
        border-color: {$warning} !important;
    }
    
    .bg-warning {
        background-color: {$warning} !important;
    }
    
    /* Danger Color */
    .btn-danger, .btn-danger:hover, .btn-danger:focus {
        background-color: {$danger} !important;
        border-color: {$danger} !important;
    }
    
    .bg-danger {
        background-color: {$danger} !important;
    }
    
    /* Info Color */
    .btn-info, .btn-info:hover, .btn-info:focus {
        background-color: {$info} !important;
        border-color: {$info} !important;
    }
    
    .bg-info {
        background-color: {$info} !important;
    }
    
    /* Sidebar Colors */
    .main-sidebar {
        background-color: {$sidebar} !important;
    }
    
    .sidebar-menu > li > a:hover {
        background-color: {$sidebar_hover} !important;
    }
    
    .sidebar-menu > li.active > a {
        background-color: {$primary} !important;
    }
    
    /* Header Colors */
    .main-header .navbar {
        background-color: {$primary} !important;
    }
    
    .main-header .logo {
        background-color: {$primary} !important;
    }
    
    /* Small Box Colors */
    .small-box.bg-primary {
        background-color: {$primary} !important;
    }
    
    .small-box.bg-secondary {
        background-color: {$secondary} !important;
    }
    
    .small-box.bg-success {
        background-color: {$success} !important;
    }
    
    .small-box.bg-warning {
        background-color: {$warning} !important;
    }
    
    .small-box.bg-danger {
        background-color: {$danger} !important;
    }
    
    .small-box.bg-info {
        background-color: {$info} !important;
    }
    
    /* Box Colors */
    .box-primary {
        border-top-color: {$primary} !important;
    }
    
    .box-success {
        border-top-color: {$success} !important;
    }
    
    .box-warning {
        border-top-color: {$warning} !important;
    }
    
    .box-danger {
        border-top-color: {$danger} !important;
    }
    
    .box-info {
        border-top-color: {$info} !important;
    }
    
    /* Text Colors */
    body {
        color: {$text} !important;
        background-color: {$background} !important;
    }
    
    /* Login Page Colors */
    .login-box-body {
        background-color: {$primary} !important;
    }
    
    .login-box-msg {
        color: white !important;
    }
    
    /* Progress Bar */
    .progress-bar {
        background-color: {$primary} !important;
    }
    
    /* Label Colors */
    .label-primary {
        background-color: {$primary} !important;
    }
    
    .label-success {
        background-color: {$success} !important;
    }
    
    .label-warning {
        background-color: {$warning} !important;
    }
    
    .label-danger {
        background-color: {$danger} !important;
    }
    
    .label-info {
        background-color: {$info} !important;
    }
    </style>
    ";
    
    return $css;
}

// Fungsi untuk mendapatkan skin class
function getSkinClass($colors) {
    $header_skin = getColor($colors, 'header_skin', 'skin-blue');
    $sidebar_skin = getColor($colors, 'sidebar_skin', 'skin-blue');
    return $header_skin . ' ' . $sidebar_skin;
}

// Fungsi untuk convert hex ke rgb
function hex2rgb($hex) {
    $hex = str_replace("#", "", $hex);
    $r = hexdec(substr($hex, 0, 2));
    $g = hexdec(substr($hex, 2, 2));
    $b = hexdec(substr($hex, 4, 2));
    return "rgb($r, $g, $b)";
}

// Fungsi untuk generate color palette
function generateColorPalette($base_color) {
    $colors = array();
    $colors['base'] = $base_color;
    $colors['light'] = adjustBrightness($base_color, 0.3);
    $colors['dark'] = adjustBrightness($base_color, -0.3);
    $colors['hover'] = adjustBrightness($base_color, -0.1);
    return $colors;
}

// Fungsi untuk adjust brightness
function adjustBrightness($hex, $percent) {
    $hex = str_replace('#', '', $hex);
    $r = hexdec(substr($hex, 0, 2));
    $g = hexdec(substr($hex, 2, 2));
    $b = hexdec(substr($hex, 4, 2));
    
    $r = max(0, min(255, $r + ($r * $percent)));
    $g = max(0, min(255, $g + ($g * $percent)));
    $b = max(0, min(255, $b + ($b * $percent)));
    
    return '#' . str_pad(dechex($r), 2, '0', STR_PAD_LEFT) . 
                 str_pad(dechex($g), 2, '0', STR_PAD_LEFT) . 
                 str_pad(dechex($b), 2, '0', STR_PAD_LEFT);
}
?>
