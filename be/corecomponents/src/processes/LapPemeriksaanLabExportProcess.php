<?php

namespace Doco\processes;

use Yii;

class LapPemeriksaanLabExportProcess extends \Doco\components\DocoBaseProcessExtension
{
    protected $viewPdfFile;

    protected function processFlow()
    {
        $viewPdfFile = 'index';

        return [
            'view_pdf_file' => $viewPdfFile,
            'hide_column' => false
        ];
    }
}