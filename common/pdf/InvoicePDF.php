<?php

namespace common\pdf;

use api\modules\v1\models\core\AddressEx;
use common\models\Invoice;
use DateTime;
use Yii;

/**
 * Class InvoicePDF
 *
 * This class generates an Invoice PDF file for a given invoice model.
 * Default size of the packing slip is 4x6 inches.
 *
 * @package common\pdf
 *
 * @property Invoice $invoice
 */
class InvoicePDF extends \FPDF
{

    /**
     * PDF design related properties
     */
    protected $fontFamily   = 'Arial';
    protected $fontStyle    = '';
    protected $fontSize     = 10;
    protected $marginLeft   = 10;
    protected $marginTop    = 10;
    protected $marginRight  = 10;
    protected $marginBottom = 15;
    protected $pageWidth; // page width without margins
    protected $pageHeight; // page height without margins

    /**
     * @var Invoice $invoice
     */
    protected $invoice;

    /** {@inheritdoc} */
    public function __construct($orientation = 'P', $unit = 'mm')
    {
        parent::__construct($orientation, $unit);
        $this->SetAuthor(Yii::$app->name);
        $this->SetTitle("Invoice");
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
        $this->setTextColor(90, 90, 90);
        $this->SetDrawColor(90, 90, 90);
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
        return Yii::$app->formatter->asCurrency($amount);
    }

    /**
     * Generate the PDF
     *
     * This function takes Invoice data and generates the PDF content (without saving neither outputting it)
     *
     * @param Invoice $invoice Invoice data to populate
     *
     * @throws \Exception
     */
    public function generate(Invoice $invoice)
    {
        $this->invoice = $invoice;
        $this->AddPage();
        $this->resetFont();

        /**
         * ShipWise Logo.
         * We intentionally reduce the logo image height to fit it in its dedicated space on label.
         * width is adjusted automatically by FPDF.
         */
        $logo      = Yii::getAlias('@frontend') . '/web/images/invoice-logo.png';
        $imgHeight = 50; // pixels
        $imgHeight = $this->px2mm($imgHeight); // convert px to mm
        try {
            $this->Image($logo, $this->marginLeft, $this->marginTop, null, $imgHeight);
        } catch (\Exception $e) {
            Yii::error($e->getMessage(), 'invoice-logo');
        }
        $this->ln(0.5);

        /**
         * From
         */
        $cellH = 5; // cell height
        $this->SetY($this->GetY() + $imgHeight + 8);
        $this->setFont($this->fontFamily, 'B');
        $y = $this->GetY();
        $this->Cell(0, $cellH + 3, 'From', 0, 2);
        $this->resetFont();

        $cellW = 80; // cell width
        $this->Cell($cellW, $cellH, Yii::$app->params['invoicing']['company'], 0, 2);
        // @todo Display address?
        $this->Cell($cellW, $cellH, Yii::$app->params['invoicing']['email'], 0, 2);
        $this->Cell($cellW, $cellH, Yii::$app->params['invoicing']['phone'], 0, 2);
        $this->ln(3);

        /**
         * Invoice details
         */
        $cellW = 20; // cell width
        $this->SetXY($this->pageWidth / 2 + $cellW, $y);
        $this->setFont($this->fontFamily, 'B');
        $this->Cell(0, $cellH + 3, 'Details', 0, 2);
        $this->resetFont();

        $this->Cell($cellW, $cellH, "Invoice #:", 0, 2);
        $this->Cell($cellW, $cellH, "Due Date:", 0, 2);
        $this->Cell($cellW, $cellH, "Status:", 0, 2);

        $cellW = 40; // cell width
        $this->SetXY($this->pageWidth - $cellW + $this->marginRight, $y + $cellH + 3);
        $this->setFont($this->fontFamily, '', $this->fontSize - 1);
        $this->Cell($cellW, $cellH, $this->invoice->id, 0, 2, 'R');
        $date = new DateTime($this->invoice->due_date);
        $this->Cell($cellW, $cellH, $date->format("F jS, Y"), 0, 2, 'R');
        $this->Cell($cellW, $cellH, $this->invoice->getStatusLabel(false), 0, 2, 'R');
        $this->ln(3);

        /**
         * For
         */
        $this->ln(2);
        $cellH = 5; // cell height
        $this->setFont($this->fontFamily, 'B');
        $y = $this->GetY();
        $this->Cell(0, $cellH + 3, 'For', 0, 2);
        $this->resetFont();

        $cellW = 80; // cell width
        $this->Cell($cellW, $cellH, $this->invoice->customer_name, 0, 2);
        // @todo Display address?
        $this->ln(3);

        /**
         * Items
         */
        $this->ln(10);
        // Column headings
        $header = [
            "Item",
            "Price",
        ];

        // Data
        $data = [];
        if (is_array($this->invoice->items) && count($this->invoice->items) > 0) {
            foreach ($this->invoice->items as $item) {
                //for ($i = 0; $i < 50; $i++) { // @todo remove after tests
                    $data[] = [
                        $item->name,
                        $this->asCurrency($item->getDecimalAmount()),
                    ];
                //}
            }
        }

        $this->drawItemsTable($header, $data);
        $this->ln(10);

        $this->AliasNbPages();
    }

    /**
     * Draw items table
     *
     * @param array $header
     * @param array $data
     *
     * @throws \yii\base\InvalidConfigException
     */
    protected function drawItemsTable(array $header, array $data)
    {
        // Column widths
        $w = [150, 40];
        // Column aligns
        $align = ['L', 'R'];

        // Header
        $this->setFont($this->fontFamily, 'B');
        for ($i = 0; $i < count($header); $i++) {
            $x = $this->GetX();
            $y = $this->GetY();
            $this->MultiCell($w[$i], 5, $header[$i], 0, $align[$i]);
            $this->SetXY($x + $w[$i], $y);
        }
        $this->Ln(8);
        $this->Line(
            $this->marginLeft,
            $this->GetY(),
            $this->pageWidth + $this->marginRight,
            $this->GetY()
        );
        $this->Ln(1);

        // Data
        $this->setFont($this->fontFamily, '');
        foreach ($data as $i => $row) {
            foreach ($row as $k => $col) {
                $this->Cell($w[$k], 6, $row[$k], 0, 0, $align[$k]);
            }
            $this->Ln();
        }

        /**
         * The total amount row
         */
        $this->Ln(1);
        $this->Line(
            $this->marginLeft,
            $this->GetY(),
            $this->pageWidth + $this->marginRight,
            $this->GetY()
        );
        $this->Ln(2);
        $this->setFont($this->fontFamily, 'B', $this->fontSize + 2);
        $txt = 'Total: ' . $this->asCurrency($this->invoice->getDecimalAmount());
        $this->Cell(0, 9, $txt, 0, 2, 'R');
        $this->resetFont();
    }

    /**
     * The footer of each page
     */
    function Footer()
    {
        // Go to 1.5 cm from bottom
        $this->SetY(-15);
        $this->setFontSize($this->fontSize - 1);
        // Print centered page number
        $this->Cell(0, 10, 'Page ' . $this->PageNo() . " of {nb}", 0, 0, 'R');
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
