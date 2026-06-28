<?php
/**
 * 3.10 PDF Export Feature.
 * Renders the menu_items table as a downloadable PDF using FPDF.
 */

// --- TEMPORARY: surface the real error if anything goes wrong on the host.
// Remove these two lines once the PDF download works.

require_once __DIR__ . '/config/auth.php';
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/libs/fpdf.php';

/**
 * FPDF core fonts only understand Latin-1, so menu text must be converted from
 * UTF-8. Some hosts (e.g. InfinityFree free tier) disable iconv(), which makes
 * a direct iconv() call a fatal error -> HTTP 500. This wrapper degrades safely.
 */
function pdf_text(string $s): string
{
    if (function_exists('iconv')) {
        $out = @iconv('UTF-8', 'ISO-8859-1//TRANSLIT', $s);
        if ($out !== false) {
            return $out;
        }
    }
    if (function_exists('mb_convert_encoding')) {
        return mb_convert_encoding($s, 'ISO-8859-1', 'UTF-8');
    }
    // Last resort: strip anything outside printable ASCII so FPDF never chokes.
    return preg_replace('/[^\x20-\x7E]/', '', $s);
}

$result = mysqli_query($conn, 'SELECT id, name, category, price, created_at FROM menu_items ORDER BY id');

class MenuPDF extends FPDF
{
    function Header()
    {
        $this->SetFont('Arial', 'B', 16);
        $this->Cell(0, 10, 'Tasty Bites - Menu Items', 0, 1, 'C');
        $this->SetFont('Arial', '', 9);
        $this->Cell(0, 6, 'Generated on ' . date('d M Y, H:i'), 0, 1, 'C');
        $this->Ln(2);

        // Table header
        $this->SetFont('Arial', 'B', 10);
        $this->SetFillColor(52, 58, 64);   // dark
        $this->SetTextColor(255, 255, 255);
        $this->Cell(15, 9, '#',        1, 0, 'C', true);
        $this->Cell(75, 9, 'Item Name',1, 0, 'L', true);
        $this->Cell(45, 9, 'Category', 1, 0, 'L', true);
        $this->Cell(35, 9, 'Price (Rs.)', 1, 1, 'R', true);
        $this->SetTextColor(0, 0, 0);
    }

    function Footer()
    {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->Cell(0, 10, 'Page ' . $this->PageNo() . '/{nb}', 0, 0, 'C');
    }
}

$pdf = new MenuPDF('P', 'mm', 'A4');
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->SetFont('Arial', '', 10);

$fill = false;
$total = 0;
$i = 1;
while ($row = mysqli_fetch_assoc($result)) {
    $pdf->SetFillColor(245, 246, 249);
    $pdf->Cell(15, 8, $i++, 1, 0, 'C', $fill);
    // strip non-latin chars FPDF core fonts can't render
    $name = pdf_text($row['name']);
    $cat  = pdf_text($row['category']);
    $pdf->Cell(75, 8, $name, 1, 0, 'L', $fill);
    $pdf->Cell(45, 8, $cat,  1, 0, 'L', $fill);
    $pdf->Cell(35, 8, number_format($row['price'], 2), 1, 1, 'R', $fill);
    $total += $row['price'];
    $fill = !$fill;
}

// Total row
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(135, 9, 'Total', 1, 0, 'R');
$pdf->Cell(35, 9, number_format($total, 2), 1, 1, 'R');

$pdf->Output('D', 'menu_items_' . date('Y-m-d') . '.pdf');
