<?php

namespace common\pdf;

use common\models\Order;
use DateTime;
use Yii;

/**
 * Class OrderPackingSlip
 *
 * This class generates a Packing Slip PDF file for a given order.
 * Default size of the packing slip is 4x6 inches.
 *
 * @package common\pdf
 */
class OrderPackingSlip extends \FPDF
{

    /**
     * PDF design related properties
     */
    protected $fontFamily = 'Arial';
    protected $fontStyle = '';
    protected $fontSize = 9;
    protected $marginLeft = 4;
    protected $marginTop = 4;
    protected $marginRight = 4;
    protected $marginBottom = 1;
    protected $pageWidth; // page width without margins
    protected $pageHeight; // page height without margins

    protected $_currency = '$';

    /** {@inheritdoc} */
    public function __construct($orientation = 'P', $unit = 'mm', $size = [100, 150])
    {
        parent::__construct($orientation, $unit, $size);
        $this->SetAuthor(Yii::$app->name);
        $this->SetTitle("Packing Slip");
        $this->SetAutoPageBreak(true, $this->marginBottom);
        $this->SetMargins($this->marginLeft, $this->marginTop, $this->marginRight);
        $this->SetFont($this->fontFamily, '', $this->fontSize);

        // calculate page width without margins
        $this->pageWidth = ($this->GetPageWidth() - $this->marginLeft - $this->marginRight);

        // calculate page height without margins
        $this->pageHeight = ($this->GetPageHeight() - $this->marginTop - $this->bMargin);
    }

    /**
     * Reset font to default values
     */
    protected function resetFont()
    {
        $this->SetFont($this->fontFamily, $this->fontStyle, $this->fontSize);
        $this->setTextColor(0);
        $this->SetDrawColor(0);
        $this->SetFillColor(0);
    }

    /**
     * Reset page margins to default values
     */
    protected function resetMargins()
    {
        $this->SetMargins($this->marginLeft, $this->marginTop, $this->marginRight);
    }

    /**
     * Formats a number using the currency format defined in the locale.
     *
     * @param mixed $amount
     *
     * @return false|string
     * @throws \yii\base\InvalidConfigException
     */
    protected function asCurrency($amount)
    {
        return Yii::$app->formatter->asCurrency($amount, $this->_currency);
    }

    /**
     * Generate code39
     *
     * @param $xpos
     * @param $ypos
     * @param $code
     * @param float $baseline
     * @param int $height
     * @throws \Exception
     * @see http://www.fpdf.org/en/script/script46.php
     */
    public function Code39($xpos, $ypos, $code, $baseline=0.5, $height=5)
    {
        $wide = $baseline;
        $narrow = $baseline / 3 ;
        $gap = $narrow;

        $barChar['0'] = 'nnnwwnwnn';
        $barChar['1'] = 'wnnwnnnnw';
        $barChar['2'] = 'nnwwnnnnw';
        $barChar['3'] = 'wnwwnnnnn';
        $barChar['4'] = 'nnnwwnnnw';
        $barChar['5'] = 'wnnwwnnnn';
        $barChar['6'] = 'nnwwwnnnn';
        $barChar['7'] = 'nnnwnnwnw';
        $barChar['8'] = 'wnnwnnwnn';
        $barChar['9'] = 'nnwwnnwnn';
        $barChar['A'] = 'wnnnnwnnw';
        $barChar['B'] = 'nnwnnwnnw';
        $barChar['C'] = 'wnwnnwnnn';
        $barChar['D'] = 'nnnnwwnnw';
        $barChar['E'] = 'wnnnwwnnn';
        $barChar['F'] = 'nnwnwwnnn';
        $barChar['G'] = 'nnnnnwwnw';
        $barChar['H'] = 'wnnnnwwnn';
        $barChar['I'] = 'nnwnnwwnn';
        $barChar['J'] = 'nnnnwwwnn';
        $barChar['K'] = 'wnnnnnnww';
        $barChar['L'] = 'nnwnnnnww';
        $barChar['M'] = 'wnwnnnnwn';
        $barChar['N'] = 'nnnnwnnww';
        $barChar['O'] = 'wnnnwnnwn';
        $barChar['P'] = 'nnwnwnnwn';
        $barChar['Q'] = 'nnnnnnwww';
        $barChar['R'] = 'wnnnnnwwn';
        $barChar['S'] = 'nnwnnnwwn';
        $barChar['T'] = 'nnnnwnwwn';
        $barChar['U'] = 'wwnnnnnnw';
        $barChar['V'] = 'nwwnnnnnw';
        $barChar['W'] = 'wwwnnnnnn';
        $barChar['X'] = 'nwnnwnnnw';
        $barChar['Y'] = 'wwnnwnnnn';
        $barChar['Z'] = 'nwwnwnnnn';
        $barChar['-'] = 'nwnnnnwnw';
        $barChar['.'] = 'wwnnnnwnn';
        $barChar[' '] = 'nwwnnnwnn';
        $barChar['*'] = 'nwnnwnwnn';
        $barChar['$'] = 'nwnwnwnnn';
        $barChar['/'] = 'nwnwnnnwn';
        $barChar['+'] = 'nwnnnwnwn';
        $barChar['%'] = 'nnnwnwnwn';

        $this->SetFont('Arial','',10);
        $this->Text($xpos, $ypos + $height + 4, $code);
        $this->SetFillColor(0);

        $code = '*'.strtoupper($code).'*';
        for($i=0; $i<strlen($code); $i++){
            $char = $code[$i];
            if(!isset($barChar[$char])){
                $this->Error('Invalid character in barcode: '.$char);
            }
            $seq = $barChar[$char];
            for($bar=0; $bar<9; $bar++){
                if($seq[$bar] == 'n'){
                    $lineWidth = $narrow;
                }else{
                    $lineWidth = $wide;
                }
                if($bar % 2 == 0){
                    $this->Rect($xpos, $ypos, $lineWidth, $height, 'F');
                }
                $xpos += $lineWidth;
            }
            $xpos += $gap;
        }
    }

    /**
     * Generate the PDF
     *
     * Creates one label for given order.
     * This function takes Order data and generates the PDF content (without saving neither outputting it)
     *
     * @param Order $order Order data to populate
     *
     * @throws \Exception
     */
    public function generate(Order $order)
    {

        $this->AddPage();

        /**
         * Order #
         */
        $txt = "Packing Slip for Order #{$order->customer_reference}";
        if (!empty($order->order_reference)) {
            $this->Code39(30,130,$order->order_reference,1,10);
        }
        $this->SetFillColor(239, 239, 239);
        $this->SetDrawColor(239, 239, 239);
        $this->Cell($this->pageWidth, 6, $txt, 1, 1, 'C', true);
        $this->resetFont();
        $this->ln(3);
        $yBeforeImage = $this->GetY();

        /**
         * Company Logo
         *
         * We intentionally reduce the logo image height to fit it in its dedicated space on label.
         * width is adjusted automatically by FPDF.
         */
        $imgHeight = 30; // pixels
        $imgHeight = $this->px2mm($imgHeight); // convert px to mm
        if (!empty($order->customer->logo)) {
            $this->Image($order->customer->logo, $this->marginLeft + 5, null, null, $imgHeight);
        }
        $this->ln(0.5);

        /**
         * Company details
         */
        $this->SetXY($this->pageWidth / 1.5, $yBeforeImage);
        $cellH = 4; // cell height
        $this->setFont($this->fontFamily, 'B', $this->fontSize - 2);
        $this->Cell(0, $cellH, $order->customer->name, 0, 2, 'R');
        $this->resetFont();
        $this->setFontSize($this->fontSize - 1);
        $txt = $order->customer->address1 . ' ' . $order->customer->address2;
        $this->Cell(0, $cellH, $txt, 0, 2, 'R');
        $txt = $order->customer->city . ', ' . ($order->customer->state->abbreviation ?? '') . ' ' . $order->customer->zip;
        $this->Cell(0, $cellH, $txt, 0, 2, 'R');
        $this->Cell(0, $cellH, $order->customer->country, 0, 2, 'R');
        $this->ln(3);
        $this->setX($this->marginLeft);

        /**
         * Ship To
         */
        $cellH = 3.5; // cell height
        $cellW = 40; // cell width
        $this->setFont($this->fontFamily, 'B', $this->fontSize - 1);
        $y = $this->GetY();
        $this->Cell($cellW, $cellH + 1, "Ship To:", 0, 2);
        $this->setFont($this->fontFamily, '', $this->fontSize - 2);
        $this->MultiCell($cellW, $cellH, $order->address->name, 0, 'L');
        $txt = $order->address->address1 . ' ' . $order->address->address2;
        $this->MultiCell($cellW, $cellH, $txt, 0, 'L');
        $txt = $order->address->city . ', ' . ($order->address->state->abbreviation ?? '') . ' ' . $order->address->zip;
        $this->MultiCell($cellW, $cellH, $txt, 0, 'L');
        $this->Cell($cellW, $cellH, $order->address->country, 0, 2);

        /**
         * Order and tracking details
         */
        $this->SetXY($this->pageWidth / 2 + 3, $y);
        $this->setFont($this->fontFamily, 'B', $this->fontSize - 1);
        $cellH = 3.7;
        $cellW = 20; // cell width
        $this->Cell($cellW, $cellH, "Order #:", 0, 2, 'R');
        $this->Cell($cellW, $cellH, "Order Date:", 0, 2, 'R');
        $this->Cell($cellW, $cellH, "Shipped Via:", 0, 2, 'R');
        $this->Cell($cellW, $cellH, "Tracking #:", 0, 2, 'R');

        $this->SetXY($this->pageWidth / 2 + $cellW - 7, $y);
        $cellW = 37; // cell width
        $this->setFont($this->fontFamily, '', $this->fontSize - 1);
        $this->Cell($cellW, $cellH, $order->customer_reference, 0, 2, 'R');
        $date = new DateTime($order->created_date);
        $this->Cell($cellW, $cellH, $date->format("m/d/Y"), 0, 2, 'R');
        $this->Cell($cellW, $cellH, $order->carrier->name ?? '', 0, 2, 'R');
        $this->MultiCell($cellW, $cellH, $order->tracking ?? '', 0, 'R');

        $this->SetX($this->marginLeft);
        $this->ln(6);

        /**
         * Items
         */

        // Column headings
        $header = [
            "QTY",
            "SKU",
            "ITEMS",
        ];

        // Data
        $data = [];
        if (is_array($order->items) && count($order->items) > 0) {
            $idx = 0;
            foreach ($order->items as $item) {
                $data[$idx] = [
                    $item->quantity,
                    $item->sku,
                    (strlen($item->name) > 33) ? substr($item->name, 0, 33) . '...' : $item->name,
                ];
                $idx++;
            }
        }

        $this->drawItemsTable($header, $data);


        $this->ln(10);
        /**
         * Ship To
         */
        $cellH = 3.5; // cell height
        $cellW = 30; // cell width
        $this->setFont($this->fontFamily, 'B', $this->fontSize - 1);
        $y = $this->GetY();
        $this->Cell($cellW, $cellH + 1, "Notes:", 0, 2);
        $this->setFont($this->fontFamily, '', $this->fontSize - 2);
        $this->MultiCell($cellW, $cellH, $order->address->notes);
    }

    /**
     * @param array $header
     * @param array $data
     */
    protected function drawItemsTable($header, $data)
    {

        // Column widths
        $w = [10, 20, 60];
        // Column aligns
        $align = ['C', 'L', 'R'];

        // Header
        $this->setFont($this->fontFamily, 'B', 8);
        for ($i = 0; $i < count($header); $i++) {
            $x = $this->GetX();
            $y = $this->GetY();
            $this->MultiCell($w[$i], 4, $header[$i], 0, $align[$i]);
            $this->SetXY($x + $w[$i], $y);
        }
        $this->Ln(5);
        $this->Line(
            $this->marginLeft,
            $this->GetY(),
            $this->pageWidth + $this->marginRight,
            $this->GetY()
        );
        $this->Ln(1);

        // Data
        $this->setFont($this->fontFamily, '', 7);
        foreach ($data as $i => $row) {
            $this->setFont($this->fontFamily, '', 8);
            foreach ($row as $k => $col) {
                $this->Cell($w[$k], 4.5, $row[$k], 0, 0, $align[$k]);
            }
            $this->Ln();
        }

        /**
         * Totals row
         */
        $this->Line(
            $this->marginLeft,
            $this->GetY(),
            $this->pageWidth + $this->marginRight,
            $this->GetY()
        );
        $this->setFont($this->fontFamily, 'B', $this->fontSize - 1);
        $this->Cell(17, 9, 'Total Items: ');
        $this->setFont($this->fontFamily, '', $this->fontSize - 1);
        $this->Cell(0, 9, count($data));
    }

    /**
     * Conversion pixel -> millimeter at 72 dpi
     *
     * @param $px
     *
     * @return float|int
     */
    protected function px2mm($px)
    {
        return $px * 25.4 / 72;
    }
}
