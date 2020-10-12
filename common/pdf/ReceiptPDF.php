<?php

namespace common\pdf;

use common\models\Invoice;
use DateTime;
use Yii;

/**
 * Class ReceiptPDF
 *
 * This class generates a Receipt PDF file for a given invoice model.
 *
 * @package common\pdf
 *
 * @property Invoice $invoice
 */
class ReceiptPDF extends \FPDF
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
        $this->SetTitle("Receipt");
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
     * String sanitizing for correct PDF output.
     *
     * @param string|null $str
     *
     * @return false|string
     */
    private function sanitizeOutput($str = null)
    {
        $str = str_replace("’", "'", $str);

        return iconv("UTF-8", "ISO-8859-1//IGNORE", $str);
    }

    /**
     * Returns sanitized text for correct PDF output.
     *
     * @param string|null $str
     *
     * @return false|string
     */
    private function out($str = null)
    {
        return $this->sanitizeOutput($str);
    }

    /**
     * Generate the PDF
     *
     * This function takes Invoice data and generates the PDF content (without saving neither outputting it)
     * After successful generation you can call `$pdf->Output('S')` to get the generated data.
     *
     * @param Invoice $invoice Invoice data to populate
     *
     *
     * @throws \Exception
     */
    public function generate(Invoice $invoice)
    {
        $this->invoice = $invoice;
        $payment = $this->invoice->paymentIntent;

        $this->AddPage();
        $this->resetFont();

        $cellH = 5; // cell height
        $txt   = Yii::t('user', 'Dear {user},', ['user' => $this->invoice->customer_name]);
        $this->Cell(0, $cellH, $txt, 0, 2);
        $this->ln(7);
        $txt = Yii::t(
            'user',
            "Thank you for using {app}. Here is your receipt for invoice #{invoice}.",
            ['app' => Yii::$app->name, 'invoice' => $this->invoice->id]
        );
        $this->Cell(0, $cellH, $this->out($txt), 0, 2);
        $this->ln(7);

        $this->Line($this->marginLeft, $this->GetY(), $this->pageWidth + $this->marginRight, $this->GetY());
        $this->ln(5);

        $col1W = 35; // column width
        $col2W = 140; // column width
        $cellH = 6; // cell height
        $this->Cell($col1W, $cellH, "Date", 0, 0);
        $txt = '';
        if ($payment && $payment->created_date) {
            $txt = (new DateTime($payment->created_date))->format("F jS, Y");
        }
        $this->Cell($col2W, $cellH, ":   " . $this->out($txt), 0, 1);

        $this->Cell($col1W, $cellH, "Transaction ID", 0, 0);
        $this->Cell($col2W, $cellH, ":   " . $this->out($invoice->stripe_charge_id), 0, 1);

        $txt = '';
        if ($payment && $payment->paymentMethod) {
            $txt = ucfirst($payment->paymentMethod->brand) . ' (* * * ' . $payment->paymentMethod->lastfour . ')';
        }
        $this->Cell($col1W, $cellH, "Payment method", 0, 0);
        $this->Cell($col2W, $cellH, ":   " . $this->out($txt), 0, 1);

        $this->Cell($col1W, $cellH, "Amount", 0, 0);
        $this->Cell($col2W, $cellH, ":   " . $this->asCurrency($invoice->amount), 0, 1);

        $this->ln(5);
        $this->Line($this->marginLeft, $this->GetY(), $this->pageWidth + $this->marginRight, $this->GetY());

        $this->AliasNbPages();
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
}
