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
        $this->ln(3);
        $this->setX($this->marginLeft);

        // @todo from here..

        /**
         * Recipient address line 1
         */
        $this->setFont($this->fontFamily, '', 9);
        $this->Cell(69, 4, $order->address->address1, 0, 2);

        /**
         * Recipient address line 2
         */
        $this->Cell(69, 4, $order->address->address2, 0, 2);

        /**
         * Recipient suburb
         */
        $this->setFont($this->fontFamily, 'B', 11);
        $this->Cell(69, 4, $order->address->city, 0, 2);

        /**
         * Recipient state, postcode
         */
        $txt = $order->address->state_id . ", " . $order->address->zip;
        $this->Cell(40, 4, $txt, 0, 0);

        /**
         * Recipient phone number (label)
         */
        $this->setFont($this->fontFamily, 'B', 7);
        $this->Cell(25, 4, "Customer Contact:", 0, 1);

        /**
         * Recipient country (not necessary for domestic)
         */
        $this->setX($this->marginLeft);
        $this->setFont($this->fontFamily, 'B', 7);
        $txt = $order->address->name;
        $this->Cell(40, 3, $txt, 0, 0);

        /**
         * Recipient phone number (value)
         */
        $this->setFont($this->fontFamily, '', 7);
        $this->Cell(29, 3, $order->address->phone, 0, 1);

        /**
         * Special Delivery Instructions
         */
        $this->setX($this->marginLeft);
        $this->Cell(69, 2.5, " ", 0, 2);
        $this->setFont($this->fontFamily, 'B', 7);
        $this->Cell(69, 3, "Special Instructions:", 0, 2);
        $this->setFont($this->fontFamily, '', 6);

        /**
         * Sender (label)
         */
        $this->setX($this->marginLeft);
        $this->Cell(69, 3, " ", 0, 2);
        $this->setFont($this->fontFamily, 'B', 7);
        $this->Cell(30, 3, "From:", 0, 0);

        /**
         * Sender account number
         */
        $txt = "Account #: " . $order->address->name;
        $this->Cell(39, 3, $txt, 0, 1);

        /**
         * Sender business name
         */
        $this->setX($this->marginLeft);
        $this->setFont($this->fontFamily, '', 6.5);
        $this->Cell(69, 2.5, $order->address->name, 0, 2);

        /**
         * Sender address line 1
         */
        $this->Cell(69, 2.5, $order->address->name, 0, 2);

        /**
         * Sender address line 2
         */
        $this->Cell(69, 2.5, $order->address->name, 0, 2);

        /**
         * Sender suburb, state, postcode
         */
        $txt = $order->address->name;
        $this->Cell(69, 2.5, $txt, 0, 2);

        /**
         * Sender country (not necessary for domestic)
         */
        $txt = $order->address->name;
        $this->Cell(69, 2.5, $txt, 0, 2);

        /**
         * Sender contact & phone
         */
        $this->Cell(69, 3, " ", 0, 2);
        $this->setFont($this->fontFamily, 'B', 6.5);
        $this->Cell(69, 2.5, "For any Issues, Please Contact:", 0, 2);
        $this->setFont($this->fontFamily, '', 6.5);
        $txt = "{$order->address->phone}, 123";
        $this->Cell(69, 2.5, $txt, 0, 2);

        /**
         * Description of goods
         */
        $this->Cell(69, 2, " ", 0, 2);
        $this->setFont($this->fontFamily, 'B', 6.5);
        $this->Cell(25, 2.5, "Description of Goods:", 0, 0);
        $this->setFont($this->fontFamily, '', 6.5);
        $this->Cell(44, 2.5, "Non-Hazardous Cargo", 0, 2);


        /**
         * Item no.
         */
        $this->Cell(69, 3, " ", 0, 2);
        $this->setX(60);
        $this->setFont($this->fontFamily, 'B', 6.5);
        $this->Cell(10, 3, "Item no.", 0, 2);
        $this->Cell(30, 3, $order->tracking, 0, 2);

        /**
         * Sender declaration
         */
        $this->setX($this->marginLeft);
        $this->setFont($this->fontFamily, 'B', 7);
        $this->Cell(24, 3, "DECLARATION BY:", 0, 0);
        $this->setFont($this->fontFamily, '', 7);
        $this->Cell(56, 3, $order->address->name, 0, 0);


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
