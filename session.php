<?php
session_start();
ob_start();
require('fpdf/fpdf.php');
define('EURO',chr(128));

//récupération de la session:
$tableau_nom = $_SESSION['tableau_nom'];
$tableau_prix = $_SESSION['tableau_prix'];

class PDF extends FPDF
{
	// En-tête
	function Header()
	{   
	  // Police Arial gras 15
	  $this->SetFont('Arial','B',15);
	  // Décalage à droite
	  $this->Cell(80);
	  // Titre
	  $this->Cell(30,10,'Reporting',1,0,'C');
	  // Saut de ligne
	  $this->Ln(20);
	}
	// Pied de page
	function Footer()
	{
	  // Positionnement à 1,5 cm du bas
	  $this->SetY(-15);
	  // Police Arial italique 8
	  $this->SetFont('Arial','I',8);
	  // Numéro de page
	  $this->Cell(0,10,'Page '.$this->PageNo().'/{nb}',0,0,'C');
	}
}
$pdf = new PDF();
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->SetFont('Times','',12);
$pdf->Cell(55, 7, count($tableau_prix). utf8_decode(" produits ont été parcourus."), 1, 1, 'C');
for($i=0; $i < count($tableau_prix); $i++){
	$pdf->Cell(180,7, utf8_decode($tableau_nom[$i]) ,0,0);
	$pdf->Cell(40,6, utf8_decode($tableau_prix[$i]) . EURO ,0,1);
}
$pdf->Cell(80, 7, utf8_decode("Le moins cher des produits est à ") . min($tableau_prix) . EURO, 1, 0);
$pdf->Cell(80, 7, utf8_decode("Le plus cher des produits est à ") . max($tableau_prix) . EURO, 1, 0);
$pdf->Output();
ob_end_flush(); 

