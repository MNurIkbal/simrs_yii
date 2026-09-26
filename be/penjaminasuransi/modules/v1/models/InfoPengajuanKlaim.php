<?php

namespace app\modules\v1\models;

use Yii;

class InfoPengajuanKlaim extends \app\components\ActiveRepositories
{
    public $_repositori = 'app\components\repositories\InfoPengajuanKlaimRepository';

    public static function tableName()
    {
        return 'infopengajuanklaim_v';
    }
}