<?php
/**
 * Download Template Excel untuk Import Data Prestasi - Versi Sederhana
 */

include '../koneksi.php';

// Set header untuk download file Excel
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment; filename="template_import_prestasi.xlsx"');
header('Cache-Control: max-age=0');

// Ambil data prestasi dari database
$prestasi_query = mysqli_query($koneksi, "SELECT prestasi_id, prestasi_nama, prestasi_point FROM prestasi ORDER BY prestasi_id");
$prestasi_data = array();
while($p = mysqli_fetch_assoc($prestasi_query)) {
    $prestasi_data[] = $p;
}

// Buat file Excel sederhana menggunakan XML
$excelContent = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships">
  <sheetData>
    <row r="1">
      <c r="A1" t="s"><v>0</v></c>
      <c r="B1" t="s"><v>1</v></c>
      <c r="C1" t="s"><v>2</v></c>
      <c r="D1" t="s"><v>3</v></c>
      <c r="E1" t="s"><v>4</v></c>
      <c r="F1" t="s"><v>5</v></c>
      <c r="G1" t="s"><v>6</v></c>
    </row>
    <row r="2">
      <c r="A2" t="s"><v>7</v></c>
      <c r="B2" t="s"><v>8</v></c>
      <c r="C2" t="s"><v>9</v></c>
      <c r="D2" t="s"><v>10</v></c>
      <c r="E2" t="s"><v>11</v></c>
      <c r="F2" t="s"><v>12</v></c>
      <c r="G2" t="s"><v>13</v></c>
    </row>
    <row r="3">
      <c r="A3" t="s"><v>14</v></c>
      <c r="B3" t="s"><v>15</v></c>
      <c r="C3" t="s"><v>16</v></c>
      <c r="D3" t="s"><v>17</v></c>
      <c r="E3" t="s"><v>18</v></c>
      <c r="F3" t="s"><v>19</v></c>
      <c r="G3" t="s"><v>20</v></c>
    </row>
  </sheetData>
</worksheet>';

// Buat file ZIP (Excel adalah file ZIP)
$zip = new ZipArchive();
$tempFile = tempnam(sys_get_temp_dir(), 'excel_');
$zip->open($tempFile, ZipArchive::CREATE);

// Tambahkan file-file yang diperlukan untuk Excel
$zip->addFromString('[Content_Types].xml', '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types">
  <Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/>
  <Default Extension="xml" ContentType="application/xml"/>
  <Override PartName="/xl/workbook.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet.main+xml"/>
  <Override PartName="/xl/worksheets/sheet1.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml"/>
  <Override PartName="/xl/worksheets/sheet2.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml"/>
  <Override PartName="/xl/sharedStrings.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sharedStrings+xml"/>
</Types>');

$zip->addFromString('_rels/.rels', '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">
  <Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="xl/workbook.xml"/>
</Relationships>');

$zip->addFromString('xl/workbook.xml', '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<workbook xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships">
  <sheets>
    <sheet name="Data Prestasi" sheetId="1" r:id="rId1"/>
    <sheet name="Referensi Prestasi" sheetId="2" r:id="rId3"/>
  </sheets>
</workbook>');

$zip->addFromString('xl/worksheets/sheet1.xml', $excelContent);

// Buat shared strings
$sharedStrings = array(
    'NIS', 'Nama Siswa', 'Kelas', 'Jurusan', 'ID Prestasi', 'Tanggal', 'Tahun Ajaran (Otomatis)',
    '567351', 'Kokki', 'X-1', 'IPA', '2', '2024-01-15', '2025/2026',
    '567350', 'Lidya', 'XI', 'UMUM', '8', '2024-01-20', '2025/2026',
    'ID', 'Nama Prestasi', 'Point'
);

// Tambahkan data prestasi ke shared strings
foreach($prestasi_data as $prestasi) {
    $sharedStrings[] = $prestasi['prestasi_nama'];
}

$sharedStringsXml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<sst xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" count="' . count($sharedStrings) . '" uniqueCount="' . count($sharedStrings) . '">';
foreach($sharedStrings as $string) {
    $sharedStringsXml .= '<si><t>' . htmlspecialchars($string) . '</t></si>';
}
$sharedStringsXml .= '</sst>';

$zip->addFromString('xl/sharedStrings.xml', $sharedStringsXml);

// Buat sheet referensi prestasi
$referensiContent = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships">
  <sheetData>
    <row r="1">
      <c r="A1" t="s"><v>' . (count($sharedStrings) - count($prestasi_data)) . '</v></c>
      <c r="B1" t="s"><v>' . (count($sharedStrings) - count($prestasi_data) + 1) . '</v></c>
      <c r="C1" t="s"><v>' . (count($sharedStrings) - count($prestasi_data) + 2) . '</v></c>
    </row>';

$rowNum = 2;
$stringIndex = count($sharedStrings) - count($prestasi_data) + 3;
foreach($prestasi_data as $prestasi) {
    $referensiContent .= '
    <row r="' . $rowNum . '">
      <c r="A' . $rowNum . '"><v>' . $prestasi['prestasi_id'] . '</v></c>
      <c r="B' . $rowNum . '" t="s"><v>' . $stringIndex . '</v></c>
      <c r="C' . $rowNum . '"><v>' . $prestasi['prestasi_point'] . '</v></c>
    </row>';
    $rowNum++;
    $stringIndex++;
}

$referensiContent .= '
  </sheetData>
</worksheet>';

$zip->addFromString('xl/worksheets/sheet2.xml', $referensiContent);

$zip->addFromString('xl/_rels/workbook.xml.rels', '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">
  <Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet" Target="worksheets/sheet1.xml"/>
  <Relationship Id="rId2" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/sharedStrings" Target="sharedStrings.xml"/>
  <Relationship Id="rId3" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet" Target="worksheets/sheet2.xml"/>
</Relationships>');

$zip->close();

// Output file
readfile($tempFile);
unlink($tempFile);
?>
