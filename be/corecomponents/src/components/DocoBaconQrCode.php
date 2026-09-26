<?php

namespace Doco\components;

use BaconQrCode\Renderer\Image\Png as qrcodePng;
use BaconQrCode\Writer as qrcodeWriter;
use yii\helpers\ArrayHelper;

class DocoBaconQrCode 
{
    /**
     * style_height = default 50px untuk style img
     * style_width = default 50px untuk style img
     * $data = hanya membaca string
     */
    public static function renderQrCode($data = null, $options = [])
    {
        $data = $data ? $data : "Data tidak ditemukan";
        $margin = ArrayHelper::getValue($options, 'margin', 0);
        $height = ArrayHelper::getValue($options, 'height', 256);
        $width = ArrayHelper::getValue($options, 'width', 256);
        $styleHeight = ArrayHelper::getValue($options, 'style_height', '50px');
        $styleWidth = ArrayHelper::getValue($options, 'style_width', '50px');

        $renderer = new qrcodePng();
        $renderer->setMargin($margin)->setHeight($height)->setWidth($width);
        $writer = new qrcodeWriter($renderer);
        $qrCode = $writer->writeString($data);
        return '<img style="height: '.$styleHeight.'; width:'.$styleWidth.';" src="data:image/png;base64,' . base64_encode($qrCode) . '">';
    }
}