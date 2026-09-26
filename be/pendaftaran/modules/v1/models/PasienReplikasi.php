<?php

namespace app\modules\v1\models;

use Yii;

class PasienReplikasi extends \Doco\components\DocoActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'pasien_r';
    }
}
?>