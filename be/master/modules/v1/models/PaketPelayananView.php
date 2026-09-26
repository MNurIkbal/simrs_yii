<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "instalasi_v".
 *
 * @property integer $instalasi_id
 * @property string $instalasi_nama
 * @property integer $instalasi_singkatan
 */
class PaketPelayananView extends \Doco\components\DocoActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'paketpelayananmp_v';
    }
}
