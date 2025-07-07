<?php
// Simple PHPExcel implementation for demo purposes
// In production, you should use the full PHPExcel library

class PHPExcel {
    private $sheets = [];
    private $activeSheetIndex = 0;
    private $properties = [];
    
    public function __construct() {
        $this->sheets[0] = new PHPExcel_Worksheet($this);
        $this->properties = [
            'creator' => 'E-Kantin System',
            'lastModifiedBy' => 'E-Kantin System',
            'title' => 'Laporan Transaksi',
            'subject' => 'Laporan Transaksi Harian',
            'description' => 'Laporan transaksi kantin'
        ];
    }
    
    public function getProperties() {
        return $this;
    }
    
    public function setCreator($creator) {
        $this->properties['creator'] = $creator;
        return $this;
    }
    
    public function setLastModifiedBy($author) {
        $this->properties['lastModifiedBy'] = $author;
        return $this;
    }
    
    public function setTitle($title) {
        $this->properties['title'] = $title;
        return $this;
    }
    
    public function setSubject($subject) {
        $this->properties['subject'] = $subject;
        return $this;
    }
    
    public function setDescription($description) {
        $this->properties['description'] = $description;
        return $this;
    }
    
    public function setActiveSheetIndex($index) {
        $this->activeSheetIndex = $index;
        return $this;
    }
    
    public function getActiveSheet() {
        return $this->sheets[$this->activeSheetIndex];
    }
    
    public function createSheet() {
        $index = count($this->sheets);
        $this->sheets[$index] = new PHPExcel_Worksheet($this);
        return $this;
    }
}

class PHPExcel_Worksheet {
    private $cells = [];
    private $title = 'Sheet1';
    private $excel;
    
    public function __construct($excel) {
        $this->excel = $excel;
    }
    
    public function setTitle($title) {
        $this->title = $title;
        return $this;
    }
    
    public function setCellValue($cell, $value) {
        $this->cells[$cell] = $value;
        return $this;
    }
    
    public function getColumnDimension($column) {
        return new PHPExcel_ColumnDimension($column);
    }
}

class PHPExcel_ColumnDimension {
    private $column;
    
    public function __construct($column) {
        $this->column = $column;
    }
    
    public function setAutoSize($autoSize) {
        return $this;
    }
}

class PHPExcel_IOFactory {
    public static function createWriter($excel, $type) {
        return new PHPExcel_Writer_Excel2007($excel);
    }
}

class PHPExcel_Writer_Excel2007 {
    private $excel;
    
    public function __construct($excel) {
        $this->excel = $excel;
    }
    
    public function save($filename) {
        // Simple CSV export for demo
        $activeSheet = $this->excel->getActiveSheet();
        $data = [];
        
        // Convert cells to array
        foreach ($activeSheet->cells as $cell => $value) {
            $col = preg_replace('/[0-9]/', '', $cell);
            $row = preg_replace('/[A-Z]/', '', $cell);
            $data[$row][$col] = $value;
        }
        
        // Output CSV
        $output = fopen($filename, 'w');
        foreach ($data as $row) {
            fputcsv($output, $row);
        }
        fclose($output);
    }
} 