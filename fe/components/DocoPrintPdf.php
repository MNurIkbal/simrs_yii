<?php


/**
 * print preview html to pdf  
 *
 * @author iqbal@docotel.com
 * @return preview pdf
 */
namespace app\components;

use Yii;
use Mpdf\Mpdf;
use app\components\DocoHelpers;

class DocoPrintPdf extends Mpdf
{

    protected $html;

    public function generate($html)
    {
        $this->setAutoTopMargin = true;
        $this->setAutoBottomMargin = true;
        if (empty(@$html['_flag_berulang'])) {
            if ( !empty(@$html['_template_header']) )
                $html['_header'];
        } else {
           $this->SetHTMLHeader($html['_header']);
        }
        if (!empty(@$html['_footer'])) {
            $this->SetHTMLFooter($html['_footer']);
        }
        if (!empty($html)) {
            $this->WriteHTML(@$html['_body']);
            $this->showWatermarkText = true;
            $this->watermarkTextAlpha = 0.1;
        }
    }

    public function showPdf($html, $title = "", $name = "")
    {
        $this->generate($html);
        if (!empty($title)) {
            $this->SetTitle($title);
        }
        if (!empty($name)) {
            $this->Output($name, 'I');
        } else {
            $this->Output();
        }
    }

    public function getBase64Pdf($html, $title = "", $name = "")
    {
        $this->generate($html);
        if (!empty($title)) {
            $this->SetTitle($title);
        }
        $pdfString = $this->Output($name, 'S');
        return base64_encode($pdfString);
    }

}