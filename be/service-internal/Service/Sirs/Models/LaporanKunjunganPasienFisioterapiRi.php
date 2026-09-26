<?php

namespace Integrasi\Service\Sirs\Models;

use app\modules\v1\classes\ExcelColumn;
use Integrasi\Components\DocoExcelActiveRecord;

class LaporanKunjunganPasienFisioterapiRi extends \Integrasi\Components\ActiveRepositories
{
    public static function tableName()
    {
        return 'lapkunjunganpasienfisiori_v';
    }

}
