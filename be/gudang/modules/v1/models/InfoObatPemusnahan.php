<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "infoobatalkesexpired_v".
 *
 * @property int $obatalkes_id
 * @property double $stok
 * @property string $obatalkes_nama
 * @property string $satuan_kecil
 * @property string $tglkadaluarsa
 * @property double $harganetto
 * @property double $jumlah_harganetto
 * @property string $instalasi_nama
 * @property string $ruangan_nama
 * @property int $periodestokobat_id
 * @property string $tglperiodeposting_awal
 * @property string $tglperiodeposting_akhir
 * @property int $ruangan_id
 * @property int $instalasi_id
 * @property int $id_stok
 */
class InfoObatPemusnahan extends \Doco\components\DocoActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'infoobatpemusnahan_v';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [];
    }
}
