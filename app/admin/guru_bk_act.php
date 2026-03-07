<?php
include '../koneksi.php';
header('Content-Type: application/json');

if($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Method tidak diizinkan']);
    exit();
}

$action = isset($_POST['action']) ? $_POST['action'] : '';

try {
    switch($action) {
        case 'tambah':
            // Tambah guru BK baru
            $nama_guru_bk = mysqli_real_escape_string($koneksi, $_POST['nama_guru_bk']);
            $nip_guru_bk = mysqli_real_escape_string($koneksi, $_POST['nip_guru_bk']);
            $email_guru_bk = mysqli_real_escape_string($koneksi, $_POST['email_guru_bk']);
            $telepon_guru_bk = mysqli_real_escape_string($koneksi, $_POST['telepon_guru_bk']);
            $alamat_guru_bk = mysqli_real_escape_string($koneksi, $_POST['alamat_guru_bk']);
            $jabatan_guru_bk = mysqli_real_escape_string($koneksi, $_POST['jabatan_guru_bk']);
            $status_guru_bk = mysqli_real_escape_string($koneksi, $_POST['status_guru_bk']);

            // Validasi data
            if(empty($nama_guru_bk)) {
                throw new Exception('Nama guru BK tidak boleh kosong');
            }

            // Cek apakah NIP sudah ada (jika diisi)
            if(!empty($nip_guru_bk)) {
                $cek_nip = mysqli_query($koneksi, "SELECT guru_bk_id FROM guru_bk WHERE nip_guru_bk = '$nip_guru_bk'");
                if(mysqli_num_rows($cek_nip) > 0) {
                    throw new Exception('NIP sudah digunakan oleh guru BK lain');
                }
            }

            // Buat username dari nama (lowercase, replace space dengan underscore)
            $username_guru = strtolower(str_replace(' ', '_', $nama_guru_bk));
            $username_guru = preg_replace('/[^a-z0-9_]/', '', $username_guru);
            
            // Cek apakah username sudah ada
            $cek_username = mysqli_query($koneksi, "SELECT user_id FROM user WHERE user_username = '$username_guru'");
            if(mysqli_num_rows($cek_username) > 0) {
                $username_guru .= '_' . time(); // Tambahkan timestamp jika username sudah ada
            }
            
            // Password default: "gurubk123"
            $password_default = md5('gurubk123');
            
            // Cek apakah kolom user_email, user_telepon, user_alamat ada di tabel user
            $check_columns = mysqli_query($koneksi, "SHOW COLUMNS FROM user LIKE 'user_email'");
            $has_email = mysqli_num_rows($check_columns) > 0;
            
            $check_columns = mysqli_query($koneksi, "SHOW COLUMNS FROM user LIKE 'user_telepon'");
            $has_telepon = mysqli_num_rows($check_columns) > 0;
            
            $check_columns = mysqli_query($koneksi, "SHOW COLUMNS FROM user LIKE 'user_alamat'");
            $has_alamat = mysqli_num_rows($check_columns) > 0;
            
            // Build INSERT query berdasarkan kolom yang tersedia
            $user_columns = ['user_nama', 'user_username', 'user_password', 'user_level'];
            $user_values = ["'$nama_guru_bk'", "'$username_guru'", "'$password_default'", "'guru_bk'"];
            
            if($has_email) {
                $user_columns[] = 'user_email';
                $user_values[] = "'$email_guru_bk'";
            }
            if($has_telepon) {
                $user_columns[] = 'user_telepon';
                $user_values[] = "'$telepon_guru_bk'";
            }
            if($has_alamat) {
                $user_columns[] = 'user_alamat';
                $user_values[] = "'$alamat_guru_bk'";
            }
            
            $insert_user = "INSERT INTO user (" . implode(', ', $user_columns) . ") 
                           VALUES (" . implode(', ', $user_values) . ")";
            
            if(mysqli_query($koneksi, $insert_user)) {
                $user_id = mysqli_insert_id($koneksi);
                
                // Insert ke tabel guru_bk
                $insert_query = "INSERT INTO guru_bk (nama_guru_bk, nip_guru_bk, email_guru_bk, telepon_guru_bk, alamat_guru_bk, jabatan_guru_bk, status_guru_bk, user_id) 
                                VALUES ('$nama_guru_bk', '$nip_guru_bk', '$email_guru_bk', '$telepon_guru_bk', '$alamat_guru_bk', '$jabatan_guru_bk', '$status_guru_bk', '$user_id')";
                
                if(mysqli_query($koneksi, $insert_query)) {
                    echo json_encode([
                        'success' => true, 
                        'message' => 'Guru BK berhasil ditambahkan! Username: ' . $username_guru . ', Password: gurubk123'
                    ]);
                } else {
                    // Rollback user jika guru_bk insert gagal
                    mysqli_query($koneksi, "DELETE FROM user WHERE user_id = '$user_id'");
                    throw new Exception('Gagal menambahkan guru BK: ' . mysqli_error($koneksi));
                }
            } else {
                $error_msg = mysqli_error($koneksi);
                throw new Exception('Gagal membuat akun login: ' . $error_msg);
            }
            break;

        case 'edit':
            // Edit guru BK
            $guru_bk_id = intval($_POST['guru_bk_id']);
            $nama_guru_bk = mysqli_real_escape_string($koneksi, $_POST['nama_guru_bk']);
            $nip_guru_bk = mysqli_real_escape_string($koneksi, $_POST['nip_guru_bk']);
            $email_guru_bk = mysqli_real_escape_string($koneksi, $_POST['email_guru_bk']);
            $telepon_guru_bk = mysqli_real_escape_string($koneksi, $_POST['telepon_guru_bk']);
            $alamat_guru_bk = mysqli_real_escape_string($koneksi, $_POST['alamat_guru_bk']);
            $jabatan_guru_bk = mysqli_real_escape_string($koneksi, $_POST['jabatan_guru_bk']);
            $status_guru_bk = mysqli_real_escape_string($koneksi, $_POST['status_guru_bk']);

            // Validasi data
            if(empty($nama_guru_bk)) {
                throw new Exception('Nama guru BK tidak boleh kosong');
            }

            // Cek apakah NIP sudah ada (jika diisi dan berbeda dari yang lama)
            if(!empty($nip_guru_bk)) {
                $cek_nip = mysqli_query($koneksi, "SELECT guru_bk_id FROM guru_bk WHERE nip_guru_bk = '$nip_guru_bk' AND guru_bk_id != '$guru_bk_id'");
                if(mysqli_num_rows($cek_nip) > 0) {
                    throw new Exception('NIP sudah digunakan oleh guru BK lain');
                }
            }

            // Ambil user_id dari guru_bk
            $get_user_id = mysqli_query($koneksi, "SELECT user_id FROM guru_bk WHERE guru_bk_id = '$guru_bk_id'");
            $guru_data = mysqli_fetch_assoc($get_user_id);
            $user_id = $guru_data['user_id'];
            
            // Update tabel guru_bk
            $update_query = "UPDATE guru_bk SET 
                            nama_guru_bk = '$nama_guru_bk',
                            nip_guru_bk = '$nip_guru_bk',
                            email_guru_bk = '$email_guru_bk',
                            telepon_guru_bk = '$telepon_guru_bk',
                            alamat_guru_bk = '$alamat_guru_bk',
                            jabatan_guru_bk = '$jabatan_guru_bk',
                            status_guru_bk = '$status_guru_bk',
                            updated_at = NOW()
                            WHERE guru_bk_id = '$guru_bk_id'";
            
            if(mysqli_query($koneksi, $update_query)) {
                // Update tabel user juga untuk sinkronisasi data
                if($user_id) {
                    // Cek kolom yang tersedia
                    $check_columns = mysqli_query($koneksi, "SHOW COLUMNS FROM user LIKE 'user_email'");
                    $has_email = mysqli_num_rows($check_columns) > 0;
                    
                    $check_columns = mysqli_query($koneksi, "SHOW COLUMNS FROM user LIKE 'user_telepon'");
                    $has_telepon = mysqli_num_rows($check_columns) > 0;
                    
                    $check_columns = mysqli_query($koneksi, "SHOW COLUMNS FROM user LIKE 'user_alamat'");
                    $has_alamat = mysqli_num_rows($check_columns) > 0;
                    
                    $update_fields = ["user_nama = '$nama_guru_bk'"];
                    if($has_email) $update_fields[] = "user_email = '$email_guru_bk'";
                    if($has_telepon) $update_fields[] = "user_telepon = '$telepon_guru_bk'";
                    if($has_alamat) $update_fields[] = "user_alamat = '$alamat_guru_bk'";
                    
                    $update_user = "UPDATE user SET " . implode(', ', $update_fields) . " WHERE user_id = '$user_id'";
                    mysqli_query($koneksi, $update_user);
                }
                
                echo json_encode(['success' => true, 'message' => 'Guru BK berhasil diupdate']);
            } else {
                throw new Exception('Gagal mengupdate guru BK: ' . mysqli_error($koneksi));
            }
            break;

        case 'hapus':
            // Hapus guru BK
            $guru_bk_id = intval($_POST['guru_bk_id']);

            // Cek apakah guru BK masih digunakan di kasus_siswa
            $cek_kasus = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM kasus_siswa WHERE guru_bk_id = '$guru_bk_id'");
            $kasus_count = mysqli_fetch_assoc($cek_kasus)['total'];
            
            if($kasus_count > 0) {
                throw new Exception('Guru BK tidak dapat dihapus karena masih memiliki ' . $kasus_count . ' kasus');
            }

            // Ambil user_id dari guru_bk sebelum hapus
            $get_user_id = mysqli_query($koneksi, "SELECT user_id FROM guru_bk WHERE guru_bk_id = '$guru_bk_id'");
            $guru_data = mysqli_fetch_assoc($get_user_id);
            $user_id = $guru_data['user_id'];
            
            $delete_query = "DELETE FROM guru_bk WHERE guru_bk_id = '$guru_bk_id'";
            
            if(mysqli_query($koneksi, $delete_query)) {
                // Hapus user account juga jika ada
                if($user_id) {
                    mysqli_query($koneksi, "DELETE FROM user WHERE user_id = '$user_id'");
                }
                
                echo json_encode(['success' => true, 'message' => 'Guru BK berhasil dihapus']);
            } else {
                throw new Exception('Gagal menghapus guru BK: ' . mysqli_error($koneksi));
            }
            break;

        default:
            throw new Exception('Action tidak valid');
    }

} catch(Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}

mysqli_close($koneksi);
?>
