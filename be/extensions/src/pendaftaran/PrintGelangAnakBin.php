<?php

namespace Extensions\pendaftaran;

use Da\QrCode\QrCode;

class PrintGelangAnakBin extends \Doco\processes\PrintGelangAnakProcess
{
    /**
     * @return string
     */
    protected function generateQr($no_rekam_medik)
    {
        $qrCode = (new QrCode($no_rekam_medik))
        ->setSize(50)
        ->setMargin(3)
        ->useForegroundColor(0, 0, 0);

        return '<img src="data:image/png;base64,' . base64_encode($qrCode->writeString()) . '">';
    }
}