<?php
/**
 * Simple Excel Generator untuk membuat file .xlsx yang valid
 */

class SimpleExcel {
    private $data = array();
    
    public function addRow($row) {
        $this->data[] = $row;
    }
    
    public function generate($filename) {
        // Buat file ZIP (Excel adalah file ZIP)
        $zip = new ZipArchive();
        $tempFile = tempnam(sys_get_temp_dir(), 'excel_');
        $zip->open($tempFile, ZipArchive::CREATE);
        
        // Tambahkan file-file yang diperlukan untuk Excel
        $this->addContentTypes($zip);
        $this->addWorkbook($zip);
        $this->addWorksheet($zip);
        $this->addSharedStrings($zip);
        $this->addRels($zip);
        
        $zip->close();
        
        // Output file
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        
        readfile($tempFile);
        unlink($tempFile);
        exit;
    }
    
    private function addContentTypes($zip) {
        $content = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types">
  <Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/>
  <Default Extension="xml" ContentType="application/xml"/>
  <Override PartName="/xl/workbook.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet.main+xml"/>
  <Override PartName="/xl/worksheets/sheet1.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml"/>
  <Override PartName="/xl/sharedStrings.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sharedStrings+xml"/>
  <Override PartName="/_rels/.rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/>
</Types>';
        $zip->addFromString('[Content_Types].xml', $content);
    }
    
    private function addWorkbook($zip) {
        $content = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<workbook xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships">
  <sheets>
    <sheet name="Sheet1" sheetId="1" r:id="rId1"/>
  </sheets>
</workbook>';
        $zip->addFromString('xl/workbook.xml', $content);
    }
    
    private function addWorksheet($zip) {
        $content = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships">
  <sheetData>';
        
        foreach ($this->data as $rowIndex => $row) {
            $content .= '<row r="' . ($rowIndex + 1) . '">';
            foreach ($row as $colIndex => $cell) {
                $colLetter = $this->getColumnLetter($colIndex);
                $cellRef = $colLetter . ($rowIndex + 1);
                
                if (is_string($cell)) {
                    $content .= '<c r="' . $cellRef . '" t="s"><v>' . $colIndex . '</v></c>';
                } else {
                    $content .= '<c r="' . $cellRef . '"><v>' . $cell . '</v></c>';
                }
            }
            $content .= '</row>';
        }
        
        $content .= '</sheetData>
</worksheet>';
        $zip->addFromString('xl/worksheets/sheet1.xml', $content);
    }
    
    private function addSharedStrings($zip) {
        $strings = array();
        foreach ($this->data as $row) {
            foreach ($row as $cell) {
                if (is_string($cell) && !in_array($cell, $strings)) {
                    $strings[] = $cell;
                }
            }
        }
        
        $content = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<sst xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" count="' . count($strings) . '" uniqueCount="' . count($strings) . '">';
        
        foreach ($strings as $string) {
            $content .= '<si><t>' . htmlspecialchars($string, ENT_XML1, 'UTF-8') . '</t></si>';
        }
        
        $content .= '</sst>';
        $zip->addFromString('xl/sharedStrings.xml', $content);
    }
    
    private function addRels($zip) {
        // Main rels
        $content = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">
  <Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="xl/workbook.xml"/>
</Relationships>';
        $zip->addFromString('_rels/.rels', $content);
        
        // Workbook rels
        $content = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">
  <Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet" Target="worksheets/sheet1.xml"/>
  <Relationship Id="rId2" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/sharedStrings" Target="sharedStrings.xml"/>
</Relationships>';
        $zip->addFromString('xl/_rels/workbook.xml.rels', $content);
    }
    
    private function getColumnLetter($index) {
        $letters = '';
        while ($index >= 0) {
            $letters = chr(65 + ($index % 26)) . $letters;
            $index = intval($index / 26) - 1;
        }
        return $letters;
    }
}
?>
