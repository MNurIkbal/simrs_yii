<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "rl3_7_radiologidetail_v".
 *
 * @property string $tgl_tindakan
 * @property int $jenispemeriksaanrad_id
 * @property int $kelompokpemeriksaanrad_id
 * @property int $qty_tindakan
 * @property string $jenispemeriksaanrad_nama
 * @property string $nama_kelompok
 */
class RLRadiologi extends \Doco\components\DocoActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'rl3_7_radiologidetail_v';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['tgl_tindakan'], 'safe'],
            [['jenispemeriksaanrad_id', 'kelompokpemeriksaanrad_id', 'qty_tindakan'], 'default', 'value' => null],
            [['jenispemeriksaanrad_id', 'kelompokpemeriksaanrad_id', 'qty_tindakan'], 'integer'],
            [['jenispemeriksaanrad_nama'], 'string', 'max' => 100],
            [['nama_kelompok'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'tgl_tindakan' => 'Tgl Tindakan',
            'jenispemeriksaanrad_id' => 'Jenispemeriksaanrad ID',
            'kelompokpemeriksaanrad_id' => 'Kelompokpemeriksaanrad ID',
            'qty_tindakan' => 'Qty Tindakan',
            'jenispemeriksaanrad_nama' => 'Jenispemeriksaanrad Nama',
            'nama_kelompok' => 'Nama Kelompok',
        ];
    }
}
