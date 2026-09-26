<?php

/**
 * @Author: [Wahyu Saepuloh][wahyu.saepuloh@docotel.com]
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "masterpaketmcu_v".
 *
 * @property int $tipepaket_id
 * @property string $tipepaket_kode
 * @property string $tipepaket_nama
 * @property string $tipepaket_namalainnya
 * @property string $keterangan_tipepaket
 * @property string $detail
 * @property string $status
 * @property string $is_active
 */
class PaketMcuView extends \Doco\components\DocoActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'masterpaketmcu_v';
    }
}
