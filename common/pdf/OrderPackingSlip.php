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
        $this->SetXY($this->pageWidth / 2, $yBeforeImage);
        $cellH = 4; // cell height
        $this->setFont($this->fontFamily, 'B', $this->fontSize - 2);
        $this->Cell(0, $cellH, $order->customer->name, 0, 2);
        $this->resetFont();
        $this->setFontSize($this->fontSize - 1);
        $txt = $order->customer->address1 . ' ' . $order->customer->address2;
        $this->Cell(0, $cellH, $txt, 0, 2);
        $txt = $order->customer->city . ', ' . $order->customer->state->abbreviation . ' ' . $order->customer->zip;
        $this->Cell(0, $cellH, $txt, 0, 2);
        $this->Cell(0, $cellH, $order->customer->country, 0, 2);
        $this->ln(3);
        $this->setX($this->marginLeft);

        /**
         * Ship To
         */
        $cellH = 3.5; // cell height
        $cellW = 44; // cell width
        $this->setFont($this->fontFamily, 'B', $this->fontSize - 1);
        $y = $this->GetY();
        $this->Cell($cellW, $cellH + 1, "Ship To:", 0, 2);
        $this->setFont($this->fontFamily, '', $this->fontSize - 2);
        $this->MultiCell($cellW, $cellH, $order->address->name);
        $txt = $order->address->address1 . ' ' . $order->address->address2;
        $this->MultiCell($cellW, $cellH, $txt);
        $txt = $order->address->city . ', ' . $order->address->state->abbreviation . ' ' . $order->address->zip;
        $this->MultiCell($cellW, $cellH, $txt);
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

        $this->SetXY($this->pageWidth / 2 + $cellW + 2, $y);
        $cellW = 27; // cell width
        $this->setFont($this->fontFamily, '', $this->fontSize - 1);
        $this->Cell($cellW, $cellH, $order->customer_reference, 0, 2);
        $date = new DateTime($order->created_date);
        $this->Cell($cellW, $cellH, $date->format("m/d/Y"), 0, 2);
        $this->Cell($cellW, $cellH, $order->carrier->name ?? '', 0, 2);
        $this->MultiCell($cellW, $cellH, $order->tracking ?? '');

        $this->SetX($this->marginLeft);
        $this->ln(5);

        /**
         * Items
         */

        // Column headings
        $header = [
            "ITEMS",
            "QTY",
            "SKU",
        ];

        // Data
        $data = [];
        if (is_array($order->items) && count($order->items) > 0) {
            $idx = 0;
            foreach ($order->items as $item) {
                $data[$idx] = [
                    $item->name,
                    $item->quantity,
                    $item->sku,
                ];
                $idx++;
            }
        }

        $this->drawItemsTable($header, $data);
    }

    /**
     * @param array $header
     * @param array $data
     */
    protected function drawItemsTable($header, $data)
    {

        // Column widths
        $w = [60, 10, 20];
        // Column aligns
        $align = ['L', 'C', 'L'];

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
