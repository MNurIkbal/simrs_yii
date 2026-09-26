<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "carakeluar_v".
 *
 * @property int $carakeluar_id
 * @property string $carakeluar_nama
 * @property string $carakeluar_namalain
 * @property string $carakeluar_kode
 * @property int $carakeluar_urutan
 * @property string $catatan
 * @property bool $is_active
 */
class CekTagihanView extends \Doco\components\DocoActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'cektagihan_v';
    }

}
