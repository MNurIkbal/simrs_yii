<?php

namespace Integrasi\Components\Custom;

class PhpOfficeNumberFormat extends \PhpOffice\PhpSpreadsheet\Style\NumberFormat
{
    // custom formats
    const FORMAT_NUMBER_COMMA_SEPARATED3 = '#,##0';
    const FORMAT_DATE_DDMMMYYYY = 'dd mmm yyyy';
    const FORMAT_DATE_DATETIME2 = 'dd mmm yyyy hh:mm:ss';
    const FORMAT_NUMBER_COMMA_SEPARATED3_NEW = '#,###0.000';
}