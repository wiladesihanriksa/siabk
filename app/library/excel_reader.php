<?php
/**
 * Simple Excel Reader untuk membaca file .xlsx
 * Menggunakan ZipArchive untuk membaca file Excel
 */

class ExcelReader {
    private $file;
    private $data = array();
    
    public function __construct($file) {
        $this->file = $file;
    }
    
    public function read() {
        if (!file_exists($this->file)) {
            throw new Exception("File tidak ditemukan: " . $this->file);
        }
        
        $zip = new ZipArchive();
        if ($zip->open($this->file) === TRUE) {
            // Baca shared strings
            $sharedStrings = $this->getSharedStrings($zip);
            
            // Baca worksheet
            $worksheet = $zip->getFromName('xl/worksheets/sheet1.xml');
            if ($worksheet === FALSE) {
                throw new Exception("Worksheet tidak ditemukan");
            }
            
            $this->parseWorksheet($worksheet, $sharedStrings);
            $zip->close();
        } else {
            throw new Exception("Tidak dapat membuka file Excel");
        }
        
        return $this->data;
    }
    
    private function getSharedStrings($zip) {
        $sharedStrings = array();
        $sharedStringsXml = $zip->getFromName('xl/sharedStrings.xml');
        
        if ($sharedStringsXml !== FALSE) {
            $xml = simplexml_load_string($sharedStringsXml);
            if ($xml !== FALSE) {
                foreach ($xml->si as $si) {
                    $sharedStrings[] = (string)$si->t;
                }
            }
        }
        
        return $sharedStrings;
    }
    
    private function parseWorksheet($worksheet, $sharedStrings) {
        $xml = simplexml_load_string($worksheet);
        if ($xml === FALSE) {
            throw new Exception("Tidak dapat memparse worksheet");
        }
        
        $rows = $xml->sheetData->row;
        foreach ($rows as $row) {
            $rowData = array();
            $cells = $row->c;
            
            foreach ($cells as $cell) {
                $value = '';
                $cellType = (string)$cell['t'];
                
                if ($cellType == 's') {
                    // Shared string
                    $index = (int)$cell->v;
                    if (isset($sharedStrings[$index])) {
                        $value = $sharedStrings[$index];
                    }
                } else {
                    // Inline string atau number
                    $value = (string)$cell->v;
                }
                
                $rowData[] = $value;
            }
            
            if (!empty($rowData)) {
                $this->data[] = $rowData;
            }
        }
    }
    
    public function getData() {
        return $this->data;
    }
}
?>
