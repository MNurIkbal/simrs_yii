<?php

namespace Extensions\laboratorium;

use Yii;
use Doco\components\DocoConstants;

class LapPemeriksaanLabExportMhsb extends \Doco\processes\LapPemeriksaanLabExportProcess
{
    protected function processFlow()
    {
        $viewPdfFile = 'index_mhsb';

        return [
            'view_pdf_file' => $viewPdfFile,
            'hide_column' => true
        ];
    }
}
