<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "ruangan_v".
 *
 * @property integer $instalasi_id
 * @property integer $ruangan_id
 * @property string $instalasi_nama
 * @property string $ruangan_nama
 * @property string $ruangan_singkatan
 */
class RuanganView extends \Doco\components\DocoActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'ruangan_v';
    }
}
