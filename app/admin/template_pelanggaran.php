<?php
/**
 * Download Template Excel untuk Import Data Pelanggaran
 * Menggunakan struktur Excel yang lengkap dan valid
 */

include '../koneksi.php';

// Ambil data pelanggaran dari database
$pelanggaran_query = mysqli_query($koneksi, "SELECT pelanggaran_id, pelanggaran_nama, pelanggaran_point FROM pelanggaran ORDER BY pelanggaran_id");
$pelanggaran_data = array();
while($p = mysqli_fetch_assoc($pelanggaran_query)) {
    $pelanggaran_data[] = $p;
}

// Ambil data siswa untuk contoh
$siswa_query = mysqli_query($koneksi, "SELECT s.siswa_nis, s.siswa_nama, k.kelas_nama, j.jurusan_nama 
    FROM siswa s 
    LEFT JOIN kelas_siswa ks ON s.siswa_id = ks.ks_siswa 
    LEFT JOIN kelas k ON ks.ks_kelas = k.kelas_id 
    LEFT JOIN jurusan j ON k.kelas_jurusan = j.jurusan_id 
    WHERE k.kelas_ta = (SELECT ta_id FROM ta WHERE ta_status = 1) 
    LIMIT 3");
$siswa_data = array();
if($siswa_query) {
    while($s = mysqli_fetch_assoc($siswa_query)) {
        $siswa_data[] = $s;
    }
}

// Jika tidak ada data siswa, buat contoh dummy
if(empty($siswa_data)) {
    $siswa_data = array(
        array('siswa_nis' => '123456', 'siswa_nama' => 'Siswa Contoh 1', 'kelas_nama' => 'X IPA', 'jurusan_nama' => 'IPA'),
        array('siswa_nis' => '123457', 'siswa_nama' => 'Siswa Contoh 2', 'kelas_nama' => 'XI IPA', 'jurusan_nama' => 'IPA'),
        array('siswa_nis' => '123458', 'siswa_nama' => 'Siswa Contoh 3', 'kelas_nama' => 'XII IPA', 'jurusan_nama' => 'IPA')
    );
}

// Ambil ID pelanggaran pertama untuk contoh
$pelanggaran_contoh_id = !empty($pelanggaran_data) ? $pelanggaran_data[0]['pelanggaran_id'] : 1;
$tanggal_contoh = date('Y-m-d');

// Build shared strings array untuk menghitung dengan benar
$sharedStringsArray = array();

// Header sheet 1
$sharedStringsArray[] = 'NIS';
$sharedStringsArray[] = 'Nama Siswa';
$sharedStringsArray[] = 'Kelas';
$sharedStringsArray[] = 'Jurusan';
$sharedStringsArray[] = 'ID Pelanggaran';
$sharedStringsArray[] = 'Tanggal (YYYY-MM-DD)';

// Data siswa contoh
foreach($siswa_data as $siswa) {
    $sharedStringsArray[] = $siswa['siswa_nis'];
    $sharedStringsArray[] = $siswa['siswa_nama'];
    $sharedStringsArray[] = $siswa['kelas_nama'] ?: 'Belum ada kelas';
    $sharedStringsArray[] = $siswa['jurusan_nama'] ?: 'Belum ada jurusan';
    $sharedStringsArray[] = (string)$pelanggaran_contoh_id;
    $sharedStringsArray[] = $tanggal_contoh;
}

// Header sheet 2
$sharedStringsArray[] = 'ID Pelanggaran';
$sharedStringsArray[] = 'Nama Pelanggaran';
$sharedStringsArray[] = 'Point';

// Data pelanggaran
foreach($pelanggaran_data as $pelanggaran) {
    $sharedStringsArray[] = (string)$pelanggaran['pelanggaran_id'];
    $sharedStringsArray[] = $pelanggaran['pelanggaran_nama'];
    $sharedStringsArray[] = (string)$pelanggaran['pelanggaran_point'];
}

// Hitung total dan unique
$totalCount = count($sharedStringsArray);
$uniqueCount = count(array_unique($sharedStringsArray));

// Build shared strings XML
$sharedStrings = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<sst xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" count="' . $totalCount . '" uniqueCount="' . $uniqueCount . '">';

foreach($sharedStringsArray as $str) {
    $sharedStrings .= '<si><t>' . htmlspecialchars($str, ENT_XML1, 'UTF-8') . '</t></si>';
}

$sharedStrings .= '</sst>';

// Set header untuk download
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment; filename="template_import_pelanggaran.xlsx"');
header('Cache-Control: max-age=0');

// Buat file Excel menggunakan ZipArchive
$zip = new ZipArchive();
$tempFile = tempnam(sys_get_temp_dir(), 'excel_template_');
if ($zip->open($tempFile, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== TRUE) {
    die('Tidak dapat membuat file Excel');
}

// Content Types
$contentTypes = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types">
  <Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/>
  <Default Extension="xml" ContentType="application/xml"/>
  <Override PartName="/xl/workbook.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet.main+xml"/>
  <Override PartName="/xl/worksheets/sheet1.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml"/>
  <Override PartName="/xl/worksheets/sheet2.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml"/>
  <Override PartName="/xl/sharedStrings.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sharedStrings+xml"/>
  <Override PartName="/xl/styles.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.styles+xml"/>
  <Override PartName="/docProps/app.xml" ContentType="application/vnd.openxmlformats-officedocument.extended-properties+xml"/>
  <Override PartName="/docProps/core.xml" ContentType="application/vnd.openxmlformats-officedocument.core-properties+xml"/>
</Types>';
$zip->addFromString('[Content_Types].xml', $contentTypes);

// Workbook
$workbook = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<workbook xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships">
  <sheets>
    <sheet name="Data Pelanggaran" sheetId="1" r:id="rId1"/>
    <sheet name="Referensi Pelanggaran" sheetId="2" r:id="rId2"/>
  </sheets>
</workbook>';
$zip->addFromString('xl/workbook.xml', $workbook);

// Shared Strings
$zip->addFromString('xl/sharedStrings.xml', $sharedStrings);

// Worksheet 1: Data Pelanggaran
$maxRow = 1 + count($siswa_data);
$worksheet1 = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships">
  <dimension ref="A1:F' . $maxRow . '"/>
  <sheetViews>
    <sheetView workbookViewId="0"/>
  </sheetViews>
  <sheetData>
    <row r="1">
      <c r="A1" t="s"><v>0</v></c>
      <c r="B1" t="s"><v>1</v></c>
      <c r="C1" t="s"><v>2</v></c>
      <c r="D1" t="s"><v>3</v></c>
      <c r="E1" t="s"><v>4</v></c>
      <c r="F1" t="s"><v>5</v></c>
    </row>';

$row = 2;
$stringIndex = 6; // Setelah 6 header
foreach($siswa_data as $siswa) {
    $worksheet1 .= '<row r="' . $row . '">';
    $worksheet1 .= '<c r="A' . $row . '" t="s"><v>' . $stringIndex . '</v></c>'; $stringIndex++; // NIS
    $worksheet1 .= '<c r="B' . $row . '" t="s"><v>' . $stringIndex . '</v></c>'; $stringIndex++; // Nama Siswa
    $worksheet1 .= '<c r="C' . $row . '" t="s"><v>' . $stringIndex . '</v></c>'; $stringIndex++; // Kelas
    $worksheet1 .= '<c r="D' . $row . '" t="s"><v>' . $stringIndex . '</v></c>'; $stringIndex++; // Jurusan
    $worksheet1 .= '<c r="E' . $row . '" t="s"><v>' . $stringIndex . '</v></c>'; $stringIndex++; // ID Pelanggaran
    $worksheet1 .= '<c r="F' . $row . '" t="s"><v>' . $stringIndex . '</v></c>'; $stringIndex++; // Tanggal
    $worksheet1 .= '</row>';
    $row++;
}

$worksheet1 .= '</sheetData>
</worksheet>';
$zip->addFromString('xl/worksheets/sheet1.xml', $worksheet1);

// Worksheet 2: Referensi Pelanggaran
$maxRow2 = 1 + count($pelanggaran_data);
$headerSheet2Index = 6 + (count($siswa_data) * 6);
$worksheet2 = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships">
  <dimension ref="A1:C' . $maxRow2 . '"/>
  <sheetViews>
    <sheetView workbookViewId="0"/>
  </sheetViews>
  <sheetData>
    <row r="1">
      <c r="A1" t="s"><v>' . $headerSheet2Index . '</v></c>
      <c r="B1" t="s"><v>' . ($headerSheet2Index + 1) . '</v></c>
      <c r="C1" t="s"><v>' . ($headerSheet2Index + 2) . '</v></c>
    </row>';

$row = 2;
$refStringIndex = $headerSheet2Index + 3; // Setelah header sheet 2
foreach($pelanggaran_data as $pelanggaran) {
    $worksheet2 .= '<row r="' . $row . '">';
    $worksheet2 .= '<c r="A' . $row . '" t="s"><v>' . $refStringIndex . '</v></c>'; $refStringIndex++;
    $worksheet2 .= '<c r="B' . $row . '" t="s"><v>' . $refStringIndex . '</v></c>'; $refStringIndex++;
    $worksheet2 .= '<c r="C' . $row . '" t="s"><v>' . $refStringIndex . '</v></c>'; $refStringIndex++;
    $worksheet2 .= '</row>';
    $row++;
}

$worksheet2 .= '</sheetData>
</worksheet>';
$zip->addFromString('xl/worksheets/sheet2.xml', $worksheet2);

// Styles
$styles = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<styleSheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">
  <numFmts count="0"/>
  <fonts count="1">
    <font>
      <sz val="11"/>
      <color theme="1"/>
      <name val="Calibri"/>
      <family val="2"/>
      <scheme val="minor"/>
    </font>
  </fonts>
  <fills count="2">
    <fill>
      <patternFill patternType="none"/>
    </fill>
    <fill>
      <patternFill patternType="gray125"/>
    </fill>
  </fills>
  <borders count="1">
    <border>
      <left/>
      <right/>
      <top/>
      <bottom/>
      <diagonal/>
    </border>
  </borders>
  <cellStyleXfs count="1">
    <xf numFmtId="0" fontId="0" fillId="0" borderId="0"/>
  </cellStyleXfs>
  <cellXfs count="1">
    <xf numFmtId="0" fontId="0" fillId="0" borderId="0" xfId="0"/>
  </cellXfs>
  <cellStyles count="1">
    <cellStyle name="Normal" xfId="0" builtinId="0"/>
  </cellStyles>
  <dxfs count="0"/>
  <tableStyles count="0" defaultTableStyle="TableStyleMedium9" defaultPivotStyle="PivotStyleLight16"/>
</styleSheet>';
$zip->addFromString('xl/styles.xml', $styles);

// Relationships
$rels = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">
  <Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet" Target="worksheets/sheet1.xml"/>
  <Relationship Id="rId2" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet" Target="worksheets/sheet2.xml"/>
  <Relationship Id="rId3" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/sharedStrings" Target="sharedStrings.xml"/>
  <Relationship Id="rId4" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/styles" Target="styles.xml"/>
</Relationships>';
$zip->addFromString('xl/_rels/workbook.xml.rels', $rels);

// App properties
$app = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<Properties xmlns="http://schemas.openxmlformats.org/officeDocument/2006/extended-properties" xmlns:vt="http://schemas.openxmlformats.org/officeDocument/2006/docPropsVTypes">
  <Application>Microsoft Excel</Application>
  <DocSecurity>0</DocSecurity>
  <ScaleCrop>false</ScaleCrop>
  <SharedDoc>false</SharedDoc>
  <HyperlinksChanged>false</HyperlinksChanged>
  <AppVersion>16.0300</AppVersion>
</Properties>';
$zip->addFromString('docProps/app.xml', $app);

// Core properties
$core = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<cp:coreProperties xmlns:cp="http://schemas.openxmlformats.org/package/2006/metadata/core-properties" xmlns:dc="http://purl.org/dc/elements/1.1/" xmlns:dcterms="http://purl.org/dc/terms/" xmlns:dcmitype="http://purl.org/dc/dcmitype/" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">
  <dc:creator>E-Point System</dc:creator>
  <cp:lastModifiedBy>E-Point System</cp:lastModifiedBy>
  <dcterms:created xsi:type="dcterms:W3CDTF">' . date('c') . '</dcterms:created>
  <dcterms:modified xsi:type="dcterms:W3CDTF">' . date('c') . '</dcterms:modified>
</cp:coreProperties>';
$zip->addFromString('docProps/core.xml', $core);

// Main relationships
$mainRels = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">
  <Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="xl/workbook.xml"/>
  <Relationship Id="rId2" Type="http://schemas.openxmlformats.org/package/2006/relationships/metadata/core-properties" Target="docProps/core.xml"/>
  <Relationship Id="rId3" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/extended-properties" Target="docProps/app.xml"/>
</Relationships>';
$zip->addFromString('_rels/.rels', $mainRels);

$zip->close();

// Output file
readfile($tempFile);
unlink($tempFile);

exit;
?>
