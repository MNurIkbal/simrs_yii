<?php

/**
 * @author : Novia Sukma Sari P (novia.putri@sirs.co.id)
 * A product of PT. Citraraya Nusatama
 * Powered by Sirs
 */

namespace Integrasi\Components;

use Yii;

class PhpOffice extends \PhpOffice\PhpSpreadsheet\Style\NumberFormat {
    const FORMAT_DATE_TIMESTAMP = 'dd mmm yy hh:mm:ss';
    const FORMAT_SHORTDATE = 'dd mmm yy';
    const FORMAT_SHORTDATE_TIMESTAMP = 'dd mmm yy hh:mm:ss';
}