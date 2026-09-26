<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "satuankonversiobat_v".
 *
 * @property int $satuankonversi_id
 * @property int $obatalkes_id
 * @property string $obatalkes_nama
 * @property int $satuankecil_id
 * @property string $satuan_kecil
 * @property int $satuanbesar_id
 * @property string $satuan_besar
 * @property double $nilai_konversi
 * @property bool $is_deleted
 * @property bool $is_active
 */
class SatuanKonversiObatView extends \Doco\components\DocoActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'satuankonversi_v';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['satuankonversi_id', 'obatalkes_id', 'satuankecil_id', 'satuanbesar_id'], 'default', 'value' => null],
            [['satuankonversi_id', 'obatalkes_id', 'satuankecil_id', 'satuanbesar_id'], 'integer'],
            [['satuan_kecil', 'satuan_besar'], 'string'],
            [['nilai_konversi'], 'number'],
            [['is_deleted', 'is_active'], 'boolean'],
            [['obatalkes_nama'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'satuankonversi_id' => 'Satuankonversi ID',
            'obatalkes_id' => 'Obatalkes ID',
            'obatalkes_nama' => 'Obatalkes Nama',
            'satuankecil_id' => 'Satuankecil ID',
            'satuan_kecil' => 'Satuan Kecil',
            'satuanbesar_id' => 'Satuanbesar ID',
            'satuan_besar' => 'Satuan Besar',
            'nilai_konversi' => 'Nilai Konversi',
            'is_deleted' => 'Is Deleted',
            'is_active' => 'Is Active',
        ];
    }
}
