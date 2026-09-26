<?php

namespace app\modules\v1\models;
use Yii;

class TindakanPelayananUpdate extends \app\components\ActiveRepositories
{
    public $_repositori = 'app\components\repositories\OdooRepositories';
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'tindakanpelayananupdate_r';
    }
}
