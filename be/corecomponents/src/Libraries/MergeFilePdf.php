<?php

namespace Doco\Libraries;

require_once __DIR__ . '/../Sign/fpdf/fpdf.php';

require_once __DIR__ . '/../Sign/fpdi/src/autoload.php';

use setasign\Fpdi\Fpdi;

class MergeFilePdf
{
    public static function NewFpdi()
    {
        return new Fpdi();
    }
}
