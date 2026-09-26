<?php

/**
 * @Author: [Wahyu Saepuloh][wahyu.saepuloh@docotel.com]
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "paketpelayananmp_v".
 *
 * @property int $tipepaket_id
 * @property int $tindakan_paket_id
 * @property string $tindakan_paket_nama
 * @property bool $is_tindakan
 * @property int $ruangan_id
 * @property string $ruangan_nama
 * @property int $instalasi_id
 * @property string $instalasi_nama
 * @property int $kelompoktindakan_id
 * @property string $kelompoktindakan_nama
 * @property bool $is_mcu
 */
class PaketPelayananV extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'paketpelayananmp_v';
    }
}
