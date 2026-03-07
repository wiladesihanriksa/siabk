<?php include 'header_dynamic.php'; ?>

<div class="content-wrapper">

  <section class="content-header">
    <h1>
     Input Prestasi
     <small>Input Prestasi</small>
   </h1>
   <ol class="breadcrumb">
    <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
    <li class="active">Dashboard</li>
  </ol>
</section>

<section class="content">
  <div class="row">
    <section class="col-lg-6">       
      <div class="box box-primary">

        <div class="box-header">
          <h3 class="box-title">Input Prestasi Baru</h3>
          <a href="input_prestasi.php" class="btn btn-primary btn-sm pull-right"><i class="fa fa-reply"></i> &nbsp Kembali</a> 
        </div>
        <div class="box-body">
          <form action="input_prestasi_act.php" method="post">

            <div class="form-group">
              <label>Tahun Ajaran</label>
              <select class="form-control pilih_ta" name="ta" required="required">
                <option value="">- Pilih Tahun Ajaran</option>
                <?php 
                $ta = mysqli_query($koneksi,"select * from ta");
                while($j = mysqli_fetch_array($ta)){
                  ?>
                  <option value="<?php echo $j['ta_id'] ?>"><?php echo $j['ta_nama'] ?> <?php if($j['ta_status'] == "1"){ echo "(Aktif)"; } ?></option>
                  <?php 
                }
                ?>
              </select>
            </div>

            <div class="form-group">
              <label>Pilih Kelas</label>
              <select class="form-control pilih_kelas" name="kelas" required="required">
                <option value="">- Pilih Kelas</option>
              </select>
            </div>

            <div class="form-group">
              <label>Siswa</label>
              <select class="form-control pilih_siswa select2" name="siswa" required="required" style="width: 100%;">
                <option value="">- Pilih Siswa</option>
              </select>
            </div>

            <div class="form-group">
              <label>Tanggal</label>
              <input type="date" class="form-control" name="tanggal" required="required">
            </div>

            <div class="form-group">
              <label>Jam</label>
              <input type="time" class="form-control" name="jam" required="required">
            </div>


            <div class="form-group">
              <label>Prestasi</label>
              <select class="form-control" name="prestasi" required="required">
                <option value=""> - Pilih Prestasi - </option>
                <?php 
                $prestasi = mysqli_query($koneksi,"select * from prestasi order by prestasi_nama asc");
                while($j = mysqli_fetch_array($prestasi)){
                  ?>
                  <option value="<?php echo $j['prestasi_id'] ?>"><?php echo $j['prestasi_nama'] ?> (<?php echo $j['prestasi_point'] ?> Point)</option>
                  <?php 
                }
                ?>
              </select>
            </div>

            <div class="form-group">
              <input type="submit" class="btn btn-sm btn-primary" value="Simpan">
            </div>
          </form>
        </div>

      </div>
    </section>
  </div>
</section>

</div>

<?php include 'footer.php'; ?>

<script>
$(document).ready(function(){
    // Inisialisasi Select2 untuk dropdown siswa
    $('.pilih_siswa').select2({
        placeholder: "Ketik untuk mencari siswa...",
        allowClear: true,
        width: '100%'
    });
    
    // Ketika tahun ajaran dipilih
    $('.pilih_ta').change(function(){
        var ta_id = $(this).val();
        
        // Reset dropdown kelas dan siswa
        $('.pilih_kelas').html('<option value="">- Pilih Kelas</option>');
        $('.pilih_siswa').html('<option value="">- Pilih Siswa</option>').trigger('change');
        
        if(ta_id != ''){
            // Load kelas berdasarkan tahun ajaran
            $.ajax({
                url: 'ajax_get_kelas.php',
                method: 'POST',
                data: {ta: ta_id},
                success: function(data){
                    $('.pilih_kelas').html(data);
                },
                error: function(xhr, status, error) {
                    $('.pilih_kelas').html('<option value="">Error loading kelas</option>');
                }
            });
        }
    });
    
    // Ketika kelas dipilih
    $('.pilih_kelas').change(function(){
        var kelas_id = $(this).val();
        
        // Reset dropdown siswa
        $('.pilih_siswa').html('<option value="">- Pilih Siswa</option>');
        
        if(kelas_id != ''){
            // Load siswa berdasarkan kelas
            $.ajax({
                url: 'ajax_get_siswa.php',
                method: 'POST',
                data: {kelas: kelas_id},
                success: function(data){
                    $('.pilih_siswa').html(data);
                    // Refresh Select2 setelah data diupdate
                    $('.pilih_siswa').trigger('change');
                },
                error: function(xhr, status, error) {
                    $('.pilih_siswa').html('<option value="">Error loading siswa</option>');
                    $('.pilih_siswa').trigger('change');
                }
            });
        } else {
            // Jika tidak ada kelas dipilih, reset Select2
            $('.pilih_siswa').trigger('change');
        }
    });
    
    // Auto-select tahun ajaran aktif jika ada
    var activeTa = $('.pilih_ta option:contains("(Aktif)")').val();
    if(activeTa) {
        $('.pilih_ta').val(activeTa).trigger('change');
    }
});
</script>