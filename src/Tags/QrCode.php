<?php
declare(strict_types = 1);
namespace Proner\PhpPimaco\Tags;

use Endroid\QrCode\ErrorCorrectionLevel\ErrorCorrectionLevelHigh;
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\QrCode as endQrCode;
use Endroid\QrCode\Label\LabelAlignment;
use Endroid\QrCode\Label\Label;
use Endroid\QrCode\Label\Font\NotoSans;
use Endroid\QrCode\RoundBlockSizeMode\RoundBlockSizeModeMargin;
use Endroid\QrCode\Writer\PngWriter;
use Endroid\QrCode\Color\Color;

class QrCode
{
    private $size;
    private $label;
    private $labelFontSize;
    private $padding;
    private $margin;
    private $align;
    private $content;
    private $br;

    /**
     * QrCode constructor.
     * @param string $content
     * @param string|null $typeCode
     */
    public function __construct(string $content, string $typeCode = null)
    {
        $this->content = $content;
        $this->labelFontSize = 12;
        $this->size = 100;
        $this->padding = 0;
        $this->align = 'left';
        return $this;
    }

    /**
     * @param float $size
     * @return $this
     */
    public function setSize(float $size)
    {
        $this->size = $size;
        return $this;
    }

    /**
     * @param string $label
     * @return $this
     */
    public function setLabel(string $label)
    {
        $this->label = $label;
        return $this;
    }

    /**
     * @param float $labelFontSize
     * @return $this
     */
    public function setLabelFontSize(float $labelFontSize)
    {
        $this->labelFontSize = $labelFontSize;
        return $this;
    }

    /**
     * @param float $padding
     * @return $this
     */
    public function setPadding(float $padding)
    {
        $this->padding = $padding;
        return $this;
    }

    /**
     * @param $margin
     * @return $this
     */
    public function setMargin($margin)
    {
        if (is_array($margin)) {
            $margin = implode("mm ", $margin).'mm';
        } else {
            $margin = $margin."mm";
        }
        $this->margin = $margin;
        return $this;
    }

    /**
     * @param string $align
     * @return $this
     */
    public function setAlign(string $align)
    {
        $this->align = $align;
        return $this;
    }

    public function br()
    {
        $this->br .= "<br>";
    }

    /**
     * @return string
     * @throws \Endroid\QrCode\Exception\InvalidWriterException
     */
    public function render()
    {
//        $qrcode = new \Endroid\QrCode\QrCode('');
//        $qrcode->setErrorCorrectionLevel(new ErrorCorrectionLevelHigh());
//        $qrcode->setEncoding(new \Endroid\QrCode\Encoding\Encoding('UTF-8'));
//        $qrcode->setForegroundColor(new \Endroid\QrCode\Color\Color(0, 0, 0));
//        $qrcode->setBackgroundColor(new \Endroid\QrCode\Color\Color(255, 255, 0));
        //$qrcode->setWriterByName('png');
//        $qrcode->setText($this->content);

        $writer = new PngWriter();

        // Create QR code
        $qrCode = endQrCode::create($this->content)
            ->setEncoding(new Encoding('UTF-8'))
            ->setErrorCorrectionLevel(new ErrorCorrectionLevelHigh)
            ->setSize(300)
            ->setMargin(10)
            ->setRoundBlockSizeMode(new RoundBlockSizeModeMargin)
            ->setForegroundColor(new Color(0, 0, 0))
            ->setBackgroundColor(new Color(255, 255, 255));

        // Create generic logo
        $logo = null;
        //Logo::create(__DIR__.'/assets/symfony.png')
        //    ->setResizeToWidth(50)
        //    ->setPunchoutBackground(true)
       // ;

        // Create generic label
        $label = Label::create('Label')
            ->setTextColor(new Color(255, 0, 0));

        if ($this->br === null) {
            if ($this->align == 'left') {
                $styles[] = "float: left";
            } else {
                $styles[] = "float: right";
            }
        }

        if ($this->margin !== null) {
            $styles[] = "margin: {$this->margin}";
        }

        if (!empty($this->size)) {
            $qrCode->setSize($this->size);
        }

        if (!empty($this->label)) {
            $qrCode->setData($this->label);
        }

        //if (!empty($this->labelFontSize)) {
        //    $qrCode->setLabelFontSize($this->labelFontSize);
       // }

        //if (!empty($this->padding)) {
        //    $qrcode->setPadding($this->padding);
       // }

        if (!empty($styles)) {
            $style = "style='".implode(";", $styles)."'";
        } else {
            $style = "";
        }
        $result = $writer->write($qrCode, $logo, null);

        // Validate the result
        //$writer->validateResult($result, 'Life is too short to be generating QR codes');

        return "<img ".$style." src='{$result->getDataUri()}'>".$this->br;
    }
}
