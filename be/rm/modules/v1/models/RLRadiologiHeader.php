<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "rl3_7_radiologi_v".
 *
 * @property string $kolom
 * @property int $kelompokpemeriksaanrad_id
 * @property string $nama_kelompok
 * @property int $jenispemeriksaanrad_id
 * @property string $jenispemeriksaanrad_nama
 */
class RLRadiologiHeader extends \Doco\components\DocoActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'rl3_7_radiologi_v';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['kolom', 'jenispemeriksaanrad_nama'], 'string'],
            [['kelompokpemeriksaanrad_id', 'jenispemeriksaanrad_id'], 'default', 'value' => null],
            [['kelompokpemeriksaanrad_id', 'jenispemeriksaanrad_id'], 'integer'],
            [['nama_kelompok'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'kolom' => 'Kolom',
            'kelompokpemeriksaanrad_id' => 'Kelompokpemeriksaanrad ID',
            'nama_kelompok' => 'Nama Kelompok',
            'jenispemeriksaanrad_id' => 'Jenispemeriksaanrad ID',
            'jenispemeriksaanrad_nama' => 'Jenispemeriksaanrad Nama',
        ];
    }
}
