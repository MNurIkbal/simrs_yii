<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "kettempattidur_m".
 *
 * @property int $kettempattidur_id
 * @property string $kettempattidur_nama
 * @property string $kettempattidur_warna
 * @property string $kode_warna
 * @property string $rgb
 */
class KetTempatTidur extends \Doco\components\DocoActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'kettempattidur_m';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['kettempattidur_id'], 'required'],
            [['kettempattidur_id'], 'default', 'value' => null],
            [['kettempattidur_id'], 'integer'],
            [['kettempattidur_nama', 'kettempattidur_warna', 'kode_warna', 'rgb'], 'string', 'max' => 255],
            [['kettempattidur_id'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'kettempattidur_id' => 'Kettempattidur ID',
            'kettempattidur_nama' => 'Kettempattidur Nama',
            'kettempattidur_warna' => 'Kettempattidur Warna',
            'kode_warna' => 'Kode Warna',
            'rgb' => 'Rgb',
        ];
    }
}
