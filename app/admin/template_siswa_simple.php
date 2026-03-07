<?php
/**
 * Download Template Excel untuk Import Data Siswa - Versi Sederhana
 */

// Set header untuk download file Excel
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment; filename="template_import_siswa.xlsx"');
header('Cache-Control: max-age=0');

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

$sharedStrings = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<sst xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" count="15" uniqueCount="15">
  <si><t>NIS</t></si>
  <si><t>Nama Siswa</t></si>
  <si><t>Jurusan</t></si>
  <si><t>Status</t></si>
  <si><t>Password</t></si>
  <si><t>123456</t></si>
  <si><t>John Doe</t></si>
  <si><t>IPA</t></si>
  <si><t>Aktif</t></si>
  <si><t>siswa123</t></si>
  <si><t>123457</t></si>
  <si><t>Jane Smith</t></si>
  <si><t>IPS</t></si>
  <si><t>Aktif</t></si>
  <si><t>siswa123</t></si>
</sst>';

// Buat file ZIP (Excel adalah file ZIP)
$zip = new ZipArchive();
$tempFile = tempnam(sys_get_temp_dir(), 'excel_template');
$zip->open($tempFile, ZipArchive::CREATE);

// Tambahkan file-file yang diperlukan untuk Excel
$zip->addFromString('[Content_Types].xml', '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types">
  <Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/>
  <Default Extension="xml" ContentType="application/xml"/>
  <Override PartName="/xl/workbook.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet.main+xml"/>
  <Override PartName="/xl/worksheets/sheet1.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml"/>
  <Override PartName="/xl/sharedStrings.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sharedStrings+xml"/>
  <Override PartName="/_rels/.rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/>
</Types>');

$zip->addFromString('_rels/.rels', '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">
  <Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="xl/workbook.xml"/>
</Relationships>');

$zip->addFromString('xl/workbook.xml', '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<workbook xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships">
  <sheets>
    <sheet name="Sheet1" sheetId="1" r:id="rId1"/>
  </sheets>
</workbook>');

$zip->addFromString('xl/_rels/workbook.xml.rels', '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">
  <Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet" Target="worksheets/sheet1.xml"/>
  <Relationship Id="rId2" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/sharedStrings" Target="sharedStrings.xml"/>
</Relationships>');

$zip->addFromString('xl/worksheets/sheet1.xml', $excelContent);
$zip->addFromString('xl/sharedStrings.xml', $sharedStrings);

$zip->close();

// Output file
readfile($tempFile);
unlink($tempFile);
exit;
?>
