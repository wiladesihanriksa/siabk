<?php
// Helper untuk menampilkan alert/notifikasi

function showAlert() {
    if(isset($_GET['alert'])) {
        $alert = $_GET['alert'];
        $pesan = isset($_GET['pesan']) ? $_GET['pesan'] : '';
        
        switch($alert) {
            case 'sukses':
                echo '<div class="alert alert-success alert-dismissible">
                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                        <h4><i class="icon fa fa-check"></i> Berhasil!</h4>
                        ' . htmlspecialchars($pesan) . '
                      </div>';
                break;
            case 'gagal':
                echo '<div class="alert alert-danger alert-dismissible">
                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                        <h4><i class="icon fa fa-ban"></i> Error!</h4>
                        ' . htmlspecialchars($pesan) . '
                      </div>';
                break;
            case 'info':
                echo '<div class="alert alert-info alert-dismissible">
                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                        <h4><i class="icon fa fa-info"></i> Info!</h4>
                        ' . htmlspecialchars($pesan) . '
                      </div>';
                break;
            case 'warning':
                echo '<div class="alert alert-warning alert-dismissible">
                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                        <h4><i class="icon fa fa-warning"></i> Peringatan!</h4>
                        ' . htmlspecialchars($pesan) . '
                      </div>';
                break;
        }
    }
}
?>
