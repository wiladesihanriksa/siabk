<?php 
include 'header.php';
?>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <section class="content-header">
    <h1>
      <i class="fa fa-users"></i> Data Guru BK
      <small>Kelola Data Guru Bimbingan Konseling</small>
    </h1>
    <ol class="breadcrumb">
      <li><a href="index.php"><i class="fa fa-dashboard"></i> Home</a></li>
      <li><a href="#">Pengaturan</a></li>
      <li class="active">Guru BK</li>
    </ol>
  </section>

  <!-- Main content -->
  <section class="content">
    <!-- Info boxes -->
    <div class="row">
      <div class="col-lg-3 col-xs-6">
        <div class="small-box bg-aqua">
          <div class="inner">
            <h3><?php
            $query_total = "SELECT COUNT(*) as total FROM guru_bk";
            $result_total = mysqli_query($koneksi, $query_total);
            $total_guru = mysqli_fetch_assoc($result_total)['total'];
            echo $total_guru;
            ?></h3>
            <p>Total Guru BK</p>
          </div>
          <div class="icon">
            <i class="fa fa-users"></i>
          </div>
        </div>
      </div>
      
      <div class="col-lg-3 col-xs-6">
        <div class="small-box bg-green">
          <div class="inner">
            <h3><?php
            $query_aktif = "SELECT COUNT(*) as total FROM guru_bk WHERE status_guru_bk = 'Aktif'";
            $result_aktif = mysqli_query($koneksi, $query_aktif);
            $guru_aktif = mysqli_fetch_assoc($result_aktif)['total'];
            echo $guru_aktif;
            ?></h3>
            <p>Guru BK Aktif</p>
          </div>
          <div class="icon">
            <i class="fa fa-check"></i>
          </div>
        </div>
      </div>
      
      <div class="col-lg-3 col-xs-6">
        <div class="small-box bg-yellow">
          <div class="inner">
            <h3><?php
            $query_tidak_aktif = "SELECT COUNT(*) as total FROM guru_bk WHERE status_guru_bk = 'Tidak Aktif'";
            $result_tidak_aktif = mysqli_query($koneksi, $query_tidak_aktif);
            $guru_tidak_aktif = mysqli_fetch_assoc($result_tidak_aktif)['total'];
            echo $guru_tidak_aktif;
            ?></h3>
            <p>Guru BK Tidak Aktif</p>
          </div>
          <div class="icon">
            <i class="fa fa-pause"></i>
          </div>
        </div>
      </div>
      
      <div class="col-lg-3 col-xs-6">
        <div class="small-box bg-red">
          <div class="inner">
            <h3><?php
            $query_kasus = "SELECT COUNT(*) as total FROM kasus_siswa ks JOIN guru_bk g ON ks.guru_bk_id = g.guru_bk_id WHERE g.status_guru_bk = 'Aktif'";
            $result_kasus = mysqli_query($koneksi, $query_kasus);
            $total_kasus = mysqli_fetch_assoc($result_kasus)['total'];
            echo $total_kasus;
            ?></h3>
            <p>Total Kasus</p>
          </div>
          <div class="icon">
            <i class="fa fa-folder"></i>
          </div>
        </div>
      </div>
    </div>

    <!-- Data Guru BK -->
    <div class="row">
      <div class="col-xs-12">
        <div class="box box-primary">
          <div class="box-header with-border">
            <h3 class="box-title">Daftar Guru BK</h3>
            <div class="box-tools pull-right">
              <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#modalTambahGuru">
                <i class="fa fa-plus"></i> Tambah Guru BK
              </button>
            </div>
          </div>
          <div class="box-body">
            <div class="table-responsive">
              <table id="tableGuruBK" class="table table-bordered table-striped">
                <thead>
                  <tr>
                    <th width="5%">No</th>
                    <th width="15%">Nama Guru BK</th>
                    <th width="12%">NIP</th>
                    <th width="15%">Email</th>
                    <th width="12%">Telepon</th>
                    <th width="10%">Jabatan</th>
                    <th width="8%">Status</th>
                    <th width="10%">Total Kasus</th>
                    <th width="13%">Aksi</th>
                  </tr>
                </thead>
                <tbody>
                  <?php
                  $query = "SELECT g.*, 
                           (SELECT COUNT(*) FROM kasus_siswa ks WHERE ks.guru_bk_id = g.guru_bk_id) as total_kasus
                           FROM guru_bk g 
                           ORDER BY g.nama_guru_bk ASC";
                  $result = mysqli_query($koneksi, $query);
                  $no = 1;
                  while($row = mysqli_fetch_assoc($result)) {
                  ?>
                  <tr>
                    <td><?php echo $no++; ?></td>
                    <td><?php echo $row['nama_guru_bk']; ?></td>
                    <td><?php echo $row['nip_guru_bk']; ?></td>
                    <td><?php echo $row['email_guru_bk']; ?></td>
                    <td><?php echo $row['telepon_guru_bk']; ?></td>
                    <td><?php echo $row['jabatan_guru_bk']; ?></td>
                    <td>
                      <?php
                      if($row['status_guru_bk'] == 'Aktif') {
                        echo '<span class="label label-success">Aktif</span>';
                      } else {
                        echo '<span class="label label-danger">Tidak Aktif</span>';
                      }
                      ?>
                    </td>
                    <td>
                      <span class="badge bg-blue"><?php echo $row['total_kasus']; ?></span>
                    </td>
                    <td>
                      <button type="button" class="btn btn-info btn-xs edit-guru" 
                              data-id="<?php echo htmlspecialchars($row['guru_bk_id']); ?>"
                              data-nama="<?php echo htmlspecialchars($row['nama_guru_bk']); ?>"
                              data-nip="<?php echo htmlspecialchars($row['nip_guru_bk'] ?? ''); ?>"
                              data-email="<?php echo htmlspecialchars($row['email_guru_bk'] ?? ''); ?>"
                              data-telepon="<?php echo htmlspecialchars($row['telepon_guru_bk'] ?? ''); ?>"
                              data-alamat="<?php echo htmlspecialchars($row['alamat_guru_bk'] ?? ''); ?>"
                              data-jabatan="<?php echo htmlspecialchars($row['jabatan_guru_bk'] ?? 'Guru BK'); ?>"
                              data-status="<?php echo htmlspecialchars($row['status_guru_bk'] ?? 'Aktif'); ?>">
                        <i class="fa fa-edit"></i> Edit
                      </button>
                      <button type="button" class="btn btn-danger btn-xs hapus-guru" 
                              data-id="<?php echo htmlspecialchars($row['guru_bk_id']); ?>"
                              data-nama="<?php echo htmlspecialchars($row['nama_guru_bk']); ?>">
                        <i class="fa fa-trash"></i> Hapus
                      </button>
                    </td>
                  </tr>
                  <?php } ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
</div>

<!-- Modal Tambah Guru BK -->
<div class="modal fade" id="modalTambahGuru" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Tambah Guru BK</h4>
      </div>
      <form id="formTambahGuru" method="post">
        <div class="modal-body">
          <div class="form-group">
            <label>Nama Guru BK</label>
            <input type="text" name="nama_guru_bk" class="form-control" required>
          </div>
          <div class="form-group">
            <label>NIP</label>
            <input type="text" name="nip_guru_bk" class="form-control">
          </div>
          <div class="form-group">
            <label>Email</label>
            <input type="email" name="email_guru_bk" class="form-control">
          </div>
          <div class="form-group">
            <label>Telepon</label>
            <input type="text" name="telepon_guru_bk" class="form-control">
          </div>
          <div class="form-group">
            <label>Alamat</label>
            <textarea name="alamat_guru_bk" class="form-control" rows="3"></textarea>
          </div>
          <div class="form-group">
            <label>Jabatan</label>
            <input type="text" name="jabatan_guru_bk" class="form-control" value="Guru BK">
          </div>
          <div class="form-group">
            <label>Status</label>
            <select name="status_guru_bk" class="form-control" required>
              <option value="Aktif">Aktif</option>
              <option value="Tidak Aktif">Tidak Aktif</option>
            </select>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-primary">Simpan</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Modal Edit Guru BK -->
<div class="modal fade" id="modalEditGuru" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Edit Guru BK</h4>
      </div>
      <form id="formEditGuru" method="post">
        <input type="hidden" name="guru_bk_id" id="edit_guru_bk_id">
        <div class="modal-body">
          <div class="form-group">
            <label>Nama Guru BK</label>
            <input type="text" name="nama_guru_bk" id="edit_nama_guru_bk" class="form-control" required>
          </div>
          <div class="form-group">
            <label>NIP</label>
            <input type="text" name="nip_guru_bk" id="edit_nip_guru_bk" class="form-control">
          </div>
          <div class="form-group">
            <label>Email</label>
            <input type="email" name="email_guru_bk" id="edit_email_guru_bk" class="form-control">
          </div>
          <div class="form-group">
            <label>Telepon</label>
            <input type="text" name="telepon_guru_bk" id="edit_telepon_guru_bk" class="form-control">
          </div>
          <div class="form-group">
            <label>Alamat</label>
            <textarea name="alamat_guru_bk" id="edit_alamat_guru_bk" class="form-control" rows="3"></textarea>
          </div>
          <div class="form-group">
            <label>Jabatan</label>
            <input type="text" name="jabatan_guru_bk" id="edit_jabatan_guru_bk" class="form-control">
          </div>
          <div class="form-group">
            <label>Status</label>
            <select name="status_guru_bk" id="edit_status_guru_bk" class="form-control" required>
              <option value="Aktif">Aktif</option>
              <option value="Tidak Aktif">Tidak Aktif</option>
            </select>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-primary">Update</button>
        </div>
      </form>
    </div>
  </div>
</div>

<?php include 'footer.php'; ?>

<script>
$(document).ready(function() {
    // Initialize DataTable
    $('#tableGuruBK').DataTable({
        "responsive": true,
        "autoWidth": false,
        "pageLength": 10
    });

    // Handle form tambah guru
    $('#formTambahGuru').on('submit', function(e) {
        e.preventDefault();
        
        var formData = $(this).serialize() + '&action=tambah';
        
        $.ajax({
            url: 'guru_bk_act.php',
            method: 'POST',
            data: formData,
            dataType: 'json',
            success: function(data) {
                if(data.success) {
                    alert('Guru BK berhasil ditambahkan!\n' + (data.message || ''));
                    $('#modalTambahGuru').modal('hide');
                    location.reload();
                } else {
                    alert('Error: ' + (data.message || 'Terjadi kesalahan'));
                }
            },
            error: function(xhr, status, error) {
                console.error('AJAX Error:', status, error);
                console.error('Response:', xhr.responseText);
                try {
                    var data = JSON.parse(xhr.responseText);
                    alert('Error: ' + (data.message || 'Terjadi kesalahan pada server'));
                } catch(e) {
                    alert('Terjadi kesalahan pada server!\n' + xhr.responseText);
                }
            }
        });
    });

    // Handle form edit guru
    $('#formEditGuru').on('submit', function(e) {
        e.preventDefault();
        
        var formData = $(this).serialize() + '&action=edit';
        
        $.ajax({
            url: 'guru_bk_act.php',
            method: 'POST',
            data: formData,
            dataType: 'json',
            success: function(data) {
                if(data.success) {
                    alert('Guru BK berhasil diupdate!');
                    $('#modalEditGuru').modal('hide');
                    location.reload();
                } else {
                    alert('Error: ' + (data.message || 'Terjadi kesalahan'));
                }
            },
            error: function(xhr, status, error) {
                console.error('AJAX Error:', status, error);
                console.error('Response:', xhr.responseText);
                try {
                    var data = JSON.parse(xhr.responseText);
                    alert('Error: ' + (data.message || 'Terjadi kesalahan pada server'));
                } catch(e) {
                    alert('Terjadi kesalahan pada server!\n' + xhr.responseText);
                }
            }
        });
    });

    // Handle edit button (using event delegation for dynamically created elements)
    $(document).on('click', '.edit-guru', function() {
        var id = $(this).data('id');
        var nama = $(this).data('nama');
        var nip = $(this).data('nip') || '';
        var email = $(this).data('email') || '';
        var telepon = $(this).data('telepon') || '';
        var alamat = $(this).data('alamat') || '';
        var jabatan = $(this).data('jabatan') || 'Guru BK';
        var status = $(this).data('status') || 'Aktif';

        $('#edit_guru_bk_id').val(id);
        $('#edit_nama_guru_bk').val(nama);
        $('#edit_nip_guru_bk').val(nip);
        $('#edit_email_guru_bk').val(email);
        $('#edit_telepon_guru_bk').val(telepon);
        $('#edit_alamat_guru_bk').val(alamat);
        $('#edit_jabatan_guru_bk').val(jabatan);
        $('#edit_status_guru_bk').val(status);

        $('#modalEditGuru').modal('show');
    });

    // Handle hapus button (using event delegation for dynamically created elements)
    $(document).on('click', '.hapus-guru', function() {
        var id = $(this).data('id');
        var nama = $(this).data('nama');
        
        if(confirm('Apakah Anda yakin ingin menghapus Guru BK "' + nama + '"?')) {
            $.ajax({
                url: 'guru_bk_act.php',
                method: 'POST',
                data: {action: 'hapus', guru_bk_id: id},
                dataType: 'json',
                success: function(data) {
                    if(data.success) {
                        alert('Guru BK berhasil dihapus!');
                        location.reload();
                    } else {
                        alert('Error: ' + (data.message || 'Terjadi kesalahan'));
                    }
                },
                error: function(xhr, status, error) {
                    console.error('AJAX Error:', status, error);
                    console.error('Response:', xhr.responseText);
                    try {
                        var data = JSON.parse(xhr.responseText);
                        alert('Error: ' + (data.message || 'Terjadi kesalahan pada server'));
                    } catch(e) {
                        alert('Terjadi kesalahan pada server!\n' + xhr.responseText);
                    }
                }
            });
        }
    });
});
</script>
