<?php
/**
 * Download Template Excel untuk Import Data Siswa
 * Menggunakan struktur Excel yang lengkap dan valid
 * Kolom jurusan otomatis terisi sesuai kelas yang dipilih
 */

include '../koneksi.php';

// Ambil data kelas dan jurusan jika ada parameter id
$jurusan_nama = 'IPA'; // Default
$kelas_id = null;

if (isset($_GET['id']) && !empty($_GET['id'])) {
    $id = mysqli_real_escape_string($koneksi, $_GET['id']);
    $kelas_query = mysqli_query($koneksi, "SELECT k.*, j.jurusan_nama 
                                          FROM kelas k 
                                          JOIN jurusan j ON k.kelas_jurusan = j.jurusan_id 
                                          WHERE k.kelas_id = '$id'");
    
    if ($kelas_query && mysqli_num_rows($kelas_query) > 0) {
        $kelas_data = mysqli_fetch_assoc($kelas_query);
        $jurusan_nama = $kelas_data['jurusan_nama'];
        $kelas_id = $kelas_data['kelas_id'];
    }
}

// Set header untuk download file Excel
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
$filename = $kelas_id ? "template_import_siswa_kelas_{$kelas_id}.xlsx" : "template_import_siswa.xlsx";
header('Content-Disposition: attachment; filename="' . $filename . '"');
header('Cache-Control: max-age=0');

// Buat file ZIP (Excel adalah file ZIP)
$zip = new ZipArchive();
$tempFile = tempnam(sys_get_temp_dir(), 'excel_template_');
if ($zip->open($tempFile, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== TRUE) {
    die('Tidak dapat membuat file Excel');
}

// 1. [Content_Types].xml
$contentTypes = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types">
  <Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/>
  <Default Extension="xml" ContentType="application/xml"/>
  <Override PartName="/xl/workbook.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet.main+xml"/>
  <Override PartName="/xl/worksheets/sheet1.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml"/>
  <Override PartName="/xl/sharedStrings.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sharedStrings+xml"/>
  <Override PartName="/xl/styles.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.styles+xml"/>
  <Override PartName="/_rels/.rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/>
  <Override PartName="/xl/_rels/workbook.xml.rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/>
</Types>';
$zip->addFromString('[Content_Types].xml', $contentTypes);

// 2. _rels/.rels
$rels = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">
  <Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="xl/workbook.xml"/>
</Relationships>';
$zip->addFromString('_rels/.rels', $rels);

// 3. xl/workbook.xml
$workbook = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<workbook xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships">
  <sheets>
    <sheet name="Sheet1" sheetId="1" r:id="rId1"/>
  </sheets>
</workbook>';
$zip->addFromString('xl/workbook.xml', $workbook);

// 4. xl/_rels/workbook.xml.rels
$workbookRels = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">
  <Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet" Target="worksheets/sheet1.xml"/>
  <Relationship Id="rId2" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/sharedStrings" Target="sharedStrings.xml"/>
  <Relationship Id="rId3" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/styles" Target="styles.xml"/>
</Relationships>';
$zip->addFromString('xl/_rels/workbook.xml.rels', $workbookRels);

// 5. xl/sharedStrings.xml
// Escape XML special characters untuk jurusan
$jurusan_escaped = htmlspecialchars($jurusan_nama, ENT_XML1, 'UTF-8');

$sharedStrings = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<sst xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" count="15" uniqueCount="15">
  <si><t>NIS</t></si>
  <si><t>Nama Siswa</t></si>
  <si><t>Jurusan</t></si>
  <si><t>Status</t></si>
  <si><t>Password</t></si>
  <si><t>123456</t></si>
  <si><t>John Doe</t></si>
  <si><t>' . $jurusan_escaped . '</t></si>
  <si><t>Aktif</t></si>
  <si><t>siswa123</t></si>
  <si><t>123457</t></si>
  <si><t>Jane Smith</t></si>
  <si><t>' . $jurusan_escaped . '</t></si>
  <si><t>Aktif</t></si>
  <si><t>siswa123</t></si>
</sst>';
$zip->addFromString('xl/sharedStrings.xml', $sharedStrings);

// 6. xl/styles.xml (PENTING untuk Excel yang valid)
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

// 7. xl/worksheets/sheet1.xml
$worksheet = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships">
  <dimension ref="A1:E3"/>
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
    </row>
    <row r="2">
      <c r="A2" t="s"><v>5</v></c>
      <c r="B2" t="s"><v>6</v></c>
      <c r="C2" t="s"><v>7</v></c>
      <c r="D2" t="s"><v>8</v></c>
      <c r="E2" t="s"><v>9</v></c>
    </row>
    <row r="3">
      <c r="A3" t="s"><v>10</v></c>
      <c r="B3" t="s"><v>11</v></c>
      <c r="C3" t="s"><v>12</v></c>
      <c r="D3" t="s"><v>13</v></c>
      <c r="E3" t="s"><v>14</v></c>
    </row>
  </sheetData>
</worksheet>';
$zip->addFromString('xl/worksheets/sheet1.xml', $worksheet);

$zip->close();

// Output file
readfile($tempFile);
unlink($tempFile);
exit;
?>
