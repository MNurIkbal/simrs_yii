<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "expertise_v".
 *
 * @property int $expertise_id
 * @property string $nama_expertise
 * @property int $pemeriksaanrad_id
 * @property string $pemeriksaanrad_nama
 * @property string $hasil_expertise
 * @property string $kesan
 * @property string $kesimpulan
 */
class ExpertiseView extends \Doco\components\DocoActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'expertise_v';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['expertise_id', 'pemeriksaanrad_id'], 'default', 'value' => null],
            [['expertise_id', 'pemeriksaanrad_id'], 'integer'],
            [['hasil_expertise', 'kesan', 'kesimpulan'], 'string'],
            [['nama_expertise'], 'string', 'max' => 255],
            [['pemeriksaanrad_nama'], 'string', 'max' => 100],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'expertise_id' => 'Expertise ID',
            'nama_expertise' => 'Nama Expertise',
            'pemeriksaanrad_id' => 'Pemeriksaanrad ID',
            'pemeriksaanrad_nama' => 'Pemeriksaanrad Nama',
            'hasil_expertise' => 'Hasil Expertise',
            'kesan' => 'Kesan',
            'kesimpulan' => 'Kesimpulan',
        ];
    }
}
