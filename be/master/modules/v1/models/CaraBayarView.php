<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "carabayar_v".
 *
 * @property int $carabayar_id
 * @property string $carabayar_nama
 * @property string $carabayar_namalainnya
 * @property int $metode_pembayaran
 * @property string $metode_pembayaran_nama
 * @property string $carabayar_loket
 * @property string $carabayar_singkatan
 * @property int $groupcarabayar_id
 * @property string $groupcarabayar_nama
 * @property bool $is_subsidiasuransi
 * @property bool $is_subsidipemerintah
 * @property bool $is_subsidirs
 * @property bool $is_penjamin
 * @property bool $kode_antrian
 */
class CaraBayarView extends \yii\db\ActiveRecord
{
   
    public static function tableName()
    {
        return 'carabayar_v';
    }
}
?>