<?php
include 'header_dynamic.php';
include '../koneksi.php';

$id_user = $_SESSION['id'];

// Ambil data profil admin
$profil = mysqli_query($koneksi, "SELECT * FROM user WHERE user_id='$id_user'");
$profil = mysqli_fetch_assoc($profil);

// Proses update profil
if(isset($_POST['update_profil'])) {
    $nama = mysqli_real_escape_string($koneksi, $_POST['nama']);
    $username = mysqli_real_escape_string($koneksi, $_POST['username']);
    $email = mysqli_real_escape_string($koneksi, $_POST['email']);
    $telepon = mysqli_real_escape_string($koneksi, $_POST['telepon']);
    $alamat = mysqli_real_escape_string($koneksi, $_POST['alamat']);
    
    // Update data profil
    $update_query = "UPDATE user SET 
                     user_nama = '$nama',
                     user_username = '$username',
                     user_email = '$email',
                     user_telepon = '$telepon',
                     user_alamat = '$alamat'
                     WHERE user_id = '$id_user'";
    
    if(mysqli_query($koneksi, $update_query)) {
        echo "<script>alert('Profil berhasil diperbarui!'); window.location='profil.php';</script>";
    } else {
        echo "<script>alert('Gagal memperbarui profil!');</script>";
    }
}

// Proses upload foto profil
// Proses upload foto profil ke Supabase
if(isset($_POST['update_foto'])) {
    if(isset($_FILES['foto_profil']) && $_FILES['foto_profil']['error'] == 0) {
        $file_tmp = $_FILES['foto_profil']['tmp_name'];
        $file_name = $_FILES['foto_profil']['name'];
        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
        
        $allowed_ext = array('jpg', 'jpeg', 'png', 'gif');
        
        if(in_array($file_ext, $allowed_ext)) {
            // Konfigurasi Supabase dari Environment Variables
            $s_url = getenv('SUPABASE_URL');
            $s_key = getenv('SUPABASE_KEY'); // Gunakan Service Role Key
            $s_bucket = getenv('SUPABASE_BUCKET');
            
            $new_file_name = 'admin_' . $id_user . '_' . time() . '.' . $file_ext;
            $path_in_supabase = 'gambar/user/' . $new_file_name;

            // Baca file konten
            $file_content = file_get_contents($file_tmp);
            $mime_type = mime_content_type($file_tmp);

            // cURL untuk Upload ke Supabase Storage
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, rtrim($s_url, '/') . "/storage/v1/object/" . $s_bucket . "/" . $path_in_supabase);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $file_content);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                "Authorization: Bearer " . $s_key,
                "Content-Type: " . $mime_type
            ));

            $result = curl_exec($ch);
            $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            if($http_code == 200) {
                // Update database hanya dengan nama filenya
                $update_foto = "UPDATE user SET user_foto = '$new_file_name' WHERE user_id = '$id_user'";
                if(mysqli_query($koneksi, $update_foto)) {
                    echo "<script>alert('Foto profil berhasil diunggah ke Supabase!'); window.location='profil.php';</script>";
                }
            } else {
                echo "<script>alert('Gagal upload ke Cloud. Error: " . $http_code . "');</script>";
            }
        } else {
            echo "<script>alert('Format tidak didukung!');</script>";
        }
    }
}

// Reload data profil setelah update
$profil = mysqli_query($koneksi, "SELECT * FROM user WHERE user_id='$id_user'");
$profil = mysqli_fetch_assoc($profil);
?>

<div class="content-wrapper">
  <section class="content-header">
    <h1>
      Profil Saya
      <small>Kelola informasi profil Anda</small>
    </h1>
    <ol class="breadcrumb">
      <li><a href="index.php"><i class="fa fa-dashboard"></i> Home</a></li>
      <li class="active">Profil Saya</li>
    </ol>
  </section>

  <section class="content">
    <div class="row">
      <!-- Profil Card -->
      <div class="col-md-4">
        <div class="box box-primary">
          <div class="box-body box-profile">
			<div class="text-center">
			  <?php 
				$baseUrl = function_exists('getSupabaseBaseUrl') ? getSupabaseBaseUrl() : '../';
				if(!empty($profil['user_foto'])): 
				  // Jika user_foto berisi URL lengkap gunakan langsung, jika tidak gabungkan dengan base URL
				  $img_src = (strpos($profil['user_foto'], 'http') === 0) ? $profil['user_foto'] : $baseUrl . 'gambar/user/' . $profil['user_foto'];
			  ?>
				<img class="profile-user-img img-responsive img-circle" src="<?php echo $img_src; ?>" style="width: 100px; height: 100px; object-fit: cover;">
			  <?php else: ?>
				<img class="profile-user-img img-responsive img-circle" src="../gambar/sistem/user.png" style="width: 100px; height: 100px; object-fit: cover;">
			  <?php endif; ?>
			</div>

            <h3 class="profile-username text-center"><?php echo $profil['user_nama']; ?></h3>

            <p class="text-muted text-center"><?php echo ucfirst($profil['user_level']); ?></p>

            <ul class="list-group list-group-unbordered">
              <li class="list-group-item">
                <b>Username</b> <a class="pull-right"><?php echo $profil['user_username']; ?></a>
              </li>
              <li class="list-group-item">
                <b>ID User</b> <a class="pull-right"><?php echo $profil['user_id']; ?></a>
              </li>
              <li class="list-group-item">
                <b>Status</b> <a class="pull-right"><span class="label label-success">Aktif</span></a>
              </li>
            </ul>

            <a href="#" class="btn btn-primary btn-block" data-toggle="modal" data-target="#modal-foto">
              <b><i class="fa fa-camera"></i> Ganti Foto</b>
            </a>
          </div>
        </div>
      </div>

      <!-- Form Edit Profil -->
      <div class="col-md-8">
        <div class="box box-primary">
          <div class="box-header with-border">
            <h3 class="box-title">Edit Profil</h3>
          </div>
          
          <form method="POST">
            <div class="box-body">
              <div class="row">
                <div class="col-md-6">
                  <div class="form-group">
                    <label>Nama Lengkap</label>
                    <input type="text" class="form-control" name="nama" 
                           value="<?php echo htmlspecialchars($profil['user_nama']); ?>" required>
                  </div>
                </div>
                
                <div class="col-md-6">
                  <div class="form-group">
                    <label>Username</label>
                    <input type="text" class="form-control" name="username" 
                           value="<?php echo htmlspecialchars($profil['user_username']); ?>" required>
                  </div>
                </div>
              </div>
              
              <div class="row">
                <div class="col-md-6">
                  <div class="form-group">
                    <label>Email</label>
                    <input type="email" class="form-control" name="email" 
                           value="<?php echo htmlspecialchars($profil['user_email'] ?? ''); ?>" 
                           placeholder="Masukkan email">
                  </div>
                </div>
                
                <div class="col-md-6">
                  <div class="form-group">
                    <label>Telepon</label>
                    <input type="text" class="form-control" name="telepon" 
                           value="<?php echo htmlspecialchars($profil['user_telepon'] ?? ''); ?>" 
                           placeholder="Masukkan nomor telepon">
                  </div>
                </div>
              </div>
              
              <div class="form-group">
                <label>Alamat</label>
                <textarea class="form-control" name="alamat" rows="3" 
                          placeholder="Masukkan alamat lengkap"><?php echo htmlspecialchars($profil['user_alamat'] ?? ''); ?></textarea>
              </div>
            </div>
            
            <div class="box-footer">
              <button type="submit" name="update_profil" class="btn btn-primary">
                <i class="fa fa-save"></i> Simpan Perubahan
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

<!-- Modal Ganti Foto -->
<div class="modal fade" id="modal-foto">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
        <h4 class="modal-title">Ganti Foto Profil</h4>
      </div>
      
      <form method="POST" enctype="multipart/form-data">
        <div class="modal-body">
          <div class="form-group">
            <label>Pilih Foto Baru</label>
            <input type="file" class="form-control" name="foto_profil" accept="image/*" required>
            <p class="help-block">Format: JPG, PNG, GIF. Maksimal 5MB</p>
          </div>
        </div>
        
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Batal</button>
          <button type="submit" name="update_foto" class="btn btn-primary">
            <i class="fa fa-upload"></i> Upload Foto
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<?php include 'footer.php'; ?>
