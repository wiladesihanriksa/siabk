<?php
/**
 * Download Template Excel untuk Import Data Kelas
 * Menggunakan struktur Excel yang lengkap dan valid
 * Dengan sheet referensi untuk Jurusan dan Tahun Ajaran
 */

include '../koneksi.php';

// Ambil data jurusan dari database
$jurusan_query = mysqli_query($koneksi, "SELECT jurusan_id, jurusan_nama FROM jurusan ORDER BY jurusan_id");
$jurusan_data = array();
while($j = mysqli_fetch_assoc($jurusan_query)) {
    $jurusan_data[] = $j;
}

// Ambil data tahun ajaran dari database
$ta_query = mysqli_query($koneksi, "SELECT ta_id, ta_nama, ta_status FROM ta ORDER BY ta_id DESC");
$ta_data = array();
while($t = mysqli_fetch_assoc($ta_query)) {
    $ta_data[] = $t;
}

// Set header untuk download file Excel
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment; filename="template_import_kelas.xlsx"');
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
  <Override PartName="/xl/worksheets/sheet2.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml"/>
  <Override PartName="/xl/worksheets/sheet3.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml"/>
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
    <sheet name="Data Kelas" sheetId="1" r:id="rId1"/>
    <sheet name="Referensi Jurusan" sheetId="2" r:id="rId2"/>
    <sheet name="Referensi Tahun Ajaran" sheetId="3" r:id="rId3"/>
  </sheets>
</workbook>';
$zip->addFromString('xl/workbook.xml', $workbook);

// 4. xl/_rels/workbook.xml.rels
$workbookRels = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">
  <Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet" Target="worksheets/sheet1.xml"/>
  <Relationship Id="rId2" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet" Target="worksheets/sheet2.xml"/>
  <Relationship Id="rId3" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet" Target="worksheets/sheet3.xml"/>
  <Relationship Id="rId4" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/sharedStrings" Target="sharedStrings.xml"/>
  <Relationship Id="rId5" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/styles" Target="styles.xml"/>
</Relationships>';
$zip->addFromString('xl/_rels/workbook.xml.rels', $workbookRels);

// 5. xl/sharedStrings.xml
// Build shared strings
$sharedStrings = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<sst xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">';

// Header Sheet 1: Data Kelas
$sharedStrings .= '<si><t>Nama Kelas</t></si>';
$sharedStrings .= '<si><t>ID Jurusan</t></si>';
$sharedStrings .= '<si><t>ID Tahun Ajaran</t></si>';

// Contoh data Sheet 1
$sharedStrings .= '<si><t>X IPA</t></si>';
$sharedStrings .= '<si><t>5</t></si>';
$sharedStrings .= '<si><t>1</t></si>';

$sharedStrings .= '<si><t>XI IPS</t></si>';
$sharedStrings .= '<si><t>6</t></si>';
$sharedStrings .= '<si><t>1</t></si>';

// Header Sheet 2: Referensi Jurusan
$sharedStrings .= '<si><t>ID Jurusan</t></si>';
$sharedStrings .= '<si><t>Nama Jurusan</t></si>';

// Data Jurusan
foreach($jurusan_data as $jurusan) {
    $sharedStrings .= '<si><t>' . htmlspecialchars($jurusan['jurusan_id'], ENT_XML1, 'UTF-8') . '</t></si>';
    $sharedStrings .= '<si><t>' . htmlspecialchars($jurusan['jurusan_nama'], ENT_XML1, 'UTF-8') . '</t></si>';
}

// Header Sheet 3: Referensi Tahun Ajaran
$sharedStrings .= '<si><t>ID Tahun Ajaran</t></si>';
$sharedStrings .= '<si><t>Nama Tahun Ajaran</t></si>';
$sharedStrings .= '<si><t>Status</t></si>';

// Data Tahun Ajaran
foreach($ta_data as $ta) {
    $sharedStrings .= '<si><t>' . htmlspecialchars($ta['ta_id'], ENT_XML1, 'UTF-8') . '</t></si>';
    $sharedStrings .= '<si><t>' . htmlspecialchars($ta['ta_nama'], ENT_XML1, 'UTF-8') . '</t></si>';
    $status_text = ($ta['ta_status'] == 1) ? 'Aktif' : 'Tidak Aktif';
    $sharedStrings .= '<si><t>' . htmlspecialchars($status_text, ENT_XML1, 'UTF-8') . '</t></si>';
}

// Hitung total strings
$stringCount = 3 + 6 + 2 + (count($jurusan_data) * 2) + 3 + (count($ta_data) * 3);
$sharedStrings .= '</sst>';

// Update count
$sharedStrings = str_replace('<sst xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">', 
                             '<sst xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" count="' . $stringCount . '" uniqueCount="' . $stringCount . '">', 
                             $sharedStrings);

$zip->addFromString('xl/sharedStrings.xml', $sharedStrings);

// 6. xl/styles.xml
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

// 7. xl/worksheets/sheet1.xml (Data Kelas)
$worksheet1 = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships">
  <dimension ref="A1:C3"/>
  <sheetViews>
    <sheetView workbookViewId="0"/>
  </sheetViews>
  <sheetData>
    <row r="1">
      <c r="A1" t="s"><v>0</v></c>
      <c r="B1" t="s"><v>1</v></c>
      <c r="C1" t="s"><v>2</v></c>
    </row>
    <row r="2">
      <c r="A2" t="s"><v>3</v></c>
      <c r="B2" t="s"><v>4</v></c>
      <c r="C2" t="s"><v>5</v></c>
    </row>
    <row r="3">
      <c r="A3" t="s"><v>6</v></c>
      <c r="B3" t="s"><v>7</v></c>
      <c r="C3" t="s"><v>8</v></c>
    </row>
  </sheetData>
</worksheet>';
$zip->addFromString('xl/worksheets/sheet1.xml', $worksheet1);

// 8. xl/worksheets/sheet2.xml (Referensi Jurusan)
$stringIndex = 9; // Mulai setelah header dan contoh data sheet 1
$worksheet2 = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships">
  <dimension ref="A1:B' . (1 + count($jurusan_data)) . '"/>
  <sheetViews>
    <sheetView workbookViewId="0"/>
  </sheetViews>
  <sheetData>
    <row r="1">
      <c r="A1" t="s"><v>' . $stringIndex . '</v></c>';
$stringIndex++;
$worksheet2 .= '<c r="B1" t="s"><v>' . $stringIndex . '</v></c></row>';
$stringIndex++;

$row = 2;
foreach($jurusan_data as $jurusan) {
    $worksheet2 .= '<row r="' . $row . '">';
    $worksheet2 .= '<c r="A' . $row . '" t="s"><v>' . $stringIndex . '</v></c>';
    $stringIndex++;
    $worksheet2 .= '<c r="B' . $row . '" t="s"><v>' . $stringIndex . '</v></c></row>';
    $stringIndex++;
    $row++;
}

$worksheet2 .= '</sheetData>
</worksheet>';
$zip->addFromString('xl/worksheets/sheet2.xml', $worksheet2);

// 9. xl/worksheets/sheet3.xml (Referensi Tahun Ajaran)
$worksheet3 = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships">
  <dimension ref="A1:C' . (1 + count($ta_data)) . '"/>
  <sheetViews>
    <sheetView workbookViewId="0"/>
  </sheetViews>
  <sheetData>
    <row r="1">
      <c r="A1" t="s"><v>' . $stringIndex . '</v></c>';
$stringIndex++;
$worksheet3 .= '<c r="B1" t="s"><v>' . $stringIndex . '</v></c>';
$stringIndex++;
$worksheet3 .= '<c r="C1" t="s"><v>' . $stringIndex . '</v></c></row>';
$stringIndex++;

$row = 2;
foreach($ta_data as $ta) {
    $worksheet3 .= '<row r="' . $row . '">';
    $worksheet3 .= '<c r="A' . $row . '" t="s"><v>' . $stringIndex . '</v></c>';
    $stringIndex++;
    $worksheet3 .= '<c r="B' . $row . '" t="s"><v>' . $stringIndex . '</v></c>';
    $stringIndex++;
    $worksheet3 .= '<c r="C' . $row . '" t="s"><v>' . $stringIndex . '</v></c></row>';
    $stringIndex++;
    $row++;
}

$worksheet3 .= '</sheetData>
</worksheet>';
$zip->addFromString('xl/worksheets/sheet3.xml', $worksheet3);

$zip->close();

// Output file
readfile($tempFile);
unlink($tempFile);
exit;
?>

