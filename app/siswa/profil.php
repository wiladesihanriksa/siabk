<?php
include 'header.php';
include '../koneksi.php';

$id_user = $_SESSION['id'];

// Ambil data profil siswa
$profil = mysqli_query($koneksi, "SELECT s.*, j.jurusan_nama FROM siswa s 
                                  LEFT JOIN jurusan j ON s.siswa_jurusan = j.jurusan_id 
                                  WHERE s.siswa_id='$id_user'");
$profil = mysqli_fetch_assoc($profil);

// Proses update profil
if(isset($_POST['update_profil'])) {
    $nama = $_POST['nama'];
    $nis = $_POST['nis'];
    $telepon = $_POST['telepon'];
    $alamat = $_POST['alamat'];
    
    // Update data profil
    $update_query = "UPDATE siswa SET 
                     siswa_nama = '$nama',
                     siswa_nis = '$nis'
                     WHERE siswa_id = '$id_user'";
    
    if(mysqli_query($koneksi, $update_query)) {
        echo "<script>alert('Profil berhasil diperbarui!'); window.location='profil.php';</script>";
    } else {
        echo "<script>alert('Gagal memperbarui profil!');</script>";
    }
}

// Proses upload foto profil
if(isset($_POST['update_foto'])) {
    $upload_dir = '../gambar/user/';
    if (!file_exists($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }
    
    if(isset($_FILES['foto_profil']) && $_FILES['foto_profil']['error'] == 0) {
        $file_name = $_FILES['foto_profil']['name'];
        $file_tmp = $_FILES['foto_profil']['tmp_name'];
        $file_size = $_FILES['foto_profil']['size'];
        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
        
        $allowed_ext = array('jpg', 'jpeg', 'png', 'gif');
        
        if(in_array($file_ext, $allowed_ext)) {
            if($file_size < 5000000) { // 5MB
                $new_file_name = 'siswa_' . $id_user . '_' . time() . '.' . $file_ext;
                $upload_path = $upload_dir . $new_file_name;
                
                if(move_uploaded_file($file_tmp, $upload_path)) {
                    // Hapus foto lama jika ada
                    if(!empty($profil['siswa_foto']) && file_exists('../gambar/user/' . $profil['siswa_foto'])) {
                        unlink('../gambar/user/' . $profil['siswa_foto']);
                    }
                    
                    // Update database
                    $update_foto = "UPDATE siswa SET siswa_foto = '$new_file_name' WHERE siswa_id = '$id_user'";
                    if(mysqli_query($koneksi, $update_foto)) {
                        echo "<script>alert('Foto profil berhasil diperbarui!'); window.location='profil.php';</script>";
                    }
                } else {
                    echo "<script>alert('Gagal mengupload foto!');</script>";
                }
            } else {
                echo "<script>alert('Ukuran file terlalu besar! Maksimal 5MB.');</script>";
            }
        } else {
            echo "<script>alert('Format file tidak didukung! Gunakan JPG, PNG, atau GIF.');</script>";
        }
    }
}

// Reload data profil setelah update
$profil = mysqli_query($koneksi, "SELECT s.*, j.jurusan_nama FROM siswa s 
                                  LEFT JOIN jurusan j ON s.siswa_jurusan = j.jurusan_id 
                                  WHERE s.siswa_id='$id_user'");
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
              <?php if(!empty($profil['siswa_foto'])): ?>
                <img class="profile-user-img img-responsive img-circle" 
                     src="../gambar/user/<?php echo $profil['siswa_foto']; ?>" 
                     alt="User profile picture" style="width: 100px; height: 100px; object-fit: cover;">
              <?php else: ?>
                <img class="profile-user-img img-responsive img-circle" 
                     src="../gambar/sistem/user.png" 
                     alt="User profile picture" style="width: 100px; height: 100px; object-fit: cover;">
              <?php endif; ?>
            </div>

            <h3 class="profile-username text-center"><?php echo $profil['siswa_nama']; ?></h3>

            <p class="text-muted text-center">Siswa</p>

            <ul class="list-group list-group-unbordered">
              <li class="list-group-item">
                <b>NIS</b> <a class="pull-right"><?php echo $profil['siswa_nis']; ?></a>
              </li>
              <li class="list-group-item">
                <b>Jurusan</b> <a class="pull-right"><?php echo $profil['jurusan_nama'] ?? 'Tidak ada'; ?></a>
              </li>
              <li class="list-group-item">
                <b>Status</b> <a class="pull-right">
                  <span class="label label-<?php echo $profil['siswa_status'] == 'Aktif' ? 'success' : 'warning'; ?>">
                    <?php echo $profil['siswa_status']; ?>
                  </span>
                </a>
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
                           value="<?php echo htmlspecialchars($profil['siswa_nama']); ?>" required>
                  </div>
                </div>
                
                <div class="col-md-6">
                  <div class="form-group">
                    <label>NIS</label>
                    <input type="text" class="form-control" name="nis" 
                           value="<?php echo htmlspecialchars($profil['siswa_nis']); ?>" required>
                  </div>
                </div>
              </div>
              
              <div class="row">
                <div class="col-md-6">
                  <div class="form-group">
                    <label>Jurusan</label>
                    <input type="text" class="form-control" 
                           value="<?php echo htmlspecialchars($profil['jurusan_nama'] ?? 'Tidak ada'); ?>" 
                           readonly>
                    <p class="help-block">Jurusan tidak dapat diubah</p>
                  </div>
                </div>
                
                <div class="col-md-6">
                  <div class="form-group">
                    <label>Status</label>
                    <input type="text" class="form-control" 
                           value="<?php echo htmlspecialchars($profil['siswa_status']); ?>" 
                           readonly>
                    <p class="help-block">Status tidak dapat diubah</p>
                  </div>
                </div>
              </div>
              
              <div class="row">
                <div class="col-md-6">
                  <div class="form-group">
                    <label>Telepon</label>
                    <input type="text" class="form-control" name="telepon" 
                           value="<?php echo htmlspecialchars($profil['siswa_telepon'] ?? ''); ?>" 
                           placeholder="Masukkan nomor telepon">
                  </div>
                </div>
                
                <div class="col-md-6">
                  <div class="form-group">
                    <label>Email</label>
                    <input type="email" class="form-control" name="email" 
                           value="<?php echo htmlspecialchars($profil['siswa_email'] ?? ''); ?>" 
                           placeholder="Masukkan email">
                  </div>
                </div>
              </div>
              
              <div class="form-group">
                <label>Alamat</label>
                <textarea class="form-control" name="alamat" rows="3" 
                          placeholder="Masukkan alamat lengkap"><?php echo htmlspecialchars($profil['siswa_alamat'] ?? ''); ?></textarea>
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
