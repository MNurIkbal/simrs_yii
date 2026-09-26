<?php

namespace app\modules\v1\models;
use Yii;

/**
 * This is the model class for table "infopermintaanmakandetail_v".
 *
 * @property int $permintaanmakandetail_id
 * @property int $permintaaanmakan_id
 * @property string $no_permintaanmakan
 * @property string $jenisdiet_nama
 * @property string $makanandiet_nama
 * @property string $waktu
 * @property int $jumlah
 * @property string $keterangan
 */
class InfoPermintaanMakanDetailView extends \Doco\components\DocoActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'infopermintaanmakandetail_v';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['permintaanmakandetail_id', 'permintaaanmakan_id', 'jumlah'], 'default', 'value' => null],
            [['permintaanmakandetail_id', 'permintaaanmakan_id', 'jumlah'], 'integer'],
            [['keterangan'], 'string'],
            [['no_permintaanmakan'], 'string', 'max' => 255],
            [['jenisdiet_nama'], 'string', 'max' => 50],
            [['makanandiet_nama', 'waktu'], 'string', 'max' => 200],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'permintaanmakandetail_id' => 'Permintaanmakandetail ID',
            'permintaaanmakan_id' => 'Permintaaanmakan ID',
            'no_permintaanmakan' => 'No Permintaanmakan',
            'jenisdiet_nama' => 'Jenisdiet Nama',
            'makanandiet_nama' => 'Makanandiet Nama',
            'waktu' => 'Waktu',
            'jumlah' => 'Jumlah',
            'keterangan' => 'Keterangan',
        ];
    }
}
