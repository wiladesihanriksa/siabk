<?php include 'header.php'; ?>

<div class="content-wrapper">

  <section class="content-header">
    <h1>
      Riwayat Pelanggaran Saya
      <small>Riwayat Point Pelanggaran Saya</small>
    </h1>
    <ol class="breadcrumb">
      <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
      <li class="active">Dashboard</li>
    </ol>
  </section>

  <section class="content">
    <div class="row">
      <section class="col-lg-12">       
        <div class="box box-primary">

          <div class="box-header">
            <h3 class="box-title">Tentang Siswa</h3>
          </div>
          <div class="box-body">
            <?php 
            $id = $_SESSION['id'];              
            $kelas = mysqli_query($koneksi, "select * from siswa, jurusan where siswa_jurusan=jurusan_id and siswa_id='$id'");
            $k = mysqli_fetch_assoc($kelas);
            $id_siswa = $k['siswa_id'];
            ?>

            <div class="table-responsive">
              <table class="table table-bordered">
                <tr>
                  <th width="15%">Nama Siswa</th>
                  <th width="1%">:</th>
                  <td><?php echo $k['siswa_nama'] ?></td>
                </tr>
                <tr>
                  <th>NIS</th>
                  <th width="1%">:</th>
                  <td><?php echo $k['siswa_nis'] ?></td>
                </tr>
                <tr>
                  <th>Jurusan</th>
                  <th width="1%">:</th>
                  <td><?php echo $k['jurusan_nama'] ?></td>
                </tr>
              </table>
            </div>

          </div>

        </div>
      </section>
    </div>

    <div class="row">
      <section class="col-lg-12">
        <div class="box box-primary">

          <div class="box-header">
            <h3 class="box-title">Riwayat Pelanggaran Saya</h3>
          </div>
          <div class="box-body">

            <div class="table-responsive">
              <table class="table table-bordered table-striped" id="table-datatable">
                <thead>
                  <tr>
                    <th width="1%">NO</th>
                    <th>WAKTU</th>
                    <th>KELAS</th>
                    <th>TAHUN AJARAN</th>
                    <th>PELANGGARAN</th>
                    <th class="text-center">POINT</th>
                  </tr>
                </thead>
                <tbody>
                  <?php 
                  $no=1;
                  $total = 0;
                  $id_saya = $_SESSION['id'];
                  $data = mysqli_query($koneksi,"SELECT * FROM siswa, pelanggaran, jurusan, input_pelanggaran, kelas, ta where ta_id=kelas_ta and jurusan_id=kelas_jurusan and input_pelanggaran.kelas=kelas_id and input_pelanggaran.siswa=siswa_id and input_pelanggaran.pelanggaran=pelanggaran_id and siswa_id='$id_saya' order by input_pelanggaran.id desc");
                  while($d = mysqli_fetch_array($data)){
                    ?>
                    <tr>
                      <td><?php echo $no++; ?></td>
                      <td><?php echo date('d-m-Y H:i:s', strtotime($d['waktu'])); ?></td>
                      <td><?php echo $d['kelas_nama']; ?></td>
                      <td><?php echo $d['ta_nama']; ?></td>
                      <td><?php echo $d['pelanggaran_nama']; ?></td>
                      <td class="text-center"><?php echo $d['pelanggaran_point']; ?></td>
                    </tr>
                    <?php
                    $total += $d['pelanggaran_point']; 
                  }
                  ?>
                  <tr>
                    <td colspan="5" class="text-center text-bold">TOTAL</td>
                    <td class="text-center text-bold bg-red"><?php echo $total ?></td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>

        </div>
      </section>
    </div>
  </section>

</div>
<?php include 'footer.php'; ?>