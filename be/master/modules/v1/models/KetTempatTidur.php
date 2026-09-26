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
            'kettempattidur_id' => Yii::t('app', 'Keterangan Tempat Tidur ID'),
            'kettempattidur_nama' => Yii::t('app', 'Nama Tempat Tidur'),
            'kettempattidur_warna' => Yii::t('app', 'Nama Warna Tempat Tidur'),
            'kode_warna' => Yii::t('app', 'Kode Warna'),
            'rgb' => Yii::t('app', 'Warna RGB'),
            'jenis' => Yii::t('app', 'Jenis'),
        ];
    }
}
