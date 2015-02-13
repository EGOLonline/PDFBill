<?php

require_once 'vendor/anouar/fpdf/src/Anouar/Fpdf/Fpdf.php';
class FPDFBill extends Anouar\Fpdf\FPDF {
    
    private $_logo;
    private $_company;
    private $_date;
    private $_products;
    private $_currency = 'EUR';
    private $_vat = 0;
    
    public function __construct($orientation='P', $unit='mm', $size='A4') {
        parent::__construct($orientation='P', $unit='mm', $size='A4');
    }
    
    public function setVat($int) {
        $this->_vat = (int)$int;
    }
    
    public function buildHeader($array) {
        $this->addPage();
        $this->Image($array['logo'], 30, 35, 0, 10);
        $this->SetFont('Arial', 'B', 8);
        $this->SetMargins(30, 50);
        $this->setY(50);
        $this->Cell(0, 4, $array['company'], '', 0, 'L');
        $this->SetFont('Arial', '', 8 ); 
        $this->Cell(-50, 4, 'Rechnungsnummer: '.$array['billno'], '', 0, 'R');
        $this->Cell(0, 4, 'Datum: '.$array['date'], '', 0, 'R');
        $this->setY(90);
        $this->SetFont('Arial', 'B', 10 ); 
        $this->Cell(0, 0, $array['title']);
    }
    
    public function addProduct($array) {
        $this->_products[] = $array;
    }
    
    public function buildCart() {
        $sum = 0;
        $vatSum = 0;
        
        $this->SetY(100);
        $this->SetFillColor(194,194,194);
        $this->Cell(30, 6, 'Anzahl', '', 0, 'L', 1);
        $this->Cell(100, 6, 'Artikelbezeichnung', '', 0, 'L', 1);
        $this->Cell(0, 6, 'Gesamtpreis', '', 0, 'R', 1);
        $this->SetY(110);
        $this->SetFont('Arial', '', 10 ); 
        
        foreach($this->_products as $product) {
            $this->Cell(30, 4, $product['count']);
            $this->Cell(100, 4, $product['title']);
            $this->Cell(0, 4, $this->_currency.' '.number_format($product['price'] * $product['count'], 2, ',', '.'), '', 0, 'R');
            $this->Ln(4);
            $sum += $product['price'] * $product['count'];
        }
        
        if($this->_vat) {
            $vatSum = $sum / 100 * $this->_vat;
            $this->Ln(4);
            $this->Cell(50, 8, 'Nettosumme', 'T');    
            $this->Cell(0, 8, $this->_currency.' '.number_format($sum, 2, ',', '.'), 'T', 0, 'R');
            $this->Ln(4);
            $this->Cell(50, 8, 'zzgl. MwSt ('.$this->_vat.'%)');    
            $this->Cell(0, 8, $this->_currency.' '.number_format($vatSum, 2, ',', '.'), '', 0, 'R');
        }
        
        $this->SetFont('Arial', 'B', 10 ); 
        $this->Ln(8);
        $this->Cell(50, 6, 'Endbetrag', 'T');
        $this->Cell(0, 6, $this->_currency.' '.number_format($sum + $vatSum, 2, ',', '.'), 'T', 0, 'R');
    }
    
    public function __destroy() {
        
    }
}