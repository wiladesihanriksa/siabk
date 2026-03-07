<?php
// memanggil library FPDF
require('../library/fpdf185/fpdf.php');

include '../koneksi.php';

// intance object dan memberikan pengaturan halaman PDF
$pdf=new FPDF('L','mm','A4');

$pdf->AddPage();

$pdf->SetFont('Arial','B',13);
$pdf->Cell(280,10,'LAPORAN POINT PRESTASI & PELANGGARAN SISWA',0,0,'C');

$tgl_dari = $_GET['tanggal_dari'];
$tgl_sampai = $_GET['tanggal_sampai'];
$urutkan = $_GET['urutkan'];

// Memberikan space kebawah agar tidak terlalu rapat
$pdf->Cell(10,15,'',0,1);
$pdf->SetFont('Arial','B',9);

$pdf->Cell(35,6,'DARI TANGGAL',0,0);
$pdf->Cell(5,6,':',0,0);
$pdf->Cell(35,6, date('d-m-Y', strtotime($tgl_dari)) ,0,0);
$pdf->Cell(10,6,'',0,1);
$pdf->Cell(35,6,'SAMPAI TANGGAL',0,0);
$pdf->Cell(5,6,':',0,0);
$pdf->Cell(35,6, date('d-m-Y', strtotime($tgl_sampai)) ,0,0);
$pdf->Cell(10,6,'',0,1);
$pdf->Cell(35,6,'URUTAN',0,0);
$pdf->Cell(5,6,':',0,0);
$pdf->Cell(35,6, $urutkan ,0,0);

$pdf->Cell(10,10,'',0,1);
$pdf->SetFont('Arial','B',9);

$pdf->Cell(14,7,'NO',1,0,'C');
$pdf->Cell(110,7,'NAMA SISWA' ,1,0,'C');
$pdf->Cell(50,7,'NIS',1,0,'C');
$pdf->Cell(50,7,'TOTAL PRESTASI',1,0,'C');
$pdf->Cell(50,7,'TOTAL PELANGGARAN',1,0,'C');

$pdf->Cell(10,7,'',0,1);
$pdf->SetFont('Arial','',10);


$no=1;
if(isset($_GET['urutkan'])){
  if($urutkan == ""){
    $u = "";
  }else if($urutkan == "prestasi_terbanyak"){
    $u = " order by total_prestasi desc";
  }else if($urutkan == "pelanggaran_terbanyak"){
    $u = " order by total_pelanggaran desc";
  }
}else{
  $u = "";
}

$data = mysqli_query($koneksi,"SELECT siswa_id, siswa_nama, siswa_nis, sum(prestasi_point) as total_prestasi, sum(pelanggaran_point) as total_pelanggaran FROM siswa, input_pelanggaran, input_prestasi, pelanggaran, prestasi WHERE input_prestasi.siswa=siswa_id and input_pelanggaran.siswa=siswa_id and input_pelanggaran.pelanggaran=pelanggaran_id and input_prestasi.prestasi=prestasi_id and (date(input_pelanggaran.waktu) >= '$tgl_dari' AND date(input_pelanggaran.waktu) <= '$tgl_sampai') or (date(input_prestasi.waktu) >= '$tgl_dari' AND date(input_prestasi.waktu) <= '$tgl_sampai') group by siswa_id $u");
while($d = mysqli_fetch_array($data)){
  $id_siswa = $d['siswa_id'];

  $pdf->Cell(14,6, $no++,1,0,'C');
  $pdf->Cell(110,6, $d['siswa_nama'],1,0,'C');
  $pdf->Cell(50,6, $d['siswa_nis'],1,0,'C');

  $jumlah_prestasi = mysqli_query($koneksi, "select sum(prestasi_point) as total from prestasi, input_prestasi where prestasi_id=input_prestasi.prestasi and input_prestasi.siswa='$id_siswa'");
  $j = mysqli_fetch_assoc($jumlah_prestasi);
  $pdf->Cell(50,6, $j['total'],1,0,'C');

  $jumlah_pelanggaran = mysqli_query($koneksi, "select sum(pelanggaran_point) as total from pelanggaran, input_pelanggaran where pelanggaran_id=input_pelanggaran.pelanggaran and input_pelanggaran.siswa='$id_siswa'");
  $j = mysqli_fetch_assoc($jumlah_pelanggaran);
  $pdf->Cell(50,6, $j['total'],1,1,'C');

}

$pdf->Output();

?>